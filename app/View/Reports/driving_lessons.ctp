<?php 
    $types      = Configure::read('bookingType'); 
    $lessonTime = Configure::read('lessonTime');
    $status     = Configure::read('lessonStatus');
?>
<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'   => $this->request->query['report_type'],
                'datetime_from' => $this->request->query['datetime_from'],
                'datetime_to'   => $this->request->query['datetime_to'],
                'csv'           => 'true',
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
            $head = ($this->request->query['report_type'] == 'unapproved_driving_lessons') ? __('Unapproved Driving Lessons') : __('Driving Lessons');
            echo (empty($searchString)) ? $head : $head.__(' from').' '.  implode(' ', $searchString); 
            ?>
            </h5>
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
                </tr>
                <?php 
                $pageNo = $this->params['paging']['DrivingLesson']['page'];
                $i = (($pageNo - 1) * $perPage) + 1;
                if(!empty($drivingLessons)) {
                    foreach($drivingLessons as $drivingLesson){
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <td align="left">
                                <?php echo $users[$drivingLesson['DrivingLesson']['teacher_id']]['firstname'].' '.$users[$drivingLesson['DrivingLesson']['teacher_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                <?php echo $users[$drivingLesson['DrivingLesson']['student_id']]['firstname'].' '.$users[$drivingLesson['DrivingLesson']['student_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                <?php echo $types[$drivingLesson['DrivingLesson']['type']]; ?>
                            </td>
                            <td align="left">
                                <?php echo date('d.m.Y H:i:s',strtotime($drivingLesson['DrivingLesson']['start_time'])); ?>
                            </td>
                            <td align="left">
                                <?php echo $lessonTime[$drivingLesson['DrivingLesson']['lesson_time']]; ?>
                            </td>
                            <td align="left">
                                <?php echo (!is_null($drivingLesson['DrivingLesson']['status'])) ? $status[$drivingLesson['DrivingLesson']['status']] : __('N/A'); ?>
                            </td>
                        </tr>
                    <?php       
                        $i++;
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="7" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
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
                'action'        => 'drivingLessons',
                '?'             => array(
                    'datetime_from' =>$this->request->query['datetime_from'],
                    'datetime_to'   =>$this->request->query['datetime_to'],
                ),
            )));
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
</div></div></div>