<?php

$types      = Configure::read('bookingType'); 
    $lessonTime = Configure::read('lessonTime');
    $status     = Configure::read('lessonStatus');
    $role       = CakeSession::read("Auth.User.role");
    $url = str_replace("/elev-admin/", "", $this->here);
    if($role != 'student'){
?>
<div class="inner-content">
    <div class="row-fluid">
        <div class="spxan12">
            <div class="widget">
                <div class="widget-header">
                    <h5>						
						<?php echo __('Driving Lessons'); ?>
					</h5>  
                    <?php 
                    echo $this->Html->link(__('Add Driving Lesson'),array(
                        'controller'    => 'drivingLessons',
                        'action'        => 'add'
                    ));
                    ?>
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <th align="left"><?php echo __('Teacher Name');?></th>
                            <th align="left"><?php echo __('Student Name');?></th>
                            <th align="left"><?php echo __('Type');?></th>
                            <th align="left"><?php echo __('Start Time');?></th>
                            <th align="left"><?php echo __('Lesson Time');?></th>
                            <th align="left"><?php echo __('Status');?></th>
                            <th align="left"><?php echo __('Actions');?></th>
                        </tr>
                        <?php 
                        $pageNo         = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
                        $i              = ($pageNo - 1)*$perPage;
                        if(!empty($bookings)) {          
                            foreach($bookings as $booking) {   
                                if(isset($booking['DrivingLesson'])){     
                                ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                        <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                        <?php echo $users[$booking['DrivingLesson']['teacher_id']]['firstname'].' '.$users[$booking['DrivingLesson']['teacher_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                        <?php echo $users[$booking['DrivingLesson']['student_id']]['firstname'].' '.$users[$booking['DrivingLesson']['student_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                        <?php echo $types[$booking['DrivingLesson']['type']] ?>
                            </td>
                            <td align="left"><?php echo date('d.m.Y H:i:s',strtotime($booking['DrivingLesson']['start_time'])); ?></td>
                            <td align="left"><?php echo $lessonTime[$booking['DrivingLesson']['lesson_time']]; ?></td>
                            <td align="left"><?php echo (!is_null($booking['DrivingLesson']['status']))?$status[$booking['DrivingLesson']['status']]:'NA'; ?></td>
                            <td align="left">
                                        <?php 
                                        echo $this->Html->link(__('View'), array(
                                                'controller'    => 'drivingLessons',
                                                'action'        => 'view',
                                                $booking['DrivingLesson']['id']
                                        ));
                                        ?>  / <?php 
                                        echo $this->Html->link(__('Edit'), array(
                                                'controller'    => 'drivingLessons',
                                                'action'        => 'edit',
                                                $booking['DrivingLesson']['id']
                                        ));
                                        ?>  / <?php 
                                        echo $this->Html->link(
                                            __('Delete'),
                                            array('controller'    => 'drivingLessons','action'  => 'delete',$booking['DrivingLesson']['id']),
                                            array('class' => 'deleteElement')
                                        );
                                        ?>  
                            </td>   
                        </tr>
                            <?php }else { ?> 
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                        <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                        <?php echo $users[$booking['Systembooking']['user_id']]['firstname'].' '.$users[$booking['Systembooking']['user_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                        <?php echo $users[$booking['Systembooking']['student_id']]['firstname'].' '.$users[$booking['Systembooking']['student_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                        <?php echo $types[$booking['Systembooking']['booking_type']] ?>
                            </td>
                            <td align="left"><?php echo date('d.m.Y H:i:s',strtotime($booking['Systembooking']['start_time'])); ?></td>
                            <td align="left"><?php 
                                    $lesson_type = (is_null($booking['Systembooking']['lesson_type'])) ? 1 : $booking['Systembooking']['lesson_type'];
                                    echo $lessonTime[$lesson_type]; ?></td>
                            <td align="left"><?php echo (!is_null($booking['Systembooking']['status']))?$booking['Systembooking']['status']:'NA'; ?></td>
                            <td align="left">
                                        <?php 
                                        echo $this->Html->link(__('View'), array(
                                                'controller'    => 'drivingLessons',
                                                'action'        => 'view',
                                                $booking['Systembooking']['id']
                                        ));
                                        ?>  / <?php 
                                        echo $this->Html->link(__('Edit'), array(
                                                'controller'    => 'drivingLessons',
                                                'action'        => 'edit',
                                                $booking['Systembooking']['id']
                                        ));
                                        ?>  / <?php 
                                        echo $this->Html->link(
                                            __('Delete'),
                                            array('controller'    => 'drivingLessons','action'  => 'delete',$booking['Systembooking']['id']),
                                            array('class' => 'deleteElement')
                                        );
                                        ?>  
                            </td>   
                        </tr>
                            <?php
                                }
                            }
                        }else{
                        ?>
                        <tr>
                            <td colspan="8" class="index_msg"><?php  echo __('No Driving Lessons are added'); ?></td>
                        </tr>
                        <?php                   
                        }
                        ?>
                    </table>
                </div>
            </div>

            <div>
                <div class="pagination_no">
                <?php
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
                    if(!empty($drivingLessons)) {
                        echo $this->Form->create('DrivingLesson',array(
                                    'class'         => 'row-fluid',
                                    'url'           => array(
                                    'controller'    => 'drivingLessons',
                                    'action'        => 'index'
                                 ),    
                             )
                        );

                        echo $this->Form->input('perPage', array(
                            'options'   => $perPageDropDown,
                            'selected'  => $perPage, 
                            'id'        => 'dropdown' 
                        ));

                        $this->Form->end();
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php }else{  ?>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="grey-block">
                    <h3><?php echo __('Du har'); ?></h3>
                    <h4><?php echo $this->Paginator->param('count').__(' tider booket'); ?><br/></h4>
                </div>
            </div>
        </div>
        <div class="col-xs-6 ">
            <?php if(isset($nextBooking) && !empty($nextBooking)) { ?>
            <div class="white-block">
                <h3><?php echo __('Din næste køretid er : '); ?><br/><span><?php echo (isset($nextBooking)) ? date('d.m.Y H:i',strtotime($nextBooking)): ''; ?></span></h3>
            </div>
            <?php } ?>
        </div>        
    </div>
    <?php  
        $statuses   = array(
            'met'       => __('Complete'),
            'not_met'   => __('Not Met')
        );
        $class      = ($currentUser['User']['role'] == 'student')?'col-sm-12':'';
    ?>
    <div class="col-xs-12 <?php echo $class; ?> info">
        <h3 class="table_title">
            <?php echo __('Dine bestilte og afholdte lektioner'); ?> 
            <div class="clearfix"></div>            
        </h3>
        <table width="100%" style="font-size:14px;" class="notable rtable">
            <thead class="cf">
                <tr class="table_heading">
                    <th class="bill"><?php echo __('No.'); ?></th>
                    <th class="bill"><?php echo ($currentUser['User']['role'] == 'student')?__('Teacher Name'):__('Student Name'); ?></th>
                    <th class="bill"><?php echo __('Type'); ?></th>
                    <th class="bill"><?php echo __('Start Time'); ?></th>
                    <th class="bill"><?php echo __('Lesson Time'); ?></th>
                    <th class="bill"><?php echo __('Status'); ?></th>
                <?php if(($currentUser['User']['role'] != 'student')){ ?>
                    <th class="bill" style="text-align: center;"><?php echo __('Action'); ?></th>
                <?php } ?>
                </tr>
            </thead>
            <?php
            $pageNo         = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
            $i              = ($pageNo - 1)*$perPage;
            if(!empty($bookings)) {
                foreach($bookings as $booking) { ?>
            <tr class="rtable-odd">
                <td class="tbl_detail" data-title="no"><?php echo ++$i; ?></td>
                <td class="tbl_detail" data-title="name"><?php echo ($currentUser['User']['role'] == 'student')?
                            $users[$booking['Systembooking']['user_id']]['firstname'].' '.$users[$booking['Systembooking']['user_id']]['lastname']:
                            $users[$booking['Systembooking']['student_id']]['firstname'].' '.$users[$booking['Systembooking']['student_id']]['lastname']; ?></td>
                <td class="tbl_detail" data-title="type"><?php echo ucfirst($types[$booking['Systembooking']['booking_type']]); ?></td>
                <td class="tbl_detail" data-title="stime"><?php echo date('d.m.Y H:i',strtotime($booking['Systembooking']['start_time'])); ?></td>
                <td class="tbl_detail" data-title="ltime"><?php echo $lessonTime[$booking['Systembooking']['lesson_type']]; ?></td>
                <td class="tbl_detail" data-title="status">
                            <?php
                                $crrSts = $booking['Systembooking']['status'];
                                if($crrSts == 'delete') 
                                    echo __(ucfirst('Slet'));
                                if($crrSts == 'pending') 
                                    echo __(ucfirst('verserende'));
                                if($crrSts == 'unapproved') 
                                    echo __(ucfirst('Udeblevet'));
                                if($crrSts == 'passed') 
                                    echo __(ucfirst('bestaet'));
                                if($crrSts == 'approved') 
                                    echo __(ucfirst('godkendt'));
                                if($crrSts == 'dumped') 
                                    echo __(ucfirst('Dumpet'));
                                else
                                    echo '';
                            ?>
                            
                </td>
                        <?php } ?>
            </tr>
                <?php                
            } else {
                ?>
            <tr>
                <td class="tbl_detail" colspan="<?php echo ($currentUser['User']['role'] != 'student')?7:8?>" align="center"><?php echo __('No Bookings Done'); ?></td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="paginationCt">
        <div class="col-xs-12 col-sm-6 col-md-6 pagination_no">
            <?php
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
                                        array('class' => 'last paginate_button'),
                                        null,
                                        array('class' => 'paginate_button_disabled')
                                            );
            ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 pagination pull-right">
            <?php
                if(!empty($bookings)) {
                    echo $this->Form->create('DrivingLesson',array(
                                'class'         => 'row-fluid',
                                'url'           => array(
                                'controller'    => 'drivingLessons',
                                'action'        => 'index'
                             ),    
                         )
                    );

                    echo $this->Form->input('perPage', array(
                        'options'   => $perPageDropDown,
                        'selected'  => $perPage, 
                        'label' => 'Antal pr. side',
                        'id'        => 'dropdown',
                        'class'     => 'form-control pull-right'
                    ));

                    $this->Form->end();
                }
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php } ?>