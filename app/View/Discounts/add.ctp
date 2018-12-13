<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Discount'):__('Add Discount');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-rupee"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
        echo $this->Form->create('Discount',array(
            'type'  => 'post',
            'class' => 'form-horizontal'
        ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Discount Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">
            <div class="form-row">
                <div class="span6">
                    <label class="field-name"><?php echo __('From Date'); ?>:</label>
                    <div class="field">
                        <?php 
                        echo $this->Form->input('from_date',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12 datepicker',
                            'placeHolder'   => __('From Date'),
                            'minYear'       => 0,
                            'maxYear'       => 20
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_from_date_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">
                    <label class="field-name"><?php echo __('City'); ?>:</label>
                    <div class="field">
                        <?php 
                        echo $this->Form->select('city',$cities,array(
                            'label' => false,
                            'div'   => null,
                            'class' => 'span12',
                            'empty' => __('Select City'),
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_city_error" class="error-message"></div>
                    </div>
                </div>
            </div>            
            <div class="form-row">
                <div class="span6">
                    <label class="field-name"><?php echo __('Discount'); ?>:</label>
                    <div class="field">
                        <div class="input-group">
                            <?php 
                            echo $this->Form->input('discount',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12 textbox',
                                'placeHolder'   => __('Discount'),
                            ));
                            ?>
                            <div class="input-group-addon">DKK</div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="txt_discount_error" class="error-message"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?php
            $btnName = ($isEdit)?'Update':'Add';               
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                'class' => 'button button-green',
                'type'  => 'button',
                'id'    => 'formSubmit'
            ),
                array('escape' => FALSE)                            
            );
            ?>           
            <?php
            echo $this->Html->link('<i class="icon-remove icon-white"></i> Cancel',array(
                    'action' => 'index'
                ),array(
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
