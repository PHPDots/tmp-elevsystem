<?php

class Systembooking extends AppModel
{
    
    public $data        = array();
    public $error_flag  = FALSE;
    public $error_msg   = array();
    
    function getAllBookings($user_id, $date, $status = array()) {
        $bookings = $this->find('all',array(
            'fields' => array('Systembooking.*', 'User.firstname','User.student_number As elev_nummer ', 'User.lastname', 'User.phone_no AS student_number', 'User.address AS student_address'),
            'joins'         => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Systembooking.student_id = User.id',
                        'User.role' => 'student'
                    )
                )
            ),
            'conditions'    => array(
                'Systembooking.user_id' => $user_id,
                'DATE(Systembooking.start_time)' => $date,
                'Systembooking.status IN ' => $status,
            )
        ));

        return $bookings;
    }

    function getAllBookingsofStudent($student_id, $date, $status = array()) {
        $bookings = $this->find('count',array(
            'fields' => array('Systembooking.id'),
            'conditions'    => array(
                'Systembooking.student_id' => $student_id,
                'Systembooking.start_time' => $date,
                'Systembooking.status IN ' => $status,
            )
        ));

        return $bookings;
    }
}