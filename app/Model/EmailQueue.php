<?php
App::uses('CakeEmail', 'Network/Email');

class EmailQueue extends AppModel {
    
    public $name                = 'EmailQueue';
    public $emailTemplate       = NULL;
    public $default_args        = array(
        'data'      => array(),
        'priority'  => 1,
        'email'     => '',
        'template'  => '',
        'instant'   => FALSE,
    );
    
    function __construct($id = false, $table = null, $ds = null) {
        
        parent::__construct($id, $table, $ds);
        
        $this->EmailSender      = new CakeEmail();
        $this->EmailTemplate    = ClassRegistry::init('EmailTemplate');
        $this->Booking          = ClassRegistry::init('Booking');
        
    }
    
    function afterFind($results, $primary = false) {
        
        
        if(count($results)==0){
            return $results;
        }
        
        for($i=0;$i<count($results);$i++){
            $results[$i]['EmailQueue']['data'] = unserialize($results[$i]['EmailQueue']['data']);
            
        }
        
        return $results;
    
    }
    
    
    /**
     * Basic Usage example
     * 
     *<code><pre>
     * 
     * $args = array(
     *  'email'         => 'anand@ blackid.com',
     *  'data'          => array(
     *      'User'          => array(
     *          &nbsp;  'firstName'         => 'Anand',
     *          &nbsp;  'lastName'          => 'Thakkar',
     *          &nbsp;  'username'          => 'anand.blackid',
     *          &nbsp;  'password'          => 'anand.blackid',
     *          &nbsp;  'emailid'           => 'anand@blackid.com',     
     *      )
     *  )
     * );
     * 
     *</pre></code>
     *
     * @param array $args 
     * @return boolean
     */
     
    function newUser($args){
        
        $args = array_merge($this->default_args,$args);
        
        $args['template']   = 'usercreate';
        $args['instant']    = TRUE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'usercreate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        
        /**
         * In Case of Instant Send the email Instantly
         */
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']);
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $this->save($data);
        
        return TRUE;
    }
    function testMail($args) {
        $emailObject = array(
            'to'        => $args['email'],
            'subject'   => $args['subject'],
            'body'      => $args['data'],
        );
        $this->sendEmail($emailObject);
    }
    
    function addBooking($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = (!isset($args['template']) && empty($args['template']))?'addbooking':$args['template'];
        $args['instant']    = TRUE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => $args['template'],
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        /**
         * In Case of Instant Send the email Instantly
         */
        if($args['instant']){
            $data['status'] = 'sent';
            $template   = $this->EmailTemplate->findByTemplate($args['template']);
//            pr($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
            
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function deleteBooking($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = (!isset($args['template']) && empty($args['template']))?'deletebooking':$args['template'];
        $args['instant']    = TRUE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => $args['template'],
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        /**
         * In Case of Instant Send the email Instantly
         */
        if($args['instant']){
            $data['status'] = 'sent';
            $template   = $this->EmailTemplate->findByTemplate($args['template']);
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
            
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function bookingReminder($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'bookingReminder';
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'bookingReminder',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        if($args['instant']){
            $data['status'] = 'sent';
            $template   = $this->EmailTemplate->findByTemplate($args['template']);
            $this->Booking->updateAll(array(
                'reminder_sent' => 1
            ),array(
                'id'    => $args['booking_id']
            ));
            $fields = array(
                'template'  => $template,
                'email'     => $this->EmailTemplate->emailData($args['email'],$template,$args['data']),
            );
//            $this->reportMail($fields,'reminderEmail');
//            pr($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
            $flag   = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        /**
         * Perform the Save Operation for the Table
         */
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    
     /**
     * Basic Usage example
     * 
     *<code><pre>
     * 
     * $args = array(
     *  'email'         => 'anand@ blackid.com',
     *  'data'          => array(
     *      'User'          => array(
     *          &nbsp;  'firstName'         => 'Anand',
     *          &nbsp;  'lastName'          => 'Thakkar',
     *          &nbsp;  'username'          => 'anand.blackid',
     *          &nbsp;  'activationlink'    => 'https://www.google.co.in/#q=test',
     *      )
     *  )
     * );
     * 
     *</pre></code>
     *
     * @param array $args 
     * @return boolean
     */
    
    function forgotPasswordEmail($args){
        
        $args = array_merge($this->default_args,$args);
        
        $args['template']   = 'forgotpassword';
        $args['instant']    = TRUE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        $data = array(
            'email'         => $args['email'],
            'template'      => 'forgotpassword',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        /**
         * In Case of Instant Send the email Instantly
         */
       
        if($args['instant']){            
            $template   = $this->EmailTemplate->findByTemplate($args['template']);            
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));            
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        //$this->emailTemplate->gete
        
        return TRUE;
    }
    
    function newStudentDrivingLesson($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'studentDrivingLessonTemplate';
        $args['instant']    = FALSE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'studentDrivingLessonTemplate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']); 
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function newTeacherDrivingLesson($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'teacherDrivingLessonTemplate';
        $args['instant']    = FALSE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'teacherDrivingLessonTemplate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']); 
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function newUpdateStudentDrivingLesson($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'updateStudentDrivingLessonTemplate';
        $args['instant']    = FALSE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'updateStudentDrivingLessonTemplate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']); 
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function newUpdateTeacherDrivingLesson($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'updateTeacherDrivingLessonTemplate';
        $args['instant']    = FALSE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'deleteStudentDrivingLessonTemplate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']); 
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function newDeleteStudentDrivingLesson($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'updateStudentDrivingLessonTemplate';
        $args['instant']    = FALSE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'deleteStudentDrivingLessonTemplate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']); 
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function newDeleteTeacherDrivingLesson($args){
        
        $args = array_merge($this->default_args,$args);
        $args['template']   = 'deleteTeacherDrivingLessonTemplate';
        $args['instant']    = FALSE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(
            'email'         => $args['email'],
            'template'      => 'deleteTeacherDrivingLessonTemplate',
            'data'          => serialize($args['data']),
            'priority'      => 0,
        );
        
        if($args['instant']){
            $template   = $this->EmailTemplate->findByTemplate($args['template']); 
            $flag       = $this->sendEmail($this->EmailTemplate->emailData($args['email'],$template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $data['status'] = 'sent';
        $this->create();
        $this->save($data);
        
        return TRUE;
    }

    function buildEmailTemplate($args){

        $template   = $this->EmailTemplate->findByTemplate($args['template']);
        
        return $this->EmailTemplate->emailData($args['email'], $template, $args['data']);
    }
    
    private function sendEmail($emailObject){
        
        $this->EmailSender
            ->from(array('noreply@in-demo.dk' => 'KTA Kolding')) 
            ->to($emailObject['to'])
            ->subject($emailObject['subject'])
            ->addHeaders(array('MIME-Version'=>'1.0'))
            ->addHeaders(array('content-type'=>'text/html'))
            ->emailFormat('html')    
            ->send($emailObject['body']);
   
        return TRUE;       
    }
}