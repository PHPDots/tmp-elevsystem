<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <?php if($this->Session->check('extraReload')) {
			unset($_SESSION['extraReload']); ?>

				<!--[if IE]-->
				<script type="text/javascript">
					window.location.reload(true);
				</script>
				<!--[endif]-->
				<!--[if EDGE ]-->
				<script type="text/javascript">
					window.location.reload(true);
				</script>
				<!--[endif]-->

    <?php } ?>
    <title><?php echo $sitename; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        echo $this->Html->meta('icon','img/lisabeth/favicon-32.png', array('type' =>'icon'));
        echo $this->Html->css(array(       
									'admin/bootstrap',
									'admin/bootstrap-responsive',
									'admin/main',
									'admin/custom_style',
									'admin/jquery.datetimepicker',
									'admin/font-awesome.min',
									'admin/chosen/chosen.min',
									'admin/bootstrap-datepicker.min'
								)
							 );
        echo $this->Html->script(array(
										'admin/jquery-1.8.3',
										'admin/ui/jquery-ui-1.9.2.custom',
										'admin/bootstrap',
										'admin/uniform/jquery.uniform',
										'admin/cleditor/jquery.cleditor',
										'admin/fancybox/jquery.fancybox',
										'admin/form-validate/jquery.validate',
										'admin/main',
										'admin/forms',
										'admin/fileuploader',
										'admin/ossuploadergallery.js.js',
										'admin/nestedsortable',
										'admin/jscolor/jscolor',
										'admin/fullcalendar/fullcalendar.min',
										'admin/jquery.datetimepicker',
										'admin/chosen/chosen.jquery.min',
										'admin/colorpicker/jscolor',
										'admin/tinymce/tinymce.min',
										'admin/jquery.scrollTo.min',
										'admin/general',
										'admin/datatables/jquery.dataTables.min',
										'admin/bootstrap-datepicker.min',
										'admin/jquery.sessionTimeout.min',
									)
								);
    
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>

    <script type="text/javascript">
        var http_host_js = '<?php echo Router::url('/', true); ?>';
    </script>

    <style>
        <?php foreach($areaColors as $area) { ?>
        .area-<?php echo $area['Area']['slug']; ?>-color{
            color       : #ffffff;
            background  : <?php echo (!empty($area['Area']['color'])) ? $area['Area']['color']:'#3f3f3f' ?> !important;
        }
        <?php } ?>
        
        <?php foreach($trackStatusColors as $key => $track) { ?>
        .slot-<?php echo $key; ?>-color {
            color       : <?php echo (!empty($track) && $track != 'FFFFFF') ? '#FFF' : '#636363 ';echo ' !important'; ?>;
            background  : <?php echo (!empty($track) && $track != 'FFFFFF') ? '#'.$track : ''; ?> !important;
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
        <?php echo $this->Element('adminheader'); ?>
        
		<div class="mainNavigation nosidebar close-menu">
            <div class="innerNavigation">
                <?php
                echo $this->Element('menusidebar');            
                //echo $this->Element('sidebar');
                ?>                   
            </div>
        </div>
        
        <div id="content" class="content addUserPage nosidebar">
            <?PHP echo $this->Element('adminpageheader'); ?>
            <?php echo $this->Session->flash(); ?>                
            <?php echo $this->fetch('content'); ?>
            <?php //echo $this->element('sql_dump'); ?>
        </div>
        <div class="clearfix"></div>
    </div>
</body>
</html>