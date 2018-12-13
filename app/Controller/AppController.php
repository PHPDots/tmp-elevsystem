<?php

App::uses('Controller'  , 'Controller');
App::import('Vendor'    , 'xtcpdf');
App::import('Vendor'    , 'autoload');

class AppController extends Controller {
    
    public $currentUser; 
    public $iframe;
    public $notifications;
    public $areaListArr;

    public $uses        = array('Role','User','ThemeOptions.Option','CustomMenu.Menu','Area','Tnc',
                                'LiveEdit', 'Activity','Option'); 
 // public $uses        = array('Role','User','Option','Tnc','Area'); 

    public $components  = array(  
        'Session',
        'Cookie',
        'SiteInfo',
        'Auth',
        // 'Auth' => array(                  
        //     'loginRedirect'     => array('controller' => 'pages', 'action' => 'home'),
        //     'logoutRedirect'    => array('controller' => 'users', 'action' => 'login')
        //  ),
        /*'AdminAuth' => array(                  
            'loginRedirect'     => array('controller' => 'adminpages', 'action' => 'home'),
            'logoutRedirect'    => array('controller' => 'adminusers', 'action' => 'login')
         ),   */
        'Paginator',
        'RequestHandler',        
    );
    
    public $helpers = array(        
        'Html'      => array('className' => 'MyHtml'),    
        'CustomMenu.MenuWalker'
    );
    
    
    public function beforeRender(){
       
        if($this->name == 'CakeError'){
            $this->layout   = 'error';
        }
    }
    public function prd($data='')
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }
    public function pr($data='')
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    public function beforeFilter() {
        if(!isset($_SESSION)) session_start();

        $url = str_replace("/elev-admin/", "", $this->here);

        $is_login_firsttime  = $this->Session->read("is_login_firsttime");
        
        if($this->Auth->user('role') == 'student' && $this->Auth->user('is_login_firsttime') == '1' && !in_array($this->request->params['action'], array('login','logout','signup','forgotPassword','resetPassword','changepassword')) && !$this->request->is('post') && ($is_login_firsttime == null || $is_login_firsttime == 0)){
            $this->Session->write("is_login_firsttime",0);
            $this->redirect(array(    'controller' => 'users','action'   => 'changepassword')); 
        }

        if($this->Auth->user('role') != 'student' && $this->Auth->loginRedirect == null){

            $this->Auth->loginRedirect =  array('controller' => 'adminpages', 'action' => 'home');
            $this->Auth->logoutRedirect =  array('controller' => 'adminusers', 'action' => 'login');
                                   
            $this->Auth->loginAction =  array(
                                                'controller' => 'adminusers',
                                                'action' => 'login',
                                                'plugin' => null
                                            );
            if(!$this->request->is('ajax') && !in_array($this->request->params['action'], array('login','signup','forgotPassword','resetPassword'))){
                $this->layout = 'admin';
            }
        }else{

            // $this->Auth->loginRedirect =  array('controller' => 'pages', 'action' => 'home');
            // $this->Auth->logoutRedirect =  array('controller' => 'users', 'action' => 'login');

            // $this->Auth->loginAction =  array(
            //                                     'controller' => 'users',
            //                                     'action' => 'login',
            //                                     'plugin' => null
            //                                 );

        }

        
        $this->iframe   = FALSE;
        
        if(isset($this->request->query['iframe']) && ($this->request->query['iframe'])){            
            $this->iframe   = TRUE;        
        }
        $this->areaListArr  = $this->Area->find('list',array('fields' => array('slug','name')));
        $this->SiteInfo->write('iframe'             , $this->iframe);
        $this->SiteInfo->write('options'                , $this->Option->getOptions());
        $this->SiteInfo->write('sitename'           , $this->Option->getOption('site_title'));
        $this->SiteInfo->write('currentUser'        , $this->User->findById($this->Auth->user('id')));
        $this->SiteInfo->write('perPageDropDown'    , Configure::read('perPageDropDown'));
        $this->SiteInfo->write('areaColors'         , $this->Area->find('all'));
        $this->SiteInfo->write('areaListArr'        , $this->areaListArr);
        
        $trackStatusArr = array(
            'all_tracks_booked'     => $this->Option->getOption('all_tracks_booked'),
            'some_tracks_booked'    => $this->Option->getOption('some_tracks_booked'),
            'no_tracks_booked'      => $this->Option->getOption('no_tracks_booked'),
        );
        $this->SiteInfo->write('trackStatusColors', $trackStatusArr);
        
        $this->currentUser  = $this->SiteInfo->read('currentUser');
        
        if(!empty($this->currentUser)){
            $userId = $this->currentUser['User']['id'];
            // print_r($this->currentUser['User']);
            if($this->currentUser['User']['role'] != 'student'){

            $this->SiteInfo->write('displayMenuItems'       , $this->Menu->getMenuItems($this->currentUser['User']['role']));             
            $this->notifications   = $this->Tnc->notificationCount($this->currentUser['User']['id']);

            //Live Edits Setting
            $curr_controller = strtolower($this->params['controller']);
            $curr_action = strtolower($this->params['action']);

            $live_edit = Configure::read('live_edits');
            $key = $curr_controller.'_'.$curr_action;

            if(array_key_exists($key, $live_edit)){
                //We will do something in future
            } else {
                $this->Session->delete('liveEdit');
                $is_exist = $this->LiveEdit->checkUserEditing($key,$userId,0);
                if($is_exist){
                   $this->LiveEdit->deleteAll(array('user_id'=>$userId,'form_type'=>'gettimebookings'));
                }
            }
            }
        }
        
        $this->Auth->authenticate = array('Custom');
        
        if($this->iframe){
            return $this->Auth->redirectUrl(array('controller' => 'bookings', 'action' => 'calendar' , '?' => array(
                'iframe'    => $this->iframe
            )));
        }
    }
    
    protected function getPerPage($modal){
        
        $perPage = 10;
        
        if(!empty($this->request->data[$modal]['perPage'])){
            $perPage  = $this->request->data[$modal]['perPage'];
            $this->Session->write("{$modal}.perPage",$this->request->data[$modal]['perPage']);
        }elseif ($this->Session->check("{$modal}.perPage")) {
            $perPage  = $this->Session->read("{$modal}.perPage");
        }else{
            $perPage  = 10;
        }
        
        return $perPage;
    }

    public function insertLog($type, $data, $to_id = 0){
        $this->Activity->create();
        
        $activity = array();

        $activity['from_id']      = $this->currentUser['User']['id'];
        $activity['to_id']        = $to_id;
        $activity['action']       = $type;
        $activity['data']         = serialize($data);

        $this->Activity->save($activity);

        return true;
    }
}