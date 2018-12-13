<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
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
        ?>
    </div>
    <div class="clearfix"></div>
    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php 
            $head = ($this->request->query['report_type'] == 'future_bookings') ? __('Future Bookings') : __('Track Bookings Report');
            echo (empty($searchString)) ? $head : $head.' '.  implode(' ', $searchString); 
            ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table" id="<?php echo (empty($bookings)) ? '' : 'dataTable';?>">
                <thead>
                <tr>
                    <th><?php echo __('No.');?></th>
                    <?php if(isset($this->request->query['area_id']) && empty($this->request->query['area_id'])) { ?> 
                    <th align="left"><?php echo __('Area');?></th>
                    <?php } ?>
                    <th align="left"><?php echo __('Track');?></th>
                    <th align="left"><?php echo __('Date');?></th>
                    <th align="left"><?php echo __('Time Slot');?></th>
                    <?php if(empty($this->request->query['teacher_id'])){ ?>
                    <th align="left"><?php echo __('Instructor Name');?></th>
                    <?php } ?>
                    <th align="left"><?php echo __('On Behalf');?></th>
                    <th align="left"><?php echo __('Co Instructor');?></th>
                    <th align="left"><?php echo __('Driving School Name');?></th>
                    <th align="left"><?php echo __('Note');?></th>
                    <th align="left"><?php echo __('Student Name');?></th>
                </tr>
                </thead>
                <tbody>
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
                            <?php if(empty($this->request->query['teacher_id'])){ ?>
                            <td align="left">
                                <?php echo (isset($booking['user_id']) && !empty($booking['user_id']) && isset($users[$booking['user_id']]))?
                                            $users[$booking['user_id']]['firstname'].' '.$users[$booking['user_id']]['lastname']:''; 
                                ?>
                            </td>
                            <?php } ?>
                            <td align="left">
                                <?php echo (isset($booking['on_behalf']) && !empty($booking['on_behalf']) && isset($users[$booking['on_behalf']]))?
                                            $users[$booking['on_behalf']]['firstname'].' '.$users[$booking['on_behalf']]['lastname']:''; 
                                ?>
                            </td>
                            <td align="left">
                                <?php echo (isset($booking['co_teacher']) && !empty($booking['co_teacher']) && isset($users[$booking['co_teacher']]))?
                                            $users[$booking['co_teacher']]['firstname'].' '.$users[$booking['co_teacher']]['lastname']:''; 
                                ?>
                            </td>
                             <td align="left"><?php 
                                echo (isset($booking['user_id']) && !empty($booking['user_id']) && 
                                     isset($users[$booking['user_id']]) && ($users[$booking['user_id']]['role'] == 'external_teacher')) ?
                                     $drivingSchools[$users[$booking['user_id']]['company_id']] : '';
                            ?></td>
                            <td align="left">
                                <?php echo $booking['note']; ?>
                            </td>
                            <td align="left">
                                <?php 
                                $trackAddress = ($booking['address'] != '' && !is_null($booking['address'])) ? ' ('.$booking['address'].')' : '';
                                echo (isset($booking['student_id']) && !empty($booking['student_id']) && isset($users[$booking['student_id']])) ?
                                    $users[$booking['student_id']]['firstname'].' '.$users[$booking['student_id']]['lastname'] : ((!empty($booking['name']) && !empty($track['address']))?
                                    $booking['name'].$trackAddress :__('External Student'));
                                ?>
                            </td>
                        </tr>
                    <?php       
                        $i++;
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="11" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
                    </tr>
                <?php 
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>    
</div></div></div>