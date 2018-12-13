<?php

App::import('Component', 'RequestHandler');

App::uses('SmsSender'       , 'Lib/Sms');

class AdminUsersController extends AppController {

    public $uses        = array('User','EmailQueue','Option','SmsQueue','BookingTrack','Product',
                                'Company','SmsTemplate','SmsQueue','Booking','Track',
                                'TeacherRegisterTime','City','DrivingType','DrivingLesson',
                                'Price','Systembooking',
                                'Expence','StudentProduct','Role','UserServices','LatestPayments');

    public $roles       = array();

    public function beforeFilter() {

        parent::beforeFilter();

        $this->Auth->allow(array('login','forgotPassword','signup','resetPassword','getStudentInfo'));

        if(in_array($this->request->params['action'], array('login','signup','forgotPassword','resetPassword')) && $this->Auth->loggedIn() && $this->Auth->user('role') != 'student'){
            return $this->redirect($this->Auth->redirectUrl());
        }

        $this->roles    = Configure::read('roles');
    }


    /**
     *
     */
    public function login() {
        $this->layout   = 'adminlogin';

        if ($this->request->is('post')) {

            if ($this->Auth->login()) {
                $this->notifications   = $this->Tnc->notificationCount($this->Auth->user('id'),'count');
                $this->currentUser     = $this->User->findById($this->Auth->user('id'));

                if(($this->notifications['count'][0] > 0) && (in_array($this->Auth->user('role'),array('internal_teacher','external_teacher')))){
                        $this->iframe   = FALSE;
                        $url   = array(
                            'controller'   => 'tncs',
                            'action'       => 'userTerms'
                        );
                        if(isset($this->request->query['iframe']) && !empty($this->request->query['iframe']) && ($this->request->query['iframe'])){
                             $this->iframe   = TRUE;
                             $url['?']    = array(
                                'iframe'    => $this->iframe
                           );
                        }
                    return $this->redirect($url);
                 } else {
                    return $this->redirect($this->Auth->redirectUrl());
                 }
            } else {
                $this->set(array(
                    'message'   => __('You are not authorized user'),
                    'class'     => 'login-error-message',
                    'iframe'    => isset($this->request->query['iframe'])?$this->request->query['iframe']:''
                ));
            }
        }
    }

    public function logout() {

         if(isset($this->request->query['iframe']) && ($this->request->query['iframe'])){
             $this->Auth->logout();
             return $this->redirect(array('controller' => 'adminusers' , 'action'    => 'login' , '?' => array(
                 'iframe'   => $this->request->query['iframe']
             )));
         }

         return $this->redirect($this->Auth->logout());
    }

    public function autoSuggest($key,$type = NULL){

        $autoSuggestion = array();
        if(!$this->request->is('ajax')){
            $this->redirect(array('action'=>'index'));
        }
        $city_slug = null;

        if(strlen($key)>=2){
            $role = $this->currentUser['User']['role'];
            if($role == 'internal_teacher'){
                $args = array(
                                'fields' => array(
                                            'Company.id','Company.name','Company.nick_name','City.slug','City.city_code','City.name'
                                        ),
                                'joins' => array(
                                                    array(
                                                        'table'         => 'cities',
                                                        'alias'         => 'City',
                                                        'type'          => 'LEFT',
                                                        'conditions'    => array(
                                                                'City.city_code = Company.city_id'
                                                            )
                                                        )
                                            ),
                                'conditions' => array(
                                                'nick_name =' => $this->currentUser['User']['company_id']
                                                ),
                                'limit' => 1,
                            );

                $drivingSchool  = $this->Company->find('first', $args); 

                if(isset($drivingSchool['City']['slug']) && $drivingSchool['City']['slug'] != ''){
                    $city_slug  = $drivingSchool['City']['slug'];
                }

                if($this->currentUser['User']['role'] == "internal_teacher")
                {
                    $city_slug  = $this->currentUser['User']['city'];
                }              
            }

            // echo "CITY: ".$city_slug;
            // exit;

            $currentuserId = $this->currentUser['User']['id'];
            $autoSuggestion  = $this->User->autoSuggest($key,$type,$currentuserId,$role,$city_slug);
        }

        $this->set('autoSuggestion' ,$autoSuggestion);
        $this->layout   = 'ajax';
        $this->render('Ajax/autoSuggetion');

    }

    private function breadcrum($case,$user = array()){

        $pageTitle[] = array(
            'name'  => __('Users'),
            'url'   => Router::url(array('controller'=>'adminusers','action'=>'index')),
        );

        switch ($case){

            case 'edit':

                $pageTitle[] = array(
                    'name'  => $user['User']['firstname'].' '.$user['User']['lastname'],
                    'url'   => Router::url(array('controller'=>'adminusers','action'=>'view',$user['User']['id'])),
                );

                $pageTitle[] = array(
                    'name'  => __('Edit'),
                    'url'   => '#',
                );

                break;

            case 'view':

                $pageTitle[] = array(
                    'name'  => $user['User']['firstname'].' '.$user['User']['lastname'],
                    'url'   => '#',
                );

                break;

            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add User'),
                     'url'   => '#',
                );
                break;

            case 'filterUsers':

                $pageTitle[] = array(
                    'name'  => __('Find User'),
                     'url'   => '#',
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

        if($this->currentUser['User']['role'] == 'external_teacher'){
            return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
        }

        if(isset($this->request->params['named']['role']) && !empty($this->request->params['named']['role'])) {
            $conditions['User.role'] = $this->request->params['named']['role'];
        }

        $conditions['User.role !='] = 'student';

        $roles  = $this->Role->find('list',array('fields' => array('slug','name')));

        $this->SiteInfo->write('pageTitle','Users');

        $this->perPage  = $this->getPerPage('User');

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

        $users      = $this->Paginator->paginate('User');

        $this->set(array(
            'users'     => $users,
            'perPage'   => $this->perPage,
            'roles'     => $roles,
            'role'      => $role
        ));
    }

    public function edit($userId = NULL) {

        $this->perPage  = $this->getPerPage('Booking');
        if((!in_array($this->currentUser['User']['role'],array('admin','internal_teacher'))) && ($this->currentUser['User']['id'] != $userId)){
            return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
        }
        $teacher    = '';
        $roles      = $this->Role->find('list',array('fields' => array('slug','name')));

        if(is_null($userId)){
            if($this->currentUser['User']['role'] == 'external_teacher'){
                return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
            }else{
                return $this->redirect(array('action'   => 'index'));
            }
        }
        $nextBooking = array();
        $bookings = array();
        if(isset($userId) && !empty($userId)) {
            $conditions     = array();
            $conditions['BookingTrack.student_id'] = $userId;
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
            $fields         = array('Booking.id','Booking.date','Booking.area_slug','Booking.user_id',
                                'BookingTrack.track_id','BookingTrack.time_slot','BookingTrack.student_id'); 
            $nextBooking    = Hash::extract($this->Booking->find('first',array(
                'fields'        => $fields,
                'joins'         => $joins,
                'conditions'    => array(
                    'student_id'   => $userId,
                    "CONCAT_WS(  ' ', Booking.date, SUBSTRING_INDEX( BookingTrack.time_slot,  '-', 1 ) ) >= now()",
                ),
                'order'         => array('Booking.date')
            )),'Booking.id');
            $args = array(
                'joins'         => $joins,
                'conditions'    => $conditions,
                'limit'         => $this->perPage,
                'order'         => array('Booking.date' => 'DESC'),
                'group'         => array('Booking.id')
            );
            
            $this->Paginator->settings = $args;
            $bookings       = $this->Paginator->paginate('Booking');

            $conditions     = array();
            $conditions['user_id'] = $userId;

            $UserServices  = $this->UserServices->find('all',array(
                                'conditions'   => $conditions,
                                'order'         => array('posting_date' => 'ASC'),
                                'group'         => array('id')
                            ));

            // echo "<pre>";
            //     print_r($UserServices);
            //     print_r($conditions);
            // echo "</pre>";            
            // exit;            
        }

        $user       = $this->User->findById($userId);
        $users          = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        if($user['User']['role'] == 'student') {
            $teacher    = $this->User->findById($user['User']['teacher_id']);
        }

        if(empty($user)){
            if($this->currentUser['User']['role'] == 'external_teacher'){
                return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
            }else{
                return $this->redirect(array('action'   => 'index'));
            }
        }

        if(($this->currentUser['User']['role'] == 'internal_teacher') && (!in_array($user['User']['role'],array('student','internal_teacher')))){
            return $this->redirect(array('action'   => 'index'));
        }

        $companies  = $this->Company->find('list',array(
            'fields'        => array('nick_name','name'),
            'conditions'   => array(
                'status'    => 'active'
            )
        ));

        $this->view = 'add';
        $city  = $this->City->find('list',array(
            'fields'        => array('slug','name'),
        ));
        
        $args = array(            
            'conditions'    => $conditions,
            'order'         => array('start_time' => 'DESC'),            
            'limit'         => $this->perPage,
        );
        $conditions = array();
        $currentDate    = date('Y-m-d H:i:s',time());
        $conditions['student_id'] = $userId;

        // $conditions[] = "start_time >= '{$currentDate}'";
        
        $conditions[] = "status != 'delete'";
        $conditions[] = "status != 'approved'";
        $conditions[] = "status != 'unapproved'";
        $conditions[] = "status != 'passed'";


        $Systembooking        = $this->Systembooking->find('all',array(
            'conditions'    => $conditions,
            'order'         => array('start_time' => 'ASC')
        ));


        // echo "<pre>";
        // print_r($conditions);
        // print_r($Systembooking);
        // exit;


        $student_number = $user['User']['student_number'];
        $Payments = array();
        if(!empty($student_number)){
            $Payments = $this->LatestPayments->find('all', array(
                                'conditions' => array(
                                    'DebitorNummer' => $student_number
                                 )));
        }
        $products   = $this->Product->find('list');
        $this->breadcrum('edit',$user);
        $this->set(array(
            'teacher'   => $teacher,
            'city'   => $city,
            'user'      => $user['User'],
            'roles'     => $roles,
            'products'     => $products,
            'isEdit'    => TRUE,
            'companies' => $companies,
            'Payments' => $Payments,
            'bookings'      => $bookings,
            'userId'      => $userId,
            'users'         => $users,
            'perPage'       => $this->perPage,
            'nextBooking' => $nextBooking,
            'UserServices' => $UserServices,
            'Systembooking' => $Systembooking,
        ));

        if($this->request->is('post')) {
            if(isset($this->request->data['User']['reset_password']) && empty($this->request->data['User']['reset_password'])){
                unset($this->request->data['User']['password']);
                unset($this->request->data['User']['confirm_password']);
                unset($this->request->data['User']['reset_password']);
            }

            $this->request->data['User']['id'] = $userId;

            if(empty($this->request->data['User']['status'])){
                $this->request->data['User']['status']  = 'active';
            }

            $errorDetails   = $this->User->validateData($this->request->data,TRUE);

            if($errorDetails['status'] == 'error'){
                $this->set(array(
                    'error_msg' => $errorDetails['error_msg'],
                ));

                $this->layout   = 'ajax';
                $this->render('Ajax/error');
                return;
            }

            if($this->User->save($this->request->data)) {
                $student = $this->User->findById($userId);

                $ydelsesData = array();
                $ydelsesData['KundeID'] = '2795cd76-0a62-4f1c-994b-f5bfbdbf24d1';
                $ydelsesData['Elevnummer'] = strval($student['User']['student_number']);
                $ydelsesData['Assistentnummer'] = strval($this->request->data['User']['assistent_id']);
                $crmdata = $this->submitCRMdata($ydelsesData);

                $this->set(array(
                 'message'   => __('The user has been updated'),
                 'status'    => 'success',
                 'title'     => __('User Edit')
                 ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
                $this->set(array(
                    'message'   => __('The user cannot updated'),
                    'status'    => 'success',
                    'title'     => __('User Edit')
                ));
                $this->layout = 'ajax';
                $this->render('Ajax/success');
            }
        }
    }

    public function view($userId = NULL) {
        $this->redirect(array("controller" => "adminusers", 
                      "action" => "edit/".$userId,
                      ),
                $status,
                $exit);

        // $this->edit($userId);
        // if($this->currentUser['User']['role'] == 'external_teacher') {
        //     return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
        // }

        // if(is_null($userId)){
        //     return $this->redirect(array('action'   => 'index'));
        // }

        // $teacher    = '';
        // $user       = $this->User->findById($userId);
        // $roles      = $this->Role->find('list',array('fields' => array('slug','name')));
        // if($user['User']['role'] == 'student') {
        //     $teacher   = $this->User->findById($user['User']['teacher_id']);
        // }

        // if(empty($user)){
        //     return $this->redirect(array('action'   => 'index'));
        // }

        // $this->breadcrum('view',$user);

        // $companies = $this->Company->find('list',array('fields' => array('nick_name','name')));
        // $this->set(array(
        //     'user'      => $user,
        //     'teacher'   => $teacher,
        //     'roles'     => $roles,
        //     'companies' => $companies,
        // ));
    }

    public function forgotPassword() {

        $this->layout = 'adminlogin';

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
                        'activationlink'    => Router::url(array('controller' => 'adminusers' , 'action' => 'resetPassword' , $user['User']['username'],$user['User']['activation_key']),true),
                     )
                ),
                'priority'      => 0,
            ));


            $this->Session->setFlash(__('We have Sent the Password Reset Link on Your Email Please Check your Email'),'alert/success');
            return $this->redirect(array('controller'   => 'adminusers' , 'action'   => 'login'));
               /*$message   = __("We have Sent the Password Reset Link on Your Email Please Check your Email.");

                $this->set(array(
                    'message'  => $message,
                    'class'    => 'successMessage'
                ));*/

            $this->request->data = array();

        }
    }

    public function resetPassword($username,$key) {

        $this->layout = 'adminlogin';


        $user = $this->User->findByUsernameAndActivationKey($username,$key);


        if(empty($user)){
             return $this->redirect(array('controller' => 'adminpages','action' => 'home'));
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

    public function students() {


        $conditions = array();

        if($this->currentUser['User']['role'] == 'external_teacher'){
            return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
        }

        $conditions['User.role'] = 'student';
        $conditions['User.is_completed !='] = "2";

        if(isset($this->request->query['searchTxt']) && !empty($this->request->query['searchTxt'])) {
            $conditions['OR'] = array(
                "User.student_number LIKE"  => "%{$this->request->query['searchTxt']}%",
                "User.phone_no LIKE"        => "%{$this->request->query['searchTxt']}%",
                "User.firstname LIKE"       => "%{$this->request->query['searchTxt']}%",
                "User.lastname LIKE"        => "%{$this->request->query['searchTxt']}%",
                "User.email_id LIKE"        => "%{$this->request->query['searchTxt']}%",
            );
        }

        if($this->currentUser['User']['role'] == 'internal_teacher'){
            $args = array(
                            'fields' => array(
                                        'Company.id','Company.name','Company.nick_name','City.slug','City.city_code','City.name'
                                    ),
                            'joins' => array(
                                                array(
                                                    'table'         => 'cities',
                                                    'alias'         => 'City',
                                                    'type'          => 'LEFT',
                                                    'conditions'    => array(
                                                            'City.city_code = Company.city_id'
                                                        )
                                                    )
                                        ),
                            'conditions' => array(
                                            'nick_name =' => $this->currentUser['User']['company_id']
                                            ),
                            'limit' => 1,
                        );

            $drivingSchool  = $this->Company->find('first', $args); 

            // if(isset($drivingSchool['City']['slug']) && $drivingSchool['City']['slug'] != '')
            // {
            //     $conditions['or'][] = array
            //     (
            //         "User.city"  => $drivingSchool['City']['slug'],
            //     );
            // }

            // if(!empty($this->currentUser['User']['city']))
            // {
            //     $conditions['or'][] = array
            //     (
            //         "User.city"  => $this->currentUser['User']['city'],
            //     );
            // }

            // exit($this->currentUser['User']['city']);

            $conditions['or'][] = array(
                "User.teacher_id"  => $this->currentUser['User']['id'],
            );

            if(!empty($this->currentUser['User']['city']))
            {
                $conditions['or'][] = array(
                    "User.city"  => $this->currentUser['User']['city'],
                );
            }

            // echo "<pre>";
            // print_r($conditions);
            // exit;            
        }

        $this->SiteInfo->write('pageTitle','Students');

        $this->perPage  = $this->getPerPage('User');

        $args = array(
            'limit'         => $this->perPage,
            'order'         => array('User.id' => 'DESC'),
            'conditions'    => $conditions,
        );

        $this->Paginator->settings = $args;

        $users      = $this->Paginator->paginate('User');
        $products   = $this->Product->find('list');

        $this->set(array(
            'users'     => $users,
            'perPage'   => $this->perPage,
            'products'  => $products
        ));
    }

    public function signup() {

        $this->layout   = 'login';

        $this->set(array(
            'roles'     => $this->roles
        ));

        if($this->request->is('post')){

            $errorDetails   = $this->User->validateData($this->request->data);

            if($errorDetails['status'] == 'error'){
                $this->set(array(
                    'message'       => array(
                        'status'    => $errorDetails['status'],
                        'message'   => $errorDetails['error_msg'],
                        'user_id'   => $errorDetails['user_id'],
                    )
                ));

                $this->layout   = 'ajax';
                $this->render('Ajax/message');
                return;
            }

            $this->User->create();

            if($this->User->save($this->request->data)) {
                $this->set(array(
                    'message'       => array(
                        'status'    => 'success',
                        'message'   => __('You have signed up successfully'),
                    ),
                ));

                $this->layout   = 'ajax';
                $this->render('Ajax/message');
            } else {
                $this->set(array(
                    'message'       => array(
                        'status'    => 'error',
                        'message'   => __('You cannot signed up.Please try again.')
                    ),
                ));

                $this->layout   = 'ajax';
                $this->render('Ajax/message');
            }
        }
    }

    public function add($type = NULL,$layout = NULL) {

        if($this->currentUser['User']['role'] == 'external_teacher'){
           return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
        }

        $roles      = $this->Role->find('list',array('fields' => array('slug','name')));
        $companies  = $this->Company->find('list',array(
            'fields'        => array('nick_name','name'),
            'conditions'   => array(
                'status'    => 'active'
            )
        ));
        $city  = $this->City->find('list',array(
            'fields'        => array('slug','name'),
        ));
        
        $this->set(array(
            'roles'     => $roles,
            'city'     => $city,
            'layout'    => (!is_null($layout)) ? 'fancybox' : '',
            'userType'  => (!is_null($type)) ? 'student' : '',
            'isEdit'    => FALSE,
            'companies' => $companies,
        ));

        $this->breadcrum('add');
        if(isset($layout) && !is_null($layout)) {
            $this->layout   = 'fancybox';
        }

        if($this->request->is('post')){
            $errorDetails   = $this->User->validateData($this->request->data,FALSE);

            if($errorDetails['status'] == 'error'){

                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/error');

                return;
            }

            $this->processData();

            $this->User->create();

            if($this->User->save($this->request->data)) {

                $this->EmailQueue->newUser(array(
                    'email'         => $this->request->data['User']['email_id'],
                    'data'          => array(
                        'User'      => array(
                            'username'          => $this->request->data['User']['username'],
                            'password'          => $this->request->data['User']['password'],
                            'emailid'           => $this->request->data['User']['email_id'],
                            'firstname'         => $this->request->data['User']['firstname'],
                            'lastname'          => $this->request->data['User']['lastname'],
                         )
                    ),
                    'priority'      => 0,
                ));

                $this->SmsQueue->newUser(array(
                    'data'          => array(
                        'User'      => array(
                            'firstname' => $this->request->data['User']['firstname'],
                            'lastname'  => $this->request->data['User']['lastname'],
                            'username'  => $this->request->data['User']['username'],
                         )
                    ),
                    'mobileno'      => $this->request->data['User']['phone_no'],
                    'priority'      => 0,
                ));

                $this->set(array(
                    'message'   => __('The user has been submitted'),
                    'status'    => 'success',
                    'title'     => __('User Add'),
                    'layout'    => (isset($layout) && !is_null($layout)) ? 'fancybox' : '',
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
           } else {
                $this->set(array(
                    'message'   => __('The user not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('User Add')
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            }
        }
    }

    public function delete($id) {

        if($this->currentUser['User']['role'] == 'external_teacher'){
           return $this->redirect(array('controller'   => 'adminpages','action'   => 'home'));
        }


        $redirectUrl    = array('action' => 'index');

        if(isset($this->request->query['type']) && !empty($this->request->query['type'])){
            $redirectUrl    = array('controller'    => 'adminusers','action'    => 'students');
        }

        if(empty($id)){
            $this->redirect($redirectUrl);
        }

        if ($this->User->delete($id,$cascade = TRUE)) {
            $this->Session->setFlash(__('The User has been deleted'),'alert/success');
            return $this->redirect($redirectUrl);
        }
    }

    private function processData($case = 'insert'){

        if(isset($this->request->data['User']['date_of_birth']) && !empty($this->request->data['User']['date_of_birth'])){
            $dateOfBirth    = date("Y-m-d",strtotime(str_replace('/', '-', $this->request->data['User']['date_of_birth'])));
            $this->request->data['User']['date_of_birth']   = $dateOfBirth;
        }

        if(isset($this->request->data['User']['role']) && ($this->request->data['User']['role'] == 'student') && $case == 'insert'){
            $this->request->data['User']['student_number']    = $this->getStudentNo();
        }

    }

    private function getStudentNo() {

        $studentNo  = $this->Option->getOption('student_no');

        $studentNo  = (int)$studentNo;

        $this->Option->updateAll(array(
            'Option.value'  => $studentNo + 1
        ),array(
            'Option.key'    => 'student_no'
        ));

        return $studentNo;
    }

    public function getStudentInfo() {

        $modifiedUser = array();
        if(isset($this->request->query['booking_track_id']) && !empty($this->request->query['booking_track_id'])) {
            $bookingTrack   = $this->BookingTrack->findById($this->request->query['booking_track_id']);
            $anotherBooking = Hash::combine($this->BookingTrack->findAllByBookingIdAndTrackIdAndTimeSlot(
                    $bookingTrack['BookingTrack']['booking_id'],
                    $bookingTrack['BookingTrack']['track_id'],
                    $bookingTrack['BookingTrack']['time_slot']
            ),'{n}.BookingTrack.id','{n}.BookingTrack');

            unset($anotherBooking[$this->request->query['booking_track_id']]);
            $anotherBooking = array_values($anotherBooking);

            if(!empty($anotherBooking)) {
                $modifiedUser   = array(
                    'name'              => $bookingTrack['BookingTrack']['name'],
                    'phone_no'          => $bookingTrack['BookingTrack']['phone'],
                    'address'           => $bookingTrack['BookingTrack']['address'],
                    'dob'               => (!empty($bookingTrack['BookingTrack']['date_of_birth'])) ? date('d/m-Y',strtotime($bookingTrack['BookingTrack']['date_of_birth'])) : '',
                    'booking_track_id'  => $bookingTrack['BookingTrack']['id'],
                    'status'            => (!is_null($bookingTrack['BookingTrack']['status'])) ? $bookingTrack['BookingTrack']['status'] : '',
                    'id'                => '',
                    'name2'             => $anotherBooking[0]['name'],
                    'phone_no2'         => $anotherBooking[0]['phone'],
                    'address2'          => $anotherBooking[0]['address'],
                    'zip_code'          => $bookingTrack['BookingTrack']['zip_code'],
                    'city'          => $bookingTrack['BookingTrack']['city'],
                    'dob2'              => (!empty($anotherBooking[0]['date_of_birth'])) ? date('d/m/Y',strtotime($anotherBooking[0]['date_of_birth'])) : '',
                    'new_student'       => 'true',
                    'new_student_id'    => $anotherBooking[0]['student_id']
                );

            } else {
                $modifiedUser   = array(
                    'name'              => $bookingTrack['BookingTrack']['name'],
                    'phone_no'          => $bookingTrack['BookingTrack']['phone'],
                    'address'           => $bookingTrack['BookingTrack']['address'],
                    'zip_code'          => $bookingTrack['BookingTrack']['zip_code'],
                    'city'          => $bookingTrack['BookingTrack']['city'],
                    'dob'               => (!empty($bookingTrack['BookingTrack']['date_of_birth'])) ? date('d/m-Y',strtotime($bookingTrack['BookingTrack']['date_of_birth'])) : '',
                    'booking_track_id'  => $bookingTrack['BookingTrack']['id'],
                    'status'            => (!is_null($bookingTrack['BookingTrack']['status'])) ? $bookingTrack['BookingTrack']['status'] : '',
                    'id'                => '',
                    'name2'             => '',
                    'phone_no2'         => '',
                    'address2'          => '',
                    'dob2'              => '',
                    'new_student'       => 'false',
                    'new_student_id'    => '',
                );
            }
        }

        if(isset($this->request->query['student_id']) && !empty($this->request->query['student_id'])) {
            $user           = $this->User->findById($this->request->query['student_id']);
            $modifiedUser   = array(
                'name'              => (!empty($user)) ? $user['User']['firstname'].' '.$user['User']['lastname'] : ((isset($modifiedUser['name']) && !empty($modifiedUser['name'])) ? $modifiedUser['name'] : ''),
                'phone_no'          => (!empty($user['User']['phone_no'])) ? $user['User']['phone_no'] : ((isset($modifiedUser['phone_no']) && !empty($modifiedUser['phone_no'])) ? $modifiedUser['phone_no'] : '') ,
                'address'           => (!empty($user['User']['address'])) ? $user['User']['address'] : ((isset($modifiedUser['address']) && !empty($modifiedUser['address'])) ? $modifiedUser['address'] : '') ,
                'dob'               => (!empty($user['User']['date_of_birth'])) ? date('d/m/Y',strtotime($user['User']['date_of_birth'])) : ((isset($modifiedUser['dob']) && !empty($modifiedUser['dob'])) ? $modifiedUser['dob'] : '') ,
                'booking_track_id'  => (isset($modifiedUser['booking_track_id'])) ? $modifiedUser['booking_track_id'] : '',
                'status'            => (isset($modifiedUser['status'])) ? $modifiedUser['status'] : '',
                'zip_code'            => (isset($modifiedUser['zip_code'])) ? $modifiedUser['zip_code'] : '',
                'city'            => (isset($modifiedUser['city'])) ? $modifiedUser['city'] : '',
                'id'                => (!empty($user)) ? $user['User']['id'] : '',
                'name2'             => (isset($modifiedUser['name2'])) ? $modifiedUser['name2'] : '',
                'phone_no2'         => (isset($modifiedUser['phone_no2'])) ? $modifiedUser['phone_no2'] : '',
                'address2'          => (isset($modifiedUser['address2'])) ? $modifiedUser['address2'] : '',
                'dob2'              => (isset($modifiedUser['dob2']) && !empty($modifiedUser['dob2'])) ? $modifiedUser['dob2'] : '',
                'new_student'       => (isset($modifiedUser['new_student'])) ? $modifiedUser['new_student'] : 'false',
                'new_student_id'    => (isset($modifiedUser['new_student_id'])) ? $modifiedUser['new_student_id'] : '',
            );
        }

        $this->set('autoSuggestion',$modifiedUser);
        $this->layout = 'ajax';
        $this->render('Ajax/autoSuggetion');
    }

    public function submitCRMdata($params){

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
            $data = $soap->SetAssistentOnDebitor($params);
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

    private function GetdataFromCRM($params,$action){

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
            $data = $soap->$action($params);
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
