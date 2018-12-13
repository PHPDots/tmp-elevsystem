<?php
/**
 * 
 * @package Lisbeth
 * @subpackage app.Console.Command
 */
App::uses('CakeEmail', 'Network/Email');
//mysql_set_charset('utf8');
class SendEmailShell extends AppShell {
    
    public $uses = array('EmailTemplate','EmailQueue');
    
    private function sendEmail($emailObject) {
        
        // $this->EmailSender
        //     ->from(array('noreply@lisbeth.org' => 'KTA Kolding'))
        //     ->to($emailObject['to'])
        //     ->subject($emailObject['subject'])
        //     ->addHeaders(array('content-type'=>'text/html'))
        //     ->emailFormat('html')
        //     ->send($emailObject['body']);
        
        return TRUE;   
    }

    public function main() {
        
        $this->EmailSender      = new CakeEmail();
        
        /**
         * Fetch the Email From the Queue 
         */
        $result['A']     = $this->EmailQueue->find('all',array(
            'conditions'    => array('EmailQueue.status'=> array('inqueue','fail')),
            'order'         => 'EmailQueue.priority ASC',           
        ));
        
        if(count($result['A'])==0)
            exit('No Email Schedule yet');
        /**
         * find the Relavent Templates from the datbase
         */
        $templates      = Hash::extract($result['A'],'{n}.EmailQueue.template');
        $result['B']    = $this->EmailTemplate->find('all',array(
            'conditions'    => array('EmailTemplate.template'=> $templates),
        ));
        
        $template       = Hash::combine($result['B'],'{n}.EmailTemplate.template','{n}');
        
        /**
         * Get Started With Sending the Email
         */
        foreach(Hash::extract($result['A'],'{n}.EmailQueue') as $emailObject){
            
            /**
             * Prepare the EmailObject
             */
            $flag = $this->sendEmail($this->EmailTemplate->emailData($emailObject['email'],$template[$emailObject['template']],$emailObject['data']));
            
            /**
             * Update the database Status of the Queue Element
             */
            $this->EmailQueue->updateAll(array(
                'EmailQueue.status' => "'sent'",
            ),array(
                'EmailQueue.id' => $emailObject['id'],
            ));
        }
    }
}