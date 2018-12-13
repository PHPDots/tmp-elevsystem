<?php

/**
 *
 * @package Lisbeth
 * @subpackage app.Console.Command
 */
App::uses('AuthComponent'   , 'Controller/Component');

//App::uses('CakeEmail', 'Network/Email');
//setlocale(LC_ALL,"danish");

class PasswordShell extends AppShell
{

    public $uses = array('Booking', 'User','Activity' ,'UserServices');

    public function main()
    {

        $users = $this->User->find('all', array(
                                'conditions' => array(
                                    'User.role' => 'student',
                                    // 'User.id'   => '2460'
                                 )));
        $i=0;
        foreach ($users as $key => $user) {

            $user = $user['User'];
            $phone_no = $user['phone_no'];
            $id = $user['id'];
            $password = $user['password'];
            $old_hash_pwd = AuthComponent::password(md5($phone_no));
            // echo $old_hash_pwd ."==". $password;
            if($old_hash_pwd == $password){
                echo "\n";
                echo $id;
                $i++;
                $new_hash_pwd = $phone_no;
                $student    = $this->User->findById($id);
                echo  $this->User->id = $id;
                echo "\n";
                $this->User->saveField('password', $new_hash_pwd);
            }

        }

        echo "\n";
        echo $i++;
    }

}
