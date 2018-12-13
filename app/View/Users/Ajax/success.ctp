<script type="text/javascript">
    
    var url = '<?PHP echo $this->Html->url(array('controller' => 'users','action'=>'view')); ?>';
    
    <?php if(isset($url) && !empty($url)){ ?>
        url = '<?php echo $url; ?>';
    <?php } ?>
    jQuery('#familyAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery(this).html('Please Wait While We are redirecting you...');
                window.location = url;
            }
        }
    });       
</script>
<div id="familyAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>