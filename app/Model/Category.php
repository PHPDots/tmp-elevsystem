<?php

App::uses('AppModel'        , 'Model');

class Category extends AppModel {
    var $validate = array(
        'name' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Name cannot be null.'
            ),
            'Unique' => array(
                'rule'      => 'isUnique',
                'message'   => 'Category already exists.'
            )
        ),
        'category_code' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Category Code cannot be null.'
            )
        ),
    );
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        
    }
}