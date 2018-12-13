<?php
/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */
class ExpiredStudentAccountShell extends AppShell 
{    
    public $uses = array('User');
    public function main()
    {
    	$sqlUpdateQuery = "UPDATE users SET users.`is_completed` = 2 WHERE users.`expiry_date` IS NOT NULL AND users.`expiry_date` <= DATE(NOW());";
    	$this->User->query($sqlUpdateQuery);
    }
}