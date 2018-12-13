<!DOCTYPE html>
<html>
    <head>
        <?php 
            echo $this->Html->script(array(
                'admin/jquery-1.8.3.js',
                'admin/ui/jquery-ui-1.9.2.custom.js',
                'admin/fileuploader.js',
                'admin/uniform/jquery.uniform.js',
                'admin/fancybox/jquery.fancybox.js',
                'admin/ossuploadergallery.js.js',
            ));
            
            echo $this->Html->css(array(
                'admin/bootstrap',
                'admin/bootstrap-responsive',
                'admin/main',
                'admin/custom_style',
                'admin/fileuploader',
            ));
            
            echo $this->fetch('script');
            echo $this->fetch('css');
        ?>  
        <script type="text/javascript">
           
            jQuery().ready(function(){
                
               jQuery("#dropdown").change(function(){    
                    jQuery(this).parents('form').submit();

                });
                
                jQuery(document).delegate('#use_files_btn','click',function(){                    
                    attachments = Array();
                    jQuery.each(jQuery('.attachment'),function(){
                        if(!jQuery(this).is(':checked'))
                            return;
                        temp = "temp = " + jQuery(this).parent().find('.attachment_json').val();
                        eval(temp);
                        attachments.push(temp);
                    });                       
                    parent.jQuery.fn.ossGalleryResponse = attachments;
                    parent.jQuery.fancybox.close();
                });               
            });             
        </script>
    </head>
    <body style="background: none !important">
        <input type="hidden" name="test" id="test" value="55" />        
        <input type="hidden" name="selectedfiles" id="selectedfiles" />
        <?php echo $this->fetch('content'); ?>
        <div id="exc" style="display: none;"></div>
    </body>
</html>


