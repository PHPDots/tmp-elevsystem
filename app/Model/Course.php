<?php

App::uses('AppModel'        , 'Model');

class Course extends AppModel {
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
    public $validate_length = array();

    function validateData($requestData,$isEdit = FALSE,$front = FALSE) {
        
        $this->data = $requestData;
        
        
        if(empty($this->data['Course']['name'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_name_error',
                'message'       => __('Please Enter Course Name.')
            );
        } 
        
        if(empty($this->data['Course']['price'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_price_error',
                'message'       => __('Please Enter Price.')
            );
        }else if(!empty($this->data['Course']['price']) && !is_numeric($this->data['Course']['price'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_price_error',
                'message'       => __('Please Enter Numeric value for price.')
            );
        } 
        
        if(empty($this->data['Course']['teacher_time'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_teacher_time_error',
                'message'       => __('Please Enter Teacher Time.')
            );
        }else if(!empty($this->data['Course']['teacher_time']) && !is_numeric($this->data['Course']['teacher_time'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_teacher_time_error',
                'message'       => __('Please Enter Numeric value for teacher time.')
            );
        } 
        
        if(empty($this->data['Course']['activity_number'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_activity_number_error',
                'message'       => __('Please Enter Activity Number.')
            );
        } 
        
        if(empty($this->data['Course']['area'])) {          
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_area_error',
                'message'       => __('Please Select Area.')
            );
        } 
        
        
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
}