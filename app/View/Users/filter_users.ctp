<?php $this->append('script'); ?>
<script type="text/javascript">    
jQuery(document).ready(function(){
    jQuery('.userautosuggest').autocomplete({
                minLength   : 1,
                select: function( event, ui ) {  
                    
                    item    = '';
                    item   += '<div class="widget-header userWidget"  user-id="'+ ui.item.sysvalue +'">';                              
                    item   += '<h5>'+ ui.item.label +'</h5>';      
                    item   += '<button class="button button-red removeUser" type="button">';
                    item   += '<i class="icon-remove icon-white"></i>';
                    item   += '</button>';
                    item   += '</div>';
                    
                    element = jQuery(this).parent().find('.userDetails');
                    element.append(item); 
                    
                    input   =  jQuery(this).parent().find('.userObjectId');
                    input.val('');
                    jQuery.each(element.find('.userWidget'),function(i,value){
                         input.val(input.val() + jQuery(this).attr('user-id') + ',' );   
                    })
                   
                },
                source      : function(request, response){

                    jQuery.ajax({
                        url         : '<?php echo $this->Html->url(array('controller'=>'Users','action'=>'autoSuggest')); ?>/' + request.term,
                        dataType    : "json",
                        complete    : function(){
                            
                        },
                        beforeSend  : function(){
                        
                        },
                        success     : function(data){
                            response( jQuery.map( data , function( item ) {
                                return {
                                  label     : item.User.username  + ' [ ' +  item.User.firstname + ' ' + item.User.lastname  + ' < ' + item.User.email_id + ' > ] ' ,
                                  value     : item.User.username  + ' [ ' +  item.User.firstname + ' ' + item.User.lastname  + ' < ' + item.User.email_id + ' > ] ' ,
                                  sysvalue  : item.User.id
                                }
                            }));
                        }

                    });
                },
                open: function() {
                    jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                },
                close: function() {
                    jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                    jQuery(this).val('');
                }
        });
});
</script> 
<?php $this->end(); ?>
<div class="inner-content">
    <div class="row-fluid addUserInfo-container">
        <div class="span6">              	
            <h5 class="addUserTitle"><?php echo __('Filter User'); ?></h5>
        </div>
        <div class="clear"></div>
    </div> 
    <form></form>
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('FilterUser',array(
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5>User account</h5>
        </div>
        <div class="widget-content no-padding">            
            <div class="form-row">
                <label class="field-name" for="firstname"><?php echo __('User'); ?>:</label>
                <div class="field">
                    <?php                         
                        echo $this->Form->input(
                            'text',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12 userautosuggest',
                                'placeHolder'   => __('Enter Usename/First Name/Last Name/Email Id'), 
                        ));
                    ?>
                    <?php 
                            echo $this->Form->hidden('user_id',array(
                                               'class'     => 'userObjectId',                                        
                                           ));
                    ?>
                    <div class="userDetails"></div>
                </div>
            </div>                
            <div class="form-row">                
                <div class="field">
                <?PHP
                    echo $this->Form->button('<i class="icon-ok icon-white"></i>  Filter',array(
                        'class'         => 'button button-green',
                        'type'          => 'submit',
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
    </div></div>
</div>
