<?php
/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */

App::uses('SmsSender'       , 'Lib/Sms');

//mysql_set_charset('utf8');

class SendSmsDrivingLessonsShell extends AppShell {
    
    public $uses = array('SmsTemplate','SmsQueue','User');
    
    public function main() {
        
        $joins[]    = array(
            'table'         => 'driving_lessons',
            'type'          => 'LEFT',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                'User.id = driving_lessons.student_id'
            )
        );
        $joins[]    = array(
            'table'         => 'systembookings',
            'type'          => 'LEFT',
            'foreignKey'    => FALSE,
            'conditions'    => array(
                'User.id = systembookings.student_id'
            )
        );
        $db = $this->User->getDataSource();
        $db->fullDebug = true;
        $students = $this->User->find('all',array(
            'fields'        => array('User.id','User.firstname','User.phone_no',
                                    'COUNT(distinct driving_lessons.id) as no_of_driving_lessons',
                                    'DATEDIFF(NOW(),MAX(driving_lessons.start_time)) as last_drivinglessons',
                                    'MAX(driving_lessons.`start_time`) last_driving_lessons_date',
                                    'COUNT(distinct systembookings.id) as no_of_driving_test',
                                    ),
            'joins'     => $joins,
            'group' => array(
                        'User.id HAVING no_of_driving_lessons >= 0 AND last_drivinglessons > 60 AND no_of_driving_test <= 0',
                    )
        ));

        $log = $db->getLog();
        print_r($log);
        print_r($students);
        // die();

        if( count($students) > 0 ){
            $this->SmsQueue             = ClassRegistry::init('SmsQueue');
            
            foreach ($students as $data) {
                if(isset($data['User']['phone_no']) && $data['User']['phone_no'] != ''){
                    $this->SmsQueue->bookingDetails(
                        array(
                            'data' => array(
                                
                            ),
                            'mobileno'      => (substr($data['User']['phone_no'], 0, 2) == '45') ? '+' . $data['User']['phone_no'] : '+45'. $data['User']['phone_no'],
                            'template'      => 'studentnonewdrivinglessonslast60day',
                            'priority'      => 0,
                            'instant'       => TRUE
                        )
                    );
                }
            }
        } else {
            echo 'No Students <br />';
        }

        echo 'Complete Queue';
        die();
        
    }
}