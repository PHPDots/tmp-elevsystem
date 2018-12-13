<?php
/**
 * 
 * SMS Template
 * 
 * @package OSS
 * @subpackage app.Model
 * 
 */
class SmsTemplate extends AppModel {
    
    public $name                = 'SmsTemplate';    
    public $smsTemplates        = array();
    public $validate            = array(
        'template' => array(
            'validtemplate' => array(
                'rule'      => 'smsTemplateExist',
                'message'   => 'Please Select Valid E-mail Template Type',
             ),
        ),
        'body'  => array(
            'minlength' => array(
                'rule'    => array('minLength', 5),
                'message' => 'Minimum length of 10 characters'
            ),
        ),
        
    );
    
    function __construct($id = false, $table = null, $ds = null) {
        
        parent::__construct($id, $table, $ds);
        
        $fileName        = LISABETHDATASMS . 'main.xml';
        
        /**
         * 
         * Initialize the Templates and Email type From the 
         * main.xmlfile 
         * 
         */
        if(file_exists($fileName)){
            
            $fileData = Xml::build($fileName);
        
                        
            if(count($fileData->templates->template)>0){
                foreach($fileData->templates->template as $template ){
                    $this->smsTemplates[(string)$template->key]  = (string)$template->name;
                }
            }

        }
        
    }
    
    function afterFind($results, $primary = false) {
        
        parent::afterFind($results, $primary);
        
        for($i=0;$i<count($results);$i++){
            
            if(!isset($results[$i]['SmsTemplate']))
                continue;
            
            $meta = $this->getTemplateMeta($results[$i]['SmsTemplate']['template']);
            
            $results[$i]['SmsTemplate'] = array_merge($results[$i]['SmsTemplate'],array('meta'=>$meta));
            
        }
        
        return $results;
    }    
    
        /**
     * get All the Templates from the main.xml File
     * 
     * @return array
     * @since V1.0
     */
    function getTemplates(){
        
        return $this->smsTemplates;
    }
    
    /**
     * Checks Weather the Email Template Exist or not 
     * 
     * @param string $template
     * @return bool
     * @since V1.0
     */
    function smsTemplateExist($template){
         $template = array_values($template);
         $template = $template[0];

         return key_exists($template,$this->smsTemplates);
    }
    
    /**
     * Returns all the Template Meta Replcements 
     * 
     * @param string $key Template Key 
     * @return array
     * @since V1.0
     */
    function getTemplateMeta($key){
        
      
        $metas      = array();
        $fileName   = LISABETHDATASMS . $key . '.xml';
        
        if(!file_exists($fileName)){
            return $metas;
        }
        
        $fileData = Xml::build($fileName);
        
        if(empty($fileData) || count($fileData->meta)==0)
            return $metas;
        
        foreach($fileData->meta as $meta ){
            $metas[(string)$meta->key]  = (array)$meta;
        }
        
        return $metas;
    }
    
    function smsData($template,$placeHolderData = array()){
        
        $pattern        = array();
        $replacement    = array();
        
        $message = '';

        if(isset($template['SmsTemplate']) && count($template['SmsTemplate']['meta'])>0){
            foreach($template['SmsTemplate']['meta'] as $key=>$value) {
                $pattern[]     = "#\%$key\%#";
                $replacement[] = isset($placeHolderData[$value['class']][$value['property']])?$placeHolderData[$value['class']][$value['property']]:'';
            }

            $message    = preg_replace($pattern,$replacement,$template['SmsTemplate']['body']);
        }
        return $message;
    }
    
   /**
    * 
    * Check the user's capability for specific actions
    * 
    * @param capabity to check $capabilty ,$user_id of other user and $args for additional arguments
    * @return boolean
    */
    
    function currentUserCan($capability,$user_id = NULL,$args = array()){
        
        switch ($capability) {
            case 'sms_template_add':
                
                    return in_array($capability,$this->currentUserCapabilities)?TRUE:FALSE;
                break;
            case 'sms_template_edit':
                
                    return in_array($capability,$this->currentUserCapabilities)?TRUE:FALSE;
                break;
            case 'sms_template_delete':
                
                    return in_array($capability,$this->currentUserCapabilities)?TRUE:FALSE;
                break;            
        }
    }
}
