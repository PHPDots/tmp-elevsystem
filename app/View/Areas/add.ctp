<script type="text/javascript">    
    jQuery(document).ready(function(){
        <?php if($isEdit == TRUE) {  ?>
            var counter = <?php echo (count($area['AreaTimeSlot'])== 0)?1:count($area['AreaTimeSlot']); ?>;
        <?php } else { ?>
            var counter = 1;
        <?php } ?>
        jQuery(document).delegate(".plus-sign",'click',function () {
            jQuery('#append_time_slot').append(jQuery('#new_time_slot').html().replace(/\%i\%/g, counter));
            jQuery(this).parent().append(jQuery('.minus_sign_div').html());
            jQuery(this).remove();
            counter++;
            
            jQuery('.timepicker').datetimepicker({
                datepicker:false,
                format:'H:i',
                allowTimes: ['0:00','0:30','1:00','1:30','2:00','2:30','3:00','3:30','4:00','4:30','5:00','5:30','6:00','6:30',
                    '7:00','7:30','8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30',
                    '14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30',
                    '21:00','21:30','22:00','22:30','23:00','23:30']
            });
        });
        jQuery(document).delegate(".minus-sign",'click',function () {
            jQuery(this).parent().next('.clearfix').remove();
            jQuery(this).parent().remove();
        });
        
        jQuery('.timepicker').datetimepicker({
            datepicker:false,
            format:'H:i',
            allowTimes: ['0:00','0:30','1:00','1:30','2:00','2:30','3:00','3:30','4:00','4:30','5:00','5:30','6:00','6:30',
                '7:00','7:30','8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30',
                '14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30',
                '21:00','21:30','22:00','22:30','23:00','23:30']
        });
    });
</script>
<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Area'):__('Add Area');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-globe"></i>'
        ));
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Area',array(
                'type'  => 'post',
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Area Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <label class="field-name"><?php echo __('Area Name'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('name',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Area'), 
                    ));
                    ?>
                    <div id="name" class="error-message"></div>
                    <div class="clearfix"></div>
                </div>
            </div>            
            <div class="form-row">
                <label class="field-name"><?php echo __('Area Slug'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('slug',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Slug'),
                    ));
                    ?>
                </div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('Address'); ?>:</label>
                <div class="field">
                    <?php 
                       echo $this->Form->input('address',array(
                           'type'          => 'text',
                           'label'         => false,
                           'div'           => null,
                           'class'         => 'span12',
                           'placeHolder'   => __('Address'),
                           ));
                   ?>
                    <div id="address" class="error-message"></div>
                </div>
            </div>
            <div class="form-row">
                <label class="field-name" for="color"><?php echo __('Color'); ?>:</label>
                <div class="field">
                    <span class="span6">
                    <?php 
                    $val    = (isset($this->data['Area']['color']) && !empty($this->data['Area']['color']))?$this->data['Area']['color']:'3f3f3f';
                    echo $this->Form->input('color',array(
                        'type'          => 'text',
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12 color',
                        'placeHolder'   => __('Color'),
                        'value'         => $val
                    ));
                    ?>
                    </span>
                </div>
            </div>          
            <div class="form-row">
                <label class="field-name"><?php echo __('Time Slot'); ?>:</label>
                <div class="span8" id="append_time_slot" >
                    <?php
                        if($isEdit) {
                            if(!empty($area['AreaTimeSlot'])){
                                for($i = 0; $i < count($area['AreaTimeSlot']); $i++) {                                   
                                    $val = explode('-',$area['AreaTimeSlot'][$i]['time_slots']);
                                       echo "<div class = 'time_slot'>";
                                        echo $this->Form->input('AreaTimeSlot.'.($i).'.time_slots.',array(
                                            'type'          => 'text',
                                            'label'         => false,
                                            'div'           => null,
                                            'class'         => 'timepicker',
                                            'placeHolder'   => __('Time Slot'),
                                            'required'      => FALSE,
                                            'value'         => $val[0]
                                        ));
                                        echo $this->Form->input('AreaTimeSlot.'.($i).'.time_slots.',array(
                                            'type'          => 'text',
                                            'label'         => false,
                                            'div'           => null,
                                            'class'         => 'timepicker endTime',                        
                                            'placeHolder'   => __('End Time'),
                                            'required'      => FALSE,
                                            'value'         => $val[1]
                                        ));
                                        if($i != (count($area['AreaTimeSlot']) - 1)){
                                            echo $this->Html->image('minus_contact.png',array(
                                                'alt'   => 'Remove City',
                                                'class' => 'minus-sign'
                                            ));
                                        }else{
                                            echo $this->Html->image('plus_contact.png',array(
                                                'alt' => 'Add City',
                                                'class' => 'plus-sign plus-css',
                                            ));  
                                        }

                                    echo "</div>";
                                }
                            }else{
                                 echo "<div class = 'time_slot'>";
                                 echo $this->Form->input('AreaTimeSlot.0.time_slots.',array(
                                    'type'          => 'text',
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'timepicker',
                                    'placeHolder'   => __('Start Time'),
                                ));
                                echo $this->Form->input('AreaTimeSlot.0.time_slots.',array(
                                    'type'          => 'text',
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'timepicker endTime',                        
                                    'placeHolder'   => __('End Time'),
                                ));
                                echo $this->Html->image('plus_contact.png',array(
                                    'alt' => 'Add City',
                                    'class' => 'plus-sign plus-css',
                                ));  
                                echo "</div>";
                            }                            
                        }
                    ?>
                    <div class="time_slot ">
                    <?php 
                        if(!$isEdit) {
                            echo $this->Form->input('AreaTimeSlot.0.time_slots.',array(
                                'type'          => 'text',
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'timepicker',
                                'placeHolder'   => __('Start Time'),
                            ));
                            echo $this->Form->input('AreaTimeSlot.0.time_slots.',array(
                                'type'          => 'text',
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'timepicker endTime',                        
                                'placeHolder'   => __('End Time'),
                            ));
                            echo $this->Html->image('plus_contact.png',array(
                                'alt' => 'Add City',
                                'class' => 'plus-sign plus-css',
                            ));  
                        }
                    ?>
                    </div>
                </div>
                <div id="time_slot" class="error-message span6 no-margin"></div>
                <div class ="clearfix"></div>
            </div>
            
            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?php
            $btnName = ($isEdit)?'Update':'Add';               
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,
                array(
                    'class' => 'button button-green',
                    'type'  => 'button',
                    'id'    => 'formSubmit'
                ),
                array('escape' => FALSE)                            
            );
            ?>           
            <?php
            echo $this->Html->link('<i class="icon-remove icon-white"></i> Cancel',
                array('action' => 'index'),
                array(
                    'class' => 'button button-red',
                    'escape' => FALSE,
            ));
            ?>
            </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div></div>
</div>

<div id="new_time_slot" style="display: none">
    <div class="time_slot">
        <?php 
        echo $this->Form->input('AreaTimeSlot.%i%.time_slots.',array(
            'type'          => 'text',
            'label'         => false,
            'div'           => null,
            'class'         => 'timepicker',
            'required'      => FALSE,
            'placeHolder'   => __('Start Time'),
        ));
        echo $this->Form->input('AreaTimeSlot.%i%.time_slots.',array(
            'type'          => 'text',
            'label'         => false,
            'div'           => null,
            'class'         => 'timepicker endTime',
            'required'      => FALSE,
            'placeHolder'   => __('End Time'),
        ));
        echo $this->Html->image('plus_contact.png',array(
            'alt' => 'Add City',
            'class' => 'plus-sign plus-css',
        ));
        ?>
    </div>
</div>
<div class="minus_sign_div" style="display:none">
<?php
echo $this->Html->image('minus_contact.png',array(
    'alt'   => 'Remove City',
    'class' => 'minus-sign no-margin no-padding'
));
?>
</div>