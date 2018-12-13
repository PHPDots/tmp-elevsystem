<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Category'):__('Add Category');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-users"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Category',array(
                'type'  => 'post',
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Category Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <label class="field-name"><?php echo __('Category Code'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('category_code',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Category Code'), 
                    ));
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div> 
            <div class="form-row">
                <label class="field-name"><?php echo __('Category Name'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('name',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Category'), 
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
