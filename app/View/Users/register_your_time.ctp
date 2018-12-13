<?php $this->append('script'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.typeObject').click(function(){
            jQuery('.typeCt').hide();
            jQuery('#'+jQuery(this).val()+'Ct').show();
            jQuery('#'+jQuery(this).val()+'Ct').find('.datetimepicker').datetimepicker({
                format:'d.m.Y H:i' ,               
                changeMonth: true,
                changeYear: true
            });
        });
        
        jQuery('.typeObject:checked').trigger('click');
    });
</script>
<?php $this->end(); ?>
<div class="row">
    <h3><?php echo __('Register Time'); ?></h3>
    <?php
        echo $this->Form->create('TeacherRegisterTime',array(
            'class' => 'form-horizontal'
        ));
    ?>
    <div class="form-group">
        <label class="col-xs-3"><?php echo __('Type'); ?></label>
        <div class="col-xs-9">
            <label>
                <?php 
                    echo $this->Form->radio('type',array(
                        'theory'    => __('Theory'),
                        'driving'   => __('Driving'),
                        'other'     => __('Other')
                    ),array(                    
                        'label'         => TRUE,
                        'div'           => TRUE,
                        'class'         => 'typeObject css-radio',
                        'legend'        => FALSE, 
                        'hiddenField'   => FALSE,
                        'value'         => ($isEdit)?$registertime['TeacherRegisterTime']['type']:'theory'
                     ));
                ?>
            </label>
        </div>
    </div>
    
    <div id="theoryCt" class="typeCt" style="display:none;">
        <div class="form-group">            
            <label class="col-xs-3"><?php echo __('City'); ?></label>
            <div class="col-xs-9">
                <div class="col-xs-5 no-padding">
                <?php 
                    echo $this->Form->select('city',$cities,array(
                        'label' => false,
                        'div'   => null,
                        'class' => 'form-control',
                        'empty' => __('Select City'),    
                        'value' => ($isEdit)?$registertime['TeacherRegisterTime']['city']:''
                    ));
                ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-offset-3 col-xs-9 error-message" id="txt_theory_city"></div>
        </div>
        <div class="form-group">
            <label class="col-xs-3"><?php echo __('Enter Date'); ?></label>
            <div class="col-xs-9">
                <div class="col-xs-5 no-padding">
                    <?php    
                    echo $this->Form->input('TeacherRegisterTime.theory.from',array(
                        'type'          => 'text',
                        'div'           => null,
                        'label'         => false,                           
                        'class'         => 'form-control datepicker',
                        'placeHolder'   => __('Enter Date'),
                        'value'         => ($isEdit)?date('d.m.Y',strtotime($registertime['TeacherRegisterTime']['from'])):'',
                        'minYear'       => 0,
                        'maxYear'       => 20,
                   ));
                    ?>
                </div>                 
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-offset-3 col-xs-9 error-message" id="txt_theory_time"></div>
        </div>
    </div>
    
    <div id="drivingCt" class="typeCt" style="display:none;">
        <div class="form-group">            
            <label class="col-xs-3"><?php echo __('Driving Type'); ?></label>
            <div class="col-xs-9">
                <div class="col-xs-5 no-padding ">
                <?php 
                echo $this->Form->select('driving_type',$drivingTypes,array(
                    'label' => false,
                    'div'   => null,
                    'class' => 'form-control',
                    'empty' => __('Select Driving Type'),
                    'value' => ($isEdit) ? $registertime['TeacherRegisterTime']['city'] : ''
                ));
                ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-offset-3 col-xs-9 error-message" id="txt_driving_driving_type"></div>
        </div>
        <div class="form-group">
            <label class="col-xs-3"><?php echo __('Enter Date'); ?></label>
            <div class="col-xs-9">
                <div class="col-xs-5 no-padding ">
                    <?php    
                    echo $this->Form->input('TeacherRegisterTime.driving.from',array(
                        'type'          => 'text',
                        'div'           => null,
                        'label'         => false,                           
                        'class'         => 'form-control datepicker',
                        'placeHolder'   => __('Enter Date'),
                        'value'         => ($isEdit)?date('d.m.Y',strtotime($registertime['TeacherRegisterTime']['from'])):'',
                        'minYear'       => 0,
                        'maxYear'       => 20,
                   ));
                    ?>
                </div>                 
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-offset-3 col-xs-9 error-message" id="txt_driving_time"></div>
        </div>
    </div>
    
    <div id="otherCt" class="typeCt" style="display:none;">
        <div class="form-group">
            <label class="col-xs-3"><?php echo __('Purpose'); ?></label>
            <div class="col-xs-9">
                <?php 
                    echo $this->Form->input('purpose',array(
                        'type'          => 'textarea',
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'form-control',
                        'placeHolder'   => __('Enter Purpose of Time'),  
                        'value'         => ($isEdit)?$registertime['TeacherRegisterTime']['purpose']:''
                    ));
                ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-offset-3 col-xs-9 error-message" id="txt_other_purpose"></div>
        </div>
        <div class="form-group">
            <label class="col-xs-3"><?php echo __('Enter Date'); ?></label>
            <div class="col-xs-9">
                <div class="col-xs-5 no-padding ">
                    <?php    
                    echo $this->Form->input('TeacherRegisterTime.other.from',array(
                        'type'          => 'text',
                        'div'           => null,
                        'label'         => false,                           
                        'class'         => 'form-control datepicker',
                        'placeHolder'   => __('Enter Date'),
                        'value'         => ($isEdit) ? date('d.m.Y',strtotime($registertime['TeacherRegisterTime']['from'])) : '',
                        'minYear'       => 0,
                        'maxYear'       => 20
                   ));
                  ?>
                </div>                
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-offset-3 col-xs-9 error-message" id="txt_other_time"></div>
        </div>        
    </div>
    <div class="form_btns col-xs-12">                
        <div id="submitForm" class="detailsLoading"><?php  echo $this->Html->image("submit-form.gif");?></div>
        <div class="field" id="formControlls">
            <?php
            $btnName = ($isEdit)?__('Edit'):__('Add');               
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                'class' => 'btn btn-success',
                'type'  => 'button',
                'id'    => 'formSubmit'
            ),
                array('escape' => FALSE)                            
            );
            ?>           
            <?php 
            echo $this->Html->link('<i class="icon-remove icon-white"></i> '.__('Cancel'),array(
                'controller'    => 'users',
                'action'        => 'registerTimeList'
            ),array(
                'class'     => 'btn btn-danger',
                'escape'    => FALSE                
            ));            
            ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
