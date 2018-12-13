<?php

App::import('Component', 'RequestHandler');

class DiscountsController extends AppController
{
    public $uses = array('Discount','City');
    
    private function breadcrum($case,$discounts = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Discounts'),
            'url'   => Router::url(array('controller'=>'discounts','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => __('Edit Discount'),
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Discount'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Discounts');
        
        $this->perPage  = $this->getPerPage('Discount');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Discount.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $discounts = $this->Paginator->paginate('Discount');
        $cities  = $this->City->find('list',array('fields' => array('slug','name')));
      
        $this->set(array(
            'discounts' => $discounts,            
            'perPage'   => $this->perPage,
            'cities'     => $cities
        ));
    }
    
    public function add() {
        
        $this->breadcrum('add');
        
        $cities  = $this->City->find('list',array('fields' => array('slug','name')));
        
        $this->set(array(
            'isEdit'    => FALSE,
            'cities'    => $cities
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Discount->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            $this->Discount->create();
            
            if($this->Discount->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Discount has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Discount Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Discount not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Discount Add')
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
        
        $price  = $this->Discount->findById($id);
        $cities  = $this->City->find('list',array('fields' => array('slug','name')));
        
        if(empty($price)) {
            return $this->redirect(array('action' => 'index'));
        }
        $price['Discount']['from_date'] = date('d.m.Y',  strtotime($price['Discount']['from_date']));
        $this->breadcrum('edit',$price);
        
        if(!$this->request->data) {
            $this->request->data = $price;
        }
        
        $this->set(array(
            'isEdit'    => TRUE,
            'cities'     => $cities,
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Discount']['id'] = $id;
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Discount->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            $this->Discount->create();
            
            if($this->Discount->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Discount has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Discount Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Discount not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Discount Add')
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');  
            }
        }
    }
    
    private function processData() {
        $this->request->data['Discount']['from_date'] = date('Y/m/d',strtotime(str_replace('/', '-', $this->request->data['Discount']['from_date'])));
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Discount->delete($id)) {
            $this->Session->setFlash(__('The Discount has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}