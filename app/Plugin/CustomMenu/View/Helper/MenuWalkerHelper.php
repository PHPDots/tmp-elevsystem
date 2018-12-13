<?php  

App::uses('WalkerHelper', 'View/Helper');

class MenuWalkerHelper extends WalkerHelper {
   
   var $helpers = array('Html');
    
   function start_el($element,$deapth,$has_child) {
       
       $addClass = ($has_child)?'dropdown':'';
       
       $this->output .= "<li class=\"{$element['cssclass']} {$has_child} {$addClass}\" >";
       
       
       if($element['linktype'] == 'internal'){
           $link          = explode(':',$element['link']);
           $parameter     = array('controller' =>  $link[0],'action' => $link[1],'plugin'   => FALSE);
           $generatedLink = $this->Html->url($parameter);
       }else{
           $generatedLink = $element['link'];
       }
       
       $this->output .= "<a href=\"{$generatedLink}\">";
       
       $name = $element['name'];
       if($has_child){
            $this->output .= "<span class='elementName'>{$name}</span>";
       }else{
            $this->output .= "{$name}";
       }
       
       
       if($has_child){
        $this->output .= "<span class=\"submenu-arrow\"></span>";
       }
       $this->output .= "</a>";
   }    
   
   function end_el($element,$deapth,$has_child) {
       $this->output .= '</li>';
   }
   
   function start_lvl($element,$deapth) {
       $this->output .= '<ul>';
   }
   
   function end_lvl($element,$deapth) {
       $this->output .= '</ul>';
   }
   
   function wrap_start() {
       $this->output .=  '<ul class="mainNav">';
   }
   
   function wrap_end() {
       $this->output .=  '</ul>';
   }
   
   function walk($elements, $args = array()) {
      
       $this->myargs = $args;  
       return parent::walk($elements, $args);
   }
}