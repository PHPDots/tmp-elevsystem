<?php

App::import('Component', 'RequestHandler');

class DrivingTypesController extends AppController
{

    public function beforeRender() {
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
    }
    
    private function breadcrum($case,$drivingTypes = array()){
        
        $pageTitle[] = array(
            'name'  => __('DrivingTypes'),
            'url'   => Router::url(array('controller'=>'drivingTypes','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $drivingTypes['DrivingType']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add DrivingType'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','DrivingTypes');
        
        $this->perPage  = $this->getPerPage('DrivingType');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('DrivingType.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $drivingTypes  = $this->Paginator->paginate('DrivingType');
      
        $this->set(array(
            'drivingTypes'    => $drivingTypes,            
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
        
        $this->DrivingType->create();
        
        if($this->DrivingType->save($this->request->data)) {
            $this->Session->setFlash(__('The DrivingType has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The DrivingType could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $drivingTypes = $this->DrivingType->findById($id);
        if(empty($drivingTypes)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$drivingTypes);
        
        if(!$this->request->data) {
            $this->request->data = $drivingTypes;
        }
        
        $this->set(array(
            'isEdit' => TRUE, 
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['DrivingType']['id'] = $id;
        if($this->DrivingType->save($this->request->data)) {
            $this->Session->setFlash(__('The DrivingType has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The DrivingType could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->DrivingType->delete($id)) {
            $this->Session->setFlash(__('The DrivingType has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}