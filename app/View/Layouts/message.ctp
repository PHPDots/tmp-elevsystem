<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $sitename; ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <?PHP 
        echo $this->Html->script(array(
            'jquery-1.8.3.js',
            'cleditor/jquery.cleditor.js',
            'ui/jquery-ui-1.9.2.custom.js',
            'uniform/jquery.uniform.js',
            'flot/excanvas.min.js',
            'flot/jquery.flot.js',
            'flot/jquery.flot.pie.min.js',
            'flot/jquery.flot.resize.js',
            'flot/jquery.flot.orderBars.js',
            'sparkline/jquery.sparkline.js',
            'full-calendar/fullcalendar.js',
            'mouse-wheel/jquery.mousewheel.js',
            'file-tree/jqueryFileTree.js',
            'easy-pie-chart/jquery.easy-pie-chart.js',
            'jquery-splitter/splitter.js',
            'cookie/jquery.cookie.js',
            'masonry/jquery.masonry.js',
            'masked/jquery.maskedinput.js',
            'powertip/jquery.powertip.js',
            'range-picker/daterangepicker.js',
            'range-picker/date.js',
            'fancybox/jquery.fancybox.js',
            'flexslider/jquery.flexslider.js',
            'tags-input/jquery.tagsinput.js',
            'form-validate/jquery.validate.js',
            'scrollbar/jquery.mCustomScrollbar.js',
            'debounced/debounced.js',
            'ibutton/jquery.ibutton.js',
            'password-meter/password_strength.js',
            'gritter/jquery.gritter.min.js',
            'bootstrap-wizards/jquery.bootstrap.wizard.js',
            'rating/jquery.rating.js',
            'bootstrap.js',
            'chosen/chosen.jquery.js',
            'main.js',
            'general.js',
            'ossuploadergallery.js.js',
            'jquery.slimscroll.min.js',        
        )); 
    ?>
       <!--[if lt IE 10]>        
            <?php echo $this->Html->css(array('ie.css')); ?>
            <?php echo $this->Html->script(array('ie.js','jquery.livequery.min.js')); ?>
        <![endif]-->    
       
    <?php
        echo $this->Html->meta('icon','favicon.ico', array('type' =>'icon'));

//		echo $this->Html->css('cake.generic');
        echo $this->Html->css(array('bootstrap','bootstrap-responsive','main','custom_style',));
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>
    
    <script>
      //* hide all elements & show preloader
//      document.documentElement.className += 'loader';
    </script>
</head>
<body>
    <div class="loading"><?PHP echo $this->Html->image('loaders/loader01.gif', array('alt' => 'CakePHP')); ?></div>
	<div id="container">
            <?php echo $this->Element('header'); ?><div class="mainNavigation">
            <div class="innerNavigation">
                <?PHP echo $this->Element('menusidebar'); ?>
                <?PHP echo $this->Element('messagesidebar'); ?>
            </div>
            </div>
            
            <div id="content" class="content addUserPage">
                <?PHP echo $this->Element('pageheader'); ?>
                <?php echo $this->Session->flash(); ?>
                <?php echo $this->fetch('content'); ?>

            </div>
            <div id="footer">
            </div>
	</div>
	<?php // echo $this->element('sql_dump'); ?>
  
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <script>
        $(document).ready(function() {
            setTimeout('$("html").removeClass("loader")',1000);
             $('.flexslider').flexslider({
                animation       : "slide",
                animationLoop   : false,
                itemWidth       : 70,
                itemMargin      : 0,
                minItems        : 3,
                directionNav    : false
            });
        });
    </script>

</body>
</html>
