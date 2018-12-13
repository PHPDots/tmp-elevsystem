<?php

class TncsController extends AppController{
    
    public $uses    = array('Tnc','TncUser','User');
    
    public function beforeRender() {
        $url = str_replace("/elev-admin/", "", $this->here);
        
        if((strpos($url, 'admin') != '' || $this->Auth->user('role') != 'student') && $url != ''){
            if(!$this->request->is('ajax')){
                $this->layout = 'admin';
            }
        }else{
            if(!$this->request->is('ajax')){
                $this->layout = 'default';
            }
        }
    }
    
    private function breadcrum($case,$terms = array()){
        
        $pageTitle[] = array(
            'name'  => __('Terms And Conditions'),
            'url'   => Router::url(array('controller'=>'tncs','action'=>'index')),
        );
        
        switch ($case){
            
            case 'view':
                
                $pageTitle[] = array(
                    'name'  => $terms['Tnc']['title'],
                    'url'   => '#',
                );
                
                break;
            
            default: 
                
                
            
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
    }
   
    public function index(){
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        $pageTitle[] = array(
            'name'  => __('Terms And Conditions'),
            'url'   => Router::url(array('controller'=>'tncs','action'=>'index')),
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        
        $this->perPage  = $this->getPerPage('Tnc');
        $conditions     = array();
        
        if(in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))){           
            $conditions['TncUser.user_id']  = $this->currentUser['User']['id'];
            $conditions['TncUser.agree']    = 0;
        }
        
        $args = array(
            'fields'        => array('COUNT(TncUser.id) as total','Tnc.title','Tnc.created'),
            'limit'         => $this->perPage,
            'order'         => array('Tnc.id' => 'DESC'),
            'conditions'    => $conditions,               
            'joins'         => array(
                array(
                    'table'         => 'tnc_users',
                    'alias'         => 'TncUser',
                    'type'          => 'LEFT',
                    'foreignKey'    => FALSE,
                    'conditions'    => array(
                        'TncUser.tnc_id = Tnc.id'
                    )
                )
            ),
            'group'         => array('Tnc.id'),
            'recursive'     => 0
        );
        
        $this->Paginator->settings = $args;        
        
        $tncs       = $this->Paginator->paginate('Tnc');
        
        if(in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher')) && empty($tncs[0]['Tnc']['title'])){            
            $this->Session->setFlash(__('Sorry You have no access.'),'alert/error');
            return $this->redirect(array('controller' => 'pages','action' => 'home'));
        }
        
        $tncIds     = Hash::extract($tncs,'{n}.Tnc.id');
        $conditions = array();
        
        $conditions['TncUser.agree']     = 1;
        $conditions['TncUser.tnc_id']    = $tncIds;
        
        $args = array(
            'fields'        => array('COUNT(TncUser.id) as total','Tnc.title','Tnc.created'),
            'limit'         => $this->perPage,
            'order'         => array('Tnc.id' => 'DESC'),
            'conditions'    => $conditions,               
            'joins'         => array(
                array(
                    'table'         => 'tnc_users',
                    'alias'         => 'TncUser',
                    'type'          => 'LEFT',
                    'foreignKey'    => FALSE,
                    'conditions'    => array(
                        'TncUser.tnc_id = Tnc.id'
                    )
                )
            ),
            'group'         => array('Tnc.id'),
            'recursive'     => 0
        );
        
        $tncAgreedUserCount = $this->Tnc->find('all',$args);
       
        $tncAgreedUserCount = Hash::combine($tncAgreedUserCount,'{n}.Tnc.id','{n}.{n}.total');
       
        $this->set(array(
            'tncs'                  => $tncs,
            'perPage'               => $this->perPage, 
            'tncAgreedUserCount'    => $tncAgreedUserCount
        ));
    }        
    
    public function add(){
     
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        if($this->request->is('post')){
            
            $errorDetails   = $this->Tnc->validateData($this->request->data);
            
            if($errorDetails['status'] == 'error'){
                
                $this->set(array(
                    'error_msg'     => $errorDetails['error_msg']
                ));
                
                $this->layout = 'ajax';
                $this->render('Ajax/error');   
                
                return;
                
            }
            
            $this->processData();
            
            if($this->Tnc->saveAssociated($this->request->data)){
                $this->set(array(
                    'message'   => __('The Terms And Conditions Are Saved.'),
                    'status'    => 'success',                    
                ));

                $this->layout = 'ajax';
                $this->render('Ajax/success');   
            }
            
        }
         
    }
    
    private function processData(){
        
        $users  = $this->User->find('all',array(
            'fields'        => array('User.id'),
            'conditions'    => array(
                'User.role' => array('internal_teacher','external_teacher')
            )
        ));
         
        $users  = Hash::extract($users,'{n}.User.id');
        
        if(!empty($users)){
            foreach($users as $user){
                $this->request->data['TncUser'][]   = array(
                    'user_id'   => $user,
                    'agree'     => 0
                );
            }
        }       
    }
    
    public function view($id){
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        if(in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))){
            
            $tncUser    = $this->TncUser->findByTncIdAndUserId($id,$this->currentUser['User']['id']);
            
            if($tncUser['TncUser']['agree'] != 0 ){
                $this->Session->setFlash(__('Sorry You have no access.'),'alert/error');
                return $this->redirect(array('controller' => 'pages','action' => 'home'));
            }
            
             $this->layout   = "no-sidebar";
        }
        
        $terms  = $this->Tnc->findById($id);
        
        $this->breadcrum('view', $terms);
        
        $this->set(array(
            'terms'     => $terms,
            'iframe'    => (isset($this->request->query['iframe']) && !empty($this->request->query['iframe']) && ($this->request->query['iframe']))?$this->request->query['iframe']:''
        ));
        
    }
    
    public function updateTermUser(){
        
        if(empty($this->request->data)){
            $this->Session->setFlash(__('Sorry You have to Agree All The Updated Terms & Conditions. Then You can Proceed Further.'),'alert/error');
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
        }
        
        $flag   = FALSE;
        $userId = $this->currentUser['User']['id'];
        
        foreach ($this->request->data['term'] as $termId    => $value){
            if($value   == 'yes'){
                $this->TncUser->updateAll(array(
                    'TncUser.agree'    => 1
                ),array(
                    'TncUser.tnc_id'   => $termId,
                    'TncUser.user_id'  => $userId,
                ));
            }else{
                $flag   = TRUE;
            }
        }
        
        if($flag){
            $this->Session->setFlash(__('Sorry You have to Agree All The Updated Terms & Conditions. Then You can Proceed Further.'),'alert/error');
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
        }
        
        $this->Session->setFlash(__('You have Agreed Our Terms & Conditions.'),'alert/success');
        
        $url    = array('controller' => 'pages','action' => 'home');
        
        if(isset($this->request->query['iframe']) && !empty($this->request->query['iframe']) && ($this->request->query['iframe'])){
            $url    = array('controller' => 'bookings','action' => 'calendar');
            $url['?']   = array(
              'iframe'  =>   $this->request->query['iframe'],
            );
            return $this->redirect($url);            
        }else{
            return $this->redirect($url);
        }
        
        
    }
    
    public function userTerms(){
        
        $this->breadcrum('userTerms');
        
        $this->notifications    = $this->Tnc->notificationCount($this->currentUser['User']['id']);
        
        if(($this->notifications['count'][0] > 0) && (in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher')))){
            
            $this->layout   = "no-sidebar";
            
            $this->set(array(
               'notifications' =>  $this->notifications
            ));  
           
        }else{            
            $this->Session->setFlash(__('Sorry You have no access.'),'alert/error');
            return $this->redirect(array('controller' => 'pages','action' => 'home'));            
        }        
    }
    
    public function delete($id){
        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
        
        if(empty($id)){
            $this->redirect(array('action'=>'index'));
        }

        if ($this->Tnc->delete($id,$cascade = TRUE)) {            
            $this->Session->setFlash(__('The Terms & conditions is Deleted.'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
    }
    
}
