<?php
/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */
App::uses('CakeEmail', 'Network/Email');
//header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL,"danish");
class QueueSmsEmailShell extends AppShell {
    
    public $uses = array('Booking', 'User');
    
    public function main()
    {
    	$start_time = (float) array_sum(explode(' ',microtime()));

        echo "Start Time:==>".date('Y-m-d H:i:s')."<====\r\n";

        $dbo = $this->Booking->getDatasource();

    	$time = time();

        $this->Booking->bookingNotification();

        $this->Booking->bookingBeforeThreeHourNotification($time);

        $this->Booking->bookingBeforeTwentyfourHourNotification($time);

        $this->User->sendBalanceNotificationStudents();

		echo "Cron job completed";
        $end_time = (float) array_sum(explode(' ',microtime()));

        $logs = $dbo->getLog();
        print_r($logs);

        echo "\r\nEnd Time:==>".date('Y-m-d H:i:s')."<====";

        echo "\nScript Processing time: ". sprintf("%.4f", ($end_time-$start_time))." seconds\n";
    }
}