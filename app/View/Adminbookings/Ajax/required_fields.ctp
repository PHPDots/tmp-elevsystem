<script type="text/javascript">

    jQuery('#bookingAdd').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery(this).dialog( "close" );
                jQuery('html,body').animate({ scrollTop: $("#booking_info").offset().top}, 'slow'); }
        }
    });       
</script>
<div id="bookingAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>
