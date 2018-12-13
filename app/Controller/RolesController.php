<?php

App::import('Component', 'RequestHandler');

class RolesController extends AppController
{
    
    private function breadcrum($case,$role = array()){
        
        $pageTitle[] = array(
            'name'  => __('Roles'),
            'url'   => Router::url(array('controller'=>'roles','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $role['Role']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Role'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function beforeRender() {        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Roles');
        
        $this->perPage  = $this->getPerPage('Role');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Role.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $roles  = $this->Paginator->paginate('Role');
      
        $this->set(array(
            'roles'     => $roles,            
            'perPage'   => $this->perPage,
        ));
    }
    
    public function add() {
        
        $this->breadcrum('add');
        
        $this->set(array(
            'isEdit'    => FALSE,
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->Role->create();
        
        if($this->Role->save($this->request->data)) {
            $this->Session->setFlash(__('The Role has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Role could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $role = $this->Role->findById($id);
        if(empty($role)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$role);
        
        if(!$this->request->data) {
            $this->request->data = $role;
        }
        
        $this->set(array(
            'isEdit' => TRUE, 
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Role']['id'] = $id;
        if($this->Role->save($this->request->data)) {
            $this->Session->setFlash(__('The Role has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Role could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Role->delete($id)) {
            $this->Session->setFlash(__('The Role has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}