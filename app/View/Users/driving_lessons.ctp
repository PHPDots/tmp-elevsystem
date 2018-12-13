<div class="row">
    <?php if($currentUser['User']['role'] == 'internal_teacher'){ ?>
    <div class="col-xs-12">
        <div class="col-xs-6">
            <div class="grey-block">
                <h3><?php echo __('You have total'); ?></h3>
                <h4><?php echo count($bookings).__(' Tracks Booked'); ?><br/></h4>
            </div>
        </div>
        <div class="col-xs-6 ">
            <?php if(isset($nextBooking) && !empty($nextBooking)) { ?>
            <div class="white-block">
                <h3><?php echo __('Din næste køretid er: '); ?><br/><span><?php echo (isset($nextBooking['Booking'])) ? date('d/m/Y',strtotime($nextBooking['Booking']['date'])).' <br /> '.$nextBooking['BookingTrack']['time_slot'] : ''; ?></span></h3>
            </div>
            <?php } ?>
        </div>
        
    </div>
    <?php } 
        $statuses   = array(
            'met'       => __('Complete'),
            'not_met'   => __('Not Met')
        );
        $class      = ($currentUser['User']['role'] == 'student')?'col-sm-7':'';
    ?>
    <div class="col-xs-12 col-sm-12 info">
        <p>
            Hvis du skal på bane på Kolding køretekniske anlæg, kan du nedenfor se hvilen tid der er
reserveret til dig på anlægget. Anvender din kørelærer andre køretekniske anlæg, har du fået
besked om din banetid på anden vis.
        </p>
        <p style="color: red;">
            Vær opmærksom på at tiden nedenfor er det tidspunkt du skal være på Kolding Køretekniske
anlæg. Du skal følge den aftale du har lavet med din kørelærer om transport og evt tidspunkt
herfor. 
        </p>
    </div>

    <div class="col-xs-12 <?php echo $class; ?> info">
        <h3>
            <?php echo __('Banetider'); ?>            
        </h3>
        <table width="100%" style="font-size:14px;">
            <tr class="table_heading">
                <th class="bill"><?php echo __('Date'); ?></th>
                <th class="bill"><?php echo __('Tid'); ?></th>
                <th class="bill"><?php echo __('Område'); ?></th>
                <!--<th class="bill"><?php echo __('Track'); ?></th>-->
                <?php if($currentUser['User']['role'] == 'student'){ ?>
                <th class="bill"><?php echo __('Kørelærer'); ?></th>
                <?php } ?>
                <?php if($currentUser['User']['role'] == 'internal_teacher'){ ?>
                <th class="bill"><?php echo __('Student'); ?></th>
                <th class="bill"><?php echo __('Status'); ?></th>
                <th class="bill" align="center"><?php echo __('Action'); ?></th>
                <?php } ?>
            </tr>
            <?php
            if(!empty($bookings)) {
                foreach($bookings as $booking) {                     
                ?>
                    <tr>
                        <td class="tbl_detail"><?php echo date('d.m.Y',strtotime($booking['Booking']['date'])); ?></td>
                        <td class="tbl_detail"><?php echo $booking['BookingTrack']['time_slot']; ?></td>
                        <td class="tbl_detail"><?php echo Inflector::humanize($booking['Booking']['area_slug']); ?></td>
                        <!--<td class="tbl_detail"><?php echo $tracks[$booking['BookingTrack']['track_id']]['name']; ?></td>-->
                        <?php if($currentUser['User']['role'] == 'student'){ ?>
                        <td class="tbl_detail"><?php echo $users[$booking['Booking']['user_id']]['firstname'].' '.$users[$booking['Booking']['user_id']]['lastname']; ?></td>
                        <?php } ?>
                        <?php if($currentUser['User']['role'] == 'internal_teacher'){ ?>
                        <td class="tbl_detail"><?php echo (!empty($booking['BookingTrack']['student_id']))?
                                                    $users[$booking['BookingTrack']['student_id']]['firstname'].' '.$users[$booking['BookingTrack']['student_id']]['lastname']:
                                                    ''
                                                ; ?></td>
                        <td class="tbl_detail"><?php echo (!is_null($booking['BookingTrack']['status']))?$statuses[$booking['BookingTrack']['status']]:__('Status Not Updated.'); ?></td>
                        <td class="tbl_detail" align="center">
                            <?php
                                echo $this->Html->link(__('Edit'),array(
                                    'controller'    => 'bookings',
                                    'action'        => 'calendar',
                                    '?'             => array(
                                        'date'      => $booking['Booking']['date'],
                                        'area'      => $booking['Booking']['area_slug']
                                    )
                                )); 
                            ?> <?php if(is_null($booking['BookingTrack']['status'])){ ?>
                            / <?php
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'bookings',
                                    'action'        => 'calendar',
                                    '?'             => array(
                                        'date'      => $booking['Booking']['date'],
                                        'area'      => $booking['Booking']['area_slug']
                                    )
                                )); 
                            ?> / <?php
                                echo $this->Html->link(__('Complete'),array(
                                    'controller'    => 'bookings',
                                    'action'        => 'status',   
                                    $booking['BookingTrack']['id'],
                                    '?'             => array(
                                        'status'    => 'met'
                                    )
                                )); 
                            ?> / <?php
                                echo $this->Html->link(__('Not met'),array(
                                    'controller'    => 'bookings',
                                    'action'        => 'status',        
                                     $booking['BookingTrack']['id'],
                                    '?'             => array(
                                        'status'    => 'not_met'
                                    )                          
                                )); 
                            ?>
                            <?php } ?>
                        </td>
                        <?php } ?>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td class="tbl_detail" colspan="<?php echo ($currentUser['User']['role'] == 'student')?4:6; ?>" align="center"><?php echo __('No Bookings Done'); ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <?php if($currentUser['User']['role'] == 'student'){ ?>
    <div class="col-xs-12 col-sm-5">
        <div class="col-xs-12 grey-block">
            <h3><?php echo __('Du har'); ?></h3>
            <h4>				
				<?php echo count($bookings).__(' banetider'); ?>
				<br/>
			</h4>
        </div>
        <?php if(isset($nextBooking) && !empty($nextBooking)) { ?>
        <div class="col-xs-12 white-block">
            <h3><?php echo __('Din næste køretid er: '); ?><br/><span><?php echo (isset($nextBooking['Booking'])) ? date('d/m/Y',strtotime($nextBooking['Booking']['date'])).' <br /> '.$nextBooking['BookingTrack']['time_slot'] : ''; ?></span></h3>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>