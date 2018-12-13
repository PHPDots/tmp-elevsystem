<?php  

App::uses('Helper', 'View');

class WalkerHelper extends AppHelper {
    
    public $elements;
    
    protected $output;
    protected $children_elements;
    protected $max_deapth;
    protected $idField;
    protected $parentIdField;

    function start_el($element,$deapth,$has_child){
        
    }
    
    function end_el($element,$deapth,$has_child){
        
    }
    
    function start_lvl($element,$deapth){
        
    }
    
    function end_lvl($element,$deapth){
        
    }
    
    function wrap_start(){
        
    }
    
    function wrap_end(){
        
    }
    
    function displayElement($element,$deapth,$args=array()){
        
        $has_child = (isset($this->children_elements[$element[$this->idField]]) && is_array($this->children_elements[$element[$this->idField]]));
      
        $this->start_el($element,$deapth,$has_child);
        
        
        if($has_child){
            
            $this->start_lvl($element,$deapth+1);
            
            foreach ($this->children_elements[$element[$this->idField]] as $element){
                $startlvl       = (!isset($startlvl))?TRUE:FALSE;
                $this->displayElement($element,$deapth+1);
            }
            
            $this->end_lvl($element,$deapth+1);
        }
        
        
        $this->end_el($element,$deapth,$has_child);
        
        
    }
    
    function walk($elements,$args = array()){
        
      
        $this->output = '';
        $default_args = array(
            'idField'           => 'id',
            'parentIdField'     => 'parent_id',
            'max_deapth'        => 0,
        );
        
        $args         = array_merge($default_args,$args);  
                
        $this->max_deapth           = $args['max_deapth'];
        $this->idField              = $args['idField'];
        $this->parentIdField        = $args['parentIdField'];
        $this->children_elements    = array();
       
        if(empty($elements) && !is_array($elements))
            return $this->output;
        
        $this->elements = $elements;
        
        foreach ($this->elements as $element){
            $this->children_elements[$element[$args['parentIdField']]][] = $element;
        }
       
        $this->wrap_start();
        
        if($args['max_deapth'] == -1){
            
            foreach($this->elements as $element){
                $startlvl       = (!isset($startlvl))?TRUE:FALSE;
                $this->displayElement($element,0,$startlvl);  
            }
           
        }else{
            
            $top_level_category_id = min(array_keys($this->children_elements));
            foreach ($this->children_elements[$top_level_category_id] as $element){
                $startlvl       = (!isset($startlvl))?TRUE:FALSE;
                $this->displayElement($element,0,$startlvl);
            }
        }
        
        $this->wrap_end();
        
        return $this->output;
        
        
    }

    
}