<?php  

App::uses('WalkerHelper', 'View/Helper');

class CustomMenuWalkerHelper extends WalkerHelper {
 
   function start_el($element,$deapth,$has_child) {
       
       $isUserModule  = isset($this->myargs['isUserModule'])?isset($this->myargs['isUserModule']):'';
       $itemId        = explode('_',$element['itemId']) ;
       $this->output .= "<li oss-menu-id =\"{$itemId[1]}\">";
       $this->output .= $this->_View->Element('Menu/menuItem',array('element' => $element,'isUserModule' => $isUserModule));
       
   }    
   
   function end_el($element,$deapth,$has_child) {
       $this->output .= '</li>';       
   }
   
   function start_lvl($element,$deapth) {
       $this->output .= '<ol>';
   }
   
   function end_lvl($element,$deapth) {
       $this->output .= '</ol>';
   }
   
   function wrap_start() {
       $this->output .=  '<ol id="menuItemsList" class="sortable">';
   }
   
   function wrap_end() {
       $this->output .=  '</ol>';
   }
   
   function walk($elements, $args = array()) {
       $this->myargs = $args;  
       return parent::walk($elements, $args);
   }
}