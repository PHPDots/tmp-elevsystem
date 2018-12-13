<?php

App::uses('CutomMenuAppModel', 'CustomMenu.Model');

class Menu extends CutomMenuAppModel {
    
    public $name        = 'Menu';
    
    function getMenuItems($menuId){
        
    
        $menu = $this->findByRoleSlug($menuId);
    
        $menu['Menu']['hierarchy']  = $menu['Menu']['menu_items'];  
   
        unset($menu['Menu']['menu_items']);
       
        $menuItems = json_decode($menu['Menu']['hierarchy']);
        
        foreach($menuItems as $menuItem){
            $displayMenuItems[]     = (array)$menuItem;
        }
     
        return $displayMenuItems;
    }

      
  /**
    * 
    * Check the user's capability for specific actions
    * 
    * @param capabity to check $capabilty ,$user_id of other user and $args for additional arguments
    * @return boolean
    */
    
    function currentUserCan($capability,$user_id = NULL,$args = array()){
        
        switch ($capability) {
            case 'menu_add':
                    return in_array($capability,$this->currentUserCapabilities)?TRUE:FALSE;
                break;
            case 'menu_edit':
                    return in_array($capability,$this->currentUserCapabilities)?TRUE:FALSE;
                break;                
        }
    }
}