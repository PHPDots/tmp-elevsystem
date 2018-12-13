<?php

class DrivingLesson extends AppModel{
    
    public $data        = array();
    public $error_flag  = FALSE;
    public $error_msg   = array();
    
    public function validateData($data,$isEdit = FALSE) {
        
        $this->data             = $data;
        
        if(isset($this->data['DrivingLesson']['student_id']) && empty($this->data['DrivingLesson']['student_id'])) {
            $this->error_flag   = TRUE;
            $this->error_msg[]  = array(
                'key'           => 'txt_student_id_error',
                'message'       => __('Please Select Student.')
            );
        }
        
        if(isset($this->data['DrivingLesson']['start_time']) && empty($this->data['DrivingLesson']['start_time'])) {
            $this->error_flag   = TRUE;
            $this->error_msg[]  = array(
                'key'           => 'txt_start_time_error',
                'message'       => __('Please Select Start Time.')
            );
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,          
        );
       
        return $errorDetail;
    }
    
    public function countNumberOfBookings($data){
        
        $this->data     = $data;
        
        $bookingCounts  = $this->find('all',array(
            'fields'        => array('COUNT(DrivingLesson.id) as total','DrivingLesson.student_id'),
            'conditions'    => array(
                'DrivingLesson.student_id'  => $this->data['DrivingLesson']['student_id'],
                'User.student_medical_profile is NUll'
            ),
            'joins'         => array(
                array(
                    'table'         => 'users',
                    'alias'         => 'User',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'User.id = DrivingLesson.student_id'
                    ))
            ),
            'group'         => array(                     
                'DrivingLesson.student_id',
            )
        ));
        
        $bookingCounts      = Hash::combine($bookingCounts,'{n}.DrivingLesson.student_id','{n}.{n}.total');   
        
        return $bookingCounts;
        
    }
}