<?php $this->append('script'); ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#priceType').change(function() {
            if(jQuery(this).val() == 'area'){               
                jQuery('.price_area').show();       
                jQuery('.price_area').parent().find('.price_field').removeClass('no-margin');
            }else{                
                jQuery('.price_area').hide();       
                jQuery('.price_area').parent().find('.price_field').addClass('no-margin');
            }            
        });
        
        jQuery('#priceType').trigger('change');
        
    });
</script>
<?php $this->end(); ?>
<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Activity Number'):__('Add Activity Number');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-money"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
        echo $this->Form->create('ActivityNumber',array(
            'type'  => 'post',
            'class' => 'form-horizontal'
        ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Activity Number Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">                
                <div class="span6">
                    <label class="field-name"><?php echo __('Select Type of Activity Number'); ?>:</label>
                    <div class="field">
                        <?php 
                            $priceType  = Configure::read('priceType');
                            echo $this->Form->select('type',$priceType,array(
                                'label' => false,
                                'div'   => null,
                                'class' => 'span12',
                                'empty' => __('Select Type'),
                                'id'    => 'priceType'
                            ));                            
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_type_error" class="error-message"></div>                        
                    </div>
                </div>
                <div class="span6 price_area"  style="display: none;" >     
                    <label class="field-name"><?php echo __('Area'); ?>:</label>
                    <div class="field">
                        <?php 
                        echo $this->Form->select('area',$areaListArr,array(
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
            </div>            
            <div class="form-row">                
                <div class="span6 price_area"  style="display: none;">
                    <label class="field-name"><?php echo __('Status'); ?>:</label>
                    <div class="field"> 
                        <div class="span3">
                            <?php 
                            echo $this->Form->radio('status',array(
                                'met'           => __('Met'),                                    
                            ),array(
                                'id'            => 'met',
                                'class'         => 'met',
                                'hiddenField'   => FALSE,
                            ));
                            ?>
                        </div>      
                        <div class="span3">
                            <?php 
                            echo $this->Form->radio('status',array(
                                'not_met'         => __('Not Met'),                                   
                            ),array(
                                'id'            => 'not_met',
                                'class'         => 'not_met',
                                'hiddenField'   => FALSE,
                            ));
                            ?>
                        </div>                                            
                        <div class="clearfix"></div>
                        <div id="txt_status_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6 price_field no-margin">
                    <label class="field-name"><?php echo __('Activity Number'); ?>:</label>
                    <div class="field">
                        <?php 
                        echo $this->Form->input('activity_number',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Activity Number'),
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_activity_number_error" class="error-message"></div>
                    </div>
                </div>
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
