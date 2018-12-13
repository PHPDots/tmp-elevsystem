<?php $this->append('script'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#selectArea').change(function() {
        area = jQuery(this).val();
        jQuery.ajax({
            url          : "<?php echo $this->Html->url(array('controller' => 'adminpages' , 'action' => 'home')) ?>?area="+area,
            dataType     : "html",
            beforeSend   : function() {
               jQuery('.loading-img').show();
            },
            complete     : function() {
               jQuery('.loading-img').hide();
            },
            success      : function(data) {
               if(data != '') {
                   jQuery('.bookingTable').html(data);
               }
            },
            error        : function() {
               alert('Error Occured');
            }
        });
    });
});
</script>
<?php $this->end(); pr(date('Y-m-d H:i')); ?>
<div class="inner-content">    
    <div class="row-fluid">
        <div class="span7">
            <div class="widget">
                <div class="widget-header">            
                    <h5>
                        <?php echo '<i class="fa fa-bookmark"></i>&nbsp;&nbsp;'.__("Recent Bookings")?>
                    </h5>
                    <div class="pull-right">
                        <?php
                        echo $this->Html->image('ajax-loader.gif',array(
                            'class' => 'hide loading-img',
                        ));
                        echo $this->Form->select('area',$areas,array(
                            'empty' => __('All Areas'),
                            'label' => FALSE,
                            'div'   => FALSE,
                            'class' => 'home-area',
                            'id'    => 'selectArea'
                        ));
                        ?>
                    </div>
                </div>
                <div class="widget-content tableLicense bookingTable">
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
                                $i      = 0;
                                $count  = 0;
                                foreach($bookedTracks as $bookingId => $data) {
                                    if($count < 15) {
                                    foreach($data as $timeSlot => $booking) {
                                        $allTracks = array_keys($booking);
                                        $booking = $booking[$allTracks[0]];
                                        $trackArr = array();
                                        foreach($allTracks as $track) {
                                            $trackArr[] = $tracks[$booking['Booking']['area_slug']][$track]['name'];
                                        }
                                        asort($trackArr);
                                    ?>
                                    <tr class="link_row <?php echo ($i%2 == 0) ? 'even' : 'odd'; ?>">
                                        <td><?php echo (isset($users[$booking['Booking']['user_id']]) && !empty($users[$booking['Booking']['user_id']])) ? $users[$booking['Booking']['user_id']]['firstname'].' '.$users[$booking['Booking']['user_id']]['lastname'] : 'N/A'; ?></td>
                                        <td><?php echo $areaListArr[$booking['Booking']['area_slug']]; ?></td>
                                        <td><?php echo date('d.m.Y',strtotime($booking['Booking']['date'])); ?></td>
                                        <td><?php echo $booking['BookingTrack']['time_slot']; ?></td>
                                        <td><?php echo implode(',', $trackArr); ?></td>
                                        <td><a href="<?php 
                                            echo $this->Html->url(array(
                                                'controller'    => 'adminbookings' , 
                                                'action'        => 'calendar', 
                                                '?' => array(
                                                    'date' => $booking['Booking']['date'],
                                                    'area' => $booking['Booking']['area_slug'],
                                                    'week' => date('W',strtotime($booking['Booking']['date'])),
                                            ))); ?>" target="_blank">
                                            <?php echo __('View Details'); ?>
                                        </a></td>
                                    </tr>
                                    <?php
                                        $i++;
                                    }
                                    $count++;
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
                </div>  
            </div>
        </div>
    </div>
</div>
