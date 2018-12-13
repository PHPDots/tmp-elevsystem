<table cellpading="0" cellspacing="0" border="1" style=" width: 100%">
    <tr>
        <th align="left" style="font-weight: bold;"><?php echo __('Date');?></th>        
        <th align="left" style="font-weight: bold;"><?php echo __('Area');?></th>        
        <th align="left" style="font-weight: bold;"><?php echo __('Courses');?></th>
        <?php if(empty($this->request->query['teacher_id'])):?>        
        <th align="left" style="font-weight: bold;"><?php echo __('Booked By');?></th>        
        <?php endif;?>
        <th align="left" style="font-weight: bold;"><?php echo __('Co Instructor');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Teacher\'s City');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Driving School Name');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Note');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Instructor Name');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Track');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Time Slot');?></th>
        <th align="left" style="font-weight: bold;"><?php echo __('Student Name');?></th>
    </tr>
    <?php 
    $i = 1;
    if(!empty($filterRecords)) {
    ?>
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
                            <?php if(empty($this->request->query['teacher_id'])):?>
                            <td align="left">
                                <?php echo (!empty($booking['course'])) ? $courses[$booking['course']] : ''; ?>
                            </td>
                            <?php endif;?>

                            <td align="left">
                                <?php echo (!empty($booking['booking_teacher_id']))? $users[$booking['booking_teacher_id']]['firstname'].' '.$users[$booking['booking_teacher_id']]['lastname'] : '';?>
                            </td>

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

                                    echo (isset($users[$booking['teacher_id']])) ? $users[$booking['teacher_id']]['firstname'].' '.$users[$booking['teacher_id']]['lastname'] : 'N/A';

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
<br /><br />