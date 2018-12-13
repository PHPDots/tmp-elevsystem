<?php

App::uses('AppModel'        , 'Model');
App::uses('AuthComponent'   , 'Controller/Component');

class ActivityNumber extends AppModel {
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
    public $validate_length = array();
    
//    function usernameExist($username) {
//        $result = $this->findByUsername($username);
//        return (count($result)==0)?FALSE:$result;
//    }
    
    function validateData($requestData,$isEdit = FALSE,$front = FALSE) {
        
        $this->data = $requestData;
        
        
        if(empty($this->data['ActivityNumber']['type'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_type_error',
                'message'       => __('Please Select Activity type.')
            );
        } 
        
        if($this->data['ActivityNumber']['type'] == 'area') {
            if(empty($this->data['ActivityNumber']['area'])) {
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                    'key'           => 'txt_area_error',
                    'message'       => __('Please Select area.')
                );
            }
            
            if(!isset($this->data['ActivityNumber']['status'])) {
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                    'key'           => 'txt_status_error',
                    'message'       => __('Please Select status.')
                );
            }
        } 
        
        if(empty($this->data['ActivityNumber']['activity_number'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_activity_number_error',
                'message'       => __('Activity Number cannot be null.')
            );
        }
        
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
}