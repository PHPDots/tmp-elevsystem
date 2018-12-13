<table cellpading="0" cellspacing="0" border="0" class="default-table">     
    <thead>
        <tr>
            <th align="left"><?php echo __('Name'); ?></th>
            <th align="left"><?php echo __('Area'); ?></th>
            <th align="left"><?php echo __('Booking Date'); ?></th>
            <th align="left"><?php echo __('Time Slot'); ?></th>
            <th align="left"><?php echo __('Track'); ?></th>
            <th align="left"><?php echo __('Action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($bookedTracks)) {
            $i = 0;
            foreach($bookedTracks as $bookingId => $data) {
                foreach($data as $timeSlot => $booking) {
                    $allTracks = array_keys($booking);
                    $booking = $booking[$allTracks[0]];
                    $trackArr = array();
                    foreach($allTracks as $track) {
                        $trackArr[] = $tracks[$booking['Booking']['area_slug']][$track]['name'];
                    }
                    if(!empty($users[$booking['Booking']['user_id']]['firstname'])){
                ?>
                <tr class="link_row <?php echo ($i % 2 == 0) ? 'even' : 'odd' ?>">
                    <td><?php echo (isset($users[$booking['Booking']['user_id']]) && !empty($users[$booking['Booking']['user_id']])) ? $users[$booking['Booking']['user_id']]['firstname'].' '.$users[$booking['Booking']['user_id']]['lastname'] : 'N/A'; ?></td>
                    <td><?php echo Inflector::humanize($booking['Booking']['area_slug']); ?></td>
                    <td><?php echo date('d.m.Y',strtotime($booking['Booking']['date'])); ?></td>
                    <td><?php echo $booking['BookingTrack']['time_slot']; ?></td>
                    <td><?php echo implode(',', $trackArr); ?></td>
                    <td><a href="<?php 
                        echo $this->Html->url(array(
                            'controller'    => 'adminbookings' , 
                            'action'        => 'calendar', 
                            '?' => array(
                                'date' => $booking['Booking']['date'],
                                'area' => $booking['Booking']['area_slug']
                        ))); ?>" target="_blank">
                        <?php echo __('View Details'); ?>
                    </a></td>
                </tr>
                <?php
                
                $i++;
                    }
                }
            }
        } else {
            ?>
            <tr><td align="center" colspan="6"><?php echo __('No Bookings Done'); ?></td></tr>
            <?php
        }
        ?>
    </tbody>
</table>