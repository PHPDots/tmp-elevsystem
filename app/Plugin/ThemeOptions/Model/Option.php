<?php

App::uses('ThemeOptionsAppModel', 'ThemeOptions.Model');

class Option extends ThemeOptionsAppModel {
    private $options;


    function __construct($id = false, $table = null, $ds = null) {
        
        parent::__construct($id, $table, $ds);
        
        $results = $this->find('all');
        
        foreach($results as $result){
            $this->options[$result['Option']['key']]  = $this->convert($result['Option']['value']   ,'fordisplay');
        }
    }
    
    private function convert($value,$case){
        switch($case){
            case 'fordb':
                if(is_array($value) || is_object($value)){
                    $value = serialize($value);
                }
                break;
            case 'fordisplay':
                $flag   = @unserialize($value);
                $value  = (!$flag)?$value:$flag;
                break;
        }
        return $value;
    }
    
    
    function getOption($key){
        return ($this->keyExist($key)?$this->options[$key]:NULL);
    }
    
    function updateOption($key,$value){
        
        if(!$this->keyExist($key)){
            return $this->saveOptions($key, $value);
        }
        
        $convertedvalue = $this->convert($value, 'fordb');
        $result         = $this->updateAll(array('value'=>"'" . $convertedvalue . "'"),array('key'=>$key));
        
        if($result){
            $this->options[$key] = $value;
        }
        
        return $result;
        
    }


    function saveOptions($key,$value){
        if($this->keyExist($key)){
            return $this->updateOption($key, $value);
        }
        
        
        $convertedvalue = $this->convert($value, 'fordb');
        $this->create();
        $result         = $this->save(array('Option'=>array('key'=>$key,'value'=>$convertedvalue)));
        
        if($result){
            $this->options[$key] = $value;
        }
        
        return $result;
    }
    
    function getOptions(){
        return $this->options;
    }
    
    function keyExist($key){
        return isset($this->options[$key])?TRUE:FALSE;
    }
    
    function removeOption($key){
        
        if(!$this->keyExist($key))
            return TRUE;
        
        unset($this->options[$key]);
        
        return $this->deleteAll(array('Option.key'=>$key));
        
    }
    
}
