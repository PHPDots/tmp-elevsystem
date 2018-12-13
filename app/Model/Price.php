<?php

App::uses('AppModel'        , 'Model');
App::uses('AuthComponent'   , 'Controller/Component');

class Price extends AppModel {
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
        
        if(empty($this->data['Price']['from_date'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_from_date_error',
                'message'       => __('From Date cannot be null.')
            );
        }
        
        if($this->data['Price']['type'] == 'area') {
            if(empty($this->data['Price']['area'])) {
                $this->error_flag   = 'error';
                $this->error_msg[]  = array(
                    'key'           => 'txt_area_error',
                    'message'       => __('Area cannot be null.')
                );
            }
        } 
        
        if(empty($this->data['Price']['price'])) {
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