<?php

App::import('Component', 'RequestHandler');

class AreasController extends AppController
{
    public $uses = array('Area','AreaTimeSlot');
    
    public function beforeRender() {
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
        
    }
    
    private function breadcrum($case,$area = array()) {
        
        $pageTitle[] = array(
            'name'  => __('Areas'),
            'url'   => Router::url(array('controller'=>'areas','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $area['Area']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Area'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $this->breadcrum('index');
        
        $this->perPage  = $this->getPerPage('Area');
        
        $joins      = array();  
        
        $joins[]    = array(
            'table'         => 'tracks',
            'alias'         => 'Track',
            'type'          => 'LEFT',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                'Track.area_id = Area.slug'
            )
        );
        
        $args       = array(
            'fields'    => array('Area.name','Area.slug','Count(Track.id) as lane','Area.address'),
            'joins'     => $joins,
            'limit'     => $this->perPage,
            'order'     => array('Area.id' => 'DESC'),
            'group'     => array('Area.id Having lane >= 0')
        );
        
        $this->Paginator->settings = $args;
        
        $areas  = $this->Paginator->paginate('Area');
        
        $this->set(array(
            'areas'     => $areas,
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
        
        $this->Area->create();
      
        $this->processData();
        
        if($this->Area->saveAll($this->request->data)){

            $this->set(array(
                'message'   => __('Area is added.'),
                'status'    => 'success',
                'title'     => __('Area Add')
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/success');   
            return;
            
        }else{
            
            $errors = array();
            $errors_details = $this->Area->validationErrors;
            $timeSlotError = Hash::extract($errors_details,'AreaTimeSlot.{n}.time_slots.{n}');
            foreach($errors_details as $key=>$error) {
                if($key == 'AreaTimeSlot' ) {
                    $errors[] = array('key' => 'time_slot','message' => $timeSlotError[0]);
                } else{
                    $errors[] = array('key' => $key,'message' => $error[0]);
                }

            }
            $this->set(array(
                'error_msg'   => $errors
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/error'); 
            return;
        }
        
    }
    
    public function edit($id = NULL) {
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $area = $this->Area->findById($id);
        if(empty($area)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$area);
        
        if(!$this->request->data) {
            $this->request->data = $area;
        }
        
        $this->set(array(
            'isEdit'    => TRUE,
            'area'      => $area,
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Area']['id'] = $id;
       
        $this->processData();

        if($this->Area->updateArea($this->request->data)) {
            $this->set(array(
                'message'   => __('Area is Updated.'),
                'status'    => 'success',
                'title'     => __('Area Update')
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/success');   
           return;
           
        }else{        
            
            $errors = array();
            $errors_details = $this->Area->validationErrors;
            $timeSlotError = Hash::extract($errors_details,'AreaTimeSlot.{n}.time_slots.{n}');
            foreach($errors_details as $key=>$error) {
                if($key == 'AreaTimeSlot' ) {
                    $errors[] = array('key' => 'time_slot','message' => $timeSlotError[0]);
                } else{
                    $errors[] = array('key' => $key,'message' => $error[0]);
                }

            }
            $this->set(array(
                'error_msg'   => $errors
            ));

            $this->layout = 'ajax';
            $this->render('Ajax/error'); 
            return;
        }
    }
    
    private function processData(){
        if(count($this->request->data['AreaTimeSlot']) > 0){
            foreach($this->request->data['AreaTimeSlot'] as $key => $timeslots){
                $this->request->data['AreaTimeSlot'][$key]['time_slots']    = implode('-', $timeslots['time_slots']);
            }
        }elseif(count($this->request->data['AreaTimeSlot']) == 0){
            $this->request->data['AreaTimeSlot'][0]['time_slots']   = NULL;
        }
        
    }
    
    public function delete($id = NULL) {
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Area->delete($id)) {
            $this->Session->setFlash(__('The Area has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}