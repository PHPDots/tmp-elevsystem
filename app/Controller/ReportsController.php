<?php

App::import('Component', 'RequestHandler');

class ReportsController extends AppController {  
    public $uses = array('Area','Booking','Track','TeacherUnavailability','TeacherRegisterTime','DrivingLesson',
                        'BookingTrack','Price','City','DrivingType','Course','Company','Systembooking','UserServices','LatestPayments');
    public $users           = array();
    public $tracks          = array();
    public $reportTypes     = array();
    public $drivingSchools  = array();
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->perPage      = $this->getPerPage('Report');
        $this->users        = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        $this->tracks       = $this->Track->find('list');
        $this->booking_type       = array('KÃ¸retime' => 'Køretimer','Teori' =>'Teori','KÃ¸reprÃ¸ve' => 'Køreprøver');
        $this->status       = array( 'approved' => 'Approved','unapproved' => 'Unapproved');
        $this->drivingSchools = $this->Company->find('list',array(
            'fields' => array('nick_name','name')
        ));
        $this->reportTypes  = array(
            'booking_report'        => array(
                'name'      => __('Bookings Report'),
                'fields'    => array('area','date_from','date_to','teacher','driving_school'),
            ),
            'booking_list'        => array(
                'name'      => __('Bookings List'),
                'fields'    => array('booking_type','status','date_from','date_to'),
            ),
            'ongoing_list'        => array(
                'name'      => __('Igangværende liste'),
                'fields'    => array('teacher'),
            ),
            'get_teacher_booking_report'        => array(
                'name'      => __('Teacher Report'),
                'fields'    => array('date_from','date_to'),
            ),
            'hour_overview'        => array(
                'name'      => __('Timeoversigt'),
                'fields'    => array('date_from','date_to'),
            ),
            'student_track_report'  => array(
                'name'      => __('Instructor-Wise Track Report'),
                'fields'    => array('area','date_from','date_to','teacher','driving_school'),
            ),
            'hourly_report'         => array(
                'name'      => __('Hourly Report'),
                'fields'    => array('datetime_from','datetime_to','teacher'),
            ),
            'theory_report'         => array(
                'name'      => __('Theory Report'),
                'fields'    => array('city','date_from','date_to'),
            ),
            'driving_lessons'   => array(
                'name'          => __('Driving Lessons'),
                'fields'        => array('datetime_from','datetime_to'),
            ),
            'unapproved_driving_lessons'   => array(
                'name'          => __('Unapproved Driving Lessons'),
                'fields'        => array('datetime_from','datetime_to'),
            ),
            'future_bookings'   => array(
                'name'          => __('Future Bookings'),
                'fields'        => array('date_from','date_to'),
            ),
            'student_charge'    => array(
                'name'          => __('Student Lesson Amount'),
                'fields'        => array('student_name'),
            ),
            'open_students'     => array(
                'name'          => __('Current Open Student'),
                'fields'        => array('city','teacher'),
            ),
        );
    }
    
    public function index() {
        $areas      = $this->Area->find('list',array('fields' => array('slug','name')));
        
        $reports    = array();
        foreach($this->reportTypes as $key => $value) {
            $reports[$key] = $value['name'];
        }
      
        $this->set(array(
            'areas'             => $areas,
            'reports'           => $reports,
            'reportTypes'       => $this->reportTypes,
            'drivingSchools'    => $this->drivingSchools,
            'booking_type'     => $this->booking_type,
            'status'            => $this->status
        ));
        
        if(isset($this->request->query['report_type']) && !empty($this->request->query['report_type'])) {
            switch($this->request->query['report_type']) {
                case 'theory_report' :
                    $this->theoryReport();
                    break;
                case 'booking_list' :
                    $this->booking_list();
                    break;
                case 'ongoing_list' :
                    $this->ongoing_list();
                    break;
                
                case 'hourly_report' :
                    $this->hourlyReport();
                    break;
                
                case 'driving_lessons':
                    $this->drivingLessons();
                    break;
                
                case 'unapproved_driving_lessons':
                    $this->drivingLessons('unapproved');
                    break;
                
                case 'future_bookings':
                    $this->bookingReport(true);
                    break;
                
                case 'student_charge':
                    $this->studentCharge();
                    break;
                
                case 'open_students':
                    $this->openStudentsDetails();
                    break;
                
                case 'student_track_report':
                    $this->studentTrackReport();
                    break;
                
                case 'hour_overview':
                    $this->hourOverview();
                    break;

                case 'get_teacher_booking_report':
                    $this->getTeacherBookingReportData();
                    break;
                
                default :
                    $this->bookingReport();
                    break;
            }
        }
    }

    public function hourOverview(){

        $from_date = $this->request->query('date_from');
        $end_date = $this->request->query('date_to');

        if( $from_date == '' ) {
            $from_date = date('Y-m-d');
        } else {
            $from_date = date('Y-m-d', strtotime($from_date));
        }

        if( $end_date == '' ) {
            $end_date = date('Y-m-d');
        } else {
            $end_date = date('Y-m-d', strtotime($end_date));
        }

        $this->autoRender = false;

        $fields = array('Users.id',
                        'CONCAT(Users.firstname, " ", Users.lastname) AS name',
                        'booking_type',
                        'COUNT(Systembooking.id) AS systembooking_count ',
                        'Systembooking.status'
                        );

        $joins          = array(
            array(
                'table'         => 'users',
                'alias'         => 'Users',
                'type'          => 'LEFT',
                'conditions'    => array(
                    'Systembooking.user_id = Users.id'
                )
            )
        );

        $Systembooking = $this->Systembooking->find('all',array(
                'fields'        => $fields,
                'joins'         => $joins,
                'conditions'    => array(
                    'DATE(start_time) >='   => $from_date,
                    'DATE(end_time) <='   => $end_date,
                    'Users.role != ' => 'student'
                ),
                'group'         => array('booking_type','Systembooking.status','user_id')
            ));

        $data = array();
        foreach ($Systembooking as $key => $value) {

            $user_id = $value['Users']['id'];
            $booking_type = $value['Systembooking']['booking_type'];
            $status = $value['Systembooking']['status'];
            
            $data[$user_id]['id'] = $value['Users']['id'];
            $data[$user_id]['name'] = $value[0]['name'];

            $data[$user_id][$booking_type] = isset($data[$user_id][$booking_type]) ? $data[$user_id][$booking_type] + $value[0]['systembooking_count'] : $value[0]['systembooking_count'];
            if($status == 'unapproved'){
                $data[$user_id][$status] = isset($data[$user_id][$status]) ? $data[$user_id][$status] + $value[0]['systembooking_count'] : $value[0]['systembooking_count'];
                
            }

        }

        $fields = array('Users.id',
                        'CONCAT(Users.firstname, " ", Users.lastname) AS name',
                        'COUNT(BookingTrack.id) AS track_count',
                        );

        $joins          = array(
                                array(
                                    'table'         => 'bookings',
                                    'alias'         => 'Bookings',
                                    'type'          => 'LEFT',
                                    'conditions'    => array(
                                        'BookingTrack.booking_id = Bookings.id'
                                    )
                                ),
                                array(
                                    'table'         => 'users',
                                    'alias'         => 'Users',
                                    'type'          => 'LEFT',
                                    'conditions'    => array(
                                        'IF(BookingTrack.booking_user_id != NULL, BookingTrack.booking_user_id , Bookings.user_id) = Users.id'
                                    )
                                ),
                            );

        $BookingTrack = $this->BookingTrack->find('all',array(
                'fields'        => $fields,
                'joins'         => $joins,
                'conditions'    => array(
                    'DATE(date) >='   => $from_date,
                    'DATE(date) <='   => $end_date,
                    'Users.role != ' => 'student'
                ),
                'group'         => array('user_id')
            ));

        $Bookings = Hash::combine($BookingTrack,'{n}.Users.id','{n}.0');
        $Bookings1 = Hash::combine($BookingTrack,'{n}.Users.id','{n}.Users');
        $BookingTrack = Hash::merge($Bookings1,$Bookings);
 
        $final_data = Hash::merge($BookingTrack,$data);
        // parent::prd($final_data);

        $this->set(array(
            'details'          => $final_data,
            ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {
            $details['data'] = $final_data;
            $details['from_date'] = $from_date;
            $details['end_date'] = $end_date;
            $this->createCsv($details);
        } else {
            $this->render('hour_overview');
        }
        
    }
    public function getTeacherBookingReportData(){

        $from_date = $this->request->query('from_date');
        $end_date = $this->request->query('end_date');

        if( $from_date == '' ) {
            $from_date = date('Y-m-d');
        } else {
            $from_date = date('Y-m-d', strtotime($from_date));
        }

        if( $end_date == '' ) {
            $end_date = date('Y-m-d');
        } else {
            $end_date = date('Y-m-d', strtotime($end_date));
        }

        $this->autoRender = false;


        $details = $this->Booking->getBookingTotalTime( $from_date, $end_date );

        $this->set(array(
            'details'          => $details,
            ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {
            $details['data'] = $details;
            $details['from_date'] = $from_date;
            $details['end_date'] = $end_date;
            $this->createCsv($details);
        } else {
            $this->render('get_teacher_booking_report');
        }
        
    }
    
    public function studentCharge() {
        
        $conditions                 = array();
        $bookingTracksConditions    = array();
        $drivingLessonConditions    = array();
        
        $this->perPage  = $this->getPerPage('User');
        
        $conditions['User.role']    = 'student';
        $conditions['User.status']  = 'active';
        
        $args = array(
            'order'         => array('User.id' => 'DESC'),
            'conditions'    => $conditions,
        );
        $bookingType    = array_keys(Configure::read('bookingType'));
        
        if(isset($this->request->query['student_id']) && !empty($this->request->query['student_id'])) {
            $students = Hash::combine($this->User->findAllById($this->request->query['student_id']),'{n}.User.id','{n}.User');
            $bookingTracksConditions = array(
                'student_id'   => $this->request->query['student_id']
            );
            $drivingLessonConditions = array(
                 'DrivingLesson.student_id'   => $this->request->query['student_id']
            );
        } else {
            $students = Hash::combine($this->User->find('all',$args),'{n}.User.id','{n}.User');
        }
        
        $bookingTracks  = Hash::combine($this->BookingTrack->find('all',array(
            'group'         => array('BookingTrack.id'),
            'conditions'    => $bookingTracksConditions,
        )),'{n}.BookingTrack.id','{n}.BookingTrack');
        
        $bookedStudents = Hash::extract($bookingTracks,'{n}.student_id');
        
        $bookings       = Hash::combine($this->Booking->find('all',array(
            'group'     => array('Booking.id')
        )),'{n}.Booking.id','{n}.Booking');
        
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'driving',
            'DrivingLesson.status IS NULL'
        );
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'test',
            'DrivingLesson.status'      => 'confirmed'
        );
      
        $drivingLessons = Hash::combine($this->DrivingLesson->find('all',array(
            'conditions'    => $drivingLessonConditions
        )),'{n}.DrivingLesson.id','{n}.DrivingLesson','{n}.DrivingLesson.student_id');
        
        $studentArr = array();
        foreach($bookingTracks as $track) {
            if(in_array($track['student_id'],$bookedStudents)) {
                if(!empty($track['student_id'])) {
                    $studentArr[$track['student_id']][$track['booking_id']] = array(
                        'booking_id'    => $track['booking_id'],
                        'area'          => $bookings[$track['booking_id']]['area_slug'],
                        'booking_date'  => $bookings[$track['booking_id']]['date'],
                    );
                }
            }
        }
        
        $areaPrices     = Hash::combine($this->Price->find('all',array(
            'conditions'    => array(
                'Price.type'   => 'area'
            )
        )),'{n}.Price.from_date','{n}.Price','{n}.Price.area');
        
        $otherPrices    = Hash::combine($this->Price->find('all',array(
            'conditions'    => array(
                'Price.type !='   => 'area'
            )
        )),'{n}.Price.from_date','{n}.Price','{n}.Price.type');
        
        $studentAmount  = array();
        $priceType      = Configure::read('priceType');
        $areaAmount     = __('N/A');
        
        foreach($studentArr as $studentId => $bookings) {
            foreach($bookings as $booking){
                if(isset($areaPrices[$booking['area']])) {
                    ksort($areaPrices[$booking['area']]);
                    $dateArray  = array_keys($areaPrices[$booking['area']]); 
                    for($i=0;$i<count($dateArray);$i++){       
                        if(isset($dateArray[$i+1])){
                            if((strtotime(date('Y-m-d',strtotime($booking['booking_date']))) > strtotime($dateArray[$i+1]))){                                                              
                                $areaAmount  = $areaPrices[$booking['area']][$dateArray[$i+1]]['price'];                                 
                            }else if((strtotime(date('Y-m-d',strtotime($booking['booking_date']))) >= strtotime($dateArray[$i]))
                                && (strtotime(date('Y-m-d',strtotime($booking['booking_date']))) < strtotime($dateArray[$i+1]))){                                                               
                                $areaAmount  = $areaPrices[$booking['area']][$dateArray[$i]]['price'];                                  
                            } 
                        }else if(count($dateArray) == 1){
                            $areaAmount  = $areaPrices[$booking['area']][$dateArray[$i]]['price']; 
                        }
                    }
                    if(isset($students[$studentId])){
                        $studentAmount[] = array(
                            'booking_id'    => $booking['booking_id'],
                            'booking_date'  => $booking['booking_date'],
                            'user_id'       => $studentId,
                            'name'          => isset($students[$studentId])?$students[$studentId]['firstname'].' '.$students[$studentId]['lastname']:__('External Student'),
                            'area'          => $booking['area'],
                            'category'      => __('Booked Track'),
                            'text'          => '',
                            'price'         => $areaAmount,
                            'date'          => date('d.m.Y',strtotime($booking['booking_date']))
                        );  
                    }
                }
            }       
        }
            
         
        $drivingAmount = __('N/A');
        
        foreach($drivingLessons as $studentId => $lessons) {
            foreach($lessons as $lessonId => $lesson){
                if(isset($otherPrices[$lesson['type']])){
                    ksort($otherPrices[$lesson['type']]);
                    $dateArray  = array_keys($otherPrices[$lesson['type']]);
                    for($i=0;$i<count($dateArray);$i=$i+2){                            
                        if(isset($dateArray[$i+1])){
                            if((strtotime(date('Y-m-d',strtotime($lesson['start_time']))) > strtotime($dateArray[$i+1]))){                                                              
                                $drivingAmount  = $otherPrices[$lesson['type']][$dateArray[$i+1]]['price']; 
                            }else if((strtotime(date('Y-m-d',strtotime($lesson['start_time']))) >= strtotime($dateArray[$i]))
                                    && (strtotime(date('Y-m-d',strtotime($lesson['start_time']))) < strtotime($dateArray[$i+1]))){                                                               
                                $drivingAmount  = $otherPrices[$lesson['type']][$dateArray[$i]]['price'];                                                            
                            }
                        }else if(count($dateArray) == 1){
                            $drivingAmount  = $otherPrices[$lesson['type']][$dateArray[$i]]['price'];    
                        }
                        if(isset($students[$studentId])){
                            $studentAmount[] = array(
                                'lesson_id'     => $lessonId,
                                'user_id'       => $studentId,
                                'name'          => isset($students[$studentId])?$students[$studentId]['firstname'].' '.$students[$studentId]['lastname']:__('External Student'),
                                'area'          => '',
                                'category'      => $priceType[$lesson['type']],
                                'text'          => $lesson['type'],
                                'price'         => $drivingAmount,
                                'date'          => date('d.m.Y',strtotime($lesson['start_time']))
                            );
                        }
                    }
                }
            }
        }
        
        $this->set(array(
            'studentAmount'     => $studentAmount,
            'perPage'           => $this->perPage,
        ));
        
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {                
            $this->createCsv($studentAmount);
        } else {
            $this->render('student_charge');
        }
    }
    
    public function theoryReport() {
        $registeredConditions   = array();
        $searchString           = array();
        
        if(isset($this->request->query['city']) && !empty($this->request->query['city'])) {
            
            $registeredConditions['TeacherRegisterTime.city LIKE ']    = "%{$this->request->query['city']}%";
            
            $searchString[] = __(' for "').$this->request->query['city'].'"';
        }
        
        if(isset($this->request->query['date_from']) && !empty($this->request->query['date_from']) &&
                isset($this->request->query['date_to']) && !empty($this->request->query['date_to'])) {
            
            $dateFrom           = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_from'])));
            $dateTo             = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_to'])));
            $registeredConditions['SUBSTRING_INDEX(TeacherRegisterTime.from," ",1) >= ']      = $dateFrom;
            $registeredConditions['SUBSTRING_INDEX(TeacherRegisterTime.from," ",1) <= ']      = $dateTo;
            
            $searchString[] = __(' from ').$this->request->query['date_from'].__(' to ').$this->request->query['date_to'];
        }
        
        $registeredConditions['TeacherRegisterTime.type'] = 'theory';
        
        $args = array(
            'conditions'    => $registeredConditions,
            'limit'         => $this->perPage,
        );
        $this->Paginator->settings  = $args;
        $registeredTeachers         = $this->Paginator->paginate('TeacherRegisterTime');
        
        $this->request->query['searchString']   = $searchString;
        
        $this->set(array(
            'searchString'          => $searchString,
            'registeredTeachers'    => $registeredTeachers,
            'users'                 => $this->users,
            'perPage'               => $this->perPage,
            
        ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {                
            $this->createCsv($registeredTeachers);
        } else {
            $this->render('theory_report');
        }
    }
    public function booking_list() {
        $conditions   = array();
        $searchString           = array();
        
        if(isset($this->request->query['booking_type']) && !empty($this->request->query['booking_type'])) {
            
            $conditions['booking_type LIKE ']    = "%{$this->request->query['booking_type']}%";
            
            $searchString[] = __(' for "').$this->request->query['booking_type'].'"';
        }
        
        if(isset($this->request->query['status']) && !empty($this->request->query['status'])) {
            
            $conditions['status']    = "{$this->request->query['status']}";
            
            $searchString[] = __('and status "').$this->request->query['status'].'"';
        }

        if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])) {
            $conditions['user_id'] = $this->request->query['teacher_id'];
            $searchString[] = ' '.__('of').' '.$this->users[$this->request->query['teacher_id']]['firstname'].' '.$this->users[$this->request->query['teacher_id']]['lastname'];
        }
        
        
        if(isset($this->request->query['date_from']) && !empty($this->request->query['date_from']) &&
                isset($this->request->query['date_to']) && !empty($this->request->query['date_to'])) {
            
            $dateFrom           = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_from'])));
            $dateTo             = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_to'])));
            $conditions['start_time >= ']      = $dateFrom;
            $conditions['end_time <= ']      = $dateTo;
            
            $searchString[] = __(' from ').$this->request->query['date_from'].__(' to ').$this->request->query['date_to'];
        }
        
        $args = array(
            'conditions'    => $conditions,
            'limit'         => $this->perPage,
        );

        $this->Paginator->settings  = $args;

        $this->Systembooking->query( "SET CHARACTER SET utf8;" );

        $Bookings         = $this->Paginator->paginate('Systembooking');

        $this->Systembooking->query( "SET CHARACTER SET default;" );
        
        $this->request->query['searchString']   = $searchString;
        
        $this->set(array(
            'searchString'          => $searchString,
            'Bookings'    => $Bookings,
            'users'                 => $this->users,
            'perPage'               => $this->perPage,
            
        ));

            $this->render('booking_list');
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


    public function ongoing_list() {
        $conditions   = array();
        $searchString  = array();
        $auth_role = $this->currentUser['User']['role'];
        if($auth_role == 'internal_teacher')
        {
            $conditions['User.teacher_id'] = $this->currentUser['User']['id'];
        }
        if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])) {
            $conditions['User.teacher_id']    = "{$this->request->query['teacher_id']}";
            
            $searchString[] = __(' for "').$this->request->query['teacher'].'"';
        }
        
        $conditions['User.role']    = "student";
        $conditions['User.is_completed !='] = "2";
        // $conditions['Systembookings.status !=']    = "delete";

        $args = array(
            'fields'        => array('User.id','CONCAT(firstname," " ,lastname) AS full_name',
                                    'CONCAT(address," ",city," ",zip) AS full_address',
                                    'theory_test_passed',
                                    'SUM(IF(Systembookings.status = "passed", 1, 0)) AS passed_count',
                                    'SUM(IF(Systembookings.status = "dumped", 1, 0)) AS dumped_count',
                                    'handed_firstaid_papirs',
                                    'firstaid_papirs_date',
                                    'SUM(IF(Systembookings.status != "delete", IF(Systembookings.lesson_type = 2, 2 ,1), 0)) AS total_count',
                                    'balance',
                                    'available_balance',
                                    'IF(Systembookings.booking_type = "Køreprøve" , MAX(start_time) , "")  AS latest_booking_date',
                                    'IF(Systembookings.booking_type = "Køreprøve" , MIN(start_time) , "")  AS first_booking_date',
                                    'MIN(IF(Systembookings.status = "pending" && start_time >= NOW() , start_time, NULL)) AS next_booking_date',
                                    'MAX(IF(Systembookings.status != "pending" && Systembookings.status != "delete", start_time, NULL)) AS last_booking_date'),
            'joins'         => array(
                array(
                    'table'         => 'systembookings',
                    'alias'         => 'Systembookings',
                    'type'          => 'left',
                    'conditions'    => array(
                        'User.id = Systembookings.student_id'
                    )
                )
            ),
            'conditions'    => $conditions,
            'limit'         => $this->perPage,
            'group'         => array('User.id'),
        );
        if(isset($this->request->query['csv'])) {   
           $args['limit']   = '-1';
        }

        $this->Paginator->settings  = $args;
        $dbo = $this->Systembooking->getDatasource();

        $allBookings         = $this->Paginator->paginate('User');

        $userIds = [];
        foreach($allBookings as $r)
        {
            $userIds[$r['User']['id']] = 
            [
                'balance' => 0
            ];
        }        

        foreach($userIds as $key => $value)
        {
            $userIds[$key]['balance'] = $this->getUserAvailableBalance($key);
            // $sql = "SELECT start_time FROM systembookings WHERE student_id = '$key' order by id desc limit 1";
            $sql = "SELECT start_time FROM systembookings WHERE student_id = '$key' AND booking_type = 'Køreprøve' order by start_time desc limit 1";            
            $dateData = $this->Systembooking->query($sql);
            $userIds[$key]['date'] = isset($dateData[0]['systembookings']['start_time']) ? $dateData[0]['systembookings']['start_time']:'';
        }        

        
        // parent::prd($allBookings);
        $this->request->query['searchString']   = $searchString;

        // echo "<pre>";
        // print_r($allBookings);
        
        $this->set(array(
            'searchString'          => $searchString,
            'Bookings'              => $allBookings,
            'users'                 => $this->users,
            'perPage'               => $this->perPage,
            'userIds' => $userIds
        ));


        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {    
            $Bookings = Hash::combine($allBookings,'{n}.User.id','{n}.0');
            $Bookings1 = Hash::combine($allBookings,'{n}.User.id','{n}.User');
            $Bookings = Hash::merge($Bookings1,$Bookings);            
            $this->createCsv($Bookings);
        } else {
            $this->render('ongoing_list');
        }
    }
    
    public function hourlyReport() {
        $bookingConditions          = array();
        $unavailabiltyConditions    = array();
        $registerConditions         = array();
        $searchString               = array();
        $drivingLessonConditions    = array();
        //pr($this->request->query);
        
        if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])){
            $bookingConditions['Booking.user_id']                       = $this->request->query['teacher_id'];
            $unavailabiltyConditions['TeacherUnavailability.user_id']   = $this->request->query['teacher_id'];
            $registerConditions['TeacherRegisterTime.user_id']          = $this->request->query['teacher_id'];
            $drivingLessonConditions['DrivingLesson.teacher_id']        = $this->request->query['teacher_id'];
            
            $searchString[] = ' of '.$this->users[$this->request->query['teacher_id']]['firstname'].' '.$this->users[$this->request->query['teacher_id']]['lastname'];
        }
        
        if(isset($this->request->query['datetime_from']) && !empty($this->request->query['datetime_from']) &&
                isset($this->request->query['datetime_to']) && !empty($this->request->query['datetime_to'])) {
            
            $dateTimeFromArr    = explode(' ',$this->request->query['datetime_from']);
            $dateTimeToArr      = explode(' ',$this->request->query['datetime_to']);
            
            $dateTimeFrom       = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $this->request->query['datetime_from'])));
            $dateTimeTo         = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $this->request->query['datetime_to'])));
            
            $dateFrom           = date('Y-m-d',strtotime(str_replace('/', '-', $dateTimeFromArr[0])));
            $dateTo             = date('Y-m-d',strtotime(str_replace('/', '-', $dateTimeToArr[0])));
            
            $bookingConditions['Booking.date BETWEEN ? AND ?'] = array($dateFrom,$dateTo);
            
            $unavailabiltyConditions['TeacherUnavailability.from >=']    = $dateTimeFrom;
            $unavailabiltyConditions['TeacherUnavailability.to <=']      = $dateTimeTo;
            
            $registerConditions['TeacherRegisterTime.from >=']  = $dateTimeFrom;
            $registerConditions['TeacherRegisterTime.from <=']  = $dateTimeTo;
            
            $drivingLessonConditions['DATE(DrivingLesson.start_time) >=']  = $dateTimeFrom;
            $drivingLessonConditions['DATE(DrivingLesson.start_time) <=']  = $dateTimeTo;
            
            $searchString[] = ' '.__(' from ').' '.$this->request->query['datetime_from'].__(' to ').$this->request->query['datetime_to'];
        }
        
        $teacherAvailability = $this->TeacherUnavailability->find('all',array(
            'conditions'    => $unavailabiltyConditions,
        ));

        $teacherRegisterTimes = $this->TeacherRegisterTime->find('all',array(
            'conditions'    => $registerConditions
        ));
        
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'driving',
            'DrivingLesson.status IS NULL'
        );
        $drivingLessonConditions['OR'][]  = array(
            'DrivingLesson.type'        => 'test',
            'DrivingLesson.status'      => 'confirmed'
        );
        
        $teacherDrivingLessons= $this->DrivingLesson->find('all',array(
            'conditions'    => $drivingLessonConditions
        ));

        $this->Booking->virtualFields = array(
            'track'     => 'BookingTrack.track_id',
            'timeSlot'  => 'BookingTrack.track_id',
        );
        
        $bookingConditions['BookingTrack.track_status']    = 'closed';
        
        $bookings   = $this->Booking->find('all',array(
            'joins'         => array(
                array(
                    'table'         => 'booking_tracks',
                    'alias'         => 'BookingTrack',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => $bookingConditions,
            'limit'         => $this->perPage,
            'order'         => array(
                'track'         => 'ASC',
                'timeSlot'      => 'ASC',
                'Booking.date'  => 'ASC',
            ),
            'group'         => array('Booking.id')
        ));
        
        $bookings       = $this->processData($bookings);
        $cities         = $this->City->find('list',array('fields' => array('slug','name')));
        $drivingTypes   = $this->DrivingType->find('list',array('fields' => array('slug','name')));
        
        $this->request->query['searchString']    = $searchString;
        
        $this->set(array(
            'bookings'              => $bookings,
            'teacherAvailability'   => $teacherAvailability,
            'teacherRegisterTimes'  => $teacherRegisterTimes,
            'users'                 => $this->users,
            'tracks'                => $this->tracks,
            'perPage'               => $this->perPage,
            'searchString'          => $searchString,
            'cities'                => $cities,
            'drivingTypes'          => $drivingTypes,
            'teacherDrivingLessons' => $teacherDrivingLessons,
        ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {  
            $hourlyReportData   = array(
                'bookings'              => $bookings,
                'teacherAvailability'   => $teacherAvailability,
                'teacherRegisterTimes'  => $teacherRegisterTimes,
                'teacherDrivingLessons' => $teacherDrivingLessons
            );            
            $this->createCsv($hourlyReportData);
        } else {
            $this->render('hourly_report');
        }
    }
    
    public function bookingReport($futureBookings = FALSE) {

        $conditions     = array();
        $searchString   = array();
        
        $joins['BookingTrack']  = array(
            'table'         => 'booking_tracks',
            'alias'         => 'BookingTrack',
            'conditions'    => array(
                'Booking.id = BookingTrack.booking_id'
            )
        );
        if(isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school'])) {
            $conditions['User.company_id']   = $this->request->query['driving_school'];
            $joins['User']  = array(
                'table'         => 'users',
                'alias'         => 'User',
                'conditions'    => array(
                    'Booking.user_id = User.id'
                )
            );
        }
        
        if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])) {
            $conditions['Booking.user_id'] = $this->request->query['teacher_id'];
            $searchString[] = ' '.__('of').' '.$this->users[$this->request->query['teacher_id']]['firstname'].' '.$this->users[$this->request->query['teacher_id']]['lastname'];
        }
        
        if(isset($this->request->query['area_id']) && !empty($this->request->query['area_id'])) {
            $conditions['Booking.area_slug'] = $this->request->query['area_id'];
            $searchString[] = ' '.__('for').' '.Inflector::humanize($this->request->query['area_id']);
        }
        
        if((isset($this->request->query['date_from']) && !empty($this->request->query['date_from'])) && 
                (isset($this->request->query['date_to']) && !empty($this->request->query['date_to']))) {
            
            $dateFrom   = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_from'])));
            $dateTo     = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_to'])));
            
            $conditions['Booking.date BETWEEN ? AND ?'] = array($dateFrom,$dateTo);
            $searchString[] = ' &nbsp;'.__('from').' '.$this->request->query['date_from'].__(' to ').$this->request->query['date_to'];
        }
        
        $conditions['BookingTrack.track_status']        = 'closed';
        $conditions['BookingTrack.status']              = 'met';
        $conditions['BookingTrack.released_by !=']      = NULL;
        
        if($futureBookings) {
            $conditions['Booking.date >'] = date('Y/m/d');
        }
        
        $this->Booking->virtualFields = array(
            'track'     => 'BookingTrack.track_id',
            'timeSlot'  => 'BookingTrack.time_slot',
        );
        $args = array(
            'joins'         => array_values($joins),
            'conditions'    => $conditions,
            'order'         => array(
                'Booking.date'  => 'ASC',
                'track'         => 'ASC',
                'timeSlot'      => 'ASC',
            ),
            'group'         => array('Booking.id')
        );
        
        $this->perPage  = $this->getPerPage('Booking');
        
        $this->Paginator->settings = $args;
        
        $bookings       = $this->Paginator->paginate('Booking');
        
        if(isset($this->request->query['report_type']) && ($this->request->query['report_type'] == 'booking_report' || 
                $this->request->query['report_type'] == 'future_bookings')) {
            $bookings = $this->processData($bookings);
            $bookings = Hash::sort($bookings, '{n}.track_id', 'asc');
        }
        
        $this->request->query['searchString']    = $searchString;
        $courses = $this->Course->find('list');
        $this->set(array(
            'bookings'      => $bookings,
            'users'         => $this->users,
            'tracks'        => $this->tracks,
            'perPage'       => $this->perPage,
            'searchString'  => $searchString,
            'courses'       => $courses,
        ));
        
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {
            $bookings       = $this->Booking->find('all',$args);
            $this->createCsv($bookings);
        } else {
            if(isset($this->request->query['report_type']) && $this->request->query['report_type'] == 'student_track_report') {
                $this->render('student_track_report');
            } else {
                $this->render('booking_report');
            }
        }
    }
    
    public function drivingLessons( $status = NULL) {
        
        $conditions     = array();
        $dateTimeFrom   = '';
        $dateTimeTo     = '';
        $searchString   = array();
        
        if(isset($this->request->query['datetime_from']) && !empty($this->request->query['datetime_from']) &&
                isset($this->request->query['datetime_to']) && !empty($this->request->query['datetime_to'])) {
            $dateTimeFrom       = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $this->request->query['datetime_from'])));
            $dateTimeTo         = date('Y-m-d H:i:s',strtotime(str_replace('/', '-', $this->request->query['datetime_to'])));
            
            $conditions['DrivingLesson.start_time BETWEEN ? AND ?'] = array($dateTimeFrom,$dateTimeTo);
            $searchString[] = __(' from ').$this->request->query['datetime_from'].__(' to ').$this->request->query['datetime_to'];     
        }
        
        if(!is_null($status) && $status == 'unapproved') {
            $conditions['DrivingLesson.approved !='] = 'yes';
        }
        
        $args = array(
            'conditions'    => $conditions,
            'limit'         => $this->perPage,
        );
        
        $this->Paginator->settings  = $args;
        $drivingLessons             = $this->Paginator->paginate('DrivingLesson');
        
        $this->request->query['searchString']    = $searchString;
        
        $this->set(array(
            'drivingLessons'    => $drivingLessons,
            'users'             => $this->users ,
            'perPage'           => $this->perPage,
            'dateTimeFrom'      => $dateTimeFrom,
            'dateTimeTo'        => $dateTimeTo,
            'searchString'      => $searchString,
        ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {                
            $this->createCsv($drivingLessons);
        } else {
            $this->render('driving_lessons');
        }
    }

    public function unapprovedLessons( $status = NULL) {
        
        $currentUser_id = $this->currentUser['User']['id'];
        $currentUserRole = $this->Auth->user('role');

        if($currentUserRole == "admin")
        {
            $conditions = [];
            $conditions['Systembooking.status'] = 'pending';
            $conditions['Systembooking.booking_type !='] = 'Privat';            

            if(isset($this->request->query['searchTxt']) && !empty($this->request->query['searchTxt'])) {

                $searchTxt = trim($this->request->query['searchTxt']);

                $conditions['OR'] = array
                (
                    "Teacher.firstname LIKE"       => "%{$searchTxt}%",                    
                    "Teacher.lastname LIKE"       => "%{$searchTxt}%",
                    "CONCAT(Teacher.firstname,' ',Teacher.lastname) LIKE"       => "%{$searchTxt}%",                    
                );
            }


            $args = array(
                'fields'         => array('Systembooking.*','User.phone_no','Teacher.firstname','User.lastname','User.firstname','Teacher.lastname'),
                'conditions'    => $conditions,
                'limit'         => $this->perPage,
                'joins'         => array(
                    array(
                        'table'     => 'users',
                        'alias'     => 'User',
                        'type'      => 'LEFT',
                        'conditions'    => array(
                            'Systembooking.student_id = User.id'
                        )                        
                    )
                    ,array
                    (
                            'table'     => 'users',
                            'alias'     => 'Teacher',
                            'type'      => 'LEFT',
                            'conditions'    => array(
                                'Systembooking.user_id = Teacher.id'
                            )
                    )                    
                ),
            );            
        }
        else
        {            
            $args = array(
                'fields'         => array('Systembooking.*','User.phone_no'),
                'conditions'    => array( 
                                            'Systembooking.status =' => 'pending' ,
                                            'user_id =' => $currentUser_id,
                                            'Systembooking.booking_type !=' => 'Privat',
                                            'Systembooking.start_time  >=' => date("Y-m-d H:i:s")
                                            // 'start_time >=' => date('Y-m-d') 
                                         ),
                'limit'         => $this->perPage,
                'joins'         => array(
                    array(
                        'table'     => 'users',
                        'alias'     => 'User',
                        'type'      => 'LEFT',
                        'conditions'    => array(
                            'Systembooking.student_id = User.id'
                        )
                    )
                ),
            );
        }        

        
        $this->Paginator->settings  = $args;
        $drivingLessons             = $this->Paginator->paginate('Systembooking');        

        // pr($drivingLessons);
        // exit;

        $this->set(array(
            'drivingLessons'    => $drivingLessons,
            'users'             => $this->users ,
            'perPage'           => $this->perPage,
            'currentUserRole' => $currentUserRole
        ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {                

            if($currentUserRole == "admin")
            {
                $filename   = __('Unapproved Driving Lessons On ').date('d/m/Y H:i:s',time()).'.csv';
                $types      = Configure::read('bookingType'); 
                $lessonTime = Configure::read('lessonTime');
                $status     = Configure::read('lessonStatus');                
                $details[]  = array(__('No.'),__('Student Name'),__('Teacher Name'),__('Type'),__('Start Time'),__('Lesson Time'),__('Status'));
                
                $i = 1;
                foreach($drivingLessons as $booking)
                {
                    $details[] = array(
                        $i,                        
                        $booking['User']['firstname'].' '.$booking['User']['lastname'],                        
                        $booking['Teacher']['firstname'].' '.$booking['Teacher']['lastname'],
                        $booking['Systembooking']['booking_type'],
                        isset($lessonTime[$booking['Systembooking']['lesson_type']]) ? $lessonTime[$booking['Systembooking']['lesson_type']]:'',
                        date('d.m.y H:i',strtotime($booking['Systembooking']['start_time'])),
                        ($booking['Systembooking']['status'] == 'approved' ) ? "Approved" : "Ej godkendt"
                    );
                    $i++;
                }
                
                $this->set(array(
                    'details'  => $details,
                    'filename' => $filename,
                ));
                header('Content-Encoding: UTF-8');
                header('Content-type: text/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header("Pragma: no-cache");
                $csv_file = fopen('php://output', 'w');
                // $details  =array_map("utf8_decode",$details);
                foreach($details as $fields){      
                    fputcsv($csv_file,$fields,',','"');
                }

                fclose($csv_file);
                die();

            }
            else
            {
                $this->createCsv($drivingLessons);
            }

        } else {
            $this->render('unapproved_driving_lessons');
        }
    }
    
    public function openStudentsDetails(){
        
        $conditions = array();
        $joins      = array();
        
        $this->perPage  = $this->getPerPage('User');
        
        $conditions['User.role']    = 'student';
        $conditions['User.status']  = 'active';
        
        
        if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])){
            $conditions['User.teacher_id']  = $this->request->query['teacher_id'];
        }
        
        if(isset($this->request->query['city']) && !empty($this->request->query['city'])){
            $conditions['User.city']  = $this->request->query['city'];
        }
       
        $fields = array('User.id','User.firstname','User.lastname','User.balance','User.student_number','User.phone_no');        
        $args   = array(
            'fields'        => $fields,
            'conditions'    => $conditions,       
            'limit'         => $this->perPage
        );
        
        $this->Paginator->settings = $args;
        $users   = $this->Paginator->paginate('User');
        
        $userIds = Hash::extract($users,'{n}.User.id');
        
        $drivingLessons = $this->DrivingLesson->find('all',array(
            'conditions'    => array(
                'DrivingLesson.student_id'    => $userIds,
                'DrivingLesson.type'          => 'test'
            )
        ));
        
        $drivingLessons = Hash::combine($drivingLessons,'{n}.DrivingLesson.id','{n}.DrivingLesson','{n}.DrivingLesson.student_id');
       
        foreach($users as $key => $user){
            $users[$key]['User']['driving_lessons_count']   = 0;
            $users[$key]['User']['module']                  = '';
            $users[$key]['User']['time']                    = 0;
        }
        
        foreach($users as $key => $user){
            if(isset($drivingLessons[$user['User']['id']]) && !empty($drivingLessons[$user['User']['id']])){
                foreach($drivingLessons[$user['User']['id']] as $drivingLesson){
                    $users[$key]['User']['driving_lessons_count']   += 1;
                    if(!empty($drivingLesson['module'])){
                        $users[$key]['User']['module']  = $drivingLesson['module'];
                    }
                    $users[$key]['User']['time']   += ((int)$drivingLesson['lesson_time']/60);
                }
            }            
        }
   
        $this->set(array(
            'users'     => $users,
            'perPage'   => $this->perPage
        ));
        
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {     
            $users  = array();
            unset($args['limit']);
            $users = $this->User->find('all',$args);
            $userIds = Hash::extract($users,'{n}.User.id');
        
            $drivingLessons = $this->DrivingLesson->find('all',array(
                'conditions'    => array(
                    'DrivingLesson.student_id'    => $userIds,
                    'DrivingLesson.type'          => 'test'
                )
            ));

            $drivingLessons = Hash::combine($drivingLessons,'{n}.DrivingLesson.id','{n}.DrivingLesson','{n}.DrivingLesson.student_id');

            foreach($users as $key => $user){
                $users[$key]['User']['driving_lessons_count']   = 0;
                $users[$key]['User']['module']                  = '';
                $users[$key]['User']['time']                    = 0;
            }

            foreach($users as $key => $user){
                if(isset($drivingLessons[$user['User']['id']]) && !empty($drivingLessons[$user['User']['id']])){
                    foreach($drivingLessons[$user['User']['id']] as $drivingLesson){
                        $users[$key]['User']['driving_lessons_count']   += 1;
                        if(!empty($drivingLesson['module'])){
                            $users[$key]['User']['module']  = $drivingLesson['module'];
                        }
                        $users[$key]['User']['time']   += ((int)$drivingLesson['lesson_time']/60);
                    }
                }            
            }
            $this->createCsv($users);
        } else {
            $this->render('open_students_details');
        }
    }
    
    private function createCsv($bookings, $defaultData = []) { 
        $users      = $this->users;
        $tracks     = $this->tracks;        
        $filename   = '';

        switch($this->request->query['report_type']) {
            
            case 'hour_overview':
                $filename = 'Timeoversigt Report On '.date('d/m/Y H:i:s').'.csv';
                $details[0]   = array(  
                                        'Periode',
                                        $bookings['from_date']." Date to ".$bookings['end_date']." Date",
                                        
                                    );
                $details[1]   = array('');
                $details[3]   = array(
                                        'Driver',
                                        'Køretimer',
                                        'Køreprøver',
                                        'Udeblivelser',
                                        'Kørelektioner total',
                                        '',
                                        'Teori (teory)',
                                        'Baner (track)',
                                        'T/B/A Total',
                                        '',
                                        'Total all',
                                    );
                $i = 4;
               
                foreach($bookings['data'] as $student){
                    $koretime = ( !empty($student['Køretime']) ) ? $student['Køretime'] : 0;
                    $koreprove = ( !empty($student['Køreprøve']) ) ? $student['Køreprøve'] : 0;
                    $Udeblivelser = ( !empty($student['unapproved']) ) ? $student['unapproved'] : 0;
                    $teori = ( !empty($student['Teori']) ) ? $student['Teori'] : 0;
                    $track = ( !empty($student['track_count']) ) ? $student['track_count'] : 0;
                    $details[$i] = array(
                        $student['name'],
                        $koretime,
                        $koreprove,
                        $Udeblivelser,
                        ($koretime + $koreprove + $Udeblivelser),
                        '',
                        $teori,
                        $track,
                        ($teori + $track),
                        '',
                        ($koretime + $koreprove + $Udeblivelser + $teori + $track )
                    );
                    $i++;
                }
                
                break;
            
            case 'get_teacher_booking_report':
                $filename = 'Teacher Booking Report On '.date('d/m/Y H:i:s').'.csv';
                $details[0]   = array(  
                                        'Periode',
                                        $bookings['from_date']." Date to ".$bookings['end_date']." Date",
                                        
                                    );
                $details[1]   = array('');
                $details[3]   = array(
                                        'Driver',
                                        __('Køretimer (driving lessons)'),
                                        'Køreprøver (driving test)',
                                        'Kørelektioner total (driving total)',
                                        '',
                                        'Teori (teory)',
                                        'Baner (track)',
                                        'Andet (other)',
                                        'T/B/A Total',
                                        '',
                                        'Total all',
                                    );
                $i = 4;
               
                foreach($bookings['data'] as $student){
                    $koretime = ( !empty($student['status']['kretime']) ) ? $student['status']['kretime'] / 60 : 0;
                    $koreprove = ( !empty($student['status']['kreprve']) ) ? $student['status']['kreprve'] / 60 : 0;
                    $teori = ( !empty($student['status']['teori']) ) ? $student['status']['teori'] / 60 : 0;
                    $privat = ( !empty($student['status']['privat']) ) ? $student['status']['privat'] / 60 : 0;
                    $track = ( !empty($student['status']['track']) ) ? $student['status']['track'] / 60 : 0;
                    $details[$i] = array(
                        $student['name'],
                        $koretime,
                        $koreprove,
                        ($koretime + $koreprove),
                        '',
                        $teori,
                        $track,
                        $privat,
                        ($teori + $track + $privat),
                        '',
                        ($koretime + $koreprove + $teori + $track + $privat)
                    );
                    $i++;
                }
                
                break;
            
            case 'student_charge':
                $filename = __('Student Lesson Charges On ').date('d/m/Y H:i:s').'.csv';
                $details[0]   = array(__('No.'),__('Student Name'),__('Area'),__('Category'),__('Amount'));
                $i = 1;
                foreach($bookings as $student){
                    $details[] = array(
                        $i,
                        $student['name'],
                        ($student['category'] == 'Booked Track') ? Inflector::humanize($student['area']) : Inflector::humanize($student['text']).' '.__('Lesson'),
                        $student['category'],
                        $student['price'],
                    );
                    $i++;
                }
                
                break;
            case 'ongoing_list':
                $filename = __('Igangværende liste ').date('d/m/Y H:i:s').'.csv';
                $details[0]   = array(__('No.'), __('Navn'), __('Adresse'), __('Teoriprøve'), __('Køreprøve'), __('Papirer afleveret'),  __('Antal timer'), __('Elev Saldo'), __('Køreprøve booket'), __('Sidste booking'), __('Dato for næste køretid') );                  
                $i = 1;
                foreach($bookings as $Booking){
                    $details[] = array(
                        $i,
                        $Booking['full_name'],
                        $Booking['full_address'],
                        $Booking['theory_test_passed'],
                        $Booking['passed_count'] - $Booking['dumped_count'],
                        ($Booking['handed_firstaid_papirs'] == 1) ? 'Ja '.date('d.m.Y', strtotime($Booking['firstaid_papirs_date'])) : '',
                        $Booking['total_count'],
                        $Booking['balance'],
                        (!empty($Booking['first_booking_date'])) ? date('d.m.Y', strtotime($Booking['first_booking_date'])) : '',
                        (!empty($Booking['last_booking_date'])) ? date('d.m.Y', strtotime($Booking['last_booking_date'])) : '',
                        (!empty($Booking['next_booking_date'])) ? date('d.m.Y', strtotime($Booking['next_booking_date'])) : ''
                    );
                    $i++;
                }
                
                break;
            
            case 'student_track_report':
                // $processedArr = $this->processData($bookings);
                $processedArr = $defaultData;
                $bookings = $processedArr['modifiedBookingsArr'];
                $courses = $this->Course->find('list');
                if(!empty($this->request->query['searchString'])) {
                    $str = implode(' ',$this->request->query['searchString']);
                    $filename = __('Teacher Wise Track Report').$str.__(' On ').date('d/m/Y H:i:s',time()).'.csv';
                } else {
                    $filename = __('Teacher Wise Track Report On ').date('d/m/Y H:i:s',time()).'.csv';
                }
                
                $details[0]   = array(
                    __('Date'),
                    __('Area'),
                    __('Courses'),
                    __('Booked By'),
                    __('Co Instructor'),
                    __('Teacher\'s City'),
                    __('Driving School'),
                    __('Note'),
                    __('Instructor Name'),
                    __('Track'),
                    __('Time Slot'),
                    __('Student Name')
                );
                $i = 1;
                foreach($bookings as $booking) {
                    $trackAddress = ($booking['address'] != '' && !is_null($booking['address'])) ? ' ('.$booking['address'].')' : '';
                    $details[] = array(
                        date('d/m/Y',strtotime($booking['date'])),
                        $this->areaListArr[$booking['area_slug']],
                        (!empty($booking['course'])) ? $courses[$booking['course']] : '',
                        (!empty($booking['booking_teacher_id']))? $users[$booking['booking_teacher_id']]['firstname'].' '.$users[$booking['booking_teacher_id']]['lastname'] : '',
                        ((!empty($booking['co_teacher'])) && isset($users[$booking['co_teacher']]))?$users[$booking['co_teacher']]['firstname'].' '.$users[$booking['co_teacher']]['lastname']:'',
                        (!empty($booking['teacher_id'])) ? $users[$booking['teacher_id']]['city'] :'',
                        (!empty($booking['teacher_id']) && isset($this->drivingSchools[$users[$booking['teacher_id']]['company_id']])) ? $this->drivingSchools[$users[$booking['teacher_id']]['company_id']] : '',
                        $booking['full_description'],
                        (isset($users[$booking['teacher_id']])) ? $users[$booking['teacher_id']]['firstname'].' '.$users[$booking['teacher_id']]['lastname'] : 'N/A',
                        $tracks[$booking['track_id']],
                        $booking['time_slot'],
                        (isset($booking['student_id']) && !empty($booking['student_id']) && isset($users[$booking['student_id']])) ?
                                    $users[$booking['student_id']]['firstname'].' '.$users[$booking['student_id']]['lastname'] : ((!empty($booking['name']))?
                                    $booking['name'].$trackAddress : __('External Student')),
                    );
                    $i++;
                }
                $details[] = array('','');
                $studentCount = $processedArr['studentDetails'];
                if(isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school']) && !empty($studentCount)) {
                foreach($studentCount as $coursesId => $count) {
                    $details[] = array(__('Course'),$courses[$coursesId]);
                    $details[] = array(__('Students from this driving school'),(isset($count['own_students'])) ? $count['own_students'] : 0);
                    $details[] = array(__('Students from other driving schools'),(isset($count['other_students'])) ? array_sum($count['other_students']) : 0);
                    if(isset($count['other_students']) && is_array($count['other_students'])) {
                        foreach($count['other_students'] as $school => $number) {
                            $details[] = array($this->drivingSchools[$school],$number);
                        }
                    }
                    
                    $details[] = array(__('Own students and Other teachers'),(isset($count['own_students_other_teachers'])) ? array_sum($count['own_students_other_teachers']) : 0);
                    if(isset($count['own_students_other_teachers']) && is_array($count['own_students_other_teachers'])) {
                        foreach($count['own_students_other_teachers'] as $school => $number) {
                            $details[] = array($this->drivingSchools[$school],$number);
                        }
                    }
                    $details[] = array('');
                }
                }
                break;
                
            case 'booking_report':
                 
                if(!empty($this->request->query['searchString'])) {
                    $str = implode(' ',$this->request->query['searchString']);
                    $filename = __('Track Booking Report').$str.__(' On ').date('d/m/Y H:i:s',time()).'.csv';
                } else {
                    $filename = __('Track Booking Report On ').date('d/m/Y H:i:s',time()).'.csv';
                }
                
                $bookings = $this->processData($bookings);
                
                $details[0]   = array(__('No.'),
                    __('Area'),
                    __('Track'),
                    __('Date'),
                    __('Time Slot'),
                    __('Instructor Name'),
                    __('Booked By'),
                    __('Co Instructor'),
                    __('Driving School'),
                    __('Note'),
                    __('Student Name'));
                $i = 1;
                foreach($bookings as $booking){
                    $details[] = array(
                        $i,
                        $this->areaListArr[$booking['area_slug']],
                        $tracks[$booking['track_id']],
                        date('d/m/Y',strtotime($booking['date'])),
                        $booking['time_slot'],
                        ((!empty($booking['released_by'])) && isset($users[$booking['released_by']]))?$users[$booking['released_by']]['firstname'].' '.$users[$booking['released_by']]['lastname']:'',
                        ((!empty($booking['on_behalf'])) && isset($users[$booking['on_behalf']]))?$users[$booking['on_behalf']]['firstname'].' '.$users[$booking['on_behalf']]['lastname']:'',
                        ((!empty($booking['co_teacher'])) && isset($users[$booking['co_teacher']]))?$users[$booking['co_teacher']]['firstname'].' '.$users[$booking['co_teacher']]['lastname']:'',
                        ((!empty($booking['user_id'])) && isset($users[$booking['user_id']]) && ($users[$booking['user_id']]['role'] == 'external_teacher'))?$users[$booking['user_id']]['company']:'',
                        $booking['note'],
                        (!empty($booking['student_id']) && ($booking['student_id'] != '-1') && isset($users[$booking['student_id']])) ? $users[$booking['student_id']]['firstname'] .' '.$users[$booking['student_id']]['lastname'] : __('External Student'),
                    );
                    $i++;
                }
                
                break;
                
            case 'unapproved_driving_lessons':
                $filename   = __('Unapproved Driving Lessons On ').date('d/m/Y H:i:s',time()).'.csv';
                if(!empty($this->request->query['searchString'])) {
                    $str = implode(' ',$this->request->query['searchString']);
                    $filename = __('Unapproved Driving Lessons ').$str.__(' On ').date('d/m/Y H:i:s',time()).'.csv';
                }
                $types      = Configure::read('bookingType'); 
                $lessonTime = Configure::read('lessonTime');
                $status     = Configure::read('lessonStatus');                
                $details[]  = array(__('No.'),__('Teacher Name'),__('Student Name'),__('Type'),__('Start Time'),__('Lesson Time'),__('Status'));
                
                $i = 1;
                foreach($bookings as $booking){
                    $details[] = array(
                        $i,
                        $this->users[$booking['DrivingLesson']['teacher_id']]['firstname'].' '.$this->users[$booking['DrivingLesson']['teacher_id']]['lastname'],
                        $this->users[$booking['DrivingLesson']['student_id']]['firstname'].' '.$this->users[$booking['DrivingLesson']['student_id']]['lastname'],
                        $types[$booking['DrivingLesson']['type']],
                        date('d.m.Y H:i:s',strtotime($booking['DrivingLesson']['start_time'])),
                        $lessonTime[$booking['DrivingLesson']['lesson_time']],
                        (!is_null($booking['DrivingLesson']['status'])) ? $status[$booking['DrivingLesson']['status']] : 'N/A',
                    );
                    $i++;
                }
                
                break;
                
            case 'future_bookings':
                if(!empty($this->request->query['searchString'])) {
                    $str = implode(' ',$this->request->query['searchString']);
                    $filename = __('Future Booking Report').$str.__(' On ').date('d/m/Y H:i:s',time()).'.csv';
                } else {
                    $filename = __('Future Booking Time Wise Track Report On ').date('d/m/Y H:i:s',time()).'.csv';
                }

                $details[0]   = array(__('No.'),__('Area'),__('Track'),__('Date'),__('Time Slot'),__('Instructor Name'),
                                __('Student Name'));
                $i = 1;
                foreach($bookings as $booking){
                    $details[] = array(
                        $i,
                        $this->areaListArr[$booking['area_slug']],
                        $tracks[$booking['track_id']],
                        date('d/m/Y',strtotime($booking['date'])),
                        $booking['time_slot'],
                        $users[$booking['user_id']]['firstname']    .' '.$users[$booking['user_id']]['lastname'],
                        (!empty($booking['student_id'])) ? $users[$booking['student_id']]['firstname'] .' '.$users[$booking['student_id']]['lastname'] : __('External Student'),
                    );
                    $i++;
                }

                break;
                
            case 'hourly_report' :
                
                if(!empty($this->request->query['searchString'])) {
                    $str = implode(' ',$this->request->query['searchString']);
                    $filename = __('Hourly Report').$str.__(' On ').date('d/m/Y H:i:s',time()).'.csv';
                } else {
                    $filename = __('Hourly Report On ').date('d/m/Y H:i:s',time()).'.csv';
                }
                
                $cities         = $this->City->find('list',array('fields' => array('slug','name')));
                $drivingTypes   = $this->DrivingType->find('list',array('fields' => array('slug','name')));
                
                $details[0]   = array(__('No.'),__('Area'),__('Track'),__('Date'),__('Time Slot'),__('Teacher Time'),__('Instructor Name'),__('Student Name'),__('Booking Type'));
                $i = 1;
                foreach($bookings['bookings'] as $booking){
                    $details[] = array(
                        $i++,
                        $this->areaListArr[$booking['area_slug']],
                        $tracks[$booking['track_id']],
                        date('d/m/Y',strtotime($booking['date'])),
                        $booking['time_slot'],
                        $booking['time_min'],
                        $users[$booking['user_id']]['firstname']    .' '.$users[$booking['user_id']]['lastname'],
                        (!empty($booking['student_id']) && isset($users[$booking['student_id']])) ? $users[$booking['student_id']]['firstname'] .' '.$users[$booking['student_id']]['lastname'] : __('External Student'),
                        Inflector::humanize($booking['type']),
                    );
                }
                
                $details[]   = array('','','','','','','','');
                $details[] = array(__('No'),__('Instrucutor Name'),__('Type'),__('Registered From'),__('Purpose / City / Driving Type'),' ',' ');
                $i = 1;
                foreach($bookings['teacherRegisterTimes'] as $data) {
                    if($data['TeacherRegisterTime']['type'] == 'other'){
                        $text   = $data['TeacherRegisterTime']['purpose'];
                    }else if($data['TeacherRegisterTime']['type'] == 'driving'){
                        $text   = $drivingTypes[$data['TeacherRegisterTime']['driving_type']];
                    }else{
                        $text   = $cities[$data['TeacherRegisterTime']['city']];
                    }                        
                    $details[] = array(
                        $i++,
                        $users[$data['TeacherRegisterTime']['user_id']]['firstname']    .' '.$users[$data['TeacherRegisterTime']['user_id']]['lastname'],
                        Inflector::humanize($data['TeacherRegisterTime']['type']),
                        $data['TeacherRegisterTime']['from'],
                        $text
                    );
                }
                
                $details[]   = array('','','','','','','','');
                $details[] = array(__('No'),__('Instrucutor Name'),__('Unavailable From'),__('Unavailable Till'),' ',' ',' ');
                $i = 1;
                foreach($bookings['teacherAvailability'] as $data) {
                    $details[] = array(
                        $i++,
                        $users[$data['TeacherUnavailability']['user_id']]['firstname']    .' '.$users[$data['TeacherUnavailability']['user_id']]['lastname'],
                        $data['TeacherUnavailability']['from'],
                        $data['TeacherUnavailability']['to']
                    );
                }
                
                $types      = Configure::read('bookingType'); 
                $lessonTime = Configure::read('lessonTime');
                $status     = Configure::read('lessonStatus');
                
                $details[]   = array('','','','','','','','');
                $details[] = array(__('No'),__('Instrucutor Name'),__('Student Name'),__('Type'),__('Start Time'),__('Lesson Time'),__('Status'));
                $i = 1;
                foreach($bookings['teacherDrivingLessons'] as $data) {
                    $details[] = array(
                        $i++,
                        $users[$data['DrivingLesson']['teacher_id']]['firstname'].' '.$users[$data['DrivingLesson']['teacher_id']]['lastname'],
                        $users[$data['DrivingLesson']['student_id']]['firstname'].' '.$users[$data['DrivingLesson']['student_id']]['lastname'],
                        $types[$data['DrivingLesson']['type']],
                        date('d.m.Y H:i:s',strtotime($data['DrivingLesson']['start_time'])),
                        $lessonTime[$data['DrivingLesson']['lesson_time']],
                        (!is_null($data['DrivingLesson']['status']))?$status[$data['DrivingLesson']['status']]:'NA'
                    );
                }
                break;
                
            case 'theory_report':
                if(!empty($this->request->query['searchString'])) {
                    $str = implode(' ',$this->request->query['searchString']);
                    $filename = __('Theory Report').$str.__(' On ').date('d/m/Y H:i:s',time()).'.csv';
                } else {
                    $filename = __('Theory Report On ').date('d/m/Y H:i:s',time()).'.csv';
                }
                
                $details[0] = array(__('No'),__('Instrucutor Name'),__('Registered From'),__('City'));
                $i = 1;
                foreach($bookings as $data) {
                    $details[] = array(
                        $i++,
                        $users[$data['TeacherRegisterTime']['user_id']]['firstname']    .' '.$users[$data['TeacherRegisterTime']['user_id']]['lastname'],
                        $data['TeacherRegisterTime']['from'],                       
                        $data['TeacherRegisterTime']['city']
                    );
                }
                break;
                
            case 'driving_lessons':
            
                $filename   = __('Driving Lessons On ').date('d/m/Y H:i:s',time()).'.csv';
                $types      = Configure::read('bookingType'); 
                $lessonTime = Configure::read('lessonTime');
                $status     = Configure::read('lessonStatus');                
                $details[]  = array(__('No.'),__('Teacher Name'),__('Student Name'),__('Type'),__('Start Time'),__('Lesson Time'),__('Status'));
                
                $i = 1;
                foreach($bookings as $booking){
                    $details[] = array(
                        $i,
                        $this->users[$booking['DrivingLesson']['teacher_id']]['firstname'].' '.$this->users[$booking['DrivingLesson']['teacher_id']]['lastname'],
                        $this->users[$booking['DrivingLesson']['student_id']]['firstname'].' '.$this->users[$booking['DrivingLesson']['student_id']]['lastname'],
                        $types[$booking['DrivingLesson']['type']],
                        date('d.m.Y H:i:s',strtotime($booking['DrivingLesson']['start_time'])),
                        $lessonTime[$booking['DrivingLesson']['lesson_time']],
                        (!is_null($booking['DrivingLesson']['status'])) ? $status[$booking['DrivingLesson']['status']] : __('N/A')
                    );
                    $i++;
                }
                
                break;
                
             case 'open_students':
            
                $filename   = __('Current Open Student Details On ').date('d/m/Y H:i:s',time()).'.csv';
                $types      = Configure::read('bookingType'); 
                $lessonTime = Configure::read('lessonTime');
                $status     = Configure::read('lessonStatus');                                
                $details[]  = array(__('No.'),__('Student Name'),__('Student Number'),__('Phone Number'),__('Last Module'),
                                    __('Number of Driving Test'),__('Hours'),__('Balance'));
                
                $i = 1;
                foreach($bookings as $booking){
                    $details[] = array(
                        $i,
                        $booking['User']['firstname'].' '.$booking['User']['lastname'],
                        $booking['User']['student_number'],
                        $booking['User']['phone_no'],
                        $booking['User']['module'],
                        $booking['User']['driving_lessons_count'],
                        $booking['User']['time'],
                        $booking['User']['balance']
                    );
                    $i++;
                }
                
                break;
        }
        
        $this->set(array(
            'details'  => $details,
            'filename' => $filename,
        ));
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header("Pragma: no-cache");
        $csv_file = fopen('php://output', 'w');
        // $details  =array_map("utf8_decode",$details);
        foreach($details as $fields){      
            fputcsv($csv_file,$fields,',','"');
        }

        fclose($csv_file);
        die();
        $this->layout = 'csv';
    }
    
    public function studentTrackReport() {

        $conditions     = array();
        $searchString   = array();
        $bookingJoins   = array();
        
        $joins['Booking']  = array(
            'table'         => 'bookings',
            'alias'         => 'Booking',
            'type'          => 'INNER',
            'conditions'    => array(
                'BookingTrack.booking_id = Booking.id'
            )
        );
        $bookingJoins['BookingTrack']  = array(
            'table'         => 'booking_tracks',
            'alias'         => 'BookingTrack',
            'conditions'    => array(
                'Booking.id = BookingTrack.booking_id'
            )
        );
        $isSchoolSearch = 0;
        if(isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school'])) {
            $isSchoolSearch = 1;
            $conditions['User.company_id']   = $this->request->query['driving_school'];
            $joins['User']  = array(
                'table'         => 'users',
                'alias'         => 'User',
                'type'          => 'LEFT',
                'conditions'    => array(
                    'OR' => array(
                    'Booking.user_id = User.id',
                    'BookingTrack.booking_user_id = User.id',
                ))
            );            
            $bookingJoins['User']  = array(
                'table'         => 'users',
                'alias'         => 'User',
                'type'          => 'LEFT',
                'conditions'    => array(
                    'OR' => array(
                    'Booking.user_id = User.id',
                    'BookingTrack.booking_user_id = User.id',
                ))
            );
            if(isset($this->drivingSchools[$this->request->query['driving_school']])) {
                $searchString[] = ' '.__('of').' '.$this->drivingSchools[$this->request->query['driving_school']].' School';
            }
        }
        
        if(isset($this->request->query['teacher_id']) && !empty($this->request->query['teacher_id'])) {
            $conditions['Booking.user_id'] = $this->request->query['teacher_id'];
            $searchString[] = ' '.__('of').' '.$this->users[$this->request->query['teacher_id']]['firstname'].' '.$this->users[$this->request->query['teacher_id']]['lastname'];
        }
        
        if(isset($this->request->query['area_id']) && !empty($this->request->query['area_id'])) {
            $conditions['Booking.area_slug'] = $this->request->query['area_id'];
            $searchString[] = ' '.__('for').' '.Inflector::humanize($this->request->query['area_id']);
        }
        
        if((isset($this->request->query['date_from']) && !empty($this->request->query['date_from'])) && 
                (isset($this->request->query['date_to']) && !empty($this->request->query['date_to']))) {
            
            $dateFrom   = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_from'])));
            $dateTo     = date('Y-m-d',strtotime(str_replace('/', '-', $this->request->query['date_to'])));
            
            $conditions['Booking.date BETWEEN ? AND ?'] = array($dateFrom,$dateTo);
            $searchString[] = ' '.__('from').' '.$this->request->query['date_from'].__(' to ').$this->request->query['date_to'];
        }
        
        //$conditions['BookingTrack.track_status']        = 'closed';
        $conditions['BookingTrack.status']              = 'met';
        $conditions['BookingTrack.released_by !=']      = NULL;
        //$conditions['BookingTrack.date_of_birth !=']      = NULL;
        
        $args = array(
            'fields'        => array(
                'Booking.id','Booking.date','Booking.area_slug','Booking.user_id','Booking.course','Booking.co_teacher',
                'BookingTrack.name','BookingTrack.released_by','BookingTrack.student_id','BookingTrack.track_id',
                'BookingTrack.time_slot','BookingTrack.student_id','BookingTrack.date_of_birth','BookingTrack.address'
            ),
            'joins'         => array_values($joins),
            'conditions'    => $conditions,
            'order'         => array(
                'Booking.date'  => 'ASC',
                'track'         => 'ASC',
                'timeSlot'      => 'ASC',
            ),
        );
        
        $this->perPage  = $this->getPerPage('BookingTrack');
        
        $this->Paginator->settings = $args;
        
        $bookings   = $this->Paginator->paginate('BookingTrack');
        $modifiedBookings = array();
        $i = 0;
        foreach($bookings as $booking) {
            if((!is_null($booking['BookingTrack']['date_of_birth']) || (isset($this->users[$booking['BookingTrack']['student_id']]) && !is_null($this->users[$booking['BookingTrack']['student_id']]['date_of_birth'])))) {
                $modifiedBookings[$booking['Booking']['id']]['Booking'] = array(
                    'date'          => $booking['Booking']['date'],
                    'area_slug'     => $booking['Booking']['area_slug'],
                    'user_id'       => $booking['Booking']['user_id'],
                    'course'        => $booking['Booking']['course'],
                    'co_teacher'    => $booking['Booking']['co_teacher'],
                );
                $modifiedBookings[$booking['Booking']['id']]['BookingTrack'][$i++] = array(
                    'track_id'      => $booking['BookingTrack']['track_id'],
                    'time_slot'     => $booking['BookingTrack']['time_slot'],
                    'student_id'    => $booking['BookingTrack']['student_id'],
                    'date_of_birth' => $booking['BookingTrack']['date_of_birth'],
                    'released_by'   => $booking['BookingTrack']['released_by'],
                    'address'       => $booking['BookingTrack']['address'],
                    'name'          => $booking['BookingTrack']['name'],
                );
            }
        }
        
        $this->request->query['searchString']    = $searchString;
        $courses = $this->Course->find('list');
        
        $this->Booking->virtualFields = array(
            'track'     => 'BookingTrack.track_id',
            'timeSlot'  => 'BookingTrack.time_slot',
        );
        
        $bookingArgs = array(
            'joins'         => array_values($bookingJoins),
            'conditions'    => $conditions,
            'order'         => array(
                'Booking.date'  => 'ASC',
                'track'         => 'ASC',
                'timeSlot'      => 'ASC',
            ),
            'group'         => array('Booking.id'),
        );
        

        $bookingTrackArgs = array(
            'joins'         => array_values($joins),
            'conditions'    => $conditions,
            'order'         => array(
                'Booking.date'  => 'ASC',
                //'track'         => 'ASC',
                // 'timeSlot'      => 'ASC',
            ),
        );


        $allBookings    = $this->Booking->find('all',$bookingArgs);

        $trackIds = [];

        if($isSchoolSearch)
        {
            $dbo = $this->BookingTrack->getDatasource();    
            $allBookingTracks    = $this->BookingTrack->find('all',$bookingTrackArgs);
            if(count($allBookingTracks) > 0)
            {
                foreach($allBookingTracks as $bkTrack)
                {
                    $trackIds[] = $bkTrack['BookingTrack']['id'];
                }
            }
        }


        $driving_school =  $this->request->query['driving_school'];
        $processedArr   = $this->processDatastudentTrackReport($allBookings,$driving_school);
        $studentDetails = $processedArr['studentDetails'];
        $allBookingsModifiedArr = array();
        $i = 0;
        foreach($allBookings as $booking) {
            
            $booking['BookingTrack'] = Hash::sort($booking['BookingTrack'],'{n}.track_id');
            foreach($booking['BookingTrack'] as $track) {
                if((!empty($track['date_of_birth']) || (isset($this->users[$track['student_id']]) && !empty($this->users[$track['student_id']]['date_of_birth']))) && !empty($track['released_by'])) {
                    $allBookingsModifiedArr[$i] = $booking;
                }
            }
            $i++;
        }

        $processedArr = $this->processData($allBookings, $trackIds);
        $filterRecords = $processedArr['modifiedBookingsArr'];        
        $studentDetails = $processedArr['studentDetails'];
        
        $this->set(array(
            // 'bookings'          => $modifiedBookings,
            'users'             => $this->users,
            'tracks'            => $this->tracks,
            'perPage'           => $this->perPage,
            'searchString'      => $searchString,
            'courses'           => $courses,
            'studentDetails'    => $studentDetails,
            'drivingSchools'    => $this->drivingSchools,
            'processedArr'      => $processedArr,
            'allBookings'       => $allBookingsModifiedArr,
            'filterRecords' => $filterRecords
        ));
        if(isset($this->request->query['csv']) && $this->request->query['csv'] == 'true') {
            $this->createCsv($allBookings,$processedArr);
        } else if(isset($this->request->query['pdf']) && $this->request->query['pdf'] == 'true') {
            $this->layout = 'pdf';
            $this->response->type('application/pdf');
            $this->render('student_track_report_pdf');
        } else {
            $this->render('student_track_report');
        }
    }
    
    private function processDatastudentTrackReport($bookings,$driving_school) {
        
        $modifiedBookingsArr    = array();
        $studentCountArr        = array();
        
        $bookedCourses = Hash::extract($bookings,'{n}.Booking.course');
        $courseStudent = array();
        $users = $this->users;
        
        foreach($bookedCourses as $course) {
            foreach(array_keys($this->drivingSchools) as $school) {
                $courseStudent[$course]['own_students'][$school]                 = 0;
                $courseStudent[$course]['other_students'][$school]               = 0;
                $courseStudent[$course]['own_students_other_teachers'][$school]  = 0;
            }
        }
        foreach($bookings as $booking) {
            if(!empty($booking) && isset($booking['BookingTrack'])) {
                foreach($booking['BookingTrack'] as $bookingTrack) {
                    $teacher_id = ($bookingTrack['booking_user_id'] != '') ? $bookingTrack['booking_user_id'] : $booking['Booking']['user_id'] ;
                    $booking_teacher_id = (!empty($booking['Booking']['on_behalf'])) ? $booking['Booking']['on_behalf'] : $teacher_id;
                    $teacher_driving_school = (isset($this->users[$teacher_id]) && !empty($this->users[$teacher_id]['company_id']) ) ? $this->users[$teacher_id]['company_id'] : '';

                    $released_by = !is_null($bookingTrack['released_by']) ? $bookingTrack['released_by'] : '';
                    $booking_status = $bookingTrack['status'];

                    $date_of_birth = '';

                    if(isset($bookingTrack['student_id']) && $users[$bookingTrack['student_id']]['date_of_birth']){
                        $date_of_birth =  $users[$bookingTrack['student_id']]['date_of_birth'];
                    }else{
                        $date_of_birth = $bookingTrack['date_of_birth'];
                    }

                    if($driving_school != ''){
                        if($driving_school == $teacher_driving_school){
                            $show_is = true;
                        }else{
                            $show_is = false;
                        }
                    }else{
                        $show_is = true;
                    }
                    // $show_is = true;

                    if($released_by != '' && $booking_status == 'met' && $date_of_birth != '' && $driving_school != '' && $show_is == true) {
                        
                        $released_teacher_driving_school = $users[$released_by]['company_id'];
                        $booking_teacher_id = (!empty($booking['Booking']['on_behalf'])) ? $booking['Booking']['on_behalf'] : $teacher_id;
                        $modifiedBookingsArr[]  = array(
                            'booking_id'        => $booking['Booking']['id'],
                            'area_slug'         => $booking['Booking']['area_slug'],
                            'type'              => $booking['Booking']['type'],
                            'track_id'          => $bookingTrack['track_id'],
                            'date'              => $booking['Booking']['date'],
                            'user_id'           => $booking['Booking']['user_id'],
                            'on_behalf'         => $booking['Booking']['on_behalf'],
                            'co_teacher'        => $booking['Booking']['co_teacher'],
                            'student_id'        => $bookingTrack['student_id'],
                            'time_slot'         => $bookingTrack['time_slot'],
                            'time_min'          => $this->get_time_difference($time_slot[0],$time_slot[1]),
                            'note'              => $booking['Booking']['full_description'],
                            'created'           => $booking['Booking']['created'],
                            'course'            => $booking['Booking']['course'],
                            'full_description'  => $booking['Booking']['full_description'],
                            'name'              => $bookingTrack['name'],
                            'released_by'       => $released_by,
                            'address'           => $bookingTrack['address'],
                            'booking_user_id'   => $bookingTrack['booking_user_id'],
                            'teacher_id'        => $teacher_id,
                            'booking_teacher_id'        => $booking_teacher_id,
                        );
                        if($teacher_driving_school != $driving_school) {
                            $studentCountArr[$booking['Booking']['course']]['other_students'][$teacher_driving_school] = ++$courseStudent[$booking['Booking']['course']]['other_students'][$teacher_driving_school];
                        } else {
                            if(isset($users[$teacher_id]) && isset($users[$released_by]) && 
                            $teacher_driving_school == $driving_school &&
                            $released_teacher_driving_school == $driving_school) {
                        
                                $studentCountArr[$booking['Booking']['course']]['own_students'] = ++$courseStudent[$booking['Booking']['course']]['own_students'][$teacher_driving_school];
                            } else {
                                $studentCountArr[$booking['Booking']['course']]['own_students_other_teachers']
                                [$released_teacher_driving_school] = 
                                        ++$courseStudent[$booking['Booking']['course']]['own_students_other_teachers']
                                        [$released_teacher_driving_school];
                            }
                        }
                    }
                }
            }
        }
        if($this->request->query['report_type'] == 'student_track_report') {
            return $outputArr = array(
                'modifiedBookingsArr'   => $modifiedBookingsArr,
                'studentDetails'        => $studentCountArr,
            );
        } else {
            return $modifiedBookingsArr;
        }
    }

    private function processData($bookings, $trackIds = []) {
        
        $modifiedBookingsArr    = array();
        $studentCountArr        = array();
        
        $bookedCourses = Hash::extract($bookings,'{n}.Booking.course');
        $courseStudent = array();
        
        foreach($bookedCourses as $course) {
            foreach(array_keys($this->drivingSchools) as $school) {
                $courseStudent[$course]['own_students'][$school]                 = 0;
                $courseStudent[$course]['other_students'][$school]               = 0;
                $courseStudent[$course]['own_students_other_teachers'][$school]  = 0;
            }
        }
        foreach($bookings as $booking) {
            if(!empty($booking) && isset($booking['BookingTrack'])) {
                foreach($booking['BookingTrack'] as $bookingTrack) {
                    if(!is_null($bookingTrack['released_by']) && $bookingTrack['status'] == 'met' &&
                            (!is_null($bookingTrack['date_of_birth']) || (isset($this->users[$bookingTrack['student_id']]) && !is_null($this->users[$bookingTrack['student_id']]['date_of_birth'])))) {


                        if(count($trackIds) > 0)
                        {
                            if(!in_array($bookingTrack['id'], $trackIds))
                            {
                                continue;
                            }
                        }

                        
                        $time_slot = explode("-", $bookingTrack['time_slot']);
                        $teacher_id = ($bookingTrack['booking_user_id'] != '') ? $bookingTrack['booking_user_id'] : $booking['Booking']['user_id'] ;
                        $booking_teacher_id = (!empty($booking['Booking']['on_behalf'])) ? $booking['Booking']['on_behalf'] : $teacher_id;
                        $modifiedBookingsArr[]  = array(
                            'booking_id'        => $booking['Booking']['id'],
                            'area_slug'         => $booking['Booking']['area_slug'],
                            'type'              => $booking['Booking']['type'],
                            'track_id'          => $bookingTrack['track_id'],
                            'date'              => $booking['Booking']['date'],
                            'user_id'           => $booking['Booking']['user_id'],
                            'on_behalf'         => $booking['Booking']['on_behalf'],
                            'co_teacher'        => $booking['Booking']['co_teacher'],
                            'student_id'        => $bookingTrack['student_id'],
                            'time_slot'         => $bookingTrack['time_slot'],
                            'time_min'          => $this->get_time_difference($time_slot[0],$time_slot[1]),
                            'note'              => $booking['Booking']['full_description'],
                            'created'           => $booking['Booking']['created'],
                            'course'            => $booking['Booking']['course'],
                            'full_description'  => $booking['Booking']['full_description'],
                            'name'              => $bookingTrack['name'],
                            'released_by'       => $bookingTrack['released_by'],
                            'address'           => $bookingTrack['address'],
                            'booking_user_id'   => $bookingTrack['booking_user_id'],
                            'teacher_id'        => $teacher_id,
                            'booking_teacher_id'        => $booking_teacher_id,
                        );
                        if($this->request->query['report_type'] == 'student_track_report' && isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school'])) {
                            
                            
                            if($this->users[$teacher_id]['company_id'] != $this->request->query['driving_school']) {
                                $studentCountArr[$booking['Booking']['course']]['other_students'][$this->users[$teacher_id]['company_id']] = ++$courseStudent[$booking['Booking']['course']]['other_students'][$this->users[$teacher_id]['company_id']];
                            } else {
                                if(isset($this->users[$teacher_id]) && isset($this->users[$bookingTrack['released_by']]) && 
                                $this->users[$teacher_id]['company_id'] == $this->request->query['driving_school'] &&
                                ($this->users[$bookingTrack['released_by']]['company_id'] == $this->request->query['driving_school'])) {
                            
                                    $studentCountArr[$booking['Booking']['course']]['own_students'] = ++$courseStudent[$booking['Booking']['course']]['own_students'][$this->users[$teacher_id]['company_id']];
                                } else {
                                    $studentCountArr[$booking['Booking']['course']]['own_students_other_teachers']
                                    [$this->users[$bookingTrack['released_by']]['company_id']] = 
                                            ++$courseStudent[$booking['Booking']['course']]['own_students_other_teachers']
                                            [$this->users[$bookingTrack['released_by']]['company_id']];
                                }
                            }
                        }
                    }
                }
            }
        }
        if($this->request->query['report_type'] == 'student_track_report') {
            return $outputArr = array(
                'modifiedBookingsArr'   => $modifiedBookingsArr,
                'studentDetails'        => $studentCountArr,
            );
        } else {
            return $modifiedBookingsArr;
        }
    }

    function get_time_difference($time1, $time2){ 
        $time1 = strtotime("1/1/1980 $time1"); 
        $time2 = strtotime("1/1/1980 $time2"); 
         
        if ($time2 < $time1) 
        { 
            $time2 = $time2 + 86400; 
        } 
         
        $diff = ($time2 - $time1) / 3600; 
        $diff = $diff * 60;
        return $diff;
         
    }  
}