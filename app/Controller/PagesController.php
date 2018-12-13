<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {
    
/**
 * This controller does not use a model
 *
 * @var array
 */
	

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
    public $uses = array('Page','Booking','Systembooking','UserServices','LatestPayments');
    
    public function display($pageId = NULL) {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        $page = $subpage = $title_for_layout = null;
        $message = $this->get_notification_message();

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        } 

        try {     
            if($pageId == 'download'){
                $this->download($subpage);
                // die();
            }elseif($pageId == 'document'){
                $this->document();
                // die();
            }elseif($pageId != 'home') {
               $this->view($pageId);  
               $this->render('view');
                
            } else {
                $fields = array('CONCAT_WS(  " ", Booking.date, SUBSTRING_INDEX( BookingTrack.time_slot,  "-", 1 ) ) as book_date'); 
                $currentDate    = date('Y-m-d H:i',time());
                $bookings = $this->Booking->find('first',array(
                    'fields'         => $fields,
                    'joins'         => array(
                        array(
                            'table'     => 'booking_tracks',
                            'alias'     => 'BookingTrack',
                            'type'      => 'INNER',
                            'conditions'    => array(
                                'Booking.id = BookingTrack.booking_id'
                            )
                        )
                    ),
                    'conditions'    => array(
                        'student_id'   => $this->currentUser['User']['id'],
                        "CONCAT_WS(  ' ', Booking.date, SUBSTRING_INDEX( BookingTrack.time_slot,  '-', 1 ) ) >= '".$currentDate ."'",
                    ),
                    'order'         => array('Booking.date')
                ));
                if($this->currentUser['User']['role'] == 'student'){
                    $nextBookingCon['student_id']  = $this->currentUser['User']['id'];
                }else{
                    $nextBookingCon['user_id']  = $this->currentUser['User']['id'];
                }
                
                $nextBookingCon['status']  = 'pending'; 
                $nextBookingCon[] = "start_time >= '{$currentDate}'";
                $nextBooking        = $this->Systembooking->find('first',array(
                    'conditions'    => $nextBookingCon,
                    'order'         => array('start_time' => 'ASC')
                ));

                if(isset($bookings[0]['book_date']) && $bookings[0]['book_date'] < $nextBooking['Systembooking']['start_time']){
                    $bookings = $bookings[0]['book_date'];
                }else{
                    $bookings = $nextBooking['Systembooking']['start_time'];
                }


                $this->set(array('nextBooking' => $bookings, 'message' => $message,'availableBalance' => $availableBal));                
                $this->render(implode('/', $path));
            }
        } catch (MissingViewException $e) {           
            if (Configure::read('debug')) {
                    throw $e;
            }
            throw new NotFoundException();
        } 
        
        $this->set(compact('page', 'subpage', 'title_for_layout' ,'message'));
    }

    public function getUserAvailableBalance($userId)
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


    public function get_notification_message(){
        if($this->currentUser['User']['role'] == 'student'){
            $student_balance = $this->currentUser['User']['balance'];

            $userId = $this->currentUser['User']['id'];                                
            $student_balance = $this->getUserAvailableBalance($userId);            
            $balance = abs($student_balance);
            
            if ($student_balance <= 0) {
                //Din nuværende saldo er
               $message =  "Din nuværende saldo er: disponibel med ".$balance." kr.";
            }else{
                $joins[]    = array(
                                    'table'         => 'driving_lessons',
                                    'alias'         => 'DrivingLessons',
                                    'type'          => 'LEFT',
                                    'foreignKey'    => FALSE,
                                    'conditions'    => array(
                                                            'User.id = DrivingLessons.student_id'
                                                            )
                                    );
        
                $students = $this->User->find('all',
                    array(
                            'conditions'    => array(
                                'CAST(User.balance AS DECIMAL(10,2)) < 1000',
                                'CAST(User.balance AS DECIMAL(10,2)) >' => 0,
                                'User.id =' => $this->currentUser['User']['id']
                            ),
                            'fields'        => array('User.id','User.firstname','User.balance','User.phone_no','DrivingLessons.student_id','count(DrivingLessons.student_id) as no_of_driving_lessons'),
                            'joins'     => $joins,
                        ));

                $message = "Din nuværende saldo er: skyldig med ".$balance." kr.";

                if(count($students) > 0)
                {
                    if($students[0][0]['no_of_driving_lessons'] < 17 && $student_balance < 1000)
                    {
                        // $message = 'Your total balance is '.$student_balance.' amount. If you shall be abel to book more lessons, you will have to deposit more funds';
                        $message = 'Din nuværende saldo: Skyldig med '.$balance.' kr';
                    } 
                }
            }
    
        }else{
            $message = '';
        }
        return utf8_encode($message);
    }

    public function beforeRender(){
        
        if(($this->notifications['count'][0] > 0) && in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))){               
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
        }

        }

    private function breadcrum($case,$page = array()){
        
       /*$pageTitle[] = array(
           'name'  => __('Pages'),
           'url'   => Router::url(array('controller'=>'pages','action'=>'home')),
       );*/
        
        switch ($case){
            
            case 'view':
                
                $pageTitle[] = array(
                    'name'  => $page['Page']['title'],
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }

    public function document(){

        $this->perPage  = $this->getPerPage('Pages');
        
        $pageTitle[]    = array(
            'name'      => __('Dokumenter'),
            'url'       => Router::url(array('controller'=>'Pages','action'=>'document')),
        );
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
        $student_number = $this->currentUser['User']['student_number'];
        
        $category_code  = substr(trim($student_number), 10, -3);

        $args = array(
                    'conditions' => array('category_code' => $category_code),
                    );

        // $args = [];
        $this->Paginator->settings = $args;
       
        $documents       = $this->Paginator->paginate('Page');  
        
        $this->set(array(
            'documents'      => $documents,
            'perPage'       => $this->perPage,
        ));

        $this->render('documents');
    }
    
    public function view($id = NULL) {
        
        if (empty($id)) {
            $this->redirect(array('action' => 'home'));
        }
        $page = $this->Page->findBySlug($id);
        
        
        $this->breadcrum('view',$page);

        if (empty($page)) {
            return $this->redirect(array('action' => 'home'));
        }
        
        $this->set(array('page' => $page));
    }
}