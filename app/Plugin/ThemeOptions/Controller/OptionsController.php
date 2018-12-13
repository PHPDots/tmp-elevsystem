<?php

App::uses('ThemeOptionsAppController', 'ThemeOptions.Controller');

class OptionsController extends ThemeOptionsAppController {
    
    public $uses = array('ThemeOptions.Option','Page');
    
    public function index() {
       
        $this->SiteInfo->write('pageTitle', __('General Options'));
        
        if($this->request->is('post')){
            
            foreach($this->request->data['Option'] as $key => $data){
                $this->Option->saveOptions($key , $data); 
            }            
            
            $this->Session->setFlash(__('The Options has been saved'),'alert/success');
        }
        
        $options    = $this->Option->getOptions();
        $pages      = $this->Page->find('list',array('fields' => array('slug','title')));
        
        $this->set(array(
            'options'   => $options,
            'pages'     => $pages,
        ));
       
    }
    
}