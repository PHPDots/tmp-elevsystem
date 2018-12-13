<?php

App::import('Component', 'RequestHandler');

class ActivityNumbersController extends AppController
{
    private function breadcrum($case,$prices = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Activity Numbers'),
            'url'   => Router::url(array('controller'=>'activityNumbers','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => isset($prices['ActivityNumber']['name'])?$prices['ActivityNumber']['name']:'',
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Activity Number'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Activity Numbers');
        
        $this->perPage  = $this->getPerPage('ActivityNumber');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('ActivityNumber.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $activityNumbers    = $this->Paginator->paginate('ActivityNumber');
      
        $this->set(array(
            'activityNumbers'   => $activityNumbers,            
            'perPage'           => $this->perPage,
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
        
        if($this->request->is('post')) {
            $errorDetails   = $this->ActivityNumber->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            $this->ActivityNumber->create();
            
            if($this->ActivityNumber->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Activity Number has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Activity Number Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Activity Number not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Activity Number Add')
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');  
            }
        }
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $price  = $this->ActivityNumber->findById($id);
        $areas  = $this->Area->find('list',array('fields' => array('slug','name')));
        
        if(empty($price)) {
            return $this->redirect(array('action' => 'index'));
        }

        $this->breadcrum('edit',$price);
        
        if(!$this->request->data) {
            $this->request->data = $price;
        }
        
        $this->set(array(
            'isEdit'    => TRUE,
            'areas'     => $areas,
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['ActivityNumber']['id'] = $id;
        
        if($this->request->is('post')) {
            $errorDetails   = $this->ActivityNumber->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            if($this->ActivityNumber->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Activity Number has been updated.'),
                    'status'    => 'success',
                    'title'     => __('Activity Number Update'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Activity Number not updated successfully'),
                    'status'    => 'success',
                    'title'     => __('Activity Number Update')
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');  
            }
        }
    }
    
    private function processData() {        
        if($this->request->data['ActivityNumber']['type'] != 'area') {           
            $this->request->data['ActivityNumber']['area']      = NULL;
            $this->request->data['ActivityNumber']['status']    = NULL;
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->ActivityNumber->delete($id)) {
            $this->Session->setFlash(__('The Activity Number has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}