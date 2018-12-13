<?php
/**
 * 
 * E-mail Template
 * 
 * @package OSS
 * @subpackage app.Model
 * 
 */
class EmailTemplate extends AppModel {
    
    public $name                = 'EmailTemplate';
    public $emailTypes          = array();
    public $emailTemplates      = array();
    public $belongsTo           = array('EmailTemplateSetting');
    public $validate            = array(
        'template' => array(
            'validtemplate' => array(
                'rule'      => 'emailTemplateExist',
                'message'   => 'Please Select Valid E-mail Template Type',
             ),
        ),
        'subject' => array(
            'minlength' => array(
                'rule'    => array('minLength', 5),
                'message' => 'Minimum length of 5 characters'
            ),
            'maxlength' => array(
                'rule'    => array('maxLength', 1024),
                'message' => 'Maximum length of 1024 characters'
            )
            
        ),
        'from'  => array(
            'email' => array(
                'rule'    => 'email',
                'message' => 'Please Enter Valid E-mail Address'
            ),
        ),
        'body'  => array(
            'minlength' => array(
                'rule'    => array('minLength', 10),
                'message' => 'Minimum length of 10 characters'
            ),
        ),
        'mailtype'  => array(
            'validtype'     => array(
                'rule'      => 'EmailTypeExist',
                'message'   => 'Please Select Valid E-mail Type'
            ),
        ),
    );
    
    function __construct($id = false, $table = null, $ds = null) {
        
        parent::__construct($id, $table, $ds);
        
        $fileName        = LISABETHDATAEMAIL . 'main.xml';
        
        /**
         * 
         * Initialize the Templates and Email type From the 
         * main.xmlfile 
         * 
         */
        if(file_exists($fileName)){
            
            $fileData = Xml::build($fileName);
        
            if(count($fileData->mailtypes->mailtype)>0){
                foreach($fileData->mailtypes->mailtype as $mailType ){
                    $this->emailTypes[(string)$mailType->key]  = (string)$mailType->name;
                }
            }
            
            if(count($fileData->templates->template)>0){
                foreach($fileData->templates->template as $template ){
                    $this->emailTemplates[(string)$template->key]  = $template->name;
                }
            }

        }
        
    }
    
    /**
     * Get all the Email Type Being Supported by the System
     * 
     * @return array
     * @since V1.0
     */
    function getEmailTypes(){
        return $this->emailTypes;
    }
    
    /**
     * 
     * check Weather the Email Type with the Given Key exist or not 
     * 
     * @param string $type Email Type Key
     * @return bool
     * @since V1.0
     */
    function EmailTypeExist($type){
        $type = array_values($type);
        $type = $type[0];
        
        return key_exists($type,$this->emailTypes);
    }
    
    /**
     * get All the Templates from the main.xml File
     * 
     * @return array
     * @since V1.0
     */
    function getTemplates(){
        
        return $this->emailTemplates;
    }
    
    /**
     * Checks Weather the Email Template Exist or not 
     * 
     * @param string $template
     * @return bool
     * @since V1.0
     */
    function emailTemplateExist($template){
        $template = array_values($template);
        $template = $template[0];

        return key_exists($template,$this->emailTemplates);
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
        $fileName   = LISABETHDATAEMAIL . $key . '.xml';
        
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
    
    function beforeSave($options = array()) {
        
        /**
         * Combining the Settings Field beforSave operation
         */
        $this->data['EmailTemplate'] = array(
            'template'                      => $this->data['EmailTemplate']['template'],
            'subject'                       => $this->data['EmailTemplate']['subject'],
            'email_template_setting_id'     => $this->data['EmailTemplate']['email_template_setting_id'],
            'body'                          => $this->data['EmailTemplate']['body'],
            'settings'                      => serialize(array(
                'from'      => $this->data['EmailTemplate']['from'],
                'username'  => $this->data['EmailTemplate']['username'],
                'password'  => $this->data['EmailTemplate']['password'],
                'mailtype'  => $this->data['EmailTemplate']['mailtype'],
                'headers'   => $this->data['EmailTemplate']['headers'],
            )),
        );
        
        parent::beforeSave($options);
        
    }
    
    function afterFind($results, $primary = false) {
        
        parent::afterFind($results, $primary);
        
        for($i=0;$i<count($results);$i++){
            
            if(!isset($results[$i]['EmailTemplate']['settings']))
                continue;
            
            $settings       = unserialize($results[$i]['EmailTemplate']['settings']);
            unset($results[$i]['EmailTemplate']['settings']);
                
            if(!is_array($settings)){
                $settings = array(
                    'from'      => '',
                    'username'  => '',
                    'password'  => '',
                    'mailtype'  => '',
                    'headers'   => '',
                );
            }
            
            $meta = $this->getTemplateMeta($results[$i]['EmailTemplate']['template']);
            
            $results[$i]['EmailTemplate']    = array_merge($results[$i]['EmailTemplate'],$settings,array('meta'=>$meta));
            
        }
        
        return $results;
    }
    
    function emailData($email,$template,$placeHolderData = array()){
        $default_data = array(
            'to'                => $email,
            'from'              => $template['EmailTemplateSetting']['from'],
            'username'          => $template['EmailTemplateSetting']['username'],
            'password'          => $template['EmailTemplateSetting']['password'],
            'setting_template'  => $template['EmailTemplateSetting']['template_type'],
            'headers'           => $template['EmailTemplateSetting']['headers'],
            'mailtype'          => $template['EmailTemplateSetting']['mailtype'],
        );
    
        $final_emaildata  = array(
            'from'              => $template['EmailTemplate']['from'],
            'username'          => $template['EmailTemplate']['username'],
            'password'          => $template['EmailTemplate']['password'],
            'template'          => $template['EmailTemplate']['template'],
            'subject'           => $template['EmailTemplate']['subject'],
            'body'              => $template['EmailTemplate']['body'],
            'headers'           => $template['EmailTemplateSetting']['headers'],
            'template'          => $template['EmailTemplate']['template'],
        );
    
        $finalemailData = array_merge($default_data,$final_emaildata);
        
        if(count($template['EmailTemplate']['meta'])>0){
        
            foreach($template['EmailTemplate']['meta'] as $key=>$value){
                $pattern[]     = "#\%$key\%#";
                $replacement[] = isset($placeHolderData[$value['class']][$value['property']])?$placeHolderData[$value['class']][$value['property']]:'';
            }
            
            $finalemailData['subject']   = preg_replace($pattern,$replacement,$template['EmailTemplate']['subject']);
            $finalemailData['body']      = preg_replace($pattern,$replacement,$template['EmailTemplate']['body']);
            
            $pattern[]      = "#\%body\%#"; 
            $replacement[]  = $finalemailData['body'];
            
            $finalemailData['body']      =  preg_replace($pattern,$replacement,$template['EmailTemplateSetting']['body']);
        
        }
        return $finalemailData;
    }
}
