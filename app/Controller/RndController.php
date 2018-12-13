<?php

App::uses('CakeEmail'       , 'Network/Email');
App::uses('SmsSender'       , 'Lib/Sms');

class RndController extends AppController {  
    
    public $uses        = array('Booking','Area','SmsTemplate','SmsQueue','Rnd',
        'EmailTemplate','EmailQueue','User','Lookup','Expence','BookingTrack','Track','Course');
    public $prefix      = array();
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->prefix   = Configure::read('prefix');
    }
    
    public function index(){
                pr(date('d/m/Y H:i:s'));
        //        echo 'Current PHP version: ' . phpversion();
                die();
        //        $this->Booking->bookingNotification();
        //        
        //        die('mail saved');
        //        
    }
    
    public function soapFunctionality(){
        
        $studentDetails = $this->Rnd->query('CreateDebitorYdelse', array('parameters' => array(
            'KundeID'       => KUNDEID,
            'ydelsesData'   => array(
                'Elevnummer'        => '000714121701029',
                'Aktivitetsnummer'  => 1,
                'Antal'             => 1,
                'AssistentNummer'   => '0e460684-3cbb-4fae-90e0-f149362acdd3',
                'PosteringsDato'    => '2015-01-01T17:30:00',
                'Pris'              => 6500.00   
            )
        )));
        
        pr($studentDetails);
        die();
    }
    
    public function soapTest() {
        $studentArray   = array();
        $studentObj     = array();
        $studentAry     = array();       
        $studentObj = $this->Rnd->query('GetElevspecifikation', array('parameters' => array(
            'KundeID'       => KUNDEID,
            'Elevnummer'    => '000714121701029'                  
        )));

        pr($studentObj);
        die();
        $systemUsers       = $this->User->find('all');        
        $systemStudents    = Hash::combine($systemUsers,'{n}.User.student_number','{n}.User');
        
        $students = $this->Rnd->query('GetRegnskabsElever', array('parameters' => array(
                'KundeID'       => KUNDEID,           
        )));
      
        $studentArray   = $students->GetRegnskabsEleverResult->string;

        $teacherIds = array();
        $i = 1;
        $crmCounts      = 0;
        $systemCounts   = 0;
        
        foreach($studentArray as $student){            
            $studentObj = $this->Rnd->query('GetElevspecifikation', array('parameters' => array(
                'KundeID'       => KUNDEID,
                'Elevnummer'    => $student
            )));            
           
            if(!empty($studentObj)){
                if(isset($studentObj->GetElevspecifikationResult->Status) && ($studentObj->GetElevspecifikationResult->Status == 'OK')){
                      $expenceTotal     = 0;
                      $paymentTotal     = 0;                      
                     
                    if(isset($studentObj->GetElevspecifikationResult->Assistent)){
                        $username       = strtolower($studentObj->GetElevspecifikationResult->Assistent->Efternavn);                                               
                        
                        $users          = $this->User->find('all',array(
                            'conditions'    => array(
                                'User.role' => 'internal_teacher'
                            )
                        )); 

                        $users  = Hash::combine($users,'{n}.User.username','{n}.User');
                  
                        if(in_array($username,array_keys($users))){
                            $this->request->data['Teacher']['User']['id']        = $users[$username]['id'];
                        }
                        
                        $this->request->data['Teacher']['User']['username']        = $username;
                        $this->request->data['Teacher']['User']['password']        = strtolower($studentObj->GetElevspecifikationResult->Assistent->Efternavn);
                        $this->request->data['Teacher']['User']['firstname']       = $studentObj->GetElevspecifikationResult->Assistent->Fornavn;
                        $this->request->data['Teacher']['User']['lastname']        = $studentObj->GetElevspecifikationResult->Assistent->Efternavn;
                        $this->request->data['Teacher']['User']['address']         = $studentObj->GetElevspecifikationResult->Assistent->Adresse;
                        $this->request->data['Teacher']['User']['zip']             = $studentObj->GetElevspecifikationResult->Assistent->Postnummer;
                        $this->request->data['Teacher']['User']['city']            = $studentObj->GetElevspecifikationResult->Assistent->By;
                        $this->request->data['Teacher']['User']['phone_no']        = $studentObj->GetElevspecifikationResult->Assistent->Telefon1;
                        $this->request->data['Teacher']['User']['other_phone_no']  = $studentObj->GetElevspecifikationResult->Assistent->Telefon2;
                        $this->request->data['Teacher']['User']['email_id']        = $studentObj->GetElevspecifikationResult->Assistent->Email;
                        $this->request->data['Teacher']['User']['role']            = 'internal_teacher';
                        $this->request->data['Teacher']['User']['crm_id']          = $studentObj->GetElevspecifikationResult->Assistent->AssistentID;
                        $this->User->saveAll($this->request->data['Teacher']['User']);

                        $teacherId  = $this->User->id;
                    }
                    
                    $last_balance_date      = NULL;
                    
                    if(in_array($student,array_keys($systemStudents))) {
                        $this->request->data['Student']['User']['id']   = $systemStudents[$student]['id'];      
                        $last_balance_date                              = $systemStudents[$student]['last_entry_for_balance'];                          
                    }
                    
                    $dates                          = array();
                    $this->request->data['Expence'] = array();
                   
                    if(isset($studentObj->GetElevspecifikationResult->Ydelser) && !empty($studentObj->GetElevspecifikationResult->Ydelser)){
                        if(isset($studentObj->GetElevspecifikationResult->Ydelser->DebitorYdelseVO) && !empty($studentObj->GetElevspecifikationResult->Ydelser->DebitorYdelseVO)){
                          $expences          = $studentObj->GetElevspecifikationResult->Ydelser->DebitorYdelseVO;
                          $crmCounts        += count($expences);
                         
                          foreach($expences as $expence){
                              if(isset($expence->SatsExclMoms)){                                  
                                  if(is_null($last_balance_date)){                                    
                                    $this->request->data['Expence'][]   = array(
                                        'date'       => date('Y-m-d',strtotime($expence->PosteringsDato)),
                                        'price'      => $expence->SatsExclMoms,
                                        'number'     => $expence->Antal,
                                        'type'       => $expence->Tekst,
                                        'tax'        => 25
                                    );
                                  }else if(!is_null($last_balance_date) && (date('Y-m-d',strtotime($expence->PosteringsDato)) > $last_balance_date)){                                                                              
                                        $this->request->data['Expence'][]   = array(
                                            'date'       => date('Y-m-d',strtotime($expence->PosteringsDato)),
                                            'price'      => $expence->SatsExclMoms,
                                            'number'     => $expence->Antal,
                                            'type'       => $expence->Tekst,
                                            'tax'        => 25
                                        );
                                  }
                                  $dates[]       = date('Y-m-d',strtotime($expence->PosteringsDato));
                                  $expenceTotal += ($expence->Antal * $expence->SatsExclMoms) + ($expence->SatsExclMoms*0.25);
                              }                               
                          }   
                         
                        }                                                 
                    }      
                   
                    arsort($dates);
                    $dates  = array_values($dates);
                  
                    if(isset($studentObj->GetElevspecifikationResult->Betalinger) && !empty($studentObj->GetElevspecifikationResult->Betalinger)){
                        if(isset($studentObj->GetElevspecifikationResult->Betalinger->DebitorBetalingVO) && !empty($studentObj->GetElevspecifikationResult->Betalinger->DebitorBetalingVO)){
                          $payments         = $studentObj->GetElevspecifikationResult->Betalinger->DebitorBetalingVO;  
                          foreach($payments as $payment){                                       
                              if(isset($payment->Kredit)){                                                                   
                                  $paymentTotal += $payment->Kredit;
                              }else if(isset($payment->Beloeb->Kredit)){
                                  $paymentTotal += $payment->Beloeb->Kredit;
                              }                               
                          }   
                        }                                                 
                    }
                    
                    $balance = $paymentTotal - $expenceTotal;
                    
                    $this->request->data['Student']['User']['student_number']           = $student;
                    $this->request->data['Student']['User']['username']                 = $student;
                    $this->request->data['Student']['User']['password']                 = (!empty($studentObj->GetElevspecifikationResult->Elev->Telefon1))?$studentObj->GetElevspecifikationResult->Elev->Telefon1:strtolower(trim($studentObj->GetElevspecifikationResult->Elev->Efternavn));
                    $this->request->data['Student']['User']['firstname']                = $studentObj->GetElevspecifikationResult->Elev->Fornavn;
                    $this->request->data['Student']['User']['lastname']                 = $studentObj->GetElevspecifikationResult->Elev->Efternavn;
                    $this->request->data['Student']['User']['address']                  = $studentObj->GetElevspecifikationResult->Elev->Adresse;
                    $this->request->data['Student']['User']['zip']                      = $studentObj->GetElevspecifikationResult->Elev->Postnummer;
                    $this->request->data['Student']['User']['city']                     = $studentObj->GetElevspecifikationResult->Elev->By;
                    $this->request->data['Student']['User']['phone_no']                 = $studentObj->GetElevspecifikationResult->Elev->Telefon1;
                    $this->request->data['Student']['User']['other_phone_no']           = $studentObj->GetElevspecifikationResult->Elev->Telefon2;
                    $this->request->data['Student']['User']['email_id']                 = $studentObj->GetElevspecifikationResult->Elev->Email;
                    $this->request->data['Student']['User']['role']                     = 'student';
                    $this->request->data['Student']['User']['balance']                  = $balance;
                    $this->request->data['Student']['User']['last_entry_for_balance']   = (isset($dates[0]))?$dates[0]:NULL;
                    $this->request->data['Student']['User']['teacher_id']               = (!isset($studentObj->GetElevspecifikationResult->Assistent))?NULL:$teacherId;
                    $this->request->data['Student']['User']['crm_id']                   = $studentObj->GetElevspecifikationResult->Elev->PersonID;;
                    
                    var_dump($this->User->saveAll($this->request->data['Student']['User']));
                    
                    if(in_array($student,array_keys($systemStudents))){        
                        echo 'student :'.$systemStudents[$student]['id'].'<br/>';
                        foreach($this->request->data['Expence'] as  $key => $expence){
                            $this->request->data['Expence'][$key]['student_id']  = $systemStudents[$student]['id'];                            
                        }                       
                        var_dump($this->Expence->saveAll($this->request->data['Expence']));         
                        echo '<br/> Stored <br/>';
                        $systemCounts       += count($this->request->data['Expence']);
                    }else{                        
                        echo '<br/> Not Stored <br/>';
                    }
                    
                    
                    echo '--------------------- <br/>';
                    
                    
                    
                    $i++;
               }
            }          
        }
        
        echo 'CRM COUNT: '.$crmCounts.'<br/>';
        echo 'SYSTEM COUNT: '.$systemCounts.'<br/>';
        die('complete');

    }
    
    public function sendEmailNotification(){
        
        $this->EmailSender      = new CakeEmail();
        $limit                  = 5;
        
        /**
         * Fetch the Email From the Queue 
         */
        $result['A']     = $this->EmailQueue->find('all',array(
            'conditions'    => array('EmailQueue.status'=> array('inqueue','fail')),
            'order'         => 'EmailQueue.priority ASC',
            'limit'         => $limit,
        ));
        
        if(count($result['A'])==0)
            exit('No Email Schedule yet');
        /**
         * find the Relavent Templates from the datbase
         */
        $templates      = Hash::extract($result['A'],'{n}.EmailQueue.template');
        $result['B']    = $this->EmailTemplate->find('all',array(
            'conditions'    => array('EmailTemplate.template'=> $templates),
        ));
        
        $template       = Hash::combine($result['B'],'{n}.EmailTemplate.template','{n}');
        
        /**
         * Get Started With Sending the Email
         */
        foreach(Hash::extract($result['A'],'{n}.EmailQueue') as $emailObject){
            
            /**
             * Prepare the EmailObject
             */
            $flag = $this->sendEmail($this->EmailTemplate->emailData($emailObject['email'],$template[$emailObject['template']],$emailObject['data']));
            
            /**
             * Update the database Status of the Queue Element
             */
            $this->EmailQueue->updateAll(array(
                'EmailQueue.status' => "'sent'",
            ),array(
                'EmailQueue.id' => $emailObject['id'],
            ));
        }
        die('Mail Sent Successfully');
    }
    
    public function sendSms(){
        
        $this->smsSender        = new SmsSender(Configure::read('smsconfig'));
        $limit                  = 5;
        
        /**
         * Fetch the Email From the Queue 
         */
        $result['A']     = $this->SmsQueue->find('all',array(
            'conditions'    => array('SmsQueue.status'=> array('inqueue','fail')),
            'order'         => 'SmsQueue.priority ASC',
            'limit'         => $limit,
        ));
        
      
        if(count($result['A'])==0)
            exit('No Sms Schedule yet');
        /**
         * find the Relavent Templates from the datbase
         */
        $templates      = Hash::extract($result['A'],'{n}.SmsQueue.template');
        $result['B']    = $this->SmsTemplate->find('all',array(
            'conditions'    => array('SmsTemplate.template'=> $templates),
        ));
        
        $template       = Hash::combine($result['B'],'{n}.SmsTemplate.template','{n}.SmsTemplate');
        
        /**
         * Get Started With Sending the Email
         */
        foreach(Hash::extract($result['A'],'{n}.SmsQueue') as $smsObject){
            
            /**
             * Prepare the EmailObject
             */
            $flag = $this->smsSender->sms_body($smsObject['mobileno'], $this->SmsTemplate->smsData($template[$smsObject['template']], $smsObject['data']));
            
            /**
             * Update the database Status of the Queue Element
             */
            $this->SmsQueue->updateAll(array(
                'SmsQueue.status' => "'sent'",
            ),array(
                'SmsQueue.id' => $smsObject['id'],
            ));
        }
    }
    
    public function testEmail() {
        $this->Booking->bookingNotification();
    }
    
    public function popup() {   
    }
    
    public function calendar() {
        $area   = (isset($this->request->query['area']) && !empty($this->request->query['area'])) ? $this->request->query['area'] : 'glatbane';
        $weekNo = (isset($this->request->query['week']) && !empty($this->request->query['week'])) ? $this->request->query['week'] : date('W');
        $year   = (isset($this->request->query['year']) && !empty($this->request->query['year'])) ? $this->request->query['year'] : date('Y');
        
        $weekDates = $this->getWholeWeek($weekNo, $year);
        $args['area']           = $area;
        
        //booking calendar
        $this->Session->delete('warningDisplayed');
        // $this->breadcrum('calendar');
        
        $time               = (isset($this->request->query['date']) && !empty($this->request->query['date']))?strtotime($this->request->query['date']):time();
        
        $tracks             = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');
        
        $joins[]    = array(
            'table'         => 'tracks',
            'alias'         => 'Track',
            'type'          => 'LEFT',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                "Track.area_id = Area.slug AND Track.status = 'active'"
            )
        );
        
        $areas              = $this->Area->find('all',array(
            'fields'        => array('Area.name','Area.slug','Count(Track.id) as lane'),
            'joins'         => $joins,
            'group'         => array('Area.slug HAVING Count(Track.id) > 0')
        )); 
        
        $areaTimeSlot       = array();
        
        foreach($areas as $area) {
            foreach($area['AreaTimeSlot'] as $timeslots) {
                $areaTimeSlot[$area['Area']['slug']][] = date('H:i',strtotime($timeslots['time_slots']));
            }
        }
        
        $areaList       = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
        $notifications  = $this->Tnc->notificationCount($this->currentUser['User']['id']);
        $courses        = Hash::combine($this->Course->find('all'),'{n}.Course.id','{n}.Course','{n}.Course.area');
        $students       = $this->User->find('list',array(
            'conditions'    => array('User.role' => 'student'),
            'fields'        => array('id','name')
        ));
        //end-booking calendar
        
        $generatedTimeSlots     = $this->Booking->getBookingTimeSlotsForTheDay($args);
        
        $conditions = array(
            'Booking.area_slug'             => $this->request->query['area'],
            'Booking.date BETWEEN ? AND ?'  => array($weekDates['database'][0],$weekDates['database'][6]),
        );
        $bookings   = $this->BookingTrack->find('all',array(
            'fields'        => array('Booking.area_slug','Booking.date','BookingTrack.id','BookingTrack.booking_id',
                'BookingTrack.time_slot','BookingTrack.track_id'),
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'alias'         => 'Booking',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => $conditions,
        ));
        
        $countTracks = count($tracks[$args['area']]);
        
        $countTracksArr = array();
        foreach($bookings as $booking) {
            $countTracksArr[$booking['Booking']['date']][$booking['BookingTrack']['time_slot']][] = $booking;
        }
        $weekTimeSlots  = array();
        $weekDatesArr = Hash::combine($weekDates,'database.{n}','display.{n}');
        $weekTimeSlots['timeSlot']['label'] = __('Time Slots');
        $weekTimeSlots['timeSlot']['slots'] = $weekDatesArr;
        foreach($generatedTimeSlots['actual'][$args['area']] as $timeSlot) {
            $weekTimeSlots[$timeSlot]['timeSlot'] = $timeSlot;
            foreach($weekDates['database'] as $week) {
                $weekTimeSlots[$timeSlot][$week] = array(
                    'count' => isset($countTracksArr[$week][$timeSlot]) ? count($countTracksArr[$week][$timeSlot]) : $countTracks,
                    'class' => isset($countTracksArr[$week][$timeSlot]) ? ((count($countTracksArr[$week][$timeSlot]) == $countTracks) ? 'all_tracks_booked' : 'some_tracks_booked') : 'no_tracks_booked',
                );
            }
        }
        
        $totalWeeks = date('W', strtotime('28-12-'.$year));
        if($weekNo == 1){
            $prevWeek   = date('W', strtotime('28-12-'. ($year - 1)));
            $nextWeek   = $weekNo + 1;
            
            $prevYear   = $year - 1;
            $nextYear   = $year;
        } else if($weekNo == $totalWeeks) {
            $prevWeek   = $totalWeeks - 1;
            $nextWeek   = 1;
            
            $prevYear   = $year;
            $nextYear   = $year + 1;
        } else {
            $prevWeek   = $weekNo - 1;
            $nextWeek   = $weekNo + 1;
            
            $prevYear   = $year;
            $nextYear   = $year;
        }
        
        $this->set(array(
            'tracks'        => $tracks,
            'areas'         => $areas,
            'areaList'      => $areaList,
            'isEdit'        => FALSE,
            'areaTimeSlot'  => $areaTimeSlot,
            'notifications' => $notifications,
            'courses'       => $courses,
            'students'      => $students,
            'weekTimeSlots' => $weekTimeSlots,
            'prevYear'      => $prevYear,
            'prevWeek'      => $prevWeek, 
            'nextYear'      => $nextYear,
            'nextWeek'      => $nextWeek,
            'calendarTitle' => date('F',strtotime($weekDates['database'][6])).' - '.date('Y',strtotime($weekDates['database'][6])).' - '.__('Week').' '.date('W',strtotime($weekDates['database'][0])),
        ));
        
        if($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->render('Form/week_calendar');
        }
    }
    
    private function getWholeWeek($week,$year) {
        $dateArr = array();
        for($day=1; $day<=7; $day++) {
            $gendate = new DateTime();
            $gendate->setISODate($year,$week,$day);
            $dateArr['display'][]     = array(
                'date'  => $gendate->format('d/m'),
                'day'   => $gendate->format('D'),
            );
            $dateArr['database'][]    = $gendate->format('Y-m-d');
        }
        return $dateArr;
    }
    
    public function closeReopenTrack() {
        $this->Booking->closeReopenTrack();
    }

    public function testCron() {
        //$this->Booking->bookingNotification();

        echo '<br /><br />';
        //$this->Booking->bookingBeforeThreeHourNotification();

        echo '<br /><br />';
        //$this->User->sendBalanceNotificationStudents();

        echo '<br /><br />';
        $this->Booking->checkAndCloseTrack();

        $this->autoRender = false;
    }

    private function sendEmail($emailObject) {
        // $this->EmailSender
        //     ->from(array('noreply@lisbeth.org' => 'KTA Kolding'))
        //     ->to('ranasoyab@gmail.com')
        //     ->subject($emailObject['subject'])
        //     ->addHeaders(array('content-type'=>'text/html'))
        //     ->emailFormat('html')
        //     ->send($emailObject['body']);
        
        return TRUE;   
    }
}