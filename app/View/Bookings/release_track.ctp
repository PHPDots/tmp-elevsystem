<?php $this->append('script'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('input[type="checkbox"], input[type="radio"]').uniform();
    jQuery('.other_student').click(function() {
        var ele = jQuery(this).parents('.form-row');
        if(jQuery(this).is(':checked')) {
            <?php if($currentUser['User']['role'] != 'external_teacher') { ?>
                ele.find('.student_name').removeAttr('disabled');
            <?php } ?>
            ele.find('.addStudentBtn').show();
        } else {
            ele.find('.student_name').attr('disabled','disabled');
            ele.find('.addStudentBtn').hide();
        }
    });
    
    jQuery('.student_name').keyup(function() {
        if(jQuery(this).val() == '') {
            jQuery(this).parents('.form-row').find('.hidden_student_id').val('');
        }
    });
    jQuery('.student_name').autocomplete({
        minLength        : 2,
        select: function( event, ui ) {
            jQuery(this).parents('.form-row').find('.hidden_student_id').val(ui.item.sysvalue);
        },
        source      : function(request, response) {
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/student',
                dataType    : "json",
                complete    : function(){},
                beforeSend  : function(){},
                success     : function(data){
                    response(jQuery.map( data , function( item ) {
                        return {
                          label     : item.User.firstname+' '+item.User.lastname,
                          value     : item.User.firstname+' '+item.User.lastname,
                          sysvalue  : item.User.id,
                          email     : ' [ Email Id : ' + item.User.email_id +' ] ',
                          no        : ' [ Student No. : #' + item.User.student_number +' ] '
                        }
                    }));
                },
                error        : function() {
                    alert('Could not connect to server.');
                }
            });
        },
        open: function() {
            jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
        },
        close: function() {
            jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
        }
    });
    
    jQuery(document).delegate('#updateTrackBtn','click',function(){
        
        form    = jQuery(this).parents('form').attr('id');
        href    = jQuery(this).parents('form').attr('action');
        
        jQuery.ajax({
            url     : href,
            data    : jQuery('#'+form).serialize(),
            type    : 'POST',
            dataType: 'html',
            beforeSend: function(data) {
                jQuery('#formControlls').hide();
                jQuery('#submitForm').show();
            },
            success  : function(data) {
                jQuery('#exc').html(data);
            },
            complete: function(data) {
                jQuery('#submitForm').hide();
                jQuery('#formControlls').show();
            },
            error   : function() {
                alert('error');
            }
        });
    });
    
    jQuery(document).delegate('.unknownStudent','click',function() {
        var id = jQuery(this).attr('id');
        if(jQuery(this).is(':checked')) {
            jQuery('#'+id+'_ct').show();
        } else {
            jQuery('#'+id+'_ct').hide();
        }
    });
    jQuery('.teacherAutoSuggest').keyup(function() {
        if(jQuery(this).val() == '') {
            jQuery('.onBehalfTeacher').val('');
        }
    });
    jQuery(document).delegate('.teacherAutoSuggest','focusin',function() {
        var element         = jQuery(this);
        jQuery(this).autocomplete({
            minLength        : 2,
            select: function( event, ui ) {
                element.val(ui.item.value);
                jQuery('.onBehalfTeacher').val(ui.item.sysvalue);
            },
            source      : function(request, response) {
                jQuery.ajax({
                    url         : '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/teacher',
                    dataType    : "json",
                    complete    : function(){
                    },
                    beforeSend  : function(){
                    },
                    success     : function(data){
                        response(jQuery.map( data , function( item ) {
                            return {
                              label     : item.User.firstname+' '+item.User.lastname  + ' [ Email Id : ' + item.User.email_id +' ] ',
                              value     : item.User.firstname+' '+item.User.lastname,
                              sysvalue  : item.User.id,
                              role      : item.User.role
                            }
                        }));
                    },
                    error        : function() {
//                            alert('Could not connect to server.');
                    }
                });
            },
            open: function() {
                jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    });
});
</script>
<?php $this->end(); ?>
<div class="inner-content">   
    <div class="row-fluid"><div class="widget">
        <?php
        echo $this->Form->create('Booking',array(
            'class'         => 'form-horizontal',
            'autocomplete'  => 'false',
            'action'        => 'updateTrack'
        ));         
        ?>
        <div class="widget-header">
            <h5><?php echo __('Area - Track Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">
            <div class="form-row">
                <span class="span4">
                    <label class="span3"><b><?php echo __('Area '); ?>:</b></label>
                    <label class="span9"><?php echo Inflector::humanize($this->request->query['area']); ?></label>
                </span>
                <span class="span4">
                    <label class="span3"><b><?php echo __('Date'); ?>:</b></label>
                    <label class="span9"><?php echo date('d/m/Y',strtotime($this->request->query['date'])); ?></label>
                </span>
                <span class="span4">
                    <label class="span3"><b><?php echo __('Time Slot'); ?>:</b></label>
                    <label class="span9"><?php echo $this->request->query['time_slot']; ?></label>
                </span>
                <div class="clearfix"></div>
            </div>
            <div class="form-row">
                <label class="span1"><b><?php echo __('Track'); ?></b></label>
                <label class="span3"><b><?php echo __('Student'); ?></b></label>
                <label class="span2"><b><?php echo __('Driving School'); ?></b></label>
                <label class="span1 center"><b><?php echo __('Met'); ?></b></label>
                <label class="span1 center"><b><?php echo __('Not Met'); ?></b></label>
                <label class="span2 center"><b><?php echo __('Select Other Student'); ?></b></label>
            </div>
            <?php
            $i=0;
            if(!empty($tracks)) {
                foreach($tracks as $key => $track) {
                    if(isset($bookings[$key])) {
                        ++$i;
            ?>
            <div class="form-row">
                <label class="span1"><?php echo $track; ?></label>
                <span class="span3">
                    <?php 
                    echo $this->Form->hidden(($i-1).'.booking_id',array(
                        'value'     => (isset($bookings[$key])) ? $bookings[$key]['booking_id'] : '',
                    ));
                    echo $this->Form->hidden(($i-1).'.track_id',array(
                        'value'     => $key,
                    ));
                    echo $this->Form->hidden(($i-1).'.area',array(
                        'value'     => $this->request->query['area'],
                    ));
                    echo $this->Form->hidden(($i-1).'.date',array(
                        'value'     => $this->request->query['date'],
                    ));
                    echo $this->Form->hidden(($i-1).'.time_slot',array(
                        'value'     => $this->request->query['time_slot'],
                    ));
                    echo $this->Form->hidden(($i-1).'.student_id',array(
                        'class'     => 'hidden_student_id',                        
                    ));
                    echo $this->Form->hidden(($i-1).'.selected_student_id',array(
                        'value'     => (isset($bookings[$key])) ? $bookings[$key]['student_id'] : ''
                    ));
                    $val = '';
                    if(isset($bookings[$key])) {
                        if(isset($users[$bookings[$key]['student_id']])) {
                            $val = $users[$bookings[$key]['student_id']]['firstname'].' '.$users[$bookings[$key]['student_id']]['lastname'];
                        }
                    }
                    echo $this->Form->input(($i-1).'.student_name',array(
                        'value'     => $val,
                        'disabled'  => TRUE,
                        'label'     => FALSE,
                        'class'     => 'student_name',
                    ));
                    ?>
                    <div id="txt_student_id_<?php echo $i; ?>" class="error-message"></div>
                </span>
                <label class="span2"><?php echo (isset($drivingSchool[$users[$bookings[$key]['user_id']]['company_id']]))? $drivingSchool[$users[$bookings[$key]['user_id']]['company_id']] : ''; ?></label>
                <span class="span1 center">
                    <?php 
                        $args   = array(
                            'value'  => 1,
                            'id'     => 'met'.$i,
                            'class'  => 'met'
                        );
                        if(isset($bookings[$key]['release_track']) && ($bookings[$key]['release_track']) && ($bookings[$key]['status'] == 'met')){
                            $args['checked']    = 'checked';
                        }
                        echo $this->Form->checkbox(($i-1).'.status_met',$args); 
                    ?>
                    <div class="clearfix"></div>
                    <div id="txt_met_<?php echo $i; ?>" class="error-message"></div>
                </span>
                <span class="span1 center">
                    <?php 
                    $args   = array(
                        'value'  => 1,
                        'id'     => 'met'.$i,
                        'class'  => 'met'
                    );
                    if(isset($bookings[$key]['release_track']) && ($bookings[$key]['release_track']) && ($bookings[$key]['status'] == 'not_met')){
                        $args['checked']    = 'checked';
                    }
                    echo $this->Form->checkbox(($i-1).'.status_notMet',$args); 
                    ?>
                    <div class="clearfix"></div>
                    <div id="txt_not_met_<?php echo $i; ?>" class="error-message"></div>
                </span>
                <span class="span2 center">
                    <?php
                    $args   = array(
                        'value'     => '1',
                        'id'        => 'other_student'.$i,
                        'class'     => 'other_student'
                    );
                    if((empty($bookings[$key]['student_id']) || ($bookings[$key]['student_id'] == '-1')) && !empty($bookings[$key]['name']) && !empty($bookings[$key]['phone'])){
                        $args['checked']    = 'checked';
                    }
                    echo $this->Form->checkbox(($i-1).'.other_student',$args); 
                    ?>
                    <div id="txt_other_student_<?php echo $i; ?>" class="error-message"></div>
                </span>
                <span class="span2 center addStudentBtn" style="<?php echo ((empty($bookings[$key]['student_id']) || ($bookings[$key]['student_id'] == '-1')) && !empty($bookings[$key]['name']) && !empty($bookings[$key]['phone']))?'':'display:none;'; ?>">
                    <input type="checkbox" name="data[Booking][<?php echo ($i-1); ?>][unknown]" id="chk_unknown_<?php echo $i; ?>" value="1" 
                           class="uniform unknownStudent"  <?php echo ((empty($bookings[$key]['student_id']) || ($bookings[$key]['student_id'] == '-1')) && !empty($bookings[$key]['name']) && !empty($bookings[$key]['phone']))?'checked':''; ?>/>
                    <label for="chk_unknown_<?php echo $i; ?>"><?php echo __('Unknown Student'); ?></label>
                </span>
                <div class="clearfix"></div>
                <?php if((empty($bookings[$key]['student_id']) || ($bookings[$key]['student_id'] == '-1')) && !empty($bookings[$key]['name']) && !empty($bookings[$key]['phone'])){ ?>                
                <div id="chk_unknown_<?php echo $i; ?>_ct">
                    <div class="span6"></div>
                    <div class="span3">
                        <input type="text" class="span12" name="data[Booking][<?php echo ($i-1); ?>][name]"  
                               value="<?php echo $bookings[$key]['name']; ?>" placeHolder="<?php echo __('Enter Student Name'); ?>"/>
                        <div class="error-message" id="txt_booking_name_<?php echo $i; ?>"></div>
                    </div>
                    <div class="span3">
                        <input type="text" class="span12 no-margin" name="data[Booking][<?php echo ($i-1); ?>][phone]" 
                               value="<?php echo $bookings[$key]['phone']; ?>" placeHolder="<?php echo __('Enter Student Mobile Number'); ?>"/>
                        <div class="error-message" id="txt_booking_phone_<?php echo $i; ?>"></div>
                    </div>
                </div>  
                <?php } else { ?>
                <div id="chk_unknown_<?php echo $i; ?>_ct" style="display:none;">
                    <div class="span6"></div>
                    <div class="span3">
                        <input type="text" class="span12" name="data[Booking][<?php echo ($i-1); ?>][name]"  placeHolder="<?php echo __('Enter Student Name'); ?>"/>
                        <div class="error-message" id="txt_booking_name_<?php echo $i; ?>"></div>
                    </div>
                    <div class="span3">
                        <input type="text" class="span12 no-margin" name="data[Booking][<?php echo ($i-1); ?>][phone]" placeHolder="<?php echo __('Enter Student Mobile Number'); ?>"/>
                        <div class="error-message" id="txt_booking_phone_<?php echo $i; ?>"></div>
                    </div>
                </div>  
                <?php } ?>
            </div>            
            <?php 
                    }
                }
            }
            if(!empty($bookings)) {
            ?>
            <div class="form-row">
                <div class="span12">
                    <div class="span5">
                        <label class="span4"><?php echo __('Select Course'); ?></label>
                        <div class="span8">
                        <?php
                        echo $this->Form->select('course',$courses,array(
                            'empty' => __('Select Course'),
                            'class' => 'span12',
                            'value' => (isset($bookings['course'])) ? $bookings['course'] : '',
                        ));
                        ?>
                        </div>
                    </div>
                    <div class="span5">
                        <label class="span4"><?php echo __('Select Co-Teacher'); ?></label>
                        <div class="span8">
                        <?php
                        echo $this->Form->input('co_teacher_auto',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12 teacherAutoSuggest',
                            'placeHolder'   => __('Search Teacher'),
                            'value'         => (isset($bookings['co_teacher_auto']) && !is_null($bookings['co_teacher_auto'])) ? $bookings['co_teacher_auto'] : '',
                        ));
                        echo $this->Form->hidden('co_teacher',array(
                            'class'     => 'onBehalfTeacher',
                            'value'     => (isset($bookings['co_teacher_auto']) && !is_null($bookings['co_teacher'])) ? $bookings['co_teacher'] : '',
                        ));
                        echo $this->Form->hidden('id',array(
                            'value'     => (isset($bookings['co_teacher_auto']) && !is_null($bookings['id'])) ? $bookings['id'] : '',
                        ));
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="form-row">
            <div class="pull-left">
            <?php
            if(!empty($bookings)) {
                $btnName = __('Release');
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                    'class' => 'button button-green',
                    'type'  => 'button',
                    'id'    => 'updateTrackBtn',
                ),
                    array('escape' => FALSE)                            
                );
            }
            ?>
            </div>
            <div id="submitForm" class="fly_loading pull-left field" style="display:none"><?php  echo $this->Html->image("submit-form.gif");?></div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div></div>
</div>
