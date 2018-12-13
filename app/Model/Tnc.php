<?php

class Tnc extends AppModel{
    
    public $hasMany         = array('TncUser');
    public $data            = array();
    public $error_flag      = 'success';
    public $error_msg       = array();
    
    function validateData($data){
        
        $this->data = $data;
        
        if(empty($this->data['Tnc']['title'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_error_title',
                'message'       => __('Title Cannot Be Empty.')
            );
        }
        
        if(empty($this->data['Tnc']['terms'])){
            $this->error_flag   = 'error';
            $this->error_msg[]  = array(
                'key'           => 'txt_error_terms',
                'message'       => __('Terms Cannot Be Empty.')
            );
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
        
        return $errorDetail;
    }    
    
    function notificationCount($userId,$type    = 'both'){
        
        $this->TncUser  = ClassRegistry::init('TncUser');
        $conditions     = array();
        
        $notification['count']  = Hash::extract($this->TncUser->find('all',array(
            'fields'        => array('COUNT(TncUser.id) as total'),
            'conditions'    => array(
                'TncUser.user_id'   => $userId,
                'TncUser.agree'     => 0
            ),            
        )),'{n}.{n}.total');
        
        if($type == 'both'){
            
            $conditions['TncUser.user_id']  = $userId;
        
            $notification['details']  = $this->find('all',array(            
                'conditions'    => array(
                    'TncUser.user_id'   => $userId,
                    'TncUser.agree'     => 0
                ),
                'joins'         => array(
                    array(
                        'table'         => 'tnc_users',
                        'alias'         => 'TncUser',
                        'type'          => 'LEFT',
                        'foreignKey'    => FALSE,
                        'conditions'    => array(
                            'TncUser.tnc_id = Tnc.id'
                        )
                    )
                ),
                'recursive'     => 0
            ));
        }
        
        
        
        return $notification;
    }
}
