<?php

App::uses('AppModel'        , 'Model');

class Area extends AppModel {
    var $validate = array(
        'name' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Name cannot be null.'
            ),
            'Unique' => array(
                'rule'      => 'isUnique',
                'message'   => 'Area already exists.'
            )
        ),
        'address' => array(
            'NotEmpty'  => array(
                'rule'      => 'notEmpty',
                'message'   => 'Address cannot be null.'
            ),
            'Unique' => array(
                'rule'      => 'isUnique',
                'message'   => 'Address already exists.'
            )
        )
    );
    
    public $hasMany = array(
        'AreaTimeSlot' => array(
            'order'     => 'AreaTimeSlot.time_slots ASC'
    ));
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        if (isset($this->data[$this->alias]['slug']) && empty($this->data[$this->alias]['slug'])) {
            $this->data[$this->alias]['slug'] = strtolower(
                    Inflector::slug($this->data[$this->alias]['name'])
            );
        }
        $this->data[$this->alias]['color'] = '#'.$this->data[$this->alias]['color'];
    }
    
    public function updateArea($data) {
        
        /*
         * Steps:
         * A. Delete records, from area_time_slots table, with provided area_id
         * B. Save all data
         */ 
        $this->getDataSource()->begin();
        
        if(!empty($data['AreaTimeSlot'])) {
            $this->AreaTimeSlot->deleteAll(array('AreaTimeSlot.area_id' => $data['Area']['id']));
        }
        
        $result = $this->saveAll($data);
        
        if($result) {
            $this->getDataSource()->commit();
            return $result;
        } else {
            $this->getDataSource()->rollback();
            return $result;
        }
    }
}