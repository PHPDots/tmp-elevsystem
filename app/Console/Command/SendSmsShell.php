<?php
/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */

App::uses('SmsSender'       , 'Lib/Sms');

//mysql_set_charset('utf8');

class SendSmsShell extends AppShell {
    
    public $uses = array('SmsTemplate','SmsQueue');
    
    public function main() {
        
        $this->smsSender        = new SmsSender(Configure::read('smsconfig'));        
        
        /**
         * Fetch the Email From the Queue 
         */
        $result['A']     = $this->SmsQueue->find('all',array(
            'conditions'    => array('SmsQueue.status'=> array('inqueue','fail')),
            'order'         => 'SmsQueue.priority ASC',            
        ));
        
      
        if(count($result['A'])==0)
            exit('No Sms Schedule yet');
        /**
         * find the Relavent Templates from the datbase
         */
        $templates      = Hash::extract($result['A'],'{n}.SmsQueue.template');
        
        $result['B']    = $this->SmsTemplate->find('all',array(
            'conditions'    => array('SmsTemplate.template'=> $templates),
        ));
        
        $template       = Hash::combine($result['B'],'{n}.SmsTemplate.template','{n}');
        
        
        /**
         * Get Started With Sending the Email
         */
        foreach(Hash::extract($result['A'],'{n}.SmsQueue') as $smsObject){
            
            /**
             * Prepare the EmailObject
             */
            $flag = $this->smsSender->sms_body($smsObject['mobileno'], $this->SmsTemplate->smsData($template[$smsObject['template']], $smsObject['data']));
            
            /**
             * Update the database Status of the Queue Element
             */
            $this->SmsQueue->updateAll(array(
                'SmsQueue.status' => "'sent'",
            ),array(
                'SmsQueue.id' => $smsObject['id'],
            ));
        }
    }
}