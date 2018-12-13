<script type="text/javascript">
    jQuery('#bookingTrackUpdate').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                jQuery('#bookingTrackUpdate').dialog('close');
            }
        }
    });
</script>
<div id="bookingTrackUpdate" style="display: none;" title="Booking conflicts with other appointments">
    <p><?php echo $message; ?></p>
</div>
