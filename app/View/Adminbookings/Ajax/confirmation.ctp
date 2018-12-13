<script type="text/javascript">    
    jQuery('#bookingAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Yes'       : function(){
                jQuery.ajax({
                    url         : '<?PHP echo $this->Html->url(array('controller' => 'adminbookings','action'=>'updateStudentDetails',$id)); ?>',
                    type        : 'post',
                    dataType    : 'json',
                    success     : function(data){                       
                        if(data.status  == 'success'){
                            jQuery('#bookingAdd').dialog('close');
                        }else{
                            alert('Alert Error');
                        }
                    }
                });                
            },
            'No'        : function(){
                jQuery('#medicalForm').dialog({
                    autoOpen        : true,
                    modal           : true,
                    buttons         : {
                        'Ok'        : function(){
                            jQuery(this).dialog('close');
                            jQuery('#bookingAdd').dialog('close');
                        }
                    }
                });
            }
        }
    });       
</script>
<div id="bookingAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo __("Has {$student} has delivered first-aid training + medical report?"); ?></p>
</div>

<div id="medicalForm" style="display: none" title="<?php echo $title; ?>">
    <p><?php echo __("The student may not continue until first aid certificate and medical certificate is handed over."); ?></p>
</div>