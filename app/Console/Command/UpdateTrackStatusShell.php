<?php

/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */
App::uses('CakeEmail', 'Network/Email');

class UpdateTrackStatusShell extends AppShell {
    
    public $uses        = array('Booking');
    
    public function main() {
        $this->Booking->closeReopenTrack();
    }
}
