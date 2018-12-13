<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Service'):__('Add Service');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-users"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Service',array(
                'type'  => 'post',
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Service Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <label class="field-name"><?php echo __('Service Name'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('name',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Service'), 
                    ));
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>            
            <div class="form-row">
                <label class="field-name"><?php echo __('Service Code'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('code',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Service Code'), 
                    ));
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div> 
            <div class="form-row">
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
                </div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('City'); ?>:</label>
                <div class="field">
                   <?php 
                        $val = isset($service['city_id']) ? $service['city_id'] : '';
                        echo $this->Form->select('city_id',$city,array(
                            'label'     => false,
                            'multiple' => true,
                            'selected'  => $val,
                            'div'       => null,
                            'class'     => 'span12',
                            'value'     => $val,
                            'style'     => 'height:100px;'
                        ));
                        
                        ?>
                    <div class="clearfix"></div>
                </div>
            </div> 

            <div class="form-row">
                <label class="field-name"><?php echo __('Category'); ?>:</label>
                <div class="field">
                   <?php 
                        $val = isset($service['category_id']) ? $service['category_id'] : '';
                        echo $this->Form->select('category_id',$category,array(
                            'label'     => false,
                            'div'       => null,
                            'class'     => 'span12',
                            'empty'     => __('Select Category'),
                            'value'     => $val,
                        ));
                        
                        ?>
                    <div class="clearfix"></div>
                </div>
            </div> 
            
            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?PHP
                $btnName = ($isEdit)?'Update':'Add';               
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                    'class' => 'button button-green',
                    'type'  => 'submit',
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
