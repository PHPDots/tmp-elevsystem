<script type="text/javascript">     
    jQuery('.error-message').hide();
    var temp =<?PHP echo json_encode($error_msg); ?>;
    for (i = 0; i < temp.length; i++){        
        jQuery('#' + temp[i].key).show().html(temp[i].message);
    }   
    jQuery(".error-message").animate({scrollTop: 0}, "slow");
</script>
