<?php

App::uses('AppModel', 'Model');

class Company extends AppModel {
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
    public $validate_length = array();

    function validateData($requestData,$isEdit = FALSE) {
        
        $this->data = $requestData;
        
        if(empty($this->data['Company']['name'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_name_error',
                'message'       => __('Please Enter Company Name.')
            );
        }
        
        if(empty($this->data['Company']['nick_name'])) {
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_nick_name_error',
                'message'       => __('Please Enter Nick Name.')
            );
        } else {
            $result = $this->findByNickName($this->data['Company']['nick_name']);
            $result = (count($result)== 0) ? FALSE : $result;
            if($isEdit) {
                if(!empty($result) && ($result['Company']['id'] != $this->data['Company']['id'])) {
                    $this->error_flag  = 'error';
                    $this->error_msg[] = array(
                        'key'          => 'txt_nick_name_error',
                        'message'      => __('Company Nick Name already exists.')
                    );
                }
            } else {
                if(!empty($result)){
                    $this->error_flag  = 'error';
                    $this->error_msg[] = array(
                        'key'          => 'txt_nick_name_error',
                        'message'      => __('Company Nick Name already exists.')
                    );               
                }
            }
        }
        
        
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
}