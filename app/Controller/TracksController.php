<?php

App::import('Component', 'RequestHandler');

class TracksController extends AppController
{  
    public $uses = array('Track','Area');
    
    public function beforeRender() {
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
    }
    
    private function breadcrum($case,$track = array()){
        
        $pageTitle[] = array(
            'name'  => __('Tracks'),
            'url'   => Router::url(array('controller'=>'tracks','action'=>'index')),
        );
        
        switch ($case){
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $track['Track']['name'],
                    'url'   => '#',
                );
                
                break;
            
            case 'add':
                $pageTitle[] = array(
                    'name'  => __('Add Track'),
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function index() {
        
        $conditions = array();
        
        $this->perPage  = $this->getPerPage('Track');
        
        if(isset($this->request->query['area']) && !empty($this->request->query['area'])){
            $conditions['Track.area_id']    = $this->request->query['area'];
            $area                           =   $this->Area->findBySlug($this->request->query['area']);
            
            $pageTitle[]  = array(
                'name'  => $area['Area']['name'],
                'url'   => Router::url(array('controller' => 'areas','action'   => 'index'))
            );
        }
        
        $pageTitle[]  = array(
            'name'  => __('Tracks'),
            'url'   => Router::url(array('controller' => 'tracks','action'   => 'index'))
        );
        
        $this->SiteInfo->write('pageTitle','Tracks');
        
        $args = array(
            'limit'         => $this->perPage,
            'conditions'    => $conditions,
            'order'         => array('Track.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $tracks  = $this->Paginator->paginate('Track');
        
        $areas  = Hash::combine($this->Area->find('all'),'{n}.Area.slug','{n}.Area.name');
        $this->set(array(
            'tracks'    => $tracks,            
            'perPage'   => $this->perPage,
            'areas'     => $areas,
        ));
    }
    
    public function add() {
        
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        $this->breadcrum('add');
        
        $areas  = Hash::combine($this->Area->find('all'),'{n}.Area.slug','{n}.Area.name');
        $this->set(array(
            'isEdit'    => FALSE,
            'areas'     => $areas,
        ));
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->Track->create();
        
        if($this->Track->save($this->request->data)) {
            $this->Session->setFlash(__('The Track has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Track could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function edit($id = NULL) {
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        if(is_null($id)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $track = $this->Track->findById($id);
        if(empty($track)) {
            return $this->redirect(array('action' => 'index'));
        }
        
        $this->breadcrum('edit',$track);
        
        if(!$this->request->data) {
            $this->request->data = $track;
        }
        
        $areas  = Hash::combine($this->Area->find('all'),'{n}.Area.slug','{n}.Area.name');
        
        $this->set(array(
            'isEdit'    => TRUE,
            'areas'     => $areas,
            'track'     => $track,
        ));
        
        $this->render('add');
        
        if(!$this->request->is('post')) {
            return;
        }
        
        $this->request->data['Track']['id'] = $id;
        
        if($this->Track->save($this->request->data)) {
            $this->Session->setFlash(__('The Track has been saved'), 'alert/success');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The Track could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function delete($id = NULL) {
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        if(is_null($id)) {
            return $this->redirect(array('aciton' => 'index'));
        }
        
        if($this->Track->delete($id)) {
            $this->Session->setFlash(__('The Track has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
}