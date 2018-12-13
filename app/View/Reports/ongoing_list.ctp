<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'   => $this->request->query['report_type'],
                'teacher_id'          => $this->request->query['teacher_id'],
                'teacher'          => $this->request->query['teacher'],
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
            <?php echo (empty($searchString)) ? __('Igangværende liste') : __('Igangværende liste').  implode(' ', $searchString); ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table ipad-table">
                <tr>
                    <th class="hide_on_tablet"><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Navn');?></th>
                    <th align="left"><?php echo __('Adresse');?></th>                    
                    <th align="left"><?php echo __('Teoriprøve');?></th>                    
                    <th align="left"><?php echo __('Køreprøve');?></th>                    
                    <th align="left"><?php echo __('Papirer afleveret');?></th>
                    <th align="left"><?php echo __('Antal timer');?></th>                    
                    <th align="left"><?php echo __('Elev Saldo');?></th>                    
                    <th align="left"><?php echo __('Køreprøve booket');?></th>
                    <th align="left"><?php echo __('Sidste booking');?></th>                    
                    <th align="left"><?php echo __('Dato for næste køretid');?></th>
                </tr>
                <?php 
                if(!empty($Bookings)) {
                     $pageNo = $this->params['paging']['User']['page'];
                $i      = (($pageNo - 1) * $perPage) + 1; 
                    foreach($Bookings as $Raw_Booking){
                        $Booking = $Raw_Booking['User'];
                        $Booking =array_merge($Raw_Booking[0], $Booking);
						$balance = isset($userIds[$Booking['id']]['balance']) ? $userIds[$Booking['id']]['balance']:0;
						// $balance = $Booking['available_balance'];
						$latestDate = isset($userIds[$Booking['id']]['date']) ? $userIds[$Booking['id']]['date']:'';
						
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td class="hide_on_tablet" align="center"><?php echo $i; ?></td>
                            <td align="left">
                                <?php 
                                echo $this->Html->link($Booking['full_name'], array(
                                        'controller'    => 'adminusers',
                                        'action'        => 'edit',
                                        $Booking['id']
                                ));
                                ?>
                            </td>
                            <td align="left"><?php echo $Booking['full_address']; ?></td>
                            <td align="left"><?php echo $Booking['theory_test_passed']; ?></td>
                            <td align="left"><?php echo $Booking['passed_count'] - $Booking['dumped_count']; ?></td>
                            <td align="left"><?php 
                            $date =  ($Booking['firstaid_papirs_date'] != '0000-00-00') ? date('d.m.Y', strtotime($Booking['firstaid_papirs_date'])) : ''; 
                            echo ($Booking['handed_firstaid_papirs'] == 1) ? 'Ja '.$date : 'Nej'; ?></td>
                            <td align="left"><?php echo $Booking['total_count']; ?></td>
                            <td align="left">
								<?php echo $balance; ?>
							</td>
                            <!-- <td align="left"><?php echo $Booking['available_balance']; ?></td> -->
                            <td align="left"><?php echo (!empty($latestDate)) ? date('d.m.Y', strtotime($latestDate)) : ''; ?></td>
                            <td align="left"><?php echo (!empty($Booking['last_booking_date'])) ? date('d.m.Y', strtotime($Booking['last_booking_date'])) : ''; ?></td>
                            <td align="left"><?php echo (!empty($Booking['next_booking_date'])) ? date('d.m.Y', strtotime($Booking['next_booking_date'])) : ''; ?></td>
                        </tr>
                    <?php       
                        $i++;
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="11" class="index_msg"><?php  echo __('Nej Igangværende liste.'); ?></td>
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

    </div>

</div></div></div>