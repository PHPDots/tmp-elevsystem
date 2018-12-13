<?php
/**
 * 
 * @package OSS
 * @subpackage app.View.Layouts
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $this->Html->charset(); ?>	
    <title>
        <?php echo $options['site_title']; ?>       
    </title>
    <?php
        echo $this->Html->meta('icon','img/favicon-32.png', array('type' =>'icon'));
        echo $this->Html->css(array(
            'front/bootstrap.min',
            'front/fonts/font-awesome.min',
            'front/style',
            'front/fonts/fontstyle',
        ));
        echo $this->Html->script(array(
            'front/jquery.min.js',
            'front/bootstrap.min'
        ));
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>
</head>
<body>   
    <div class="login-wrapper">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->fetch('content'); ?>
    </div>
</body>
</html>
