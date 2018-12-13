<?php

App::uses('AppModel'        , 'Model');
App::uses('AuthComponent'   , 'Controller/Component');

class Discount extends AppModel {
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
    public $validate_length = array();
    
    function validateData($requestData,$isEdit = FALSE,$front = FALSE) {
        
        $this->data = $requestData;
        
        if(empty($this->data['Discount']['from_date'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_from_date_error',
                'message'       => __('From Date cannot be null.')
            );
        }
        
        if(empty($this->data['Discount']['city'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_city_error',
                'message'       => __('City cannot be null.')
            );
        }
        
        if(empty($this->data['Discount']['discount'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_discount_error',
                'message'       => __('Discount cannot be null.')
            );
        }
        
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
}