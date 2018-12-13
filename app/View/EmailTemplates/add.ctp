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
    
    jQuery('#email_templates').change(function(){
        emailtemplatetype = jQuery(this).val();        
        
        jQuery.ajax({
            url         : '<?php echo $this->Html->url(array('action'=>'loadMeta',)) ?>' + '/' + jQuery(this).val() ,
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
    });
    
    <?php
    if($isEdit){
        ?>
        jQuery('#email_templates').trigger('change');           
        <?php
    }
    ?>
});
</script> 
<?php 
    $this->end();
?>
<div class="inner-content">
    <?php $this->Html->pageInnerTitle(__('Add Email template')); ?>
    <div class="row-fluid">
    <div class="widget">
        <?php
            echo $this->Form->create('EmailTemplate',array(
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="form-row">
            <label class="field-name"><?php echo __('Email Template Type'); ?>:</label>
            <div class="field">
            <?php
                echo $this->Form->select('template',$EmailTemplates,array(
                    'id'        => 'email_templates',
                    'empty'     => __('Select E-mail Template')
                ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Setting Template Type'); ?>:</label>
            <div class="field">
            <?php
                echo $this->Form->select('email_template_setting_id',$emailTemplateSettings,array(
                    'id'        => 'email_template_setting_id',
                    'empty'     => __('Select E-mail Setting Template')
                ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Subject'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('subject',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12 mailmetatext',
                        'placeHolder'   => __('Subject of the Email'),
                    ));
                ?>
                <div class="placeMarker">
                    <div class="tagsinput"></div>
                </div>
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
                        'id'            => 'cleditor',
                        'rows'          => 10,
                        'placeHolder'   => __('E-mail Boby')
                    ));
                ?>
                <div class="placeMarker">
                    <div class="tagsinput"></div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('From'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('from',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('From E-mail Address')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Username'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('username',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('User name for the E-mail Address')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Password'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('password',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('password for the E-mail Address')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Mail Types'); ?>:</label>
            <div class="field">
            <?php
                echo $this->Form->select('mailtype',$EmailTypes,array(
                    'id'        => 'mailtype',
                    'empty'     => __('Select E-mail Type to be used')
                ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Header'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->textarea('headers',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'rows'          => 3,
                        'placeHolder'   => __('Header to be passed for the E-mail')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">                
            <div class="field">
            <?PHP
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.__(' Save'),array(
                                'class'         => 'button button-blue',
                                'type'          => 'submit',
                            ),array(
                                'escape' => FALSE
                            ));
            ?>
                
            <?PHP
            echo $this->Html->link(
                        '<i class="icon-remove icon-white"></i>'.__(' Cancel'),
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

