<?php

class BookingsController extends AppController{
    
    public $uses    = array('Area','Track','Booking','BookingTrack','AreaTimeSlot','EmailQueue',
                            'SmsQueue','TeacherUnavailability','TeacherRegisterTime','DrivingLesson',
                            'City','DrivingType','Price');
    
    public function beforeRender(){
        
        if(($this->notifications['count'][0] > 0) && in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))){               
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
        }
    }
    
    private function breadcrum($case,$booking = array()){
        
        $pageTitle[] = array(
            'name'  => __('Bookings'),
            'url'   => Router::url(array('controller'=>'bookings','action'=>'calendar')),
        );
        
        switch ($case){
            case 'add':
                
                $pageTitle[] = array(
                    'name'  => __('Add Booking'),
                     'url'   => '#',
                );
                
                break;
            
            case 'view':
                
                $pageTitle[] = array(
                    'name'      => __('Booking For ').Inflector::humanize($booking['Booking']['area_slug']),
                    'url'       => '#',
                );
                
                break;
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'      => __('Booking For ').Inflector::humanize($booking['Booking']['area_slug']),
                    'url'       => Router::url(array('controller'=>'bookings','action'=>'view',$booking['Booking']['id'])),
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
                
                break;
           case 'hourlyReport':
               $pageTitle[]    = array(
                    'name'  => __('Hour Report'),
                    'url'   => '#',
                );
                
                break;
    
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
    }
    
    public function index() {
        
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
            'url'       => Router::url(array('controller'=>'bookings','action'=>'index')),
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        $conditions['Booking.user_id']  = $this->currentUser['User']['id'];
        $fields     = array('Booking.id','Booking.date','Booking.area_slug','Booking.user_id',
            'BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.student_id','BookingTrack.id','BookingTrack.status'); 
        
        $args = array(
            'fields'        => $fields,
            'joins'         => $joins,
            'conditions'    => $conditions,
            'limit'         => $this->perPage,
            'order'         => array('Booking.date' => 'DESC'),            
        );
        
        $this->Paginator->settings = $args;
        
        $bookings       = $this->Paginator->paginate('Booking');     
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $nextBooking = array();
        
        foreach ($bookings as $booking) {
         
            if($booking['Booking']['date'] >= date('Y-m-d',time())) {
                $timeSlot = explode('-',$booking['BookingTrack']['time_slot']);  
              
                if($booking['Booking']['date'].' '.$timeSlot[0] >= date('Y-m-d H:i',time())) {
                    $nextBooking = $booking;
                    break;
                }
            }
        }
        
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
        foreach($areas as $area) {
            foreach($area['AreaTimeSlot'] as $timeslots) {
                $areaTimeSlot[$area['Area']['slug']][] = date('H:i',strtotime($timeslots['time_slots']));
            }
        }
        
        $areas          = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
        
        $tacks          = Hash::combine($this->Track->find('all',array(
            'conditions'    => array(
                'Track.status   !=' => 'inactive'
            )
        )),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');
        
        $students = $this->User->find('list',array(
            'conditions'    => array('User.role' => 'student'),
            'fields'        => array('id','name')
        ));
        
        $this->set(array(
            'areas'         => $areas,
            'tacks'         => $tacks,
            'students'      => $students,
            'isEdit'        => FALSE,
            'areaTimeSlot'  => $areaTimeSlot
        ));
        
        if($this->request->is('post')) {
            
            if($this->request->data['Booking']['option'] == 'unavailability') {
                $this->teacherUnavailability();
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
            
            if(!isset($this->request->data['Booking']['on_behalf'])) {
                $this->request->data['Booking']['user_id']  = $this->currentUser['User']['id'];
            } else if(isset($this->request->data['Booking']['on_behalf']) && (!$this->request->data['Booking']['on_behalf'])) {
                $this->request->data['Booking']['user_id']  = $this->currentUser['User']['id'];
            }
            
            $warningsArr = $this->generateWarnings($students);
            if(!empty($warningsArr)) {
                $this->set($warningsArr);
                
                $this->layout = 'ajax';
                $this->render('Ajax/balance_warning');
                return;
            }
            $this->Booking->create();
           
            $invites    = $this->sendInvites($this->request->data);
          
            if($this->Booking->saveAssociated($this->request->data)) {
                $this->Session->delete('warningDisplayed');
                foreach($invites['internal'] as $invite) {
                    
                   /*$this->EmailQueue->addBooking(array(
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
                    
                   $this->SmsQueue->bookingDetails(array(
                       'data'          => array(
                           'User'      => array(
                               'firstname' => $invite['firstname'],
                               'lastname'  => $invite['lastname'],
                               'bookedby'  => $invite['bookedby'],
                               'date'      => $invite['bookingdate'],
                               'area'      => $invite['area']
                            )
                       ),
                       'mobileno'      => $invite['phone_no'],
                       'template'      => $invite['message_type'],
                       'priority'      => 0,
                   ));*/
                }
                
                /*foreach($invites['external'] as $invite) {
                    
                   $this->SmsQueue->externalBookingDetails(array(
                       'data'          => array(
                           'User'      => array(
                               'bookedby'  => $invite['bookedby'],
                               'date'      => $invite['bookingdate'],
                               'area'      => $invite['area'],
                               'timeslot'  => $invite['timeslot'],
                               'track'     => $invite['track']
                            )
                       ),
                       'mobileno'      => $invite['number'],
                       'priority'      => 0,
                   ));
                }*/
                
                $this->set(array(
                    'message'   => __('The Booking is done.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'date'      => $this->request->data['Booking']['date'],
                    'area'      => $this->request->data['Booking']['area_slug'],
                    'iframe'    => isset($this->request->query['iframe'])?$this->request->query['iframe']:''
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
                $this->set(array(
                    'message'   => __('The Booking is not done. Please try some time later.'),
                    'status'    => 'success',
                    'title'     => __('Booking'),
                    'iframe'    => isset($this->request->query['iframe'])?$this->request->query['iframe']:''
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/success');
            }
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
        $amt = $lessonPrice['Price']['price'];
        $arr = array();
        foreach ($studentsArr as $studentId) {
            if(!is_null($studentId)) {
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
        }
        
        return $arr;
    }
    
    private function sendInvites($args,$action = NULL) {
        
        $testBookingCounts  = array();
        
        if($args['Booking']['type'] == 'testing'){
            $testBookingCounts  = $this->Booking->getTestBookingCounts($args);          
        }
        
        $userDetails    = $this->User->find('all');
        $userDetails    = Hash::combine($userDetails,'{n}.User.id','{n}.User');
        $bookingDetails = $args['BookingTrack'];
        $bookingArr     = array();
        
        $tracks         = Hash::combine($this->Track->findAllByAreaId(
            $args['Booking']['area_slug']),'{n}.Track.id','{n}.Track.name'
        );
        
        $external   = array();
    
        foreach($bookingDetails as $booking) {
            if(($booking['student_id'] != -1) && !is_null($booking['student_id'])){
                $bookingArr[$booking['student_id']]['type'] = 'student';
                $bookingArr[$booking['student_id']]['bookingDetails'][] = array(                    
                    'track_id'  => $booking['track_id'],
                    'time_slot' => $booking['time_slot'],                    
                );
            }else if($booking['student_id'] == -1){
                if(!empty($booking['number']) && (!is_null($booking['number']))){
                    $external[$booking['number']]['type']   = 'student';
                    $external[$booking['number']]['bookingDetails'][] = array(                        
                        'track_id'  => $booking['track_id'],
                        'time_slot' => $booking['time_slot'],   
                    );
                }                
            }
            
      
            $bookingArr[$args['Booking']['user_id']]['type']    = 'teacher';
            $bookingArr[$args['Booking']['user_id']]['bookingDetails'][] = array(
                'track_id'  => $booking['track_id'],
                'student'   => (isset($booking['student_id']) && ($booking['student_id'] != '') && ($booking['student_id'] != '-1')) ? 
                    $userDetails[$booking['student_id']]['firstname'].' '.$userDetails[$booking['student_id']]['lastname'] : 
                    __('External User'),
                'time_slot' => $booking['time_slot']
            );
            
            if(!empty($args['Booking']['co_teacher']) && !is_null($args['Booking']['co_teacher'])){
                 $bookingArr[$args['Booking']['co_teacher']]['type']                = 'teacher';
                 $bookingArr[$args['Booking']['co_teacher']]['bookingDetails'][]    = array(
                    'track_id'  => $booking['track_id'],
                    'student'   => (isset($booking['student_id']) && ($booking['student_id'] != '')  && ($booking['student_id'] != '-1')) ? 
                        $userDetails[$booking['student_id']]['firstname'].' '.$userDetails[$booking['student_id']]['lastname'] : 
                        __('External User'),
                    'time_slot' => $booking['time_slot']
                 );
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
            $table['internal'][$userId]['area']         = Inflector::humanize($args['Booking']['area_slug']);
            $table['internal'][$userId]['bookingdate']  = date('d.m.Y',  strtotime($args['Booking']['date']));
           // $table['internal'][$userId]['bookedby']     = ($userBooking['type'] == 'student')?$userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname']:
           //         $userDetails[$args['Booking']['on_behalf']]['firstname'].' '.$userDetails[$args['Booking']['on_behalf']]['lastname'];
            $table['internal'][$userId]['bookedby']     = $userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname'];

            $table['internal'][$userId]['phone_no']     = 4560607550;//$userDetails[$userId]['phone_no'];
            if($userBooking['type'] == 'student'){
                $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'studentDeleteTemplate' : 'studentTemplate';
            }else{
                if(isset($args['Booking']['on_behalf']) && $args['Booking']['on_behalf'] != 0){
                    $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'bookedByDeleteTemplate' : 'bookedByTamplate';
                }else{
                    $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'teacherDeleteTemplate' : 'teacherTamplate';
                }
                
                if(isset($args['Booking']['co_teacher']) && !is_null($args['Booking']['co_teacher'])){
                    $table['internal'][$userId]['message_type'] = (!is_null($action) && $action == 'delete') ? 'bookedByDeleteTemplate' : 'bookedByTamplate';
                }
            }
            
            $table['internal'][$userId]['msg']          = '<table border="0" align="center"><thead>';
            $table['internal'][$userId]['msg']         .= '<tr>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">Track Name</th>';
            if($userBooking['type'] != 'student') {
                $table['internal'][$userId]['msg']     .= '<th style="'.$style1.'">Student Name</th>';
            }
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">Booking Type</th>';
            $table['internal'][$userId]['msg']         .= '<th style="'.$style1.'">Timing</th>';
            $table['internal'][$userId]['msg']         .= '</tr>';
            $table['internal'][$userId]['msg']         .= '</thead><tbody>';
            
            foreach($userBooking['bookingDetails'] as $booking) {
                if($userBooking['type'] != 'student') {
                    
                    $bookingType    = ($args['Booking']['type'] == 'testing')?__('Test Booking'):__('Driving Lesson');                    
                    $table['internal'][$userId]['msg'].= '<tr>
                        <td style="'.$style2.'">'.$tracks[$booking['track_id']].'</td>
                        <td style="'.$style2.'">'.$booking['student'].'</td>                        
                        <td style="'.$style2.'">'.$bookingType.'</td>
                        <td style="'.$style2.'">'.$booking['time_slot'].'</td></tr>';
                } else {
                    $bookingType    = (($args['Booking']['type'] == 'testing') && isset($testBookingCounts[$userId]))?__('Test Booking - ').($testBookingCounts[$userId]+1):__('Driving Lesson');                    
                    $table['internal'][$userId]['msg'].= '<tr>
                        <td style="'.$style2.'">'.$tracks[$booking['track_id']].'</td>
                        <td style="'.$style2.'">'.$bookingType.'</td>
                        <td style="'.$style2.'">'.$booking['time_slot'].'</td></tr>';
                }
                
            }
            $table['internal'][$userId]['msg'] .= '</tbody></table>';          
            
        }
        
        foreach($external as $phone => $details){
            foreach ($details as $detail){
                $table['external'][]    = array(
                    'number'        => 4560607550, //$phone,
                    'track'         => $tracks[$detail['track_id']],
                    'timeslot'      => $detail['time_slot'],
                    'bookedby'      => $userDetails[$args['Booking']['user_id']]['firstname'].' '.$userDetails[$args['Booking']['user_id']]['lastname'],
                    'bookingdate'   => date('d.m.Y',  strtotime($args['Booking']['date'])),
                    'area'          => Inflector::humanize($args['Booking']['area_slug'])
                );
            }
        }
        
        return $table;
    }
    
    public function edit($bookingId = NULL) {
        
        if(is_null($bookingId)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $booking = $this->Booking->findById($bookingId);
        
        if(empty($booking)) {
            return $this->redirect(array('action' => 'index'));
        }
        $deadTime   = strtotime($booking['Booking']['created']) + (30 * 60 * 60);
        
        $this->breadcrum('edit',$booking);
        
        $tracks  = Hash::combine($this->Track->find('all',array(
            'conditions'    => array(
                'Track.status   !=' => 'inactive',
                'Track.area_id'     => $booking['Booking']['area_slug']
            )
        )),'{n}.Track.id','{n}.Track.name');
        
        $bookedTracks       = $booking['BookingTrack'];
        $area               = $this->Area->findBySlug($booking['Booking']['area_slug']);
        $args['area']       = $booking['Booking']['area_slug'];
        $args['date']       = $booking['Booking']['date'];
        
        $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay($args);
        
        $students = $this->User->find('list',array(
            'conditions'    => array('User.role' => 'student'),
            'fields'        => array('id','name')
        ));
        
        $bookedTrackWiseTimeSlot = Hash::combine($bookedTracks,'{n}.time_slot','{n}','{n}.track_id');
        
        foreach($tracks as $id => $track) {
            foreach($generatedTimeSlots['display'][$args['area']] as $timeSlot) {
               // if(isset($bookedTrackWiseTimeSlot[$id][$timeSlot]) && $bookedTrackWiseTimeSlot[$id][$timeSlot]['release_track'] != 1) {
                    $areaTimeSlots[$id][$timeSlot] = $timeSlot;
               // }
            }
        }
        
        foreach($bookedTracks as $bookedTrack) {
            if($bookedTrack['track_status'] != 'closed') {
                $modifiedbookedTracks[$bookedTrack['track_id']]['time_slot'][]   = $bookedTrack['time_slot'];
                $modifiedbookedTracks[$bookedTrack['track_id']]['student_id']    = $bookedTrack['student_id'];
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
        
        $this->set(array(
            'tracks'                => $tracks,
            'modifiedUsers'         => $modifiedUsers,
            'modifiedbookedTracks'  => $modifiedbookedTracks,
            'isEdit'                => TRUE,
            'areaTimeSlots'         => $areaTimeSlots,
            'booking'               => $booking,    
            'area'                  => $area,
            'time'                  => $selected,
            'deadTime'              => $deadTime,
            'isExternal'            => FALSE
        ));
        
        if($this->request->is('ajax') && $this->request->is('post')) {
            
            $this->request->data['Booking']['id'] = $bookingId;
            
            $this->processData('update');
            
            $errorDetails   = $this->Booking->validateData($this->request->data,TRUE);
            
            if($errorDetails['status']) {
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');
                
                return;
            }
            
            if(!$this->request->data['Booking']['on_behalf']) {
                $this->request->data['Booking']['user_id']  = $this->currentUser['User']['id'];
            }
            
            $studentArrOld  = Hash::extract($bookedTracks,'{n}.student_id');
            $studentsArr    = Hash::extract($this->request->data['BookingTrack'],'{n}.student_id');
            $arrDiff        = array_diff($studentArrOld, $studentsArr);
            if(!empty($arrDiff) || count($studentArrOld) != count($studentsArr)) {
                $warningsArr = $this->generateWarnings($students);
                
                if(!empty($warningsArr)) {
                    $this->set($warningsArr);
                    
                    $this->layout = 'ajax';
                    $this->render('Ajax/balance_warning');
                    return;
                }
            }
            
            $this->Booking->updateBooking($this->request->data);
            
            if($this->Booking->saveAssociated($this->request->data)) {
                
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
        
        if($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->render('add');
            return;
        }
    }
    
    private function processData($type  = 'insert') {
        
        $this->request->data['Booking']['date']     = date('Y-m-d',strtotime(str_replace('.','-', $this->request->data['Booking']['date'])));
        foreach($this->request->data['BookingTrack'] as $key => $bookingTrack){
            if((isset($bookingTrack['time_slot']) && !empty($bookingTrack['time_slot'])) && (isset($bookingTrack['track_id']) && !empty($bookingTrack['track_id']))){
                
                $args['area']           = $this->request->data['Booking']['area'];
                $args['date']           = $this->request->data['Booking']['date'];
                
                $timeSlotsForBooking    = $this->Booking->getBookingTimeSlotsForTheDay($args);
                for($i = 0;$i< count($bookingTrack['time_slot']);$i++){
                    
                    $bookingTrack['time_slot'][$i] = $timeSlotsForBooking['mapping'][$args['area']][$bookingTrack['time_slot'][$i]];
                    
                    if(is_array($bookingTrack['time_slot'][$i])){
                        for($j = 0;$j< count($bookingTrack['time_slot'][$i]);$j++){
                            $this->request->data['BookingTrack'][]  = array(
                                'track_id'                          => $bookingTrack['track_id'],
                                'time_slot'                         => $bookingTrack['time_slot'][$i][$j],
                                'student_id'                        => (empty($bookingTrack['student_id']))?NULL:$bookingTrack['student_id'],
                                'number'                            => (isset($bookingTrack['number']))?$bookingTrack['number']:NULL
                            );  
                        }
                    }else{
                        $this->request->data['BookingTrack'][]  = array(
                            'track_id'                          => $bookingTrack['track_id'],
                            'time_slot'                         => $bookingTrack['time_slot'][$i],
                            'student_id'                        => (empty($bookingTrack['student_id']))?NULL:$bookingTrack['student_id'],
                            'number'                            => (isset($bookingTrack['number']))?$bookingTrack['number']:NULL
                        );
                    }                    
                }
                   
            }
            unset($this->request->data['BookingTrack'][$key]);            
        }
    
        $this->request->data['BookingTrack']    = array_values($this->request->data['BookingTrack']);
        
        if(isset($this->request->data['Booking']['isDead']) && $this->request->data['Booking']['isDead'] == TRUE) {
            unset($this->request->data['BookingTrack']);
        }
    }
    
    public function view($id) {
        
        if(empty($id)){
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
        
        if(empty($bookingDetails)){
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
            return $this->redirect(array('aciton' => 'index'));
        }
        $bookings   = $this->Booking->findById($id);
        $invites    = $this->sendInvites($bookings,'delete');
        
        if($this->Booking->delete($id,$cascade = TRUE )) {
            /*foreach($invites['internal'] as $invite) {
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

               $this->SmsQueue->bookingDetails(array(
                   'data'          => array(
                       'User'      => array(
                           'firstname' => $invite['firstname'],    
                           'lastname'  => $invite['lastname'],
                           'bookedby'  => $invite['bookedby'],    
                           'date'      => $invite['bookingdate'], 
                           'area'      => $invite['area']
                        )
                   ),
                   'mobileno'      => $invite['phone_no'],
                   'template'      => $invite['message_type'],
                   'priority'      => 0,
               ));
            }*/
           /*if(isset($invites['external'])) {
               foreach($invites['external'] as $invite){

                   $this->SmsQueue->externalBookingDetails(array(
                       'data'          => array(
                           'User'      => array(
                               'bookedby'  => $invite['bookedby'],    
                               'date'      => $invite['bookingdate'], 
                               'area'      => $invite['area'],
                               'timeslot'  => $invite['timeslot'],
                               'track'     => $invite['track']
                            )
                       ),
                       'mobileno'      => $invite['number'],
                       'priority'      => 0,
                   ));

               }
           }*/
            $this->Session->setFlash(__('The Booking has been deleted'),'alert/success'); 
            return $this->redirect(array(
                    'controller'    => 'bookings',
                    'action'        => 'calendar',
                    '?'             => array(
                        'area'      => $this->request->query['area'],
                        'date'      => $this->request->query['date']                        
                    )
            ));
        }
    }
    
    public function calendar(){
        
        $this->Session->delete('warningDisplayed');
        $this->breadcrum('calendar');
        
        $time               = (isset($this->request->query['date']) && !empty($this->request->query['date']))?strtotime($this->request->query['date']):time();        
        $calendars          = array(
            array(
                'month'     => date('n',$time),
                'year'      => date('Y',$time),
            ),
            array(
                'month'     => date('n',($time+(24*60*60*31*1))),
                'year'      => date('Y',($time+(24*60*60*31*1))),
            ),
            array(
                'month'     => date('n',($time+(24*60*60*31*2))),
                'year'      => date('Y',($time+(24*60*60*31*2))),
            ),           
        );
        
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
        
        foreach($areas as $area){
            if(isset($area['AreaTimeSlot']) && !empty($area['AreaTimeSlot'] )) {
                foreach($area['AreaTimeSlot'] as $timeslots){
                    $areaTimeSlot[$area['Area']['slug']][] = date('H:i',strtotime($timeslots['time_slots']));
                }
            }
        }
        
        $areaList       = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');        
        $notifications  = $this->Tnc->notificationCount($this->currentUser['User']['id']);
        
        $this->set(array(
            'tracks'            => $tracks,
            'areas'             => $areas,
            'calendars'         => $calendars,            
            'areaList'          => $areaList,
            'isEdit'            => FALSE,
            'areaTimeSlot'      => $areaTimeSlot,
            'notifications'     => $notifications
        ));
       
    }
    
    public function getBookings(){
        
        $args['area']       = $this->request->query['area'];
        $args['date']       = date('Y-m-d',strtotime(str_replace('.','-',$this->request->query['date'])));
        $generatedTimeSlots = $this->Booking->getBookingTimeSlotsForTheDay($args);
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

        foreach($bookings   as  $booking){
            foreach($booking['BookingTrack']    as $bookingTrack){                
                $studentBookedTimeSlots[$bookingTrack['track_id']][$bookingTrack['student_id']]['teacher']          = $booking['Booking']['user_id'];    
                $studentBookedTimeSlots[$bookingTrack['track_id']][$bookingTrack['student_id']]['booking_id']       = $bookingTrack['booking_id'];    
                $studentBookedTimeSlots[$bookingTrack['track_id']][$bookingTrack['student_id']]['time_slots'][]     = $bookingTrack['time_slot'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$bookingTrack['student_id']]['status']           = $bookingTrack['status'];
                $studentBookedTimeSlots[$bookingTrack['track_id']][$bookingTrack['student_id']]['other_student']    = (!empty($bookingTrack['other_student'])) ? $bookingTrack['other_student'] : ' ';
                $studentBookedTimeSlots[$bookingTrack['track_id']][$bookingTrack['student_id']]['release_track']    = $bookingTrack['release_track'];
            }
        }
        
        /*
         *  Set Time Slot as per Booked Time slots
         */

        $modifiedStudentArray  = array();
        
        if(!empty($studentBookedTimeSlots)){
            foreach($selectedAreaTracks as $id => $track){                
                if(isset($studentBookedTimeSlots[$id])){
                    foreach($studentBookedTimeSlots[$id] as $student_id => $studentBookedTimeSlot){
                        foreach($generatedTimeSlots['mapping'][$args['area']] as $mapped => $timeSlot){                           
                            foreach($timeSlot as $slot){                              
                              if(in_array($slot,$studentBookedTimeSlots[$id][$student_id]['time_slots'])){           
                                   $modifiedStudentArray[$id][$mapped]['booking_id']    = $studentBookedTimeSlots[$id][$student_id]['booking_id'];
                                   $modifiedStudentArray[$id][$mapped]['student_id']    = $student_id;
                                   $modifiedStudentArray[$id][$mapped]['teacher']       = $studentBookedTimeSlots[$id][$student_id]['teacher'];
                                   $modifiedStudentArray[$id][$mapped]['status']        = $studentBookedTimeSlots[$id][$student_id]['status'];
                                   $modifiedStudentArray[$id][$mapped]['other_student'] = $studentBookedTimeSlots[$id][$student_id]['other_student'];
                                   $modifiedStudentArray[$id][$mapped]['release_track'] = $studentBookedTimeSlots[$id][$student_id]['release_track'];
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
        
         foreach($generatedTimeSlots['display'][$args['area']] as $timeSlot){
            $finalBookingDetails[$i][0] = array(
                'key'   => $timeSlot
            );
            
            foreach($selectedAreaTracks as $trackId => $selectedAreaTrack){ 
                if(isset($modifiedStudentArray[$trackId][$timeSlot]) && !empty($modifiedStudentArray[$trackId][$timeSlot])){                      
                    $finalBookingDetails[$i][]  = array(     
                        'teacher'               => $modifiedStudentArray[$trackId][$timeSlot]['teacher'],
                        'user'                  => $modifiedStudentArray[$trackId][$timeSlot]['student_id'],
                        'id'                    => $modifiedStudentArray[$trackId][$timeSlot]['booking_id'],
                        'status'                => $modifiedStudentArray[$trackId][$timeSlot]['status'],
                        'other_student'         => $modifiedStudentArray[$trackId][$timeSlot]['other_student'],
                        'release_track'         => $modifiedStudentArray[$trackId][$timeSlot]['release_track'],
                        'track_id'              => $trackId,
                    );
                }else{
                    $finalBookingDetails[$i][]  = array(
                        'time_slot'     => $timeSlot,
                        'track_id'      => $trackId
                    );
                } 
            }
            
            $i++;
        }        
        $headers    = $selectedAreaTracks;
        
        array_unshift($headers,__('Tid'));
        
        $this->set(array(
            'date'                  => $this->request->query['date'],
            'finalBookingDetails'   => $finalBookingDetails,
            'headers'               => $headers,
            'area'                  => $this->request->query['area'],
            'userDetails'           => $userDetails,
        ));
        
        $this->layout   = 'ajax';
        $this->render('Ajax/bookings');
    }
    
    public function releaseTrack($type = NULL) {
        
        $conditions = array();
        
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
        
        $bookings = Hash::combine($this->BookingTrack->find('all',array(
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
        )),'{n}.BookingTrack.track_id','{n}.BookingTrack');
                
        $tracks = $this->Track->find('list',array(
            'conditions'    => array(
                'area_id'   => $this->request->query['area']
            )
        ));
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $this->set(array(
            'bookings'  => $bookings,
            'tracks'    => $tracks,
            'users'     => $users,
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
        foreach($this->request->data['Booking'] as $key => $booking) {
            if(empty($booking['booking_id'])) {
                unset($this->request->data['Booking'][$key]);
            }
        }
        $this->request->data['Booking'] = array_values($this->request->data['Booking']);

        $errorDetails = $this->Booking->validateReleasedTrack($this->request->data['Booking'],$studentArr);
        if($errorDetails['status'] == 'error'){
                
            $this->set(array(
                'error_msg'     => $errorDetails['error_msg']
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/error');   

            return;
        }
        
        $result = $this->Booking->updateBookingTrack($this->request->data['Booking']);
        if($result) {
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
        
        $conditions     = array();
        $bookingTracks  = array();
        $users          = array();
        $teacher        = array();
        $tracks         = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track.name','{n}.Track.area_id');
        
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
        
        foreach($areas as $area){
            foreach($area['AreaTimeSlot'] as $timeslots){
                $areaTimeSlot[$area['Area']['slug']][] = $timeslots['time_slots'];
            }
        }
        
        $areaList = Hash::combine($areas,'{n}.Area.slug','{n}.Area.name');
        
        if(isset($this->request->query['area']) && !empty($this->request->query['area'])) {
            $conditions['Booking.area_slug'] = $this->request->query['area'];
        }
        
        if(isset($this->request->query['time_slot']) && !empty($this->request->query['time_slot'])) {
            $conditions['BookingTrack.time_slot'] = $this->request->query['time_slot'];
        }
        
        if(isset($this->request->query['date']) && !empty($this->request->query['date'])) {
            $conditions['Booking.date'] = date('Y-m-d',strtotime(str_replace('/','-',$this->request->query['date'])));
        }   
        
        if(isset($this->request->query['date']) || isset($this->request->query['area']) || isset($this->request->query['time_slot'])) {
            
            $conditions['BookingTrack.release_track'] = 1;
            
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
                'order'         => array('BookingTrack.track_id' => 'ASC','BookingTrack.id' => 'ASC')
            )),'{n}.BookingTrack');
            
            $teacher = Hash::combine($this->Booking->find('all',array(
                'fields' => array('user_id'),
            )),'{n}.Booking.id','{n}.Booking');
            
            $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        }
        $this->set(array(
            'tracks'            => $tracks,
            'areas'             => $areas,
            'areaList'          => $areaList,
            'areaTimeSlot'      => $areaTimeSlot,
            'bookingTracks'     => $bookingTracks,
            'users'             => $users,
            'teacher'           => $teacher,
        ));
    }
    
    public function updateTrackUser() {
        
        $errorDetails   = $this->Booking->validateTrackUser($this->request->data);
        
        if($errorDetails['status']) {
            $this->set(array(
                'error_msg'     => $errorDetails['error_msg']
            ));
            $this->layout = 'ajax';
            $this->render('Ajax/error');
            return;
        }
        
        $this->request->data['BookingTrack']['date_of_birth'] = (!empty($this->request->data['BookingTrack']['date_of_birth'])) ?
                date('Y-m-d',strtotime(str_replace('/','-',$this->request->data['BookingTrack']['date_of_birth']))) : '';
        $this->request->data['BookingTrack']['id'] = $this->request->data['BookingTrack']['booking_track_id'];
        
        $update     = $this->BookingTrack->save($this->request->data);
        $message    = __('Booking Track updated successfully');
        $status     = 'success';
        
        if(!$update) {
            $message    = __('Booking Track could not updated');
            $status     = 'failure';     
        }
        
        $this->set(array(
            'message'   => $message,
            'title'     => __('Update Booking Track'),
            'status'    => $status,
        ));
        
        $this->layout = 'ajax';
        $this->render('Ajax/updateTrackError');
        return;
    }
    
    public function getUnavailaility(){
        
        
        $teacherUnavailabilityTime  = Hash::combine(
                $this->TeacherUnavailability->find('all',array(
                  'conditions'  => array(
                      'TeacherUnavailability.user_id'       => $this->currentUser['User']['id'],
                      'DATE(TeacherUnavailability.from)'    => $this->request->query['date']
                  )  
                )),
        '{n}.TeacherUnavailability.id','{n}.TeacherUnavailability');
        
        $this->set(array(
            'teacherUnavailabilityTime' => $teacherUnavailabilityTime,
            'date'                      => $this->request->query['date']
        ));
        
        $this->layout = 'ajax';
        $this->render('teacher_unavailability_time');  
    }
    
    public function teacherUnavailability(){   
        
        unset($this->request->data['Booking']);
        
        $date   = $this->request->data['TeacherUnavailability']['date'];
        unset($this->request->data['TeacherUnavailability']['date']);
        
        $errorDetails   = $this->Booking->validateTeacherUnavailability($this->request->data);
        
        if($errorDetails['status']){

            $this->set(array(
                'error_msg'     => $errorDetails['error_msg']
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/error');   

            return;

        }
        
        foreach($this->request->data['TeacherUnavailability'] as $key => $unavailability){            
            $this->request->data['TeacherUnavailability'][$key]['from']     = date('Y-m-d H:i:s',strtotime(str_replace('.','-',$unavailability['from'])));;
            $this->request->data['TeacherUnavailability'][$key]['to']       = date('Y-m-d H:i:s',strtotime(str_replace('.','-',$unavailability['to'])));;
            $this->request->data['TeacherUnavailability'][$key]['user_id']  = $this->currentUser['User']['id'];           
            if($this->request->data['TeacherUnavailability'][$key]['from'] == $this->request->data['TeacherUnavailability'][$key]['to']){
                unset($this->request->data['TeacherUnavailability'][$key]);
            }
        }
        
        $this->Booking->deleteUnavailabilityTime($this->currentUser['User']['id'],$date);
       
        if($this->TeacherUnavailability->saveAll($this->request->data['TeacherUnavailability'])){
             $this->set(array(
                'message'   => __('The Unavailability Time is added successfully.'),
                'status'    => 'success',
                'title'     => __('Unavailability Time'),
                'date'      => '',
                'area'      => '',
                'iframe'    => isset($this->request->query['iframe'])?$this->request->query['iframe']:''
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/success');  
            return;
        }else{
            $this->set(array(
                'message'   => __('The Unavailability Time is not added . Please try some time later.'),
                'status'    => 'success',
                'title'     => __('Unavailability Time'),
                'iframe'    => isset($this->request->query['iframe'])?$this->request->query['iframe']:''
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/success');  
            
            return;
        }
        
    }
    
    public function updateStudentDetails($id){
        if($this->request->is(array('ajax','post'))){
            
          $result   =  $this->User->updateAll(array(
                'User.student_medical_profile'  => "'"."yes"."'",                
            ),array(
                'User.id'                       => $id
            ));
          
           if($result){
               $bookingData = array(                      
                   'status' => 'success'
               );
               $this->set('bookingData',$bookingData);
               
               $this->layout    = 'ajax';
               $this->render('Ajax/json');
               return;
           }else{
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
    
    public function status($id){
        
        $bookedTrackDetails = $this->BookingTrack->findById($id);
        
        if((!empty($bookedTrackDetails)) && (($this->request->query['status'] == 'met') || ($this->request->query['status'] == 'not_met'))){
            $result   =  $this->BookingTrack->updateAll(array(
                'BookingTrack.status'           => "'".$this->request->query['status']."'",
                'BookingTrack.track_status'     => "'close'",
                'BookingTrack.release_track'    => 1
            ),array(
                'BookingTrack.id'               => $id
            ));
            
            if($result){
                $this->Session->setFlash(__('Status of booking is udated successfully.'),'alert/success');
                return $this->redirect(array('controller' => 'bookings','action' => 'index'));
            }
        }
        
        $this->Session->setFlash(__('Sorry cannot update Booking status. Something went wrong.'),'alert/error');
        return $this->redirect(array('controller' => 'bookings','action' => 'index'));
    }
    
    public function hourlyReport() {
        
        $this->breadcrum('hourlyReport');
        
        $bookingConditions          = array();
        $unavailabiltyConditions    = array();
        $registerConditions         = array();
        $searchString               = array();
        $drivingLessonConditions    = array();
        
        if(isset($this->request->query['datetime_from']) && !empty($this->request->query['datetime_from']) &&
                isset($this->request->query['datetime_to']) && !empty($this->request->query['datetime_to'])) {
            
            $dateTimeFromArr    = explode(' ',$this->request->query['datetime_from']);
            $dateTimeToArr      = explode(' ',$this->request->query['datetime_to']);
            
            $dateTimeFrom       = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $this->request->query['datetime_from'])));
            $dateTimeTo         = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $this->request->query['datetime_to'])));
            
            $dateFrom           = date('Y-m-d',strtotime(str_replace('/', '-', $dateTimeFromArr[0])));
            $dateTo             = date('Y-m-d',strtotime(str_replace('/', '-', $dateTimeToArr[0])));
            
            $bookingConditions['Booking.date BETWEEN ? AND ?'] = array($dateFrom,$dateTo);
            
            $unavailabiltyConditions['TeacherUnavailability.from >=']    = $dateTimeFrom;
            $unavailabiltyConditions['TeacherUnavailability.to <=']      = $dateTimeTo;
            
            $registerConditions['TeacherRegisterTime.from >=']    = $dateTimeFrom;
            $registerConditions['TeacherRegisterTime.to <=']      = $dateTimeTo;
            
            $drivingLessonConditions['DATE(DrivingLesson.start_time) >=']  = $dateTimeFrom;
            $drivingLessonConditions['DATE(DrivingLesson.start_time) <=']  = $dateTimeTo;
            
            $searchString[] = __(' from ').$this->request->query['datetime_from'].__(' to ').$this->request->query['datetime_to'];
        }
        
        $unavailabiltyConditions['TeacherUnavailability.user_id']   = $this->currentUser['User']['id'];
        
        $teacherAvailability = $this->TeacherUnavailability->find('all',array(
            'conditions'    => $unavailabiltyConditions,
        ));
        
        $registerConditions['TeacherRegisterTime.user_id']   = $this->currentUser['User']['id'];
        
        $teacherRegisterTimes   = $this->TeacherRegisterTime->find('all',array(
            'conditions'    => $registerConditions
        ));

        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'driving',
            'DrivingLesson.status IS NULL'
        );
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'test',
            'DrivingLesson.status'      => 'confirmed'
        );
        
        $teacherDrivingLessons= $this->DrivingLesson->find('all',array(
            'conditions'    => $drivingLessonConditions
        ));

        
        $this->Booking->virtualFields = array(
            'track'     => 'BookingTrack.track_id',
            'timeSlot'  => 'BookingTrack.track_id',
        );
        
        $bookingConditions['Booking.user_id']   = $this->currentUser['User']['id'];
        
        $bookings   = $this->Booking->find('all',array(
            'joins'         => array(
                array(
                    'table'         => 'booking_tracks',
                    'alias'         => 'BookingTrack',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => $bookingConditions,
            'limit'         => $this->perPage,
            'order'         => array(
                'track'         => 'ASC',
                'timeSlot'      => 'ASC',
                'Booking.date'  => 'ASC',
            ),
            'group'         => array('Booking.id')
        ));
        
        $bookings       = $this->processReportData($bookings);
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        $tracks         = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track.name');
        $cities         = $this->City->find('list',array('fields' => array('slug','name')));
        $drivingTypes   = $this->DrivingType->find('list',array('fields' => array('slug','name')));
        
        $this->request->query['searchString']    = $searchString;
        
        $this->set(array(
            'bookings'              => $bookings,
            'teacherAvailability'   => $teacherAvailability,
            'teacherRegisterTimes'  => $teacherRegisterTimes,            
            'tracks'                => $tracks,
            'perPage'               => $this->perPage,
            'searchString'          => $searchString,
            'users'                 => $users,
            'cities'                => $cities,
            'drivingTypes'          => $drivingTypes,
            'teacherDrivingLessons' => $teacherDrivingLessons
        ));
      
    }
    
    private function processReportData($bookings) {
        
        $modifiedBookingsArr = array();
        
        foreach($bookings as $booking) {
            foreach($booking['BookingTrack'] as $bookingTrack) {
                $modifiedBookingsArr[]  = array(
                    'booking_id'    => $booking['Booking']['id'],
                    'area_slug'     => $booking['Booking']['area_slug'],
                    'type'          => $booking['Booking']['type'],
                    'track_id'      => $bookingTrack['track_id'],
                    'date'          => $booking['Booking']['date'],
                    'user_id'       => $booking['Booking']['user_id'],
                    'student_id'    => $bookingTrack['student_id'],
                    'time_slot'     => $bookingTrack['time_slot'],
                    'created'       => $booking['Booking']['created']
                );                
            }
        }
        
        return $modifiedBookingsArr;
    }
   
    public function todaysBooking() {
        
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
        
        $pageTitle[]    = array(
            'name'      => __('Today\'s Bookings'),
            'url'       => Router::url(array('controller'=>'bookings','action'=>'index')),
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
        $fields     = array('Booking.id','Booking.date','Booking.area_slug','Booking.user_id',
            'BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.student_id','BookingTrack.id','BookingTrack.status');

        $bookings       = $this->Booking->find('all',array(
            'fields'        => $fields,
            'joins'         => $joins,
            'conditions'    => array(
                'Booking.date'      => date('Y/m/d'),
                'Booking.user_id'   => $this->currentUser['User']['id'],
            ),
            'order'         => array('Booking.date' => 'DESC')
        ));
        
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');

        $drivingLessons = $this->DrivingLesson->find('all',array(
            'conditions'    => array(
                'Date(DrivingLesson.start_time) = CURDATE()',
                'DrivingLesson.teacher_id'  => $this->currentUser['User']['id'],
            ),
            'order'         => array('DrivingLesson.start_time' => 'DESC'),            
        ));
        
        $tracks = $this->Track->find('list',array('fields'  => array('id','name')));
        
        $this->set(array(
            'drivingLessons'    => $drivingLessons,
            'bookings'          => $bookings,
            'users'             => $users,
            'tracks'            => $tracks,
        ));
    }
}