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
        $title = ($isEdit)?__('Edit Product'):__('Add Product');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-shopping-cart"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Product',array(
                'type'  => 'post',
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Product Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <div class="span6">
                    <label class="field-name"><?php echo __('Name'); ?>:</label>
                    <div class="field">
                        <?php 
                        echo $this->Form->input('name',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Enter Name')                            
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_name_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">
                    <label class="field-name"><?php echo __('Activity Number'); ?>:</label>
                     <div class="field">
                        <?php 
                        echo $this->Form->input('activity_number',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Enter Activity Number')                            
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_activity_number_error" class="error-message"></div>
                    </div>
                </div>
            </div>            
            <div class="form-row">                
                <div class="span6">
                    <label class="field-name"><?php echo __('Price'); ?>:</label>
                    <div class="field">
                        <?php 
                        echo $this->Form->input('price',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Price'),
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_price_error" class="error-message"></div>
                    </div>
                </div>

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
