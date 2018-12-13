<?php
App::uses('Activity', 'Model');
class Booking extends AppModel
{
    public $hasMany     = array('BookingTrack');
    public $data        = array();
    public $error_flag  = FALSE;
    public $error_msg   = array();
    public $i           =  0;
    
    public function validateData($data,$isEdit = FALSE,$trackStatus = FALSE) {
        
        $this->data             = $data;
        
        $args['area']           = $this->data['Booking']['area_slug'];
        $args['date']           = $this->data['Booking']['date'];   
        
        if(isset($this->data['Booking']['area_slug']) && empty($this->data['Booking']['area_slug'])) {
            $this->error_flag   = TRUE;
            $this->error_msg[]  = array(
                'key'           => 'txt_area_error',
                'message'       => __('Please Select Area.')
            );
        }
        
        if(!$trackStatus) {
            if(strtotime($args['date']) < strtotime(date('Y-m-d',time())." 00:00:00") && (!$isEdit)) {
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_booking_details',
                    'message'       => __('You cannot make a booking for past date.')
                );
            }
        }
        
        if(!isset($this->data['Booking']['isDead']) || $this->data['Booking']['isDead'] != TRUE) {
            if(empty($this->data['BookingTrack'])){
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_booking_details',
                    'message'       => __('Please Enter Atleast One Booking Details.')
                );
            }
            
            if(isset($this->data['Booking']['on_behalf']) && ($this->data['Booking']['on_behalf'])) {
                if(empty($this->data['Booking']['user_id'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_booking_users',
                        'message'       => __('Please Select User.')
                    );
                }
            }
            
            if($isEdit) {
                $args['id']         = $this->data['Booking']['id'];
            }
            
            $generatedTimeSlots     = $this->getBookingTimeSlotsForTheDay($args);
            
            $bookedTracks   = array();
            $bookedStudents = array();
            
            foreach($this->data['BookingTrack'] as $bookingTrack) {
                $bookedTracks[$bookingTrack['track_id']][]      = $bookingTrack['time_slot'];
                if(!empty($bookedStudents) && isset($bookedStudents[$bookingTrack['time_slot']]) && in_array($bookingTrack['student_id'],$bookedStudents[$bookingTrack['time_slot']])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_booking_details',
                        'message'       => __('You Can not Add Single Student For Multiple Tracks.')
                    );
                    break;
                }     
                if(($bookingTrack['student_id'] != -1) && ($bookingTrack['student_id'] != NULL)) {
                    $bookedStudents[$bookingTrack['time_slot']][]   = $bookingTrack['student_id'];
                }
            }
            $role   =  CakeSession::read("Auth.User.role");
            if($role != 'external_teacher') {
                foreach($this->data['BookingTrack'] as $bookingTrack) {
                    if(isset($bookingTrack['unknown']) && ($bookingTrack['unknown'] == 1)) {
                       /*if(empty($bookingTrack['name'])){
                           $this->error_flag   = TRUE;
                           $this->error_msg[]  = array(
                               'key'           => 'txt_booking_name_'.$bookingTrack['track_id'],
                               'message'       => __('Please fill up student name.')
                           )
                       }
                       
                       if(empty($bookingTrack['phone'])) {
                           $this->error_flag   = TRUE;
                           $this->error_msg[]  = array(
                               'key'           => 'txt_booking_phone_'.$bookingTrack['track_id'],
                               'message'       => __('Please fill up student phone number.')
                           );
                       }*/
                        
                        if(!empty($bookingTrack['student_id']) && ($bookingTrack['student_id'] != -1)) {
                            $this->error_flag   = TRUE;
                            $this->error_msg[]  = array(
                                'key'           => 'txt_booking_details',
                                'message'       => __('You can not select student you have selected unknown student.')
                            );
                        }
                    }
                }
            }
            
            foreach($bookedTracks as $trackID => $timeSlots) {
                
                $result     = array_intersect($generatedTimeSlots['actual'][$args['area']],$timeSlots);
                $resultKeys = array_keys($result);
                
                foreach($resultKeys as $key => $resultKey) {
                    if(isset($resultKeys[$key+1]) && !empty($resultKeys[$key+1])) {
                        $value  = abs($resultKey - $resultKeys[$key+1]);
                        if($value > 1) {
                            $this->error_flag   = TRUE;
                            $this->error_msg[]  = array(
                                'key'           => 'txt_booking_details',
                                'message'       => __('Time Slots Should be in sequence.')
                            );
                            
                            $errorDetail   = array(
                                'status'    => $this->error_flag,
                                'error_msg' => $this->error_msg,
                            );
                            
                            return $errorDetail;
                        }
                    }
                }
            }
            if(!$trackStatus) {
                foreach($bookedStudents as $timeSlot => $students) {
                    foreach($students as $student) {
                        if(isset($generatedTimeSlots['students'][$timeSlot]) && in_array($student,$generatedTimeSlots['students'][$timeSlot])) {
                            $this->error_flag   = TRUE;
                            $this->error_msg[]  = array(
                                'key'           => 'txt_booking_details',
                                'message'       => __('You Can not Add Single Student For Multiple Tracks.')
                            );
                            
                            $errorDetail   = array(
                                'status'    => $this->error_flag,
                                'error_msg' => $this->error_msg,
                            );
                            
                            return $errorDetail;
                            
                            break;
                        }
                    }
                }
            }
            if(!$trackStatus) {
                $bookedTracks   = array_values($bookedTracks);
                for($i = 0;$i<count($bookedTracks);$i++) {
                    if(count($bookedTracks[0]) > count($bookedTracks[$i])) {
                        $result     = array_diff($bookedTracks[0],$bookedTracks[$i]);
                        if(!empty($result)) {
                            $this->error_flag   = TRUE;
                            $this->error_msg[]  = array(
                                'key'           => 'txt_booking_details',
                                'message'       => __('Please Select minimum timeslot as selected in first track.')
                            );
                            
                            $errorDetail   = array(
                                'status'    => $this->error_flag,
                                'error_msg' => $this->error_msg,
                            );
                            
                            return $errorDetail;
                            
                            break;
                        }
                    }
                }
            }
        }
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
    
    public function validateReleasedTrack($data,$studentArr) {
        $this->data = $data;
        $i = 1;
        foreach($this->data as $booking) {
            if(isset($booking['student_id']) && !empty($booking['student_id'])) {
                if(in_array($booking['student_id'], $studentArr)) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_student_id_'.$i,
                        'message'       => __('Student already selected')
                    );
                    break;
                }
            }
            
           /*if(isset($booking['other_student']) && $booking['other_student'] == 1) {
               if(!empty($booking['student_name']) && empty($booking['student_id'])) {
                   $this->error_flag   = TRUE;
                   $this->error_msg[]  = array(
                       'key'           => 'txt_student_id_'.$i,
                       'message'       => __('New student not selected')
                   );
                   break;
               }
                
               if($booking['status_notMet'] == 0 ) {
                   $this->error_flag   = TRUE;
                   $this->error_msg[]  = array(
                       'key'           => 'txt_not_met_'.$i,
                       'message'       => __('Please check Not Met status')
                   );
                   
                   if($booking['status_met'] == 1 ) {
                       $this->error_flag   = TRUE;
                       $this->error_msg[]  = array(
                           'key'           => 'txt_met_'.$i,
                           'message'       => __('Please uncheck Met status')
                       );
                   }
                   break;
               }
           }*/
            
            if($booking['status_met'] == 1 && $booking['status_notMet'] == 1 ) {
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_not_met_'.$i,
                    'message'       => __('Please check only one status')
                );
                break;
            }
            if($booking['status_met'] == 0 && $booking['status_notMet'] == 0 && $booking['other_student'] == 0) {
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_student_id_'.$i,
                    'message'       => __('Please check atleast one status')
                );
                break;
            }
            
            $i++;
        }
        
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
    
    public function validateTrackUser($data,$statusUpdate) {
        $this->data = $data;
        $i = 1;
        foreach($this->data['BookingTrack'] as $id => $value) {
            if(is_array($value) && ($id == 1 || $id == 2)) {
                if(isset($value['name']) && empty($value['name'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_error_name'.$i,
                        'message'       => __('Student name cannot be empty'),
                    );
                }
                
                if(isset($value['phone']) && empty($value['phone'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_error_phone'.$i,
                        'message'       => __('Phone no. cannot be empty'),
                    );
                }
                
                if(isset($value['address']) && empty($value['address'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_error_address'.$i,
                        'message'       => __('Address cannot be empty'),
                    );
                }
                if(isset($value['zip_code']) && empty($value['zip_code'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'zip_code'.$i,
                        'message'       => __('Postnummer cannot be empty'),
                    );
                }
                if(isset($value['city']) && empty($value['city'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'city'.$i,
                        'message'       => __('City cannot be empty'),
                    );
                }
                
                if(isset($value['date_of_birth']) && empty($value['date_of_birth'])) {
                    $this->error_flag   = TRUE;
                    $this->error_msg[]  = array(
                        'key'           => 'txt_error_date_of_birth'.$i,
                        'message'       => __('Date of Birth cannot be empty'),
                    );
                }
                
               /*if(is_null($statusUpdate)) {
                   if((!isset($value['status_notMet']) && !isset($value['status_met'])) && $id == 1 && $value['status'] == '') {
                       pr('in here');
                       $this->error_flag   = TRUE;
                       $this->error_msg[]  = array(
                           'key'           => 'txt_error_not_met'.$i,
                           'message'       => __('Please check atleast one Status'),
                       );
                       break;
                   }
               
                   if((isset($value['status_notMet']) && $value['status_notMet'] == 1) && (isset($value['status_met']) && $value['status_met'] == 1)) {
                       $this->error_flag   = TRUE;
                       $this->error_msg[]  = array(
                           'key'           => 'txt_error_not_met'.$i,
                           'message'       => __('Please check only one Status'),
                       );
                   }
               
                   if(isset($value['other_student']) && $value['status'] == '') {
                       if(((isset($value['status_notMet']) && $value['status_notMet'] == 0) || !isset($value['status_notMet'])) && $id == 1) {
                           $this->error_flag   = TRUE;
                           $this->error_msg[]  = array(
                               'key'           => 'txt_error_not_met'.$i,
                               'message'       => __('Please check Not Met'),
                           );
                       }
                   }
               }*/
                $i++;
            }
        }
        
        $errorDetail   = array(
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
    
    public function updateBookingTrack($data,$updateBooking = NULL) {
        $this->data = $data['Booking'];
        $dataSource = $this->getDataSource();
        $result     = array();
        
        $generatedTimeSlots = $this->getBookingTimeSlotsForTheDay(array(
            'area'  => $this->data[0]['area'],
            'date'  => date('Y-m-d',strtotime(str_replace('.','-',$this->data[0]['date']))),
        ));
        
        $timeSlotArr = $generatedTimeSlots['mapping'][$this->data[0]['area']];
        $result[] = $this->updateAll(array(
            'co_teacher'    => (empty($updateBooking['co_teacher'])) ? NULL : "{$updateBooking['co_teacher']}",
            'course'        => (empty($updateBooking['course'])) ? NULL : "{$updateBooking['course']}",
        ), array(
            'id'    => $updateBooking['id'],
        ));
        foreach($this->data as $booking) {
            foreach($timeSlotArr[$booking['time_slot']] as $timeSlots) {
                $result[] = $this->BookingTrack->updateAll(array(
                    'release_track'             => 1,
                    'recent_realeased_tracks'   => "'".date('Y-m-d H:i:s')."'",
                    'track_status'              => "'closed'",
                    'name'                      => (!empty($booking['name']))   ? "'".$booking['name']."'"  : NULL,
                    'phone'                     => (!empty($booking['phone']))  ? "'".$booking['phone']."'" : NULL,
                    'other_student'             => "'".$booking['other_student']."'",
                    'released_by'               => CakeSession::read("Auth.User.id"),
                ), array(
                    'booking_id'    => $booking['booking_id'],
                    'track_id'      => $booking['track_id'],
                    'time_slot'     => $timeSlots,
                ));
                
                if($booking['status_notMet'] == 1) {
                    if($booking['other_student'] == 1 && !empty($booking['student_id'])) {
                        $result[] = $this->BookingTrack->updateAll(array(
                                'status'                    => '"not_met"',
                                'recent_realeased_tracks'   => "'".date('Y-m-d H:i:s')."'",
                                'track_status'              => "'closed'",
                                'name'                      => (!empty($booking['name']))?"'".$booking['name']."'":NULL,
                                'phone'                     => (!empty($booking['phone']))?"'".$booking['phone']."'":NULL,
                                'other_student'             => "'".$booking['other_student']."'",
                                'student_id'                => $booking['student_id'],
                                'released_by'               => CakeSession::read("Auth.User.id"),
                            ), array(
                                'booking_id'    => $booking['booking_id'],
                                'track_id'      => $booking['track_id'],
                                'time_slot'     => $timeSlots,
                                'student_id'    => $booking['selected_student_id'],
                            )
                        );
                    } else {
                        $result[] = $this->BookingTrack->updateAll(array(
                            'status'                    => '"not_met"',
                            'recent_realeased_tracks'   => "'".date('Y-m-d H:i:s')."'",
                            'track_status'              => "'closed'",
                            'other_student'             => "'".$booking['other_student']."'",
                            'released_by'               => CakeSession::read("Auth.User.id"),
                        ), array(
                                'booking_id'                => $booking['booking_id'],
                                'track_id'                  => $booking['track_id'],
                                'time_slot'                 => $timeSlots,
                        ));
                    }
                }
                
                if($booking['status_met'] == 1) {
                    $result[] = $this->BookingTrack->updateAll(array(
                            'status'                    => '"met"',
                            'recent_realeased_tracks'   => "'".date('Y-m-d H:i:s')."'",
                            'name'                      => (!empty($booking['name']))?"'".$booking['name']."'":NULL,
                            'phone'                     => (!empty($booking['phone']))?"'".$booking['phone']."'":NULL,
                            'other_student'             => "'".$booking['other_student']."'",
                            'released_by'               => CakeSession::read("Auth.User.id"),
                        ), array(
                            'booking_id'    => $booking['booking_id'],
                            'track_id'      => $booking['track_id'],
                            'time_slot'     => $timeSlots,
                        )
                    );
                }
            }
        }
        
        if(in_array(FALSE, $result)) {
            $dataSource->rollback();
            return FALSE;
        } else {
            $dataSource->commit();
            return TRUE;
        }
    }

    public function updateBooking($data) {
        
        $this->BookingTrack  = ClassRegistry::init('BookingTrack');
        $this->User  = ClassRegistry::init('User');

        // print_r($data['BookingTrack']);
        // exit;
        
        if(!empty($data['BookingTrack'])) {

            $bookingTracks =  $this->BookingTrack->find('all',array(
                                                                    'conditions'    => array(
                                                                            'BookingTrack.booking_id'=> $data['Booking']['id'],
                                                                            'BookingTrack.release_track !=' => 1 
                                                                            ),
                                                                    )
                                                            );

            // echo "<pre>";print_r($bookingTracks);
            // exit;


            foreach ($bookingTracks as $key => $bookingTrack) {
                $bookingTrack = $bookingTrack['BookingTrack'];
                $booking_user_id = (!empty( $bookingTrack['booking_user_id'] )) ?  $bookingTrack['booking_user_id']  : $data['Booking']['user_id'];
                $User =  $this->User->findById( $booking_user_id );

                // echo "<pre>";
                // print_r($bookingTrack);
                // print_r($User['User']['google_token']);
                // exit;
                
                if( $bookingTrack['g_cal_id'] != '' && $User['User']['google_token'] !=  '' ) 
                {
                    try
                    {
                      $client = new Google_Client();
                      $dir =  __DIR__."/../Config";
                      $client->setAuthConfigFile($dir.'/client_secret.json');
                      $token = $client->refreshToken($User['User']['google_token']);
                      $client->setAccessToken($token);
                      $service  = new Google_Service_Calendar($client);
                      $a = $service->events->delete('primary', $bookingTrack['g_cal_id']);
                    }
                    catch(\Exception $e)
                    {
                       // do nothing
                    }
                }
            }

            $this->BookingTrack->deleteAll(array(
                'BookingTrack.booking_id'       => $data['Booking']['id'],
                'BookingTrack.release_track !=' => 1 
            ));
        }
    }
    
    function getBookingTimeSlotsForTheDay($args = NULL) {
        
        $this->Area = ClassRegistry::init('Area');
        $date       = (!isset($args['date']) || is_null($args['date']))?date('Y-m-d',time()):$args['date'];
        
        $conditions                 = array();
        $areaWiseBookings           = array();
        $timeSlotsMapping           = array();
        $displayTimeSlots           = array();
        $conditions['Booking.date'] = $date;
        
        if(isset($args['area']) && !empty($args['area'])) {
            $conditions['Booking.area_slug'] = $args['area'];
        }
        
        if(isset($args['id']) && !empty($args['id'])) {
            $conditions['Booking.id !='] = $args['id'];
        }
        
        $allBookings            = $this->find('all',array(
            'conditions'        => $conditions
        ));
        
        if(isset($args['area']) && !empty($args['area'])) {
            $conditions             = array(
                'Area.slug'         => $args['area']
            );
        }
        
        $area                   = $this->Area->find('all',array(
            'conditions'        => $conditions
        ));
        
        $areaWiseTimeSlots      = Hash::combine($area,'{n}.AreaTimeSlot.{n}.id','{n}.AreaTimeSlot.{n}.time_slots','{n}.AreaTimeSlot.{n}.area_id');
        $areaName               = Hash::combine($area,'{n}.Area.id','{n}.Area');
        
        foreach($areaWiseTimeSlots as $id => $timeSlots) {
            $actualTimeSlots[$areaName[$id]['slug']]  = array_values($timeSlots);
        }
        
        if(!empty($allBookings)) {    
            foreach($allBookings as $booking) {
                foreach($booking['BookingTrack'] as $key => $bookingTrack) {
                    if($bookingTrack['other_student'] == 1) {
                        unset($booking['BookingTrack'][$key]);
                    }
                }
                
                $bookedTimeSlots = Hash::combine($booking['BookingTrack'],'{n}.track_id','{n}.time_slot','{n}.track_id'); 
                //$bookedTimeSlots = array();
                foreach($bookedTimeSlots as $timeSlots) {
                   
                    $timeSlots    = array_values($timeSlots);
                    
                    if(!empty($timeSlotsMapping)) {
                        foreach($timeSlotsMapping[$booking['Booking']['area_slug']] as $timeSlot) {
                            foreach($timeSlot as $slot) {
                                if(in_array($slot,$timeSlots)) {
                                    $key   = array_search($slot,$timeSlots);
                                    unset($timeSlots[$key]);
                                }
                            }
                        }
                   }
                
                   $flag    = TRUE;
                   
                   $resultKeys = array_keys($timeSlots);
                   
                   foreach($resultKeys as $key => $resultKey) {
                        if(isset($resultKeys[$key+1]) && !empty($resultKeys[$key+1])) {
                            $value  = abs($resultKey - $resultKeys[$key+1]);
                            if($value > 1) {
                                $flag = FALSE;
                            }
                        }
                    }

                    $timeSlots   = array_values($timeSlots);
                   
                    if((count($timeSlots) > 1) && ($flag)) {
                        $firstTimeSlot   = explode('-',$timeSlots[0]);
                        $lastTimeSlot    = explode('-',$timeSlots[count($timeSlots) - 1]);
                       
                        $timeSlotsMapping[$booking['Booking']['area_slug']][$firstTimeSlot[0].'-'.$lastTimeSlot[1]]  = $timeSlots;

                        $displayTimeSlots[$booking['Booking']['area_slug']][$firstTimeSlot[0].'-'.$lastTimeSlot[1]]   = $firstTimeSlot[0].'-'.$lastTimeSlot[1];
                    } else if((count($timeSlots) > 1) && (!$flag)) {
                      
                        foreach($timeSlots as $timeSlot){
                            $timeSlotsMapping[$booking['Booking']['area_slug']][$timeSlot][]    = $timeSlot;      
                            $displayTimeSlots[$booking['Booking']['area_slug']][$timeSlot]      = $timeSlot;
                        }
                    } else if(count($timeSlots) == 1) {
                       $timeSlots   = array_values($timeSlots);
                       $timeSlotsMapping[$booking['Booking']['area_slug']][$timeSlots[0]][]     = $timeSlots[0];
                       $displayTimeSlots[$booking['Booking']['area_slug']][$timeSlots[0]]       = $timeSlots[0];
                    }
               }
            }
        } else {
            foreach($actualTimeSlots as $area => $timeSlots) {
                foreach($timeSlots as $timeSlot) {
                    $displayTimeSlots[$area][$timeSlot]  = $timeSlot;
                    $timeSlotsMapping[$area][$timeSlot]  = array($timeSlot);
                }
            }
        }
        
        foreach($actualTimeSlots as $area => $timeSlots) {
            
            if(isset($timeSlotsMapping[$area]) && !empty($timeSlotsMapping[$area])) {
                
                foreach($timeSlotsMapping[$area] as $timeSlot => $slots) {
                    foreach($slots as $slot){
                        $allSlots[] = $slot;
                    }
                }
                
                foreach($timeSlots as $slot) {
                    if(!in_array($slot,$allSlots)) {
                        $displayTimeSlots[$area][$slot] = $slot;
                        $timeSlotsMapping[$area][$slot] = array($slot);
                    } else {
                        $key = array_search($slot,$timeSlots);
                        unset($timeSlots[$key]);
                    }
                }
            } else {
                foreach($timeSlots as $timeSlot) {
                    $displayTimeSlots[$area][$timeSlot]  = $timeSlot;
                    $timeSlotsMapping[$area][$timeSlot]  = array($timeSlot);
                }
            }
        }
        
        foreach ($timeSlotsMapping as $area => $timeSlotMapping) {
            foreach($timeSlotMapping as $slot => $mapping) {
                $timeSlotsMapping[$area][$slot] = array_unique($mapping);
            }
        }
        
        $allReadyBookedStudents     = array();
        if(!empty($allBookings)) {
            foreach($allBookings as $studentBooking) {
                foreach($studentBooking['BookingTrack'] as $student) {
                    //                    $allReadyBookedStudents[$student['time_slot']][]    = $student['student_id'];
                    $allReadyBookedStudents[$student['time_slot']][$student['track_id']]    = $student['student_id'];
                }
            }
        }
        
        if(isset($args['area']) && !empty($args['area'])) {
            ksort($timeSlotsMapping[$args['area']]);
            ksort($displayTimeSlots[$args['area']]);
        } else {
            foreach($timeSlotsMapping as $area => $slots) {
                ksort($timeSlotsMapping[$area]);
                ksort($displayTimeSlots[$area]);
            }
        }
        
        $arrayOfTimeSlots   = array(
            'actual'        => $actualTimeSlots,
            'display'       => $displayTimeSlots,
            'mapping'       => $timeSlotsMapping,
            'students'      => $allReadyBookedStudents
        );
        
        return $arrayOfTimeSlots;
    }
    
    function bookingNotification()
    {
        $this->EmailQueue           = ClassRegistry::init('EmailQueue');
        $this->SmsQueue             = ClassRegistry::init('SmsQueue');
        $this->User                 = ClassRegistry::init('User');
        $this->Track                = ClassRegistry::init('Track');
        $this->Option               = ClassRegistry::init('Option');
        $teacherNotificaitonTime    = $this->Option->getOption('teacher_notification_time');
        $studentNotificaitonTime    = $this->Option->getOption('student_notification_time');

        //echo "==Teacher Notification==>".$teacherNotificaitonTime."<====\n";
        //echo "==Student Notification==>".$studentNotificaitonTime."<====\n";

        $studentStartTime           = date('H:i', (time()+($studentNotificaitonTime*60)));
        $studentEndTime             = date('H:i', (time()+($studentNotificaitonTime*60)+(60*60)));
        $teacherStartTime           = date('H:i', (time()+($teacherNotificaitonTime*60)));
        $teacherEndTime             = date('H:i', (time()+($teacherNotificaitonTime*60)+(60*60)));

        //echo "==Student Start time==>".$studentStartTime."<===End time==>".$studentEndTime."<===\n";
        //echo "==Teacher Start time==>".$teacherStartTime."<===End time==>".$teacherEndTime."<===\n";

        $bookings = $this->find('all',array(
            'joins'         => array(
                array(
                    'table' => 'booking_tracks',
                    'alias' => 'BookingTrack',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => array(
                'Booking.reminder_sent !=' => 1,
                'OR'        => array(
                    array(
                        'date'                                              => date('Y-m-d' , (time()+($studentNotificaitonTime*60))),
                        'SUBSTRING_INDEX(time_slot,"-",1) BETWEEN ? AND ?'  => array($studentStartTime, $studentEndTime),
                    ),
                    array(
                        'date'                                              => date('Y-m-d' , (time()+($teacherNotificaitonTime*60))),
                        'SUBSTRING_INDEX(time_slot,"-",1) BETWEEN ? AND ?'  => array($teacherStartTime,$teacherEndTime)
                    )
                ),
            ),
            'group'         => array('Booking.id'),
        ));
        
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');
        
        
        /*$this->EmailQueue->testMail(array(
            'email'     => 'soyab@blackidsolutions.com',
            'subject'   => date('d-m-Y h:i:s A') .' :: CRON WORKING elev-system Before 24 Hours ',
            'data'      => 'Test Mail',
        ));*/

        //print_r($bookings);
        
        if(empty($bookings)) {
            echo 'No Bookings available';
        } else {
            $usedExternalStudentTime    = array();
            $usedStudentTime            = array();
            $teacherTime                = array();
            $usedStudent                = array();
            $usedExternalStudent        = array();
            
            foreach($bookings as $booking) {
                $teacherTime[$booking['Booking']['id']] = array();
                if($booking['Booking']['date'] == date('Y-m-d',time()+($studentNotificaitonTime*60))) {
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot                   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($studentStartTime) && strtotime($timeSlot[0]) < strtotime($studentEndTime)) {
                            if(!is_null($tracks['student_id']) && $tracks['student_id'] != '-1') {
                                $usedStudentTime[$booking['Booking']['id']][$tracks['student_id']][]    = $tracks['time_slot'];
                            } else if((is_null($tracks['student_id']) || $tracks['student_id'] == '-1') && !is_null($tracks['phone'])) {
                                $usedExternalStudentTime[$booking['Booking']['id']][$tracks['phone']][] = $tracks['time_slot'];
                            }
                            if((isset($booking['Booking']['reference']) && !empty($booking['Booking']['reference']))) {
                                $teacherTime[$booking['Booking']['reference']][]       = $tracks['time_slot'];
                            } else {
                                $teacherTime[$booking['Booking']['id']][]       = $tracks['time_slot'];
                            }
                        }
                    }
                }
            }

            foreach($bookings as $booking) {
                if($booking['Booking']['date'] == date('Y-m-d',time()+($studentNotificaitonTime*60))) {
                    $usedStudent[$booking['Booking']['id']]         = array();
                    $usedExternalStudent[$booking['Booking']['id']] = array();
                    //for a student
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($studentStartTime) && strtotime($timeSlot[0]) < strtotime($studentEndTime)) {
                            if(!is_null($tracks['student_id']) && $tracks['student_id'] != '-1' && 
                                    !in_array($tracks['student_id'],$usedStudent[$booking['Booking']['id']])) {
                                $usedStudent[$booking['Booking']['id']][]  = $tracks['student_id'];
                                $student        = $users[$tracks['student_id']];
                                    $userArr = array(
                                        'users'     => $student,
                                        'tracks'    => $tracks,
                                        'bookings'  => $booking,
                                        'time_slot' => implode(', ',  array_unique($usedStudentTime[$booking['Booking']['id']][$tracks['student_id']])),
                                        'template'  => 'bookingStudentReminder'
                                    );
                                    $this->sendReminder($userArr);

                            } else if((is_null($tracks['student_id']) || $tracks['student_id'] == '-1') && !is_null($tracks['phone']) && 
                                    !in_array($tracks['phone'],$usedExternalStudent[$booking['Booking']['id']])) {
                                $usedExternalStudent[$booking['Booking']['id']][]  = $tracks['phone'];
                                $userArr = array(
                                    'tracks'    => $tracks,
                                    'bookings'  => $booking,
                                    'time_slot' => implode(', ',array_unique($usedExternalStudentTime[$booking['Booking']['id']][$tracks['phone']])),
                                    'template'  => 'bookingStudentReminder'
                                );
                                $this->sendReminder($userArr,'external');
                            }
                        }
                    } // end foreach
                }
                
                if($booking['Booking']['date'] == date('Y-m-d',time()+($teacherNotificaitonTime*60))) {
                    
                    //for a teacher
                    $teacher = $users[$booking['Booking']['user_id']];
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($teacherStartTime) && strtotime($timeSlot[0]) < strtotime($teacherEndTime)) {
                            if((isset($booking['Booking']['reference']) && empty($booking['Booking']['reference'])) || !isset($booking['Booking']['reference'])) {
                                $userArr = array(
                                    'users'     => $teacher,
                                    'tracks'    => $tracks,
                                    'bookings'  => $booking,
                                    'time_slot' => implode(', ',array_unique($teacherTime[$booking['Booking']['id']])),
                                    'template'  => 'bookingStudentReminder'
                                );
                                $this->sendReminder($userArr,'internal',true);
                                break;
                            }
                        } // end if
                    } // end foreach
                } // end else
            }
        }

        echo 'Complete Queue';
    }
    
    private function sendReminder($user,$type = 'internal') {

        //print_r($user);
        //die;
        $this->Area     = ClassRegistry::init('Area');
        $areaListArr    = $this->Area->find('list',array('fields' => array('slug','name')));
        $tracksList     = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track');
        $timeSlot = Hash::extract($user['bookings']['BookingTrack'],'{n}.time_slot');
        $timeSlot = array_unique($timeSlot);
        $data = array();
        $template = array();
        /*$this->EmailQueue->bookingReminder(array(
            'email'         => 'soyab@blackidsolutions.com',
            'data'          => array(
                'Booking'      => array(
                    'firstname'         => 'Soyab',
                    'lastname'          => 'Rana',
                    'timeslot'          => '11:30-15:30',
                    'area'              => 'Test',
                    'bookingdate'       => '06-06-2016',
                    'track'             => '1',
                 )
            ),
            'instant'       => TRUE,
            'priority'      => 0,
            'booking_id'    => 1170,
        ));*/
        if($type == 'internal') {
            $data = array(
                    'Booking'      => array(
                        'firstname'         => $user['users']['firstname'],
                        'lastname'          => $user['users']['lastname'],
                        'timeslot'          => $user['time_slot'],
                        'area'              => $areaListArr[$user['bookings']['Booking']['area_slug']],
                        'bookingdate'       => date('d.m.Y',strtotime($user['bookings']['Booking']['date'])),
                        'track'             => $tracksList[$user['tracks']['track_id']]['name'],
                     )
                );
                
            $this->EmailQueue->bookingReminder(array(
                'email'         => empty($user['users']['email_id'])?'jesper@schlebaum.dk':$user['users']['email_id'],
                'data'          => $data,
                'instant'       => TRUE,
                'priority'      => 0,
                'booking_id'    => $user['bookings']['Booking']['id'],   
            ));
            
        }
        
        $mobileNo = ($type == 'internal') ? $user['users']['phone_no'] : $user['tracks']['phone'];
        $mobileNo = (substr($mobileNo, 0, 2) == '45') ? '+' . $mobileNo : '+45'. $mobileNo;
        
        if(strlen($mobileNo) > 3){
            $template = (isset($user['template']) && !empty($user['template'])) ? $user['template'] : '';
            $data = array(
                    'Booking'      => array(
                        'firstname'     => ($type == 'internal') ? $user['users']['firstname'] : 
                            (isset($user['tracks']) && (!empty($user['tracks']['name'])) ? $user['tracks']['name'] : __('External User')),
                        'lastname'      => ($type == 'internal') ? $user['users']['lastname']  : '',
                        'timeslot'      => $user['time_slot'],
                        'bookingdate'   => date('d.m.Y',strtotime($user['bookings']['Booking']['date'])),
                        'area'          => $areaListArr[$user['bookings']['Booking']['area_slug']],
                        'track'         => $tracksList[$user['tracks']['track_id']]['name'],
                     )
                );
            $this->SmsQueue->bookingReminder(array(
                'data'          => $data,
                'mobileno'      => $mobileNo,
                'priority'      => 0,
                'instant'       => TRUE,
                'booking_id'    => $user['bookings']['Booking']['id'],
                'template'      => $template,
            ));
            $data['Booking']['sent_date'] = date('d-m-Y H:i:s');
            $data['Booking']['mobileno'] = $mobileNo;
            $data['template'] = $template;

            if(isset($user['rem_type']) && $user['rem_type']==3){
                $data['rem_type'] = 3;
            }

            if(isset($user['rem_type']) && $user['rem_type']==24){
                $data['rem_type'] = 24;
            }
            $this->insertLog('reminder_sent', $data, $user['tracks']['student_id']);
        }
    }
    
    function validateTeacherUnavailability($data) {
        $this->data = $data;
        
        foreach($this->data['TeacherUnavailability'] as $key => $unavailability) {
            
            if(empty($unavailability['from'])) {
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_unavailability_error_'.$key,
                    'message'       => __('Please Select From Time.')
                );
                break;
            }
            if(empty($unavailability['to'])) {
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_unavailability_error_'.$key,
                    'message'       => __('Please Select To Time.')
                );
                break;
            }
            
            if(!empty($unavailability['from']) && !empty($unavailability['to']) && ($unavailability['from'] > $unavailability['to'])) {
                $this->error_flag   = TRUE;
                $this->error_msg[]  = array(
                    'key'           => 'txt_unavailability_error_'.$key,
                    'message'       => __('From Time Must be less then To Time.')
                );
                break;
            }
            
           /*if(!empty($unavailability['from']) && !empty($unavailability['to']) && ($unavailability['from'] == $unavailability['to'])){
               $this->error_flag   = TRUE;
               $this->error_msg[]  = array(
                   'key'           => 'txt_unavailability_error_'.$key,
                   'message'       => __('From Time and To Time must not be same.')
               );
               break;
           }*/
            
        }
        
        $errorDetail   = array(            
            'status'    => $this->error_flag,
            'error_msg' => $this->error_msg,
        );
        
        return $errorDetail;
    }
    
    function deleteUnavailabilityTime($user,$date){
        
        $qry     = "DELETE FROM `teacher_unavailabilities` WHERE user_id = {$user} ";
        $qry    .= "AND DATE(`teacher_unavailabilities`.`from`) = '".$date."'";
        
        $this->query($qry);
    }
    
    function countNumberOfBookings($data){
        
        $this->data         = $data;
        $studentIds         = Hash::extract($this->data,'{n}.student_id');
        $this->BookingTrack = ClassRegistry::init('BookingTrack');
        
        $bookingCounts      = $this->BookingTrack->find('all',array(
            'fields'        => array('COUNT(BookingTrack.id) as total','BookingTrack.student_id'),
            'conditions'    => array(
                'BookingTrack.student_id'          => $studentIds,
                'User.student_medical_profile is NUll'
            ),
            'joins'         => array(
                array(
                    'table'         => 'users',
                    'alias'         => 'User',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'User.id = BookingTrack.student_id'
                    ))
            ),
            'group'         => array(
                'BookingTrack.student_id',
            )
        ));
        
        $bookingCounts      = Hash::combine($bookingCounts,'{n}.BookingTrack.student_id','{n}.{n}.total');
        
        return $bookingCounts;
    }

    function countNumberOfBookingsbyStudent($student_id,$date,$time){
        
        $bookingCounts      = $this->find('count',array(
            'fields'        => array('BookingTrack.id'),
            'joins'         => array(
                array(
                    'table'         => 'booking_tracks',
                    'alias'         => 'BookingTrack',
                    'type'          => 'inner',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    ))
            ),
            'conditions'    => array(
                'BookingTrack.student_id'          => $student_id,
                'DATE(Booking.date)' => $date,
                'BookingTrack.time_slot LIKE' => $time."%",
            ),
            'group'         => array(
                'BookingTrack.student_id',
            )
        ));
        
        return $bookingCounts;
    }
    function getTestBookingCounts($args = array()) {
        
        $testBookingCount       = array();
        $this->BookingTrack     = ClassRegistry::init('BookingTrack');
        
        foreach($args['BookingTrack'] as $bookingTrack) {
            $bookingCount   = $this->BookingTrack->find('all',array(
                'fields'        => array('Count(BookingTrack.id) as count'),
                'conditions'    => array(
                    'Booking.type'              => 'testing',
                    'BookingTrack.student_id'   => $bookingTrack['student_id']
                ),
                'joins'         => array(
                    array(
                        'table'         => 'bookings',
                        'alias'         => 'Booking',
                        'type'          => 'INNER',
                        'conditions'    => array(
                            'Booking.id = BookingTrack.booking_id'
                        )
                    )
                )
            ));
            
            $count  = Hash::extract($bookingCount,'{n}.{n}.count');
            
            $testBookingCount[$bookingTrack['student_id']]  = $count[0];
        }
        
        return $testBookingCount;
    }  
    
    function closeReopenTrack() {
        $this->BookingTrack = ClassRegistry::init('BookingTrack');
        $tracks = $this->BookingTrack->find('all',array(
            'fields'     => array('Booking.id','Booking.date','BookingTrack.id','BookingTrack.time_slot','SUBSTRING_INDEX(time_slot,"-",1) as timeSlot'),
            'joins'      => array(
                array(
                    'table'      => 'bookings',
                    'alias'      => 'Booking',
                    'type'       => 'INNER',
                    'conditions' => array(
                        'BookingTrack.booking_id = Booking.id'
                    )
                )
            ),
            'conditions' => array(
                'OR' => array(
                    'track_status !='   => 'closed',
                    'track_status'   => NULL,
                ),
                'SUBSTRING_INDEX(time_slot,"-",1) <= '  => date('H:i'),
                'date'                                  => date('Y-m-d'),
            )
        ));
        foreach($tracks as $track) {
            $timeSlot = strtotime($track['Booking']['date'].' '.$track[0]['timeSlot']) + 60*60*6;
            if($timeSlot <= time()) {
                $this->BookingTrack->updateAll(array(
                    'track_status'  => '"closed"'
                ),array(
                    'id'    => $track['BookingTrack']['id']
                ));
            }
        }
    }

    function checkAndCloseTrack(){
        $date = date('Y-m-d');

        $bookings = $this->find('all',array(
            'joins' => array(
                array(
                    'table'         => 'booking_tracks',
                    'alias'         => 'BookingTrack',
                    'type'          => 'INNER',
                    'conditions'    => array(
                        'Booking.id = BookingTrack.booking_id'
                    ),
                )
            ),
            'conditions'    => array(
                'Booking.date <='              => $date,
                'OR' =>array(
                    'BookingTrack.track_status' => NULL,
                    'BookingTrack.track_status NOT LIKE' => '%closed%'
                )
            )
        ));
        
        $this->EmailQueue = ClassRegistry::init('EmailQueue');
        $this->EmailQueue->testMail(array(
            'email'     => 'iqbal.svit@gmail.com',
            'subject'   => date('d-m-Y h:i:s A') .' :: CRON WORKING elev-system Check & Close Track ',
            'data'      => 'Total closed bookings are ' . count($bookings),
        ));
        
        if(isset($bookings) && count($bookings) > 0){
            foreach ($bookings as $booking) {
                if(isset($booking['BookingTrack']) && count($booking['BookingTrack']) > 0){
                    foreach ($booking['BookingTrack'] as $track) {
                        $startSlot  = explode('-',$track['time_slot']);
                        $dateTime   = $booking['Booking']['date'] .' '. $startSlot[0];

                        $time = strtotime(date('Y-m-d H:i:s'));
                        $track_time = date('Y-m-d H:i:s', strtotime($dateTime));
                        $plus_12_hour = strtotime('+12 hours',strtotime($track_time));

                        if( ($time > $plus_12_hour) ) {
                            $this->BookingTrack->save(array('id' => $track['id'], 'track_status' => 'closed'));
                        }
                    }
                }
            }
        }

        die();
    }

    public function bookingBeforeThreeHourNotification($time = '')
    {
        $this->EmailQueue           = ClassRegistry::init('EmailQueue');
        $this->SmsQueue             = ClassRegistry::init('SmsQueue');
        $this->User                 = ClassRegistry::init('User');
        $this->Track                = ClassRegistry::init('Track');
        $this->Option               = ClassRegistry::init('Option');

        $studentNotificaitonTime    = 240; //Minutes

        if($time == ''){
            $time = time();
        }
        $studentStartTime           = date('H:i', ($time+($studentNotificaitonTime*60)));
        $studentEndTime             = date('H:i', ($time+($studentNotificaitonTime*60)+(60*60)));

        /*$this->EmailQueue->testMail(array(
            'email'     => 'iqbal.svit@gmail.com',
            'subject'   => date('d-m-Y h:i:s A') .' :: CRON WORKING elev-system Before 3 Hours ',
            'data'      => 'Test Mail',
        ));*/

        /*$student    = $this->User->findById( 517 );
        $this->User->id   = 517;
        $this->User->saveField( 'lastname', date('d-m-Y h:i:s A') );*/
        
        $bookings = $this->find('all',array(
            'joins'         => array(
                array(
                    'table' => 'booking_tracks',
                    'alias' => 'BookingTrack',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => array(
                'Booking.second_reminder_sent ='                    => 0,
                'date'                                              => date('Y-m-d' , ($time+($studentNotificaitonTime*60))),
                'SUBSTRING_INDEX(time_slot,"-",1) BETWEEN ? AND ?'  => array($studentStartTime, $studentEndTime)
            ),
            'group'         => array('Booking.id'),
        ));
        
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');

        if(empty($bookings)) {
            echo 'No Bookings available';
        } else {
            $usedExternalStudentTime    = array();
            $usedStudentTime            = array();
            $teacherTime                = array();
            $usedStudent                = array();
            $usedExternalStudent        = array();
            
            foreach($bookings as $booking) {
                $teacherTime[$booking['Booking']['id']] = array();
                if($booking['Booking']['date'] == date('Y-m-d',$time+($studentNotificaitonTime*60))) {
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot                   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($studentStartTime) && strtotime($timeSlot[0]) < strtotime($studentEndTime)) {
                            if(!is_null($tracks['student_id']) && $tracks['student_id'] != '-1') {
                                $usedStudentTime[$booking['Booking']['id']][$tracks['student_id']][]    = $tracks['time_slot'];
                            } else if((is_null($tracks['student_id']) || $tracks['student_id'] == '-1') && !is_null($tracks['phone'])) {
                                $usedExternalStudentTime[$booking['Booking']['id']][$tracks['phone']][] = $tracks['time_slot'];
                            }
                            if((isset($booking['Booking']['reference']) && !empty($booking['Booking']['reference']))) {
                                $teacherTime[$booking['Booking']['reference']][]       = $tracks['time_slot'];
                            } else {
                                $teacherTime[$booking['Booking']['id']][]       = $tracks['time_slot'];
                            }
                        }
                    }
                }
            }

            foreach($bookings as $booking) {
                if($booking['Booking']['date'] == date('Y-m-d',$time+($studentNotificaitonTime*60))) {
                    $usedStudent[$booking['Booking']['id']]         = array();
                    $usedExternalStudent[$booking['Booking']['id']] = array();
                    //for a student
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($studentStartTime) && strtotime($timeSlot[0]) < strtotime($studentEndTime)) {
                            if(!is_null($tracks['student_id']) && $tracks['student_id'] != '-1' && 
                                    !in_array($tracks['student_id'],$usedStudent[$booking['Booking']['id']])) {
                                $usedStudent[$booking['Booking']['id']][]  = $tracks['student_id'];
                                $student        = $users[$tracks['student_id']];
                                $userArr = array(
                                    'users'     => $student,
                                    'rem_type'  => '3',
                                    'tracks'    => $tracks,
                                    'bookings'  => $booking,
                                    'time_slot' => implode(', ',  array_unique($usedStudentTime[$booking['Booking']['id']][$tracks['student_id']])),
                                    'template'  => 'bookingStudentThreeHourReminder'
                                );
                                $this->sendReminder($userArr);
                                $this->id   = $booking['Booking']['id'];
                                $this->saveField( 'second_reminder_sent', 1 );

                            } else if((is_null($tracks['student_id']) || $tracks['student_id'] == '-1') && !is_null($tracks['phone']) && 
                                    !in_array($tracks['phone'],$usedExternalStudent[$booking['Booking']['id']])) {
                                $usedExternalStudent[$booking['Booking']['id']][]  = $tracks['phone'];
                                $userArr = array(
                                    'tracks'    => $tracks,
                                    'rem_type'  => '3',
                                    'bookings'  => $booking,
                                    'time_slot' => implode(', ',array_unique($usedExternalStudentTime[$booking['Booking']['id']][$tracks['phone']])),
                                    'template'  => 'bookingStudentThreeHourReminder'
                                );
                                $this->sendReminder($userArr,'external');
                                $this->id   = $booking['Booking']['id'];
                                $this->saveField( 'second_reminder_sent', 1 );
                            }
                        }
                    } // end foreach
                }
            }
        }
        echo 'Complete Queue';
    }

    public function bookingBeforeTwentyfourHourNotification($time = '')
    {
        $this->EmailQueue           = ClassRegistry::init('EmailQueue');
        $this->SmsQueue             = ClassRegistry::init('SmsQueue');
        $this->User                 = ClassRegistry::init('User');
        $this->Track                = ClassRegistry::init('Track');
        $this->Option               = ClassRegistry::init('Option');
        
        $studentNotificaitonTime    = 1440; //Minutes
        if($time == ''){
            $time = time();
        }
        $studentStartTime           = date('H:i', ($time+($studentNotificaitonTime*60)));
        $studentEndTime             = date('H:i', ($time+($studentNotificaitonTime*60)+(60*60)));

        echo "====>".$studentStartTime."<=====>".$studentEndTime."<====";
        CakeLog::write('activity', 'A special message for activity logging');

        /*$this->EmailQueue->testMail(array(
            'email'     => 'iqbal.svit@gmail.com',
            'subject'   => date('d-m-Y h:i:s A') .' :: CRON WORKING elev-system Before 3 Hours ',
            'data'      => 'Test Mail',
        ));*/

        /*$student    = $this->User->findById( 517 );
        $this->User->id   = 517;
        $this->User->saveField( 'lastname', date('d-m-Y h:i:s A') );*/
        
        $bookings = $this->find('all',array(
            'joins'         => array(
                array(
                    'table' => 'booking_tracks',
                    'alias' => 'BookingTrack',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.id = BookingTrack.booking_id'
                    )
                )
            ),
            'conditions'    => array(
                'Booking.third_reminder_sent ='                    => 0,
                'date'                                              => date('Y-m-d' , ($time+($studentNotificaitonTime*60))),
                'SUBSTRING_INDEX(time_slot,"-",1) BETWEEN ? AND ?'  => array($studentStartTime, $studentEndTime)
            ),
            'group'         => array('Booking.id'),
        ));
        
        $users  = Hash::combine($this->User->find('all'),'{n}.User.id','{n}.User');

        if(empty($bookings)) {
            echo 'No Bookings available';
        } else {
            $usedExternalStudentTime    = array();
            $usedStudentTime            = array();
            $teacherTime                = array();
            $usedStudent                = array();
            $usedExternalStudent        = array();
            
            foreach($bookings as $booking) {
                $teacherTime[$booking['Booking']['id']] = array();
                if($booking['Booking']['date'] == date('Y-m-d',$time+($studentNotificaitonTime*60))) {
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot                   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($studentStartTime) && strtotime($timeSlot[0]) < strtotime($studentEndTime)) {
                            if(!is_null($tracks['student_id']) && $tracks['student_id'] != '-1') {
                                $usedStudentTime[$booking['Booking']['id']][$tracks['student_id']][]    = $tracks['time_slot'];
                            } else if((is_null($tracks['student_id']) || $tracks['student_id'] == '-1') && !is_null($tracks['phone'])) {
                                $usedExternalStudentTime[$booking['Booking']['id']][$tracks['phone']][] = $tracks['time_slot'];
                            }
                            if((isset($booking['Booking']['reference']) && !empty($booking['Booking']['reference']))) {
                                $teacherTime[$booking['Booking']['reference']][]       = $tracks['time_slot'];
                            } else {
                                $teacherTime[$booking['Booking']['id']][]       = $tracks['time_slot'];
                            }
                        }
                    }
                }
            }

            foreach($bookings as $booking) {
                if($booking['Booking']['date'] == date('Y-m-d',$time+($studentNotificaitonTime*60))) {
                    $usedStudent[$booking['Booking']['id']]         = array();
                    $usedExternalStudent[$booking['Booking']['id']] = array();
                    //for a student
                    foreach($booking['BookingTrack'] as $tracks) {
                        $timeSlot   = explode('-',$tracks['time_slot']);
                        if(strtotime($timeSlot[0]) > strtotime($studentStartTime) && strtotime($timeSlot[0]) < strtotime($studentEndTime)) {
                            if(!is_null($tracks['student_id']) && $tracks['student_id'] != '-1' && 
                                    !in_array($tracks['student_id'],$usedStudent[$booking['Booking']['id']])) {
                                $usedStudent[$booking['Booking']['id']][]  = $tracks['student_id'];
                                $student        = $users[$tracks['student_id']];
                                    $userArr = array(
                                        'users'     => $student,
                                        'rem_type'  => '24',
                                        'tracks'    => $tracks,
                                        'bookings'  => $booking,
                                        'time_slot' => implode(', ',  array_unique($usedStudentTime[$booking['Booking']['id']][$tracks['student_id']])),
                                        'template'  => 'bookingStudentReminder'
                                    );
                                    $this->sendReminder($userArr);
                                    $this->id   = $booking['Booking']['id'];
                                    $this->saveField( 'third_reminder_sent', 1 );

                            } else if((is_null($tracks['student_id']) || $tracks['student_id'] == '-1') && !is_null($tracks['phone']) && 
                                    !in_array($tracks['phone'],$usedExternalStudent[$booking['Booking']['id']])) {
                                $usedExternalStudent[$booking['Booking']['id']][]  = $tracks['phone'];
                                $userArr = array(
                                    'tracks'    => $tracks,
                                    'rem_type'  => '24',
                                    'bookings'  => $booking,
                                    'time_slot' => implode(', ',array_unique($usedExternalStudentTime[$booking['Booking']['id']][$tracks['phone']])),
                                    'template'  => 'bookingStudentReminder'
                                );
                                $this->sendReminder($userArr,'external');
                                $this->id   = $booking['Booking']['id'];
                                $this->saveField( 'third_reminder_sent', 1 );
                            }
                        }
                    } // end foreach
                }
            }    
        }
        
        echo 'Complete Queue';
    }

    public function getAllBookings($user_id, $date){
        $bookings = $this->find('all',array(
            'conditions'    => array(
                'Booking.user_id' => $user_id,
                'DATE(Booking.date)' => $date,
            )
        ));

        return $bookings;
    }

    public function getBookingTotalTime($start_time, $end_time){

        $qry  = ' SELECT users.id, CONCAT(users.firstname, " ", users.lastname) AS name, users.city, booking_type, SUM( TIMESTAMPDIFF(MINUTE, start_time, end_time) ) AS total_time';
        $qry .= " FROM systembookings";
        $qry .= " JOIN users ON systembookings.user_id = users.id";
        $qry .= " WHERE DATE(start_time) >= '$start_time' AND DATE(end_time) <= '$end_time'";
        $qry .= " GROUP BY booking_type, user_id";
        $qry .= " ORDER BY users.id ASC";
  
        $bookings =  $this->query($qry);

        $results = array();
        if( count($bookings) > 0 ) {
            foreach ($bookings as $book) {
                $booking_type = preg_replace("/[^a-zA-Z]/", "", strtolower($book['systembookings']['booking_type']));
                $results[$book['users']['id']]['name'] = $book[0]['name'];
                $results[$book['users']['id']]['city'] = $book['users']['city'];
                $results[$book['users']['id']]['status'][ $booking_type ] = $book[0]['total_time'];
            }
        }

        unset($qry);
        //$qry  = ' SELECT users.id, CONCAT(users.firstname, " ", users.lastname) AS name, users.city, SUM( TIMESTAMPDIFF( MINUTE, CONCAT(bookings.date, " ", SUBSTRING_INDEX(time_slot, "-", 1)), CONCAT(bookings.date, " ", SUBSTRING_INDEX(time_slot, "-", -1)) ) ) AS total_time';

        $qry  = ' SELECT users.id, CONCAT(users.firstname, " ", users.lastname) AS name, users.city, COUNT(bookings.id) AS total_book';        
        $qry .= " FROM bookings";
        $qry .= " JOIN users ON bookings.co_teacher = users.id ";
        //$qry .= " JOIN booking_tracks ON booking_tracks.booking_id = bookings.id";
        $qry .= " WHERE DATE(date) >= '$start_time' AND DATE(date) <= '$end_time'";
        $qry .= " GROUP BY co_teacher";
        $qry .= " ORDER BY users.id ASC";
  
        $tracks =  $this->query($qry);

        if( count($tracks) > 0 ) {
            foreach ($tracks as $track) {
                if( in_array( $track['users']['id'], array_keys($results) ) ) {
                    $results[$track['users']['id']]['status']['track'] = $track[0]['total_book'] * 210;
                } else {
                    $results[$track['users']['id']]['name'] = $track[0]['name'];
                    $results[$track['users']['id']]['city'] = $track['users']['city'];
                    $results[$track['users']['id']]['status']['track'] = $track[0]['total_book'] * 210;
                }
            }
        }

        return $results;
    }

    public function insertLog($type, $data, $to_id = 0){
    	$user = ClassRegistry::init('User');
        if(!empty($to_id) && $to_id > 0) {
        	$to_record = $user->findById($to_id);
        	if(!empty($to_record) && !empty($to_record['User']['firstname']) && !empty($to_record['User']['lastname'])){
        		$data['to_user'] = $to_record['User']['firstname']." ".$to_record['User']['lastname'];
        	}
        } else {
            $to_id =-1;
            $data['to_user'] = '';
        }
         $data['data'] = $data;
        App::uses('Activity', 'Model');
        $Activity_model = new Activity;
        $Activity_model->create();
        $activity = array();
        $activity['from_id']      = (-1);
        $activity['to_id']        = $to_id;
        $activity['action']       = $type;
        $activity['data']         = serialize($data);
        $Activity_model->save($activity);
        return true;
    }
}