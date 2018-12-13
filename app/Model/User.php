<?php

App::uses('AppModel'        , 'Model');
App::uses('AuthComponent'   , 'Controller/Component');

class User extends AppModel {
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
    public $validate_length = array();
    public $virtualFields = array(
        'name' => 'CONCAT(User.firstname, " ", User.lastname)'
    );
    
    public function beforeSave($options = array()) {
        
        if (isset($this->data[$this->alias]['password'])) {            
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        
        parent::beforeSave($options);
    }
    
    function usernameExist($username){
        $result = $this->findByUsername($username);
        return (count($result)==0)?FALSE:$result;
    }
    
    function emailExist($email){
        $result = $this->findByEmailId($email);
        return (count($result)== 0)?FALSE:$result;
    }
    
    function generatePassword($length = 8){ 
        // inicializa variables 
        $password = ""; 
        $i = 0; 
        $possible = "0123456789abcdfghjkmnpqrstvwxyz";  
    
        // agrega random 
        while ($i < $length){ 
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1); 
    
            if (!strstr($password, $char)) {  
                $password .= $char; 
                $i++; 
            }
        } 
        return $password; 
    } 
    
    function generateActivationKey($userdetail){
         
        if(Validation::email($userdetail)){
            $user = $this->emailExist($userdetail);
        }else{
            $user = $this->usernameExist($userdetail);
        }
        
        if(empty($user))
            return FALSE;
        
        $random_key = $this->generatePassword(8);
        $password   = AuthComponent::password($random_key);
        
        $this->updateAll(array(
            'User.activation_key'   => "'{$random_key}'",     
            'User.password'         => "'{$password}'",     
        ),array(
            'User.id' => $user['User']['id'],
        ));
        
        return array(
            'username'          => $user['User']['username'],
            'new_password'      => $random_key,
        );
    }
    
    function autoSuggest($key,$type = NULL,$userId = NULL,$role=null,$city_slug=null){
        
        $qry  = " SELECT User.id,User.username,User.firstname,User.lastname,User.balance,User.available_balance,User.student_number,User.email_id,User.role,User.phone_no,User.address,User.handed_firstaid_papirs,User.theory_test_passed,User.teacher_id, COUNT(DISTINCT systembookings.`id`) AS no_of_booking, SUM(if(systembookings.`booking_type` = 'Køretime',1,0)) AS no_of_driving_lessons";
        if($type == 'student'){
            $qry .= ",  CONCAT(TUser.firstname,' ',TUser.lastname)  as teacher_name";
        }
        $qry .= " FROM users as `User`";       
        $qry .= " LEFT JOIN systembookings ON systembookings.`student_id` = User.`id`";
        if($type == 'student'){
            $qry .= " LEFT JOIN users as TUser ON User.`teacher_id` = TUser.`id`";
        }       
        $qry .= " WHERE ( ";
        $qry .= " (User.is_completed = 0) AND ";
        $qry .= " (User.firstname       LIKE '%{$key}%' OR ";
        $qry .= " User.lastname         LIKE '%{$key}%' OR ";
        $qry .= " concat(User.firstname , ' ',User.lastname)         LIKE '%{$key}%' OR ";
        $qry .= " User.email_id         LIKE '%{$key}%' OR ";
        $qry .= " User.phone_no         LIKE '%{$key}%' OR ";
        $qry .= " User.username         LIKE '%{$key}%')";
        if($type == 'student'){
            $qry .= "  AND User.role = 'student' AND User.status = 'active'";
            if(!is_null($userId) && !is_null($city_slug) && $city_slug != '' && ($role == 'internal_teacher' || $role == 'external_teacher')){
                $qry .= " AND (User.teacher_id = {$userId}";
                $qry .= " OR User.city = '$city_slug')";
                $qry .= " AND User.teacher_id = {$userId}";
            }elseif(!is_null($userId)  && ($role == 'internal_teacher' || $role == 'external_teacher') ){
                $qry .= " AND User.teacher_id = {$userId}";
            }
        }elseif ($type == 'teacher') {
            $qry .= "  AND User.role IN ('internal_teacher','external_teacher','admin')";
        }
        
        $qry .= " ) ";
        $qry .= " GROUP BY User.`id` ";
        if(!is_null($userId)){
            $qry .= " ORDER BY IF(User.teacher_id = '$userId', 1, 2) ASC";
        }
        return $this->query($qry);
        
    }
    
    function validateData($requestData,$isEdit = FALSE,$front = FALSE){
        
        $this->data             = $requestData;        
        $this->validate_length  = Configure::read('validate');
        
        if(empty($this->data['User']['firstname'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_firstname_error',
                'message'       => __('First Name cannot be null.')
            );
        }
        
        if(!empty($this->data['User']['firstname'])){
            if((strlen($this->data['User']['firstname']) < $this->validate_length['mini']) || (strlen($this->data['User']['firstname']) > $this->validate_length['max'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                    'key'           => 'txt_firstname_error',
                    'message'       => __('First Name Should be 3-30 characters.')
                );
            }           
        }
      
        if(empty($this->data['User']['lastname'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_lastname_error',
                'message'       => __('Last Name cannot be null.')
            );
        }
        
        if(!empty($this->data['User']['lastname'])){
            if((strlen($this->data['User']['lastname']) < $this->validate_length['mini']) || (strlen($this->data['User']['lastname']) > $this->validate_length['max'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                    'key'           => 'txt_lastname_error',
                    'message'       => __('Last Name Should be 3-30 characters.')
                );
            }
        }
        
        if(empty($this->data['User']['email_id'])){
           $this->error_flag    = 'error';
           $this->error_msg[]   = array(
               'key'            => 'txt_email_id_error',
               'message'        => __('Email ID cannot be null.')
           );           
        }
        
        if(!empty($this->data['User']['email_id'])){
            if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$this->data['User']['email_id'])){
                $this->error_flag    = 'error';
                $this->error_msg[]   = array(
                    'key'            => 'txt_email_id_error',
                    'message'        => __('Please Enter Valid Email ID.')
                );  
            }
        }
        
        if($isEdit){
            if(!empty($this->data['User']['email_id'])){               
                $result = $this->emailExist($this->data['User']['email_id']);                         
                if(!empty($result) && ($result['User']['id'] != $this->data['User']['id'])){
                    $this->error_flag  = 'error';
                    $this->error_msg[] = array(
                        'key'          => 'txt_email_id_error',
                        'message'      => __('Email Id already exists.')
                    );               
                }                
            }
        }else{
            if(!empty($this->data['User']['email_id'])){               
                $result = $this->emailExist($this->data['User']['email_id']);
                if(!empty($result)){
                    $this->error_flag  = 'error';
                    $this->error_msg[] = array(
                        'key'          => 'txt_email_id_error',
                        'message'      => __('Email Id already exists.')
                    );               
                }                
            }
        }
        
        if(empty($this->data['User']['phone_no'])){
           $this->error_flag    = 'error';
           $this->error_msg[]   = array(
               'key'            => 'txt_phone_no_error',
               'message'        => __('Phone Number cannot be null.')
           );           
        }
        
        if($this->data['User']['role'] == 'student') {
            if(empty($this->data['User']['teacher_id'])) {
                $this->error_flag    = 'error';
                $this->error_msg[]   = array(
                    'key'            => 'txt_teacher_id_error',
                    'message'        => __('Please Select Teacher.')
                ); 
            }
        }
        
        if(in_array($this->data['User']['role'],array('external_teacher','internal_teacher')) && (!$front)){
            
            if(isset($this->data['User']['company_id']) && empty($this->data['User']['company_id'])){
                $this->error_flag    = 'error';
                $this->error_msg[]   = array(
                    'key'            => 'txt_company_id_error',
                    'message'        => __('Please Select Company.')
                );         
            }
            
            if(empty($this->data['User']['nick_name_user'])){
                $this->error_flag    = 'error';
                $this->error_msg[]   = array(
                    'key'            => 'txt_nick_name_user_error',
                    'message'        => __('Please Enter Nick Name For User.')
                );         
            }else if(strlen($this->data['User']['nick_name_user']) > 3){
                $this->error_flag    = 'error';
                $this->error_msg[]   = array(
                    'key'            => 'txt_nick_name_user_error',
                    'message'        => __('Maximum 3 Characters Are allowed.')
                ); 
            }
        }
        
        if($this->data['User']['role'] == 'internal_teacher') {
            if(empty($this->data['User']['city'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                    'key'           => 'txt_city_error',
                    'message'       => __('City cannot be null.')
                );
            }
           
            if(!empty($this->data['User']['city'])){
                if((strlen($this->data['User']['city']) < $this->validate_length['mini']) || (strlen($this->data['User']['city']) > $this->validate_length['max'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                        'key'           => 'txt_city_error',
                        'message'       => __('City Should be 3-30 characters.')
                    );
                }
            }
        }
        
        if($isEdit){
       
            if(isset($this->data['User']['username'])){
                if(empty($this->data['User']['username'])){
                    $this->error_flag  = 'error';
                        $this->error_msg[] = array(
                            'key'          => 'txt_username_error',
                            'message'      => __('Please Enter Username')
                        );    
                 }
                if(!empty($this->data['User']['username'])){
                    $result = $this->usernameExist($this->data['User']['username']);               
                    if(!empty($result) && ($result['User']['id'] != $this->data['User']['id'])){
                        $this->error_flag  = 'error';
                        $this->error_msg[] = array(
                            'key'          => 'txt_username_error',
                            'message'      => __('Username already exists.')
                        );               
                    }                
                 }
            }
            
        }
        
        if(!$isEdit){
            if(!empty($this->data['User']['username'])){
                $result = $this->usernameExist($this->data['User']['username']);
                if(!empty($result)){
                     $this->error_flag  = 'error';
                     $this->error_msg[] = array(
                        'key'           => 'txt_username_error',
                        'message'       => __('Username already exists.')
                     );                        
                }
             }
             
             if(empty($this->data['User']['username'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_username_error',
                   'message'        => __('Username cannot be null.')
                );  
            }

        }
        
        if(!empty($this->data['User']['username'])){
        //            if(!preg_match('/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/', $this->data['User']['username'])){
        //                $this->error_flag   = 'error';
        //                $this->error_msg[]  = array(
        //                   'key'            => 'txt_username_error',
        //                   'message'        => __('Please Enter Valid Username.')
        //                );      
        //            }
            
            if((strlen($this->data['User']['username']) < $this->validate_length['min']) || (strlen($this->data['User']['username']) > $this->validate_length['max'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_username_error',
                   'message'        => __('Username should be 4-16 characters.')
                );    
            }
        }
        
        
        if($isEdit){
            if(isset($this->data['User']['password']) || isset($this->data['User']['confirm_password'])){
                 if(empty($this->data['User']['password']) && !empty($this->data['User']['confirm_password'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                       'key'            => 'txt_password_error',
                       'message'        => __('Please Enter password')
                    ); 
                }

                if(!empty($this->data['User']['password']) && empty($this->data['User']['confirm_password'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                       'key'            => 'txt_confirm_password_error',
                       'message'        => __('Please Enter confirm password')
                    ); 
                }

                if(!empty($this->data['User']['password']) && !empty($this->data['User']['confirm_password']) && ($this->data['User']['password'] != $this->data['User']['confirm_password'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                       'key'            => 'txt_confirm_password_error',
                       'message'        => __('Password and Confirm Password are not same')
                    );  
                }

                if(!empty($this->data['User']['password'])){
                    if((strlen($this->data['User']['password']) < $this->validate_length['min']) || (strlen($this->data['User']['password']) > $this->validate_length['max'])){
                        $this->error_flag   = 'error';
                        $this->error_msg[]  = array(
                           'key'            => 'txt_password_error',
                           'message'        => __('Password Length Should be 4-16 characters.')
                        );  
                    }
                }

                if(!empty($this->data['User']['confirm_password'])){
                    if((strlen($this->data['User']['confirm_password']) < $this->validate_length['min']) || (strlen($this->data['User']['confirm_password']) > $this->validate_length['max'])){
                        $this->error_flag   = 'error';
                        $this->error_msg[]  = array(
                           'key'            => 'txt_confirm_password_error',
                           'message'        => __('Confirm Password Length Should be 4-16 characters.')
                        );  
                    }
                }
            }
        }else{
            if(empty($this->data['User']['password'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_password_error',
                   'message'        => __('Please Enter password')
                ); 
            }
        
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
        
        return $errorDetail;
    }
    function validateDataForChangePassword($requestData,$isEdit = FALSE,$front = FALSE){
        
        $this->data             = $requestData;        
        $this->validate_length  = Configure::read('validate');
        
    
        if($isEdit){
            if(isset($this->data['User']['password']) || isset($this->data['User']['confirm_password'])){
                 if(empty($this->data['User']['password']) && !empty($this->data['User']['confirm_password'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                       'key'            => 'txt_password_error',
                       'message'        => __('Please Enter password')
                    ); 
                }

                if(!empty($this->data['User']['password']) && empty($this->data['User']['confirm_password'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                       'key'            => 'txt_confirm_password_error',
                       'message'        => __('Please Enter confirm password')
                    ); 
                }

                if(!empty($this->data['User']['password']) && !empty($this->data['User']['confirm_password']) && ($this->data['User']['password'] != $this->data['User']['confirm_password'])){
                    $this->error_flag   = 'error';
                    $this->error_msg[]  = array(
                       'key'            => 'txt_confirm_password_error',
                       'message'        => __('Password and Confirm Password are not same')
                    );  
                }

                if(!empty($this->data['User']['password'])){
                    if((strlen($this->data['User']['password']) < $this->validate_length['min']) || (strlen($this->data['User']['password']) > $this->validate_length['max'])){
                        $this->error_flag   = 'error';
                        $this->error_msg[]  = array(
                           'key'            => 'txt_password_error',
                           'message'        => __('Password Length Should be 4-16 characters.')
                        );  
                    }
                }

                if(!empty($this->data['User']['confirm_password'])){
                    if((strlen($this->data['User']['confirm_password']) < $this->validate_length['min']) || (strlen($this->data['User']['confirm_password']) > $this->validate_length['max'])){
                        $this->error_flag   = 'error';
                        $this->error_msg[]  = array(
                           'key'            => 'txt_confirm_password_error',
                           'message'        => __('Confirm Password Length Should be 4-16 characters.')
                        );  
                    }
                }
            }
        }else{
            if(empty($this->data['User']['password'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_password_error',
                   'message'        => __('Please Enter password')
                ); 
            }
        
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
        
        return $errorDetail;
    }
    function validateDataForStudent($requestData,$isEdit = FALSE,$front = FALSE){
        
        $this->data             = $requestData;        
        $this->validate_length  = Configure::read('validate');
        
       
        if(empty($this->data['User']['phone_no'])){
           $this->error_flag    = 'error';
           $this->error_msg[]   = array(
               'key'            => 'txt_phone_no_error',
               'message'        => __('Phone Number cannot be null.')
           );           
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
        
        return $errorDetail;
    }       
    
    function updateSystemUsers(){
        
        $studentArray   = array();
        $studentObj     = array();
        $studentAry     = array();
        $this->Rnd      = ClassRegistry::init('Rnd');
        
        $systemUsers       = $this->find('all');        
        $systemStudents    = Hash::combine($systemUsers,'{n}.User.student_number','{n}.User');
        
        $students = $this->Rnd->query('GetRegnskabsElever', array('parameters' => array(
                'KundeID'       => KUNDEID,           
        )));
      
        $studentArray   = $students->GetRegnskabsEleverResult->string;

        $i = 0;
        
        $teacherIds = array();
        
        foreach($studentArray as $student){            
            $studentObj = $this->Rnd->query('GetElevspecifikation', array('parameters' => array(
                'KundeID'       => KUNDEID,
                'Elevnummer'    => $student
            )));
       
            if(!empty($studentObj)){
                if(isset($studentObj->GetElevspecifikationResult->Status) && ($studentObj->GetElevspecifikationResult->Status == 'OK')){

                    if(isset($studentObj->GetElevspecifikationResult->Assistent)){
                        $username       = strtolower($studentObj->GetElevspecifikationResult->Assistent->Efternavn);                                               
                        $users          = $this->find('all',array(
                            'conditions'    => array(
                                'User.role' => 'internal_teacher'
                            )
                        )); 
                        
                        $users  = Hash::combine($users,'{n}.User.username','{n}.User');
                        
                        if(in_array($username,array_keys($users))){
                            $this->data['Teacher']['User']['id']        = $users[$username]['id'];
                        }
                        
                        $this->data['Teacher']['User']['username']        = $username;
                        $this->data['Teacher']['User']['password']        = strtolower($studentObj->GetElevspecifikationResult->Assistent->Efternavn);
                        $this->data['Teacher']['User']['firstname']       = $studentObj->GetElevspecifikationResult->Assistent->Fornavn;
                        $this->data['Teacher']['User']['lastname']        = $studentObj->GetElevspecifikationResult->Assistent->Efternavn;
                        $this->data['Teacher']['User']['address']         = $studentObj->GetElevspecifikationResult->Assistent->Adresse;
                        $this->data['Teacher']['User']['zip']             = $studentObj->GetElevspecifikationResult->Assistent->Postnummer;
                        $this->data['Teacher']['User']['city']            = $studentObj->GetElevspecifikationResult->Assistent->By;
                        $this->data['Teacher']['User']['phone_no']        = $studentObj->GetElevspecifikationResult->Assistent->Telefon1;
                        $this->data['Teacher']['User']['other_phone_no']  = $studentObj->GetElevspecifikationResult->Assistent->Telefon2;
                        $this->data['Teacher']['User']['email_id']        = $studentObj->GetElevspecifikationResult->Assistent->Email;
                        $this->data['Teacher']['User']['role']            = 'internal_teacher';
                        $this->saveAll($this->data['Teacher']['User']);

                        $teacherId  = $this->id;
                    }
                
                    if(in_array($student,array_keys($systemStudents))){
                        $this->data['Student']['User']['id']              = $systemStudents[$student]['id'];                             
                    }
                    
                    $this->data['Student']['User']['student_number']  = $student;
                    $this->data['Student']['User']['username']        = $student;
                    $this->data['Student']['User']['password']        = (!empty($studentObj->GetElevspecifikationResult->Elev->Telefon1))?$studentObj->GetElevspecifikationResult->Elev->Telefon1:strtolower($studentObj->GetElevspecifikationResult->Elev->Efternavn);
                    $this->data['Student']['User']['firstname']       = $studentObj->GetElevspecifikationResult->Elev->Fornavn;
                    $this->data['Student']['User']['lastname']        = $studentObj->GetElevspecifikationResult->Elev->Efternavn;
                    $this->data['Student']['User']['address']         = $studentObj->GetElevspecifikationResult->Elev->Adresse;
                    $this->data['Student']['User']['zip']             = $studentObj->GetElevspecifikationResult->Elev->Postnummer;
                    $this->data['Student']['User']['city']            = $studentObj->GetElevspecifikationResult->Elev->By;
                    $this->data['Student']['User']['phone_no']        = $studentObj->GetElevspecifikationResult->Elev->Telefon1;
                    $this->data['Student']['User']['other_phone_no']  = $studentObj->GetElevspecifikationResult->Elev->Telefon2;
                    $this->data['Student']['User']['email_id']        = $studentObj->GetElevspecifikationResult->Elev->Email;
                    $this->data['Student']['User']['role']            = 'student';
                    $this->data['Student']['User']['teacher_id']      = (!isset($studentObj->GetElevspecifikationResult->Assistent))?NULL:$teacherId;
                    
                    $this->saveAll($this->data['Student']['User']);

               }
            }          
        }
        
        die('complete');
        
    }
    
    public function validateRegisterTime($details){
        
        $this->data = $details;
        
        if($this->data['TeacherRegisterTime']['type'] == 'theory'){
            if(empty($this->data['TeacherRegisterTime']['city'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_theory_city',
                   'message'        => __('Please Enter City Name.')
                ); 
            }
        }
        
        if($this->data['TeacherRegisterTime']['type'] == 'other'){
            if(empty($this->data['TeacherRegisterTime']['purpose'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_other_purpose',
                   'message'        => __('Please Enter Purpose , spend behind that time.')
                ); 
            }
        }
        
        if($this->data['TeacherRegisterTime']['type'] == 'driving'){
            if(empty($this->data['TeacherRegisterTime']['driving_type'])){
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                   'key'            => 'txt_driving_driving_type',
                   'message'        => __('Select Driving Type.')
                ); 
            }
        }
        
        if(empty($this->data['TeacherRegisterTime'][$this->data['TeacherRegisterTime']['type']]['from'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
               'key'            => "txt_{$this->data['TeacherRegisterTime']['type']}_time",
               'message'        => __('Please Enter Time.')
            ); 
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
    
        return $errorDetail;
    }
    
    public function getBalance($studentId, $amt) {
        $student    = $this->findById($studentId);
        $balanceArr = array();
        if(!empty($student))
        {
            $available_balance = (int)$student['User']['balance'];
            $available_balance_new = (int)$student['User']['available_balance'];
            $computedBalance = $available_balance + $amt;
            $computedBalanceNew = $available_balance_new + $amt;

            $balanceArr = array
            (
                'originalBalance'   => $available_balance,
                'credit_max'   => (int)$student['User']['credit_max'],
                'computedBalance'   => $computedBalance,
                'computedBalanceNew'   => $computedBalanceNew,
                'current_available_balance' => (int)$student['User']['available_balance']                
            );
        }
        
        return $balanceArr;
    }

    public function updateBalance($studentId, $amt) {
        $student    = $this->findById($studentId);
        $this->id = $studentId;
        $available_balance = (float)$student['User']['balance'];
        $available_balance = $available_balance + $amt;
        $this->saveField('available_balance', $available_balance );
        $this->saveField('balance', $available_balance );
    }

    public function sendBalanceNotificationStudents() {

        $joins[]    = array(
            'table'         => 'driving_lessons',
            'alias'         => 'DrivingLessons',
            'type'          => 'LEFT',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                'User.id = DrivingLessons.student_id'
            )
        );
        // $db = $this->getDataSource();
        // $db->fullDebug = true;
        $students = $this->find('all',array(
            'conditions'    => array(
                                    'CAST(User.balance AS DECIMAL(10,2)) < User.last_balance',
                                    'CAST(User.balance AS DECIMAL(10,2)) >' => 0,
                                    'CAST(User.balance AS DECIMAL(10,2)) <= 1000',
                                    ),
            'fields'        => array('User.id','User.firstname','User.balance','User.phone_no',
                                    'DrivingLessons.student_id','count(DrivingLessons.id) as no_of_driving_lessons'
                                    ),
            'joins'     => $joins,
            'group' => array(
                        'User.id HAVING no_of_driving_lessons < 17'
                        // 'User.id HAVING no_of_driving_lessons > -1'
                        // 'User.id'
                    )
        ));

        // $log = $db->getLog();
        // print_r($log);

        if( count($students) > 0 ){
            $this->SmsQueue             = ClassRegistry::init('SmsQueue');
            
            foreach ($students as $data) {
                $this->SmsQueue->bookingDetails(
                    array(
                        'data' => array(
                            'User'      => array(
                                'firstname' => $data['User']['firstname'],
                                'amount'    => $data['User']['balance']
                            )
                        ),
                        'mobileno'      => (substr($data['User']['phone_no'], 0, 2) == '45') ? '+' . $data['User']['phone_no'] : '+45'. $data['User']['phone_no'],
                        'template'      => 'studentBalanceNotification',
                        'priority'      => 0,
                        'instant'       => TRUE
                    )
                );

                $student    = $this->findById( $data['User']['id'] );
                $this->id   = $data['User']['id'];
                $this->saveField( 'last_balance', (float)$student['User']['balance'] );
            }
        } else {
            echo 'No Students <br />';
        }

        echo 'Complete Queue';
    }
}