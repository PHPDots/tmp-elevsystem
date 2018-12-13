<?php

App::import('Component', 'RequestHandler');

class ServiceController extends AppController
{
     public $uses        = array('Category','City','Service');

    public function beforeRender() {
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
    }
    
    private function breadcrum($case,$service = array()){
        
        $pageTitle[] = array(
            'name'  => __('Service'),
            'url'   => Router::url(array('controller'=>'service','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $service['Service']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Service'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Service');
        
        $this->perPage  = $this->getPerPage('Service');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Service.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        $dbo = $this->Service->getDatasource();
        $service  = $this->Paginator->paginate('Service');
        $this->set(array(
            'service'    => $service,            
            'perPage'   => $this->perPage,
        ));
    }
    
    public function add() {
        
        $this->breadcrum('add');
        
        $this->set(array(
            'isEdit'    => FALSE,
        ));
        
        $city  = $this->City->find('list',array(
            'fields'        => array('city_code','name'),
        ));

        $category  = $this->Category->find('list',array(
            'fields'        => array('category_code','name'),
        ));

        $this->set(array(
            'city' => $city,
            'category' => $category,
        ));

        if($this->request->is('post') && !empty($this->request->data) ) {
            $this->request->data['Service']['city_id'] = implode(",", $this->request->data['Service']['city_id']);
            $this->Service->create();
            $this->request->data['Service']['service_date']  = date('Y-m-d H:i:s');

            if($this->Service->save($this->request->data)) {
                $this->Session->setFlash(__('The Service has been saved'), 'alert/success');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Service could not be saved. Please, try again.'), 'alert/error');
            }
        }
        
            return;
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $service = $this->Service->findById($id);
        if(empty($service)) {
            return $this->redirect(array('action' => 'index'));
        }
        $city  = $this->City->find('list',array(
            'fields'        => array('city_code','name'),
        ));

        $category  = $this->Category->find('list',array(
            'fields'        => array('category_code','name'),
        ));

        $this->breadcrum('edit',$service);
        
        if(!$this->request->data) {
            $this->request->data = $service;
        }
        $service['Service']['city_id'] = explode(",", $service['Service']['city_id']);
        $this->set(array(
            'isEdit' => TRUE, 
            'city' => $city,
            'category' => $category,
            'service' => $service['Service'],
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Service']['id'] = $id;
        $this->request->data['Service']['city_id'] = implode(",", $this->request->data['Service']['city_id']);

        if($this->Service->save($this->request->data)) {
            $this->Session->setFlash(__('The Service has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Service could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Service->delete($id)) {
            $this->Session->setFlash(__('The Service has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}