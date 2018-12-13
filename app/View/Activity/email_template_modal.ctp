<?php if($email){ ?>
    <div class="modal-body" style="max-height : 100%">
        <div class="col-sm-12">
            <?php echo $email['body']; ?>
        </div>
    </div>
<?php } else { ?>
    <div class="modal-body">
        <div class="col-sm-12">
            <h4 class="modal-title">No Tempalte Found</h4>      
        </div>
    </div>
<?php } ?>