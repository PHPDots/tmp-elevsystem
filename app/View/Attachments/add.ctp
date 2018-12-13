<?php
    $this->append('script');        
    ?>
    <script type="text/javascript">
        var uploader;        
        jQuery().ready(function(){   
            
            jQuery('#recent_uploaded_file').click(function(){
                list = jQuery('.qq-upload-list .qq-upload-success').find('.qq-upload-file');                
                attachments = Array();
                jQuery.each(list.find('.attachment'),function(){                    
                    temp = "temp = " + jQuery(this).parent().find('.attachment_json').val();                      
                    eval(temp);
                    attachments.push(temp);
                });                       
                parent.jQuery.fn.ossGalleryResponse = attachments;
                parent.jQuery.fancybox.close();
            });
            
            uploader = new qq.FileUploader({                
                element             : document.getElementById('doc_file'),
                action              : '<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'upload')); ?>',                      
                multiple            :  <?php echo $multiple; ?>,
                //allowedExtensions   : ['jpg', 'png', 'gif'],
                sizeLimit           : 104857600,
                onSubmit            : function(id, fileName){},
                onProgress          : function(id, fileName, loaded, total){},
                onComplete          : function(id, fileName, responseJSON){     
                    attachments = Array();
                    
                    temp = "temp = " + JSON.stringify(responseJSON);                      
                    eval(temp);
                    attachments.push(temp);
                    
                    parent.jQuery.fn.ossGalleryResponse = attachments;
                    parent.jQuery.fancybox.close();
                 
                },
                onCancel        : function(id, fileName){},                
                debug           : true,
                extraDropzones  : [qq.getByClass(document, 'qq-upload-extra-drop-area')[0]]                
            });
        });
    </script>
    <?php
    $this->end();
?>
<div class="inner-content">
    <?php if($isFancybox){?>
    <ul class="upload_files">
<!--        <li><a href="<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'index','fancybox')); ?>"><?php echo __('Library');?></a></li>-->
        <li><a href="<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'add','fancybox','multiple' => 'false')); ?>"><?php echo __('Upload Files');?></a></li>        
    </ul>    
    <?php } ?>
    <?php
         echo $this->Form->create('Attachment',array(
             'class' => 'form-horizontal'
         ));
     ?>
    <div id="doc_file"></div>
    <?php if($isFancybox){?>
<!--    <input type="button" value="<?php echo __('Use File'); ?>" class="button button-orange attachmentSave" id="recent_uploaded_file"/>-->
    <?php } ?>
    <?php echo $this->Form->end(); ?>    
</div>


