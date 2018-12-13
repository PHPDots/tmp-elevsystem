<script type="text/javascript">
    var buttons = [
        {
            text: '<?php echo (isset($button1)) ? $button1 : 'Ok'; ?>',
            icons: {
              primary: "ui-icon-heart"
            },
            click: function() {
                <?php if(isset($cancelBooking)) { ?>
                    var studentId = <?php echo $studentId ?>;
                    jQuery('.trackDetails').each(function() {
                        
                        if(jQuery(this).find('.studentId').val() == studentId) {
                            var id = jQuery(this).find('.trackCheckbox').attr('id');
                            jQuery('#'+id).trigger('click');
                            jQuery(this).find('.trackCheckbox').attr('checked',false);
                            jQuery(this).find('.trackCheckbox').parent().removeClass('checked');
                            jQuery(this).find('.studentIdAutoSuggest').val('');
                            jQuery(this).find('.studentId').val('');
                        }
                    });
                    jQuery('#bookingAdd').dialog('close');
                <?php } else { ?>
                    jQuery('#bookingAdd').dialog('close');
                <?php } ?>
            }
        }
    ];
    
    <?php
    if(isset($button2)) {
    ?>
        buttons.push({
            text: '<?php echo $button2; ?>',
            icons: {
              primary: "ui-icon-heart"
            },
            click: function() {
                var studentId = <?php echo $studentId ?>;
                jQuery('.trackDetails').each(function() {

                    if(jQuery(this).find('.studentId').val() == studentId) {
                        var id = jQuery(this).find('.trackCheckbox').attr('id');
                        jQuery('#'+id).trigger('click');
                        jQuery(this).find('.trackCheckbox').attr('checked',false);
                        jQuery(this).find('.trackCheckbox').parent().removeClass('checked');
                        jQuery(this).find('.studentIdAutoSuggest').val('');
                        jQuery(this).find('.studentId').val('');
                    }
                });
                jQuery('#bookingAdd').dialog('close');
            }
        });
    <?php
    }
    ?>
    jQuery('#bookingAdd').dialog({
        autoOpen        : true,
        modal           : true,
        width           : 400,
        height          : 210,
        close           : function(ev, ui) { 
            jQuery(this).remove();
        }
    });
    jQuery('#bookingAdd').dialog('option','buttons',buttons);
</script>
<div id="bookingAdd" style="display: none;" title="<?php echo $title; ?>">
    <p><?php echo $message; ?></p>
</div>