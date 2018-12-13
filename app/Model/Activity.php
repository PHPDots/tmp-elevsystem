<?php

App::uses('AppModel'        , 'Model');

class Activity extends AppModel {

	function __construct(){
		parent::__construct();
		$this->sms   	= ClassRegistry::init('SmsQueue');
		$this->Booking 	= ClassRegistry::init('Booking');
		$this->City 	= ClassRegistry::init('City');
	}

	public function getDetailsByDate($from_date, $to_date){
		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));

		$results  = $this->find('all',array(
			'fields'        => array('Activity.id', 'Activity.action', 'Activity.data', 'CONCAT(FromUser.firstname, " ", FromUser.lastname) AS from_user', 'CONCAT(ToUser.firstname, " ", ToUser.lastname) AS to_user', 'Activity.created'),
            'conditions'    => array(
                'date(Activity.created) >='  => $from_date,
                'date(Activity.created) <='  => $to_date,
            ),
            'joins'         => array(
                array(
                    'table'         => 'users',
                    'alias'         => 'FromUser',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'FromUser.id = Activity.from_id'
                    )),
               	array(
                    'table'         => 'users',
                    'alias'         => 'ToUser',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'ToUser.id = Activity.to_id'
                    ))
            ),
            'order' => array('created DESC')
        ));
		//print_r($results);die();
        if(count($results) > 0){
        	return $results;
        } else {
        	return false;
        }
	}

	public function getDetailsByStudent($student_id, $activity_type, $from_date, $to_date){
		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));

		$conditions = array();

		$conditions['date(Activity.created) >='] = $from_date;
		$conditions['date(Activity.created) <='] = $to_date;
		$conditions['Activity.to_id'] = $student_id;

		if(!empty($activity_type) && $activity_type != 'all'){
			$conditions['action'] = $activity_type;			
		}

		$results  = $this->find('all',array(
			'fields'        => array('Activity.id', 'Activity.action', 'Activity.data', 'CONCAT(FromUser.firstname, " ", FromUser.lastname) AS from_user', 'CONCAT(ToUser.firstname, " ", ToUser.lastname) AS to_user', 'Activity.created'),
            'conditions' => $conditions,
            'joins'         => array(
                array(
                    'table'         => 'users',
                    'alias'         => 'FromUser',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'FromUser.id = Activity.from_id'
                    )),
               	array(
                    'table'         => 'users',
                    'alias'         => 'ToUser',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'ToUser.id = Activity.to_id'
                    ))
            ),
            'order' => array('created DESC')
        ));

        if(count($results) > 0){
        	return $results;
        } else {
        	return false;
        }
	}

	public function getDetailsByTeacher($teacher_id, $activity_type, $from_date, $to_date){
		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));

		$conditions = array();

		$conditions['date(Activity.created) >='] = $from_date;
		$conditions['date(Activity.created) <='] = $to_date;
		$conditions['Activity.from_id'] = $teacher_id;

		if(!empty($activity_type) && $activity_type != 'all'){
			$conditions['action'] = $activity_type;			
		}

		$results  = $this->find('all',array(
			'fields'        => array('Activity.id', 'Activity.action', 'Activity.data', 'CONCAT(FromUser.firstname, " ", FromUser.lastname) AS from_user', 'CONCAT(ToUser.firstname, " ", ToUser.lastname) AS to_user', 'Activity.created'),
            'conditions' => $conditions,
            'joins'         => array(
                array(
                    'table'         => 'users',
                    'alias'         => 'FromUser',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'FromUser.id = Activity.from_id'
                    )),
               	array(
                    'table'         => 'users',
                    'alias'         => 'ToUser',
                    'type'          => 'LEFT',
                    'conditions'    => array(
                        'ToUser.id = Activity.to_id'
                    ))
            ),
            'order' => array('created DESC')
        ));

        if(count($results) > 0){
        	return $results;
        } else {
        	return false;
        }
	}

	public function getActivityDetail($action, $data, $from_user = null, $to_user = null){
		$return = array('title' => '', 'template' => '');

		switch ($action) {
			case 'student_import_crm':
				$return = array();
				
				$return['title'] 	=  'Student Import From CRM';
				$return['template'] = '';

				if(!empty($data)){
					$return['template'] =  'Student '.$data['student_name'].' Imported From CRM With StudentNumber ('.$data['student_number'].')';
				}
				break;
				
			case 'track_booked':
				$return = array();
				
				$return['title'] 	=  'Track Booked';
				$return['template'] = '';

				if(!empty($data)){
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'calendar', '?' => array('date' => $data['Booking']['date'], 'area' => $data['Booking']['area'])));
					$return['template'] =  $from_user . ' has booked the track of <a href="'. $url .'" target="_blank">'. date('d-m-Y', strtotime($data['Booking']['date'])) . '</a> in ' . ucfirst($data['Booking']['area']);
				}
				break;

			case 'new_cal_driving_time_added':
				$return = array();
				
				$return['title'] 	=  'Driving Lesson Booked';
				$return['template'] = '';

				if(!empty($data)){
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'listBookings', '?' => array('date' => $data['book_date'])));
					$return['template'] =  $from_user . ' has booked the '. $data['add_type'] .' at <a href="'. $url .'" target="_blank">'. date('d-m-Y', strtotime($data['book_date'])) . '</a> for ' . ucfirst($data['name_of_student']);
				}
				break;

			case 'theory_test_chaged':
				$return = array();
				
				$return['title'] 	=  'Teoriprøve status Ændret';
				$return['template'] = '';

				if(!empty($data)){
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'listBookings', '?' => array('date' => $data['book_date'])));
					$old_status = $data['old_status'];
					if($old_status == 0){
						$old_status = 'Ikke bestået';
					}else{
						$old_status = 'Bestået';
					}
					$new_status = $data['new_status'];
					if($new_status == 0){
						$new_status = 'Nej';
					}else{
						$new_status = 'Bestået';
					}
					$return['template'] =  $from_user . ' has Updated status of Teoriprøve from '.$old_status.' to '.$new_status .' for '. $to_user ;
				}
				break;

			case 'new_cal_driving_test_added':
				$return = array();
				
				$return['title'] 	=  'Driving Test Booked';
				$return['template'] = '';

				if(!empty($data)){
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'listBookings', '?' => array('date' => $data['book_date'])));
					$return['template'] =  $from_user . ' has booked the '. $data['add_type'] .' at <a href="'. $url .'" target="_blank">'. date('d-m-Y', strtotime($data['book_date'])) . '</a> for ' . ucfirst($data['name_of_student']);
				}
				break;
			
			case 'new_cal_theory_added':
				$return = array();
				
				$return['title'] 	=  'Theory Booked';
				$return['template'] = '';

				if(!empty($data)){
					$city = $this->City->findById($data['city_id']);
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'listBookings', '?' => array('date' => $data['book_date'])));
					$return['template'] =  $from_user . ' has added the Theory for <a href="'. $url .'" target="_blank">'. $city['City']['name'] . '</a>';
				}
				break;

			case 'new_cal_update':
				$return = array();
				
				$return['title'] 	=  'New Calender Booking Updated';
				$return['template'] = '';

				if(!empty($data)){
					$date = date('d-m-Y', strtotime($data['booking']['start_time']));
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'listBookings', '?' => array('date' => $date)));
					$return['template'] =  $from_user . ' has Updated status of '.$data['booking_type'].' from '.$data['old_status'].' to '.$data['new_status'] .' for <a href="'. $url .'" target="_blank">'. $date . '</a> ' ;
				}
				break;

			case 'new_cal_private_added':
				$return = array();
				
				$return['title'] 	=  'Private Booked';
				$return['template'] = '';

				if(!empty($data)){
					$return['template'] =  $from_user . ' has added the Private Time';
				}
				break;

			case 'reminder_sent':
				$return = array();
				if(isset($data['rem_type']) && $data['rem_type']==3){
					$return['title'] 	=  'SMS Reminder Sent - Before 3 Hours';
				}else if(isset($data['rem_type']) && $data['rem_type']==24){
					$return['title'] 	=  'SMS Reminder Sent - Before 24 Hours';
				}else{
					$return['title'] 	=  'SMS Reminder Sent';
				}
				
				$return['template'] = '';

				if(!empty($data)){
					$str_tmpl =  'SMS Reminder has been sent ';
					
					if(isset($data['to_user'])){
						$str_tmpl = $str_tmpl .' to '.$data['to_user'];
					}

					if(isset($data['Booking']['mobileno'])){
						$str_tmpl = $str_tmpl .' ('.$data['Booking']['mobileno'].')';
					}

					if(isset($data['Booking']['sent_date']) && !empty($data['Booking']['sent_date'])){

						$str_tmpl = $str_tmpl.' on '.date('d-m-Y H:i:s', strtotime($data['Booking']['sent_date']));
					}

					if(isset($data['Booking']['timeslot']) && !empty($data['Booking']['timeslot'])){
						$str_tmpl = $str_tmpl .' for timeslot '.$data['Booking']['timeslot'];
					}

					if($this->sms->buildSMSTemplate($data)!=''){
						$str_tmpl = $str_tmpl . '<br />(' . $this->sms->buildSMSTemplate($data) . ')';
					}

					$return['template'] = $str_tmpl;
				}
				break;

			case 'track_updates':
				$return = array();
				
				$return['title'] 	=  'Track Updated';
				$return['template'] = '';

				if(!empty($data)){
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'calendar', '?' => array('date' => $data['Booking']['date'], 'area' => $data['Booking']['area'])));
					$return['template'] = $from_user . ' has updated the track of <a href="'. $url .'" target="_blank">'. date('d-m-Y', strtotime($data['Booking']['date'])) . '</a> in ' . ucfirst($data['Booking']['area']);
				}

				break;			

			case 'timeslot_booked_internal_email':
				$return = array();
				
				$return['title'] 	=  'Internal Student / Teacher Email Sent For Timeslot Booked';
				$return['template'] = '';
				$return['show_modal'] 	= false;
				$return['tr_class'] 	= 'info';

				if(!empty($data)){
					$return['template'] 	= 'Email has been sent to ' . @$to_user . ' ('. @$data['email'] .').';
					$return['show_modal'] 	= true;
				}

				break;

			case 'driving_test_student_passed_admin':
				$return = array();
				
				$return['title'] 	=  'Admin Email Sent For Driving time Updated';
				$return['template'] = '';
				$return['show_modal'] 	= false;
				$return['tr_class'] 	= 'info';

				if(!empty($data)){
					$return['template'] 	= 'Email has been sent to ' . @$to_user . ' ('. @$data['email'] .').';
					$return['show_modal'] 	= true;
				}

				break;

			case 'timeslot_booked_internal_sms':
				$return = array();
				
				$return['title'] 	=  'Internal Student / Teacher SMS Sent For Timeslot Booked';
				$return['template'] = '';
				$return['tr_class'] 	= 'warning';

				if(!empty($data)){
					$return['template'] 	= 'An SMS has been sent to ' . @$to_user . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;
			
			case 'new_cal_driving_time_added_sms':
				$return = array();
				
				$return['title'] 	=  'Internal Student / Teacher SMS Sent For Driving Lesson Booked';
				$return['template'] = '';
				$return['tr_class'] 	= 'warning';

				if(!empty($data)){
					$return['template'] 	= 'An SMS has been sent to ' . @$to_user . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;

			case 'new_cal_driving_test_added_sms':
				$return = array();
				
				$return['title'] 	=  'Internal Student / Teacher SMS Sent For Driving Test Booked';
				$return['template'] = '';
				$return['tr_class'] 	= 'warning';

				if(!empty($data)){
					$return['template'] 	= 'An SMS has been sent to ' . @$to_user . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;

			case 'timeslot_booked_external_sms':
				$return = array();
				
				$return['title'] 	=  'External Student SMS Sent For Timeslot Booked';
				$return['template'] = '';
				$return['tr_class'] = 'warning';

				if(!empty($data)){
					$data['template'] = (!isset($data['template']) && empty($data['template'])) ? 'studentTemplate' : $data['template'];
					$return['template'] = 'An SMS has been sent to ' . @$data['data']['User']['name'] . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;

			case 'timeslot_deleted':
				$return = array();
				
				$return['title'] 	=  'Timeslot Deleted';
				$return['template'] = '';

				if(!empty($data)){
					$return['template'] =  $from_user . ' has deleted the Timeslot of ' . date('d-m-Y', strtotime($data['Booking']['date'])) . ' ' . $data['BookingTrack'][0]['time_slot'] . ' in ' . $data['Booking']['area_slug'];
				}
				break;

			case 'track_deleted':
				$return = array();
				
				$return['title'] 	=  'Track Deleted';
				$return['template'] = '';

				if(!empty($data)){
					$return['template'] =  $from_user . ' has deleted the track of '. date('d-m-Y', strtotime($data['Booking']['date'])) . '	in ' . $data['Booking']['area_slug'];
				}
				break;

			case 'timeslot_deleted_internal_email':
				$return = array();
				
                $return['title']     =  'Internal Student / Teacher Email Sent For Timeslot Deleted';
				$return['template'] = '';
				$return['show_modal'] 	= false;
				$return['tr_class'] 	= 'info';

				if(!empty($data)){
					$return['template'] 	= 'Email has been sent to ' . @$to_user . ' ('. @$data['email'] .').';
					$return['show_modal'] 	= true;
				}
				break;

			case 'negative_balance_email':
				$return = array();
				
				$return['title'] 	=  'Email Sent For negative balance';
				$return['template'] = '';
				$return['show_modal'] 	= false;
				$return['tr_class'] 	= 'info';

				if(!empty($data)){
					$return['template'] 	= 'Email has been sent to '. @$data['email'];
					$return['show_modal'] 	= true;
				}
				break;

			case 'timeslot_deleted_internal_sms':
				$return = array();
				
				$return['title'] 	=  'Internal Student  / TeacherSMS Sent For Timeslot Deleted';
				$return['template'] = '';
				$return['tr_class'] 	= 'warning';

				if(!empty($data)){
					$return['template'] 	= 'An SMS has been sent to ' . @$to_user . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;

			case 'new_cal_driving_time_delete_sms':
				$return = array();
				
				$return['title'] 	=  'Internal Student  / TeacherSMS Sent For Driving Lesson Deleted';
				$return['template'] = '';
				$return['tr_class'] 	= 'warning';

				if(!empty($data)){
					$return['template'] 	= 'An SMS has been sent to ' . @$to_user . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;
			
			case 'new_cal_driving_test_delete_sms':
				$return = array();
				
				$return['title'] 	=  'Internal Student  / TeacherSMS Sent For Driving Test Deleted';
				$return['template'] = '';
				$return['tr_class'] 	= 'warning';

				if(!empty($data)){
					$return['template'] 	= 'An SMS has been sent to ' . @$to_user . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;

			case 'timeslot_deleted_external_sms':
				$return = array();
				
				$return['title'] 	=  'External Student SMS Sent For Timeslot Deleted';
				$return['template'] = '';
				$return['tr_class'] = 'warning';

				if(!empty($data)){
					$data['template'] = (!isset($data['template']) && empty($data['template'])) ? 'studentTemplate' : $data['template'];

					$return['template'] = 'An SMS has been sent to ' . @$data['data']['User']['firstname'] . ' ('. @$data['mobileno'] .'). <br />(' . $this->sms->buildSMSTemplate($data) . ')';
				}
				break;

			case 'track_released':
				$return = array();
				
				$return['title'] 	=  'Track Released';
				$return['template'] = '';

				if(!empty($data)){
					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'calendar', '?' => array('date' => $data['Booking'][0]['date'], 'area' => $data['Booking'][0]['area'])));

					$return['template'] =  $from_user . ' has released the track. <br />Date : <a href="'. $url .'" target="_blank">' . date('d-m-Y', strtotime($data['Booking'][0]['date'])) . '</a><br />Time Slot: ' . $data['Booking'][0]['time_slot'] . '<br />Area : ' . ucfirst($data['Booking'][0]['area']);
				}
				break;

			case 'track_reopen':
				$return = array();
				
				$return['title'] 	=  'Track Re-Open';
				$return['template'] = '';

				if(!empty($data)){
					$booking_details = $this->Booking->findById($data[0]['BookingTrack']['booking_id']);

					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'calendar', '?' => array('date' => $booking_details['Booking']['date'], 'area' => $booking_details['Booking']['area_slug'])));

					$return['template'] =  $from_user . ' has re-open the track. <br />Date : <a href="'. $url .'" target="_blank">' . date('d-m-Y', strtotime($booking_details['Booking']['date'])) . '</a><br />Time Slot: ' . $data[0]['BookingTrack']['time_slot'] . '<br />Area : ' . ucfirst($booking_details['Booking']['area_slug']);
				}
				break;

			case 'track_close':
				$return = array();
				
				$return['title'] 	=  'Track Close';
				$return['template'] = '';

				if(!empty($data)){
					$booking_details = $this->Booking->findById($data[0]);

					$url = Router::url(array('controller' => 'adminbookings', 'action' => 'calendar', '?' => array('date' => $booking_details['Booking']['date'], 'area' => $booking_details['Booking']['area_slug'])));
					
					$return['template'] =  $from_user . ' has closed the track. <br />Date : <a href="'. $url .'" target="_blank">' . date('d-m-Y', strtotime($booking_details['Booking']['date'])) . '</a><br />Time Slot: ' . $booking_details['BookingTrack'][0]['time_slot'] . '<br />Area : ' . ucfirst($booking_details['Booking']['area_slug']);
				}
				break;
			
			default:
				$return = array('title' => '', 'template' => '');
				break;
		}

		return $return;
	}
}