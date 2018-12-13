<?php

class EmailTemplatesController extends AppController {

    public $paginate;
    public $perPage = 5;
    public $uses = array('EmailTemplate', 'EmailTemplateSetting');
    
    public function beforeRender() {        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
    }

    public function loadMeta($key) {

        if (!$this->request->is('ajax')) {
            $this->redirect(array('action' => 'index'));
        }

        $templateMeta = $this->EmailTemplate->getTemplateMeta($key);

        $this->set('templateMeta', $templateMeta);

        $this->layout = 'ajax';
        $this->render('Ajax/loadMeta');
    }
    
    private function breadcrum($case,$emailTemplate = array()){
        
        $pageTitle[] = array(
            'name'  => __('Email Templates'),
            'url'   => Router::url(array('controller'=>'emailTemplates','action'=>'index')),
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
                    'name'  => __('Edit ') . $emailTemplate['EmailTemplate']['template'],
                     'url'   => '#',
                );
                break;
         
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
    }

    public function index() {

        $this->SiteInfo->write('pageTitle','Email Templates');
        
        $this->perPage = $this->getPerPage('EmailTemplate');

        $this->paginate = array(
            'limit' => $this->perPage,
            'order' => array('EmailTemplate.id' => 'DESC'),
        );

        $this->Paginator->settings = $this->paginate;

        $data = $this->Paginator->paginate('EmailTemplate');


        $this->set(array(
            'data' => $data,
            'perPage' => $this->perPage,
        ));
    }

    public function add() {

        $this->set(array('isEdit' => FALSE));

        $this->breadcrum('add');
        
        if (!$this->request->is('post')) {
            return;
        }

        $this->EmailTemplate->create();
        
        if ($this->EmailTemplate->save($this->request->data)) {
            $this->Session->setFlash(__('The E-mail Template has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The E-mail Template could not be saved. Please, try again.'), 'alert/error');
        }
    }

    public function edit($id) {
        if (empty($id)) {
            $this->redirect(array('action' => 'index'));
        }

        $emailTemplate = $this->EmailTemplate->findById($id);
        
        $this->breadcrum('edit',$emailTemplate);

        if (empty($emailTemplate)) {
            $this->Session->setFlash(__('Requested Email Settings Template Not Found.'), 'alert/error');
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->EmailTemplate->id = $id;
            if ($this->EmailTemplate->save($this->request->data)) {
                
                $this->Session->setFlash(__('The E-mail Template has been updated'), 'alert/success');
                return $this->redirect(array('action' => 'edit', $id));
            } else {
                $this->Session->setFlash(__('The E-mail Template could not be updated. Please, try again.'), 'alert/error');
            }
        }

        $this->set(array('isEdit' => TRUE));

        $this->request->data = $emailTemplate;

        $this->render('add');
    }

    public function delete($id) {
        
        if (empty($id)) {
            $this->redirect(array('action' => 'index'));
        }

        if ($this->EmailTemplate->delete($id)) {                                 
            $this->Session->setFlash(__('The E-mail Template has been deleted'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function beforeFilter() {

        parent::beforeFilter();

        $this->SiteInfo->write('emailTemplateSettings', $this->EmailTemplateSetting->find('list'));
        $this->SiteInfo->write('EmailTemplates', $this->EmailTemplate->getTemplates());
        $this->SiteInfo->write('EmailTypes', $this->EmailTemplate->getEmailTypes());
    }

}