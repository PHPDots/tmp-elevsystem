<?php

App::import('Component', 'RequestHandler');

class CategoryController extends AppController
{
    public function beforeRender() {
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
    }
    
    private function breadcrum($case,$category = array()){
        
        $pageTitle[] = array(
            'name'  => __('Category'),
            'url'   => Router::url(array('controller'=>'category','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $category['Category']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Category'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Category');
        
        $this->perPage  = $this->getPerPage('Category');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Category.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $category  = $this->Paginator->paginate('Category');
      
        $this->set(array(
            'category'    => $category,            
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
        
        $this->Category->create();
        
        if($this->Category->save($this->request->data)) {
            $this->Session->setFlash(__('The Category has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Category could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $category = $this->Category->findById($id);
        if(empty($category)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$category);
        
        if(!$this->request->data) {
            $this->request->data = $category;
        }
        
        $this->set(array(
            'isEdit' => TRUE, 
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Category']['id'] = $id;
        if($this->Category->save($this->request->data)) {
            $this->Session->setFlash(__('The Category has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Category could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Category->delete($id)) {
            $this->Session->setFlash(__('The Category has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}