<?php

class CompaniesController extends AppController{
    
    public $uses        = array('Company','City');

    private function breadcrum($case,$prices = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Companies'),
            'url'   => Router::url(array('controller'=>'companies','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => isset($prices['Company']['name'])?$prices['Company']['name']:'',
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Company'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle',__('Companies'));
        
        $this->perPage  = $this->getPerPage('Company');
        
        $args = array(
             'fields'        => array(
                        'Company.id','Company.name','Company.nick_name','Company.city_id','Company.status','City.city_code','City.name'
                    ),
            'joins' => array(
                                array(
                                    'table'         => 'cities',
                                    'alias'         => 'City',
                                    'type'          => 'LEFT',
                                    'conditions'    => array(
                                            'City.city_code = Company.city_id'
                                        )
                                    )
                        ),
            'limit' => $this->perPage,
            'order' => array('Company.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $companies    = $this->Paginator->paginate('Company');
      
        $this->set(array(
            'companies' => $companies,            
            'perPage'   => $this->perPage,
        ));
    }
    
    public function add() {
        
        $this->breadcrum('add');
        
        $city  = $this->City->find('list',array(
            'fields'        => array('city_code','name'),
        ));
        
        $this->set(array(
            'isEdit'    => FALSE,
            'city'    => $city,
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Company->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->Company->create();
            
            if($this->Company->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Company has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Company Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Company not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Company Add')
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
        
        $company = $this->Company->findById($id);
        if(empty($company)) {
            return $this->redirect(array('action' => 'index'));
        }

        $this->breadcrum('edit',$company);
        
        if(!$this->request->data) {
            $this->request->data = $company;
        }
        $city  = $this->City->find('list',array(
            'fields'        => array('city_code','name'),
        ));
        
        $this->set(array(
            'isEdit'    => TRUE,
            'company'   => $company,
            'city'   => $city,
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Company']['id'] = $id;
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Company->validateData($this->request->data,TRUE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            if($this->Company->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Company has been updated.'),
                    'status'    => 'success',
                    'title'     => __('Company Update'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Company not updated successfully'),
                    'status'    => 'success',
                    'title'     => __('Company Update')
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');  
            }
        }
    }
       
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Company->delete($id)) {
            $this->Session->setFlash(__('The Company has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}