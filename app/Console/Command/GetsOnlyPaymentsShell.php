<?php

/**
 *
 * @package Lisbeth
 * @subpackage app.Console.Command
 */


class GetsOnlyPaymentsShell extends AppShell{

    public $uses = array('LatestPayments');

    public function main(){

        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 900);
        ini_set('default_socket_timeout', 600);

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
                $data = $soap->GetSenesteBetalinger($params);
            } catch (Exception $e) {
                die($e->getMessage());
            }
            // print_r($data);
            // echo "\n--------------\n";
            // echo json_encode($data);
            // echo "\n--------------\n";
            // die();
            if (isset($data->GetSenesteBetalingerResult) && !empty($data->GetSenesteBetalingerResult->DebitorBetalingVO)) {

                $DebitorBetalingVO = $data->GetSenesteBetalingerResult->DebitorBetalingVO;

                if(isset($DebitorBetalingVO->DebitorRegistreringID)){
                    $DebitorBetalingV[0] = $DebitorBetalingVO;
                }else{
                    $DebitorBetalingV = $DebitorBetalingVO;
                }
                foreach ($DebitorBetalingV as $key => $value) {

                    $Payments = $this->LatestPayments->find('first', array(
                                    'conditions' => array(
                                        'BilagsNummer' => $value->BilagsNummer
                                     )));

                    // print_r($value);
                    if(!isset($Payments['LatestPayments'])){
                        
                        $this->LatestPayments->create();
                        $Payments                           = array();
                        $Payments['DebitorRegistreringID']  = $value->DebitorRegistreringID;
                        $Payments['IsPosteret']             = (string)$value->IsPosteret;
                        $Payments['PosteringsDato']         = $value->PosteringsDato;
                        $Payments['DebitorNummer']          = $value->DebitorNummer;
                        $Payments['Debet']                  = $value->Beloeb->Debet;
                        $Payments['Kredit']                 = $value->Beloeb->Kredit;
                        $Payments['BilagsNummer']           = $value->BilagsNummer;
                        $Payments['ModKontoID']             = $value->ModKontoID;
                        $Payments['created_at']             = date('Y-m-d H:i:s');
                        
                        // print_r($Payments);
                        // die();
                        $this->LatestPayments->save($Payments);
                    }
                }
            }
        }
    }


}
