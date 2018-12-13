<script type="text/javascript">
    jQuery('#bookingTrackUpdate').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery(this).dialog('close');
                <?php if($status == 'success') { ?>
                        jQuery('.student_info').slideUp('slow');
                        location.reload();
                <?php } ?>
            }
        }
    });
</script>
<div id="bookingTrackUpdate" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>
