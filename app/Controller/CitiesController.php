<?php

App::import('Component', 'RequestHandler');

class CitiesController extends AppController
{
    public function beforeRender() {
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
    }
    
    private function breadcrum($case,$city = array()){
        
        $pageTitle[] = array(
            'name'  => __('Cities'),
            'url'   => Router::url(array('controller'=>'cities','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $city['City']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add City'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Cities');
        
        $this->perPage  = $this->getPerPage('City');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('City.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $cities  = $this->Paginator->paginate('City');
      
        $this->set(array(
            'cities'    => $cities,            
            'perPage'   => $this->perPage,
        ));
    }

    public function autoSuggest($key){
        
        $autoSuggestion = array();        
        if(!$this->request->is('ajax')){
            $this->redirect(array('action'=>'index'));
        }
        
        if(strlen($key)>=2){
           $conditions['name LIKE'] = "%{$key}%";
           $autoSuggestion   = $this->City->find('all',array(
                    'conditions'    => $conditions,
                ));
        }
       
        $this->set('autoSuggestion' ,$autoSuggestion);
        $this->layout   = 'ajax';
        $this->render('Ajax/autoSuggetion');
       
    }

    
    public function add() {
        
        $this->breadcrum('add');
        
        $this->set(array(
            'isEdit'    => FALSE,
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->City->create();
        
        if($this->City->save($this->request->data)) {
            $this->Session->setFlash(__('The City has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The City could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $city = $this->City->findById($id);
        if(empty($city)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$city);
        
        if(!$this->request->data) {
            $this->request->data = $city;
        }
        
        $this->set(array(
            'isEdit' => TRUE, 
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['City']['id'] = $id;
        if($this->City->save($this->request->data)) {
            $this->Session->setFlash(__('The City has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The City could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->City->delete($id)) {
            $this->Session->setFlash(__('The City has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}