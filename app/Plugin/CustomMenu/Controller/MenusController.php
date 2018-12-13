<?php
App::uses('CustomMenuAppController', 'CustomMenu.Controller');

class MenusController extends CustomMenuAppController {
    
    public $uses        = array('CustomMenu.Menu','Role');
    public $components  = array('CustomMenu.ControllerList');
    
    public function beforeRender(){
        // $this->layout = 'admin';
    }
    public function index($layout = NULL){
        $this->layout = 'admin';
        $isFancybox = FALSE;       
        if($layout == 'fancybox'){            
            $this->layout   = 'fancybox';
            $isFancybox     = TRUE;            
        }
        
        $menusList      = Hash::combine($this->Menu->find('all'),'{n}.Menu.id','{n}.Menu.name','{n}.Menu.object_type');        
        $controllerList = $this->ControllerList->getList();
        $roles          = Hash::combine($this->Role->find('all'),'{n}.Role.slug','{n}.Role.name');
        $this->set(array(
            'menuList'          => $menusList,
            'controllerList'    => $controllerList,            
            'isFancybox'        => $isFancybox,   
            'roles'             => $roles
        ));
        
        return ;
    }
    
    public function getMenu($id,$userId = NULL ,$type = ''){
        
       
        $menu   = $this->Menu->findById($id);
        $menu['Menu']['hierarchy']  = $menu['Menu']['menu_items'];       
        unset($menu['Menu']['menu_items']);
        $isEdit = FALSE;

        
        $this->set(array(
            'menu'      => $menu,
            'isEdit'    => $isEdit
        ));
        
        $this->layout = 'ajax';
        $this->render('Menus/Ajax/getMenu');
    }
    
    public function saveMenu(){
        
        
        if(!$this->request->is('ajax')){
             $this->redirect(array('action'=>'index'));
        }
        
        if($this->request->data['Menu']['action'] == 'update'){
           
            $this->processMenuData('update');
             
            if($this->Menu->save($this->request->data['Menu'])){
                $message = __('Menu saved successfully');
            }else{
                $message = __('Menu not saved successfully');
            }
            
        }else{
            
            $this->processMenuData();
           
            if($this->Menu->save($this->request->data['Menu'])){
                $message = __('Menu saved successfully');
            }else{
                $message = __('Menu not saved successfully');
            }
        }
        
        $this->set(array(
            'message'  => $message
        ));
        
        $this->layout = 'ajax';
        $this->render('Menus/Ajax/saveMenu');
         
         
    }
   
    public function edit($id){
        
        if(empty($id)){
            $this->redirect(array('action'=>'index'));
        }
        
        $menu = $this->Menu->findById($id);
        
        if (empty($menu)) {
           $this->Session->setFlash(__('Requested Menu Not Found.'),'alert/error');
           return $this->redirect(array('action' => 'index'));
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Menu->id = $id;
            if ($this->Menu->save($this->request->data)) {
                
                $data    = array(
                    'menu_id'       => $this->Menu->id,
                    'menu_name'     => $this->request->data['Menu']['name'],
                    'user_name'     => $this->currentUser['User']['firstname'] .' '. $this->currentUser['User']['lastname'],
                 ); 
                $this->Activity->logActivity($this->currentUser['User']['id'],'menu_edited',$data);
                $this->Session->setFlash(__('The Capability Group has been updated'),'alert/success');  
            }else{
                
                $this->Session->setFlash(__('The Capability Group could not be updated. Please, try again.'),'alert/error'); 
            }
        }
        
       if (!$this->request->data) {
            $this->request->data = $menu;
        }
        
    }
    
    public function delete($id){
        
         if(empty($id)){
            $this->redirect(array('action'=>'index'));
        }
        
        if ($this->Menu->delete($id)) {
            $this->Activity->logActivity($this->currentUser['User']['id'],'menu_deleted',array('menu_id' => $id));
            $this->Session->setFlash(__('The Menu has been deleted'),'alert/success'); 
            return $this->redirect(array('action' => 'index'));
        }
        
    }
    
    public function addMenuItem($id){
        
        $menu               = $this->Menu->findById($id);    
        $capability         = Hash::combine($this->Capability->find('all'),'{n}.Capability.key','{n}.Capability.name','{n}.Capability.capability_group_id');        
        $capabilityGroup    = Hash::combine($this->CapabilityGroup->find('all'),'{n}.CapabilityGroup.id','{n}.CapabilityGroup.name');
       
        foreach($capabilityGroup as $key => $val){
           $capability[$val]    = $capability[$key];
           unset($capability[$key]);
        }
        
        $parent = Hash::combine($this->MenuItem->findAllByMenuId($id),'{n}.MenuItem.id','{n}.MenuItem');
        
        $this->set(array(
            'menu'          => $menu,
            'capability'    => $capability,
            'parent'        => $parent
        ));
        
    }
    
    private function processMenuData($case = 'insert'){
        
        if($case == 'update'){
            $this->request->data['Menu']['id']      = $this->request->data['Menu']['menu_id'];
        }
        
        $this->request->data['Menu']['menu_items']  = $this->request->data['Menu']['hierarchy'];
  
        unset($this->request->data['Menu']['hierarchy']);
        unset($this->request->data['Menu']['action']);
        unset($this->request->data['Menu']['menu_id']);
        unset($this->request->data['Menu']['menuList']);
        unset($this->request->data['Menu']['department']);
        unset($this->request->data['Menu']['ministry']);
        unset($this->request->data['Menu']['province']);
        
    }
    
    public function additionalMenu(){
        
        
        $this->view   = 'index';
        
        $menusList = $this->Menu->find('list');
        
        $this->set(array(
            'menuList'          => $menusList,
            'controllerList'    => $controllerList,
            'departments'       => $departments,
            'ministries'        => $ministries,
            'provinces'         => $provinces,
            'isFancybox'        => TRUE,
        ));
        return ;
        
    }
}