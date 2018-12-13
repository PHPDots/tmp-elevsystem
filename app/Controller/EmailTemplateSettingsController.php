<?php

class EmailTemplateSettingsController extends AppController {
    
    public $uses = array('EmailTemplate','EmailTemplateSetting');
    public $paginate;
    public $perPage = 10;
    
    public function beforeRender() {        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
    }
    
    private function breadcrum($case,$emailTemplate = array()){
        
        $pageTitle[] = array(
            'name'  => __('Email Template Settings'),
            'url'   => Router::url(array('controller'=>'emailTemplatesSettings','action'=>'index')),
        );
        
        switch ($case){
            
            case 'add':
                
                $pageTitle[] = array(
                    'name'  => __('Add'),
                     'url'   => '#',
                );
                break;
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => __('Edit ') . $emailTemplate['EmailTemplateSetting']['name'],
                     'url'   => '#',
                );
                break;
            
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
    }

    public function index() {
        
        $this->SiteInfo->write('pageTitle','Email Template Settings');
        
        $this->perPage  = $this->getPerPage('EmailTemplateSetting');
        $this->paginate = array(
            'limit' => $this->perPage,
            'order' => array('EmailTemplateSetting.id' => 'DESC'),
        );
        $this->Paginator->settings  = $this->paginate;
        $data   = $this->Paginator->paginate('EmailTemplateSetting');
        $this->set(array(
            'data'      => $data,
            'perPage'   => $this->perPage,
        ));
    }


    public function add() {
        
        $this->breadcrum('add');
        
        if (!$this->request->is('post')) {
            return;
        }
        $this->EmailTemplateSetting->create();
        
        if ($this->EmailTemplateSetting->save($this->request->data)){
            $this->Session->setFlash(__('The E-mail Settings Template has been saved'),'alert/success');
            return $this->redirect(array('action' => 'index'));
        }else{
            $this->Session->setFlash(__('The E-mail Settings Template could not be saved. Please, try again.'),'alert/error');
        }
    }


    public function edit($id) {
        
        if(empty($id)){
            $this->redirect(array('action'=>'index'));
        }

        $emailTemplateSetting   = $this->EmailTemplateSetting->findById($id);
        $this->breadcrum('edit',$emailTemplateSetting);
        
        if (empty($emailTemplateSetting)) {
           $this->Session->setFlash(__('Requested Email Settings Template Not Found.'),'alert/error');
           return $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->EmailTemplateSetting->id = $id;
            if ($this->EmailTemplateSetting->save($this->request->data)) {
                
                $this->Session->setFlash(__('The E-mail Settings Template has been updated'),'alert/success');
                return $this->redirect(array('action' => 'edit',$id));
            }else{
                $this->Session->setFlash(__('The E-mail Setting Template could not be updated. Please, try again.'),'alert/error');
            }
            
        }

        $this->request->data    = $emailTemplateSetting;
        $this->render('add');

    }
    
    public function delete($id) {
        
        if(empty($id)){
            $this->redirect(array('action'=>'index'));
        }
        
        if ($this->EmailTemplateSetting->delete($id)) {                     
            $this->Session->setFlash(__('The E-mail Setting Template has been deleted'),'alert/success');
            return $this->redirect(array('action' => 'index'));
        }
        
    }
    
    public function beforeFilter() {
        
        parent::beforeFilter();
        
        $this->SiteInfo->write('EmailTypes'         , $this->EmailTemplate->getEmailTypes());
        
    }

}