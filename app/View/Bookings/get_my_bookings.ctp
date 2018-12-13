<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Timer der ikke er godkendte'); ?></h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('Name');?></th>
                    <th align="left"><?php echo __('Dato & tid');?></th>     
                    <th align="left"><?php echo __('Booking Type');?></th>     
                    <th align="left"><?php echo __('Details');?></th>  
                    <th align="left"><?php echo __('Oprettelses dato');?></th>   
                   
                </tr>
                <?php                
                $i=0;
                if(!empty($bookings)) {
                    foreach($bookings as $booking){        
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php 
                                
                                  
                                        echo $booking['User']['firstname']." ".$booking['User']['lastname'];
                                                                  
                                                                           
                                ?>
                            </td>
                            <td align="left">
                                <?php  echo $booking['Systembooking']['start_time']." To  ".$booking['Systembooking']['end_time'];; ?>
                            </td>
                            <td align="left">
                               
                                <?php echo $booking['Systembooking']['booking_type']; ?>
                            </td>
                            <td align="left">
                                 <?php if($booking['Systembooking']['student_id']){
                                        ?>
                                        <div>
                                            Elev : <?php echo $student_list[$booking['Systembooking']['student_id']]['name'];?> 
                                        </div>
                                        <?php
                                    } ?>
                                     <?php if($booking['Systembooking']['city_id']){
                                        ?>
                                        <div>
                                            Student : <?php echo $booking['Systembooking']['city_id'];?> 
                                        </div>
                                        <?php
                                    } ?>
                                     <?php if($booking['Systembooking']['note']){
                                        ?>
                                        <div>
                                            Notat : <?php echo $booking['Systembooking']['note'];?> 
                                        </div>
                                        <?php
                                    } ?>
                            </td>
                            <td align="left">
                                <?php echo $booking['Systembooking']['created']; ?>
                            </td>
                            
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No records found'); ?></td>
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
            if(!empty($booking)) {
                echo $this->Form->create('Bookings',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'Bookings',
                            'action'        => 'getTeacherBookings'
                         ),    
                     )
                );

                echo $this->Form->input('Pr. side', array(
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