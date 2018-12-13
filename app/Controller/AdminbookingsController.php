<?php
App::import('Controller', 'AdminUsers');

class AdminbookingsController extends AppController 
{
    public $uses        = array('Area','Track','Booking','BookingTrack','AreaTimeSlot',
                                'EmailQueue','SmsQueue','Price','Course','Option','Company',
                                'Systembooking', 'City', 'Product', 'Rnd','UserServices','LatestPayments');
    
    public function beforeRender() {
        if(($this->notifications['count'][0] > 0) && in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))) {
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
        }
    }
    
    public function beforeFilter() {
        parent::beforeFilter();

        $this->Auth->allow(array('getReleasedTracks','updateTrackUser', 'updateGetReleasedTrackStatus'));
    }

    #####################################################
    #
    # New starts here
    #
    ######################################################
    public function listBookings() {
        
        $teachers_list = array();

        if($this->currentUser['User']['role'] == 'admin'){
            //die('Bad Request.Only Teacher can access this page');    
            $args = array(
                'conditions' => array('User.role' => array('internal_teacher','external_teacher')),
                'recursive' => -1, //int
                'order' => 'User.firstname ASC',
                'fields' => array('id', 'firstname', 'lastname'),
               
            );

            $teachers = $this->User->find('all', $args);
            
            if(count($teachers)>0){
                $teachers_list = Set::extract('/User/.',$teachers); 
            }

        }

        if($this->request->query('calview')){
             $calview = true;
             $this->set(array(
                'calview'           => $calview,
            ));
        } else {
            $date = $this->request->query('date');

            if($this->request->query('id')  !== null){
                $user_id = $this->request->query('id');
            }else{
                $user_id = $this->currentUser['User']['id'];
            }
            
            if( empty($date) ){
                $date       = date('Y-m-d');
                $next_date  = date('Y-m-d', strtotime('+1 days'));
                $prev_date  = date('Y-m-d', strtotime('-2 days'));
            } else {
                $date       = date('Y-m-d', strtotime($date));
                $next_date  = date('Y-m-d', strtotime($date . ' +1 days'));
                $prev_date  = date('Y-m-d', strtotime($date . ' -2 days'));
            }

            $tmp_current_bookings   = $this->Systembooking->getAllBookings($user_id, $date, array('pending', 'approved', 'unapproved','passed','dumped'));
            $tmp_current_tracks   = $this->Booking->getAllBookings($user_id, $date);
            $current_bookings       = $this->__processNewBooking( $tmp_current_bookings, $tmp_current_tracks );

            $tmp_next_bookings      = $this->Systembooking->getAllBookings($user_id, $next_date, array('pending', 'approved', 'unapproved','passed','dumped'));
            $tmp_next_tracks        = $this->Booking->getAllBookings($user_id, $next_date);
            $next_bookings          = $this->__processNewBooking( $tmp_next_bookings, $tmp_next_tracks );

            $cities = Hash::combine( $this->City->find('all') ,'{n}.City.id','{n}.City.name');

            $calview = false;
            $tmp_time_ary = array('00:00','01:00','02:00','03:00');
            foreach ($current_bookings as $key => $value) {
                if(in_array($value['start_time'], $tmp_time_ary)){
                    unset($current_bookings[$key]);
                }
            }
            foreach ($next_bookings as $key => $value) {
                if(in_array($value['start_time'], $tmp_time_ary)){
                    $current_bookings[$key] = $value;
                    unset($next_bookings[$key]);
                }
            }
                    // parent::pr($current_bookings);
                    // parent::prd($next_bookings);
            $this->set(array(
                'date'              => $date,
                'calview'           => $calview,
                'next_date'         => $next_date,
                'prev_date'         => $prev_date,
                'current_bookings'  => $current_bookings,
                'next_bookings'     => $next_bookings,
                'cities'            => $cities,
                'teachers_list'     => $teachers_list,
            ));
        }
    }

    public function desync(){
        $studentId = $this->currentUser['User']['id'];
        $student    = $this->User->findById($studentId);
        $this->User->id = $studentId;
        $this->User->saveField('google_token', '' );
        $URL ="http://".$_SERVER['HTTP_HOST'];

        $redirect_URL = $URL.Router::url(array('controller'=>'adminbookings','action'=>'listBookings'));
        header('Location: ' . filter_var($redirect_URL, FILTER_SANITIZE_URL));
        die();      
    }
    public function oauth2callback(){
        $dir =  __DIR__."/../Config";
        $URL ="http://".$_SERVER['HTTP_HOST'];
        $auth_URL = $URL.Router::url(array('controller'=>'adminbookings','action'=>'oauth2callback'));
        $redirect_URL = $URL.Router::url(array('controller'=>'adminbookings','action'=>'listBookings'));
        $client = new Google_Client();
        $client->setAccessType("offline");
        $client->setApprovalPrompt('force');
        $client->setAuthConfigFile($dir.'/client_secret.json');
        $client->setRedirectUri($auth_URL);
        $client->addScope(Google_Service_Calendar::CALENDAR);
        if(!$this->request->query('code')){
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            die();
        }else{
            $a = $client->authenticate($this->request->query('code'));
            $access_token = $client->getAccessToken();
            $access_token = $client->getScopes();
            $refresh_token = $client->getRefreshToken();
            $studentId = $this->currentUser['User']['id'];
            $student    = $this->User->findById($studentId);
            $this->User->id = $studentId;
            $this->User->saveField('google_token', $refresh_token );

            $this->Session->write('google_token',$refresh_token);
            $this->Session->write('google_access_token',$access_token);
            header('Location: ' . filter_var($redirect_URL, FILTER_SANITIZE_URL));
            die();      
        }
    }

    private function __processNewBooking( $bookings, $booking_tracks ){
        $return_bookings = array();

        if( count( $bookings ) > 0 ) {
            foreach ($bookings as $book) {
                unset($book['Systembooking']['created']);
                unset($book['Systembooking']['modified']);

                $user = $book['User'];
                unset($book['User']);

                $book['Systembooking']['date']              = date('Y-m-d', strtotime($book['Systembooking']['start_time']));
                $book['Systembooking']['end_date']              = date('Y-m-d', strtotime($book['Systembooking']['end_time']));
                $book['Systembooking']['start_time']        = date('H:i', strtotime($book['Systembooking']['start_time']));
                $book['Systembooking']['end_time']          = date('H:i', strtotime($book['Systembooking']['end_time']));
                $book['Systembooking']['student_name']      = $user['firstname'] . ' ' . $user['lastname'];
                $book['Systembooking']['student_number']    = $user['student_number'];
                $book['Systembooking']['student_address']    = $user['student_address'];
                $book['Systembooking']['elev_nummer']    = $user['elev_nummer'];

                $return_bookings[ 'B_' . $book['Systembooking']['id'] ] = $book['Systembooking'];
            }
        }

        if( count( $booking_tracks ) > 0 ) {
            foreach ($booking_tracks as $tracks) {
  
                foreach ($tracks['BookingTrack'] as $track) {
                    $student_id = $track['student_id'];
                    $book = array();
                    if(!empty($student_id) ){
                        $User = $this->User->find('first',array(
                            'fields' => array('firstname','lastname','student_number As elev_nummer ', 'lastname', 'phone_no', 'address AS student_address'),
                            'conditions'    => array(
                                'id' => $student_id,
                            )
                        ));
                        if($User != array()){
                            $book['student_name']       = $User['User']['firstname']." ".$User['User']['lastname'];
                            $book['student_number']     = $User['User']['phone_no'];
                            $book['student_address']     = $User['User']['student_address'];
                            $book['elev_nummer']     = $User['User']['elev_nummer'];
                            
                        }else{
                            $book['student_name']       = '';
                            $book['student_number']     = '';
                            $book['student_address']     = '';
                            $book['elev_nummer']     = '';
                        }
                    }else{
                        $book['student_name']       = '';
                        $book['student_number']     = '';
                        $book['student_address']     = '';
                        $book['elev_nummer']     = '';
                    }
                    $timeslot = explode('-', $track['time_slot']);

                    
                    $book['id']                 = $track['id'];
                    $book['date']               = date('Y-m-d', strtotime($tracks['Booking']['date']));
                    $book['booking_type']       = 'Track';
                    $book['start_time']         = ( isset($timeslot[0]) ) ? $timeslot[0] : 0;
                    $book['end_time']           = ( isset($timeslot[1]) ) ? $timeslot[1] : 0;
                    if($book['start_time']  == '22:00'){
                        $book['end_date']               = date('Y-m-d', strtotime($tracks['Booking']['date'].'+1 day'));
                    }else{
                        $book['end_date']               = date('Y-m-d', strtotime($tracks['Booking']['date']));
                    }
                    $book['c_end_time']         = ( isset($timeslot[1]) ) ? ( $timeslot[0] == '22:00' ? '23:59' : $timeslot[0] ) : 0;
                    $book['city_id']            = $tracks['Booking']['area_slug'];
                    $book['note']               = $tracks['Booking']['full_description'];
                    $book['student_id']         = $student_id;
                    // $book['student_name']       = '';
                    // $book['student_number']     = '';

                    $return_bookings[ 'T_' . $track['booking_id'] ] = $book;
                }
            }
        }

        return $return_bookings;
    }
    
    public function create(){
        if(empty($this->request->data['add_type'])){
            $response['status']  = 0;
            $response['message'] = "Please select booking type";
            echo json_encode($response);
            die();
        }

        $response = array();
        $type = $this->request->data['add_type'];
        $currentUser_id = $this->currentUser['User']['id'];

        switch ($type) {
            case 'Køretime':
                if(empty($this->request->data['set_type'])           || empty($this->request->data['book_date'])
                   || empty($this->request->data['time_start'])      || $this->request->data['time_end'] == ""
                   || empty($this->request->data['name_of_student']) || empty($this->request->data["student_id"])){
                    $response['status']  = 0;
                    $response['message'] = "Please enter required fields";
                    echo json_encode($response);
                    die();
                }

                $time = strtotime( $this->request->data['time_start'] .'.00' );
               
                $startMins = date("H:i", strtotime(''.$this->request->data['time_end'] .' minutes', $time));
                $start_date = $this->request->data['book_date'] . " " . $startMins;
               
                $end_date_time = strtotime($start_date);    
                if($this->request->data['set_type'] == 1){
                    $end_date = date("Y-m-d H:i", strtotime('+45 minutes',   $end_date_time));
                } else {
                    $end_date = date("Y-m-d H:i", strtotime('+90 minutes',   $end_date_time));
                }
                $start_date = date("Y-m-d H:i:s", strtotime( $start_date));
                
                $is_overlapping = $this->__checkOverLap( $start_date, $end_date );
                $is_overlapping_user_booking = $this->__checkOverLapStudent( $this->request->data,$start_date ,$startMins);
                if($is_overlapping_user_booking != false || $is_overlapping != false){
                    $response['status']  = 97;
                    $response['message'] = "Bookingen kan ikke gennemføres, da den kollidere med en anden aftale.";
                    echo json_encode($response);
                    die();
                }elseif( !$is_overlapping ) {
                    $neg_overwrite = ( !empty( $this->request->data['neg_overwrite'] ) ) ? $this->request->data['neg_overwrite'] : 0;
                    $product = $this->Product->findByActivityNumber(1201);

                    if($this->request->data['set_type'] == 1){
                        $amt = $product['Product']['price'];
                    } else {
                        $amt = $product['Product']['price'] * 2;
                    }

                    $balance = $this->User->getBalance($this->request->data["student_id"], $amt);

                    $org_balance = ( !empty($balance['originalBalance']) ) ? $balance['originalBalance'] : 0;
                    $left_balance = ( !empty($balance['computedBalance']) ) ? $balance['computedBalance'] : 0;
                    $credit_max = ( !empty($balance['credit_max']) ) ? $balance['credit_max'] : 0;
                    $left_balance_new = ( !empty($balance['computedBalanceNew']) ) ? $balance['computedBalanceNew'] : 0;
                    
                    $org_balance = $this->getUserAvailableBalance($this->request->data["student_id"]);

                    if($org_balance < 0 && $left_balance_new > 0)
                    {
                            $response['status']  = 98;
                            $response['message'] = "Elevens samlede saldo før denne booking er skyldig med -" . $org_balance . "  Kr. Eleven skal indbetale flere penge, hvis der skal bookes flere ydelser.";                            
                            echo json_encode($response);
                            die();
                    }
                    else if( $neg_overwrite == 0 && isset($product['Product']) && isset($product['Product']['price']) && !empty($product['Product']['price']) ) 
                    {
                        if( $credit_max <= $left_balance )
                        {                            
                            $response['status']  = 98;
                            $response['message'] = "Elevens samlede saldo før denne booking er skyldig med -" . $org_balance . "  Kr. Eleven skal indbetale flere penge, hvis der skal bookes flere ydelser.";
                            echo json_encode($response);
                            die();
                        }
                    }

                    $booking['booking_type'] = 'Køretime';
                    $booking['lesson_type']  = $this->request->data['set_type'];
                    $booking['user_id']      = $this->currentUser['User']['id'];
                    $booking['student_id']   = $this->request->data["student_id"];
                    $booking['start_time']   = $start_date;
                    $booking['end_time']     = $end_date;
                    $booking['note']         = $this->request->data["extra_note"];

                    $Systembooking = $this->Systembooking->save($booking);

                    $student    = $this->User->findById($booking['student_id']);
                    // print_r($student);
                    // die();
                    $this->User->id = $booking['student_id'];

                    if($this->request->data['teacher_id'] == 1){
                         $this->User->saveField('teacher_id', $currentUser_id );
                    }
                    if(isset($this->request->data['handed_firstaid_papirs']) && $this->request->data['handed_firstaid_papirs'] != ''){

                        if($this->request->data['handed_firstaid_papirs'] == 1){
                            $this->User->saveField('firstaid_papirs_date', date('Y-m-d') );
                        }
                        $this->User->saveField('handed_firstaid_papirs', $this->request->data['handed_firstaid_papirs'] + $student['User']['handed_firstaid_papirs'] );
                        
                    }
                    if(isset($this->request->data['theory_test_passed'])){
                        $lastFirstaid = $student['User']['handed_firstaid_papirs'];
                        $lastTheory = $student['User']['theory_test_passed'];
                        if($lastFirstaid > 0 && $lastTheory != '')
                        {
                            $crrTheory = $this->request->data['theory_test_passed'];
                            if($crrTheory == 0)
                            {
                                $thrVal = $lastTheory - 1;
                                $this->User->saveField('theory_test_passed', $thrVal );
                            }
                            else{
                                $thrVal = 1 - $lastTheory;
                                $this->User->saveField('theory_test_passed', $thrVal );
                            }
                        }
                        //$this->User->saveField('theory_test_passed', $this->request->data['theory_test_passed'] + $student['User']['theory_test_passed'] );
                        
                        $logdata = array();
                        $logdata['student_id'] = $booking['student_id'];
                        $logdata['new_status'] = $this->request->data['theory_test_passed'];
                        $logdata['old_status'] = $student['User']['theory_test_passed'];
                        $logdata['booking'] = $booking;

                        $this->insertLog('theory_test_chaged', $logdata, $booking['student_id']);
                    }

                    // After validation lets start for coding this booking should not conflict with other bookings.
                    $response['status']  = 1;
                    $response['message'] = "Booking created";
                    $response['balance'] = $left_balance;

                    $student =  $this->User->findById( $booking['student_id'] );
                    $booking['firstname']   = $student['User']['firstname'];
                    $booking['lastname']    = $student['User']['lastname'];
                    $booking['phone_no']    = $student['User']['phone_no'];
                    $booking['other_phone_no']    = $student['User']['other_phone_no'];
                    
                    $this->User->updateBalance( $booking['student_id'], $amt );
                    $this->insertLog('new_cal_driving_time_added', $this->request->data,$booking['student_id']);
                    //Send SMS to Student on Booking
                    $this->__newBookingNotificationToStudent( $booking, 'calendarViewAddBookingNotification', $neg_overwrite , 'new_cal_driving_time_added'  );
                    $g_cal_id = $this->__addBookingtoGoogleCalendar( $booking);
                    $this->Systembooking->id =  $Systembooking['Systembooking']['id'];
                    $this->Systembooking->saveField('g_cal_id', $g_cal_id );
                    
                    // Get new available balance
                    $response['balance'] = $this->getUserAvailableBalance($this->request->data["student_id"]);                    
                } else {
                    $response['status']  = 99;
                    $response['message'] = "BOOKINGEN KAN IKKE GENNEMFØRES";
                }
                echo json_encode($response);
                die();
            break;

            case 'Køreprøve':
                if(empty($this->request->data['book_date'])          || empty($this->request->data['book_date'])
                   || empty($this->request->data['time_start'])      || $this->request->data['time_end'] == ""
                   || empty($this->request->data['name_of_student']) || empty($this->request->data["student_id"]) ){
                    $response['status']  = 0;
                    $response['message'] = "Please enter required fields";
                    echo json_encode($response);
                    die();
                }
                $time = strtotime( $this->request->data['time_start'] .'.00' );
               
                $startMins = date("H:i", strtotime(''.$this->request->data['time_end'] .' minutes', $time));
                $start_date = $this->request->data['book_date']." ".$startMins;
              
                $end_date_time = strtotime($start_date);    
                if($this->request->data['set_type'] == 1){
                    $end_date = date("Y-m-d H:i", strtotime('60 minutes',   $end_date_time));
                }else{
                    $end_date = date("Y-m-d H:i", strtotime('60 minutes',   $end_date_time));
                }
                $start_date = date("Y-m-d H:i:s", strtotime( $start_date));

                $is_overlapping = $this->__checkOverLap( $start_date, $end_date );

                $is_overlapping_user_booking = $this->__checkOverLapStudent( $this->request->data,$start_date ,$startMins);
                if($is_overlapping_user_booking != false || $is_overlapping != false){
                    $response['status']  = 97;
                    $response['message'] = "Bookingen kan ikke gennemføres, da den kollidere med en anden aftale.";
                    echo json_encode($response);
                    die();
                }elseif( !$is_overlapping ) {

                    $neg_overwrite = ( !empty( $this->request->data['neg_overwrite'] ) ) ? $this->request->data['neg_overwrite'] : 0;
                    $product = $this->Product->findByActivityNumber(1201);

                    if($this->request->data['set_type'] == 1){
                            $amt = $product['Product']['price'];
                        } else {
                            $amt = $product['Product']['price'] * 2;
                        }
                    $balance = $this->User->getBalance($this->request->data["student_id"], $amt);

                    $org_balance = ( !empty($balance['originalBalance']) ) ? $balance['originalBalance'] : 0;
                    $left_balance = ( !empty($balance['computedBalance']) ) ? $balance['computedBalance'] : 0;
                    $credit_max = ( !empty($balance['credit_max']) ) ? $balance['credit_max'] : 0;

                    if( $neg_overwrite == 0 && isset($product['Product']) && isset($product['Product']['price']) && !empty($product['Product']['price']) ) {


                        if(  $credit_max <= $left_balance){
                            $response['status']  = 98;
                            $response['message'] = "Elevens samlede saldo før denne booking er skyldig med " . $org_balance . "  Kr. Eleven skal indbetale flere penge, hvis der skal bookes flere ydelser.";
                            echo json_encode($response);
                            die();
                        }
                    }
                    
                    $booking['booking_type'] = 'Køreprøve';
                    $booking['user_id']      = $this->currentUser['User']['id'];
                    $booking['lesson_type']  = 1;
                    $booking['student_id']   = $this->request->data["student_id"];
                    $booking['start_time']   = $start_date;
                    $booking['end_time']     = $end_date;
                    $booking['note']         = $this->request->data["extra_note"];
                    $Systembooking = $this->Systembooking->save($booking);
                    $this->insertLog('new_cal_driving_test_added', $this->request->data,$booking['student_id']);
                    // After validation lets start for coding this booking should not conflict with other bookings.
                    $response['status']  = 1;
                    $response['message'] = "Booking created";
                    $response['balance'] = $left_balance;

                    $student =  $this->User->findById( $booking['student_id'] );
                    $booking['firstname']   = $student['User']['firstname'];
                    $booking['lastname']    = $student['User']['lastname'];
                    $booking['phone_no']    = $student['User']['phone_no'];
                    $booking['other_phone_no']    = $student['User']['other_phone_no'];

                    $this->User->updateBalance( $booking['student_id'], $amt );

                    if($this->request->data['teacher_id'] == 1){
                         $this->User->saveField('teacher_id', $currentUser_id );
                    }

                    $this->User->id = $booking['student_id'];
                    if(isset($this->request->data['handed_firstaid_papirs']) && $this->request->data['handed_firstaid_papirs'] != ''){
                        if($this->request->data['handed_firstaid_papirs'] == 1){
                            $this->User->saveField('firstaid_papirs_date', date('Y-m-d') );
                        }
                        $this->User->saveField('handed_firstaid_papirs', $this->request->data['handed_firstaid_papirs'] + $student['User']['handed_firstaid_papirs'] );
                        
                    }
                    if(isset($this->request->data['theory_test_passed'])){

                        $this->User->saveField('theory_test_passed', $this->request->data['theory_test_passed'] + $student['User']['theory_test_passed'] );
                        
                        $logdata = array();
                        $logdata['student_id'] = $booking['student_id'];
                        $logdata['new_status'] = $this->request->data['theory_test_passed'];
                        $logdata['old_status'] = $student['User']['theory_test_passed'];
                        $logdata['booking'] = $booking;

                        $this->insertLog('theory_test_chaged', $logdata, $booking['student_id']);
                    }
                    
                    //Send SMS to Student on Booking
                    $this->__newBookingNotificationToStudent( $booking, 'calendarViewAddBookingNotification', $neg_overwrite ,'new_cal_driving_test_added' );
                    $g_cal_id = $this->__addBookingtoGoogleCalendar( $booking);
                    $this->Systembooking->id =  $Systembooking['Systembooking']['id'];
                    $this->Systembooking->saveField('g_cal_id', $g_cal_id );

                    // Get new available balance
                    $response['balance'] = $this->getUserAvailableBalance($this->request->data["student_id"]);
                } else {
                    $response['status']  = 99;
                    $response['message'] = "BOOKINGEN KAN IKKE GENNEMFØRES";
                }
                echo json_encode($response);
                die();
            break; 
            
            case 'Teori':
                if(empty($this->request->data['city_id']) || empty($this->request->data['time_start_from'])
                   || $this->request->data['time_start_from_min'] == '' || $this->request->data['time_start_to'] == ""
                   || $this->request->data['time_start_to_min'] == '' ){
                    $response['status']  = 0;
                    $response['message'] = "Please enter required fields";
                    echo json_encode($response);
                    die();
                }
                $time_start = strtotime($this->request->data['time_start_from'] .'.00' );
                $startMins = date("H:i", strtotime(''.$this->request->data['time_start_from_min'] .' minutes', $time_start));
                $start_date = $this->request->data['book_date']." ".$startMins;
                $time_end = strtotime($this->request->data['time_start_to'] .'.00' );   
                $endMins = date("H:i", strtotime(''.$this->request->data['time_start_to_min'] .' minutes', $time_end));
                $end_date = $this->request->data['book_date']." ".$endMins;               
                $start_date = date("Y-m-d H:i:s", strtotime( $start_date));
                $end_date = date("Y-m-d H:i:s", strtotime( $end_date));
                if($end_date <  $start_date){
                    $response['status']  = 0;
                    $response['message'] = "Please selecte valid time";
                    echo json_encode($response);
                    die();
                } 
               
                $is_overlapping = $this->__checkOverLap( $start_date, $end_date );
                if( !$is_overlapping ) {
                    $booking['booking_type'] = 'Teori';
                    $booking['user_id']      = $this->currentUser['User']['id'];
                    $booking['city_id']      = $this->request->data["city_id"];
                    $booking['start_time']   = $start_date;
                    $booking['end_time']     = $end_date;
                   
                    $Systembooking = $this->Systembooking->save($booking);
                    $city_name = $this->City->find('first',array(
                                                            'fields' => array('name'),
                                                            'conditions'    => array(
                                                                            'id' => $booking['city_id'],
                                                                        )
                                                            )
                                                        );
                    $booking['firstname'] = "City";
                    $booking['lastname'] = $city_name['City']['name'];
                    $g_cal_id = $this->__addBookingtoGoogleCalendar( $booking);
                    $this->Systembooking->id =  $Systembooking['Systembooking']['id'];
                    $this->Systembooking->saveField('g_cal_id', $g_cal_id );
                        
                    $this->insertLog('new_cal_theory_added', $this->request->data);
                    // After validation lets start for coding this booking should not conflict with other bookings.
                    $response['status']  = 1;
                    $response['message'] = "Booking created";
                } else {
                    $response['status']  = 99;
                    $response['message'] = "BOOKINGEN KAN IKKE GENNEMFØRES";
                }
                echo json_encode($response);
                die();
            break;

            case 'Privat':
                if(empty($this->request->data['book_date']) || empty($this->request->data['time_start_from'])
                   || $this->request->data['time_start_from_min'] == '' || $this->request->data['time_start_to'] == ""
                   || $this->request->data['time_start_to_min'] == '' ){
                    $response['status']  = 0;
                    $response['message'] = "Please enter required fields";
                    echo json_encode($response);
                    die();
                }
                $time_start = strtotime($this->request->data['time_start_from'] .'.00' );
                $startMins = date("H:i", strtotime(''.$this->request->data['time_start_from_min'] .' minutes', $time_start));
                $start_date = $this->request->data['book_date']." ".$startMins;

                $time_end = strtotime($this->request->data['time_start_to'] .'.00' );   
                $endMins = date("H:i", strtotime(''.$this->request->data['time_start_to_min'] .' minutes', $time_end));
                $end_date = $this->request->data['book_date']." ".$endMins;               
                $start_date = date("Y-m-d H:i:s", strtotime( $start_date));
                $end_date = date("Y-m-d H:i:s", strtotime( $end_date));
                if($end_date <  $start_date){
                    $response['status']  = 0;
                    $response['message'] = "Please selecte valid time";
                    echo json_encode($response);
                    die();
                } 
               
                $is_overlapping = $this->__checkOverLap( $start_date, $end_date );

                if( !$is_overlapping ) {
                    $booking['booking_type'] = 'Privat';
                    $booking['user_id']      = $this->currentUser['User']['id'];
                    $booking['start_time']   = $start_date;
                    $booking['end_time']     = $end_date;
                    $booking['note']         = $this->request->data["extra_note"];
                    $Systembooking = $this->Systembooking->save($booking);
                    $g_cal_id = $this->__addBookingtoGoogleCalendar( $booking);
                    $this->Systembooking->id =  $Systembooking['Systembooking']['id'];
                    $this->Systembooking->saveField('g_cal_id', $g_cal_id );
                    $this->insertLog('new_cal_private_added', $this->request->data);
                    // After validation lets start for coding this booking should not conflict with other bookings.
                    $response['status']  = 1;
                    $response['message'] = "Booking created";
                } else {
                    $response['status']  = 99;
                    $response['message'] = "BOOKINGEN KAN IKKE GENNEMFØRES";
                }

                echo json_encode($response);
                die();
            break;   
            
            default:
                $response['status']  = 0;
                $response['message'] = "Please select booking type";
                echo json_encode($response);
                die();
            break;
        }
    }

    function getUserAvailableBalance($userId)
    {
        $user = $this->User->findById($userId);
        $returnBalance = 0;

        if(!empty($user))
        {
            $Total_crm_in = 0;
            $gtotal = 0;
            $Balance = 0;
            $UserServices_total = 0;

            $conditions = array();
            $conditions['user_id'] = $userId;
            $UserServices  = $this->UserServices->find('all',
                             array
                             (
                                'conditions'   => $conditions,
                                'order'         => array('posting_date' => 'ASC'),
                                'group'         => array('id')
                             ));


            $conditions = array();
            $currentDate    = date('Y-m-d H:i:s',time());
            $conditions['student_id'] = $userId;
            $conditions[] = "status != 'delete'";
            $conditions[] = "status != 'approved'";
            $conditions[] = "status != 'unapproved'";
            $conditions[] = "status != 'passed'";
            $Systembooking = $this->Systembooking->find('all',
            array
            (
                'conditions'    => $conditions,
                'order'         => array('start_time' => 'ASC')
            ));                           

            $student_number = $user['User']['student_number'];  

            $Payments = array();
            if(!empty($student_number))
            {
                $Payments = $this->LatestPayments->find('all', 
                            array
                            ( 'conditions' => array(
                                        'DebitorNummer' => $student_number
                            )));

                foreach ($Payments as $key => $Payment) 
                {
                    $Payment = (object)$Payment['LatestPayments'];
                    $Total_crm_in = $Total_crm_in + round($Payment->Kredit);
                }                
            }            

            foreach($UserServices as $UserService)
            {
                $total_price = number_format($UserService['UserServices']['total_price'], 2, '.', '');
                $UserServices_total +=  $total_price;
            }            

            foreach($Systembooking as $booking)
            {
                $type = ($booking['Systembooking']['lesson_type']  != '') ? $booking['Systembooking']['lesson_type'] : '1' ;
                $total =  $type*500;
                $gtotal = $gtotal + $total;
            }            

            if($Total_crm_in < 0){
                $Balance =  $Total_crm_in - $UserServices_total;
            }
            else
            {
                $Balance =  (-$Total_crm_in) + $UserServices_total;
            }

            $returnBalance = $Balance + $gtotal;
        }

        return $returnBalance;
    }


    private function __checkOverLapStudent( $data, $date,$time){
        $dbo = $this->Systembooking->getDatasource();
        $systembooking_count   = $this->Systembooking->getAllBookingsofStudent($data['student_id'], $date, array('pending', 'approved', 'unapproved'));
        $date = date('Y-m-d', strtotime($date));
        $booking_count   = $this->Booking->countNumberOfBookingsbyStudent($data['student_id'], $date,$time);

        if( $systembooking_count > 0 ) {
            return true;                    
        } else if( $booking_count > 0 ) {
            return true;
        }
           
        return false;
    }
    private function __checkOverLap( $start_date_time, $end_date_time ){
        $date = date('Y-m-d', strtotime($start_date_time));
        $tmp_current_bookings   = $this->Systembooking->getAllBookings($this->currentUser['User']['id'], $date, array('pending', 'approved', 'unapproved'));
        $tmp_current_tracks   = $this->Booking->getAllBookings($this->currentUser['User']['id'], $date);
        $current_bookings       = $this->__processNewBooking( $tmp_current_bookings, $tmp_current_tracks );
        if ( count($current_bookings) > 0) {
            foreach ($current_bookings as $booking) {
                $cst = strtotime( $booking['date'] . ' ' . $booking['start_time'] );
                $cet = strtotime( $booking['end_date'] . ' ' . $booking['end_time'] );
                $bst = strtotime( $start_date_time );
                $bet = strtotime( $end_date_time );
                $a = $this->isBetween($cst, $bet, $bst);
                $b = $this->isBetween($cet, $bet, $bst);
                $c = $this->isBetween($bst, $cet, $cst);
                $d = $this->isBetween($bet, $cet, $cst);
                if( $a == true || $b == true || $c == true || $c == true ) {
                    return true;                    
                }
            }
        }

        return false;
    }
    public function isBetween ( $varToCheck, $high, $low){
        if($varToCheck <= $low){
            return false;
        }
        if($varToCheck >= $high){
            return false;
        } 
        return true;
    }

    public function getTeacherBookings(){
        if($this->currentUser['User']['role'] != 'admin'){
            die('Bad Request.Only Admin can access this page');
        }

        $this->perPage  = $this->getPerPage('Systembooking');
        
        $joins      = array();  
        
        $joins[]    = array(
            'table'         => 'users',
            'alias'         => 'User',
            'type'          => 'INNER',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                'Systembooking.user_id = User.id'
            )
        );

        $args       = array(
            'fields'        => array( 'Systembooking.*', 'User.*' ),
            'conditions'    => array( 'Systembooking.booking_type !=' => 'Privat' ),
            'joins'         => $joins,
            'limit'         => $this->perPage,
            'order'         => array('Systembooking.id' => 'DESC')
        );
        
        $this->Paginator->settings = $args;
        
        $bookings  = $this->Paginator->paginate('Systembooking');

        $args = array(
            'conditions' => array('User.role' => 'student'), //array of conditions
            'recursive' => -1, //int
            'fields' => array('User.id', 'User.firstname', 'User.lastname'),
           
        );
        $students = $this->User->find('all', $args);
        $student_list = array();
        if(count($students)>0){
            foreach($students as $student){
                $name = $student['User']['firstname'] ." ".$student['User']['lastname']; 
                $student_list[$student['User']['id']] = array("name"=>$name); 
            }
        }
      
        $this->set(array(
            'bookings'     => $bookings,
            'student_list' => $student_list,
            'perPage'      => $this->perPage,
            'role'         => $this->currentUser['User']['role']
        ));    
    }

    public function getMyBookings(){
        if( !in_array($this->currentUser['User']['role'], array('internal_teacher','external_teacher')) ){
            die('Bad Request.Only Teacher can access this page');
        }

        $this->perPage  = $this->getPerPage('Systembooking');
        
        $joins      = array();  
        
        $joins[]    = array(
            'table'         => 'users',
            'alias'         => 'User',
            'type'          => 'INNER',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                'Systembooking.user_id = User.id'
            )
        );
        

        $args       = array(
            'fields'        => array('Systembooking.*','User.*'),
            'conditions'    => array('Systembooking.user_id' => $this->currentUser['User']['id'], 'Systembooking.status' => 'pending'),
            'joins'         => $joins,
            'limit'         => $this->perPage,
            'order'         => array('Systembooking.id' => 'DESC')
        );
        
        $this->Paginator->settings = $args;
        
        $bookings  = $this->Paginator->paginate('Systembooking');

        $args = array(
            'conditions' => array('User.role' => 'student'), //array of conditions
            'recursive' => -1, //int
            'fields' => array('User.id', 'User.firstname', 'User.lastname'),
           
        );
        $students = $this->User->find('all', $args);
        $student_list = array();
        if(count($students)>0){
            foreach($students as $student){
                $name = $student['User']['firstname'] ." ".$student['User']['lastname']; 
                $student_list[$student['User']['id']] = array("name"=>$name); 
            }
        }
      
        $this->set(array(
            'bookings'     => $bookings,
            'student_list' => $student_list,
            'perPage'      => $this->perPage,
            'role'         => $this->currentUser['User']['role']
        ));    
    }

    public function TeacherListBookings() {
        if($this->currentUser['User']['role'] != 'admin'){
            die('bad Request.');    
        }

        $args = array(
            'conditions' => array('User.role' => array('internal_teacher','external_teacher')), //array of conditions
            'recursive' => -1, //int
            'order' => 'User.firstname ASC',
            'fields' => array('User.id', 'User.firstname', 'User.lastname'),
           
        );

        $teachers = $this->User->find('all', $args);
        $teachers_list = array();

        if(count($teachers)>0){
            foreach($teachers as $teacher){
                $name = $teacher['User']['firstname'] ." ".$teacher['User']['lastname']; 
                $teachers_list[$teacher['User']['id']] = array("name"=>$name); 
            }
        }

        if($this->request->query('teacher')){
            $t_id = $this->request->query('teacher');
            $date = $this->request->query('date');
            
            
            if( empty($date) ){
                $date       = date('Y-m-d');
                $next_date  = date('Y-m-d', strtotime('+1 days'));
                $prev_date  = date('Y-m-d', strtotime('-2 days'));
            } else {
                $date       = date('Y-m-d', strtotime($date));
                $next_date  = date('Y-m-d', strtotime($date . ' +1 days'));
                $prev_date  = date('Y-m-d', strtotime($date . ' -2 days'));
            }

            $tmp_current_bookings   = $this->Systembooking->getAllBookings($t_id, $date, array('pending', 'approved', 'unapproved'));
            $tmp_current_tracks   = $this->Booking->getAllBookings($t_id, $date);
            $current_bookings       = $this->__processNewBooking( $tmp_current_bookings, $tmp_current_tracks );

            $tmp_next_bookings      = $this->Systembooking->getAllBookings($t_id, $next_date, array('pending', 'approved', 'unapproved'));
            $tmp_next_tracks        = $this->Booking->getAllBookings($t_id, $next_date);
            $next_bookings          = $this->__processNewBooking( $tmp_next_bookings, $tmp_next_tracks );

            $calview = false;
            if($this->request->query('calview')){
                $calview = true;
            }

            $this->set(array(
                'date'              => $date,
                'calview'           => $calview,
                'next_date'         => $next_date,
                'prev_date'         => $prev_date,
                'current_bookings'  => $current_bookings,
                'next_bookings'     => $next_bookings,
                'teachers_list' => $teachers_list 
            ));
        } else {
            $this->set(array('teachers_list' => $teachers_list ));  
        }
    }

    public function updateBookingStatus() {
        $this->autoRender = false;
        $post = $this->request->data;
        $studentMarkedAsCompleted = false;

        if(is_null( $post['booking_id'] )) {
            $data = array('status' => 0, 'msg' => 'Something Went Worng. Please Try Again.');
        }

        if($this->currentUser['User']['role'] == 'admin'){
            $args = array( 'id'=> $post['booking_id'] );
        }else{
            $args = array( 'user_id' => $this->currentUser['User']['id'], 'id'=> $post['booking_id'] );
        }

        $booking   = $this->Systembooking->find('first',array(
                'conditions'    => $args
            ));
        if(empty($booking)) {
            $data = array('status' => 0, 'msg' => 'No Booking Found. Please Try Again.');
        } else {
            $booking = $booking['Systembooking'];
            $booking_type = $booking['booking_type'];
            $send_sms = false;
            $send_crm = false;
            $g_booking_delete = false;
            $send_mail_to_admin = false;

            // Update private appointment notes
            if($post['action'] == "update_appointment")
            {
                $this->Systembooking->id = $post['booking_id'];
                $this->Systembooking->saveField('note', $post['note']);
                $data = array('status' => 1, 'msg' => 'Done Success');
                echo json_encode($data);
                die();                
            }


            if( $post['action'] == '3') {
                $status = 'delete';
                $send_sms = true;
                $g_booking_delete = true;
            } else if( $post['action'] == '2') {
                $status = 'unapproved';
            } else if( $post['action'] == '4') {
                $status = 'passed';
                $send_crm = true;
            } else if( $post['action'] == '5') {
                // approved and passed
                $status = 'passed';
                $send_crm = true;
                $send_mail_to_admin = true;
                $studentMarkedAsCompleted = true;
            } else if( $post['action'] == '6') {
                $status = 'dumped';
                $send_crm = true;
            } else if( $post['action'] == '1') {
                $status = 'approved';
                $send_crm = true;
            } else {
                $status = 'pending';
            }

            $this->Systembooking->id = $post['booking_id'];
            $this->Systembooking->saveField('status', $status);

            if($booking['status'] != $status){
                $logdata = $this->request->data;
                $logdata['booking_type'] = $booking_type;
                $logdata['new_status'] = $status;
                $logdata['old_status'] = $booking['status'];
                $logdata['booking'] = $booking;
                
                $student_id = !empty( $booking['student_id'] ) ? $booking['student_id'] : 0;
                $this->insertLog('new_cal_update', $logdata, $student_id);
            }
            
            if( !empty( $booking['student_id'] ) ) {
                $student =  $this->User->findById( $booking['student_id'] );

                // if student mark as completed
                if($studentMarkedAsCompleted)
                {
                    $expiry_date = date('Y-m-d', strtotime("+30 days"));
                    $dataToUpdateForStudent = 
                    [
                        "is_completed" => 1,
                        "expiry_date" => $expiry_date
                    ];                    
                    $this->User->id = $booking['student_id'];
                    $this->User->save($dataToUpdateForStudent);
                }                


                if($status == 'delete'){

                    $product = $this->Product->findByActivityNumber(1201);
                    if($booking['lesson_type'] == 1){
                        $amt = $product['Product']['price'];
                    } else {
                        $amt = $product['Product']['price'] * 2;
                    }

                    $this->User->id = $booking['student_id'];
                    $available_balance = (float)$student['User']['balance'];
                    $available_balance = $available_balance - $amt;
                    $this->User->saveField('available_balance', $available_balance );
                    $this->User->saveField('balance', $available_balance );
                }
                $booking['firstname']   = $student['User']['firstname'];
                $booking['lastname']    = $student['User']['lastname'];
                $booking['phone_no']    = $student['User']['phone_no'];
                $booking['other_phone_no']    = $student['User']['other_phone_no'];
                $booking['student_number']    = $student['User']['student_number'];
                $booking['city']    = $student['User']['city'];
                $params = array();
                $ydelsesData = array();
                $params['KundeID'] = '2795cd76-0a62-4f1c-994b-f5bfbdbf24d1';
                $ydelsesData['Elevnummer'] = strval($student['User']['student_number']);
                //'000514091101030';
                $ydelsesData['AssistentNummer'] = strval($booking['user_id']);
                $current_time = date('Y-m-d H:i:s');
                $ydelsesData['PosteringsDato'] = date(DATE_ATOM,strtotime($current_time));
                $date1 = $booking['start_time'];
                $date2 = $booking['end_time'];
                $d1= new DateTime($date1);
                $d2 = new DateTime($date2);
                $interval= $d1->diff($d2);
                $hours = $interval->h;
                $minutes = (($hours*60)+$interval->i);
                $hour = intval($minutes/45);
                $ydelsesData['Antal'] = $hour;
                if( $send_mail_to_admin == true ) {
                    $this->__NotificationToAdmin( $booking );
                }
                
                if( $send_sms ) {
                    //Send SMS to Student on Booking
                    
                    if($booking_type == 'Køretime'){
                        $log_type = 'new_cal_driving_time';
                    }elseif ($booking_type == 'Køreprøve') {
                        $log_type = 'new_cal_driving_test';
                    }elseif ($booking_type == 'Teori') {
                        $log_type = 'new_cal_theory';
                    }elseif ($booking_type == 'Privat') {
                        $log_type = 'new_cal_private';
                    }else{
                        $log_type = 'new_cal';
                    }
                    $log_type .= '_delete'; 
                    $this->__newBookingNotificationToStudent( $booking, 'calendarViewDeleteBookingNotification' , '' ,$log_type );
                }

                if( $post['action'] == '2') {
                    $booking['activity_name']    = 1205;
                }elseif($post['action'] == '1' || $post['action'] == '5' || $post['action'] == '6') {
                    if( $booking['booking_type'] == 'Køretime') {
                        $booking['activity_name']    = 1201;
                    } else if( $booking['booking_type'] == 'Køreprøve') {
                        $booking['activity_name']    = 1207;
                    }
                }

                if( $post['action'] == '2' || $post['action'] == '1' || $post['action'] == '5' || $post['action'] == '6') {
                    $booking['username']    = $student['User']['username'];

                    $product = $this->Product->findByActivityNumber( $booking['activity_name'] );
                    $amt = 0;
                    if(isset($product['Product']) && isset($product['Product']['price']) && !empty($product['Product']['price']) ) {
                        $amt = $product['Product']['price'];
                    }

                    $booking['price']           = $amt;
                    $ydelsesData['Pris'] = $amt;
                    $ydelsesData['KontoNummer'] = strval($booking['activity_name']);
                    // print_r($ydelsesData);
                    // die();
                    $params['ydelsesData'] = $ydelsesData;
                    $this->submitCRMdata($params);
                }
            }
            $User =  $this->User->findById( $booking['user_id'] );
            if( $g_booking_delete == true && $booking['g_cal_id'] != '' && $User['User']['google_token'] !=  '' ) {
                $this->__deleteBookingtoGoogleCalendar( $booking['g_cal_id'] ,$User['User']['google_token'] );
            }
            $data = array('status' => 1, 'msg' => 'Done Success');
        }

        echo json_encode($data);
        die();
    }

    private function __addBookingtoGoogleCalendar( $data){

        if($this->currentUser['User']['google_token'] != ''){
            if($data['booking_type'] == 'Køretime' || $data['booking_type'] == 'Køreprøve' || $data['booking_type'] == 'Teori'){
                $event_data['summary'] = $data['booking_type']." Booking for ".$data['firstname']." ".$data['lastname'];
            }else{
                $event_data['summary'] = $data['booking_type']." Booking";
            }
            $event_data['description'] = $data['note'];
            $event_data['start']['dateTime'] = date('c',strtotime($data['start_time']));
            $event_data['start']['timeZone'] = 'Europe/Copenhagen';
            $event_data['end']['dateTime'] = date('c', strtotime($data['end_time']));
            $event_data['end']['timeZone'] = 'Europe/Copenhagen';

            $client = new Google_Client();
            $dir =  __DIR__."/../Config";
            $client->setAuthConfigFile($dir.'/client_secret.json');

            $token = $client->refreshToken($this->currentUser['User']['google_token']);
            $client->setAccessToken($token);
            $service  = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event($event_data);
            $calendarId = 'primary';
            $event = $service->events->insert($calendarId, $event);
            return $event->id;
        }

        return true;
    }
    private function __deleteBookingtoGoogleCalendar( $eventId,$google_token){

        $client = new Google_Client();
        $dir =  __DIR__."/../Config";
        $client->setAuthConfigFile($dir.'/client_secret.json');

        $token = $client->refreshToken($google_token);
        $client->setAccessToken($token);
        $service  = new Google_Service_Calendar($client);
        $temp = $service->events->delete('primary', $eventId);
        return true;
    }

    private function __newBookingNotificationToStudent( $data, $template, $neg_overwrite,$log_type = '' ){

        $tmp_sms_details = array(
                                'data'          => array(
                                    'User'      => array(
                                        'firstname' => $data['firstname'],
                                        'lastname'  => $data['lastname'],
                                        'bookedby'  => $this->currentUser['User']['firstname'] . ' ' . $this->currentUser['User']['lastname'],
                                        'date'      => date('d-m-Y H:i', strtotime($data['start_time'])),
                                        'type'      => $data['booking_type'],
                                        'note'      => $data['note'],
                                     )
                                ),
                                'template'      => $template,
                                'priority'      => 0,
                                'instant'       => TRUE
                            );

        $tmp_sms_details['mobileno'] = (substr($data['mobileno'], 0, 2) == '45') ? '+' . $data['mobileno'] : '+45'.$data['mobileno'];
        $this->SmsQueue->bookingDetails($tmp_sms_details);
        $this->insertLog($log_type.'_sms', $tmp_sms_details, $data['student_id']);

        if(isset($data['other_phone_no']) && $data['other_phone_no'] != ''){
            $tmp_sms_details['mobileno'] = (substr($data['other_phone_no'], 0, 2) == '45') ? '+' . $data['other_phone_no'] : '+45'.$data['other_phone_no'];
            $this->SmsQueue->bookingDetails($tmp_sms_details);
            $this->insertLog($log_type.'_sms', $tmp_sms_details, $data['student_id']);
        }

        if( isset($neg_overwrite) && !empty($neg_overwrite) && $neg_overwrite == 1 ){
            $tmp_email_details = array(
                'email'         => 'morten.s@lisbeth.dk',
                'data'          => array(
                    'Booking'      => array(
                        'firstname'         => $data['firstname'],
                        'lastname'          => $data['lastname'],
                        'bookingdate'       => date('d-m-Y h:i A', strtotime($data['start_time'])),
                        'teacher_name'      => $this->currentUser['User']['firstname'] . ' ' . $this->currentUser['User']['lastname'],
                        'booked_date_time'  => date('d-m-Y h:i A'),
                        
                     )
                ),
                'template'      => 'negativeBalanceNotification',
                'priority'      => 0,
            );

            $this->EmailQueue->addBooking($tmp_email_details);
            $this->insertLog('negative_balance_email', $tmp_email_details);
        }

        return true;
    }

    private function __SMSToStudent( $tmp_sms_details, $to, $to1, $student_id='', $log_type = '' ){

        $tmp_sms_details['mobileno'] = (substr($to, 0, 2) == '45') ? '+' . $to : '+45'.$to;
        $this->SmsQueue->bookingDetails($tmp_sms_details);
        if($log_type != ''){
            $this->insertLog($log_type, $tmp_sms_details, $student_id);
        }

        if(isset($to1) && $to1 != ''){
            $tmp_sms_details['mobileno'] = (substr($to1, 0, 2) == '45') ? '+' . $to1 : '+45'.$to1;
            $this->SmsQueue->bookingDetails($tmp_sms_details);
            if($log_type != ''){
                $this->insertLog($log_type, $tmp_sms_details, $student_id);
            }
        }

        return true;
    }

    private function __NotificationToAdmin( $data ){

        $tmp_email_details = array(
            'email'         => 'ravi.gajera@phpdots.com',
            'email'         => 'annemette.l@lisbeth.dk',
            'data'          => array(
                'Booking'      => array(
                    'firstname'         => $data['firstname'],
                    'lastname'          => $data['lastname'],
                    'city'          => $data['city'],
                    'student_number'          => $data['student_number'],
                    'bookingdate'       => date('d/m/y H:i', strtotime($data['start_time'])),
                    'teacher_name'      => $this->currentUser['User']['firstname'] . ' ' . $this->currentUser['User']['lastname'],
                    'booked_date_time'  => date('d/m/y H:i'),
                    
                 )
            ),
            'template'      => 'drivingTestStudentPassedAdmin',
            'priority'      => 0,
        );

        $this->EmailQueue->addBooking($tmp_email_details);
        $this->insertLog('driving_test_student_passed_admin', $tmp_email_details);

        return true;
    }

    private function __sendCRMNotification( $data ) {
        $args = array('parameters' => 
            array(
                'KundeID'       => KUNDEID,
                'ydelsesData'   => array(
                    'Elevnummer'        => $data['student_number'],
                    'KontoNummer'       => ( string )$data['activity_name'],
                    'Antal'             => '1',
                    'AssistentNummer'   => '0e460684-3cbb-4fae-90e0-f149362acdd3',
                    'PosteringsDato'    => date('Y-m-d\TH:i:s'),
                    'Pris'              => number_format($data['price'], 2)
                )
            )
        );
        $result = $this->Rnd->query( 'CreateDebitorYdelse', $args );

        if( isset( $result->CreateDebitorYdelseResult ) && strtoupper( $result->CreateDebitorYdelseResult ) == 'OK'){
            return true;
        } else {
            return false;
        }
    }

    public function getTeacherBookingReport(){
        if($this->currentUser['User']['role'] != 'admin'){
            die('Bad Request.Only Admin can access this page');
        }
    }

    public function getTeacherBookingReportData(){

        $from_date = $this->request->query('from_date');
        $end_date = $this->request->query('end_date');

        if( $from_date == '' ) {
            $from_date = date('Y-m-d');
        } else {
            $from_date = date('Y-m-d', strtotime($from_date));
        }

        if( $end_date == '' ) {
            $end_date = date('Y-m-d');
        } else {
            $end_date = date('Y-m-d', strtotime($end_date));
        }

        $this->autoRender = false;

        $details = $this->Booking->getBookingTotalTime( $from_date, $end_date );

        $html = '<tr><td colspan="10">No Records Found</td></tr>';
        
        if( !empty($details) ) {
            $html = '';

            foreach ($details as $detail) {
                $html .= '<tr>';
                    $html .= '<td>' . $detail['name'] . '</td>';
                    $html .= '<td>' . $detail['city'] . '</td>';

                    $koretime = ( !empty($detail['status']['kretime']) ) ? $detail['status']['kretime'] / 60 : 0;
                    $koreprove = ( !empty($detail['status']['kreprve']) ) ? $detail['status']['kreprve'] / 60 : 0;

                    $html .= '<td align="right">' . $koretime . '</td>';
                    $html .= '<td align="right">' . $koreprove . '</td>';

                    $html .= '<td align="right"><strong>' . ($koretime + $koreprove)  . '</strong></td>';

                    $teori = ( !empty($detail['status']['teori']) ) ? $detail['status']['teori'] / 60 : 0;
                    $privat = ( !empty($detail['status']['privat']) ) ? $detail['status']['privat'] / 60 : 0;
                    $track = ( !empty($detail['status']['track']) ) ? $detail['status']['track'] / 60 : 0;

                    $html .= '<td align="right">' . $teori . '</td>';
                    $html .= '<td align="right">' . $track . '</td>';
                    $html .= '<td align="right">' . $privat . '</td>';
                    $html .= '<td align="right"><strong>' . ($teori + $track + $privat)  . '</strong></td>';

                    $html .= '<td align="right"><strong>' . ($koretime + $koreprove + $teori + $track + $privat)  . '</strong></td>';
                $html .= '</tr>';
            }
        }

        echo $html;
    }
    #######################################################
    #
    # Old starts here
    #
    ########################################################
    private function breadcrum($case,$booking = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Bookings'),
            'url'   => Router::url(array('controller'=>'adminbookings','action'=>'calendar')),
        );
        
        switch ($case) {
            case 'add':
                
                $pageTitle[] = array(
                    'name'  => __('Add Booking'),
                     'url'   => '#',
                );
                
                break;
            
            case 'view':
                
                $pageTitle[] = array(
                    'name'      => __('Booking For ').$this->areaListArr[$booking['Booking']['area_slug']],
                    'url'       => '#',
                );
                
                break;
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'      => __('Booking For ').$this->areaListArr[$booking['Booking']['area_slug']],
                    'url'       => Router::url(array('controller'=>'adminbookings','action'=>'view',$booking['Booking']['id'])),
                );
                
                $pageTitle[] = array(
                    'name'      => __('Edit '),
                    'url'       => '#',
                );
                
                break;
            
            case 'calendar': 
                
                $pageTitle[]    = array(
                    'name'  => __('Calendar'),
                    'url'   => '#',
                );
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        //$this->Booking->bookingNotification();
        $this->perPage  = $this->getPerPage('Booking');
        $nextBooking    = array();
        
        $conditions     = array();
        $joins          = array(
            array(
                'table'         => 'booking_tracks',
                'alias'         => 'BookingTrack',
                'type'          => 'INNER',
                'conditions'    => array(
                    'Booking.id = BookingTrack.booking_id'
                )
            )
        );
        
        if(isset($this->request->query['student_booking_detail']) && !empty($this->request->query['student_booking_detail'])) {
            $conditions['BookingTrack.student_id'] = $this->request->query['student_booking_detail'];
            
            $fields         = array('Booking.id','Booking.date','Booking.area_slug','Booking.user_id',
                                'BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.student_id'); 
            $nextBooking    = Hash::extract($this->Booking->find('first',array(
                'fields'        => $fields,
                'joins'         => $joins,
                'conditions'    => array(
                    'student_id'   => $this->request->query['student_booking_detail'],
                    "CONCAT_WS(  ' ', Booking.date, SUBSTRING_INDEX( BookingTrack.time_slot,  '-', 1 ) ) >= now()",
                ),
                'order'         => array('Booking.date')
            )),'Booking.id');
        }
        
        $pageTitle[]    = array(
            'name'      => __('Bookings'),
            'url'       => Router::url(array('controller'=>'adminbookings','action'=>'index')),
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
        $args = array(
            'joins'         => $joins,
            'conditions'    => $conditions,
            'limit'         => $this->perPage,
            'order'         => array('Booking.date' => 'DESC'),
            'group'         => array('Booking.id')
        );
        
        $this->Paginator->settings = $args;
        
        $bookings       = $this->Paginator->paginate('Booking');
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $this->set(array(
            'bookings'      => $bookings,
            'perPage'       => $this->perPage,
            'users'         => $users,
            'nextBooking'   => $nextBooking,
        ));
    }
    
    public function add() {
        $this->breadcrum('add');
        
        $areaTimeSlot   = array();
        $areas          = $this->Area->find('all');
        foreach($areas as $area){
            foreach($area['AreaTimeSlot'] as $timeslots) {
                $areaTimeSlot[$area['Area']['slug']][] = date('H:i',strtotime($timeslots['time_slots']));
            }
        }
        
        $areas  = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
        
        $tacks  = Hash::combine($this->Track->find('all',array(
            'conditions'    => array(
                'Track.status   !=' => 'inactive'  
            )
        )),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');        
        
        $students = $this->User->find('all',array(
            'conditions'    => array('User.role' => 'student'),
            'fields'        => array('id','name','student_number')
        ));

        $students_1         = Hash::combine($students,'{n}.User.id','{n}.User');
        $students         = Hash::combine($students,'{n}.User.id','{n}.User.name');

        $this->set(array(
            'areas'         => $areas,
            'tacks'         => $tacks,
            'students'      => $students,
            'isEdit'        => FALSE,
            'areaTimeSlot'  => $areaTimeSlot
        ));
        
        if($this->request->is('post')) {
            $checkpoint = true;
            // foreach ($this->request->data['BookingTrack'] as $tmp_booking_detail) {
            //     if(isset($tmp_booking_detail['track_id']) && $tmp_booking_detail['track_id'] != '' && isset($tmp_booking_detail['time_slot']) && count($tmp_booking_detail['time_slot']) > 0) {
            //         /*if(empty($tmp_booking_detail['student_id']) && empty($tmp_booking_detail['name']) && empty($tmp_booking_detail['phone'])){
            //             $checkpoint = false;
            //         } else {*/
            //             if(empty($tmp_booking_detail['student_id'])){
            //                 if( !empty($tmp_booking_detail['name']) && empty($tmp_booking_detail['phone']) ) {
            //                     $checkpoint = false;
            //                 } else if( empty($tmp_booking_detail['name']) && !empty($tmp_booking_detail['phone']) ) {
            //                     $checkpoint = false;
            //                 } else if( empty($tmp_booking_detail['name']) && empty($tmp_booking_detail['phone']) ) {
            //                     $checkpoint = false;
            //                 }
            //             }
            //         /*}*/
            //     }
            // }

            if($checkpoint == false){
                $this->set(array(
                    'message'   => __('Begge felter skal udfyldes'),
                    'status'    => 'error',
                    'title'     => __('Booking Details'),
                    'iframe'    => $this->request->query['iframe']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/required_fields');
                return;
            }
            
            $this->processData();
            $countDetails   = $this->Booking->countNumberOfBookings($this->request->data['BookingTrack']);
            $key            = array_search(9,$countDetails);
            
            if($key) {
                $this->set(array(
                    'title'     => __('Student Profile'),
                    'student'   => $students[$key],
                    'id'        => $key
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/confirmation');
                
                return;
            }

            $errorDetails   = $this->Booking->validateData($this->request->data,FALSE);
          
            if($errorDetails['status']) {
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');
                
                return;
            }
            
            if($this->currentUser['User']['role'] != 'external_teacher') {
                $warningsArr = $this->generateWarnings($students);
            }
            
            // if(!empty($warningsArr)) {
            //     $this->set($warningsArr);
                
            //     $this->layout = 'ajax';
            //     $this->render('Ajax/balance_warning');
            //     return;
            // }
            $save_data = $this->request->data;
            if(empty($save_data['Booking']['user_id'])) {
                $save_data['Booking']['user_id']  = $this->currentUser['User']['id'];
                $booking_user_id  = $this->currentUser['User']['id'];
            } else {
                $booking_user_id  = $save_data['Booking']['user_id'];
                $save_data['Booking']['user_id']  = $save_data['Booking']['user_id'];
            }
            $date = $save_data['Booking']['date'];

            foreach ($save_data['BookingTrack'] as $key => $BookingTrack) {

                $save_data['BookingTrack'][$key]['booking_user_id'] = $booking_user_id;
                $is_overlapping_user_booking = false;
                $is_overlapping = false;

                if(!empty($BookingTrack['time_slot'])){
                    $time_slots = $BookingTrack['time_slot'];
                    $time_slot = explode("-", $time_slots);
                    $start_date = date('Y-m-d H:i:s', strtotime($date." ".$time_slot[0]));
                    if($time_slot[0] == "22:00"){
                        $date = date("Y-m-d",strtotime($date."+1 day"));
                    }
                    $end_date = date('Y-m-d H:i:s', strtotime($date." ".$time_slot[1]));
                    $is_overlapping = $this->__checkOverLap( $start_date, $end_date );
                    if(isset($BookingTrack['student_id']) && !empty($BookingTrack['student_id']) ){
                        $is_overlapping_user_booking = $this->__checkOverLapStudent( $BookingTrack,$start_date , $time_slot[0]);
                    }
                }
               
                if($is_overlapping_user_booking != false || $is_overlapping != false){
                     
                    $this->set(array(
                        'message'     => __('Bookingen kan ikke gennemføres, da den kollidere med en anden aftale.')
                    ));
                    
                    $this->layout = 'ajax';
                    $this->render('Ajax/time_warning');
                    
                    return;
                }

            }

            $studentIds         = Hash::extract($this->request->data['BookingTrack'],'{n}.student_id');

            if(!empty($studentIds) ){

                $AdminUsers = new AdminUsersController;

                foreach ($studentIds as $studentId ) {

                    $count = isset($countDetails[$studentId]) ? $countDetails[$studentId] : 0;

                    if($count < 1){
                        $ydelsesData = array();
                        $ydelsesData['KundeID'] = '2795cd76-0a62-4f1c-994b-f5bfbdbf24d1';
                        $ydelsesData['Elevnummer'] = strval($students_1[$studentId]['student_number']);
                        $ydelsesData['Assistentnummer'] = strval($booking_user_id);
                        $CRMDATA = $AdminUsers->submitCRMdata($ydelsesData);
                    }
                }
            }

            $studentsSendSmsArr     = Hash::extract($this->request->data['BookingTrack'],'{n}.send_sms');
            $newStudentArr = $save_data;
            /*
            foreach ($newStudentArr['BookingTrack'] as $key => $bookingTrack) {
                if(!in_array($key, array_keys(array_filter($studentsSendSmsArr)))) {
                    unset($newStudentArr['BookingTrack'][$key]);
                }
            }
            */

            if(isset($newStudentArr['BookingTrack']) && count($newStudentArr['BookingTrack']) > 0){
                $invites    = $this->sendInvites($newStudentArr);
            }
            
            $reference = $this->request->data['Booking']['reference'];
            if(isset($reference) && $reference != ''){
                $this->request->data['Booking']['id'] = $reference;
                unset($this->request->data['Booking']['reference']);
                $book = $this->Booking->updateBooking($this->request->data);
            }else{
                $book = $this->Booking->create();
            }
           
            
            $is_new = true;
            /*$checkpoint = $this->Booking->find('all',array(
                'fields'        => array('id'),
                'conditions'    => array(
                        'user_id'       => $this->request->data['Booking']['user_id'],
                        'date'          => $this->request->data['Booking']['date'],
                        'area_slug'     => $this->request->data['Booking']['area_slug'],
                        'course'        => $this->request->data['Booking']['course']
                    )
                )
            );

            if(isset($checkpoint[0]['Booking']['id']) && !empty($checkpoint[0]['Booking']['id'])){
                //$this->request->data['Booking']['id'] = $checkpoint[0]['Booking']['id'];
                //$is_new = false;
            }*/
           
            // print_r($save_data);
            // die();
            if($this->Booking->saveAssociated($save_data)) {
                $booking_id = $this->Booking->getInsertID();
           
                $bookingTracks = $this->request->data['BookingTrack'];

                foreach ($bookingTracks as $key => $bookingTrack) {
                    if(isset($bookingTrack['time_slot']) && !empty($bookingTrack['time_slot'])){

                        $track_id = $bookingTrack['track_id'];
                        $BookingTrack   = $this->BookingTrack->find('first',array(
                                                        'conditions'    => array(
                                                            'booking_id' => $booking_id,
                                                            'track_id'   => $track_id
                                                        ),
                                                    ));

                        $time_slot = $bookingTrack['time_slot'];
                        $time_slot = explode("-", $time_slot);
                        $date = $this->request->data['Booking']['date'];
                        $start_time = $date." ".$time_slot[0];
                        if($time_slot[1] == '01:30'){
                            $date =  date('Y-m-d', strtotime($date . ' +1 day'));
                        }
                        $end_time = $date." ".$time_slot[1];
                        $booking['booking_type'] = $this->request->data['Booking']['area'].' Område';
                        if(isset($bookingTrack['name']) && !empty($bookingTrack['name'])){
                            $booking['note'] = "Bookinger til ".$bookingTrack['name']." Elev";
                        }else{
                            $User   = $this->User->find('first',array(
                                                        'conditions'    => array(
                                                            'id' => $bookingTrack['student_id'],
                                                        ),
                                                    ));

                            $booking['note'] = "Bookinger til ".$User['User']['firstname']." ".$User['User']['lastname']." Elev";
                        }
                        $booking['note'] = $booking['note']."\n".$this->request->data['Booking']['full_description'];
                        $booking['start_time'] = $start_time;
                        $booking['end_time'] = $end_time;
                       
                        $g_cal_id = $this->__addBookingtoGoogleCalendar( $booking);
                        $this->BookingTrack->id =  $BookingTrack['BookingTrack']['id'];
                        $this->BookingTrack->saveField('g_cal_id', $g_cal_id );
                    }
                }
                // $this->Session->delete('warningDisplayed');
                
                //$this->Booking->reportMail($this->request->data,'add');
                
                foreach($invites['internal'] as $user_id => $invite) {
                    unset($tmp_email_details);
                    $tmp_email_details = array(
                        'email'         => $invite['email'],
                        'data'          => array(
                            'Booking'      => array(
                                'firstname'         => $invite['firstname'],
                                'lastname'          => $invite['lastname'],
                                'table'             => $invite['msg'],
                                'area'              => $invite['area'],
                                'bookingdate'       => $invite['bookingdate'],
                                'bookedby'          => $invite['bookedby'],
                             )
                        ),
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->EmailQueue->addBooking($tmp_email_details);
                    $this->insertLog('timeslot_booked_internal_email', $tmp_email_details, $user_id);

                    unset($tmp_sms_details);
                    $tmp_sms_details = array(
                        'data'          => array(
                            'User'      => array(
                                'firstname' => $invite['firstname'],
                                'lastname'  => $invite['lastname'],
                                'bookedby'  => $invite['bookedby'],
                                'date'      => $invite['bookingdate'],
                                'area'      => $invite['area'],
                                'timeslot'  => (isset($invite['all_timeslot'])) ? $invite['all_timeslot'] : '',
                             )
                        ),
                        'mobileno'      => (substr($invite['phone_no'], 0, 2) == '45') ? '+' . $invite['phone_no'] : '+45'.$invite['phone_no'],
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->__SMSToStudent( $tmp_sms_details, $invite['phone_no'], $invite['other_phone_no'] , $user_id, 'timeslot_booked_internal_sms' );

                
                }
                if(isset($invites['external']) && !empty($invites['external'])) {
                    unset($invite);

                    foreach($invites['external'] as $invite) {
                        unset($tmp_sms_details);
                        $tmp_sms_details = array(
                            'data'          => array(
                                'User'      => array(
                                    'bookedby'  => $invite['bookedby'],
                                    'date'      => $invite['bookingdate'],
                                    'area'      => $invite['area'],
                                    'timeslot'  => date('d-m-Y H:i', $invite['timeslot']),
                                    'track'     => $invite['track'],
                                    'name'      => $invite['name'],
                                 )
                            ),
                            'mobileno'      => (substr($invite['number'], 0, 2) == '45') ? '+' . $invite['number'] : '+45'.$invite['number'],
                            'priority'      => 0,
                        );

                        $this->SmsQueue->externalBookingDetails($tmp_sms_details);
                        $this->insertLog('timeslot_booked_external_sms', $tmp_sms_details);
                    }
                }

                if($is_new){
                    $this->insertLog('track_booked', $this->request->data);
                } else {
                    $this->insertLog('track_updates', $this->request->data);    
                }

                $this->set(array(
                    'message'   => __('The Booking is done.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'date'      => $this->request->data['Booking']['date'],
                    'area'      => $this->request->data['Booking']['area_slug'],
                    'week'      => date('W',strtotime($this->request->data['Booking']['date'])),
                    'iframe'    => $this->request->query['iframe']
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
                $this->set(array(
                    'message'   => __('The Booking is not done. Please try some time later.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'iframe'    => $this->request->query['iframe']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
            }
        }
    }
    
    public function edit($bookingId = NULL) {

        if(is_null($bookingId)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $booking = $this->Booking->findById($bookingId);

        // echo "<pre>";print_r($booking);exit;        

        if(empty($booking)) {
            return $this->redirect(array('action' => 'index'));
        }
        $userId    =  $this->currentUser['User']['id'];
        $do_edit   =  $this->LiveEdit->isEditing($booking['Booking']['date'],$booking['Booking']['area_slug'],$userId);
        $now_time  = strtotime(date("Y-m-d H:i:s"));


        if(count($do_edit)>0){
            $delete =  false;
            $created_time  = strtotime($do_edit['LiveEdit']['created']);
            $created_time =  $created_time + (int)Configure::read('live_edit_time.time');
            $time_older = $created_time - $now_time;
            if($time_older<0){
                $delete =  true;
                $this->LiveEdit->delete( array( 'id' => $do_edit['LiveEdit']['id']) );
            } 
            if(!$delete){
                if($do_edit['LiveEdit']['user_id'] != $userId ){
                    if($this->request->is('ajax') && $this->request->is('post')) {
                        die('d');
                    }else{
                        die('StopEditing');
                    }
                }
            }
        }
        $students = $this->User->find('list',array(
            'conditions'    => array('User.role' => 'student'),
            'fields'        => array('id','name')
        ));
        
        $deadTime   = strtotime($booking['Booking']['created']) + (30 * 60 * 60);
        
        $this->breadcrum('edit',$booking);
        
        $tracks  = Hash::combine($this->Track->find('all',array(
            'conditions'    => array(
                'Track.status   !=' => 'inactive',
                'Track.area_id'     => $booking['Booking']['area_slug']
            )
        )),'{n}.Track.id','{n}.Track.name');
        
        $bookedTracks   = $booking['BookingTrack'];
        $area           = $this->Area->findBySlug($booking['Booking']['area_slug']);
        $args['area']   = $booking['Booking']['area_slug'];
        $args['date']   = $booking['Booking']['date'];
        
        $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay($args);
        foreach($tracks as $id => $track) {
            foreach($generatedTimeSlots['display'][$args['area']] as $timeSlot) {
                $areaTimeSlots[$id][$timeSlot] = $timeSlot;
            }
        }
        
        $releasedTracks = $this->BookingTrack->find('all',array(
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'alias'         => 'Booking',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'BookingTrack.booking_id = Booking.id'
                    )
                )
            ),
            'conditions'    => array(
                'Booking.date'                  => $booking['Booking']['date'],
                'Booking.area_slug'             => $booking['Booking']['area_slug'],
                'BookingTrack.release_track ='  => 1,
            )
        ));
        
        $releasedTracks = Hash::combine($releasedTracks,'{n}.BookingTrack.time_slot','{n}.BookingTrack','{n}.BookingTrack.track_id');
        
        if(!empty($releasedTracks)) {
            foreach($releasedTracks as $trackId => $timeSlotTrack) {
                foreach($timeSlotTrack as $timeSlot => $track) {
                    unset($areaTimeSlots[$trackId][$timeSlot]);
                }
            }
        }
        
        $modifiedbookedTracks = array();
        foreach($bookedTracks as $bookedTrack) {
            if($bookedTrack['release_track'] != 1) {
                $modifiedbookedTracks[$bookedTrack['track_id']]['time_slot'][]  = $bookedTrack['time_slot'];
                $modifiedbookedTracks[$bookedTrack['track_id']]['student_id']   = $bookedTrack['student_id'];
                $modifiedbookedTracks[$bookedTrack['track_id']]['course']       = $bookedTrack['course'];
                $modifiedbookedTracks[$bookedTrack['track_id']]['booking_user_id']       = $bookedTrack['booking_user_id'];
                if(($bookedTrack['student_id'] == '-1') || (is_null($bookedTrack['student_id']))) {
                     $modifiedbookedTracks[$bookedTrack['track_id']]['name']    = $bookedTrack['name'];
                     $modifiedbookedTracks[$bookedTrack['track_id']]['phone']   = $bookedTrack['phone'];
                }
                $modifiedbookedTracks[$bookedTrack['track_id']]['created']       = $bookedTrack['created'];
            }
        }
        
        if(!empty($modifiedbookedTracks)) {
            foreach($tracks as $id => $track) {
                if(isset($modifiedbookedTracks[$id]['time_slot'])) {
                    foreach($generatedTimeSlots['mapping'][$args['area']] as $mapped => $timeSlot) {
                        foreach($timeSlot as $slot) {
                            if(in_array($slot,$modifiedbookedTracks[$id]['time_slot'])) {
                                $modifiedbookedTracks[$id]['time_slot'][$mapped] = $mapped;
                                $key = array_search($slot,$modifiedbookedTracks[$id]['time_slot']);
                                unset($modifiedbookedTracks[$id]['time_slot'][$key]);
                            }
                        }
                    }
                }
            }
        }
        
        $users          = $this->User->find('all');
        
        $selected       = array();
        $modifiedUsers  = array();
        
        foreach ($users as $user) {
            $modifiedUsers[$user['User']['id']]['name']   = $user['User']['firstname'].' '.$user['User']['lastname'];
            $modifiedUsers[$user['User']['id']]['role']   = $user['User']['role'];
        }
        
        $courses    = $this->Course->find('list',array(
            'conditions'    => array(
                'area'  => $args['area'],
            )
        ));

        // echo "<pre>";
        // print_r($modifiedbookedTracks);
        // echo "</pre>";


        $this->set(array(
            'tracks'                => $tracks,
            'modifiedUsers'         => $modifiedUsers,
            'modifiedbookedTracks'  => $modifiedbookedTracks,
            'isEdit'                => TRUE,
            'areaTimeSlots'         => $areaTimeSlots,
            'booking'               => $booking,
            'area'                  => $area,
            'time'                  => $selected,
            'isExternal'            => FALSE,
            'isTracksEdit'          => FALSE,
            'courses'               => $courses,
            'bookedTimeSlots'       => $generatedTimeSlots['students']
        ));

        if($this->request->is('ajax') && $this->request->is('post')) {



            $checkpoint = true;
            foreach ($this->request->data['BookingTrack'] as $tmp_booking_detail) {
                if(isset($tmp_booking_detail['track_id']) && $tmp_booking_detail['track_id'] != '' && isset($tmp_booking_detail['time_slot']) && count($tmp_booking_detail['time_slot']) > 0) {
                    /*if(empty($tmp_booking_detail['student_id']) && empty($tmp_booking_detail['name']) && empty($tmp_booking_detail['phone'])){
                        $checkpoint = false;
                    } else {*/
                        if(empty($tmp_booking_detail['student_id'])){
                            if( !empty($tmp_booking_detail['name']) && empty($tmp_booking_detail['phone']) ) {
                                $checkpoint = false;
                            } else if( empty($tmp_booking_detail['name']) && !empty($tmp_booking_detail['phone']) ) {
                                $checkpoint = false;
                            }
                        }
                    /*}*/
                }
            }

            if($checkpoint == false){
                $this->set(array(
                    'message'   => __('Begge felter skal udfyldes'),
                    'status'    => 'error',
                    'title'     => __('Booking Details'),
                    'iframe'    => $this->request->query['iframe']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/required_fields');
                return;
            }


            
            $this->request->data['Booking']['id'] = $bookingId;
            $this->processData('update');

            
            $errorDetails   = $this->Booking->validateData($this->request->data,TRUE);

            // echo "<pre>";
            // print_r($errorDetails);
            // exit;
            
            if($errorDetails['status']) {
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');
                
                return;
            }            
            
            $studentArrOld          = Hash::extract($bookedTracks,'{n}.student_id');
            $studentsArr            = Hash::extract($this->request->data['BookingTrack'],'{n}.student_id');

            //matching old and new student details for sms sending
            $old_student_data = Hash::combine($bookedTracks, '{n}.track_id', '{n}');
            $new_student_data = Hash::combine($this->request->data['BookingTrack'],'{n}.track_id', '{n}');
            $allow_sms = array();

            /*foreach ($new_student_data as $track_id => $new_student) {
                if(!empty($new_student['name']) && !empty($new_student['phone'])){
                    if(isset($old_student_data[$track_id])){
                        if(empty($old_student_data[$track_id]['name']) && empty($old_student_data[$track_id]['number'])){
                            $allow_sms[$track_id] = $track_id;
                        }
                    } else {
                        $allow_sms[$track_id] = $track_id;
                    }
                }
            }*/

            foreach ($new_student_data as $track_id => $new_student) 
            {
                if(!empty($new_student['student_id']))
                {
                    if(isset($old_student_data[$track_id]))
                    {
                    } 
                    else 
                    {
                        $tmpStudent = $this->User->findById($new_student['student_id']);
                        if(!empty($tmpStudent))
                        {
                            $new_student_data[$track_id]['name'] = $tmpStudent['User']['firstname'].' '.$tmpStudent['User']['lastname'];
                            $new_student_data[$track_id]['phone'] = $tmpStudent['User']['phone_no'];
                        }
                        $allow_sms[$track_id] = $track_id;
                    }
                }
            }

            $studentsSendSmsArr     = Hash::extract($this->request->data['BookingTrack'],'{n}.send_sms');
            $array_diff             = array_diff(array_keys($studentsArr), array_keys($studentArrOld));
            if(!empty($array_diff) || count($studentArrOld) != count($studentsArr)) {
                $warningsArr = $this->generateWarnings($students);
                
                if(!empty($warningsArr)) {
                    $this->set($warningsArr);
                    
                    // $this->layout = 'ajax';
                    // $this->render('Ajax/balance_warning');
                    // return;
                }
            }
            
            $this->request->data['Booking']['user_id'] = $booking['Booking']['user_id'];

            $allInvites             = array();
            $addInvites             = array();
            $invites                = array();
            $newStudentModifiedArr  = $this->request->data;
            $save_data = $this->request->data;
            if(empty($save_data['Booking']['user_id'])) {
                $save_data['Booking']['user_id']  = $this->currentUser['User']['id'];
                $booking_user_id  = $this->currentUser['User']['id'];
            } else {
                $booking_user_id  = $save_data['Booking']['user_id'];
                $save_data['Booking']['user_id']  = $save_data['Booking']['user_id'];
            }
            foreach ($save_data['BookingTrack'] as $key => $BookingTrack) {
                $save_data['BookingTrack'][$key]['booking_user_id'] = $booking_user_id;
            }
            unset($newStudentModifiedArr['BookingTrack']);
            if(!empty($allow_sms)) {
                $newStudentArr = $this->request->data;
                foreach ($newStudentArr['BookingTrack'] as $key => $bookingTrack) {
                    if(!in_array($bookingTrack['track_id'], $allow_sms)) {
                        unset($newStudentArr['BookingTrack'][$key]);
                    }
                }
                $addInvites = $this->sendInvites($newStudentArr,'edit');
                array_push($allInvites, $addInvites);
            }            

            $this->Booking->updateBooking($this->request->data);


            if($this->Booking->saveAssociated($this->request->data)) {
                $booking_id = $this->Booking->id;
                $bookingTracks = $this->request->data['BookingTrack'];

                foreach ($bookingTracks as $key => $bookingTrack) {
                    if(isset($bookingTrack['time_slot']) && !empty($bookingTrack['time_slot'])){

                        $track_id = $bookingTrack['track_id'];
                        $BookingTrack   = $this->BookingTrack->find('first',array(
                                                        'conditions'    => array(
                                                            'booking_id' => $booking_id,
                                                            'track_id'   => $track_id
                                                        ),
                                                    ));

                        $time_slot = $bookingTrack['time_slot'];
                        $time_slot = explode("-", $time_slot);
                        $date = $this->request->data['Booking']['date'];
                        $start_time = $date." ".$time_slot[0];
                        if($time_slot[1] == '01:30'){
                            $date =  date('Y-m-d', strtotime($date . ' +1 day'));
                        }
                        $end_time = $date." ".$time_slot[1];
                        $booking['booking_type'] = $this->request->data['Booking']['area'].' Område';
                        if(isset($bookingTrack['name']) && !empty($bookingTrack['name'])){
                            $booking['note'] = "Bookinger til ".$bookingTrack['name']." Elev";
                        }else{
                            $User   = $this->User->find('first',array(
                                                        'conditions'    => array(
                                                            'id' => $bookingTrack['student_id'],
                                                        ),
                                                    ));

                            $booking['note'] = "Bookinger til ".$User['User']['firstname']." ".$User['User']['lastname']." Elev";
                        }
                        $booking['note'] = $booking['note']."\n".$this->request->data['Booking']['full_description'];
                        $booking['start_time'] = $start_time;
                        $booking['end_time'] = $end_time;
                       
                        $g_cal_id = $this->__addBookingtoGoogleCalendar( $booking);
                        $this->BookingTrack->id =  $BookingTrack['BookingTrack']['id'];
                        $this->BookingTrack->saveField('g_cal_id', $g_cal_id );
                    }
                }

                if(!empty($allInvites)) {
                    foreach($allInvites as $invites) {
                        foreach($invites['internal'] as $invite) {
                            $this->EmailQueue->addBooking(array(
                                'email'         => $invite['email'],
                                'data'          => array(
                                    'Booking'      => array(
                                        'firstname'         => $invite['firstname'],
                                        'lastname'          => $invite['lastname'],
                                        'table'             => $invite['msg'],
                                        'area'              => $invite['area'],
                                        'bookingdate'       => $invite['bookingdate'],
                                        'bookedby'          => $invite['bookedby'],
                                     )
                                ),
                                'template'      => $invite['message_type'],
                                'priority'      => 0,
                            ));

                            $tmp_sms_details = array(
                                'data'          => array(
                                    'User'      => array(
                                        'firstname' => $invite['firstname'],
                                        'lastname'  => $invite['lastname'],
                                        'bookedby'  => $invite['bookedby'],
                                        'date'      => $invite['bookingdate'],
                                        'area'      => $invite['area']
                                     )
                                ),
                                'mobileno'      => (substr($invite['phone_no'], 0, 2) == '45') ? '+' . $invite['phone_no'] : '+45'.$invite['phone_no'],
                                'template'      => $invite['message_type'],
                                'priority'      => 0,
                            );

                            $this->__SMSToStudent( $tmp_sms_details, $invite['phone_no'], $invite['other_phone_no'] );

                        }

                        foreach($invites['external'] as $invite) {

                            $this->SmsQueue->externalBookingDetails(array(
                                'data'          => array(
                                    'User'      => array(
                                        'bookedby'  => $invite['bookedby'],
                                        'date'      => $invite['bookingdate'],
                                        'area'      => $invite['area'],
                                        'timeslot'  => date('d-m-Y H:i', $invite['timeslot']),
                                        'track'     => $invite['track'],
                                        'name'      => $invite['name'],
                                     )
                                ),
                                'mobileno'      => (substr($invite['number'], 0, 2) == '45') ? '+' . $invite['number'] : '+45'.$invite['number'],
                                'priority'      => 0,
                            ));
                        }
                    }
                }
                $this->set(array(
                    'message'   => __('The Booking has been updated.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'date'      => $this->request->data['Booking']['date'],
                    'area'      => $this->request->data['Booking']['area_slug'],
                    'week'      => date('W',strtotime($this->request->data['Booking']['date'])),
                    'iframe'    => $this->request->query['iframe']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
                
                return;
            } 
            else 
            {
                
                
                $this->set
                (
                    array
                    (
                        'message'   => __('The Booking could not be updated. Please try some time later.'),
                        'status'    => 'success',
                        'title'     => __('Booking')
                    )
                );
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
                return;
            }
        }
        
        if($this->request->is('ajax')) 
        {
            $this->layout = 'ajax';
            $this->render('add');
            return;
        }
    }
    
    private function processData($type  = 'insert') {
        $this->request->data['Booking']['date'] = date('Y-m-d',strtotime(str_replace('.','-', $this->request->data['Booking']['date'])));
        $bt_cnt = 0;
        foreach($this->request->data['BookingTrack'] as $key => $bookingTrack) {
            if((isset($bookingTrack['time_slot']) && !empty($bookingTrack['time_slot'])) && (isset($bookingTrack['track_id']) && !empty($bookingTrack['track_id']))) {
                
                $args['area']           = $this->request->data['Booking']['area'];
                $args['date']           = $this->request->data['Booking']['date'];
                
                $timeSlotsForBooking    = $this->Booking->getBookingTimeSlotsForTheDay($args);
                for($i = 0;$i< count($bookingTrack['time_slot']);$i++) {

                    $bookingTrack['time_slot'][$i] = (isset($timeSlotsForBooking['mapping'][$args['area']][$bookingTrack['time_slot'][$i]])) ?
                        $timeSlotsForBooking['mapping'][$args['area']][$bookingTrack['time_slot'][$i]] : array(0 => $bookingTrack['time_slot'][$i]);
                    
                    if(is_array($bookingTrack['time_slot'][$i])) {
                        for($j = 0;$j< count($bookingTrack['time_slot'][$i]);$j++) {
                            if(isset($bookingTrack['booking_id'])) {
                                $this->request->data['BookingTrack'][$bt_cnt]  = array(
                                    'track_id'          => $bookingTrack['track_id'],
                                    'time_slot'         => $bookingTrack['time_slot'][$i][$j],
                                    'student_id'        => (empty($bookingTrack['student_id']))?NULL:$bookingTrack['student_id'],
                                    'phone'             => (isset($bookingTrack['phone']))?$bookingTrack['phone']:NULL,
                                    'name'              => (isset($bookingTrack['name']))?$bookingTrack['name']:NULL,
                                    'booking_id'        => $bookingTrack['booking_id'],
                                    'unknown'           => (isset($bookingTrack['unknown']))?$bookingTrack['unknown']:NULL,
                                    'send_sms'          => (isset($bookingTrack['send_sms']))?$bookingTrack['send_sms']:NULL,
                                    'course'            => (isset($bookingTrack['course']))?$bookingTrack['course']:NULL
                                );
                                if(isset($bookingTrack['booking_user_id'])){
                                    $this->request->data['BookingTrack'][$bt_cnt]['booking_user_id'] = $bookingTrack['booking_user_id'];
                                }
                                $bt_cnt++;
                            } else {
                                $this->request->data['BookingTrack'][$bt_cnt]  = array(
                                    'track_id'      => $bookingTrack['track_id'],
                                    'time_slot'     => $bookingTrack['time_slot'][$i][$j],
                                    'student_id'    => (empty($bookingTrack['student_id']))?NULL:$bookingTrack['student_id'],
                                    'phone'         => (isset($bookingTrack['phone']))?$bookingTrack['phone']:NULL,
                                    'name'          => (isset($bookingTrack['name']))?$bookingTrack['name']:NULL,
                                    'unknown'       => (isset($bookingTrack['unknown']))?$bookingTrack['unknown']:NULL,
                                    'send_sms'      => (isset($bookingTrack['send_sms']))?$bookingTrack['send_sms']:NULL,
                                    'course'        => (isset($bookingTrack['course']))?$bookingTrack['course']:NULL
                                );
                                if(isset($bookingTrack['booking_user_id'])){
                                    $this->request->data['BookingTrack'][$bt_cnt]['booking_user_id'] = $bookingTrack['booking_user_id'];
                                }
                                $bt_cnt++;
                            }
                        }
                    } else {
                        $this->request->data['BookingTrack'][$bt_cnt]  = array(
                            'track_id'      => $bookingTrack['track_id'],
                            'time_slot'     => $bookingTrack['time_slot'][$i],
                            'student_id'    => (empty($bookingTrack['student_id']))?NULL:$bookingTrack['student_id'],
                            'phone'         => (isset($bookingTrack['phone']))?$bookingTrack['phone']:NULL,
                            'name'          => (isset($bookingTrack['name']))?$bookingTrack['name']:NULL,
                            'unknown'       => (isset($bookingTrack['unknown']))?$bookingTrack['unknown']:NULL,
                            'send_sms'      => (isset($bookingTrack['send_sms']))?$bookingTrack['send_sms']:NULL,
                            'course'        => (isset($bookingTrack['course']))?$bookingTrack['course']:NULL
                        );
                        if(isset($bookingTrack['booking_user_id'])){
                                    $this->request->data['BookingTrack'][$bt_cnt]['booking_user_id'] = $bookingTrack['booking_user_id'];
                                }
                        $bt_cnt++;
                    }
                }
            }
            unset($this->request->data['BookingTrack'][$key]);
        }

        $this->request->data['BookingTrack']    = array_values($this->request->data['BookingTrack']);
        
        if(isset($this->request->data['Booking']['isDead']) && $this->request->data['Booking']['isDead'] == TRUE) {
            unset($this->request->data['BookingTrack']);
        }
        
        if(isset($this->request->data['Booking']['full_description']) && ($this->request->data['Booking']['full_description'] == 'Enter Long Description' || $this->request->data['Booking']['full_description'] == 'Indtast lang beskrivelse')) {
            $this->request->data['Booking']['full_description'] = '';
        }
    }
    
    private function generateWarnings($students = array()) {
        $studentsArr    = Hash::extract($this->request->data['BookingTrack'],'{n}.student_id');
        $lessonPrice    = $this->Price->find('first',array(
            'fields'     => array('price'),
            'conditions' => array(
                'type'  => 'area',
                'area'  => $this->request->data['Booking']['area'],
            ),
            'order' => array(
                'Price.from_date' => 'DESC'
            )
        ));

        $amt = (isset($lessonPrice['Price']['price']))?$lessonPrice['Price']['price']:0;
        $i = 0;
        $arr = array();
        foreach ($studentsArr as $studentId) {
            if(!is_null($studentId) && $studentId != -1) {
                $studentBalance = $this->User->getBalance($studentId,$amt);
                
                if(!$this->Session->check('warningDisplayed.student_'.$studentId) || $this->Session->read('warningDisplayed.student_'.$studentId) == FALSE) {
                    if($studentBalance['originalBalance'] <= -1800) {
                        $this->Session->write('warningDisplayed.student_'.$studentId,TRUE);
                        
                        $arr = array(
                            'title'         => __('Minimum Balance Warning'),
                            'message'       => __('Sorry! Booking could not be done, due to insufficient balance.').' '.$students[$studentId].__('\'s balance is').' '.$studentBalance['originalBalance'].'kr',
                            'cancelBooking' => TRUE,
                            'studentId'     => $studentId
                        );
                    } else if($studentBalance['originalBalance'] > -1800 && $studentBalance['originalBalance'] < 0) {
                        $this->Session->write('warningDisplayed.student_'.$studentId,TRUE);
                        $arr = array(
                            'title'         => __('Minimum Balance Warning'),
                            'message'       => $students[$studentId].__('\'s balance is').' '.$studentBalance['originalBalance'].'kr '.__('and after this booking would be').' '.$studentBalance['computedBalance'].'kr '.__('. If you have seen the payment of proof click on "Seen proof of payment/receipt of cash" button else cancel this booking'),
                            'button1'       => __('Seen proof of payment/receipt of cash'),
                            'button2'       => __('Cancel Booking'),
                            'studentId'     => $studentId
                        );
                    } else if($studentBalance['originalBalance'] >= 0 && $studentBalance['computedBalance'] < 0) {
                        $this->Session->write('warningDisplayed.student_'.$studentId,TRUE);
                        $arr = array(
                            'title'     => __('Minimum Balance Warning'),
                            'message'   => $students[$studentId].__('\'s balance would be ').$studentBalance['computedBalance'].'kr '.__('after this booking.'),
                        );
                    }
                }
            }
            $i++;
        }
        return $arr;
    }
    
    public function view($id) {
        
        if(empty($id)) {
            return $this->redirect(array('action'   => 'index'));
        }
        $bookingTracks  = array();
        $bookingDetails = $this->Booking->findById($id);
        
        if(isset($this->request->query['student_booking_detail']) && !empty($this->request->query['student_booking_detail'])) {
            $bookingTracks = $this->BookingTrack->find('all',array(
                'joins'     => array(
                    array(
                        'table'         => 'bookings',
                        'alias'         => 'Booking',
                        'type'          => 'inner',
                        'conditions'    => array(
                            'Booking.id = BookingTrack.booking_id'
                        )
                    )
                ),
                'conditions' => array(
                    'BookingTrack.student_id'   => $this->request->query['student_booking_detail'],
                    'Booking.id'                => $id
                )
            ));
        }
        
        if(empty($bookingDetails)) {
            return $this->redirect(array('action'   => 'index'));
        }
        
        $this->breadcrum('view',$bookingDetails);
        
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        $lanes  = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track');
        
        $this->set(array(
            'bookingDetails'    => $bookingDetails,
            'bookingTracks'     => $bookingTracks,
            'users'             => $users,
            'lanes'             => $lanes,
        ));
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('action' => 'calendar'));
        }
        $bookings   = $this->Booking->findById($id);
        if(empty($bookings)) {
            return $this->redirect(array('action' => 'calendar'));
        }
        $invites    = $this->sendInvites($bookings,'delete');

        foreach ($bookings['BookingTrack'] as $key => $bookingTrack) {
            $booking_user_id = (!empty( $bookingTrack['booking_user_id'] )) ?  $bookingTrack['booking_user_id']  : $data['Booking']['user_id'];
            $User =  $this->User->findById( $booking_user_id );
            
            if( $bookingTrack['g_cal_id'] != '' && $User['User']['google_token'] !=  '' ) {
                $client = new Google_Client();
                $dir =  __DIR__."/../Config";
                $client->setAuthConfigFile($dir.'/client_secret.json');

                $token = $client->refreshToken($User['User']['google_token']);
                $client->setAccessToken($token);
                $service  = new Google_Service_Calendar($client);
                $a = $service->events->delete('primary', $bookingTrack['g_cal_id']);
            }
        }

        if($this->Booking->delete($id,$cascade = TRUE )) {
            //            $this->Booking->reportMail($bookings,'delete');
            if(isset($invites['internal'])) {
                foreach($invites['internal'] as $user_id => $invite) {
                    unset($tmp_email_details);
                    $tmp_email_details = array(
                        'email'         => $invite['email'],
                        'data'          => array(
                            'Booking'      => array(
                                'firstname'         => $invite['firstname'],
                                'lastname'          => $invite['lastname'],
                                'table'             => $invite['msg'],
                                'area'              => $invite['area'],
                                'bookingdate'       => $invite['bookingdate'],
                                'bookedby'          => $invite['bookedby'],
                            )
                        ),
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->EmailQueue->addBooking($tmp_email_details);
                    $this->insertLog('track_deleted_internal_email', $tmp_email_details, $user_id);

                    unset($tmp_sms_details);
                    $tmp_sms_details = array(
                        'data'          => array(
                            'User'      => array(
                                'firstname' => $invite['firstname'],
                                'lastname'  => $invite['lastname'],
                                'bookedby'  => $invite['bookedby'],
                                'date'      => $invite['bookingdate'],
                                'area'      => $invite['area'],
                                'timeslot'  => (isset($invite['all_timeslot'])) ? $invite['all_timeslot'] : $invite['timeslot'],
                             )
                        ),
                        'mobileno'      => (substr($invite['phone_no'], 0, 2) == '45') ? '+' . $invite['phone_no'] : '+45'.$invite['phone_no'],
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                      
                    $this->__SMSToStudent( $tmp_sms_details, $invite['phone_no'], $invite['other_phone_no'], $user_id , 'track_deleted_internal_sms');

                }
            }

            if(isset($invites['external'])) {
                foreach($invites['external'] as $invite) {
                    unset($tmp_sms_details);
                    $tmp_sms_details = array(
                        'data'          => array(
                            'User'      => array(
                                'firstname' => $invite['name'],
                                'bookedby'  => $invite['bookedby'],
                                'date'      => $invite['bookingdate'],
                                'area'      => $invite['area'],
                                'timeslot'  => (isset($invite['all_timeslot'])) ? $invite['all_timeslot'] : $invite['timeslot'],
                                'track'     => $invite['track']
                             )
                        ),
                        'mobileno'      => (substr($invite['number'], 0, 2) == '45') ? '+' . $invite['number'] : '+45'.$invite['number'],
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->SmsQueue->externalBookingDetails($tmp_sms_details);
                    $this->insertLog('track_deleted_external_sms', $tmp_sms_details);
                }
            }
            $this->Session->setFlash(__('The Booking has been deleted'),'alert/success');
            return $this->redirect(array(
                'controller'    => 'adminbookings',
                'action'        => 'calendar',
                '?'             => array(
                    'area'      => $this->request->query['area'],
                    'date'      => $this->request->query['date'],
                    'week'      => date('W',strtotime($this->request->query['date'])),
                    'iframe'    => $this->request->query['iframe']
                )
            ));
        }
    }
    
    public function deleteTrack($bookingId = NULL,$trackId = NULL) {
        $result = array();
        if(is_null($bookingId) || is_null($trackId)) {
            return $this->redirect(array('action' => 'calendar'));
        }

        $bookings   = $this->BookingTrack->find('all',array(
            'fields'        => array('Booking.id','Booking.area_slug','Booking.course','Booking.date','Booking.user_id','BookingTrack.id','BookingTrack.g_cal_id','BookingTrack.booking_user_id',
                'BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.student_id','BookingTrack.name','BookingTrack.phone'),
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'type'          => 'INNER',
                    'alias'         => 'Booking',
                    'conditions'    => array(
                        'BookingTrack.booking_id = Booking.id'
                    )
                )
            ),
            'conditions'    => array(
                'booking_id'        => $bookingId,
                'track_id'          => $trackId,
            )
        ));

        if(empty($bookings)) {
           $result['message']   = __('Track could not be deleted');
           $result['status']    = 'error';
        }
        
        $args['Booking']        = $bookings[0]['Booking'];
        $args['BookingTrack']   = array_values(Hash::combine($bookings,'{n}.BookingTrack.id','{n}.BookingTrack'));
        foreach ($bookings as $key => $bookingTrack) {
            $bookingTrack = $bookingTrack['BookingTrack'];
            $booking_user_id = (!empty( $bookingTrack['booking_user_id'] )) ?  $bookingTrack['booking_user_id']  : $data['Booking']['user_id'];
            $User =  $this->User->findById( $booking_user_id );
            
            if( $bookingTrack['g_cal_id'] != '' && $bookingTrack['g_cal_id'] != '1' && $User['User']['google_token'] !=  '' ) 
            {
                try
                {
                    $client = new Google_Client();
                    $dir =  __DIR__."/../Config";
                    $client->setAuthConfigFile($dir.'/client_secret.json');
                    $token = $client->refreshToken($User['User']['google_token']);
                    $client->setAccessToken($token);
                    $service  = new Google_Service_Calendar($client);
                    $a = $service->events->delete('primary', $bookingTrack['g_cal_id']);
                }
                catch(\Exception $e)
                {
                    // hanlde error
                }
            }
        }

        $invites                = $this->sendStudentDeleteInvites($args);

        $show_dialog = 'false';
        if($this->BookingTrack->deleteAll(array(
            'booking_id'    => $args['Booking']['id'],
            'track_id'      => $args['BookingTrack'][0]['track_id']
            ),$cascade = TRUE )) {

            //$this->Booking->reportMail($args,'deleteTrack');
            if(isset($invites['internal'])) {
                foreach($invites['internal'] as $user_id => $invite) {
                    unset($tmp_email_details);
                    $tmp_email_details = array(
                        'email'         => $invite['email'],
                        'data'          => array(
                            'Booking'      => array(
                                'firstname'         => $invite['firstname'],
                                'lastname'          => $invite['lastname'],
                                'table'             => $invite['msg'],
                                'area'              => $invite['area'],
                                'bookingdate'       => $invite['bookingdate'],
                                'bookedby'          => $invite['bookedby'],
                            )
                        ),
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->EmailQueue->addBooking($tmp_email_details);
                    $this->insertLog('timeslot_deleted_internal_email', $tmp_email_details, $user_id);

                    unset($tmp_sms_details);
                    $tmp_sms_details = array(
                        'data'          => array(
                            'User'      => array(
                                'firstname' => $invite['firstname'],
                                'lastname'  => $invite['lastname'],
                                'bookedby'  => $invite['bookedby'],
                                'date'      => $invite['bookingdate'],
                                'area'      => $invite['area'],
                                'timeslot'  => (isset($invite['all_timeslot'])) ? $invite['all_timeslot'] : $invite['timeslot'],
                             )
                        ),
                        'mobileno'      => (substr($invite['phone_no'], 0, 2) == '45') ? '+' . $invite['phone_no'] : '+45'.$invite['phone_no'],
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->__SMSToStudent( $tmp_sms_details, $invite['phone_no'], $invite['other_phone_no'], $user_id , 'timeslot_deleted_internal_sms');

                }
            }
            
            if(isset($invites['external'])) {
                foreach($invites['external'] as $invite) {
                    unset($tmp_sms_details);
                    $tmp_sms_details = array(
                        'data'          => array(
                            'User'      => array(
                                'firstname' => $invite['name'],
                                'bookedby'  => $invite['bookedby'],
                                'date'      => $invite['bookingdate'],
                                'area'      => $invite['area'],
                                'timeslot'  => (isset($invite['all_timeslot'])) ? $invite['all_timeslot'] : $invite['timeslot'],
                                'track'     => $invite['track']
                             )
                        ),
                        'mobileno'      => (substr($invite['number'], 0, 2) == '45') ? '+' . $invite['number'] : '+45'.$invite['number'],
                        'template'      => $invite['message_type'],
                        'priority'      => 0,
                    );

                    $this->SmsQueue->externalBookingDetails($tmp_sms_details);
                    $this->insertLog('timeslot_deleted_external_sms', $tmp_sms_details);
                }
            }

            $this->insertLog('timeslot_deleted', $args);

            $left_booking = $this->BookingTrack->find('count',array(
                'joins'         => array(
                    array(
                        'table'         => 'bookings',
                        'type'          => 'INNER',
                        'alias'         => 'Booking',
                        'conditions'    => array(
                            'BookingTrack.booking_id = Booking.id'
                        )
                    )
                ),
                'conditions'    => array(
                    'booking_id'        => $bookingId
                )
            ));

            if($left_booking == 0){
                $show_dialog = 'true';

                $this->Booking->delete(array('id'    => $bookingId));

                $this->insertLog('track_deleted', $args);
            }

            $result['message']          = __('Track deleted!');
            $result['status']           = 'success';
            $result['show_dialog']      = $show_dialog;
        }

        $this->set(array(
            'bookingData' => $result,
        ));
        
        $this->layout = 'ajax';
        $this->render('Ajax/json');
    }
    
    private function sendStudentDeleteInvites($args) {
        $bookingArr = array();
        $external   = array();
        $userDetails    = $this->User->find('all');
        $userDetails    = Hash::combine($userDetails,'{n}.User.id','{n}.User');
        $courses        = $this->Course->find('list');
        $tracks         = Hash::combine($this->Track->findAllByAreaId(
            $args['Booking']['area_slug']),'{n}.Track.id','{n}.Track.name'
        );
        
        foreach($args['BookingTrack'] as $bookingTrack) {
            if(($bookingTrack['student_id'] != -1) && !is_null($bookingTrack['student_id'])) {
                $bookingArr[$bookingTrack['student_id']]['type'] = 'student';
                $bookingArr[$bookingTrack['student_id']]['bookingDetails'][] = array(
                    'track_id'  => $bookingTrack['track_id'],
                    'time_slot' => $bookingTrack['time_slot'],
                    'course'    => (isset($args['Booking']['course']) && isset($courses[$args['Booking']['course']]))?$courses[$args['Booking']['course']]:'',
                );
            } else if($bookingTrack['student_id'] == -1 || empty($bookingTrack['student_id'])) {
                if(!empty($bookingTrack['phone']) && (!is_null($bookingTrack['phone']))) {
                    $external[$bookingTrack['phone']]['type']   = 'student';
                    $external[$bookingTrack['phone']]['bookingDetails'][] = array(
                        'name'      => $bookingTrack['name'],
                        'track_id'  => $bookingTrack['track_id'],
                        'time_slot' => $bookingTrack['time_slot'],
                        'course'    => (isset($args['Booking']['course']) && isset($courses[$args['Booking']['course']]))?$courses[$args['Booking']['course']]:'',
                    );
                }
            }
            if(isset($bookingTrack['student_id']) && !empty($bookingTrack['student_id']) && isset($userDetails[$bookingTrack['student_id']])) {
                $student    = $userDetails[$bookingTrack['student_id']]['firstname'].' '.$userDetails[$bookingTrack['student_id']]['lastname'];
            } else if(isset($bookingTrack['unknown']) && ($bookingTrack['unknown'])) {
                $student    = $bookingTrack['name'].' ('.__('External User').')';
            } else {
                $student    =  __('External User');
            }
        }
        $table = array();
        $i = 1;
        $style1 = "background-color: rgb(238, 238, 238); border: 1px solid rgb(187, 187, 187); color: rgb(51, 51, 51); font-family: sans-serif; padding: 10px; vertical-align: top;width:200px;";
        $style2 = "background-color: rgb(255, 255, 255); border: 1px solid rgb(187, 187, 187); color: rgb(51, 51, 51); font-family: sans-serif; padding: 10px; vertical-align: top;width:200px;";
        foreach($bookingArr as $userId => $userBooking) {
            $table['internal'][$userId]['email']        = (empty($userDetails[$userId]['email_id']))?'jesper@schlebaum.dk':$userDetails[$userId]['email_id'];
            $table['internal'][$userId]['firstname']    = $userDetails[$userId]['firstname'];
            $table['internal'][$userId]['lastname']     = $userDetails[$userId]['lastname'];
            $table['internal'][$userId]['area']         = $this->areaListArr[$args['Booking']['area_slug']];
            $table['internal'][$userId]['bookingdate']  = date('d.m.Y',  strtotime($args['Booking']['date']));
            
            $table['internal'][$userId]['phone_no']     = $userDetails[$userId]['phone_no'];//4560607550;
            $table['internal'][$userId]['other_phone_no']     = $userDetails[$userId]['other_phone_no'];//4560607550;
            $table['internal'][$userId]['all_timeslot']     = implode(',',Hash::extract($args['BookingTrack'], '{n}.time_slot'));
            $table['internal'][$userId]['message_type'] = 'studentDeleteTemplate';
            $table['internal'][$userId]['bookedby']     = $userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname'];
            
            $table['internal'][$userId]['msg']          = '<table border="0" align="center"><thead>';
            $table['internal'][$userId]['msg']         .= '<tr>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Track Name').'</th>';
            if($userBooking['type'] != 'student') {
                $table['internal'][$userId]['msg']     .= '<th style="'.$style1.'">'.__('Student Name').'</th>';
            }
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Booking Type').'</th>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Course').'</th>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Timing').'</th>';
            $table['internal'][$userId]['msg']         .= '</tr>';
            $table['internal'][$userId]['msg']         .= '</thead><tbody>';
            foreach($userBooking['bookingDetails'] as $booking) {
                $table['internal'][$userId]['timeslot'] =  $booking['time_slot'];
                if($userBooking['type'] != 'student') {
                    $bookingType    = (isset($args['Booking']['type']) && ($args['Booking']['type'] == 'testing'))?__('Test Booking'):__('Track Booking');
                    $table['internal'][$userId]['msg'].= '<tr>
                        <td style="'.$style2.'">'.$tracks[$booking['track_id']].'</td>
                        <td style="'.$style2.'">'.$booking['student'].'</td>
                        <td style="'.$style2.'">'.$bookingType.'</td>
                        <td style="'.$style2.'">'.$booking['course'].'</td>
                        <td style="'.$style2.'">'.$booking['time_slot'].'</td></tr>';
                } else {
                    $bookingType    = (isset($args['Booking']['type']) && ($args['Booking']['type'] == 'testing') && isset($testBookingCounts[$userId]))?__('Test Booking - ').($testBookingCounts[$userId]+1):__('Track Booking');
                    $table['internal'][$userId]['msg'].= '<tr>
                        <td style="'.$style2.'">'.$tracks[$booking['track_id']].'</td>
                        <td style="'.$style2.'">'.$bookingType.'</td>
                        <td style="'.$style2.'">'.$booking['course'].'</td>
                        <td style="'.$style2.'">'.$booking['time_slot'].'</td></tr>';
                }
            }
            
            $table['internal'][$userId]['msg'] .= '</tbody></table>';
        }
        foreach($external as $phone => $details) {
            $detail = $details['bookingDetails'][0];
            $studentTimeSlots = Hash::extract($details['bookingDetails'],'{n}.time_slot');
            $table['external'][]    = array(
                'number'        => '45'.$phone,
                'name'          => $detail['name'],
                'track'         => $tracks[$detail['track_id']],
                'timeslot'      => implode(', ',$studentTimeSlots),
                'bookedby'      => $userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname'],
                'bookingdate'   => date('d.m.Y',  strtotime($args['Booking']['date'])),
                'area'          => $this->areaListArr[$args['Booking']['area_slug']],
                'message_type'  => 'studentDeleteTemplate',
            );
        }
        return $table;
    }
    
    private function sendInvites($args,$action = NULL) {

        //$studentsSendSmsArr     = Hash::extract($this->request->data['BookingTrack'],'{n}.send_sms');
        
        $testBookingCounts  = array();
        
        if(isset($args['Booking']['type']) && ($args['Booking']['type'] == 'testing')) {
            $testBookingCounts  = $this->Booking->getTestBookingCounts($args);
        }
        $courses        = $this->Course->find('list');
        $userDetails    = $this->User->find('all');
        $userDetails    = Hash::combine($userDetails,'{n}.User.id','{n}.User');
        $bookingDetails = $args['BookingTrack'];
        $bookingArr     = array();
        $tracks         = Hash::combine($this->Track->findAllByAreaId(
            $args['Booking']['area_slug']),'{n}.Track.id','{n}.Track.name'
        );
        $external       = array();
        $usedTracks     = array();
        $userTimeSlots  = array();

        foreach($bookingDetails as $booking) {
            $booking['course'] = $args['Booking']['course'];
            if(($booking['student_id'] != -1) && !is_null($booking['student_id'])) {
                $bookingArr[$booking['student_id']]['type'] = 'student';
                $bookingArr[$booking['student_id']]['bookingDetails'][] = array(
                    'track_id'  => $booking['track_id'],
                    'time_slot' => $booking['time_slot'],
                    'course'    => (isset($booking['course']) && isset($courses[$booking['course']]))?$courses[$booking['course']]:'',
                );
            } else if($booking['student_id'] == -1 || empty($booking['student_id'])) {
                if(!empty($booking['phone']) && (!is_null($booking['phone']))) {
                    $userTimeSlots[$booking['phone']][] =  $booking['time_slot'];
                    if(!in_array($booking['track_id'],$usedTracks)) {
                        $usedTracks[] = $booking['track_id'];
                        $external[$booking['phone']]['type']   = 'student';
                        $external[$booking['phone']]['bookingDetails'] = array(
                            'name'      => $booking['name'],
                            'track_id'  => $booking['track_id'],
                            'time_slot' => $booking['time_slot'],
                            'course'    => (isset($booking['course']) && isset($courses[$booking['course']])) ? $courses[$booking['course']] : '',
                        );
                    }
                }
            }
            
            if(isset($userTimeSlots[$booking['phone']])) {
                $external[$booking['phone']]['bookingDetails']['time_slot'] = implode(', ',$userTimeSlots[$booking['phone']]);
            }
            
            $student    = '';
            if(isset($booking['student_id']) && !empty($booking['student_id']) && isset($userDetails[$booking['student_id']])) {
                $student    = $userDetails[$booking['student_id']]['firstname'].' '.$userDetails[$booking['student_id']]['lastname'];
            } else if((isset($booking['unknown']) && !empty($booking['unknown'])) || !empty($booking['name'])) {
                $student    = $booking['name'].' ('.__('External User').')';
            } else {
                $student    =  __('External User');
            }
            if($action != 'edit') {
                if((isset($args['Booking']['reference']) && empty($args['Booking']['reference'])) || !isset($args['Booking']['reference']) || $action == 'delete') {
                    $bookingArr[$args['Booking']['user_id']]['type']    = 'teacher';
                    $bookingArr[$args['Booking']['user_id']]['bookingDetails'][] = array(
                        'track_id'  => $booking['track_id'],
                        'student'   => $student,
                        'time_slot' => $booking['time_slot'],
                        'course'    => (isset($booking['course']) && isset($courses[$booking['course']])) ? $courses[$booking['course']] : '',
                    );

                    if(!empty($args['Booking']['co_teacher']) && !is_null($args['Booking']['co_teacher'])) {
                         $bookingArr[$args['Booking']['co_teacher']]['type']                = 'teacher';
                         $bookingArr[$args['Booking']['co_teacher']]['bookingDetails'][]    = array(
                            'track_id'  => $booking['track_id'],
                            'student'   => $student,
                            'time_slot' => $booking['time_slot'],
                            'course'    => (isset($booking['course']) && isset($courses[$booking['course']]))?$courses[$booking['course']]:'',
                         );
                    }
                }
            }
        }
        
        $table = array();
        $i = 1;
        $style1 = "background-color: rgb(238, 238, 238); border: 1px solid rgb(187, 187, 187); color: rgb(51, 51, 51); font-family: sans-serif; padding: 10px; vertical-align: top;width:200px;";
        $style2 = "background-color: rgb(255, 255, 255); border: 1px solid rgb(187, 187, 187); color: rgb(51, 51, 51); font-family: sans-serif; padding: 10px; vertical-align: top;width:200px;";
        
        foreach($bookingArr as $userId => $userBooking) {
            $table['internal'][$userId]['email']        = (empty($userDetails[$userId]['email_id']))?'jesper@schlebaum.dk':$userDetails[$userId]['email_id'];
            $table['internal'][$userId]['firstname']    = $userDetails[$userId]['firstname'];
            $table['internal'][$userId]['lastname']     = $userDetails[$userId]['lastname'];
            $table['internal'][$userId]['area']         = $this->areaListArr[$args['Booking']['area_slug']];
            $table['internal'][$userId]['bookingdate']  = date('d.m.Y',  strtotime($args['Booking']['date']));
            $table['internal'][$userId]['bookedby']     = $userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname'];
            
            $table['internal'][$userId]['phone_no']     = $userDetails[$userId]['phone_no'];//4560607550;
            $table['internal'][$userId]['other_phone_no']     = $userDetails[$userId]['other_phone_no'];//4560607550;
            $table['internal'][$userId]['all_timeslot']     = implode(',',  array_unique(Hash::extract($userBooking['bookingDetails'], '{n}.time_slot')));
            if($userBooking['type'] == 'student') {
                $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'studentDeleteTemplate' : 'studentTemplate';
            } else {
                if(isset($args['Booking']['on_behalf']) && $args['Booking']['on_behalf'] != 0) {
                    $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'bookedByDeleteTemplate' : 'bookedByTemplate';
                } else {
                    $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'teacherDeleteTemplate' : 'teacherTemplate';
                }
            }
            
            $table['internal'][$userId]['msg']          = '<table border="0" align="center"><thead>';
            $table['internal'][$userId]['msg']         .= '<tr>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Track').'</th>';
            if($userBooking['type'] != 'student') {
                $table['internal'][$userId]['msg']     .= '<th style="'.$style1.'">'.__('Student').'</th>';
            }
            //$table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Booking Type').'</th>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Course').'</th>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">'.__('Time').'</th>';
            $table['internal'][$userId]['msg']         .= '</tr>';
            $table['internal'][$userId]['msg']         .= '</thead><tbody>';
            foreach($userBooking['bookingDetails'] as $booking) {
                $table['internal'][$userId]['timeslot'] =  $booking['time_slot'];
                if($userBooking['type'] != 'student') {
                    $bookingType    = (isset($args['Booking']['type']) && ($args['Booking']['type'] == 'testing'))?__('Test Booking'):__('Track Booking');
                    $table['internal'][$userId]['msg'].= '<tr>
                        <td style="'.$style2.'">'.$tracks[$booking['track_id']].'</td>
                        <td style="'.$style2.'">'.$booking['student'].'</td>
                        <td style="'.$style2.'">'.$booking['course'].'</td>
                        <td style="'.$style2.'">'.$booking['time_slot'].'</td></tr>';
                } else {
                    $bookingType    = (isset($args['Booking']['type']) && ($args['Booking']['type'] == 'testing') && isset($testBookingCounts[$userId]))?__('Test Booking - ').($testBookingCounts[$userId]+1):__('Track Booking');
                    $table['internal'][$userId]['msg'].= '<tr>
                        <td style="'.$style2.'">'.$tracks[$booking['track_id']].'</td>
                        <td style="'.$style2.'">'.$booking['course'].'</td>
                        <td style="'.$style2.'">'.$booking['time_slot'].'</td></tr>';
                }
            }
            
            $table['internal'][$userId]['msg'] .= '</tbody></table>';
        }

        foreach($external as $phone => $details) {
            $detail = $details['bookingDetails'];
            $table['external'][]    = array(
                'number'        => (substr($phone, 0, 2) == '45') ? $phone : '45'.$phone,
                'name'          => @$detail['name'],
                'track'         => @$tracks[$detail['track_id']],
                'timeslot'      => @$detail['time_slot'],
                'bookedby'      => $userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname'],
                'bookingdate'   => date('d.m.Y',  strtotime($args['Booking']['date'])),
                'area'          => $this->areaListArr[$args['Booking']['area_slug']],
                'message_type'  => (!is_null($action) && $action == 'delete') ? 'studentDeleteTemplate' : 'externalbookingdetails',
            );
        }

        return $table;
    }
    
    public function calendar() {
        $args               = array();
        $generatedTimeSlots = array();
        $areaTimeSlot       = array();
        
        $joins[]    = array(
            'table'         => 'tracks',
            'alias'         => 'Track',
            'type'          => 'LEFT',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                "Track.area_id = Area.slug AND Track.status = 'active'"
            )
        );
        
        $this->Booking->closeReopenTrack();
        
        $areas  = $this->Area->find('all',array(
            'fields'    => array('Area.name','Area.slug','Count(Track.id) as lane'),
            'joins'     => $joins,
            'group'     => array('Area.slug HAVING Count(Track.id) > 0'),
        ));
        
        $selectedArea   = (isset($this->request->query['area']) && !empty($this->request->query['area'])) ? $this->request->query['area'] : $areas[0]['Area']['slug'];
        $currDate       = (isset($this->request->query['date']) && !empty($this->request->query['date'])) ? $this->request->query['date'] : date('Y-m-d');
        $weekNo         = (isset($this->request->query['week']) && !empty($this->request->query['week'])) ? $this->request->query['week'] : date('W',strtotime($currDate));
        $year           = (isset($this->request->query['year']) && !empty($this->request->query['year'])) ? $this->request->query['year'] : date('Y',strtotime($currDate));
        
        if(isset($this->request->query['select_month']) && $this->request->query['select_month'] == 'true') {
            $timeString = strtotime($currDate);
            $currDate   = date('Y-m-d',$timeString);
            $weekNo     = date('W',$timeString);
            $year       = date('Y',$timeString);
        }
        $lastYearTotalWeeks = date('W', strtotime('28-12-'.($year-1)));
        if(date('m',  strtotime($currDate)) == 1 && $weekNo == $lastYearTotalWeeks) {
            $year -= 1;
        }
        
        $totalWeeks     = date('W', strtotime('28-12-'.$year));
        $weekDates      = $this->getWholeWeek($weekNo, $year);
        
        $this->Session->delete('warningDisplayed');
        $this->breadcrum('calendar');
        
        $tracks         = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');
        $areaList       = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
        $notifications  = $this->Tnc->notificationCount($this->currentUser['User']['id']);
        $courses        = Hash::combine($this->Course->find('all'),'{n}.Course.id','{n}.Course','{n}.Course.area');
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        $students       = $this->User->find('list',array(
            'conditions'    => array('User.role' => 'student'),
            'fields'        => array('id','name')
        ));
        
        $conditions = array(
            'Booking.date BETWEEN ? AND ?'  => array($weekDates['database'][0],$weekDates['database'][6]),
        );
        
        $bookings   = $this->BookingTrack->find('all',array(
            'fields'        => array('Booking.area_slug','Booking.date','Booking.user_id','BookingTrack.id','BookingTrack.booking_id',
                'BookingTrack.time_slot','BookingTrack.track_id','BookingTrack.booking_user_id'),
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
        
        $countTracksArr = array();
        foreach($bookings as $booking) {
            $countTracksArr[$booking['Booking']['area_slug']][$booking['Booking']['date']][$booking['BookingTrack']['time_slot']][] = $booking;
        }
        $weekTimeSlots  = array();
        $weekDatesArr = Hash::combine($weekDates,'database.{n}','display.{n}');
        foreach($areas as $area) {
            $args['area'] = $area['Area']['slug']; 
            $generatedTimeSlots[$args['area']]     = $this->Booking->getBookingTimeSlotsForTheDay($args);
            foreach($area['AreaTimeSlot'] as $timeslots) {
                $areaTimeSlot[$args['area']][] = date('H:i',strtotime($timeslots['time_slots']));
            }
            $countTracks = count($tracks[$args['area']]);
            $weekTimeSlots[$args['area']]['timeSlot']['label'] = __('Time Slots');
            $weekTimeSlots[$args['area']]['timeSlot']['slots'] = $weekDatesArr;
            foreach($generatedTimeSlots[$args['area']]['actual'][$area['Area']['slug']] as $timeSlot) {
                $weekTimeSlots[$args['area']][$timeSlot]['timeSlot'] = $timeSlot;
                foreach($weekDates['database'] as $week) {
                    $school = (isset($countTracksArr[$args['area']][$week][$timeSlot][0]) && 
                            !empty($countTracksArr[$args['area']][$week][$timeSlot][0])) ?
                        $users[$countTracksArr[$args['area']][$week][$timeSlot][0]['Booking']['user_id']]['company_id'] : '';
                    //$users[$countTracksArr[$args['area']][$week][$timeSlot][0]['Booking']['user_id']]['nick_name_company'] : '';
                    
                    $weekTimeSlots[$args['area']][$timeSlot][$week] = array(
                        'count'     => isset($countTracksArr[$args['area']][$week][$timeSlot]) ? ((count($countTracksArr[$args['area']][$week][$timeSlot]) >= $countTracks) ? '' : ($countTracks - count($countTracksArr[$args['area']][$week][$timeSlot]))) : $countTracks,
                        'school'    => ($school == '') ? '' : '('.$school.')',
                        'class'     => isset($countTracksArr[$args['area']][$week][$timeSlot]) ? ((count($countTracksArr[$args['area']][$week][$timeSlot]) >= $countTracks) ? 'all_tracks_booked' : 'some_tracks_booked') : 'no_tracks_booked',
                    );
                }
            }
        }
        
        if($selectedArea != $areas[0]['Area']['slug']) {
            rsort($areas);
        }
        
        if($weekNo == 1) {
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
        
        $danishMonths = Configure::read('danishMonths');

        // echo "<pre>";
        // print_r($tracks);
        // exit;
        
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
            'selectedArea'  => $selectedArea,
            'currDate'      => $currDate,
            'year'          => $year,
            'weekNo'        => $weekNo,
            'totalWeeks'    => $totalWeeks,
            'prevYear'      => $prevYear,
            'prevWeek'      => $prevWeek,
            'nextYear'      => $nextYear,
            'nextWeek'      => $nextWeek,
            'startDate'     => (isset($weekDates['database'][0])) ? $weekDates['database'][0] : date('Y-m-d'),
            'calendarTitle' => $danishMonths[date('F',strtotime($weekDates['database'][6]))].' - '.date('Y',strtotime($weekDates['database'][6])).' - '.__('Week').' '.date('W',strtotime($weekDates['database'][0])),
            'live_edit_time' => Configure::read('live_edit_time.time')
        ));
        
        if($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->render('Ajax/week_calendar');
        }
    }
    
    public function getBookings() {
        $timeSlotWiseBookings   = array();
        
        $args['area']           = $this->request->query['area'];
        $args['date']           = date('Y-m-d',strtotime(str_replace('.','-',$this->request->query['date'])));
        
        $generatedTimeSlots     = $this->Booking->getBookingTimeSlotsForTheDay($args);
        
        $conditions = array(
            'Booking.area_slug'     => $this->request->query['area'],
            'Booking.date'          => date('Y-m-d',strtotime(str_replace('.','-',$this->request->query['date']))),
        );
        if(isset($this->request->query['teacher_booking_detail']) && !empty($this->request->query['teacher_booking_detail'])) {
            $conditions['Booking.user_id'] = $this->request->query['teacher_booking_detail'];
        }
        $bookings       = $this->Booking->find('all',array(
            'joins'         => array(
                array(
                    'table'         => 'booking_tracks',
                    'alias'         => 'BookingTrack',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => $conditions,
            'group'         => array('Booking.id')
        ));
        
        $userDetails    = $this->User->find('all');
        $userDetails    = Hash::combine($userDetails,'{n}.User.id','{n}.User');
        
        $selectedAreaTimeSlots  = $this->Area->find('all',array(
            'conditions'        => array(
                'Area.slug'     => $this->request->query['area']
            )
        ));
        
        $selectedAreaTimeSlots  = Hash::combine($selectedAreaTimeSlots,'{n}.AreaTimeSlot.{n}.time_slots','{n}.AreaTimeSlot.{n}.time_slots');
        
        $selectedAreaTracks     = $this->Track->find('all',array(
            'conditions'        => array(
                'Track.area_id'     => $this->request->query['area']
            )
        ));
        
        $selectedAreaTracksName = Hash::combine($selectedAreaTracks,'{n}.Track.name','{n}.Track.id');
        $selectedAreaTracks     = Hash::combine($selectedAreaTracks,'{n}.Track.id','{n}.Track.name');
        
        $studentBookedTimeSlots = array();
        
        /*
         *  Modify BookingDetails
         */
        $i = 0;
        foreach($bookings   as  $booking) {
            foreach($booking['BookingTrack'] as $bookingTrack) {
                $id             = $bookingTrack['id'];
                $studentFlag    = (empty($bookingTrack['student_id']))? FALSE:TRUE;
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['teacher']          = $booking['Booking']['user_id'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['booking_id']       = $bookingTrack['booking_id'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['time_slots'][]     = $bookingTrack['time_slot'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['status']           = $bookingTrack['status'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['other_student']    = (!empty($bookingTrack['other_student'])) ? $bookingTrack['other_student'] : ' ';
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['release_track']    = $bookingTrack['release_track'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['track_status']     = $bookingTrack['track_status'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['student_flag']     = $studentFlag;
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['student_id']       = $bookingTrack['student_id'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['booking_user_id']       = $bookingTrack['booking_user_id'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['note'] = $booking['Booking']['full_description'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$id]['date_of_birth']    = (isset($userDetails[$bookingTrack['student_id']]['date_of_birth']) && !empty($userDetails[$bookingTrack['student_id']]['date_of_birth']))?
                    $userDetails[$bookingTrack['student_id']]['date_of_birth'] : $bookingTrack['date_of_birth'];
                $timeSlotWiseBookings[$bookingTrack['time_slot']][]   = array(
                    'teacher'       => $booking['Booking']['user_id'],
                    'booking_id'    => $bookingTrack['booking_id']
                );
                if($bookingTrack['track_status'] == 'reopen') {
                    $timeSlotWiseBookings[$bookingTrack['time_slot']]['track_status']   = $bookingTrack['track_status'];
                }
                $i++;
            }
        }
        
        /*
         *  Set Time Slot as per Booked Time slots
         */
        $modifiedStudentArray  = array();
        if(!empty($studentBookedTimeSlots)) {
            foreach($selectedAreaTracks as $id => $track) {
                if(isset($studentBookedTimeSlots[$id])) {
                    foreach($studentBookedTimeSlots[$id] as $student_id => $studentBookedTimeSlot) {
                        foreach($generatedTimeSlots['mapping'][$args['area']] as $mapped => $timeSlot) {
                            foreach($timeSlot as $slot) {
                                if(in_array($slot,$studentBookedTimeSlots[$id][$student_id]['time_slots'])) {
                                    $modifiedStudentArray[$id][$mapped]['booking_id']       = $studentBookedTimeSlots[$id][$student_id]['booking_id'];
                                    $modifiedStudentArray[$id][$mapped]['student_id']       = ($studentBookedTimeSlots[$id][$student_id]['student_flag']) ? $studentBookedTimeSlots[$id][$student_id]['student_id'] : NULL;
                                    $modifiedStudentArray[$id][$mapped]['teacher']          = $studentBookedTimeSlots[$id][$student_id]['teacher'];
                                    $modifiedStudentArray[$id][$mapped]['status']           = $studentBookedTimeSlots[$id][$student_id]['status'];
                                    $modifiedStudentArray[$id][$mapped]['other_student']    = $studentBookedTimeSlots[$id][$student_id]['other_student'];
                                    $modifiedStudentArray[$id][$mapped]['release_track']    = $studentBookedTimeSlots[$id][$student_id]['release_track'];
                                    $modifiedStudentArray[$id][$mapped]['track_status']     = $studentBookedTimeSlots[$id][$student_id]['track_status'];
                                    $modifiedStudentArray[$id][$mapped]['date_of_birth']    = $studentBookedTimeSlots[$id][$student_id]['date_of_birth'];
                                    $modifiedStudentArray[$id][$mapped]['note']             = $studentBookedTimeSlots[$id][$student_id]['note'];
                                    $modifiedStudentArray[$id][$mapped]['booking_user_id']             = $studentBookedTimeSlots[$id][$student_id]['booking_user_id'];
                                }
                            }
                        }
                    }
                }
            }
        }
        $finalBookingDetails    = array();
        $i  = 0;
        
        /*
         *  Populate time Slot wise Booking Array
         */
            
        foreach($generatedTimeSlots['display'][$args['area']] as $timeSlot) {
            $finalBookingDetails[$i][0] = array(
                'key'   => $timeSlot
            );
            
            foreach($selectedAreaTracks as $trackId => $selectedAreaTrack) {
                if(isset($modifiedStudentArray[$trackId][$timeSlot]) && !empty($modifiedStudentArray[$trackId][$timeSlot])) {
                    $finalBookingDetails[$i][]  = array(
                        'teacher'       => $modifiedStudentArray[$trackId][$timeSlot]['teacher'],
                        'user'          => $modifiedStudentArray[$trackId][$timeSlot]['student_id'],
                        'id'            => $modifiedStudentArray[$trackId][$timeSlot]['booking_id'],
                        'status'        => $modifiedStudentArray[$trackId][$timeSlot]['status'],
                        'other_student' => $modifiedStudentArray[$trackId][$timeSlot]['other_student'],
                        'release_track' => $modifiedStudentArray[$trackId][$timeSlot]['release_track'],
                        'track_status'  => $modifiedStudentArray[$trackId][$timeSlot]['track_status'],
                        'date_of_birth' => $modifiedStudentArray[$trackId][$timeSlot]['date_of_birth'],
                        'track_id'      => $trackId,
                        'note'          => $modifiedStudentArray[$trackId][$timeSlot]['note'],
                        'booking_user_id'          => $modifiedStudentArray[$trackId][$timeSlot]['booking_user_id'],
                    );
                } else {
                    $finalBookingDetails[$i][]  = array(
                        'time_slot'     => $timeSlot,
                        'track_id'      => $trackId
                    );
                }
            }
            
            $i++;
        }        
        $released_ip_address = $this->Option->getOption('release_track_ip_adrress');
        $headers    = $selectedAreaTracks;
        
        array_unshift($headers,__('Tid'));

        // echo "<pre>";print_r($finalBookingDetails);exit;
       
        $this->set(array(
            'date'                  => $this->request->query['date'],
            'finalBookingDetails'   => $finalBookingDetails,
            'headers'               => $headers,
            'area'                  => $this->request->query['area'],
            'userDetails'           => $userDetails,
            'timeSlotWiseBookings'  => $timeSlotWiseBookings,
            'generatedTimeSlots'    => $generatedTimeSlots,
            'released_ip_address'   => $released_ip_address,
        ));
        $this->layout   = 'ajax';
        $this->render('Ajax/bookings');
    }
    
    public function releaseTrack($type = NULL) {
        
        $conditions         = array();
        $courseConditions   = array();
        if(isset($this->request->query['area']) && !empty($this->request->query['area'])) {
            $conditions['Booking.area_slug'] = $this->request->query['area'];
        }
        
        if(isset($this->request->query['date']) && !empty($this->request->query['date'])) {
            $conditions['Booking.date'] = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date'])));
        }
        
        if(isset($this->request->query['time_slot']) && !empty($this->request->query['time_slot'])) {
            
            $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay(array(
                'area'  => $this->request->query['area'],
                'date'  => date('Y-m-d',strtotime(str_replace('.','-',$this->request->query['date']))),
            ));
            
            foreach($generatedTimeSlots['mapping'][$this->request->query['area']][$this->request->query['time_slot']] as $timeSlot) {
                $conditions['BookingTrack.time_slot'][] = $timeSlot;
            }
        }
        
        $users      = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $allBookings = $this->BookingTrack->find('all',array(
            'fields'        => array('BookingTrack.id','Booking.id','Booking.course','Booking.co_teacher','Booking.user_id',
                'BookingTrack.student_id','BookingTrack.booking_user_id','BookingTrack.track_id','BookingTrack.booking_id','BookingTrack.track_status',
                'BookingTrack.phone','BookingTrack.name','BookingTrack.release_track','BookingTrack.status','Booking.reference'),
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'alias'         => 'Booking',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'BookingTrack.booking_id = Booking.id'
                    )
                )
            ),
            'conditions'    => $conditions,
        ));
        $bookings = Hash::combine($allBookings,'{n}.Booking.id','{n}');
        $modifiedBookings = array();
        foreach($allBookings as $booking) {
            $modifiedBookings[$booking['BookingTrack']['track_id']] = array(
                'booking_id'    => $booking['BookingTrack']['booking_id'],
                'student_id'    => $booking['BookingTrack']['student_id'],
                'track_id'      => $booking['BookingTrack']['track_id'],
                'track_status'  => $booking['BookingTrack']['track_status'],
                'phone'         => $booking['BookingTrack']['phone'],
                'name'          => $booking['BookingTrack']['name'],
                'release_track' => $booking['BookingTrack']['release_track'],
                'status'        => $booking['BookingTrack']['status'],
                'user_id'       => $booking['Booking']['user_id'],
                'booking_user_id'       => $booking['BookingTrack']['booking_user_id'],
            );
            if(empty($booking['Booking']['reference'])) {
                $modifiedBookings['course']             = $booking['Booking']['course'];
                $modifiedBookings['co_teacher_auto']    = (isset($users[$booking['Booking']['co_teacher']])) ? $users[$booking['Booking']['co_teacher']]['firstname'].' '.$users[$booking['Booking']['co_teacher']]['lastname'] : '';
                $modifiedBookings['co_teacher']         = $booking['Booking']['co_teacher'];
                $modifiedBookings['id']                 = $booking['Booking']['id'];
            } else {
                $modifiedBookings['course']             = $bookings[$booking['Booking']['reference']]['Booking']['course'];
                $modifiedBookings['co_teacher_auto']    = (isset($users[$bookings[$booking['Booking']['reference']]['Booking']['co_teacher']])) ? $users[$bookings[$booking['Booking']['reference']]['Booking']['co_teacher']]['firstname'].' '.$users[$bookings[$booking['Booking']['reference']]['Booking']['co_teacher']]['lastname'] : '';
                $modifiedBookings['co_teacher']         = $bookings[$booking['Booking']['reference']]['Booking']['co_teacher'];
                $modifiedBookings['id']                 = $booking['Booking']['reference'];
            }
        }
        $tracks = $this->Track->find('list',array(
            'conditions'    => array(
                'area_id'   => $this->request->query['area']
            )
        ));
        if(isset($this->request->query['area']) && !empty($this->request->query['area'])) {
            $courseConditions = array(
                'area' => $this->request->query['area']
            );
        }
        $courses        = $this->Course->find('list',array('fields' => array('id','name'),'conditions' => $courseConditions));
        $drivingSchool  = $this->Company->find('list',array('fields' => array('nick_name','name'))); 
        
        $this->set(array(
            'bookings'      => $modifiedBookings,
            'tracks'        => $tracks,
            'users'         => $users,
            'courses'       => $courses,
            'drivingSchool' => $drivingSchool,
        ));
        $this->layout   = 'fancybox';
    }
    
    public function updateTrack() {
        $studentArr = Hash::extract($this->request->data['Booking'],'{n}.selected_student_id');
        foreach($studentArr as $key => $studentId) {
            if(empty($studentId)) {
                unset($studentArr[$key]);
            }
        }
        $updateBooking = array(
            'co_teacher'    => $this->request->data['Booking']['co_teacher'],
            'course'        => $this->request->data['Booking']['course'],
            'id'            => $this->request->data['Booking']['id'],
        );
        foreach($this->request->data['Booking'] as $key => $booking) {
            if(empty($booking['booking_id'])) {
                unset($this->request->data['Booking'][$key]);
            }
        }
        if(isset($this->request->data['Booking']['co_teacher'])) {
            unset($this->request->data['Booking']['co_teacher']);
        }
        if(isset($this->request->data['Booking']['course'])) {
            unset($this->request->data['Booking']['course']);
        }
        if(isset($this->request->data['Booking']['id'])) {
            unset($this->request->data['Booking']['id']);
        }
        if(isset($this->request->data['Booking']['co_teacher_auto'])) {
            unset($this->request->data['Booking']['co_teacher_auto']);
        }
        $this->request->data['Booking'] = array_values($this->request->data['Booking']);
        
        $errorDetails = $this->Booking->validateReleasedTrack($this->request->data['Booking'],$studentArr);
        if($errorDetails['status'] == 'error') {
            
            $this->set(array(
                'error_msg'     => $errorDetails['error_msg']
            ));
            
            $this->layout = 'ajax';
            $this->render('Ajax/error');
            
            return;
        }

        $get_all_closed_tracks = $this->BookingTrack->find('all',array(
            'fields'        => array('BookingTrack.id', 'Booking.id','BookingTrack.track_status'),
            'joins'         => array(
                array(
                    'table' => 'bookings',
                    'alias' => 'Booking',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.id = BookingTrack.booking_id',
                        'Booking.date ' => $this->request->data['Booking'][0]['date'],
                        'Booking.area_slug ' => $this->request->data['Booking'][0]['area'],        
                    )
                )
            ),
            'conditions'    => array(
                'track_status' => 'closed'
            )
        ));

        $result = $this->Booking->updateBookingTrack($this->request->data,$updateBooking);
        if($result) {
            $this->insertLog('track_released', $this->request->data);

            foreach ($get_all_closed_tracks as $closed_track) {
                $this->BookingTrack->updateAll(array(
                    'track_status'  => '"reopen"'
                ),array(
                    'id'    => $closed_track['BookingTrack']['id']
                ));
            }

            $this->layout = 'ajax';
            $this->render('Ajax/updateTrackSuccess');
            
            return;
        } else {
            $this->set(array(
                'message'   => __('Could not be updated, please try again.'),
                'title'     => __('Booking Track Update'),
            ));
            $this->layout = 'ajax';
            $this->render('Ajax/updateTrackError');
            
            return;
        }
    }
    
    public function getReleasedTracks() {
        
        $ip_address = $this->Option->getOption('ip_adrress');
     
        if(empty($this->currentUser) && ($_SERVER['SERVER_ADDR'] == $ip_address)) {
            return $this->redirect(array('controller'   => 'adminpages','action' => 'home'));
        }
        
        $conditions     = array();
        $tracks         = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');
        
        $joins[]        = array(
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
        
        $areaList = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
        
        $conditions['BookingTrack.release_track']       = 1;
        $conditions['BookingTrack.status']              = 'met';
        // $conditions['Booking.date']                     = date('Y-m-d');
        $conditions['Booking.date']                     = (isset($this->request->query['date']) && $this->request->query['date'] != '') ? date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date']))) : date('Y-m-d');
        $conditions['BookingTrack.track_status']        = 'closed';
        //$conditions['OR']['BookingTrack.recent_realeased_tracks BETWEEN ? AND ?'] = array(date('Y-m-d H:i:s',(time()-1200)),date('Y-m-d H:i:s',(time()+1200)));
        //$conditions['OR']['BookingTrack.track_status']  = 'reopen';
        
        $bookingTracks = Hash::extract($this->BookingTrack->find('all',array(
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'alias'         => 'Booking',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'BookingTrack.booking_id = Booking.id'
                    ),
                )
            ),
            'conditions'    => $conditions,
            'group'         => array('Booking.id','track_id','time_slot'),
            'order'         => array('Booking.date','BookingTrack.time_slot','BookingTrack.track_id','BookingTrack.id'),
            //'order'         => array('BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.id')
        )),'{n}.BookingTrack');
        
        $bookingIds = Hash::extract($bookingTracks,'{n}.booking_id');
        
        $bookings = Hash::combine($this->Booking->find('all',array(
            'conditions'        => array(
                'Booking.id'    => $bookingIds
            ),
        )),'{n}.Booking.id','{n}.Booking');
        
        $teacher = Hash::combine($this->Booking->find('all',array(
            'fields'            => array('user_id'),
            'conditions'        => array(
                'Booking.id'    => $bookingIds
            ),
        )),'{n}.Booking.id','{n}.Booking');
        
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $drivingSchools = $this->Company->find('list',array(
            'fields' => array('nick_name','name')
        ));
        
        $this->set(array(
            'tracks'            => $tracks,
            'areas'             => $areas,
            'areaList'          => $areaList,
            'bookingTracks'     => $bookingTracks,
            'users'             => $users,
            'teacher'           => $teacher,
            'bookings'          => $bookings,
            'drivingSchools'    => $drivingSchools,
        ));
    }
    
    public function searchReleasedTracks() {
        
        $conditions     = array();
        $tracks         = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');
        $bookingTracks  = array();
        $teacher        = array();
        $bookings       = array();
        $bookingJoins   = array();
        $joins[]        = array(
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
        $bookingJoins[] = array(
            'table'         => 'bookings',
            'alias'         => 'Booking',
            'type'          => 'inner',
            'conditions'    => array(
                'BookingTrack.booking_id = Booking.id'
            ),
        );
        
        $areaList = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
            
        $conditions['BookingTrack.release_track']   = 1;
        
        if(isset($this->request->query['search']) && ($this->request->query['search'] == 'true')) {
            //if(isset($this->request->query['student_id']) && !empty($this->request->query['student_id'])) {
            //  $conditions['BookingTrack.student_id']  = $this->request->query['student_id'];
            //} else if(isset($this->request->query['student']) && !empty($this->request->query['student'])) {
            //  $conditions['BookingTrack.name LIKE']  =  "%{$this->request->query['student']}%";
            //}
            if((isset($this->request->query['student']) && !empty($this->request->query['student'])) || 
                    (isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school']))) {
                
                $bookingJoins[] = array(
                    'table'         => 'users',
                    'alias'         => 'User',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'OR' => array(
                            'BookingTrack.student_id    = User.id',
                            //'BookingTrack.released_by   = User.id',
                            'Booking.user_id            = User.id'
                        )
                    ),
                );
            }
            if(isset($this->request->query['student']) && !empty($this->request->query['student'])) {

                $conditions['OR']['BookingTrack.name LIKE'] = "%{$this->request->query['student']}%";
                $conditions['OR']['User.firstname LIKE']    = "%{$this->request->query['student']}%";
                $conditions['OR']['User.lastname LIKE']     = "%{$this->request->query['student']}%";
            }
            
            if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])) {
                $conditions['Booking.user_id']  = $this->request->query['teacher_id'];
            }
            
            if(isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school'])) {
                $conditions['User.company_id']  = $this->request->query['driving_school'];
            }
            
            if(isset($this->request->query['area_id']) && !empty($this->request->query['area_id'])) {
                $conditions['Booking.area_slug']  = $this->request->query['area_id'];
            }
            
            if(isset($this->request->query['date_from']) && !empty($this->request->query['date_from']) 
               && (isset($this->request->query['date_to'])) && (empty($this->request->query['date_to']))) {
                $conditions['Booking.date'] = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_from'])));
            }
            
            if(isset($this->request->query['date_from']) && !empty($this->request->query['date_from']) 
               && (isset($this->request->query['date_to'])) && (!empty($this->request->query['date_to']))) {
                
                $datefrom   = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_from'])));
                $dateTo     = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_to'])));
                if($dateTo < $datefrom) {
                    $this->Session->setFlash(__('Please select valid date range.'),'alert/error');
                    return $this->redirect(array('controller'   => 'adminbookings','action'  => 'getReleasedTracks'));
                }
                
                $conditions['Booking.date BETWEEN ? AND ?'] = array($datefrom,$dateTo);
            }
            $conditions['BookingTrack.status']  = 'met';
            
            $bookingTracks = Hash::extract($this->BookingTrack->find('all',array(
                'joins'         => $bookingJoins,
                'conditions'    => $conditions,
                'group'         => array('Booking.id','track_id','time_slot'),
                'order'         => array('Booking.date','BookingTrack.time_slot','BookingTrack.track_id','BookingTrack.id')
            )),'{n}.BookingTrack');
            
            $bookingIds = Hash::extract($bookingTracks,'{n}.booking_id');
            
            $bookings = Hash::combine($this->Booking->find('all',array(
                'conditions'        => array(
                    'Booking.id'    => $bookingIds
                ),
            )),'{n}.Booking.id','{n}.Booking');
            
            $teacher = Hash::combine($this->Booking->find('all',array(
                'fields'            => array('user_id'),
                'conditions'        => array(
                    'Booking.id'    => $bookingIds
                ),
            )),'{n}.Booking.id','{n}.Booking');
        }
        
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $drivingSchools = $this->Company->find('list',array(
            'fields' => array('nick_name','name')
        ));
        
        $this->set(array(
            'tracks'            => $tracks,
            'areas'             => $areas,
            'areaList'          => $areaList,
            'bookingTracks'     => $bookingTracks,
            'users'             => $users,
            'teacher'           => $teacher,
            'bookings'          => $bookings,
            'drivingSchools'    => $drivingSchools,
        ));
    }
    
    public function updateTrackUser($statusUpdate = NULL) {
        $errorDetails   = $this->Booking->validateTrackUser($this->request->data,$statusUpdate);
        $result         = array();
        
        if($errorDetails['status']) {
            $this->set(array(
                'error_msg'     => $errorDetails['error_msg']
            ));
            $this->layout = 'ajax';
            $this->render('Ajax/error');
            return;
        }
        
        $bookingTrack = $this->BookingTrack->findById($this->request->data['BookingTrack']['booking_track_id']);
        
        $saveArr = array();
        
        foreach($this->request->data['BookingTrack'] as $id => $value) {
            if(is_array($value) && $id == 1 ) {
                $saveArr = array(
                    'name'          => "'{$value['name']}'",
                    'phone'         => "'{$value['phone']}'",
                    'date_of_birth' => (empty($value['date_of_birth'])) ? '' : "'".date('Y-m-d',strtotime(str_replace('/','-',$value['date_of_birth'])))."'",
                    'address'       => "'{$value['address']}'",
                    'zip_code'       => "'{$value['zip_code']}'",
                    'city'       => "'{$value['city']}'",
                );
                
                $result[] = $this->BookingTrack->updateAll($saveArr, array(
                        'id'    => $this->request->data['BookingTrack']['booking_track_id']
                    )
                );
            }
            
            if(is_array($value) && $id == 2) {
                if($value['new_student'] == 'true') {
                    $saveArr = array(
                        'name'          => "'{$value['name']}'",
                        'phone'         => "'{$value['phone']}'",
                        'date_of_birth' => (empty($value['date_of_birth'])) ? '' : "'".date('Y-m-d',strtotime(str_replace('/','-',$value['date_of_birth'])))."'",
                        'address'       => "'{$value['address']}'",
                        'status'        => "'met'",
                    );
                    $result[] = $this->BookingTrack->updateAll($saveArr, array(
                            'track_id'      => $bookingTrack['BookingTrack']['track_id'],
                            'booking_id'    => $bookingTrack['BookingTrack']['booking_id'],
                            'time_slot'     => $bookingTrack['BookingTrack']['time_slot'],
                            'id !='         => $this->request->data['BookingTrack']['booking_track_id'],
                        )
                    );
                } else {
                    $saveArr = array(
                        'student_id'    => $value['id'],
                        'name'          => "{$value['name']}",
                        'phone'         => "{$value['phone']}",
                        'date_of_birth' => (empty($value['date_of_birth'])) ? '' : date('Y-m-d',strtotime(str_replace('/','-',$value['date_of_birth']))),
                        'address'       => "{$value['address']}",
                        'status'        => "met",
                        'track_id'      => $bookingTrack['BookingTrack']['track_id'],
                        'booking_id'    => $bookingTrack['BookingTrack']['booking_id'],
                        'time_slot'     => $bookingTrack['BookingTrack']['time_slot'],
                        'release_track' => 1,
                        'other_student' => 1,
                    );
                    $this->BookingTrack->set($saveArr);
                    $result[] = $this->BookingTrack->save();
                }
            }
            
            if(is_array($value)) {
                if(!empty($value['id']) || !is_null($value['id'])) {
                    $name       = explode(' ',$value['name']);
                    $firstname  = $name[0];
                    unset($name[0]);
                    $lastname   = implode(' ',$name);
                    
                    $saveArr = array(
                        'firstname'     => "'{$firstname}'",
                        'lastname'      => "'{$lastname}'",
                        'phone_no'      => "'{$value['phone']}'",
                        'date_of_birth' => "'".date('Y-m-d',strtotime(str_replace('/','-',$value['date_of_birth'])))."'",
                        'address'       => "'{$value['address']}'",
                    );
                    
                    $result[] = $this->User->updateAll($saveArr, array(
                            'id'    => $value['id']
                        )
                    );
                }
            }
        }
        $message    = __('Booking Track updated successfully');
        $status     = 'success';
        
        if(in_array(FALSE,$result)) {
            $message    = __('Booking Track could not updated');
            $status     = 'failure';
        }
        
        $this->set(array(
            'message'   => __('Vi har nu registreret dine informationer i systemet'),
            'title'     => __('Dine informationer er gemt'),
            'status'    => $status,
        ));
        
        $this->layout = 'ajax';
        $this->render('Ajax/updateTrackSuccessNew');
        return;
    }
    
    public function updateStudentDetails($id) {
        if($this->request->is(array('ajax','post'))) {
            
            $result   =  $this->User->updateAll(array(
                'User.student_medical_profile'  => "'"."yes"."'",
            ),array(
                'User.id'                       => $id
            ));
            
            if($result) {
               $bookingData = array(
                   'status' => 'success'
               );
               $this->set('bookingData',$bookingData);
               
               $this->layout    = 'ajax';
               $this->render('Ajax/json');
               return;
            } else {
               $bookingData = array(
                   'status' => 'error'
               );
               
               $this->set('bookingData',$bookingData);
               
               $this->layout    = 'ajax';
               $this->render('Ajax/json');
               return;
            }
        }
    }
    
    public function closeTracks() {
        $this->request->query['date'] = date('Y-m-d',strtotime(str_replace('.','-',$this->request->query['date'])));
        $update = array();
        $bookingIds = Hash::extract($this->Booking->find('all',array(
            'joins' => array(
                array(
                    'table'         => 'booking_tracks',
                    'alias'         => 'BookingTrack',
                    'type'          => 'INNER',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    ),
                )
            ),
            'conditions'    => array(
                'Booking.date'              => $this->request->query['date'],
                'Booking.area_slug'         => $this->request->query['area'],
                'BookingTrack.time_slot'    => $this->request->query['time'],
            ),
            'recursive'     => 0,
        )),'{n}.Booking.id');

        $this->insertLog('track_close', $bookingIds);

        /*$get_all_closed_tracks = $this->BookingTrack->find('all',array(
            'fields'        => array('BookingTrack.id', 'Booking.id','BookingTrack.track_status'),
            'joins'         => array(
                array(
                    'table' => 'bookings',
                    'alias' => 'Booking',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.id = BookingTrack.booking_id',
                        'Booking.date ' => $this->request->query['date'],
                        'Booking.area_slug ' => $this->request->query['area'],
                    )
                )
            ),
            'conditions'    => array(
                'track_status' => 'closed'
            )
        ));

        foreach ($get_all_closed_tracks as $closed_track) {
            $this->BookingTrack->updateAll(array(
                'track_status'  => '"reopen"'
            ),array(
                'id'    => $closed_track['BookingTrack']['id']
            ));
        }*/

        foreach($bookingIds as $bookingId) {
            $update[] = $this->BookingTrack->updateAll(array(
                'track_status' => "'reopen'",
            ),array(
                'BookingTrack.time_slot'    => $this->request->query['time'],
                'BookingTrack.booking_id'   => $bookingId,
            ));
        }
        
        $msg = __('Track Status Closed');
        if(in_array(FALSE, $update)) {
            $msg = __('Track Status could not be Closed');
        }
        $this->set(array(
            'title'     => __('Update Track Status'),
            'message'   => $msg,
            'date'      => $this->request->query['date'],
            'area'      => $this->request->query['area'],
            'iframe'    => 0
        ));
        $this->layout = 'ajax';
        $this->render('Ajax/success');
        return;
    }
    
    public function editTracks() {
        $this->request->query['date'] = date('Y-m-d',strtotime(str_replace('.','-',$this->request->query['date'])));
        
        $args['area']       = $this->request->query['area'];
        $args['date']       = $this->request->query['date'];
        $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay($args);
        $bookings = array();
        
        foreach($generatedTimeSlots['mapping'][$this->request->query['area']][$this->request->query['time']] as $timeSlot) {
            $bookings[] = $this->BookingTrack->find('all',array(
                'joins' => array(
                    array(
                        'table'         => 'bookings',
                        'alias'         => 'Booking',
                        'type'          => 'INNER',
                        'conditions'    => array(
                            'Booking.id = BookingTrack.booking_id'
                        ),
                    )
                ),
                'conditions'    => array(
                    'Booking.date'              => $this->request->query['date'],
                    'Booking.area_slug'         => $this->request->query['area'],
                    'BookingTrack.time_slot'    => $timeSlot,
                ),
            ));
        }
        $bookings = array_values(Hash::extract($bookings,'{n}.{n}.BookingTrack'));
        
        $tracks  = Hash::combine($this->Track->find('all',array(
            'conditions'    => array(
                'Track.status   !=' => 'inactive',
                'Track.area_id'     => $this->request->query['area']
            )
        )),'{n}.Track.id','{n}.Track.name');
        
        $area               = $this->Area->findBySlug($this->request->query['area']);
        
        $areaTimeSlots      = array();
        foreach($tracks as $id => $track){
            foreach($generatedTimeSlots['display'][$args['area']] as $timeSlot){
                $areaTimeSlots[$id][$timeSlot] = $timeSlot;
            }
        }
        
        $modifiedbookedTracks = array();
        foreach($bookings as $bookedTrack) {
            $modifiedbookedTracks[$bookedTrack['track_id']]['time_slot'][]      = $bookedTrack['time_slot'];
            $modifiedbookedTracks[$bookedTrack['track_id']]['student_id']       = $bookedTrack['student_id'];
            $modifiedbookedTracks[$bookedTrack['track_id']]['booking_id']       = $bookedTrack['booking_id'];
        }
        
        if(!empty($modifiedbookedTracks)) {
            foreach($tracks as $id => $track) {
                if(isset($modifiedbookedTracks[$id]['time_slot'])) {
                    foreach($generatedTimeSlots['mapping'][$args['area']] as $mapped => $timeSlot) {
                        foreach($timeSlot as $slot) {
                            if(in_array($slot,$modifiedbookedTracks[$id]['time_slot'])) {
                                $modifiedbookedTracks[$id]['time_slot'][$mapped] = $mapped;
                                $key = array_search($slot,$modifiedbookedTracks[$id]['time_slot']);
                                unset($modifiedbookedTracks[$id]['time_slot'][$key]);
                            }
                        }
                    }
                }
            }
        }
        
        $users          = $this->User->find('all');
        
        $selected       = array();
        $modifiedUsers  = array();
        
        foreach ($users as $user) {
            $modifiedUsers[$user['User']['id']]['name']   = $user['User']['firstname'].' '.$user['User']['lastname'];
            $modifiedUsers[$user['User']['id']]['role']   = $user['User']['role'];
        }
        
        $this->set(array(
            'tracks'                => $tracks,
            'modifiedUsers'         => $modifiedUsers,
            'modifiedbookedTracks'  => $modifiedbookedTracks,
            'isEdit'                => TRUE,
            'areaTimeSlots'         => $areaTimeSlots,
            'area'                  => $area,
            'time'                  => $selected,
            'isExternal'            => FALSE,
            'isTracksEdit'          => TRUE
        ));
        
        if($this->request->is('ajax') && $this->request->is('post')) {
            $this->processData('update');
            $errorDetails   = $this->Booking->validateData($this->request->data,FALSE,TRUE);
            
            if($errorDetails['status']) {
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');
                
                return;
            }

            $saveArr = array();
            foreach($this->request->data['BookingTrack'] as $bookingTrack) {
                if(isset($bookingTrack['booking_id']) && !empty($bookingTrack['booking_id'])) {
                    $saveArr[] = $this->BookingTrack->updateAll(array(
                        'student_id'    => $bookingTrack['student_id'],
                    ),array(
                        'track_id'      => $bookingTrack['track_id'],
                        'booking_id'    => $bookingTrack['booking_id'],
                        'time_slot'     => $bookingTrack['time_slot'],
                    ));
                } else {
                    $data = array(
                        'Booking'   => array(
                            'user_id'   => $this->currentUser['User']['id'],
                            'date'      => $this->request->data['Booking']['date'],
                            'area_slug' => $this->request->data['Booking']['area_slug'],
                        ),
                        'BookingTrack'  => array(
                            array(
                                'student_id'    => $bookingTrack['student_id'],
                                'track_id'      => $bookingTrack['track_id'],
                                'booking_id'    => $bookingTrack['booking_id'],
                                'time_slot'     => $bookingTrack['time_slot'],
                                'release_track' => 1
                            )
                        )
                    );
                    $saveArr[] = $this->Booking->saveAssociated($data);
                }
            }

            if(!in_array(FALSE,$saveArr)) {
                $this->set(array(
                    'message'   => __('The Booking Track Status has been updated.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'date'      => $this->request->query['date'],
                    'area'      => $this->request->query['area'],
                    'iframe'    => (isset($this->request->query['iframe'])) ? $this->request->query['iframe'] : 0
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
                
                return;
            } else {
                
                $this->set(array(
                    'message'   => __('The Booking Track Status could not be updated. Please try some time later.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'date'      => $this->request->query['date'],
                    'area'      => $this->request->query['area'],
                    'iframe'    => (isset($this->request->query['iframe'])) ? $this->request->query['iframe'] : 0
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
                return;
            }
        }
        
        if($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->render('add');
            return;
        }
    }
    
    public function editBookings() {
        
        $booking        = array();
        $modifiedUsers  = array();
        
        if(!$this->request->is('post')) {
            $args = array();
            $args['area']       = $this->request->query['area'];
            $args['date']       = date('Y-m-d',strtotime($this->request->query['date']));

            $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay($args);
            
            $booking        = $this->Booking->find('first',array(
                'conditions'    => array(
                    'Booking.date'              => date('Y-m-d',  strtotime(str_replace('.','-',$this->request->query['date']))),
                    'Booking.area_slug'         => $this->request->query['area'],
                    'BookingTrack.time_slot'    => $generatedTimeSlots['mapping'][$args['area']][$this->request->query['time']],
                ),
                'joins'         => array(
                    array(
                        'table'         => 'booking_tracks',
                        'alias'         => 'BookingTrack',
                        'type'          => 'INNER',
                        'conditions'    => array(
                            'Booking.id = BookingTrack.booking_id'
                        ),
                    )
                ),
                'recursive'     => 0
            ));
            
            $userIds    = array();
            $userIds[]  = $booking['Booking']['user_id'];
            $userIds[]  = $booking['Booking']['on_behalf'];
            $userIds[]  = $booking['Booking']['co_teacher'];
            
            $users          = $this->User->find('all',array(
                'conditions'    => array(
                    'User.id'   => $userIds
                )
            ));
            
            foreach ($users as $user) {
                $modifiedUsers[$user['User']['id']]['name']   = $user['User']['firstname'].' '.$user['User']['lastname'];
                $modifiedUsers[$user['User']['id']]['role']   = $user['User']['role'];
            }
        }
        
        if($this->request->is('post')) {
           $bookings        = $this->Booking->find('all',array(
               'fields'         => array('Booking.id'),
                'conditions'    => array(
                    'Booking.date'              => date('Y-m-d',  strtotime(str_replace('.','-',$this->request->data['date']))),
                    'Booking.area_slug'         => $this->request->data['area'],
                    'BookingTrack.time_slot'    => $this->request->data['time'],
                ),
                'joins'         => array(
                    array(
                        'table'         => 'booking_tracks',
                        'alias'         => 'BookingTrack',
                        'type'          => 'INNER',
                        'conditions'    => array(
                            'Booking.id = BookingTrack.booking_id'
                        ),
                    )
                ),
                'recursive'     => 0
            ));
           
            $bookings    = Hash::extract($bookings,'{n}.Booking.id');
            
            if(isset($this->request->data['Booking']['user_id']) && empty($this->request->data['Booking']['user_id'])){
                $this->request->data['Booking']['user_id']   = $this->currentUser['User']['id'];
            }
            if(isset($this->request->data['Booking']['on_behalf']) && !empty($this->request->data['Booking']['on_behalf']) && ($this->request->data['Booking']['on_behalf'])){
                $this->request->data['Booking']['on_behalf'] = $this->currentUser['User']['id'];
            }
           
            $data    = array();
            foreach($bookings as $booking) {
                $data[]  = array(
                    'Booking'    => array(
                        'id'                 => $booking,
                        'on_behalf'          => $this->request->data['Booking']['on_behalf'],
                        'co_teacher'         => $this->request->data['Booking']['co_teacher'],
                        'user_id'            => $this->request->data['Booking']['user_id'],
                        'full_description'   => $this->request->data['Booking']['full_description'],
                        'area_slug'          => $this->request->data['area']
                    )
                );
            }
            unset($this->request->data);
            $this->request->data     = $data;
            
            if($this->Booking->saveAll($this->request->data)) {
                
                $this->set(array(
                    'message'   => __('The Booking has been updated.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'date'      => $this->request->data['Booking']['date'],
                    'area'      => $this->request->data['Booking']['area_slug'],
                    'iframe'    => $this->request->query['iframe']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
                
                return;
            } else {
                $this->set(array(
                    'message'   => __('The Booking could not be updated. Please try some time later.'),
                    'status'    => 'success',
                    'title'     => __('Booking')
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
                return;
            }
        }
        
        $conditions = array();
        
        if(isset($this->request->query['area']) && !empty($this->request->query['area'])) {
            $conditions['area'] = $this->request->query['area'];
        }
        
        if(isset($this->request->data['area']) && !empty($this->request->data['area'])) {
            $conditions['area'] = $this->request->data['area'];
        }
        
        $courses    = $this->Course->find('list',array(
            'conditions'    => $conditions
        ));
        
        $this->set(array(
            'booking'       => $booking,
            'modifiedUsers' => $modifiedUsers,
            'courses'       => $courses
        ));
        
        $this->layout   = 'ajax';
    }
    
    public function reopenTracks() {
        $bookings        = $this->BookingTrack->find('all',array(
            'conditions'    => array(
                'Booking.date'              => date('Y-m-d',  strtotime(str_replace('.','-',$this->request->query['date']))),
                'Booking.area_slug'         => $this->request->query['area'],
                'BookingTrack.time_slot'    => $this->request->query['time'],
            ),
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'alias'         => 'Booking',
                    'type'          => 'INNER',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    ),
                )
            ),
            'recursive'     => 0
        ));
        
        if(!empty($bookings)) {
            $this->insertLog('track_reopen', $bookings);
            foreach($bookings as $booking) {
                $this->BookingTrack->updateAll(array(                            
                    'track_status'  => "'closed'"
                ), array(
                    'id'            => $booking['BookingTrack']['id'],
                ));
            }
        }
        
        $this->getBookings();
    }
    
    public function getTimeBookings($bookingId = NULL) {

        $this->loadModel('LiveEdit');

        $conditions = array();
        
        $args = array();
        $args['area']       = $this->request->query['area'];
        $args['date']       = date('Y-m-d',strtotime($this->request->query['date']));

        if(isset($args['area']) && isset($args['date'])){
            $userId     =   $this->currentUser['User']['id'];
            $a          =   trim($args['area']);
            $d          =   trim($args['date']);

            $get_data   =  $this->LiveEdit->getAllDataByDate($d,$a);
            $do_edit    =  $this->LiveEdit->bookingLive($get_data,$d,$a,$userId,'gettimebookings',$bookingId);
            
            if($do_edit['edit'] != 1){
                if($bookingId){
                    die('StopEditing');
                }else{
                    $res = array("do_edit"=>0);
                    die(json_encode($res));
                }
                
            }
        }
        
        $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay($args);
        
        if(isset($this->request->query['area']) && !empty($this->request->query['area'])){
            $conditions['Booking.area_slug'] = $this->request->query['area'];
        }
        if(isset($this->request->query['date']) && !empty($this->request->query['date'])){
            $conditions['Booking.date'] = date('Y/m/d',strtotime($args['date']));
        }
        if(isset($this->request->query['time']) && !empty($this->request->query['time'])){
            $timeSlotArr = $generatedTimeSlots['mapping'][$this->request->query['area']][$this->request->query['time']];
            $conditions['BookingTrack.time_slot'] = $timeSlotArr;
        }
        
        $bookings = Hash::combine($this->BookingTrack->find('all',array(
            'joins'         => array(
                array(
                    'table'         => 'bookings',
                    'alias'         => 'Booking',
                    'type'          => 'INNER',
                    'conditions'    => array(
                        'BookingTrack.booking_id = Booking.id'
                    )
                )
            ),
            'conditions'    => $conditions,
        )),'{n}.BookingTrack.track_id','{n}.BookingTrack','{n}.BookingTrack.booking_id');
        
        if(!is_null($bookingId)) {
            unset($bookings[$bookingId]);
        }
        $this->set('bookingData',$bookings);
        $this->layout = 'ajax';
        $this->render('Ajax/json');
    }
    
    private function getWholeWeek($week,$year) {
        $dateArr = array();
        for($day = 1; $day <= 7; $day++) {
            $gendate = new DateTime();
            $gendate->setISODate((int)$year,(int)$week,$day);
            $dateArr['display'][]     = array(
                'date'  => $gendate->format('d/m'),
                'day'   => $gendate->format('D'),
            );
            $dateArr['database'][]    = $gendate->format('Y-m-d');
        }
        return $dateArr;
    }
    
    public function redirectUrl() {
        $this->Session->write('extraReload', 'true');
        $this->redirect(array(
            'controller'    => 'adminbookings',
            'action'        => 'calendar',
            '?'             => $this->request->query
        ));
    }

    public function updateGetReleasedTrackStatus(){
        $this->autoRender = false;
        
        $id = $this->request->query['id'];

        $detail = $this->BookingTrack->findById($id);

        $status = (int)0;

        if(isset($detail['BookingTrack']) && $detail['BookingTrack']['is_edit'] == 1){
            $status = (int)$detail['BookingTrack']['is_edit'];
        } else {
            $data['id']     = $id;
            $data['is_edit'] = 1;
            $this->BookingTrack->save($data);
        }

        echo json_encode(array('status' => $status));
    }

    public function testing(){

    }

    private function submitCRMdata($params){

        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 900);
        ini_set('default_socket_timeout', 600);

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
            // print_r($params);
            // echo "\n------------\n";
            // print_r($data);
            // die();
        } catch (Exception $e) {
            // echo "\n------------\n";
            $data = $e->getMessage();
        }

        return $data;
    }
}