<?php

class CoursesController extends AppController{
    
    private function breadcrum($case,$prices = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Courses'),
            'url'   => Router::url(array('controller'=>'courses','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => isset($prices['Course']['name'])?$prices['Course']['name']:'',
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Course'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->SiteInfo->write('pageTitle',__('Courses'));
        
        $this->perPage  = $this->getPerPage('Course');
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Course.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $courses    = $this->Paginator->paginate('Course');
      
        $this->set(array(
            'courses'   => $courses,            
            'perPage'   => $this->perPage,
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
            $errorDetails   = $this->Course->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->Course->create();
            
            if($this->Course->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Course has been submitted'),
                    'status'    => 'success',
                    'title'     => __('Course Add'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Course not submitted successfully'),
                    'status'    => 'success',
                    'title'     => __('Course Add')
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
        
        $course = $this->Course->findById($id);
        $areas  = $this->Area->find('list',array('fields' => array('slug','name')));
        
        if(empty($course)) {
            return $this->redirect(array('action' => 'index'));
        }

        $this->breadcrum('edit',$course);
        
        if(!$this->request->data) {
            $this->request->data = $course;
        }
        
        $this->set(array(
            'isEdit'    => TRUE,
            'areas'     => $areas,
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Course']['id'] = $id;
        
        if($this->request->is('post')) {
            $errorDetails   = $this->Course->validateData($this->request->data,FALSE);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            if($this->Course->save($this->request->data)) {
                $this->set(array(
                    'message'   => __('The Course has been updated.'),
                    'status'    => 'success',
                    'title'     => __('Course Update'),
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');
            } else {
       
                $this->set(array(
                    'message'   => __('The Course not updated successfully'),
                    'status'    => 'success',
                    'title'     => __('Course Update')
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
        
        if($this->Course->delete($id)) {
            $this->Session->setFlash(__('The Course has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}