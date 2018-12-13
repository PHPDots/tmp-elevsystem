<?php

class LiveEdit extends AppModel{
    
    public $data        = array();
    public $error_flag  = FALSE;
    public $error_msg   = array();
 
    public function checkUserEditing($type,$userId,$entityId = 0){
       
        $conditions = array(
            'user_id' => $userId
        );
        $is_exist = $this->find('all', array('condition'=>array('user_id'=>$userId,'form_type'=>$type)));
       
        if (count($is_exist)>0){
            return true;
        }else {
            return false;
        }
               
    }

    public function whoIsEditing($type,$userId,$entityId = 0){
       
        $conditions = array(
            'user_id' => $userId
        );
        $data = array();
        $data = $this->find('first', array('condition'=>array('form_type'=>$type)),
                                        array('order' => array('id' => 'desc')));
        
        if (count($data)>0){
            return $data;
        }else {
            return $data;
        }
               
    }

    public function getAllData($type){
       
       
        $data = array();
        $data = $this->find('all', array('condition'=>array('form_type'=>$type)),
                                        array('order' => array('id' => 'desc')));
        
        if (count($data)>0){
            return $data;
        }else {
            return $data;
        }
               
    }

    public function getAllDataByDate($date,$location){
       
        
        $data = array();
        $data = $this->find('all', array('conditions'=>array('date_selected' =>  $date,
                                                            'location'      =>  $location)),
                                   array('order' => array('id' => 'desc')));
      
        if (count($data)>0){
            return $data;
        }else {
            return $data;
        }
               
    }

    public function isEditing($date,$location,$userId){
        $data = array();
        $data = $this->find('first', array('conditions'=>array('date_selected' =>  $date,
                                                               'location'      =>  $location)),
                                   array('order' => array('id' => 'desc')));
      
        if (count($data)>0){
            return $data;
        }else {
            return $data;
        }
               
    }

    public function bookingLive($all_data,$d,$l,$userId,$key,$bookingId){

      
         $this->deleteAll( array( 'user_id' => $userId, array( 'not' => array( 'date_selected' => $d ) ) ) );
        if(count($all_data)>0){
            $now_time  = strtotime(date("Y-m-d H:i:s"));
            foreach($all_data as $data){
                $delete =  false;
                $created_time  = strtotime($data['LiveEdit']['created']);
                $created_time =  $created_time+360;
                $time_older = $created_time - $now_time;
                if($time_older<0){
                    $delete =  true;
                    $this->delete(array('id'=>$data['LiveEdit']['id']));
                } 
                if(!$delete){
                    if($data['LiveEdit']['user_id'] == $userId){
                         return array("edit"=>1,"time"=> $created_time);
                    }else{
                         return array("edit"=>0,"user_id"=>$data['LiveEdit']['user_id']);
                    }
                }  

            }
            $time = date("Y-m-d H:i:s");
            $live['user_id']         = $userId;
            $live['created']         = $time; 
            $live['modified']        = $time;
            $live['date_selected']   = $d;
            $live['location']        = $l;
            $live['form_type']       = $key;
            $this->save($live);
            return array("edit"=>1,"time"=>300);
        }else{
            $time = date("Y-m-d H:i:s");
            $live['user_id']         = $userId;
            $live['created']         = $time; 
            $live['modified']        = $time;
            $live['date_selected']   = $d;
            $live['location']        = $l;
            $live['form_type']       = $key;
            $this->save($live);
            return array("edit"=>1,"time"=>300);
        }
    }
}