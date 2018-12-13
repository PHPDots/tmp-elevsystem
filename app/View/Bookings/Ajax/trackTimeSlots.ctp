<?php
     foreach($areaTimeSlots as $key => $areaTimeSlot){   ?>
        <option value=<?php echo json_encode($areaTimeSlot); ?> <?php echo (in_array(json_encode($areaTimeSlot),$time ))?'selected':''; ?>><?php echo $key; ?></option>
<?php } ?>
