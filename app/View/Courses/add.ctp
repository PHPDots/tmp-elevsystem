<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Course'):__('Add Course');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-file-text"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
        echo $this->Form->create('Course',array(
            'type'  => 'post',
            'class' => 'form-horizontal'
        ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Course Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            <div class="form-row">                
                <div class="span6">
                    <label class="field-name"><?php echo __('Course'); ?>:</label>
                    <div class="field">
                        <?php 
                            echo $this->Form->input('name',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Enter Course Name'),
                            )); 
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_name_error" class="error-message"></div>                        
                    </div>
                </div>
                <div class="span6">     
                    <label class="field-name"><?php echo __('Price'); ?>:</label>
                    <div class="field">
                        <div class="input-group">
                           <?php 
                                echo $this->Form->input('price',array(
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'span12',
                                    'placeHolder'   => __('Enter Price'),
                                )); 
                            ?>
                            <div class="input-group-addon">DKK</div>
                        </div>                        
                        <div class="clearfix"></div>
                        <div id="txt_price_error" class="error-message"></div>                        
                    </div>
                </div>
            </div>            
            <div class="form-row">                
                <div class="span6">
                    <label class="field-name"><?php echo __('Teacher Time'); ?>:</label>
                    <div class="field">
                        <?php 
                             echo $this->Form->input('teacher_time',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Enter Teacher Time in Minutes'),
                            ));                             
                        ?>                      
                        <div class="clearfix"></div>
                        <div id="txt_teacher_time_error" class="error-message"></div>                        
                    </div>
                </div>
                <div class="span6">     
                    <label class="field-name"><?php echo __('Activity Number'); ?>:</label>
                    <div class="field">
                        <?php 
                            echo $this->Form->input('activity_number',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Enter Activity Number'),
                            )); 
                        ?>            
                        <div class="clearfix"></div>
                        <div id="txt_activity_number_error" class="error-message"></div>                        
                    </div>
                </div>
            </div>            
            <div class="form-row">                                
                <div class="span6">     
                    <label class="field-name"><?php echo __('Area'); ?>:</label>
                    <div class="field">
                        <?php 
                            echo $this->Form->select('area',$areas,array(
                                'label' => false,
                                'div'   => null,
                                'class' => 'span12',
                                'empty' => __('Select Area'),
                            ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_area_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">     
                    <label class="field-name"><?php echo __('Preselected Course'); ?>:</label>
                    <div class="field">
                        <?php 
                            $args   = array(
                                'value'     => '1',                               
                            );                           
                            echo $this->Form->checkbox('pre_selected',$args); 
                        ?>
                        <div class="clearfix"></div>                        
                    </div>
                </div>
                <div class="clearfix"></div>                 
            </div>                 
            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?PHP
                $btnName = ($isEdit)?'Update':'Add';               
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                    'class' => 'button button-green',
                    'type'  => 'button',
                    'id'    => 'formSubmit'
                ),
                    array('escape' => FALSE)                            
                );
            ?>           
            <?PHP
                echo $this->Html->link(
                            '<i class="icon-remove icon-white"></i> Cancel',
                            array('action' => 'index'),
                            array('class' => 'button button-red',
                                'escape' => FALSE,)
                );
            ?>
            </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div></div>
</div>
