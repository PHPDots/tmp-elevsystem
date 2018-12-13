<?php 
    $this->append('script');
?>
<script type="text/javascript">   
jQuery(document).ready(function(){
    
    jQuery('body').delegate('.tag','click',function(){
        
        element = jQuery(this);
        input   = element.parents('.field').first().find('.mailmetatext');
        input.val(input.val() + ' %' + element.attr('key') + '%');
        if(input.hasClass('cleditor')){
            input.cleditor()[0].updateFrame();
        }
    });
    
    jQuery('#sms_templates').change(function() {
        emailtemplatetype = jQuery(this).val();        
        
        jQuery.ajax({
            url         : '<?php echo $this->Html->url(array('action'=>'smsMeta',)) ?>' + '/' + jQuery(this).val() ,
            dataType    : 'json',
            beforeSend  : function(){
                
            },
            success     : function(data){
                if(data.length==0){
                    return ;
                }
                
                jQuery('.tag').remove();
                jQuery('#templatemetalist').html('');
                for(i=0;i<data.length;i++){
                    if(data[i].hint === undefined)
                        data[i].hint = '';
                    jQuery('#templatemetalist').append('<dt>%' + data[i].key + '%</dt><dd>' + data[i].name + ' - ' + data[i].hint + '</dd>')
                    jQuery('.tagsinput').append('<span class="tag clickable" key=' + data[i].key  +'><span>&nbsp;' + data[i].name + '&nbsp;</span></span>');
                }
            },
            complete    : function(){
                
            }
            
        });
    }).trigger('change');
    
});
</script> 
<?php 
    $this->end(); 
?>
<div class="inner-content">
    <?php $this->Html->pageInnerTitle(__('Add SMS template')); ?>
    <div class="row-fluid">
    <div class="widget">
        <?php
            echo $this->Form->create('SmsTemplate',array(
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="form-row">
            <label class="field-name"><?php echo __('Sms Template Type'); ?>:</label>
            <div class="field">
            <?php
                echo $this->Form->select('template',$SmsTemplates,array(
                    'id'        => 'sms_templates',
                    'empty'     => __('Select SMS Template')
                ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Body'); ?>:</label>
            <div class="field">
                <?php 
                echo $this->Form->textarea('body',array(
                    'label'         => false,
                    'div'           => null,
                    'class'         => 'span12 cleditor mailmetatext',
                    'rows'          => 10,
                    'placeHolder'   => __('SMS Boby')
                ));
                ?>
            </div>
        </div>
        <div class="form-row">                
            <div class="field">
            <?php
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.__(' Save'),array(
                'class'         => 'button button-blue',
                'type'          => 'submit',
            ),array(
                'escape' => FALSE
            ));
            echo $this->Html->link('<i class="icon-remove icon-white"></i>'.__(' Cancel'),
            array('action' => 'index'),
            array(
                    'class'     => 'button button-red',
                    'escape'    => FALSE,
            ));
            ?>
            </div>
        </div>
        <div class="clearfix">&nbsp;</div>
    </div>
    </div>
</div>

