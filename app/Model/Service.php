<?php

App::uses('AppModel'        , 'Model');

class Service extends AppModel {
    var $validate = array(
        'name' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Name cannot be null.'
            )
        ),
        'price' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Price cannot be null.'
            ),
            'Numeric' => array(
                'rule'      => 'numeric',
                'message'   => 'Price can be numeric only.'
            )
        ),
        'city_id' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'City cannot be null.'
            )
        ),
        'category_id' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Category cannot be null.'
            )
        ),
        'code' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Service Code cannot be null.'
            ),
            'Unique' => array(
                'rule'      => 'isUnique',
                'message'   => 'Service Code already exists.'
            )
        ),
    );
    
    public $belongsTo = array(
       
        'Category' => array(
            'className' => 'Category',
            'foreignKey' => 'category_id'
        )

    );
}