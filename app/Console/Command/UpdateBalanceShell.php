<?php

/**
 *
 * @package Lisbeth
 * @subpackage app.Console.Command
 */

//App::uses('CakeEmail', 'Network/Email');
//setlocale(LC_ALL,"danish");

class UpdateBalanceShell extends AppShell
{

    public $uses = array('Systembooking', 'User', 'UserServices', 'LatestPayments');

    public function main()
    {
        $conditions['role'] = 'student';
        // $conditions['id'] = 2519;

        $Users = $this->User->find('all',
        array
        (
            'conditions'    => $conditions
        ));


        foreach ($Users as $key => $user)
        {
            $user_id = $user['User']['id'];
            $balance = $user['User']['balance'];
            $available_balance = $user['User']['available_balance'];
            $newBalace = $this->getUserAvailableBalance($user_id);
            // echo "\n".$user_id."-->".$newBalace;
            if($available_balance != $newBalace)
            {
                echo "\n".$user_id."-->".$newBalace;
                // exit;
                $student    = $this->User->findById($user_id);
                $this->User->id = $user_id;
                $this->User->saveField('available_balance', $newBalace);
            }            
        }
    }    

    function getUserAvailableBalance($userId)
    {
        $user = $this->User->findById($userId);
        $returnBalance = 0;

        if(!empty($user))
        {
            $Total_crm_in = 0;
            $gtotal = 0;
            $Balance = 0;
            $UserServices_total = 0;

            $conditions = array();
            $conditions['user_id'] = $userId;
            $UserServices  = $this->UserServices->find('all',
                             array
                             (
                                'conditions'   => $conditions,
                                'order'         => array('posting_date' => 'ASC'),
                                'group'         => array('id')
                             ));


            $conditions = array();
            $currentDate    = date('Y-m-d H:i:s',time());
            $conditions['student_id'] = $userId;
            $conditions[] = "status != 'delete'";
            $conditions[] = "status != 'approved'";
            $conditions[] = "status != 'unapproved'";
            $conditions[] = "status != 'passed'";
            $Systembooking = $this->Systembooking->find('all',
            array
            (
                'conditions'    => $conditions,
                'order'         => array('start_time' => 'ASC')
            ));  


            $student_number = $user['User']['student_number'];  

            $Payments = array();
            if(!empty($student_number))
            {
                $Payments = $this->LatestPayments->find('all', 
                            array
                            ( 'conditions' => array(
                                        'DebitorNummer' => $student_number
                            )));

                foreach ($Payments as $key => $Payment) 
                {
                    $Payment = (object)$Payment['LatestPayments'];
                    $Total_crm_in = $Total_crm_in + round($Payment->Kredit);
                }                
            }            

            foreach($UserServices as $UserService)
            {
                $total_price = number_format($UserService['UserServices']['total_price'], 2, '.', '');
                $UserServices_total +=  $total_price;
            }            

            foreach($Systembooking as $booking)
            {
                $type = ($booking['Systembooking']['lesson_type']  != '') ? $booking['Systembooking']['lesson_type'] : '1' ;
                $total =  $type*500;
                $gtotal = $gtotal + $total;
            }            

            if($Total_crm_in < 0){
                $Balance =  $Total_crm_in - $UserServices_total;
            }
            else
            {
                $Balance =  (-$Total_crm_in) + $UserServices_total;
            }

            $returnBalance = $Balance + $gtotal;
        }

        return $returnBalance;
    }

}
