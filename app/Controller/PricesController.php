<?php

App::import('Component', 'RequestHandler');

class PricesController extends AppController
{
    private function breadcrum($case,$prices = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Prices'),
            'url'   => Router::url(array('controller'=>'prices','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => isset($prices['Price']['name'])?$prices['Price']['name']:'',
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Price'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle','Prices');
        
        $this->perPage  = $this->getPerPage('Price');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Price.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $prices = $this->Paginator->paginate('Price');
        $areas  = $this->Area->find('list',array('fields' => array('slug','name')));
      
        $this->set(array(
            'prices'    => $prices,            
            'perPage'   => $this->perPage,
            'areas'     => $areas
        ));
    }
    
    public function add() {
        
        $this->breadcrum('add');
        
        $areas  = $this->Area->find('list',array('fields' => array('slug','name')));
        
        $this->set(array(
            'isEdit'    => FALSE,
            'areas'     => $areas
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Price->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            $this->Price->create();
            
            if($this->Price->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Price has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Price Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Price not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Price Add')
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
        
        $price  = $this->Price->findById($id);
        $areas  = $this->Area->find('list',array('fields' => array('slug','name')));
        
        if(empty($price)) {
            return $this->redirect(array('action' => 'index'));
        }
        $price['Price']['from_date'] = date('d.m.Y',  strtotime($price['Price']['from_date']));
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
        
        $this->request->data['Price']['id'] = $id;
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Price->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            $this->Price->create();
            
            if($this->Price->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Price has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Price Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Price not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Price Add')
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');  
            }
        }
    }
    
    private function processData() {
        $this->request->data['Price']['from_date'] = date('Y/m/d',strtotime(str_replace('/', '-', $this->request->data['Price']['from_date'])));
        if($this->request->data['Price']['type'] == 'area') {
            $this->request->data['Price']['name']   = NULL;
        } else {
            $this->request->data['Price']['area']   = NULL;
        }
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Price->delete($id)) {
            $this->Session->setFlash(__('The Price has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}