<?php

App::uses('AppModel'        , 'Model');
App::uses('AuthComponent'   , 'Controller/Component');

class Product extends AppModel {
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
      
    function autoSuggest($key,$type = NULL,$userId = NULL){
        
        $qry  = " SELECT User.id,User.username,User.firstname,User.lastname,User.student_number,User.email_id,User.role";
        $qry .= " FROM users as `User`";       
        $qry .= " WHERE ( ";
        $qry .= " (User.firstname       LIKE '%{$key}%' OR ";
        $qry .= " User.lastname         LIKE '%{$key}%' OR ";
        $qry .= " User.email_id         LIKE '%{$key}%' OR ";
        $qry .= " User.phone_no         LIKE '%{$key}%' OR ";
        $qry .= " User.username         LIKE '%{$key}%')";
        if($type == 'student'){
            $qry .= "  AND User.role = 'student' AND User.status = 'active'";
            if(!is_null($userId)){
                $qry .= " AND User.teacher_id = {$userId}";
            }
        }elseif ($type == 'teacher') {
            $qry .= "  AND User.role IN ('internal_teacher','external_teacher','admin')";
        }
        
        $qry .= " ) ";
  
        return $this->query($qry);
        
    }
    
    function validateData($requestData,$isEdit = FALSE,$front = FALSE){
        
        $this->data             = $requestData;        
        $this->validate_length  = Configure::read('validate');
        
        if(empty($this->data['Product']['name'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_name_error',
                'message'       => __('Name cannot be null.')
            );
        }
        
        if(empty($this->data['Product']['activity_number'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_activity_number_error',
                'message'       => __('Activity Number cannot be null.')
            );
        }
        
        if(empty($this->data['Product']['price'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_price_error',
                'message'       => __('Price cannot be null.')
            );
        }
      
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
        
        return $errorDetail;
    }       
}