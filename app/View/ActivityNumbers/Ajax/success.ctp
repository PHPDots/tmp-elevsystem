<script type="text/javascript">
    jQuery('#familyAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery(this).html('Please Wait While We are redirecting you...');
                window.location='<?PHP echo $this->Html->url(array('controller' => 'activityNumbers','action'=>'index')); ?>';
            }
        }
    });       
</script>
<div id="familyAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>