<?php

/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */
App::uses('CakeEmail', 'Network/Email');

class UpdateUserShell extends AppShell {
    
    public $uses        = array('User');
    
    public function main() {
        
        $this->User->updateSystemUsers();
      
    }
    
}
