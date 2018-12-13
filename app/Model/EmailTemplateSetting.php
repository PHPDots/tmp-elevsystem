<?php

class EmailTemplateSetting extends AppModel {
    
    function beforeSave($options = array()) {
        
        /**
         * Combining the Settings Field beforSave operation
         */
        $this->data['EmailTemplateSetting'] = array(
            'name'              => $this->data['EmailTemplateSetting']['name'],
            'template_type'     => $this->data['EmailTemplateSetting']['template_type'],
            'body'              => $this->data['EmailTemplateSetting']['body'],
            'settings'          => serialize(array(
                'from'              => $this->data['EmailTemplateSetting']['from'],
                'username'          => $this->data['EmailTemplateSetting']['username'],
                'password'          => $this->data['EmailTemplateSetting']['password'],
                'mailtype'          => $this->data['EmailTemplateSetting']['mailtype'],
                'headers'           => $this->data['EmailTemplateSetting']['headers'],
            )),
        );
        
        parent::beforeSave($options);
        
    }
    
    function afterFind($results, $primary = false) {
        
        for($i=0;$i<count($results);$i++){
            
            if(!isset($results[$i]['EmailTemplateSetting']['settings']))
                continue;
            
            $settings       = unserialize($results[$i]['EmailTemplateSetting']['settings']);
            unset($results[$i]['EmailTemplateSetting']['settings']);
                
            if(!is_array($settings)){
                $settings = array(
                    'from'      => '',
                    'username'  => '',
                    'password'  => '',
                    'mailtype'  => '',
                    'headers'   => '',
                );
            }
            
            $results[$i]['EmailTemplateSetting']    = array_merge($results[$i]['EmailTemplateSetting'],$settings);
            
        }
        
        return $results;
    }
}