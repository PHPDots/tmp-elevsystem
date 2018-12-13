<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'       => $this->request->query['report_type'],
                'datetime_from'     => $this->request->query['datetime_from'],
                'datetime_to'       => $this->request->query['datetime_to'],
                'teacher_id'        => $this->request->query['teacher_id'],
                'csv'               => 'true',
            )),array(
                'class'     => 'button button-green',           
                'escape'    => FALSE,
        ));
        ?>
    </div>
    <div class="clearfix"></div>   
    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php echo (empty($searchString)) ? __('Hourly Report ') : __('Hourly Report ').  implode(' ', $searchString); ?>
            </h5>
        </div>
        <div class="sub-widget">
            <div class="widget">
                <div class="widget-header">
                    <h5>
                    <?php echo __('Bookings'); ?>
                    </h5>
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <?php if(isset($this->request->query['area_id']) && empty($this->request->query['area_id'])) { ?> 
                            <th align="left"><?php echo __('Area');?></th>
                            <?php } ?>
                            <th align="left"><?php echo __('Track');?></th>
                            <th align="left"><?php echo __('Date');?></th>
                            <th align="left"><?php echo __('Time Slot');?></th>
                            <th align="left"><?php echo __('Teacher Time');?></th>
                            <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                            <th align="left"><?php echo __('Instructor Name');?></th>
                            <?php } ?>
                            <th align="left"><?php echo __('Student Name');?></th>
                            <th align="left"><?php echo __('Booking Type');?></th>
                        </tr>
                        <?php 
                        $i = 1;
                        if(!empty($bookings)) {
                            foreach($bookings as $booking){
                                ?>
                                <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                                    <td align="center">
                                        <?php echo $i; ?>
                                    </td>
                                    <?php if(isset($this->request->query['area_id']) && empty($this->request->query['area_id'])) { ?> 
                                    <td align="left">
                                        <?php echo $areaListArr[$booking['area_slug']]; ?>
                                    </td>
                                    <?php } ?>
                                    <td align="left">
                                        <?php echo $tracks[$booking['track_id']]; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo date('d.m.Y',strtotime($booking['date'])); ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $booking['time_slot']; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $booking['time_min']; ?>
                                    </td>
                                    <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                                    <td align="left">
                                        <?php echo $users[$booking['user_id']]['firstname'].' '.$users[$booking['user_id']]['lastname']; ?>
                                    </td>
                                    <?php } ?>
                                    <td align="left">
                                        <?php echo (isset($booking['student_id']) && !empty($booking['student_id']) && isset($users[$booking['student_id']])) ?  $users[$booking['student_id']]['firstname'].' '.$users[$booking['student_id']]['lastname'] : __('External Student'); ?>
                                    </td>
                                    <td align="left">
                                        <?php echo Inflector::humanize($booking['type']); ?>
                                    </td>
                                </tr>
                            <?php       
                                $i++;
                            }
                        }else{
                        ?>
                            <tr>
                                <td colspan="8" class="index_msg"><?php  echo __('No Bookings Found.'); ?></td>
                            </tr>
                        <?php 
                        }
                        ?>
                    </table>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h5>
                    <?php echo __('Registered Time'); ?>
                    </h5>
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                            <th align="left"><?php echo __('Instructor Name');?></th>
                            <?php } ?>
                            <th align="left"><?php echo __('Type');?></th>
                            <th align="left"><?php echo __('Date');?></th>
                            <th align="left"><?php echo __('Purpose / City / Driving Type');?></th>    
                        </tr>
                        <?php 
                        $i = 1;
                        if(!empty($teacherRegisterTimes)) {
                            foreach($teacherRegisterTimes as $data){
                                ?>
                                <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                                    <td align="center">
                                        <?php echo $i; ?>
                                    </td>                     
                                    <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                                    <td align="left">
                                        <?php echo $users[$data['TeacherRegisterTime']['user_id']]['firstname'].' '.$users[$data['TeacherRegisterTime']['user_id']]['lastname']; ?>
                                    </td>
                                    <?php } ?>
                                    <td align="left">
                                        <?php echo Inflector::humanize($data['TeacherRegisterTime']['type']); ?>
                                    </td>
                                    <td align="left"><?php echo $data['TeacherRegisterTime']['from']; ?></td>
                                    <?php if($data['TeacherRegisterTime']['type'] == 'other'){ ?>
                                    <td align="left"><?php echo String::truncate(strip_tags(__($data['TeacherRegisterTime']['purpose'])), 60, array('html' => true)); ?></td> 
                                    <?php } else if($data['TeacherRegisterTime']['type'] == 'driving') { ?>
                                    <td align="left"><?php echo $drivingTypes[$data['TeacherRegisterTime']['driving_type']]; ?></td>    
                                    <?php } else { ?>
                                    <td align="left"><?php echo $cities[$data['TeacherRegisterTime']['city']]; ?></td>
                                    <?php } ?>
                                </tr>
                            <?php       
                                $i++;
                            }
                        }else{
                        ?>
                            <tr>
                                <td colspan="5" class="index_msg"><?php  echo __('No Instructor Registered.'); ?></td>
                            </tr>
                        <?php 
                        }
                        ?>
                    </table>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h5>
                    <?php echo __('Driving Lessons Details'); ?>
                    </h5>
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                            <th align="left"><?php echo __('Instructor Name');?></th>
                            <?php } ?>
                            <th align="left"><?php echo __('Student Name');?></th>
                            <th align="left"><?php echo __('Type');?></th>
                            <th align="left"><?php echo __('Start Time');?></th>
                            <th align="left"><?php echo __('Lesson Time');?></th>
                            <th align="left"><?php echo __('Status');?></th>
                        </tr>
                        <?php
                            $types      = Configure::read('bookingType'); 
                            $lessonTime = Configure::read('lessonTime');
                            $status     = Configure::read('lessonStatus');
                            if(!empty($teacherDrivingLessons)) {
                                $i = 1;
                                foreach($teacherDrivingLessons as $data) {
                                ?>
                                    <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                                        <td align="center"><?php echo $i; ?></td>
                                        <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                                        <td align="left"><?php echo $users[$data['DrivingLesson']['teacher_id']]['firstname'].' '.$users[$data['DrivingLesson']['teacher_id']]['lastname']; ?></td>
                                        <?php } ?>
                                        <td align="left"><?php echo $users[$data['DrivingLesson']['student_id']]['firstname'].' '.$users[$data['DrivingLesson']['student_id']]['lastname']; ?></td>
                                        <td align="left"><?php echo $types[$data['DrivingLesson']['type']]; ?></td>
                                        <td align="left"><?php echo date('d.m.Y H:i:s',strtotime($data['DrivingLesson']['start_time'])); ?></td>
                                        <td align="left"><?php echo $lessonTime[$data['DrivingLesson']['lesson_time']]; ?></td>
                                        <td align="left"><?php echo (!is_null($data['DrivingLesson']['status']))?$status[$data['DrivingLesson']['status']]:'NA'; ?></td>
                                    </tr>
                                <?php
                                 $i++;
                                }
                            } else {
                                ?>
                                <tr>
                                    <td class="tbl_detail" colspan="7" align="center"><?php echo __('No Driving Lesson Booking'); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                    </table>
                </div>
            </div>
            
             <div class="widget">
                <div class="widget-header">
                    <h5>
                    <?php echo __('Instructor Unavailability'); ?>
                    </h5>
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                            <th align="left"><?php echo __('Instructor Name');?></th>
                            <?php } ?>
                            <th align="left"><?php echo __('Unavailable From');?></th>
                            <th align="left"><?php echo __('Unavailable Till');?></th>
                            <th align="left"><?php echo __('Total Time');?></th>
                        </tr>
                        <?php 
                        $i = 1;
                        if(!empty($teacherAvailability)) {
                            foreach($teacherAvailability as $data){
                                ?>
                                <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                                    <td align="center">
                                        <?php echo $i; ?>
                                    </td>
                                    <?php if(isset($this->request->query['teacher_id']) && empty($this->request->query['teacher_id'])) { ?> 
                                    <td align="left">
                                        <?php echo $users[$data['TeacherUnavailability']['user_id']]['firstname'].' '.$users[$data['TeacherUnavailability']['user_id']]['lastname']; ?>
                                    </td>
                                    <?php } ?>
                                    <td align="left">
                                        <?php echo $data['TeacherUnavailability']['from']; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $data['TeacherUnavailability']['to']; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $this->Html->timeConversion($data['TeacherUnavailability']['from'],$data['TeacherUnavailability']['to']); ?>
                                    </td>
                                </tr>
                            <?php       
                                $i++;
                            }
                        }else{
                        ?>
                            <tr>
                                <td colspan="5" class="index_msg"><?php  echo __('All Instructor Available.'); ?></td>
                            </tr>
                        <?php 
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
            
    <div>
        <div class="pagination_no">
        <?php
//        $this->paginator->options(array('url' => array('?' => $this->request->query)));
//        echo $this->paginator->first(__('First'),
//                                    array('class' => 'first paginate_button'),
//                                    null,
//                                    array('class' => 'paginate_button_disabled')
//                                    );
//        echo $this->Paginator->prev(__('Previous'),
//                                    array('class' => 'previous paginate_button'),
//                                    null,
//                                    array('class' => 'paginate_button_disabled')
//                                    );
//        echo $this->Paginator->numbers(array('class' => 'paginate_button','modulus' => 2,'separator' => FALSE));
//        echo $this->Paginator->next(__('Next'),
//                                    array('class' => 'next paginate_button'),
//                                    null,
//                                    array('class' => 'paginate_button_disabled')
//                                    );
//        echo $this->paginator->last(__('Last'),
//                                    array('class' => 'first paginate_button'),
//                                    null,
//                                    array('class' => 'paginate_button_disabled')
//                                    );
        ?>
        </div>
        <div class="pagination">
        <?php
//        if(!empty($users)) {
//            echo $this->Form->create('Report',array(
//                'class'         => 'row-fluid',
//                'url'           => array(
//                'controller'    => 'reports',
//                'action'        => 'bookingReport',
//                '?' => array(
//                    'report_type'   => $this->request->query['report_type'],
//                    'date_from'     => $this->request->query['date_from'],
//                    'date_to'       => $this->request->query['date_to'],
//                    'area_id'       => $this->request->query['area_id'],
//                ),
//            )));
//            echo $this->Form->input('perPage', array(
//                'options'   => $perPageDropDown,
//                'selected'  => $perPage, 
//                'id'        => 'dropdown' 
//            ));

//            $this->Form->end();
//        }
        ?>
        </div>
    </div>
</div></div></div>