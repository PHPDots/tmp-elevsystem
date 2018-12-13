<script type="text/javascript">
    jQuery('#familyAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                <?php if(!$isEdit && $layout == 'fancybox') { ?>
                        parent.jQuery.fancybox.close();
                <?php } else if($role != 'admin') { ?>
                    jQuery(this).html('Please Wait While We are redirecting you...');
                    window.location='<?PHP echo $this->Html->url(array('controller' => 'AdminUsers','action'=>'students')); ?>';
                <?php } else { ?>
                    jQuery(this).html('Please Wait While We are redirecting you...');
                    window.location='<?PHP echo $this->Html->url(array('controller' => 'AdminUsers','action'=>'index')); ?>';
                <?php } ?>
            }
        }
    });       
</script>
<div id="familyAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>