<?php

class DrivingLessonsController extends AppController{
    
    public $uses    = array('DrivingLesson','User','EmailQueue','Price','Systembooking','Booking');

    public function beforeRender() {
        $url = str_replace("/elev-admin/", "", $this->here);
        
        if((strpos($url, 'admin') != '' || $this->Auth->user('role') != 'student') && $url != ''){
            if(!$this->request->is('ajax')){
                $this->layout = 'admin';
            }
        }else{
            if(!$this->request->is('ajax')){
                $this->layout = 'default';
            }
        }
    }
    
    public function index(){
        

        $this->Session->delete('warningDisplayed');
        $this->perPage  = $this->getPerPage('DrivingLesson');
        
        $conditions     = array();
        $joins          = array();
        $nextBooking    = array();
        $nextBookingCon = array();
        $pageTitle[]    = array(
            'name'      => __('Driving Lessons'),
            'url'       => Router::url(array('controller'=>'drivingLessons','action'=>'index')),
        );
        
        $currentDate    = date('Y-m-d H:i:s',time());
      
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
        if($this->currentUser['User']['role'] == 'student'){
            $conditions['student_id']  = $this->currentUser['User']['id'];
            $nextBookingCon['student_id']  = $this->currentUser['User']['id'];
        }else{
            $conditions['user_id']  = $this->currentUser['User']['id'];
            $nextBookingCon['user_id']  = $this->currentUser['User']['id'];
        }
        
        if(isset($this->request->query['today']) && ($this->request->query['today'])){
            $conditions[]  = 'Date(start_time) = CURDATE()'; 
        }

        $fields = array('CONCAT_WS(  " ", Booking.date, SUBSTRING_INDEX( BookingTrack.time_slot,  "-", 1 ) ) as book_date'); 
        $currentDate    = date('Y-m-d H:i',time());
        $bookings = $this->Booking->find('first',array(
            'fields'         => $fields,
            'joins'         => array(
                array(
                    'table'     => 'booking_tracks',
                    'alias'     => 'BookingTrack',
                    'type'      => 'INNER',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => array(
                'student_id'   => $this->currentUser['User']['id'],
                "CONCAT_WS(  ' ', Booking.date, SUBSTRING_INDEX( BookingTrack.time_slot,  '-', 1 ) ) >= '".$currentDate ."'",
            ),
            'order'         => array('Booking.date')
        ));
        
        $nextBookingCon['status']  = 'pending'; 
        $nextBookingCon[] = "start_time >= '{$currentDate}'";
        $nextBooking        = $this->Systembooking->find('first',array(
            'conditions'    => $nextBookingCon,
            'order'         => array('start_time' => 'ASC')
        ));

        if(isset($bookings[0]['book_date']) && strtotime($bookings[0]['book_date']) < strtotime($nextBooking['Systembooking']['start_time'])){
            $nextBooking = $bookings[0]['book_date'];
        }else{
            $nextBooking = $nextBooking['Systembooking']['start_time'];
        }

        $args = array(            
            'conditions'    => $conditions,
            'order'         => array('start_time' => 'ASC'),            
            'limit'         => $this->perPage,
        );
        
        $this->Paginator->settings = $args;
       
        $bookings       = $this->Paginator->paginate('Systembooking');  
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $this->set(array(
            'bookings'      => $bookings,
            'perPage'       => $this->perPage,
            'users'         => $users,   
            'nextBooking'   => $nextBooking
        ));
    }
    
    public function add() {
        $url = str_replace("/elev-admin/", "", $this->here);

        if((strpos($url, 'admin') != '' || $this->Auth->user('role') != 'student') && $url != ''){
            $this->layout = 'admin';
        }
        
       $pageTitle[]    = array(
            'name'  => __('Driving Lesson'),
            'url'   => Router::url(array('controller'   => 'drivingLessons','action'    => 'index')),
       );

       $pageTitle[]    = array(
            'name'  => __('Add'),
            'url'   => '#',
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
       
        $this->set(array(
            'isEdit'    => FALSE
        ));
        
        if($this->request->is('post')){
            $student        = $this->User->findById($this->request->data['DrivingLesson']['student_id']);            
            $countDetails   = $this->DrivingLesson->countNumberOfBookings($this->request->data);
            
            $this->processData();
            
            $errorDetails   = $this->DrivingLesson->validateData($this->request->data,FALSE);
            
            if($errorDetails['status']) {
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');
                
                return;
            }
            
            if(isset($countDetails[$this->request->data['DrivingLesson']['student_id']]) && $countDetails[$this->request->data['DrivingLesson']['student_id']] >= 9) {
                $this->set(array(
                    'title'     => __('Student Profile'),
                    'student'   => $student['User']['firstname'].' '.$student['User']['lastname'],
                    'id'        => $this->request->data['DrivingLesson']['student_id']//$key
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/confirmation');   
                
                return;
            }
            
            $this->request->data['DrivingLesson']['teacher_id'] = $this->currentUser['User']['id'];
            $this->request->data['DrivingLesson']['start_time'] = date('Y-m-d H:i:s',  strtotime(str_replace('.','-',$this->request->data['DrivingLesson']['start_time']))); 
            
            $drivingLessonDetails   = $this->request->data;            
            $types                  = Configure::read('bookingType'); 
            $lessonTime             = Configure::read('lessonTime');
            $status                 = Configure::read('lessonStatus');
            
            $warningArr = $this->generateWarnings();
            
            if(!empty($warningArr)) {
                $this->set($warningArr);
                
                $this->layout = 'ajax';
                $this->render('Ajax/balance_warning');
                return;
            }
            
            if($this->DrivingLesson->saveAll($this->request->data)) {
                $this->Session->delete('warningDisplayed');
                
                $this->EmailQueue->newStudentDrivingLesson(array(
                    'email'         => $student['User']['email_id'],                               
                    'data'          => array(
                        'DrivingLesson' => array(
                            'type'          => $types[$drivingLessonDetails['DrivingLesson']['type']],
                            'starttime'     => date('d.m.Y H:i',strtotime($drivingLessonDetails['DrivingLesson']['start_time'])),
                            'lessontime'    => $lessonTime[$drivingLessonDetails['DrivingLesson']['lesson_time']],                            
                            'teacher'       => $this->currentUser['User']['firstname'].' '.$this->currentUser['User']['lastname'],                                                                                
                            'status'        => (isset($drivingLessonDetails['DrivingLesson']['status']))?$status[$drivingLessonDetails['DrivingLesson']['status']]:__('Not Confirmed yet')
                         )
                    ),
                    'priority'      => 0,
                ));
                
                $this->EmailQueue->newTeacherDrivingLesson(array(
                    'email'         => $this->currentUser['User']['email_id'],                               
                    'data'          => array(
                        'DrivingLesson' => array(
                            'type'          => $types[$drivingLessonDetails['DrivingLesson']['type']],
                            'starttime'     => date('d.m.Y H:i',strtotime($drivingLessonDetails['DrivingLesson']['start_time'])),
                            'lessontime'    => $lessonTime[$drivingLessonDetails['DrivingLesson']['lesson_time']],                            
                            'student'       => $student['User']['firstname'].' '.$student['User']['lastname'], 
                            'status'        => (isset($drivingLessonDetails['DrivingLesson']['status']))?$status[$drivingLessonDetails['DrivingLesson']['status']]:__('Not Confirmed yet')
                         )
                    ),
                    'priority'      => 0,
                ));
                
                $this->set(array(
                    'message'   => __('Driving Lesson Added Successfully.'),
                    'status'    => 'success',
                    'title'     => __('Add Driving Lesson'),
                    'url'       => Router::url(array('controller'   => 'bookings' , 'action'   => 'index'))
                ));

               $this->layout = 'ajax';
               $this->render('Ajax/success');  
            } else {
                $this->set(array(
                    'message'   => __('Driving Lesson Couldnot Added. Please try again later.'),
                    'status'    => 'success',
                    'title'     => __('Add Driving Lesson'),
                    'url'       => Router::url(array('controller'   => 'bookings' , 'action'   => 'index'))
               ));

               $this->layout = 'ajax';
               $this->render('Ajax/success');         
            }
        }
    }
    
    public function edit($id){
        
        $pageTitle[]    = array(
            'name'  => __('Driving Lesson'),
            'url'   => Router::url(array('controller'   => 'drivingLessons','action'    => 'index')),
        );

       $pageTitle[]    = array(
            'name'  => __('Edit'),
            'url'   => '#',
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
        $drivingLesson  = $this->DrivingLesson->findById($id);
        
        $this->set(array(
            'isEdit'        => TRUE,
            'drivingLesson' => $drivingLesson,
            'user'          => $this->User->findById($drivingLesson['DrivingLesson']['student_id'])
        ));
        
        $this->render('add');
        
        if($this->request->is('post')) {
            $this->processData();
            $errorDetails   = $this->DrivingLesson->validateData($this->request->data,TRUE);
            
            if($errorDetails['status']){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            if($this->request->data['DrivingLesson'] == '') {
                $this->request->data['DrivingLesson']['teacher_id'] = $this->currentUser['User']['id'];
            }
            
            $this->request->data['DrivingLesson']['start_time'] = date('Y-m-d H:i:s',  strtotime(str_replace('.','-',$this->request->data['DrivingLesson']['start_time']))); 
            $this->request->data['DrivingLesson']['id']         = $id;
            
            $drivingLessonDetails   = $this->request->data;
            $student                = $this->User->findById($this->request->data['DrivingLesson']['student_id']);
            $types                  = Configure::read('bookingType'); 
            $lessonTime             = Configure::read('lessonTime');
            $status                 = Configure::read('lessonStatus');
            
            if($this->request->data['DrivingLesson']['student_id'] != $drivingLesson['DrivingLesson']['student_id']) {
                $warningArr = $this->generateWarnings();

                if(!empty($warningArr)) {
                    $this->set($warningArr);

                    $this->layout = 'ajax';
                    $this->render('Ajax/balance_warning');
                    return;
                }
            }
            
            if($this->DrivingLesson->saveAll($this->request->data)){
                
                $this->EmailQueue->newUpdateStudentDrivingLesson(array(
                    'email'         => $student['User']['email_id'],                               
                    'data'          => array(
                        'DrivingLesson' => array(
                            'type'          => $types[$drivingLessonDetails['DrivingLesson']['type']],
                            'starttime'     => date('d.m.Y H:i',strtotime($drivingLessonDetails['DrivingLesson']['start_time'])),
                            'lessontime'    => $lessonTime[$drivingLessonDetails['DrivingLesson']['lesson_time']],                            
                            'teacher'       => $this->currentUser['User']['firstname'].' '.$this->currentUser['User']['lastname'],                                                                                 
                            'status'        => (isset($drivingLessonDetails['DrivingLesson']['status']))?$status[$drivingLessonDetails['DrivingLesson']['status']]:__('Not Confirmed yet')
                         )
                    ),
                    'priority'      => 0,
                ));
                
                $this->EmailQueue->newUpdateTeacherDrivingLesson(array(
                    'email'         => $this->currentUser['User']['email_id'],                               
                    'data'          => array(
                        'DrivingLesson' => array(
                            'type'          => $types[$drivingLessonDetails['DrivingLesson']['type']],
                            'starttime'     => date('d.m.Y H:i',strtotime($drivingLessonDetails['DrivingLesson']['start_time'])),
                            'lessontime'    => $lessonTime[$drivingLessonDetails['DrivingLesson']['lesson_time']],                            
                            'student'       => $student['User']['firstname'].' '.$student['User']['lastname'],                                                                                 
                            'status'        => (isset($drivingLessonDetails['DrivingLesson']['status']))?$status[$drivingLessonDetails['DrivingLesson']['status']]:__('Not Confirmed yet')
                         )
                    ),
                    'priority'      => 0,
                ));
                
                $this->set(array(
                    'message'   => __('Driving Lesson Updated Successfully.'),
                    'status'    => 'success',
                    'title'     => __('Update Driving Lesson'),
                    'url'       => Router::url(array('controller'   => 'bookings' , 'action'   => 'index'))
                ));

               $this->layout = 'ajax';
               $this->render('Ajax/success');  
            }else{
                $this->set(array(
                    'message'   => __('Driving Lesson Couldnot Updated. Please try again later.'),
                    'status'    => 'success',
                    'title'     => __('Update Driving Lesson'),
                    'url'       => Router::url(array('controller'   => 'bookings' , 'action'   => 'index'))
               ));

               $this->layout = 'ajax';
               $this->render('Ajax/success');         
            }
        }
    }
    
    public function generateWarnings() {
        $lessonPrice    = $this->Price->find('first',array(
            'fields'     => array('price'),
            'conditions' => array(
                'type'  => 'driving',
            ),
            'order' => array(
                'Price.from_date' => 'DESC'
            )
        ));
        $arr = array();
        $amt = (isset($this->request->data['DrivingLesson']['lesson_time']) && $this->request->data['DrivingLesson']['lesson_time'] == 60) ? $lessonPrice['Price']['price'] : $lessonPrice['Price']['price']*2;
        $studentBalance = $this->User->getBalance($this->request->data['DrivingLesson']['student_id'],$amt);
        if(!$this->Session->check('warningDisplayed') || $this->Session->read('warningDisplayed') == FALSE) {
            if($studentBalance['originalBalance'] <= -1800) {
                $this->Session->write('warningDisplayed',TRUE);
                
                $arr = array(
                    'title'         => __('Minimum Balance Warning'),
                    'message'       => __('Sorry! Booking could not be done, due to insufficient balance. Student\'s balance is').' '.$studentBalance['originalBalance'].'kr',
                    'cancelBooking' => TRUE,
                );
            } else if($studentBalance['originalBalance'] > -1800 && $studentBalance['originalBalance'] < 0) {
                $this->Session->write('warningDisplayed',TRUE);
                $arr = array(
                    'title'     => __('Minimum Balance Warning'),
                    'message'   => __('Student\'s balance is').' '.$studentBalance['originalBalance'].'kr '.__('and after this booking would be').' '.$studentBalance['computedBalance'].'kr '.__('. If you have seen the payment of proof click on "Seen proof of payment/receipt of cash" button else cancel this booking'),
                    'button1'   => __('Seen proof of payment/receipt of cash'),
                    'button2'   => __('Cancel Booking')
                );
            } else if($studentBalance['originalBalance'] >= 0 && $studentBalance['computedBalance'] < 0) {
                $this->Session->write('warningDisplayed',TRUE);
                $arr = array(
                    'title'     => __('Minimum Balance Warning'),
                    'message'   => __('Student\'s balance would be ').$studentBalance['computedBalance'].'kr '.__('after this booking.'),
                );
            }
        }
        return $arr;
    }
    
    private function processData() {
        
        if($this->request->data['DrivingLesson']['type'] == 'driving') {
            $this->request->data['DrivingLesson']['status'] = NULL;
        }
        if(!isset($this->request->data['DrivingLesson']['approved'])) {
            $this->request->data['DrivingLesson']['approved'] = 'no';
        }
        
        $this->request->data['DrivingLesson']['start_time'] = ($this->request->data['DrivingLesson']['start_time'] != '') ? ($this->request->data['DrivingLesson']['start_time'].' '.
            $this->request->data['DrivingLesson']['start_time_hour'].':'.$this->request->data['DrivingLesson']['start_time_min']) : '';
        unset($this->request->data['DrivingLesson']['start_time_hour']);
        unset($this->request->data['DrivingLesson']['start_time_min']);
    }
    
    public function delete($id){
        
        $drivingLessonDetails   = $this->DrivingLesson->findById($id);
        $student                = $this->User->findById($drivingLessonDetails['DrivingLesson']['student_id']);
        $types                  = Configure::read('bookingType'); 
        $lessonTime             = Configure::read('lessonTime');
        $status                 = Configure::read('lessonStatus');
            
        if($this->DrivingLesson->delete($id,$cascade = TRUE )) {
            $this->EmailQueue->newDeleteStudentDrivingLesson(array(
                'email'         => $student['User']['email_id'],                               
                'data'          => array(
                    'DrivingLesson' => array(
                        'type'          => $types[$drivingLessonDetails['DrivingLesson']['type']],
                        'starttime'     => date('d.m.Y H:i',strtotime($drivingLessonDetails['DrivingLesson']['start_time'])),
                        'lessontime'    => $lessonTime[$drivingLessonDetails['DrivingLesson']['lesson_time']],                            
                        'teacher'       => $this->currentUser['User']['firstname'].' '.$this->currentUser['User']['lastname'],                                                                                 
                        'status'        => (isset($drivingLessonDetails['DrivingLesson']['status']))?$status[$drivingLessonDetails['DrivingLesson']['status']]:__('Not Confirmed yet')
                     )
                ),
                'priority'      => 0,
            ));

            $this->EmailQueue->newDeleteTeacherDrivingLesson(array(
                'email'         => $this->currentUser['User']['email_id'],                               
                'data'          => array(
                    'DrivingLesson' => array(
                        'type'          => $types[$drivingLessonDetails['DrivingLesson']['type']],
                        'starttime'     => date('d.m.Y H:i',strtotime($drivingLessonDetails['DrivingLesson']['start_time'])),
                        'lessontime'    => $lessonTime[$drivingLessonDetails['DrivingLesson']['lesson_time']],                            
                        'student'       => $student['User']['firstname'].' '.$student['User']['lastname'],                                                                                 
                        'status'        => (isset($drivingLessonDetails['DrivingLesson']['status']))?$status[$drivingLessonDetails['DrivingLesson']['status']]:__('Not Confirmed yet')
                     )
                ),
                'priority'      => 0,
            ));
            
            $this->Session->setFlash(__('The Booking has been deleted'),'alert/success'); 
            return $this->redirect(array(
                    'controller'    => 'drivingLessons',
                    'action'        => 'index'                   
            ));
        }
        
    }
   
    public function view($id = NULL) {
        $pageTitle[]    = array(
            'name'  => __('Driving Lesson'),
            'url'   => Router::url(array('controller'   => 'drivingLessons','action'    => 'index')),
        );

        $pageTitle[]    = array(
            'name'  => __('View'),
            'url'   => '#',
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
        $drivingLesson  = $this->DrivingLesson->findById($id);
        
        $users = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        $this->set(array(
            'drivingLesson' => $drivingLesson,
            'users'         => $users,
        ));
    }
    
    public function confirm($id = NULL){
        
        if(is_null($id)){
            return $this->redirect(array('controller'    => 'bookings','action' => 'todaysBooking'));
        }
        
        $result = $this->DrivingLesson->updateAll(array(
            'DrivingLesson.status'  => "'"."confirmed"."'"
        ),array(
            'DrivingLesson.id'      => $id,
        ));
        
        
        return $this->redirect(array('controller'    => 'bookings','action' => 'todaysBooking'));
        
    }
}
