<?php

class ProductsController extends AppController {
    
    public $uses    = array('StudentProduct','Product','Student');
    
    private function breadcrum($case,$prices = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Products'),
            'url'   => Router::url(array('controller'=>'products','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => isset($prices['Product']['name'])?$prices['Product']['name']:'',
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
        
        $conditions = array();
        $joins      = array();
        $student    = array();
        
        $this->SiteInfo->write('pageTitle',__('Products'));
        
        $this->perPage  = $this->getPerPage('Product');
        
        if(isset($this->request->query['student']) && !empty($this->request->query['student'])){
            $conditions['StudentProduct.student_id']    = $this->request->query['student'];
            $joins[]    = array(
                    'table'         => 'student_products',
                    'alias'         => 'StudentProduct',
                    'type'          => 'INNER',
                    'conditions'    => array(
                        'Product.id = StudentProduct.product_id'
                    )                
            );
            
            $student        = $this->User->findById($this->request->query['student']);
        }
        
        $args = array(
            'limit'         => $this->perPage,
            'conditions'    => $conditions,
            'joins'         => $joins,
            'order'         => array('Product.id' => 'DESC'),            
        );
        
        $this->Paginator->settings = $args;
        
        $products = $this->Paginator->paginate('Product');        
      
        $this->set(array(
            'products'  => $products,            
            'perPage'   => $this->perPage,
            'student'   => $student
        ));
    }
    
    public function add() {
        
        $this->breadcrum('add');
        
        $this->set(array(
            'isEdit'    => FALSE        
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Product->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->Product->create();
            
            if($this->Product->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Product has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Product Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Product not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Product Add')
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
        
        $product  = $this->Product->findById($id);
        
        if(empty($product)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$product);
        
        if(!$this->request->data) {
            $this->request->data = $product;
        }
        
        $this->set(array(
            'isEdit'    => TRUE            
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Product']['id'] = $id;
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Product->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            if($this->Product->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Product has been updated'),
                    'status'    => 'success',
                    'title'     => __('Product Update'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Product not updated successfully'),
                    'status'    => 'success',
                    'title'     => __('Product Update')
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
        
        if($this->Product->delete($id)) {
            $this->Session->setFlash(__('The Product has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }

    private function submitCRMdata($params)
    {
        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 900);
        ini_set('default_socket_timeout', 600);

        $wsdl = 'http://elevdata.jb-edb.dk/debitor/debitorservice.asmx?WSDL';

        $options = array
        (
            'uri'                => 'http://schemas.xmlsoap.org/soap/envelope/',
            'style'              => SOAP_RPC,
            'use'                => SOAP_ENCODED,
            'soap_version'       => SOAP_1_1,
            'cache_wsdl'         => WSDL_CACHE_NONE,
            'connection_timeout' => 15,
            'trace'              => true,
            'encoding'           => 'UTF-8',
            'exceptions'         => true,
        );

        try 
        {            
            $soap = new SoapClient($wsdl, $options);
            $data = $soap->CreateDebitorYdelse($params);
        } 
        catch(Exception $e)
        {            
            $data = $e->getMessage();
        }

        return $data;
    }        
    
    public function studentProduct(){
        
        if(isset($this->request->query['product']) && empty($this->request->query['product'])){
            $message    = array(
                'status'    => 'error',
                'message'   => __('Please Select product to add.')
            );
            
            $this->set('message',$message);
            
            $this->layout   = 'ajax';
            $this->render('Ajax/autoSuggetion');
            
            return;
        }
        
        $this->request->data['StudentProduct']  = array(
            'student_id'        => $this->request->query['student'],
            'product_id'        => $this->request->query['product']
        );  
        
        $this->StudentProduct->create();
        
        if($this->StudentProduct->save($this->request->data)){

            $student =  $this->User->findById($this->request->query['student']);
            $product_id = $this->request->query['product'];
            $product = $this->Product->findById($product_id);

            if(isset($product['Product']['id']))
            {                                
                $loginUserId = $this->currentUser['User']['id'];                
                $params = array();
                $ydelsesData = array();
                $amt = strval($product['Product']['price']);
                $params['KundeID'] = '2795cd76-0a62-4f1c-994b-f5bfbdbf24d1';
                $ydelsesData['Elevnummer'] = strval($student['User']['student_number']);
                $ydelsesData['AssistentNummer'] = strval($loginUserId);
                $current_time = date('Y-m-d H:i:s');
                $ydelsesData['PosteringsDato'] = date(DATE_ATOM,strtotime($current_time));
                $ydelsesData['Antal'] = 1;
                $ydelsesData['Pris'] = $amt;
                $ydelsesData['KontoNummer'] = strval($product['Product']['activity_number']);
                $params['ydelsesData'] = $ydelsesData;
                $data = $this->submitCRMdata($params);

                // echo "<pre>";
                // print_r($params);
                // echo "</pre>";
                // echo "<pre>";
                // print_r($data);
                // echo "</pre>";
                // exit;
            }                        


            $message    = array
            (
                'status'    => 'success',
                'message'   => __('Product added successfully.')
            );
            
            $this->set('message',$message);            
            $this->layout   = 'ajax';
            $this->render('Ajax/autoSuggetion');            
            return;
        }else{
            $message    = array(
                'status'    => 'error',
                'message'   => __('Sorry cannot add product. Please try again later.')
            );
            
            $this->set('message',$message);
            
            $this->layout   = 'ajax';
            $this->render('Ajax/autoSuggetion');
            
            return;
        }
        
    }
    
}
