<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5>
                <?php 
                echo __('Bookings');
                if (isset($this->request->query['student_booking_detail']) && 
                        !empty($this->request->query['student_booking_detail'])) {
                    echo __(' of ').$users[$this->request->query['student_booking_detail']]['firstname'].' '.
                            $users[$this->request->query['student_booking_detail']]['lastname'];
                }
                ?>
            </h5>  
            <?php 
            echo $this->Html->link(__('Add Booking'),array(
                'controller'    => 'adminbookings',
                'action'        => 'calendar'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Name');?></th>
                    <th align="left"><?php echo __('Area');?></th>
                    <th align="left"><?php echo __('Date');?></th>
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php                
                $i=0;
                if(!empty($bookings)) {
                    foreach($bookings as $booking){
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; echo (!empty($nextBooking) && $nextBooking[0] == $booking['Booking']['id']) ? 
                        ' next_booking_row' : ''; ?> " align="center"
                        title = "<?php echo (!empty($nextBooking) && $nextBooking[0] == $booking['Booking']['id']) ? __('Next Run Time') : ''; ?>"
                        >
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <td align="left">
                                <?php echo $users[$booking['Booking']['user_id']]['firstname'].' '.$users[$booking['Booking']['user_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                <?php echo Inflector::humanize($booking['Booking']['area_slug']); ?>
                            </td>
                            <td align="left">
                                <?php echo date('d.m.Y',strtotime($booking['Booking']['date'])); ?>
                            </td>
                            <td align="center">
                                <?php                                    
                                echo $this->Html->link(__('View'), array(
                                    'controller'    => 'adminbookings',
                                    'action'        => 'view',
                                    $booking['Booking']['id'],
                                    '?'             => array(
                                        'student_booking_detail' => (isset($this->request->query['student_booking_detail']) && !empty($this->request->query['student_booking_detail'])) 
                                        ? $this->request->query['student_booking_detail']
                                        : ''
                                    )
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Edit'), array(
                                    'controller'    => 'adminbookings',
                                    'action'        => 'calendar',
                                    '?'             => array(
                                        'area'      => $booking['Booking']['area_slug'],
                                        'date'      => $booking['Booking']['date'],
                                    )
                                ));
                                ?> 
                            </td>
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
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
            if(!empty($users)) {
                echo $this->Form->create('User',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'adminusers',
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
</div></div></div>