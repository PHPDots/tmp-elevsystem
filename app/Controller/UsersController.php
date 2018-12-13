<?php

App::import('Component', 'RequestHandler');

class UsersController extends AppController {  

    public $uses        = array('User','EmailQueue','Role','Booking','BookingTrack','Track','TeacherRegisterTime',
                        'City','DrivingType','DrivingLesson','Price','Expence','StudentProduct','Product','UserServices','LatestPayments','Systembooking');
    public $roles       = array();
    
    public function beforeFilter() {
        $this->Auth->loginRedirect =  array('controller' => 'pages', 'action' => 'home');
        $this->Auth->logoutRedirect =  array('controller' => 'users', 'action' => 'login');

        $this->Auth->loginAction =  array(
                                            'controller' => 'users',
                                            'action' => 'login',
                                            'plugin' => null
                                        );
        parent::beforeFilter();
        
        
        $this->Auth->allow(array('login','forgotPassword','signup','resetPassword'));
         
        if(in_array($this->request->params['action'], array('login','signup','forgotPassword','resetPassword')) && $this->Auth->loggedIn()){
            return $this->redirect($this->Auth->redirectUrl());
        }
        
        $this->roles    = Configure::read('roles');
        
    }
    
    public function login() {
               
        $this->layout   = 'login';
        
        if ($this->request->is('post')) {
            if($this->Auth->login()) {
                
                $this->notifications   = $this->Tnc->notificationCount($this->Auth->user('id'),'count');  
                
                if($this->Auth->user('role') == 'internal_teacher'){
                    if(($this->notifications['count'][0] > 0)){
                        $url   = array(
                            'controller'   => 'tncs', 
                            'action'       => 'userTerms'
                        );
                        return $this->redirect($url);
                    }
                }
                
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->set(array(
                    'message'   => __('You are not authorized user'),
                ));
            }
        }
    }
    
    public function logout() { 
        $this->Session->write("is_login_firsttime",0);
         return $this->redirect($this->Auth->logout());
    }
    
    public function autoSuggest($key,$type = NULL){
        
        $autoSuggestion = array();        
        if(!$this->request->is('ajax')){
            $this->redirect(array('action'=>'index'));
        }
        
        if(strlen($key)>=2){
            $userId = NULL;
            if(isset($this->request->query['get_my_student'])){
               $userId  = $this->currentUser['User']['id'];
           }
           
           $autoSuggestion  = $this->User->autoSuggest($key,$type,$userId);
        }
       
        $this->set('autoSuggestion' ,$autoSuggestion);
        $this->layout   = 'ajax';
        $this->render('Ajax/autoSuggetion');
       
    }
    
    private function breadcrum($case,$user = array()){
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $user['User']['firstname'].' '.$user['User']['lastname'],
                    'url'   => Router::url(array('controller'=>'users','action'=>'view')),
                );
                
                $pageTitle[] = array(
                    'name'  => __('Edit'),
                    'url'   => '#',
                );
                
                break;
            
            case 'view':
                
                $pageTitle[] = array(
                    'name'  => __('Din Profil'),
                    'url'   => '#',
                );
                
                break;
            
            case 'registerTimeList':
                
                $pageTitle[] = array(
                    'name'  => __('Register Time List'),
                    'url'   => '#'
                );
                
                break;            
            
            case 'drivingLessons':
                
                $pageTitle[] = array(
                    'name'  => __('Banetider'),
                    'url'   => '#'
                );
                
                break;         
            
            case 'registerTime':
                
                $pageTitle[] = array(
                    'name'  => __('Register Time List'),
                    'url'   => Router::url(array('controller'   => 'users','action' => 'registerTimeList'))
                );
                
                $pageTitle[] = array(
                    'name'  => __('View'),
                    'url'   => '#'
                );
                
                
                break;
            
            case 'editRegisterTime':
                
                $pageTitle[] = array(
                    'name'  => __('Register Time List'),
                    'url'   => Router::url(array('controller'   => 'users','action' => 'registerTimeList'))
                );
                
                $pageTitle[] = array(
                    'name'  => __('Edit'),
                    'url'   => '#'
                );
                
                
                break;
            
            case 'registerYourTime':
                
                $pageTitle[] = array(
                    'name'  => __('Register Time List'),
                    'url'   => Router::url(array('controller'   => 'users','action' => 'registerTimeList'))
                );
                
                $pageTitle[] = array(
                    'name'  => __('Add'),
                    'url'   => '#'
                );
                break;
            
            case 'studentCharges':
                
                $pageTitle[] = array(
                    'name'  => __('Din økonomi'),
                    'url'   => '#'
                );
             
                break;
            
            case 'products':
                
                $pageTitle[] = array(
                    'name'  => __('Products'),
                    'url'   => '#'
                );
             
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
    }
    
    public function beforeRender(){
        
        if(($this->notifications['count'][0] > 0) && in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))){               
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
        }
    }
    
    public function index($role = NULL,$layout = NULL) {
        
        $conditions = array();
        $roles      = array();
              
        $this->SiteInfo->write('pageTitle','Users');
        
        $this->perPage  = $this->getPerPage('User');
        
        $conditions['User.role']        = 'student';
        $conditions['User.teacher_id']  = $this->currentUser['User']['id'];
        
        $args = array(
            'limit'         => $this->perPage,
            'order'         => array('User.id' => 'DESC'),
            'conditions'    => $conditions,
        );
        
        if(!is_null($role)){
            $args['conditions']   = array(
                'User.role'                 => $role
            );
        }
        
        $this->Paginator->settings = $args;
        
        $users  = $this->Paginator->paginate('User');
        
        $this->set(array(
            'users'     => $users,            
            'perPage'   => $this->perPage,                                 
        ));
    }
    
    public function edit($userId = NULL){
        
        // if($this->currentUser['User']['role']   == 'student'){
        //     return $this->redirect(array('controller'   => 'pages' , 'action'   => 'home'));
        // }
        
        $user       = $this->User->findById($this->currentUser['User']['id']);
        
        if(empty($user)){
            return $this->redirect(array('action'   => 'index'));
        }
        
        $this->view = 'add';
            
        $this->breadcrum('edit',$user);
        
        $this->set(array(
            'user'      => $user['User'],
            'roles'     => $this->roles,
            'isEdit'    => TRUE
        ));
        
        if($this->request->is('post')){
            
            if(empty($this->request->data['User']['password']) && empty($this->request->data['User']['confirm_password'])){
                unset($this->request->data['User']['password']);
                unset($this->request->data['User']['confirm_password']);
                unset($this->request->data['User']['reset_password']);
            }
            
            $this->request->data['User']['id'] = $this->currentUser['User']['id'];
            
            if(empty($this->request->data['User']['status'])){
                $this->request->data['User']['status']  = 'active';
            }
            
            $errorDetails   = $this->User->validateDataForStudent($this->request->data,TRUE,TRUE);

            if($errorDetails['status'] == 'error'){
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
            }
            
            if($this->User->save($this->request->data)){
                
                $this->set(array(
                    'message'   => __('User Updated Successfully.'),
                    'status'    => 'success',
                    'title'     => __('User Update')
               ));

               $this->layout = 'ajax';
               $this->render('Ajax/success');  
               return;
            }else{
                   $this->set(array(
                    'message'   => __('User cannot Updated.Please Try again later.'),
                    'status'    => 'success',
                    'title'     => __('User Update')
               ));

               $this->layout = 'ajax';
               $this->render('Ajax/success');              
               return;
            }
        }
    } 

    public function changepassword($userId = NULL){
        
        $user       = $this->User->findById($this->currentUser['User']['id']);
        
        if(empty($user)){
            return $this->redirect(array('action'   => 'index'));
        }
        
        $this->view = 'changepassword';
            
        $this->breadcrum('edit',$user);
        
        $this->set(array(
            'user'      => $user['User'],
            'roles'     => $this->roles,
            'isEdit'    => TRUE
        ));
        
        if($this->request->is('post')){
            
            if(empty($this->request->data['User']['password']) && empty($this->request->data['User']['confirm_password'])){
                unset($this->request->data['User']['password']);
                unset($this->request->data['User']['confirm_password']);
                unset($this->request->data['User']['reset_password']);
            }
            
            $this->request->data['User']['id'] = $this->currentUser['User']['id'];
            
            if(empty($this->request->data['User']['status'])){
                $this->request->data['User']['status']  = 'active';
            }
            
            $errorDetails   = $this->User->validateDataForChangePassword($this->request->data,TRUE,TRUE);

            if($errorDetails['status'] == 'error'){
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
            }
            
            if($this->User->save($this->request->data)){
                
                $this->set(array(
                    'message'   => __('Brugeren er opdateret'),
                    'status'    => 'success',
                    'title'     => __('Bruger opdateret')
               ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');  
                $this->Session->write("is_login_firsttime",1);
                // $this->redirect(array('action'   => 'logout'));
                return;
            }else{
                   $this->set(array(
                    'message'   => __('User cannot Updated.Please Try again later.'),
                    'status'    => 'success',
                    'title'     => __('User Update')
               ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');     
                $this->Session->write("is_login_firsttime",1);
                // $this->redirect(array('action'   => 'logout'));     
                return;
            }
        }
    }
    
    public function view($userId = NULL){
        
        if(is_null($userId)) {
            $userId = $this->currentUser['User']['id'];
        }
        
        $user       = $this->User->findById($userId);
        $teacher    = $this->User->findById($user['User']['teacher_id']);
        $users      = array();
        
        if(empty($user)){
            return $this->redirect(array('action'   => 'index'));
        }
        
        $this->breadcrum('view',$user);
        
        if($this->currentUser['User']['role'] == 'internal_teacher'){
            $conditions = array();
            $roles      = array();

            $this->breadcrum('view');

            $this->perPage  = $this->getPerPage('User');

            $conditions['User.role']        = 'student';
            $conditions['User.teacher_id']  = $this->currentUser['User']['id'];

            $args = array(
                'limit'         => $this->perPage,
                'order'         => array('User.id' => 'DESC'),
                'conditions'    => $conditions,
            );

            $this->Paginator->settings = $args;

            $users  = $this->Paginator->paginate('User');
        }
        
        
        $this->set(array(
            'users'         => $users, 
            'user'          => $user,
            'teacher'       => $teacher,
            'roles'         => $this->roles,
            'perPage'       => $this->perPage, 
        ));
    }
    
    public function drivingLessons() {
        
        $this->breadcrum('drivingLessons');
        
        $conditions = array();
        
        $fields     = array('Booking.id','Booking.date','Booking.area_slug','Booking.user_id',
            'BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.student_id','BookingTrack.id','BookingTrack.status'); 
        
        switch($this->currentUser['User']['role']){
            case 'student':
                $conditions['BookingTrack.student_id']  = $this->currentUser['User']['id'];
                break;
            
            case 'internal_teacher':
                $conditions['Booking.user_id']          = $this->currentUser['User']['id'];
                $conditions['Booking.date']             = date('Y-m-d',time());
                break;
        }
        
        $bookings = $this->Booking->find('all',array(
            'fields'         => $fields,
            'joins'          => array(
                array(
                    'table'     => 'booking_tracks',
                    'alias'     => 'BookingTrack',
                    'type'      => 'INNER',
                    'conditions' => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => $conditions,
            'order'         => array('Booking.date')
        ));
  
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
        
        $tracks = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track');
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
                
        $this->set(array(
            'bookings'      => $bookings,
            'users'         => $users,
            'tracks'        => $tracks,
            'nextBooking'   => $nextBooking,
        ));
    }
    
    public function registerYourTime(){
        
        $this->breadcrum('registerYourTime');
        
        $cities         = $this->City->find('list',array('fields' => array('slug','name')));
        $drivingTypes   = $this->DrivingType->find('list',array('fields' => array('slug','name')));
        
        $this->set(array(
            'isEdit'        => FALSE,
            'cities'        => $cities,
            'drivingTypes'  => $drivingTypes,
        ));
        
        if($this->request->is('post')){
         
            $errorDetails   = $this->User->validateRegisterTime($this->request->data);
            
            if($errorDetails['status'] == 'error'){
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
            }
               
            $this->processDetails();
            
            if($this->TeacherRegisterTime->save($this->request->data)){
                
                   $this->set(array(
                        'message'   => __('Time Registered Successfully.'),
                        'status'    => 'success',
                        'title'     => __('Time Registration'),
                        'url'       => Router::url(array('controller'   => 'users' , 'action'   => 'registerTimeList'))
                    ));

                   $this->layout = 'ajax';
                   $this->render('Ajax/success');  
            }else{
                   $this->set(array(
                        'message'   => __('Time could not registered. Please try again later.'),
                        'status'    => 'success',
                        'title'     => __('Time Registration'),
                        'url'       => Router::url(array('controller'   => 'users' , 'action'   => 'registerTimeList'))
                   ));
                   
                   $this->layout = 'ajax';
                   $this->render('Ajax/success');                     
            }
            
        }
        
    }
    
    private function processDetails(){
        
        if($this->request->data['TeacherRegisterTime']['type'] == 'theory'){
            $this->request->data['TeacherRegisterTime']['purpose']  = NULL;
        }
        
        if($this->request->data['TeacherRegisterTime']['type'] == 'other'){
            $this->request->data['TeacherRegisterTime']['city'] = NULL;
        }
        
        $this->request->data['TeacherRegisterTime']['from'] = date('Y-m-d',strtotime(str_replace('.','-',$this->request->data['TeacherRegisterTime'][$this->request->data['TeacherRegisterTime']['type']]['from'])));
        
        $this->request->data['TeacherRegisterTime']['user_id']  = $this->currentUser['User']['id'];
        
        unset($this->request->data['TeacherRegisterTime']['theory']);
        unset($this->request->data['TeacherRegisterTime']['other']);        
    }
    
    public function registerTimeList(){
        
        $conditions = array();
  
        $this->breadcrum('registerTimeList');
        
        $this->perPage  = $this->getPerPage('TeacherRegisterTime');
        
        $conditions['TeacherRegisterTime.user_id']  = $this->currentUser['User']['id'];
        
        $args = array(
            'limit'         => $this->perPage,
            'order'         => array('TeacherRegisterTime.id' => 'DESC'),
            'conditions'    => $conditions,
        );
        
        $this->Paginator->settings = $args;
        
        $registerdTimes = $this->Paginator->paginate('TeacherRegisterTime');
        
        $cities         = $this->City->find('list',array('fields' => array('slug','name')));
        $drivingTypes   = $this->DrivingType->find('list',array('fields' => array('slug','name')));
        
        $this->set(array(
            'registerdTimes'    => $registerdTimes,
            'perPage'           => $this->perPage,
            'cities'            => $cities,
            'drivingTypes'      => $drivingTypes,
        ));
    }
    
    public function registerTime($id){
        
        $registertime = $this->TeacherRegisterTime->findById($id);
        
        if(empty($registertime)){
            return $this->redirect(array('controller'   => 'users' , 'action'   => 'registerTimeList'));
        }
        
        $this->breadcrum('registerTime');
        
        $cities         = $this->City->find('list',array('fields' => array('slug','name')));
        $drivingTypes   = $this->DrivingType->find('list',array('fields' => array('slug','name')));
        
        $this->set(array(
            'registertime'  => $registertime,
            'cities'        => $cities,
            'drivingTypes'  => $drivingTypes,
        ));
    }
    
    public function editRegisterTime($id){
        
        $registertime = $this->TeacherRegisterTime->findById($id);
        
        if(empty($registertime)){
            return $this->redirect(array('controller'   => 'users' , 'action'   => 'registerTimeList'));
        }
        
        $this->breadcrum('editRegisterTime');
        
        $this->set(array(
            'registertime'  => $registertime,
            'isEdit'        => TRUE
        ));        
        
        if($this->request->is('post')){
            
            $errorDetails   = $this->User->validateRegisterTime($this->request->data);
   
            if($errorDetails['status'] == 'error'){
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
            }
               
            $this->processDetails();
            
            $this->request->data['TeacherRegisterTime']['id']   = $id;
            
            if($this->TeacherRegisterTime->save($this->request->data)){
                
                   $this->set(array(
                        'message'   => __('Registered Time Updated Successfully.'),
                        'status'    => 'success',
                        'title'     => __('Time Registration'),
                        'url'       => Router::url(array('controller'   => 'users' , 'action'   => 'registerTimeList'))
                    ));

                   $this->layout = 'ajax';
                   $this->render('Ajax/success');  
            }else{
                   $this->set(array(
                        'message'   => __('Registered Time could not update. Please try again later.'),
                        'status'    => 'success',
                        'title'     => __('Time Registration'),
                        'url'       => Router::url(array('controller'   => 'users' , 'action'   => 'registerTimeList'))
                   ));
                   
                   $this->layout = 'ajax';
                   $this->render('Ajax/success');                     
            }
            
        }
        
        $this->view = 'register_your_time';
    }
    
    public function studentCharges(){
        $this->breadcrum('studentCharges');
        
        $conditions                 = array();
        $bookingTracksConditions    = array();
        $drivingLessonConditions    = array();
        
        $this->perPage  = $this->getPerPage('User');
        
        $conditions['User.role']    = 'student';
        $conditions['User.status']  = 'active';
        
        $args = array(
            'order'         => array('User.id' => 'DESC'),
            'conditions'    => $conditions,
        );
        
        $bookingType    = array_keys(Configure::read('bookingType'));        
        
        $students = Hash::combine($this->User->findAllById($this->currentUser['User']['id']),'{n}.User.id','{n}.User');
        $bookingTracksConditions = array(
            'student_id'   => $this->currentUser['User']['id']
        );
        $drivingLessonConditions = array(
             'DrivingLesson.student_id'   => $this->currentUser['User']['id']
        );        
        
        $bookingTracks  = Hash::combine($this->BookingTrack->find('all',array(
            'group'         => array('BookingTrack.id'),
            'conditions'    => $bookingTracksConditions,
        )),'{n}.BookingTrack.id','{n}.BookingTrack');
        
        $bookedStudents = Hash::extract($bookingTracks,'{n}.student_id');

        if($this->currentUser['User']['teacher_id'] != ''){
            $BookingConditions['user_id'] = $this->currentUser['User']['teacher_id'];
        }else{
            $BookingConditions['user_id'] = $this->currentUser['User']['id'];
        }
        $student_number = $this->currentUser['User']['student_number'];
        $city_id  = substr(trim($student_number), 2, -11);
        $city_data = $this->City->find('first',array(
            'conditions'    => array(
                'city_code'   => $city_id
            )
        ));
        $city_data = $city_data['City'];

        $bookings       = Hash::combine($this->Booking->find('all',array(
            'conditions'    => $BookingConditions,
            'group'     => array('Booking.id')
        )),'{n}.Booking.id','{n}.Booking');
        
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'driving',
            'DrivingLesson.status IS NULL'
        );
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'test',
            'DrivingLesson.status'      => 'confirmed'
        );
      
        $drivingLessons = Hash::combine($this->DrivingLesson->find('all',array(
            'conditions'    => $drivingLessonConditions
        )),'{n}.DrivingLesson.id','{n}.DrivingLesson','{n}.DrivingLesson.student_id');
        
        $studentArr = array();
        foreach($bookingTracks as $track) {
            if(!empty($track['student_id'])) {
                if(in_array($track['student_id'],$bookedStudents)) {
                    $studentArr[$track['student_id']][$track['booking_id']] = array(
                        'booking_id'    => $track['booking_id'],
                        'area'          => $bookings[$track['booking_id']]['area_slug'],
                        'booking_date'  => $bookings[$track['booking_id']]['date'],
                    );
                }
            }
        }

        $areaPrices     = Hash::combine($this->Price->find('all',array(
            'conditions'    => array(
                'Price.type'   => 'area'
            )
        )),'{n}.Price.from_date','{n}.Price','{n}.Price.area');
        
        $otherPrices    = Hash::combine($this->Price->find('all',array(
            'conditions'    => array(
                'Price.type !='   => 'area'
            )
        )),'{n}.Price.from_date','{n}.Price','{n}.Price.type');
        
        $studentAmount  = array();
        $priceType      = Configure::read('priceType');
        $areaAmount     = __('N/A');
        
        $studentAmount  = $this->Expence->find('all',array(
            'conditions'        => array(
                'student_id'    => $this->currentUser['User']['id']
            )
        ));
        
        foreach($studentAmount as $key => $value){
            $studentAmount[$key]    = array(
                'name'              => $students[$this->currentUser['User']['id']]['firstname'].' '.$students[$this->currentUser['User']['id']]['lastname'],
                'text'              => $value['Expence']['type'],
                'date'              => $value['Expence']['date'],
                'price'             => $value['Expence']['price'],
                'count'             => $value['Expence']['number']
            );         
        }
       
        foreach($studentArr as $studentId => $bookings) {
            foreach($bookings as $booking){
                if(isset($areaPrices[$booking['area']])) {
                    ksort($areaPrices[$booking['area']]);
                    $dateArray  = array_keys($areaPrices[$booking['area']]); 
                    for($i=0;$i<count($dateArray);$i++){       
                        if(isset($dateArray[$i+1])){
                            if((strtotime(date('Y-m-d',strtotime($booking['booking_date']))) > strtotime($dateArray[$i+1]))){                                                              
                                $areaAmount  = $areaPrices[$booking['area']][$dateArray[$i+1]]['price'];                                 
                            }else if((strtotime(date('Y-m-d',strtotime($booking['booking_date']))) >= strtotime($dateArray[$i]))
                                && (strtotime(date('Y-m-d',strtotime($booking['booking_date']))) < strtotime($dateArray[$i+1]))){                                                               
                                $areaAmount  = $areaPrices[$booking['area']][$dateArray[$i]]['price'];                                  
                            } 
                        }else if(count($dateArray) == 1){
                            $areaAmount  = $areaPrices[$booking['area']][$dateArray[$i]]['price']; 
                        }
                    }
                    
                    $studentAmount[] = array(
                        'booking_id'    => $booking['booking_id'],
                        'booking_date'  => $booking['booking_date'],
                        'user_id'       => $studentId,
                        'name'          => $students[$studentId]['firstname'].' '.$students[$studentId]['lastname'],
                        'area'          => $booking['area'],
                        'category'      => 'Booked Track',
                        'text'          => 'ManÃ¸vrebane, 4 lekt.',
                        'price'         => $areaAmount,
                        'date'          => date('d.m.Y',strtotime($booking['booking_date'])),
                        'count'         => 1
                    );  
                }
            }       
        }
            
         
        $drivingAmount = __('N/A');
        
        foreach($drivingLessons as $studentId => $lessons) {
            foreach($lessons as $lessonId => $lesson){
                if(isset($otherPrices[$lesson['type']])){
                    ksort($otherPrices[$lesson['type']]);
                    $dateArray  = array_keys($otherPrices[$lesson['type']]);
                    for($i=0;$i<count($dateArray);$i=$i+2){                            
                        if(isset($dateArray[$i+1])){
                            if((strtotime(date('Y-m-d',strtotime($lesson['start_time']))) > strtotime($dateArray[$i+1]))){                                                              
                                $drivingAmount  = $otherPrices[$lesson['type']][$dateArray[$i+1]]['price']; 
                            }else if((strtotime(date('Y-m-d',strtotime($lesson['start_time']))) >= strtotime($dateArray[$i]))
                                    && (strtotime(date('Y-m-d',strtotime($lesson['start_time']))) < strtotime($dateArray[$i+1]))){                                                               
                                $drivingAmount  = $otherPrices[$lesson['type']][$dateArray[$i]]['price'];                                                            
                            }
                        }else if(count($dateArray) == 1){
                            $drivingAmount  = $otherPrices[$lesson['type']][$dateArray[$i]]['price'];    
                        }
                        
                        $studentAmount[] = array(
                            'lesson_id'     => $lessonId,
                            'user_id'       => $studentId,
                            'name'          => $students[$studentId]['firstname'].' '.$students[$studentId]['lastname'],
                            'area'          => '',
                            'category'      => 'KÃ¸retime',
                            'text'          => 'KÃ¸retime',
                            'price'         => $drivingAmount,
                            'date'          => date('d.m.Y',strtotime($lesson['start_time'])),
                            'count'         => 1
                        );
                    }
                }
            }
        }

        $conditions     = array();
        $conditions['user_id'] = $this->currentUser['User']['id'];

        $UserServices  = $this->UserServices->find('all',array(
                                'conditions'   => $conditions,
                                'order'         => array('posting_date' => 'ASC'),
                                'group'         => array('id')
                            ));

        foreach ($UserServices as $key => $UserService) {
            $studentAmount[] = array(
                            'lesson_id'     => "",
                            'user_id'       => $UserService['UserServices']['user_id'],
                            'name'          => $this->currentUser['User']['firstname'].' '.$this->currentUser['User']['lastname'],
                            'area'          => '',
                            'category'      => $UserService['UserServices']['description'],
                            'text'          => $UserService['UserServices']['description'],
                            'price'         => number_format($UserService['UserServices']['total_price'],2),
                            'date'          => date('d.m.Y',strtotime($UserService['UserServices']['posting_date'])),
                            'count'         => round($UserService['UserServices']['qty']),
                        );
        }

        $Payments = $this->LatestPayments->find('all', array(
                                'conditions' => array(
                                    'DebitorNummer' => $this->currentUser['User']['student_number']
                                 )));

        $conditions = array();
        $currentDate    = date('Y-m-d H:i:s',time());
        $conditions['student_id'] = $this->currentUser['User']['id'];
        // $conditions[] = "start_time >= '{$currentDate}'";
        $conditions[] = "status != 'delete'";
        $conditions[] = "status != 'approved'";
        $conditions[] = "status != 'unapproved'";

        $Systembooking        = $this->Systembooking->find('all',array(
            'conditions'    => $conditions,
            'order'         => array('start_time' => 'ASC')
        ));
            
        function date_compare($a, $b){
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);
            return $t1 - $t2;
        }    
        usort($studentAmount, 'date_compare');

        $this->set(array(
            'studentAmount'     => $studentAmount,
            'Payments'     => $Payments,
            'Systembooking'     => $Systembooking,
            'UserServices'     => $UserServices,
            'city_data'     => $city_data,
            'perPage'           => $this->perPage
        ));
    }
    
    public function forgotPassword() {
        
        $this->layout = 'login';
        
        if(!$this->request->is('post')){
            return;
        }
        
        
        $data = FALSE;
        
        if(isset($this->request->data['User']['verification_data']) && !empty($this->request->data['User']['verification_data'])){
            $data = $this->User->generateActivationKey($this->request->data['User']['verification_data']);           
        }
       
        if($data == FALSE){             
            
            $this->Session->setFlash(__('Entered Username or Email is not Correct. Please Enter Valid Username or Email.'),'alert/error');            
            
        }else{                        
            
            $user   = $this->User->findByUsername($data['username']);   
           
           $this->EmailQueue->forgotPasswordEmail(array(
               'email'         => $user['User']['email_id'],                
               'data'          => array(
                   'User'      => array(
                       'firstname'         => $user['User']['firstname'],
                       'lastname'          => $user['User']['lastname'],
                       'username'          => $user['User']['username'],
                       'activationlink'    => Router::url(array('controller' => 'users' , 'action' => 'resetPassword' , $user['User']['username'],$user['User']['activation_key']),true),
                    )
               ),
               'priority'      => 0,
           ));
            
            
            $this->Session->setFlash(__('We have Sent the Password Reset Link on Your Email Please Check your Email'),'alert/success');
            return $this->redirect(array('controller'   => 'users' , 'action'   => 'login'));
               /*$message   = __("We have Sent the Password Reset Link on Your Email Please Check your Email.");
                
                $this->set(array(
                    'message'  => $message,
                    'class'    => 'successMessage'
                ));*/
                
            $this->request->data = array();
            
        }
    }
    
    public function resetPassword($username,$key) {
        
        $this->layout = 'login';
        
        
        $user = $this->User->findByUsernameAndActivationKey($username,$key);
       
      
        if(empty($user)){           
             return $this->redirect(array('controller' => 'pages','action' => 'home'));      
             $this->Session->setFlash(__('Your are not authorized user. Please, try again.'),'alert/error');
        }
        
            
        if(!$this->request->is('post')){
            return;
        }

        if(!empty($this->request->data['User']['password']) && !empty($this->request->data['User']['confirm_password']) && ($this->request->data['User']['password'] == $this->request->data['User']['confirm_password'])){  
            unset($this->request->data['User']['confirm_password']);
            
            $this->User->id = $user['User']['id'];
            $this->User->saveField('password',$this->request->data['User']['password']);
            
            $this->Session->setFlash(__('Your Password is reset'),'alert/success');
            
            return $this->redirect(array('action' => 'login'));               
            
        }else{
            $this->Session->setFlash(__('Please Enter valid Password and Confirm Password.'),'alert/error');
        }

        
    }
    
    public function products(){
        
        if(!isset($this->currentUser)){
            return $this->redirect(array('controller'   => 'pages' , 'action'   => 'home'));
        }
        
        if((isset($this->currentUser['User']['role'])) && ($this->currentUser['User']['role'] != 'student')){
            return $this->redirect(array('controller'   => 'pages' , 'action'   => 'home'));
        }
        
        $this->breadcrum('products');
        
        $conditions['StudentProduct.student_id']    = $this->currentUser['User']['id'];
        $joins[]    = array(
                'table'         => 'student_products',
                'alias'         => 'StudentProduct',
                'type'          => 'INNER',
                'conditions'    => array(
                    'Product.id = StudentProduct.product_id'
                )                
        );
        
        $products           = $this->Product->find('all',array(
            'conditions'    => $conditions,
            'joins'         => $joins
        ));        
        
        $this->set(array(
            'products'          => $products
        ));
        
    }
}
