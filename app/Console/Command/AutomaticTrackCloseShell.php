<?php
/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */

class AutomaticTrackCloseShell extends AppShell {
    
    public $uses = array('Booking');
    
    public function main() {
        $this->Booking->checkAndCloseTrack();
    }
}