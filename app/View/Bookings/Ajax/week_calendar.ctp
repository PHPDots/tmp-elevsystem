<?php $weekDanish = Configure::read('weekDanish'); ?>
<div class="clearfix"></div>
<div class="span12 no-margin">
    <div class="calendarPagination">
        <a href="javascript:" class="calendarPaginationLink pull-left"  id="previousCalendar" week-no="<?php echo $prevWeek; ?>" year="<?php echo $prevYear; ?>"><?php echo '<i class="fa fa-angle-left"></i>&nbsp;&nbsp;'.__('Previous'); ?></a>
        <a href="javascript:" class="calendarPaginationLink pull-right" id="nextCalendar" week-no="<?php echo $nextWeek; ?>" year="<?php echo $nextYear; ?>"><?php echo __('Next').'&nbsp;&nbsp;<i class="fa fa-angle-right"></i>'; ?></a>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="span12 no-margin">
    <?php 
    $i = 0;
    foreach($areas as $area) {
    ?>
    <div class="booking_calendar_view span6 <?php echo ($area['Area']['slug'] == $selectedArea) ? '' : 'disabled_area' ?>">
        <div class="booking_calendar_title center">
            <h2 class="center"><?php echo $area['Area']['name'].' - '.$calendarTitle; ?></h2>
        </div>
        <div class="clearfix"></div>
        <div class="table-responsive">
            <table border="1" cellpadding="10" id="week_calendar_<?php echo ++$i; ?>" year="<?php echo $year; ?>" start-date="<?php echo $startDate; ?>" date="<?php echo $currDate;?>" week="<?php echo $weekNo;?>" class="booking_calendar_table">
                <?php 
                $rows = 0;
                foreach($weekTimeSlots[$area['Area']['slug']] as $key => $weekTimeSlot) {
                    if($key == 'timeSlot') {
                    ?>
                    <tr>
                        <th><?php echo $weekTimeSlot['label']; ?></th>
                        <?php
                        foreach($weekTimeSlot['slots'] as $date => $slots) {
                            ?>
                            <th slot-date="<?php echo $date; ?>" class="<?php echo ($date == date('Y-m-d')) ? 'today' : ''; ?>"><?php echo $weekDanish[$slots['day']].' '.$slots['date']; ?></th>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php } else { ?>
                        <tr>
                        <?php foreach($weekTimeSlot as $date => $slots) { ?>
                            <td time-slot="<?php echo $key;?>" slot-date="<?php echo $date; ?>" class="<?php echo ($rows == count($weekTimeSlots[$area['Area']['slug']]) - 1) ? 'last_item ' : ''; echo ($date == $currDate) ? 'today ' : ''; ?> <?php echo ($date != 'timeSlot') ? 'date_event ' : ''; echo ($date == 'timeSlot') ? '' : 'slot-'.$slots['class'].'-color'; ?>"><?php echo ($date == 'timeSlot') ? $slots : $slots['count']; ?><?php echo ($date != 'timeSlot' && $slots['school'] != '') ? '<br />'.$slots['school'] : ''; ?></td>
                        <?php } ?>
                        </tr>
                        <?php
                    }
                    $rows++;
                }
                ?>
            </table>
        </div>
    </div>
    <?php } ?>
</div>
<div class="clearfix"></div>