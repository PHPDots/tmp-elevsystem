<script type="text/javascript">
    var buttons = [
        {
            text: '<?php echo (isset($button1)) ? $button1 : 'Ok'; ?>',
            icons: {
              primary: "ui-icon-heart"
            },
            click: function() {
                <?php if(isset($cancelBooking)) { ?>
                    window.location = '<?php echo $this->Html->url(array('controller' => 'drivingLessons','action' => 'index')); ?>';
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
                window.location = '<?php echo $this->Html->url(array('controller' => 'drivingLessons','action' => 'index')); ?>';
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