<div class="span6 no-margin"><div class="widget">
    <div class="widget-header">            
        <h5>
            <?php echo __('Receipt Search')?>
        </h5>            
    </div>
    <?php 
            echo $this->Form->create('Family',array(
                'class' => 'form-horizontal',
                'url'   => array(
                    'controller'    => 'receipts',
                    'action'        => 'index',    
                ), 
                'type' => 'GET'
            ));
        ?>
        <div class="form-row">
            <label class="field-name" for="lastnmae"><?php echo __('Receipt ID'); ?>:</label>
            <div class="field">
                <?php                    
                    echo $this->Form->input(
                        'receiptId',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Receipt ID'),                                 
                            'type'          => 'text'
                    ));
                ?>
            </div>
        </div>        
        <div class="form-row">
            <div class="field">
                <?php
                    echo $this->Form->button('<i class="icon-search icon-white"></i> '.__('Search'),array(
                           'class' => 'button button-green',
                           'type'  => 'submit',                           
                       ),
                           array('escape' => FALSE)                            
                       );

                   ?>
            </div>
        </div>
    <?php echo $this->Form->end();?>
</div></div>
