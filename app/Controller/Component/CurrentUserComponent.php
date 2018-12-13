<?php

/**
 * CurrentUser to Manage the Data B/w Various Sites and Along with View As well
 * 
 * @package Black
 * @subpackage app.Controller.Component
 * @author Anand Thakkar <anand@blackid.com>
 */

App::uses('Component', 'Controller');


class SiteInfoComponent extends Component {
    
    private $data;


    public function write($key,$value){
        $this->data[$key] = $value;
    }


    public function readAll(){
        return $this->data;
    }
    
    /**
     * 
     * @param string $key
     * @return mix
     */
    public function read($key) {
        return ((array_key_exists($key,$this->data))?$this->data[$key]:NULL);
    }
    
    
    public function beforeRender(\Controller $controller) {
        
        $controller->set($controller->SiteInfo->readAll());
        
        parent::beforeRender($controller);
    }
    
}