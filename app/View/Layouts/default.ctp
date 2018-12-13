<!DOCTYPE html>
<html>
<head>
    <?php  echo $this->Html->charset(); ?>
    <title><?php echo $options['site_title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        echo $this->Html->meta('icon','img/favicon-32.png', array('type' =>'icon'));
        echo $this->Html->css(array(
										'front/bootstrap.min',
										'front/fonts/font-awesome.min',
										'front/style',
										'front/fonts/fontstyle',
										'front/responsive',
										'front/fullcalendar',
										'front/jquery-ui',
										'front/jquery.fancybox',
										'front/chosen/chosen.min',
										'front/jquery.datetimepicker',
										'front/jquery.rtable'
									)
								);
        echo $this->Html->script(array(
											'front/jquery-1.9.0',
											'front/bootstrap.min',
											'front/fullcalendar/fullcalendar',
											'front/general',
											'front/jquery.datetimepicker',
											'front/jquery.scrollTo.min',
											'front/ui/jquery-ui-1.9.2.custom',
											'front/fancybox/jquery.fancybox',
											'front/jquery.datetimepicker.js',
											'front/chosen/chosen.jquery.min',
											'front/chosen/chosen.proto.min',            
											'front/ossuploadergallery.js.js',
											'front/jquery.rtable.js',
											'front/table_custom.js'											
										)
								);    
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');

    ?>
    
    <script type="text/javascript">
        jQuery().ready(function(){
            jQuery('.submenu').hide();
            jQuery('.has-submenu').click(function(){
                if(jQuery(this).hasClass('open-block')){
                    jQuery(this).find('.submenu').slideUp();
                    jQuery(this).removeClass('open-block');                        
                }else{
                     jQuery(this).find('.submenu').slideDown();
                     jQuery(this).addClass('open-block');  
                }                    
            });
            
            jQuery('.side-bar').height(jQuery(document).height());
        });
   </script>
   <style>
        <?php foreach($areaColors as $area){ ?>
        .area-<?php echo $area['Area']['slug']; ?>-color{
            color       : #ffffff;
            background  : <?php echo (!empty($area['Area']['color']))?$area['Area']['color']:'#3f3f3f' ?> !important;
            border      : 1px solid <?php echo '#3f3f3f'; ?>;
            border-bottom: 0px;
        }
        <?php } ?>
    </style>
</head>
<body>
    <div id="main" class="container-fluid">
        <div class="header row">
            <?php echo $this->Element('header'); ?>
            <div class="col-xs-12 col-sm-9">
                <?php echo $this->Element('pageheader'); ?>
            </div>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-xs-12 col-sm-3 navbar navbar-default menu menu-default">
                    <div class="side-bar">                        
                    <?php 
                        switch($currentUser['User']['role']){
                            case 'student':
                                 echo $this->Element('frontsidebar'); 
                                 
                                break;
                            case 'internal_teacher':
                                 echo $this->Element('internal_teacher_sidebar'); 
                                break;
                        }
                    ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-9 menu_ct">                    
                    <?php echo $this->Session->flash(); ?>                
                    <?php echo $this->fetch('content'); ?>
                </div>
            </div>
            <div id="exc" style="display: none;"></div>
        </div>
    </div>
	<?php echo $this->Element('footer');  ?>
</body>
</html>