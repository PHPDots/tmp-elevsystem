<?php

App::uses('AppModel'        , 'Model');

class track extends AppModel {
    var $validate = array(
        'name' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Name cannot be null.'
            ),
        ),
        'area_id' => array(
            'NotEmpty'  => array(
                'rule'      => 'notEmpty',
                'message'   => 'Area no selected.'
            ) ,
        )
    );
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        if (isset($this->data[$this->alias]['slug'])) {
            $this->data[$this->alias]['slug'] = strtolower(
                    Inflector::slug($this->data[$this->alias]['name'])
            );
        }
    }
}