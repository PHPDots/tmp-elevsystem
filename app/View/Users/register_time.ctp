<div class="row">
    <h3 class="col-xs-12"><?php echo __('Your Registrer Time Details'); ?></h3>
    <div class="col-xs-12 col-sm-4">
        <div class="form-group">
            <label class="col-xs-5"><?php echo __('Type'); ?></label>
            <label class="col-xs-7">:&nbsp;<?php echo Inflector::humanize($registertime['TeacherRegisterTime']['type']);?></label>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-xs-5"><?php echo __('Date from'); ?></label>
            <label class="col-xs-7">:&nbsp;<?php echo date('d.m.Y',strtotime($registertime['TeacherRegisterTime']['from']));?></label>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-8">
        <?php if($registertime['TeacherRegisterTime']['type'] == 'theory'){ ?>
        <div class="form-group">
            <label class="col-xs-3"><?php echo __('City'); ?>:</label>
            <label class="col-xs-9">:&nbsp;<?php echo $cities[$registertime['TeacherRegisterTime']['city']]; ?></label>
            <div class="clearfix"></div>
        </div>
        <?php } else if($registertime['TeacherRegisterTime']['type'] == 'driving') { ?>
         <div class="form-group">
            <label class="col-xs-3"><?php echo __('Driving Type'); ?>:</label>
            <label class="col-xs-9">:&nbsp;<?php echo (!empty($registertime['TeacherRegisterTime']['driving_type'])) ? $drivingTypes[$registertime['TeacherRegisterTime']['driving_type']] : __('N/A'); ?></label>
            <div class="clearfix"></div>
        </div>   
        <?php } else { ?>
        <div class="form-group">
            <label class="col-xs-3"><?php echo __('Purpose'); ?></label>
            <label class="col-xs-9">:&nbsp;<?php echo $registertime['TeacherRegisterTime']['purpose']; ?></label>
            <div class="clearfix"></div>
        </div>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>
