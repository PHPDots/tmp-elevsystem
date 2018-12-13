<?php

/**
 *
 * @package Lisbeth
 * @subpackage app.Console.Command
 */

//App::uses('CakeEmail', 'Network/Email');
//setlocale(LC_ALL,"danish");

class ImportStudentsFromCRMShell extends AppShell{

    public $uses = array('Booking', 'User','Activity' ,'UserServices', 'Services','LatestPayments','Systembooking');

    public function main(){

        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 900);
        ini_set('default_socket_timeout', 600);
        echo date("Y-m-d H:i:s");

        $Lokationsnummers = array('99','95');

        foreach ($Lokationsnummers as $Lokationsnummer) {

            $params = array('KundeID' => '2795cd76-0a62-4f1c-994b-f5bfbdbf24d1', 'Lokationsnummer' => $Lokationsnummer);

            $wsdl = 'http://elevdata.jb-edb.dk/debitor/debitorservice.asmx?wsdl';

            $options = array(
                'uri'                => 'http://schemas.xmlsoap.org/soap/envelope/',
                'style'              => SOAP_RPC,
                'use'                => SOAP_ENCODED,
                'soap_version'       => SOAP_1_1,
                'cache_wsdl'         => WSDL_CACHE_NONE,
                'connection_timeout' => 15,
                'trace'              => true,
                'encoding'           => 'UTF-8',
                'exceptions'         => true,
            );
            try {
                $soap = new SoapClient($wsdl, $options);
                $data = $soap->GetDebitorListe($params);
            } catch (Exception $e) {
                die($e->getMessage());
            }
            // echo "<pre>";
            // echo "\n--------------\n";
            // echo json_encode($data);
            // echo "\n--------------\n";
            // die();
            $i = 1;
            if (isset($data->GetDebitorListeResult->DebitorListeVO) && !empty($data->GetDebitorListeResult->DebitorListeVO)) {
                foreach ($data->GetDebitorListeResult->DebitorListeVO as $key => $value) {
                    $user = $this->User->find('first', array(
                                    'conditions' => array(
                                        'User.student_number' => $value->Elevnummer
                                     )));
                    
                    $DebitorBeloeb = $value->DebitorBeloeb;

                    // if($DebitorBeloeb < 0){
                    //     $DebitorBeloeb = abs($DebitorBeloeb);
                    // }else{
                    //     $DebitorBeloeb = "-".$DebitorBeloeb;
                    // }

                    if(!isset($user['User']) ){
                        $this->User->create();
                        $User                   = array();
                        $User['student_number'] = $value->Elevnummer;
                        

                        $User['balance']        = round($DebitorBeloeb,2);
                        if(!empty($value->Elevspecifikation->Elev->Email)){
                            $User['username']       = $value->Elevspecifikation->Elev->Email;
                        }else{
                            $User['username']       = $value->Elevnummer;
                        }
                        $User['password']       = $value->Elevspecifikation->Elev->Telefon1;
                        $User['role']           = 'student';
                        $User['crm_id']         = $value->Elevspecifikation->Elev->PersonID;
                        $User['firstname']      = $value->Elevspecifikation->Elev->Fornavn;
                        $User['lastname']       = $value->Elevspecifikation->Elev->Efternavn;
                        $User['address']        = $value->Elevspecifikation->Elev->Adresse;
                        $User['zip']            = $value->Elevspecifikation->Elev->Postnummer;
                        $User['city']           = $value->Elevspecifikation->Elev->By;
                        $User['phone_no']       = $value->Elevspecifikation->Elev->Telefon1;
                        $User['other_phone_no'] = $value->Elevspecifikation->Elev->Telefon2;
                        $User['email_id']       = $value->Elevspecifikation->Elev->Email;
                        $User['is_login_firsttime']       = '1';
                        if (isset($value->Elevspecifikation->Assistent->PersonID)) {
                            $User['teacher_id'] = $value->Elevspecifikation->Assistent->PersonID;
                        }
                        // echo "\n==============================\n";
                        // print_r($User);
                        $this->User->save($User);

                        $Elevnummer = $value->Elevnummer;
                        $City_id = substr($Elevnummer, 2, -11);
                        $Category_id = substr($Elevnummer, 10, -3);

                        $Services = $this->Services->find('all', array(
                                                    'conditions' => array(
                                                        'city_id LIKE' => "%".$City_id."%",
                                                        'category_id' => $Category_id
                                                     )
                                                    ));
                        if(count($Services) > 0){
                            foreach ($Services as $key => $Service) {
                                $ydelsesData = array();
                                $params = array();
                                $params['KundeID'] = '2795cd76-0a62-4f1c-994b-f5bfbdbf24d1';
                                $ydelsesData['Elevnummer'] = strval($User['student_number']);
                                $ydelsesData['AssistentNummer'] = strval(8);
                                $current_time = date('Y-m-d H:i:s');
                                $ydelsesData['PosteringsDato'] = date(DATE_ATOM,strtotime($current_time));
                                $ydelsesData['Antal'] = '1';
                                $ydelsesData['Pris'] = $Service['Services']['price'];
                                $ydelsesData['KontoNummer'] = strval($Service['Services']['code']);
                                $params['ydelsesData'] = $ydelsesData;
                                $crm_data = $this->submitCRMdata($params);

                            }
                        }

                        $pass_data     = array();
                        $last_inserted_id = $this->User->getLastInsertID();
                        $pass_data['last_inserted_id'] = $last_inserted_id;
                        $pass_data['student_name'] = $User['firstname'];
                        $pass_data['student_number'] = $User['student_number'];
                        $this->InsertActivityLog($pass_data,'student_import_crm');
                        echo "counter=> ".$i." <=> Inserted ID:".$last_inserted_id;
                        echo "\n";
                        $i++;
                    }else{
                        $user_id = $user['User']['id'];
                        $this->User->id = $user_id;
                        
                        $g_total = 0;
                        $g_total_1 = 0;
                        $gtotal = 0;

                        if(isset($value->Elevspecifikation->Ydelser->DebitorYdelseVO)){
                            if(isset($value->Elevspecifikation->Ydelser->DebitorYdelseVO->DebitorRegistreringID)){
                                $base_loop = $value->Elevspecifikation->Ydelser;
                            }else{
                                $base_loop = $value->Elevspecifikation->Ydelser->DebitorYdelseVO;
                            }
                            foreach ($base_loop as $key1 => $DebitorYdelseVO) {

                                if(isset($DebitorYdelseVO->DebitorRegistreringID)){

                                    $debtor_registration_id = $DebitorYdelseVO->DebitorRegistreringID;

                                    $services = $this->UserServices->find('count', array(
                                            'conditions' => array(
                                                'debtor_registration_id' => $debtor_registration_id
                                             )));
                                    if($services == 0){
                                        $this->UserServices->create();
                                        $UserServices                   = array();
                                        $UserServices['user_id'] = $user_id;
                                        $UserServices['debtor_registration_id'] = $debtor_registration_id;
                                        $UserServices['posting_date'] = date("Y-m-d H:i:s" , strtotime($DebitorYdelseVO->PosteringsDato));
                                        $UserServices['description'] = $DebitorYdelseVO->Tekst;
                                        $UserServices['qty'] = $DebitorYdelseVO->Antal;
                                        $UserServices['price'] = $DebitorYdelseVO->SatsInclMoms;
                                        $UserServices['total_price'] = $DebitorYdelseVO->BeloebInclMoms;

                                        $this->UserServices->save($UserServices);
                                        $UserServices['last_inserted_id'] = $this->UserServices->getLastInsertID();
                                        $this->InsertActivityLog($UserServices,'services_import_crm');
                                    }

                                    // $g_total += floatval(preg_replace('/[^\d.]/', '', $DebitorYdelseVO->BeloebInclMoms));
                                    $totalPrice = str_replace(',','', $DebitorYdelseVO->BeloebInclMoms);
                                    $totalPrice = number_format($totalPrice, 2, '.', '');
                                    $g_total +=  $totalPrice;
                                }
                                else
                                {
                                    print_r($key1);
                                    echo "<--->";
                                    print_r($DebitorYdelseVO);
                                    // print_r($value->Elevspecifikation->Ydelser);
                                    echo "\n";
                                }
                            }
                        }

                        $Payments = $this->LatestPayments->find('all', array(
                                'conditions' => array(
                                    'DebitorNummer' => $value->Elevnummer
                                 )));
                        if(!empty($Payments)) {
                            foreach ($Payments as $key => $Payment) 
                            {
                                $tmp_g_total_1 = round($Payment['LatestPayments']['Kredit']);
                                $g_total_1 = $g_total_1 + $tmp_g_total_1;
                            }
                        }

                        $conditions = array();
                        $currentDate    = date('Y-m-d H:i:s',time());
                        $conditions['student_id'] = $user_id;
                        $conditions[] = "start_time >= '{$currentDate}'";
                        $conditions[] = "status != 'delete'";

                        $Systembooking        = $this->Systembooking->find('all',array(
                            'conditions'    => $conditions,
                            'order'         => array('start_time' => 'ASC')
                        ));
                        
                        if(!empty($Systembooking)) {
                            foreach($Systembooking as $booking){
                                $type = ($booking['Systembooking']['lesson_type']  != '') ? $booking['Systembooking']['lesson_type'] : '1' ;
                                $total =  $type*500;
                                $gtotal = $gtotal + $total;
                            }
                        }

                        // $this->User->saveField('balance',round($DebitorBeloeb,2));
                        $this->User->saveField('balance',round( ($g_total - $g_total_1) + $gtotal,2));
                    }
                }
            }

        }
    }

    function InsertActivityLog($data,$log_type){
        $this->Activity->create();
        $activity = array();
        $activity['from_id']      = $data['last_inserted_id'];
        $activity['to_id']        = $data['last_inserted_id'];
        $activity['action']       = $log_type;
        $activity['data']         = serialize($data);
        $this->Activity->save($activity);
        return true;
    }

    function submitCRMdata($params){

        $wsdl = 'http://elevdata.jb-edb.dk/debitor/debitorservice.asmx?WSDL';

        $options = array(
            'uri'                => 'http://schemas.xmlsoap.org/soap/envelope/',
            'style'              => SOAP_RPC,
            'use'                => SOAP_ENCODED,
            'soap_version'       => SOAP_1_1,
            'cache_wsdl'         => WSDL_CACHE_NONE,
            'connection_timeout' => 15,
            'trace'              => true,
            'encoding'           => 'UTF-8',
            'exceptions'         => true,
        );

        try {
            
            $soap = new SoapClient($wsdl, $options);
            $data = $soap->CreateDebitorYdelse($params);
            // print_r($data);
        } catch (Exception $e) {
            // echo "\n------------\n";
            $data = $e->getMessage();
        }

        return $data;
    }
}
