<?php

App::uses('WalkerHelper', 'View/Helper');

class SelectWalkerHelper extends WalkerHelper {
    
    private $myargs;


    function start_el($element,$deapth,$has_child) {
       
       $value = isset($this->myargs['value'])?$this->myargs['value']:''; 
       if($value == $element['id'] && !empty($value)){
           $this->output .= '<option value="' . $element['id'] . '" selected="selected">';
       }else{
           $this->output .= '<option value="' . $element['id'] . '">';
       }
       $this->output .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $deapth);
       if(isset($this->myargs['lang']) && ($this->myargs['lang'] == 'ara')){
           $this->output .= $element['ara_name'];
       }else{
           $this->output .= $element['name'];
       }
       
   }    
   
   function end_el($element,$deapth,$has_child) {
       $this->output .= '</option>';
   }
   
   function start_lvl($element,$deapth) {
       $this->output .= '';
   }
   
   function end_lvl($element,$deapth) {
       $this->output .= '';
   }
   
   function wrap_start() {
       $value = isset($this->myargs['value'])?$this->myargs['value']:'';
       $class = isset($this->myargs['class'])?$this->myargs['class']:'';    
       $id    = isset($this->myargs['id'])?$this->myargs['id']:'';
       $this->output .=  '<select name="'  . $this->myargs['name'] . '" class="' . $class . '" value="' . $value . '" id="'. $id .'">';
       $this->output .= '<option value="" >'.$this->myargs['empty'].'</option>';
   }
   
   function wrap_end() {
       $this->output .=  '</select>';
   }
   
   function walk($elements, $args = array()) {
      
       $this->myargs = $args;  
       return parent::walk($elements, $args);
   }
   
}