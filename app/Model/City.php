<?php

App::uses('AppModel'        , 'Model');

class City extends AppModel {
    var $validate = array(
        'name' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Name cannot be null.'
            ),
            'Unique' => array(
                'rule'      => 'isUnique',
                'message'   => 'City already exists.'
            )
        ),
        'city_code' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'City Code cannot be null.'
            )
        ),
    );
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        if (isset($this->data[$this->alias]['slug']) && empty($this->data[$this->alias]['slug'])) {
            $this->data[$this->alias]['slug'] = strtolower(
                    Inflector::slug($this->data[$this->alias]['name'])
            );
        }
    }
}