<div class="row">
    <div class="col-xs-12 col-sm-8 info">
        <!--<h3>Hej John john</h3>-->
        <h3><?php echo __('Hej ').' '.$currentUser['User']['firstname'].' '.$currentUser['User']['lastname']; ?></h3>
        <p>
            Velkommen til vores online elev system.
            Via systemet kan du få adgang til at se dine køretider, overblik over din økonomi, ændre dine
            stamdata, samt se og genfinde det materiale du har fået udleveret til holdstart.
            Systemet er under opbygning og test, hvorfor der kan forekomme enkelte fejl. Skulle du støde
            på system fejl, eller har forslag til forbedringer mv. til systemet, bedes du henvende dig pr. mail
            <a href="mailto:morten.s@lisbeth.dk">morten.s@lisbeth.dk</a>
        </p>
    </div>
    <div class="col-xs-12 col-sm-4">
        <?php 
        $class = ($currentUser['User']['balance'] < 0) ? 'green' : 'red'; 
        if($message != '') { ?>
        <div class="<?php echo $class; ?>-block col-xs-12">
            <h3><?php echo $message; ?></h3>
        </div>
        <?php } ?>
        <?php if(isset($nextBooking) && !empty($nextBooking)) { ?>
        <div class="white-block col-xs-12">
            <h3><?php echo __('Your next run time is:'); ?><br/>
            <span><?php echo (isset($nextBooking)) ? date('d.m.Y',strtotime($nextBooking)) : '';?><br/><?php echo (isset($nextBooking)) ? date('H:i',strtotime($nextBooking)) : '';?></span></h3>
        </div>
        <?php } ?>
    </div>
</div>    
