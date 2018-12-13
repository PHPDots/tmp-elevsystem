
<div class="clearfix"></div>

<div class="span12 no-margin">
    <div class="booking_calendar_view span6">
        <div class="booking_calendar_title calendarPagination center">
            <a href="javascript:" class="calendarPaginationLink pull-left span2 no-margin" id="previousCalendar" week-no="<?php echo $prevWeek; ?>" year="<?php echo $prevYear; ?>">
                <?php echo '<i class="fa fa-angle-double-left "></i>'; ?>
            </a>
            <h2 class="span8 center"><?php echo $calendarTitle; ?></h2>
            <a href="javascript:" class="calendarPaginationLink pull-right span2 no-margin" id="nextCalendar" week-no="<?php echo $nextWeek; ?>" year="<?php echo $nextYear; ?>">
                <?php echo '<i class="fa fa-angle-double-right "></i>'; ?>
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <table border="1" cellpadding="10" id="week_calendar" class="booking_calendar_table">
        <?php
        foreach($weekTimeSlots as $key => $weekTimeSlot) {
            if($key == 'timeSlot') {
            ?>
            <tr>
                <th><?php echo $weekTimeSlot['label']; ?></th>
                <?php
                foreach($weekTimeSlot['slots'] as $date => $slots) {
                    ?>
                    <th slot-date="<?php echo $date; ?>" class="<?php echo ($date == date('Y-m-d')) ? 'today' : ''; ?>"><?php echo $slots['day'].' '.$slots['date']; ?></th>
                    <?php
                }
                ?>
            </tr>
            <?php 
            } else {
                ?>
                <tr>
                <?php
                foreach($weekTimeSlot as $date => $slots) {
                    ?>
                    <td time-slot="<?php echo $key;?>" slot-date="<?php echo $date; ?>" class="date_event <?php echo ($date == 'timeSlot') ? '' : 'slot-'.$slots['class'].'-color'; ?>"><?php echo ($date == 'timeSlot') ? $slots : $slots['count']; ?></td>
                    <?php
                }
                ?>
                </tr>
                <?php
            }
        }
        ?>
        </table>
    </div>
</div>
<div class="clearfix"></div>