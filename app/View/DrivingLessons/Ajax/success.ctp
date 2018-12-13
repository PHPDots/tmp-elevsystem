<script type="text/javascript">  
    jQuery('#bookingAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery(this).html('Please Wait While We are redirecting you...');
                window.location='<?PHP echo $this->Html->url(array('controller' => 'drivingLessons','action'=>'index')); ?>';
            }
        }
    });       
</script>
<div id="bookingAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>
