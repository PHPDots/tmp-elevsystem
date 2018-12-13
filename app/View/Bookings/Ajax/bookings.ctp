<?php $this->append('script'); ?>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery( "[title]" ).tooltip({
        position: {
            my: "left top",
            at: "right+5 top-5"
        }
    });
});
</script>
<?php $this->end(); ?>
<?php setlocale(LC_ALL,'da_DK'); ?>
<div class="tableLicense bookingListTable">
    <div class="bookingTitle"><h5><?php //echo date('M d, Y',strtotime($date));
//         echo strftime('%d %B, %Y',strtotime($date));
    $danishMonths = Configure::read('danishMonths');
    echo date('d',  strtotime($date)).' '.$danishMonths[date('F',  strtotime($date))].', '.date('Y',  strtotime($date));
    ?></h5></div>
    <table cellpading="0" cellspacing="0" border="0" class="default-table">
        <?php 
        $i = 1;
        if(!empty($finalBookingDetails)) {
        ?>
        <tr class="<?php echo ($i%2==0)?'even':'odd';?>">
            <?php 
            $j = 0; 
            foreach($headers as $head) {
                $icon   = ($j!= 0)?'<i class="fa fa-car"></i>&nbsp;&nbsp;':'';
            ?>
            <th align="center"><?php echo $icon.$head; ?></th>
            <?php 
                $j++;
            }
            ?>
            <th width="13%">&nbsp;</th>
        </tr>
        <?php 

        foreach($finalBookingDetails as $finalBookingDetail) {

            $releasedTrack      = Hash::extract($finalBookingDetail,'{n}.release_track');
            $trackStatus        = Hash::extract($finalBookingDetail,'{n}.track_status');
            $result             = in_array('1', $releasedTrack);
            $trackStatusFlag    = in_array('closed',$trackStatus);
            $reopenFlag         = isset($timeSlotWiseBookings[$finalBookingDetail[0]['key']]['track_status'])?TRUE:FALSE;
            $teachers           = Hash::extract($finalBookingDetail,'{n}.teacher');
            
            ?>
            <tr class="<?php echo (++$i%2==0)?'even':'odd';?>">
                <?php 
                    $date       = date('d.m.Y',strtotime($date));
                    $startSlot  = explode('-',$finalBookingDetail[0]['key']);
                    $dateTime   = $date.' '.$startSlot[0];

                    $show_button = false;

                    foreach ($finalBookingDetail as $bookingDetail) {   
                        $username = '';

                        if(isset($bookingDetail['time_slot'])) {
                            $oneTimeSlot        = $generatedTimeSlots['mapping'][$area][$bookingDetail['time_slot']][0];
                            $relatedBookingId   = (isset($timeSlotWiseBookings[$oneTimeSlot][0]) && !empty($timeSlotWiseBookings[$oneTimeSlot][0])) ? $timeSlotWiseBookings[$oneTimeSlot][0]['booking_id'] : FALSE;
                        }

                        $dateofbirthclass   = (isset($bookingDetail['date_of_birth']) && !empty($bookingDetail['date_of_birth']))?'addBackground':'addBackgroundRed';
                        
                        $metUsersClass      = 'registered_user_green';
                        
                        $noteAvailable = (isset($bookingDetail['note']) && !empty($bookingDetail['note'])) ? 
                        '<span class="pull-right note_available" title="'.$bookingDetail['note'].'"><i class="fa fa-sticky-note"></i></span>' : '';

                        if(isset($bookingDetail['key']) && !empty($bookingDetail['key'])) {
                            $url   = $bookingDetail['key'];
                        } else if(isset($bookingDetail['time_slot']) && !empty($bookingDetail['time_slot'])) {
                            $date       = date('d.m.Y',strtotime("{$date}"));

                            $timeslot   = $bookingDetail['time_slot'];

                            $deactiveClass   = (strtotime($dateTime) > time() && !$result) ? '' : 'deactiveLink';
                            $url    = '<a href="javascript:" class="bookingLink '.$deactiveClass.'" area="'.$area.'" date="'.$date.'" time="'.$timeslot.'" 
                                        track='.$bookingDetail['track_id'].' reference="'.$relatedBookingId.'">';
                            $url   .= '<i class="fa fa-plus-circle"></i>';
                            $url   .= '</a>';
                        } else {
                            $show_button = TRUE;

                            $link       = $this->Html->url(array('controller' => 'bookings','action'  => 'edit',$bookingDetail['id']));
                            $linkClass  = (!$result) ? 'bookingLinkName ' : '';
                            $linkClass .= ($bookingDetail['teacher'] == $currentUser['User']['id']) ? 'logged_in_teacher ' : '';

                            $deactiveClass   = ((strtotime($dateTime) > time()) && (!$result)) ? 'className' :  (isset($bookingDetail['track_status']) && ($bookingDetail['track_status'] == 'reopen'))? '' : 'deactiveLink';

                            if(($userDetails[$bookingDetail['teacher']]['role'] == 'external_teacher') && ($bookingDetail['user'] == -1)) {
                                $username   = $userDetails[$bookingDetail['teacher']]['firstname'].' '.$userDetails[$bookingDetail['teacher']]['lastname'];
                                $firstname  = (empty($userDetails[$bookingDetail['teacher']]['nick_name_user']))?$userDetails[$bookingDetail['teacher']]['firstname']:$userDetails[$bookingDetail['teacher']]['nick_name_user'];

                                if(!empty($userDetails[$bookingDetail['teacher']]['company_id'])) {
                                    $firstname .= ' ( '.$userDetails[$bookingDetail['teacher']]['company_id'].' )';
                                }

                                if(!empty($userDetails[$bookingDetail['teacher']]['company'])) {
                                    $username  .= ' - '.$userDetails[$bookingDetail['teacher']]['company'];
                                }

                                $url    = '<a href="javascript:" url="'.$link.'" class="'.$linkClass.$metUsersClass.' '.$dateofbirthclass.' '.$deactiveClass.'" title="'.$username.'" area="'.$area.'" date="'.$date.'" time="'.$finalBookingDetail[0]['key'].'" item-id="'.$bookingDetail['id'].'">';
                                $url   .= '[ '.$firstname.' ]';
                                $url   .= "<a>";
                            } else if(isset($bookingDetail['user']) && ($bookingDetail['user'] != NULL) && (in_array($userDetails[$bookingDetail['teacher']]['role'],array('internal_teacher','admin')))) {
                                $username   = $userDetails[$bookingDetail['teacher']]['firstname'].' '.$userDetails[$bookingDetail['teacher']]['lastname'];

                                if(!empty($userDetails[$bookingDetail['teacher']]['company'])) {
                                    $username  .= ' - '.$userDetails[$bookingDetail['teacher']]['company'];
                                }

                                if($currentUser['User']['role'] != 'external_teacher' && isset($userDetails[$bookingDetail['user']])) {
                                    $username   .=  ' - '. $userDetails[$bookingDetail['user']]['firstname'].' '. $userDetails[$bookingDetail['user']]['lastname'];
                                }

                                $firstname  = (empty($userDetails[$bookingDetail['teacher']]['nick_name_user'])) ? $userDetails[$bookingDetail['teacher']]['firstname'] :  $userDetails[$bookingDetail['teacher']]['nick_name_user'];

                                if(!empty($userDetails[$bookingDetail['teacher']]['company_id'])) {
                                    $firstname .= ' ( '.$userDetails[$bookingDetail['teacher']]['company_id'].' )';
                                }

                                $url    = '<a href="javascript:" url="'.$link.'" class="'.$linkClass.$metUsersClass.' '.$dateofbirthclass.' '.$deactiveClass.'" title="['.$username.']" area="'.$area.'" date="'.$date.'" time="'.$finalBookingDetail[0]['key'].'" item-id="'.$bookingDetail['id'].'">';
                                $url   .= '[ '.$firstname;

                                    if($currentUser['User']['role'] != 'external_teacher' && isset($userDetails[$bookingDetail['user']])) {
                                        $url   .=  ' - '. $userDetails[$bookingDetail['user']]['firstname'];     
                                    }

                                $url   .=  ' ]';
                                $url   .= "<a>";
                            } else {
                                $username   = $userDetails[$bookingDetail['teacher']]['firstname'].' '.$userDetails[$bookingDetail['teacher']]['lastname'];

                                if(!empty($userDetails[$bookingDetail['teacher']]['company'])) {
                                    $username  .= ' - '.$userDetails[$bookingDetail['teacher']]['company'];
                                }

                                if($currentUser['User']['role'] != 'external_teacher') {
                                    $username   .= ' - NA ';
                                }

                                $firstname  = (empty($userDetails[$bookingDetail['teacher']]['nick_name_user']))?$userDetails[$bookingDetail['teacher']]['firstname']:$userDetails[$bookingDetail['teacher']]['nick_name_user'];

                                if(!empty($userDetails[$bookingDetail['teacher']]['company_id'])) {
                                    $firstname .= ' ( '.$userDetails[$bookingDetail['teacher']]['company_id'].' )';
                                }

                                $url    = '<a href="javascript:" url="'.$link.'" class="'.$linkClass.$metUsersClass.' '.$dateofbirthclass.' '.$deactiveClass.'" title="['.$username.']" area="'.$area.'" date="'.$date.'" time="'.$finalBookingDetail[0]['key'].'" item-id="'.$bookingDetail['id'].'">';

                                $url   .= '[ '.$firstname;
                                    if($currentUser['User']['role'] != 'external_teacher') {
                                        $url   .= ' - NA ';
                                    }
                                $url   .= ' ] ';

                                $url   .= "<a>";
                            }
                        }
                    ?>

                    <td align="center" title="<?php echo ($username != '') ? '['.$username.']' : '';?>"
                        <?php echo isset($bookingDetail['track_id']) ? 'track-id="'.$bookingDetail['track_id'].'"' : '' ?>>
                        <?php echo $url; ?>
                        <?php echo $noteAvailable; ?>
                    </td>
                <?php } ?>
                
                <td align="center" width="20%">
                    <?php
                        $ipAddresses = array($released_ip_address,'180.211.118.242','192.168.1.29');
                        $time = strtotime(date('Y-m-d H:i:s'));
                        $track_time = date('Y-m-d H:i:s', strtotime($dateTime));

                        $tt = strtotime($track_time);
                        $minus_12_hour = strtotime('-12 hours',strtotime($track_time));

                        if( $show_button && (strtotime('+12 hours',strtotime($track_time)) > $time) && (strtotime('-12 hours',strtotime($track_time)) < $time) ) {
                            //https://trello.com/c/aAWXNEgw/55-open-close-tracks
                            //Release track: 12 hours before time
                            //if( (strtotime('-12 hours',strtotime($track_time)) < $time) && (!$result) && !$trackStatusFlag && in_array($_SERVER['REMOTE_ADDR'], $ipAddresses)){
                            if( ($time > $minus_12_hour) && (!$result) && !$trackStatusFlag && in_array($_SERVER['REMOTE_ADDR'], $ipAddresses)){
                                ?>
                                    <a style="width:40%;float:left;" href="javascript:" class="releaseTrack button button-green" area="<?php echo $this->request->query['area']; ?>" date = "<?php echo $this->request->query['date']; ?>" time_slot="<?php echo $finalBookingDetail[0]['key']; ?>">
                                        <?php echo __('Release Track'); ?>
                                    </a>            
                                <?php 
                            } else if( isset($finalBookingDetail[0]['key']) && isset($timeSlotWiseBookings[$finalBookingDetail[0]['key']]) && !empty($timeSlotWiseBookings[$finalBookingDetail[0]['key']])) {
                                //Re open: Shows after the Close button is pressed or when other track is opened

                                if($trackStatus[0] == 'reopen') {
                                    ?>
                                        <a style="width:30%;float:left;" href="javascript:" class="releaseTrack reopenTracks button button-green" area="<?php echo $this->request->query['area']; ?>" date = "<?php echo $this->request->query['date']; ?>" time_slot="<?php echo $finalBookingDetail[0]['key']; ?>">
                                            <?php echo __('Reopen'); ?>
                                        </a>
                                    <?php
                                } else if($trackStatus[0] == 'closed') {
                                    //Close: Shows after the Release track button is pressed
                                    ?>
                                        <a href="javascript:" class="closeTrack button button-green" area="<?php echo $this->request->query['area']; ?>" date = "<?php echo $this->request->query['date']; ?>" time_slot="<?php echo $finalBookingDetail[0]['key']; ?>">
                                            <?php echo __('Close'); ?>
                                        </a>
                                    <?php
                                }
                            }
                        } else {
                           /* echo '<pre>';
                            print_r(date('d-m-Y H:i', strtotime('-12 hours',strtotime($dateTime))));
                            echo '</pre>';

                            echo '<pre>';
                            print_r(date('d-m-Y H:i', strtotime($dateTime)));
                            echo '</pre>';

                            echo '<pre>';
                            print_r(date('d-m-Y H:i'));
                            echo '</pre>';

                            echo '<pre>';
                            print_r(date('d-m-Y H:i', strtotime('+12 hours',strtotime($dateTime))));
                            echo '</pre>';*/
                        }
                    ?>


                    <?php
                        /*
                            if( isset($finalBookingDetail[0]['key']) && isset($timeSlotWiseBookings[$finalBookingDetail[0]['key']]) && !empty($timeSlotWiseBookings[$finalBookingDetail[0]['key']])) {
                                if(!$reopenFlag && $result) {
                            ?>  
                                <a style="width:30%;float:left;" href="javascript:" class="releaseTrack reopenTracks button button-green" area="<?php echo $this->request->query['area']; ?>"
                                    date = "<?php echo $this->request->query['date']; ?>" time_slot="<?php echo $finalBookingDetail[0]['key']; ?>">
                                    <?php echo __('Reopen'); ?>
                                </a>
                            <?php } else if($reopenFlag) { ?>
                                <a href="javascript:" class="closeTrack button button-green" area="<?php echo $this->request->query['area']; ?>"
                                    date = "<?php echo $this->request->query['date']; ?>" time_slot="<?php echo $finalBookingDetail[0]['key']; ?>">
                                    <?php echo __('Close'); ?>
                                </a>
                            <?php    }  }
                            $ipAddresses = array($released_ip_address,'180.211.118.242','192.168.1.29');
                            if((strtotime('+12 hours',strtotime($dateTime)) > time()) && (strtotime('-12 hours',strtotime($dateTime)) < time()) && (!$result) && !$trackStatusFlag && in_array($_SERVER['REMOTE_ADDR'], $ipAddresses)) {
                            ?>
                            <a style="width:40%;float:left;" href="javascript:" class="releaseTrack button button-green" area="<?php echo $this->request->query['area']; ?>"
                               date = "<?php echo $this->request->query['date']; ?>" time_slot="<?php echo $finalBookingDetail[0]['key']; ?>">
                                <?php echo __('Release Track'); ?>
                            </a>
                            <?php  }
                        */
                    ?>
                </td>
            </tr>
        <?php 
            } 
        }
        ?>
    </table>
</div>