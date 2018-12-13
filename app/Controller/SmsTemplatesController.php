<?php
/**
 * 
 * @package OSS
 * @subpackage app.Controller.Project
 * @property EmailTemplate $EmailTemplate Email Template Model
 * 
 */
class SmsTemplatesController extends AppController {
    
    public $paginate;
    public $perPage     = 5;
    
    
    public function beforeRender() {        
        if($this->currentUser['User']['role'] != 'admin'){
           return $this->redirect(array('controller'   => 'pages','action'   => 'home'));
        }
    }
       
    public function smsMeta($key){
        
        if(!$this->request->is('ajax')){
            $this->redirect(array('action'=>'index'));
        }
        
        
        $templateMeta = $this->SmsTemplate->getTemplateMeta($key);
        
        $this->set('templateMeta',$templateMeta);
        
        $this->layout   = 'ajax';
        $this->render('Ajax/smsMeta');
        
        
    }
    
    public function index() {
        /*$fileds =  json_encode(array('message' => array('recipients' => '+919601516399', 'sender' => 'Køreskolen' ,'message' => 'hello')));
        $ch         = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, 'http://restapi.smsgateway.dk/v2/message.json?apikey=922e170d74a1c9e38176c74204902df1' );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileds);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $tmp_return = json_decode(curl_exec($ch));
        curl_close ($ch);

        echo '<pre>';
        print_r($tmp_return);
        echo '</pre>';
        exit();*/
        
        $this->perPage = $this->getPerPage('SmsTemplate');
        
        $this->paginate = array(
            'limit' => $this->perPage,
        );
        
        $this->Paginator->settings = $this->paginate;
        
        $smsTemplates = $this->Paginator->paginate('SmsTemplate');
        
        $this->set(array(
            'smsTemplates'       => $smsTemplates,
            'perPage'            => $this->perPage,
        ));
       
    }
    
    
    public function add() {
        
        if (!$this->request->is('post')) {
            return;
        }
        
       /*if(!$this->SmsTemplate->currentUserCan('sms_template_add')){
           $this->Session->setFlash(__('You have no Access'),'alert/error'); 
           return $this->redirect(array('controller' => 'pages','action' => 'home'));
       }*/
        
        $this->SmsTemplate->create();        
        if ($this->SmsTemplate->save($this->request->data)) {        
            $this->Session->setFlash(__('The SMS Template has been saved'),'alert/success');
            return $this->redirect(array('action' => 'index'));
        }else{
            $this->Session->setFlash(__('The SMS Template could not be saved. Please, try again.'),'alert/error');
        }
        
    }
    
    public function edit($id) {
        if(empty($id)){
            $this->redirect(array('action'=>'index'));
        }
        
       /*if(!$this->SmsTemplate->currentUserCan('sms_template_edit')){
           $this->Session->setFlash(__('You have no Access'),'alert/error'); 
           return $this->redirect(array('controller' => 'pages','action' => 'home'));
       }*/
        
        $projecttype = $this->SmsTemplate->findById($id);
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->SmsTemplate->id = $id;
           // $this->request->data['SmsTemplate']['body'] = mysql_real_escape_string($this->request->data['SmsTemplate']['body']);
            if ($this->SmsTemplate->save($this->request->data)) {
               $this->Session->setFlash(__('The SMS Template has been updated'),'alert/success');
                return $this->redirect(array('action' => 'edit',$id));
            }
            $this->Session->setFlash(__('The SMS Template could not be updated. Please, try again.'),'alert/error');
        }
        $this->view             = 'add';
        $this->request->data    = $projecttype;
    }
    
    
    public function delete($id) {
        
       /*if(!$this->SmsTemplate->currentUserCan('sms_template_delete')){
           $this->Session->setFlash(__('You have no Access'),'alert/error'); 
           return $this->redirect(array('controller' => 'pages','action' => 'home'));
       }*/
        
        if ($this->SmsTemplate->delete($id)) {
            $this->Session->setFlash(__('The SMS Template has been deleted'),'alert/success');
            return $this->redirect(array('action' => 'index'));
        }else{
            $this->Session->setFlash(__('There is some error while deleting the SMS template.'),'alert/error');
        }
        
        $this->view = 'index';
        
    }
    
    
     public function beforeFilter() {
        
        parent::beforeFilter();
        
        $this->SiteInfo->write('SmsTemplates'     , $this->SmsTemplate->getTemplates());
        

    }



}