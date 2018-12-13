<?php

App::uses('AppModel'        , 'Model');

class Page extends AppModel {
    var $validate = array(
        'title' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Title cannot be null.'
            ),
        ),
        'slug' => array(
            'NotEmpty' => array(
                'rule'    => 'notEmpty',
                'message' => 'Slug cannot be null.'
            ),
        ),
    );
}