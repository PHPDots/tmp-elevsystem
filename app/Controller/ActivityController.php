<?php

App::uses('AppController', 'Controller');

class ActivityController extends AppController {

    public $uses = array('Activity', 'EmailQueue');
    
    public function beforeRender(){
    }

    public function index(){
        $this->searchByDate();
    }

    private function __getLogCategory(){
        $results  = $this->Activity->find('all',array(
            'fields'        => array('DISTINCT(action)'),
            'order_by' => array('created', 'DESC')
        ));

        $return = array();

        foreach ($results as $result) {
            $action = $result['Activity']['action'];
            $title  = $this->Activity->getActivityDetail($action, ''); 
            $return[$action] = $title['title'];
        }

        return $return;
    }

    public function searchByDate(){
        $date = date('Y-m-d');

        $from_date = date('d.m.Y', strtotime($date . ' -7 days'));
        $to_date   = date('d.m.Y', strtotime($date));

        $this->set(array(
            'from_date' => $from_date,            
            'to_date'   => $to_date,
        ));
    }

    public function searchByStudent(){
        $date = date('Y-m-d');

        $from_date = date('d.m.Y', strtotime($date . ' -7 days'));
        $to_date   = date('d.m.Y', strtotime($date));

        $log_categories = $this->__getLogCategory();

        $this->set(array(
            'from_date'         => $from_date,            
            'to_date'           => $to_date,
            'log_categories'    => $log_categories
        ));
    }

    public function searchByTeacher(){
        $date = date('Y-m-d');

        $from_date = date('d.m.Y', strtotime($date . ' -7 days'));
        $to_date   = date('d.m.Y', strtotime($date));

        $log_categories = $this->__getLogCategory();

        $this->set(array(
            'from_date'         => $from_date,            
            'to_date'           => $to_date,
            'log_categories'    => $log_categories
        ));
    }

    public function getData(){
        ob_start();
        $this->autoRender = false;

        $type       = $this->request->query['type'];
        $date = date('Y-m-d');

        $from_date  = $this->request->query['from_date'];
        $to_date    = $this->request->query['to_date'];

        if(empty($from_date) || is_null($from_date)){
            $from_date = date('d.m.Y', strtotime($date . ' -7 days'));
        }

        if(empty($to_date) || is_null($to_date)){
            $to_date   = date('d.m.Y', strtotime($date));
        }

        if(strtotime($to_date) < strtotime($from_date)){
            $to_date   = date('d.m.Y', strtotime($date));
        }

        $table = '';

        if(!empty($type) && in_array($type, array('all', 'student', 'teacher'))){

            $results = false;

            if($type == 'all'){
                $results = $this->Activity->getDetailsByDate($from_date, $to_date);
                
            }

            if($type == 'student'){
                $student_id     = $this->request->query['student_id'];
                $activity_type  = $this->request->query['activity_type'];

                if(!empty($student_id)){
                    $results = $this->Activity->getDetailsByStudent($student_id, $activity_type, $from_date, $to_date);
                }
            }

            if($type == 'teacher'){
                $teacher_id     = $this->request->query['teacher_id'];
                $activity_type  = $this->request->query['activity_type'];

                if(!empty($teacher_id)){
                    $results = $this->Activity->getDetailsByTeacher($teacher_id, $activity_type, $from_date, $to_date);
                }                
            }

            if($results != false){

                foreach ($results as $result) {
                    $activity_details = $this->Activity->getActivityDetail($result['Activity']['action'], unserialize($result['Activity']['data']), $result[0]['from_user'], $result[0]['to_user']);

                    if(!empty($activity_details['title'])){
                        $table .= '<tr class="'. @$activity_details['tr_class'] .'">';
                            $table .= '<td>'. date('d-m-Y', strtotime($result['Activity']['created'])) . '<br />' . date('h:i:s A', strtotime($result['Activity']['created'])) .'</td>';
                            
                            $table .= '<td>'. $activity_details['title'] .'</td>';
                            
                            $table .= '<td>';
                                $table .= $activity_details['template'];

                                if(isset($activity_details['show_modal']) && $activity_details['show_modal'] == true){
                                    $url = Router::url(array('controller' => 'Activity', 'action' => 'getActivityForModal', $result['Activity']['id']));

                                    $table .= '<span class="pull-right"><a href="'. $url .'" data-target="#modal_view_modal" data-toggle="modal">'. __('View') .'</a></span>';
                                }
                            $table .= '</td>';

                        $table .= '</tr>';    
                    }
                }
            }

            ob_end_clean();
            echo json_encode(array('status' => 'success', 'from_date' => $from_date, 'to_date' => $to_date, 'table' => $table));
        } 
        else 
        {
            ob_end_clean();
            echo json_encode(array('status' => 'error', 'from_date' => $from_date, 'to_date' => $to_date, 'table' => ''));
            die();    
        }
    }

    public function getActivityForModal($id){
        $result = $this->Activity->findById($id);

        if(!empty($result) && count($result) > 0){
            $this->set(array('email' => $this->EmailQueue->buildEmailTemplate(unserialize($result['Activity']['data']))));
        } else {
            $this->set(array('email' => false));
        }

        $this->autoRender = false;
        $this->render('email_template_modal');
    }
}