<?php
App::uses('SmsSender'       , 'Lib/Sms');
/**
 * 
 * SMS Template
 *  
 * @subpackage app.Model
 * @property EmailTemplate $EmailTemplate Email Template Model
 */
class SmsQueue extends AppModel {
    
    public $name                = 'SmsQueue';
    public $default_args        = array(
        'data'      => array(),
        'priority'  => 1,
        'mobileno'  => '',
        'template'  => '',
        'instant'   => FALSE,
    );


    function __construct($id = false, $table = null, $ds = null) {
        
        parent::__construct($id, $table, $ds);
        
        $this->SmsSender        = new SmsSender(Configure::read('smsconfig'));
        $this->SmsTemplate      = ClassRegistry::init('SmsTemplate');
        $this->Booking          = ClassRegistry::init('Booking');
    }
    
    function afterFind($results, $primary = false) {
        
        
        if(count($results)==0){
            return $results;
        }
        
        for($i=0;$i<count($results);$i++){
            $results[$i]['SmsQueue']['data'] = unserialize($results[$i]['SmsQueue']['data']);
            
        }
        
        return $results;
    
    }
    
    function newUser($args){
        $args = array_merge($this->default_args,$args);
        
        $args['template']   = 'usercreate';
        $args['instant']    = TRUE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        $data = array(
            'mobileno'      => $args['mobileno'],
            'template'      => 'usercreate',
            'data'          => serialize($args['data']),
            'priority'      => $args['priority'],
        );
        
        /**
         * In Case of Instant Send the email Instantly
         */
        if($args['instant']){
            $template   = $this->SmsTemplate->findByTemplate($args['template']); 
            $flag       = $this->SmsSender->sms_body($args['mobileno'],$this->SmsTemplate->smsData($template,$args['data']));
        }
        
        /**
         * Perform the Save Operation for the Table
         */
        $this->save($data);
        
        
        return TRUE;
    }
    
    function bookingDetails($args){
        $args = array_merge($this->default_args,$args);
        
        $args['template']   = (!isset($args['template']) && empty($args['template']))?'bookingdetails':$args['template'];
        $args['instant']    = TRUE;
        /**
         *  Data to be Saved in the Database About the Email
         */
        $data = array(    
            'mobileno'      => $args['mobileno'],
            'template'      => $args['template'],
            'data'          => serialize($args['data']),
            'priority'      => $args['priority'],
        );
        
        /**
         * In Case of Instant Send the email Instantly
         */
        if($args['instant']){
            $data['status'] = 'sent';
            $template       = $this->SmsTemplate->findByTemplate($args['template']);
            //pr($this->SmsTemplate->smsData($template,$args['data']));
            $flag           = $this->SmsSender->sms_body($args['mobileno'],$this->SmsTemplate->smsData($template,$args['data']));
        }

        /**
         * Perform the Save Operation for the Table
         */
        
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function bookingReminder($args) {
        
        $args = array_merge($this->default_args,$args);
        
            //        $args['template']   = 'bookingdetails';
        $args['template']   = (isset($args['template']) && !empty($args['template'])) ? $args['template'] : 'bookingReminder';
        /**
         *  Data to be Saved in the Database About the Email
         */
        $data = array(    
            'mobileno'      => $args['mobileno'],
            'template'      => 'bookingReminder',
            'data'          => serialize($args['data']),
            'priority'      => $args['priority'],
        );
        if($args['instant']) {
            $data['status'] = 'sent';
            $template       = $this->SmsTemplate->findByTemplate($args['template']);
            $this->Booking->updateAll(array(
                'reminder_sent' => 1
            ),array(
                'Booking.id'    => $args['booking_id']
            ));
            $fields = array(
                'template'  => $template,
                'sms'       => $this->SmsTemplate->smsData($template,$args['data']),
            );
            //            $this->reportMail($fields,'reminderSms');
            //            pr(utf8_decode($this->SmsTemplate->smsData($template,$args['data'])));
            $flag   = $this->SmsSender->sms_body($args['mobileno'], utf8_decode($this->SmsTemplate->smsData($template,$args['data'])));
        }
        /**
         * Perform the Save Operation for the Table
         */
        $this->create();
        $this->save($data);
        
        return TRUE;
    }
    
    function externalBookingDetails($args){
        
        $args = array_merge($this->default_args,$args);
        
        $args['template']   = (isset($args['template']) && !empty($args['template'])) ? $args['template'] : 'externalbookingdetails';
        $args['instant']    = TRUE;
        
        /**
         *  Data to be Saved in the Database About the Email
         */
        
        $data = array(    
            'mobileno'      => $args['mobileno'],
            'template'      => 'externalbookingdetails',
            'data'          => serialize($args['data']),
            'priority'      => $args['priority'],
        );
        
        /**
         * In Case of Instant Send the email Instantly
         */
        
        if($args['instant']){
            $data['status'] = 'sent';
            $template       = $this->SmsTemplate->findByTemplate($args['template']);
        //            pr($this->SmsTemplate->smsData($template,$args['data']));
            $flag           = $this->SmsSender->sms_body($args['mobileno'],$this->SmsTemplate->smsData($template,$args['data']));
        }
         $data['status'] = 'sent';
        /**
         * Perform the Save Operation for the Table
         */
         
        $this->create();
        $this->save($data);        

        return TRUE;
    }

    function buildSMSTemplate($args){
        $args = array_merge($this->default_args,$args);
        
        $args['template'] = (!isset($args['template']) && empty($args['template'])) ? 'bookingdetails' : $args['template'];

        $template = $this->SmsTemplate->findByTemplate($args['template']);
         
        return $this->SmsTemplate->smsData($template, $args['data']);
    }
}