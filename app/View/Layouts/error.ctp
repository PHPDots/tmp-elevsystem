<!DOCTYPE html>
<html>
    <head>
        <?php 
            echo $this->Html->script(array(
                'jquery-1.8.3.js',
                'fileuploader.js',               
            ));
            
            echo $this->Html->css(array('bootstrap','bootstrap-responsive','main','custom_style','fileuploader',));
            
            echo $this->fetch('script');
            echo $this->fetch('css');
        ?>          
    </head>
    <body>       
        <?php echo $this->fetch('content'); ?>
    </body>
</html>