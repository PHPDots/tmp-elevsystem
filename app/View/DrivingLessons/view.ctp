<?php 
$types      = Configure::read('bookingType');
$lessonTime = Configure::read('lessonTime');
$role       = CakeSession::read("Auth.User.role");
$url = str_replace("/elev-admin/", "", $this->here);
if((strpos($url, 'admin') != '' || $role != 'student') && $url != ''){
?>
<div class="inner-content"><div class="row-fluid"><div class="user-profile row-fluid">
    <div class="widget">
        <div class="widget-header"><h5><?PHP echo __('Driving Lesson Details'); ?></h5></div>
        <div class="slide">
            <div class="widget-content">
                <div class="span12">
                    <div class="user-line">
                        <label><?PHP echo __('Booking Type'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo $types[$drivingLesson['DrivingLesson']['type']]; ?></label>
                        
                    </div>
                    <div class="user-line">
                        <div class="span6">
                            <label><?php echo __('Student Name')?></label>
                            <label>:&nbsp;&nbsp;<?php echo $users[$drivingLesson['DrivingLesson']['student_id']]['firstname'].' '.$users[$drivingLesson['DrivingLesson']['student_id']]['lastname']; ?></label>
                        </div>
                        <div class="span6">
                            <label><?php echo __('Teacher Name')?></label>
                            <label>:&nbsp;&nbsp;<?php echo $users[$drivingLesson['DrivingLesson']['teacher_id']]['firstname'].' '.$users[$drivingLesson['DrivingLesson']['teacher_id']]['lastname']; ?></label>
                        </div>
                    </div>
                    <div class="user-line">
                        <div class="span6">
                            <label><?php echo __('Lesson Start Time')?></label>
                            <label>:&nbsp;&nbsp;<?php echo $drivingLesson['DrivingLesson']['start_time']; ?></label>
                        </div>
                        <div class="span6">
                            <label><?php echo __('Lesson Time')?></label>
                            <label>:&nbsp;&nbsp;<?php echo $lessonTime[$drivingLesson['DrivingLesson']['lesson_time']]; ?></label>
                        </div>
                    </div>
                    <div class="user-line">
                        <label><?php echo __('Module')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $drivingLesson['DrivingLesson']['module']; ?></label>
                    </div>
                    <div class="user-line">
                        <label><?php echo __('Comments')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $drivingLesson['DrivingLesson']['comments']; ?></label>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div></div></div>

<?php }else{ ?>
<div class="row">
    <h3 class="col-xs-12"><?php echo __('Driving Lesson Details'); ?></h3>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Type'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $types[$drivingLesson['DrivingLesson']['type']];?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Student Name'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $users[$drivingLesson['DrivingLesson']['student_id']]['firstname'].' '.$users[$drivingLesson['DrivingLesson']['student_id']]['lastname']; ?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Teacher Name'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $users[$drivingLesson['DrivingLesson']['teacher_id']]['firstname'].' '.$users[$drivingLesson['DrivingLesson']['teacher_id']]['lastname']; ?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Lesson Start Time'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $drivingLesson['DrivingLesson']['start_time']; ?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Lesson Time'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $lessonTime[$drivingLesson['DrivingLesson']['lesson_time']]; ?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Module'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $drivingLesson['DrivingLesson']['module']; ?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2"><?php echo __('Comments'); ?></label>
            <label class="col-xs-10">:&nbsp;<?php echo $drivingLesson['DrivingLesson']['comments']; ?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>


<?php } ?>