<!DOCTYPE html>
<html>
    <head>
        <?php 
            echo $this->Html->script(array(
                'jquery-1.8.3.js',
                'ui/jquery-ui-1.9.2.custom.js',                          
            ));
            
            echo $this->fetch('script');            
        ?>          
    </head>
    <body style="background: none !important">       
        <?php echo $this->fetch('content'); ?>
    </body>
</html>

