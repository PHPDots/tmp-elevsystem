<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php 
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __('Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'       => $this->request->query['report_type'],
                'area_id'           => $this->request->query['area_id'],
                'date_from'         => $this->request->query['date_from'],
                'date_to'           => $this->request->query['date_to'],
                'teacher_id'        => $this->request->query['teacher_id'],
                'driving_school'    => $this->request->query['driving_school'],
                'csv'               => 'true',
            )),array(
                'class'     => 'button button-green',
                'escape'    => FALSE,
        ));
        echo '&nbsp;&nbsp;';
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __('Generate PDF'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'       => $this->request->query['report_type'],
                'area_id'           => $this->request->query['area_id'],
                'date_from'         => $this->request->query['date_from'],
                'date_to'           => $this->request->query['date_to'],
                'teacher_id'        => $this->request->query['teacher_id'],
                'driving_school'    => $this->request->query['driving_school'],
                'pdf'               => 'true',
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
            <?php echo __('Booking Report').  implode(' ', $searchString); ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th align="left"><?php echo __('Date');?></th>
                    <th align="left"><?php echo __('Area');?></th>
                    <th align="left"><?php echo __('Courses');?></th>
                    <?php if(empty($this->request->query['teacher_id'])) { ?>
                    <th align="left"><?php echo __('Booked By');?></th>
                    <?php } ?>
                    <th align="left"><?php echo __('Co Instructor');?></th>
                    <th align="left" style="width: 10%;"><?php echo __('Teacher\'s City');?></th>
                    <th align="left" style="width: 12%;"><?php echo __('Driving School Name');?></th>
                    <th align="left" style="width: 15%;"><?php echo __('Note');?></th>
                    <th align="left" style="width: 12%;"><?php echo __('Instructor Name');?></th>
                    <th align="left"><?php echo __('Track');?></th>
                    <th align="left"><?php echo __('Time Slot');?></th>
                    <th align="left" style="width: 15%;"><?php echo __('Student Name');?></th>
                </tr>
                <?php 
                $pageNo = $this->params['paging']['BookingTrack']['page'];
                $i = (($pageNo - 1) * $perPage) + 1;
                $j = 1;
				
				
                if(!empty($filterRecords)) {
				?>
					<?php /*
				
                    foreach($allBookings as $booking) {

                        $main_book_ary = array();
                        $booking['BookingTrack'] = Hash::sort($booking['BookingTrack'],'{n}.track_id');
                        $driving_school =  $this->request->query['driving_school'];
                        foreach($booking['BookingTrack'] as $track) {
                            $teacher_id = ($track['booking_user_id'] != '') ? $track['booking_user_id'] :  $booking['Booking']['user_id'] ;
                            $booking_teacher_id = (!empty($booking['Booking']['on_behalf'])) ? $booking['Booking']['on_behalf'] : $teacher_id;

                            $teacher_id_for_driving_school = isset($track['released_by']) ? $track['released_by'] : $teacher_id;
                            $cur_driving_school = (isset($users[$teacher_id_for_driving_school]) && !empty($users[$teacher_id_for_driving_school]['company_id']) ) ? $users[$teacher_id_for_driving_school]['company_id'] :'';
                            if($driving_school != ''){
                                if($driving_school == $cur_driving_school){
                                    $show_is = true;
                                }else{
                                    $show_is = false;
                                }
                            }else{
                                $show_is = true;
                            }

                            if((!is_null($track['date_of_birth']) || (isset($users[$track['student_id']]) && !is_null($users[$track['student_id']]['date_of_birth']))) && !is_null($track['released_by']) && $show_is == true) {
                                $is_show = true;
                                ?>
                                <tr class="<?php echo ($i%2==0)?'even':'odd'; ?> " align="center" >
                                    <?php 
                                    if(!in_array($booking_teacher_id, $main_book_ary) && ($last_date != date('d.m.Y',strtotime($booking['Booking']['date'])) || $last_teacher_id != $booking_teacher_id ) ){
                                        $last_teacher_id = $booking_teacher_id;
                                        $main_book_ary[] = $booking_teacher_id;
                                        $is_show = false; ?>
                                        <td align="left"><?php echo $last_date =  date('d.m.Y',strtotime($booking['Booking']['date'])); ?></td>
                                        <td align="left"><?php echo $areaListArr[$booking['Booking']['area_slug']]; ?></td>
                                        <td align="left"><?php echo (!empty($booking['Booking']['course'])) ? $courses[$booking['Booking']['course']] : ''; ?></td>
                                        <?php if(empty($this->request->query['teacher_id'])) { ?>
                                            <td align="left"><?php echo (!empty($booking_teacher_id)) ? $users[$booking_teacher_id]['firstname'].' '.$users[$booking_teacher_id]['lastname'] :''; ?></td>
                                        <?php } ?>
                                        <td align="left"><?php echo (isset($booking['Booking']['co_teacher']) && !empty($booking['Booking']['co_teacher']) && isset($users[$booking['Booking']['co_teacher']]))? $users[$booking['Booking']['co_teacher']]['firstname'].' '.$users[$booking['Booking']['co_teacher']]['lastname']:''; 
                                            ?></td>
                                        <td align="left"><?php echo (!empty($teacher_id) && isset($users[$teacher_id])) ? $users[$teacher_id]['city'] :''; ?></td>
                                        <td align="left">
                                        <?php echo (isset($users[$teacher_id]) && !empty($users[$teacher_id]['company_id']) ) ? $drivingSchools[$users[$teacher_id]['company_id']] :''; ?>
                                        </td>
                                        <td align="left"><?php echo $booking['Booking']['full_description']; ?>
                                        </td>

                                        <?php 

                                        $j++;
                                    }

                                    if($is_show == true){
                                        ?>
                                        <td colspan="8">&nbsp;</td>
                                    <?php } ?>
                                    <td align="left"><?php echo (isset($users[$track['released_by']])) ? $users[$track['released_by']]['firstname'].' '.$users[$track['released_by']]['lastname'] : 'N/A'; ?>
                                    <!-- <td align="left"><?php echo (isset($users[$teacher_id])) ? $users[$teacher_id]['firstname'].' '.$users[$teacher_id]['lastname'] : 'N/A'; ?> -->
                                    </td>
                                    <td align="left">
                                        <?php echo (!empty($tracks[$track['track_id']])) ? $tracks[$track['track_id']] : '-'; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo (!empty($track['time_slot'])) ? $track['time_slot'] : '-'; ?>
                                    </td>
                                    <td align="left" style="width: 15%">
                                        <?php 
                                        $trackAddress = ($track['address'] != '' && !is_null($track['address'])) ? ' ('.$track['address']." ".$track['city']." ".$track['zip_code'].')' : '';
                                        echo (isset($track['student_id']) && !empty($track['student_id']) && isset($users[$track['student_id']])) ?
                                            $users[$track['student_id']]['firstname'].' '.$users[$track['student_id']]['lastname'] : ((!empty($track['name']) && !empty($track['address']))?
                                            $track['name'].$trackAddress :__('External Student'));
                                        ?>
                                    </td>
                                </tr>
                                <?php  
                            } 
                        } 
                        $i++;
                    }
					
					*/ ?>
					
                    <?php foreach($filterRecords as $booking):?>

                        <tr>

                            <?php 
                                $trackAddress = ($booking['address'] != '' && !is_null($booking['address'])) ? ' ('.$booking['address'].')' : '';
                            ?>

                            <td align="left">
                                <?php echo date('d/m/Y',strtotime($booking['date'])); ?>
                            </td>

                            <td align="left">
                                <?php echo $areaListArr[$booking['area_slug']]; ?>
                            </td>

                            <td align="left">
                                <?php echo (!empty($booking['course'])) ? $courses[$booking['course']] : ''; ?>
                            </td>

                            <?php if(empty($this->request->query['teacher_id'])):?>

                            <td align="left">
                                <?php echo (!empty($booking['booking_teacher_id']))? $users[$booking['booking_teacher_id']]['firstname'].' '.$users[$booking['booking_teacher_id']]['lastname'] : '';?>
                            </td>

                            <?php endif;?>

                            <td align="left">
                                <?php 
                                    echo ((!empty($booking['co_teacher'])) && isset($users[$booking['co_teacher']]))?$users[$booking['co_teacher']]['firstname'].' '.$users[$booking['co_teacher']]['lastname']:'';
                                ?>
                            </td>

                            <td align="left">
                                <?php echo (!empty($booking['teacher_id'])) ? $users[$booking['teacher_id']]['city'] :''; ?>
                            </td>

                            <td align="left">
                                <?php 
                                    echo (!empty($booking['teacher_id']) && isset($drivingSchools[$users[$booking['teacher_id']]['company_id']])) ? $drivingSchools[$users[$booking['teacher_id']]['company_id']] : '';
                                ?>
                            </td>

                            <td align="left">
                                <?php echo $booking['full_description']; ?>
                            </td>

                            <td align="left">

                                <?php 

                                    echo (isset($users[$booking['released_by']])) ? $users[$booking['released_by']]['firstname'].' '.$users[$booking['released_by']]['lastname'] : 'N/A';

                                ?>
                            </td>

                            <td align="left">

                                <?php echo $tracks[$booking['track_id']]; ?>

                            </td>

                            <td align="left">

                                <?php echo $booking['time_slot']; ?>

                            </td>

                            <td align="left">
                                <?php 
                                    echo (isset($booking['student_id']) && !empty($booking['student_id']) && isset($users[$booking['student_id']])) ?
                                    $users[$booking['student_id']]['firstname'].' '.$users[$booking['student_id']]['lastname'] : ((!empty($booking['name']))?
                                    $booking['name'].$trackAddress : __('External Student'));
                                ?>
                            </td>

                        </tr>

                    <?php endforeach;?>
					
					
				<?php 	
                } else { ?>
                    <tr>
                        <td colspan="11" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
                    </tr>
                <?php 
                }
                ?>
            </table>
        </div>
    </div>
    <?php if(isset($this->request->query['driving_school']) && !empty($this->request->query['driving_school']) && !empty($studentDetails)) { ?>
    <div class="span6 widget no-margin-left">
    <div class="widget-header">
        <h5>
        <?php echo __('Student Details'); ?>
        </h5>
    </div>
    <div class="tableLicense">
        <table class="default-table">
            <?php 
            foreach($studentDetails as $coursesId => $count) {
            ?>
            <tr class="even">
                <td><?php echo __('Course'); ?></td>
                <td><?php echo $courses[$coursesId]; ?></td>
            </tr>
            <tr>
                <td><b><?php echo __('Students from this driving school'); ?></b></td>
                <td><b><?php echo isset($count['own_students']) ? $count['own_students'] : 0; ?></b></td>
            </tr>
            <tr class="">
                <td><b><?php echo __('Students from other driving schools'); ?></b></td>
                <td><b><?php echo (isset($count['other_students'])) ? array_sum($count['other_students']) : 0; ?></b></td>
            </tr>
            <?php
            if(isset($count['other_students']) && is_array($count['other_students'])) {
                foreach($count['other_students'] as $school => $number) {
                    ?>
                    <tr class="">
                        <td><?php echo $drivingSchools[$school]; ?></td>
                        <td><?php echo $number; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr class="">
                <td><b><?php echo __('Own students and Other teachers'); ?></b></td>
                <td><b><?php echo (isset($count['own_students_other_teachers'])) ? array_sum($count['own_students_other_teachers']) : 0; ?></b></td>
            </tr>
            <?php 
            if(isset($count['own_students_other_teachers']) && is_array($count['own_students_other_teachers'])) {
                foreach($count['own_students_other_teachers'] as $school => $number) {
                ?>
                <tr class="">
                    <td><?php echo $drivingSchools[$school]; ?></td>
                    <td><?php echo $number; ?></td>
                </tr>
                <?php
                }
            } ?>
                <tr class="">
                    <td colspan="2">&nbsp;</td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    </div>
    <div class="clearfix"></div>
    <?php } ?>
<!--    <div>
        <div class="pagination_no">
        <?php
        $this->paginator->options(array('url' => array('?' => $this->request->query)));
        echo $this->paginator->first(__('First'),
                                    array('class' => 'first paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        echo $this->Paginator->prev(__('Previous'),
                                    array('class' => 'previous paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        echo $this->Paginator->numbers(array('class' => 'paginate_button','modulus' => 2,'separator' => FALSE));
        echo $this->Paginator->next(__('Next'),
                                    array('class' => 'next paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        echo $this->paginator->last(__('Last'),
                                    array('class' => 'first paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        ?>
        </div>
        <div class="pagination">
        <?php 
        if(!empty($users)) {
            echo $this->Form->create('Report',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'reports',
                'action'        => 'bookingReport',
                '?' => array(
                    'report_type'   => $this->request->query['report_type'],
                    'date_from'     => $this->request->query['date_from'],
                    'date_to'       => $this->request->query['date_to'],
                    'area_id'       => $this->request->query['area_id'],
                ),
            )));
            echo $this->Form->input('perPage', array(
                'options'   => $perPageDropDown,
                'selected'  => $perPage,
                'id'        => 'dropdown',
            ));
            $this->Form->end();
        }
        ?>
        </div>
    </div>-->
</div></div></div>