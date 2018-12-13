<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
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
            'bootstrap',
            'bootstrap-responsive',
            'main',
            'custom_style',
            'jquery.datetimepicker',
            'font-awesome.min',
            'chosen/chosen.min'
        ));
        echo $this->Html->script(array(
            'jquery-1.8.3.js', 
            'ui/jquery-ui-1.9.2.custom.js',      
            'bootstrap.js',
            'uniform/jquery.uniform.js', 
            'sparkline/jquery.sparkline.js',  
            'mouse-wheel/jquery.mousewheel.js', 
            'file-tree/jqueryFileTree.js', 
            'easy-pie-chart/jquery.easy-pie-chart.js', 
            'cleditor/jquery.cleditor.js', 
            'jquery-splitter/splitter.js', 
            'cookie/jquery.cookie.js', 
            'masonry/jquery.masonry.js', 
            'masked/jquery.maskedinput.js', 
            'powertip/jquery.powertip.js', 
            'range-picker/daterangepicker.js', // for date picker
            'range-picker/date.js', // for date Picker
            'fancybox/jquery.fancybox.js', 
            'flexslider/jquery.flexslider.js', // user for chart
            'tags-input/jquery.tagsinput.js', 
            'form-validate/jquery.validate.js', 
            'scrollbar/jquery.mCustomScrollbar.js', 
            'debounced/debounced.js', 
            'ibutton/jquery.ibutton.js', 
            'password-meter/password_strength.js', 
            'gritter/jquery.gritter.min.js', 
            'bootstrap-wizards/jquery.bootstrap.wizard.js', 
            'rating/jquery.rating.js',             
            'main.js', 
            'forms.js',
            'fileuploader.js',
            'ossuploadergallery.js.js',
            'nestedsortable.js',           
            'jscolor/jscolor.js',        
            'jquery.countdown.js',
            'jquery.slimscroll.min.js',           
            'fullcalendar/fullcalendar.min.js',            
            'jquery.datetimepicker.js',
            'chosen/chosen.jquery.min',
            'chosen/chosen.proto.min',
            'colorpicker/jscolor',
            'tinymce/tinymce.min',                       
            'jquery.scrollTo.min.js',
//            'datetomysql.js',
            'general.js',
        ));
    
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');

    ?>
    <style>
        <?php foreach($areaColors as $area){ ?>
        .area-<?php echo $area['Area']['slug']; ?>-color{
            color       : #ffffff;
            background  : <?php echo (!empty($area['Area']['color']))?$area['Area']['color']:'#3f3f3f' ?> !important;
        }
        <?php } ?>
        
        <?php if($iframe){ ?>
        .mainNavigation{
            display:none !important;
        }
        .nosidebar header{
            margin-left:0px !important;
        }
        .nosidebar .content {
            margin-left: 0px !important;
        }

        <?php } ?>
    </style>
</head>
<body>
    <div class="loading"><?PHP echo $this->Html->image('loaders/loader01.gif', array('alt' => 'CakePHP')); ?></div>
    <div id="container" class="nosidebar">
        <?php echo $this->Element('no-margin-header'); ?>
        <div id="content" class="no-margin content addUserPage nosidebar">
            <?PHP echo $this->Element('pageheader'); ?>
            <?php echo $this->Session->flash(); ?>                
            <?php echo $this->fetch('content'); ?>
            <?php //echo $this->element('sql_dump'); ?>
        </div>
        <div class="clearfix"></div>
    </div>
</body>
</html>

