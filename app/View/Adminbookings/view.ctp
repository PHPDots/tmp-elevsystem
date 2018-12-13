<div class="inner-content"><div class="row-fluid"><div class="user-profile row-fluid">
    <div class="span6">
        <div class="widget">
        <div class="widget-header"><h5><?php echo __('Booking Details'); ?></h5></div>
        <div class="slide">
            <div class="widget-content">
                <div class="span12">
                    <div class="user-line">
                        <label><?php echo __('Instructor'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo $users[$bookingDetails['Booking']['user_id']]['firstname'] .'&nbsp;'. $users[$bookingDetails['Booking']['user_id']]['lastname']; ?></label>
                    </div>
                    <div class="user-line">
                        <label><?php echo __('Date'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo date('d.m.Y',strtotime($bookingDetails['Booking']['date'])); ?></label>
                    </div>
                    <div class="user-line">
                        <label><?php echo __('Area'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo Inflector::humanize($bookingDetails['Booking']['area_slug']); ?></label>
                    </div>
                    <div class="user-line">
                        <label><?php echo __('Long Description'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo (empty($bookingDetails['Booking']['full_description']))?__('No decscription'):$bookingDetails['Booking']['full_description']; ?></label>              
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        </div>
    </div>
    <div class="span6">
        <?php 
        $i = 1;
        if(empty($bookingTracks)) {
            $bookings = $bookingDetails['BookingTrack'];
        } else {
            $bookings = $bookingTracks;
        }
        foreach($bookings as $bookingTrack) {
            if(!empty($bookingTracks)) {
                $bookingTrack = $bookingTrack['BookingTrack'];
            }
        ?>
        <div class="widget">
            <div class="widget-header">
            <h5 class="pull-left"><?php echo $i.'. '.$users[$bookingTrack['student_id']]['firstname'] .' '. $users[$bookingTrack['student_id']]['lastname'].'\'s '.__('Track Details '); ?></h5>
            <span class="pull-right">
                <?php
                echo $this->Html->link(__('View ').$users[$bookingTrack['student_id']]['firstname'].'\'s '.__('Details'),array(
                        'controller'    => 'adminusers',
                        'action'        => 'view',
                        $bookingTrack['student_id']
                    ), array(
                        'target' => '_blank'
                )); 
                ?>
            </span>
            </div>
            <div class="slide">
                <div class="widget-content">
                    <div class="span12">
                        <div class="span6">
                        <label><?php echo __('Track'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo $lanes[$bookingTrack['track_id']]['name']; ?></label>
                        </div>
                        <div class="span6 no-margin">
                        <label><?php echo __('Time Slot'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo $bookingTrack['time_slot']; ?></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
            $i++;
        }
        ?>
    </div>
</div></div></div>