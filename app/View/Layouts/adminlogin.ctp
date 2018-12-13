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
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $sitename; ?>		
	</title>
	<?php
		echo $this->Html->meta('icon','img/lisabeth/favicon-32.png', array('type' =>'icon'));
                echo $this->Html->css(array(
                                            'admin/bootstrap',
                                            'admin/bootstrap-responsive',
                                            'admin/main',
                                            'admin/custom_style',
                                            )
                                );
                echo $this->Html->script(array(
                        'admin/jquery-1.8.3.js',                       
                    ));
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
        <?php if($iframe){ ?>
        <style>
            .login-container .login .avatar,.login-container > span{
                display: none !important;
            }
        </style>
        <?php } ?>
</head>
<body>   
    <div id="container">
        <div id="content" class="content">
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->fetch('content'); ?>
        </div>
    </div>    
</body>
</html>
