<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(document).delegate('#editBookingDetails','click',function(){            
            form        = jQuery(this).parents('form').attr('id');
            var date    = '<?php echo $this->request->query['date']; ?>';
            var area    = '<?php echo $this->request->query['area']; ?>';
            var time    = '<?php echo $this->request->query['time']; ?>';
            jQuery.ajax({
                url     : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'editBookings')); ?>',
                data    : jQuery('#'+form).serialize()+'&date='+date+'&area='+area+'&time='+time,
                type    : 'POST',
                dataType: 'html',
                beforeSend: function(data){
                    jQuery('#formControlls').hide();
                    jQuery('#submitForm').show();
                },
                success  : function(data){                           
                    jQuery('#exc').html(data);
                },
                complete: function(data){
                    jQuery('#submitForm').hide();
                    jQuery('#formControlls').show();
                },
                error   : function(){
                    alert('error');
                }
            });               
        });
    });
</script>
<div class="form-row">
    <div class="span12">
        <label class="span3"><?php echo __('Would You like to Add a Note?'); ?>:</label>
        <div class="span9">
            <?php
                $args   = array(
                    'id'    => 'longDescriptionChk'
                );

                if(!empty($booking['Booking']['full_description'])){
                    $args['checked']    = 'checked';
                }
                echo $this->Form->checkbox('long_description',$args);
            ?>
        </div>
    </div>
</div>
<div class="form-row longDescription"  id="longDescription" style="<?php echo (empty($booking['Booking']['full_description']))?'display:none':'display:block';?>">                                
    <label class="span3"><?php echo __('Long Description'); ?>:</label>
    <div class="span9">
        <?php 
            echo $this->Form->input('Booking.full_description',array(
                'type'          => 'textarea',
                'label'         => false,
                'div'           => null,
                'class'         => 'span12',
                'placeHolder'   => __('Enter Long Description'),
                'value'         => $booking['Booking']['full_description']
            ));
        ?>
    </div>                                
</div>
<?php if(in_array($currentUser['User']['role'],array('admin','internal_teacher'))){ ?>
<div class="form-row">
    <div class="span4">
        <label class="span9" id="onBehalfLabel"><?php echo __('Change Teacher'); ?>:</label>
        <div class="span2">
            <?php
                $args   = array(
                    'id'    => 'onBehalf',                          
                );                        
                if(isset($booking) && $booking['Booking']['on_behalf']){
                    $args['checked']    = 'checked';
                }
                echo $this->Form->checkbox('Booking.on_behalf',$args);
            ?>
        </div>
    </div>
    <div class="offset1 span7 bookingTeacher" id="bookingTeacher" style="display: <?php echo (isset($booking) && $booking['Booking']['on_behalf'])?'block':'none'?>;">
        <label class="span2"><?php // echo __('Select Teacher '); ?>:</label>
        <div class="span10">
            <?php
                 echo $this->Form->input(
                    'teacher',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12 teacherAutoSuggest',
                        'placeHolder'   => __('Search Teacher'),     
                        'value'         => (isset($booking) && $booking['Booking']['on_behalf'])?$modifiedUsers[$booking['Booking']['user_id']]['name']:''
                ));
            ?>
            <?php 
                echo $this->Form->hidden('Booking.user_id',array(
                    'class'     => 'onBehalfTeacher',
                    'value'     => (isset($booking) && $booking['Booking']['on_behalf'])?$booking['Booking']['user_id']:''
                )); 
            ?>
        </div>
    </div>
</div>
<?php } ?>
<div class="form-row">            
    <div class="span12" >
        <label class="span3"><?php echo __('Select Co Teacher '); ?>:</label>
        <div class="span9">
            <?php
                 echo $this->Form->input(
                    'co_teacher_auto',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span6 teacherAutoSuggest',
                        'placeHolder'   => __('Search Teacher'),     
                        'value'         => (isset($booking) && !is_null($booking['Booking']['co_teacher']))?$modifiedUsers[$booking['Booking']['co_teacher']]['name']:''
                ));
            ?>
            <?php 
                echo $this->Form->hidden('Booking.co_teacher',array(
                    'class'     => 'onBehalfTeacher',
                    'value'     => (isset($booking) && !is_null($booking['Booking']['co_teacher']))?$booking['Booking']['co_teacher']:''
                )); 
            ?>
        </div>
    </div>
</div>
<div class="form-row">            
    <div class="span12" >
        <label class="span3"><?php echo __('Select Teacher Course'); ?>:</label>
        <div class="span9">
            <?php
                 echo $this->Form->select('Booking.course',$courses,array(
                    'label' => false,
                    'div'   => null,
                    'class' => 'span6',
                    'empty' => __('Select Course'),
                    'value' => (isset($booking) && !is_null($booking['Booking']['course']))?$booking['Booking']['course']:''
                ));
            ?>                           
        </div>
    </div>
</div>
<div class="form-row">                
    <div id="submitForm" class="fly_loading" style="display:none;"><?php  echo $this->Html->image("submit-form.gif");?></div>
    <div class="field" id="formControlls">
        <?php
            $btnName = __('Add');               
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                'class' => 'button button-green',
                'type'  => 'button',
                'id'    => 'editBookingDetails'
            ),
                array('escape' => FALSE)                            
            );
        ?>           
        <?php 
            echo $this->Form->button('<i class="icon-remove icon-white"></i> '.__('Cancel'),array(
                'class' => 'button button-red',
                'type'  => 'button',
                'id'    => 'cancelForm'
            ),
                array('escape' => FALSE)                            
            );
        ?>             
    </div>
</div>
