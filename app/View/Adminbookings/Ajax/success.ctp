<script type="text/javascript">
    var date    = '<?php echo ((isset($date) && !empty($date)))?$date:''; ?>';
    var area    = '<?php echo ((isset($area) && !empty($area)))?$area:''; ?>';
    var week    = '<?php echo ((isset($week) && !empty($week)))?$week:''; ?>';
    var url     = '';
    <?php if((isset($date) && !empty($date)) && (isset($area) && !empty($area))) { ?>           
            url     = '?date='+date+'&area='+area+'&week='+week+'&iframe=<?php echo $iframe; ?>';
    <?php } ?>
    jQuery('#bookingAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery(this).html('Please Wait While We are redirecting you...');
                window.location.href = "<?php echo $this->Html->url(array('controller' => 'adminbookings','action'=>'calendar')); ?>"+url;
//                window.location.href = "<?php // echo $this->Html->url(array('controller' => 'adminbookings','action'=>'redirectUrl')); ?>"+url;
            }
        }
    });       
</script>
<div id="bookingAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>
