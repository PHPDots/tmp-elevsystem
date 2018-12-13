<?php  $this->append('script'); ?>
<script type="text/javascript">
    
   var controllerList   = <?php echo json_encode($controllerList);?>;
   var actions          = [];
   var menuIdCount      = 0;
   var hierarchy        = [];
   var indexParent;
   jQuery().ready(function(){
        jQuery('#saveMenu').hide();
        jQuery('#createMenuDiv,.linkDetailDiv,.object,.oss_loading').hide();
        
        temp = '<option value="-1"><?php echo __('Select Controller'); ?></option>';
        for(controller in controllerList){
            eval("name=controllerList." + controller + ".name;" )
            temp += '<option value="' + controller + '">' + name +'</option>';
        }
        jQuery('#controllerList').html(temp)
        
        jQuery('input[name="linkType"]').click(function(){    
            jQuery('.linkDetailDiv').slideUp();
            jQuery('#'+ jQuery(this).val() + 'LinkingDiv' ).slideDown(); 
        });
        
        jQuery('#controllerList').change(function(){
            jQuery('#actionList').empty();            
            if(jQuery(this).val() == '-1' ){
                return
            }
            eval("actions = controllerList." + jQuery(this).val() + ".actions;"); 
            temp = '<option value="-1"><?php echo __('Select Action'); ?></option>';
            for(x in actions){                
                temp += '<option value="' + actions[x] + '">' + actions[x] +'</option>';
            }
            jQuery('#actionList').append(temp);
           
        });
        
        jQuery('#createMenuBtn').click(function(){
            jQuery('#menuName').val('');
            jQuery('#menuSlug').val('');
            jQuery('#menuList').val('');
            jQuery('#menuArabicName').val('');
            jQuery('#menu_id').val('');
            jQuery('#action').val('');            
            jQuery('#selectObjectType').val('');
            jQuery('.object').hide();
            jQuery('#menuItemsList').html('');
            jQuery('#saveMenu').show();
        });
        
        jQuery('#saveMenu').click(function(){
            
            if(jQuery('#menuName').val() == ''){
                return;
            }
            
            jQuery('#hierarchy').val(jQuery.fn.blackmenu.getMenu('menuItemsList'));
            
            jQuery.ajax({
                type        : "POST",
                url         : '<?php echo $this->Html->url(array('controller' => 'menus','action'=>'saveMenu')); ?>',             
                data        : jQuery('#MenuIndexForm').serialize(),
                dataType    : "html",                
                beforeSend  : function(){
                    jQuery('#menuLoader').show();
                    jQuery('#menuItemsList').hide();
                    jQuery('#saveMenu').hide();
                },
                success     : function(data){                   
                   jQuery('#menuItemsListContainer').html(data);
                },
                complete    : function(){
                    jQuery('#menuLoader').hide();
                    jQuery('#menuItemsList').show();
                }
            });
            
        });
       
       jQuery('body').delegate('.menu-widget-header','click',function(){
            jQuery(this).parent().find('.menu-slide').first().slideToggle('slow');
        });
        
        jQuery('#menuName').blur(function(){
           var slug = jQuery(this).val();
           var modifiedSlug = slug.toLowerCase().replace(/ /g ,'_');
           jQuery('#menuSlug').val(modifiedSlug);
        });
        
        jQuery('#loadMenuBtn').click(function(){  
            
            if(jQuery('#menuList').val() == '')
                return;
           
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'   => 'menus','action'=>'getMenu')); ?>/' +  jQuery('#menuList').val(),
                dataType    : "html",
                beforeSend  : function(){
                    jQuery('#menuLoader').show();
                    jQuery('#menuItemsList').hide();
                },
                success     : function(data){ 
                     jQuery('#menuItemsListContainer').html(data);
                     jQuery('#saveMenu').show();
                },
                complete    : function(){
                    jQuery('#menuLoader').hide();                    
                    jQuery('#menuItemsList').show();
                }
            });
            
        });
        
        jQuery('#addMenuItemBtn').click(function(){

            var contoller;
            var action;
            var link;
            
            linkType = jQuery('input[name="linkType"]:checked').val();
                 
            input  = '<div class="widget menu-item">';
            input += '<div class="menu-widget-header">';
            
            input += '<div class="leftMenuItemEdit">'+jQuery('#menuItemName').val();
            input += '<input type="hidden" value="' + jQuery('#menuItemName').val()  + '" class="name" />';
            input += '<input type="hidden" value="' + jQuery('#menuItemClass').val() + '" class="cssclass" />';
            input += '<input type="hidden" value="' + linkType                       + '" class="linktype" />';           
            
            if(linkType == 'internal'){               
                contollerName   = jQuery('#controllerList').val();
                contoller       = eval("name=controllerList." + contollerName + ".name;" );
                action          = jQuery('#actionList').val();
                link            = 'Internal : ' + contoller +' : ' + action;
                
                input          += '<input type="hidden" value="'+ contoller + ':' + action +'" class="link" />';
                
            }else if(linkType == 'custom'){                
                input += '<input type="hidden" value="'+ jQuery('#customLink').val() +'" class="link" />';
                link   = 'Custom :  ' + jQuery('#customLink').val();
                
            }
            
            input  += '</div>';
            input  += '<div class="rightMenuItemEdit">';
            
            input  += '<button class="button button-red remove_link removeMenuItem" type="button"><i class="icon-remove icon-white"></i></button>&nbsp;'; 
            input  += '</div>';
            
            input  += '</div>';
            
            input  += '<div class="menu-slide">';
            
            input  += '<div class="widget-content form-horizontal">';
            
            input  += '<div class="form-row">';
            input  += '<div class="span6" >';
            input  += '<label class="field-name menuLabel"><?php echo __('Navigation Label: &nbsp;');?></label>';
            input  += '<input type="text" class="span12" placeholder="Navigation Label" value="'+jQuery('#menuItemName').val()+'" />';
            input  += '</div>';
            
            input  += '<div class="span6">';
            input  += '<label class="field-name menuLabel"><?php echo __('Class Name: &nbsp;');?></label>';
            input  += '<input type="text" class="span12" placeholder="Classes" value="'+jQuery('#menuItemClass').val()+'" />';
            input  += '</div>';
            
            input  += '<div class="clearfix"></div>';
            input  += '</div>';
            
            input  += '<div class="form-row">';           
            input  += '<label class="field-name menuLabel"><?php echo __('Link: &nbsp;');?>';
            input  += '<a href="#" target="blank">'+ link +'</a>';            
            input  += '</label>';
            input  += '</div>';
            
            input  += '</div>';
            input  += '</div>';
            input  += '</div>';
             
            jQuery('#menuItemsList').append('<li id=list_' + (menuIdCount++) +'>' + input + '</li>');

            jQuery.each(jQuery('#menuItemsList li'),function(index,element){                                   
                    jQuery(this).attr('oss-menu-id',index);
            });
        });
       
       jQuery(document).delegate('#menuListItemName','keyup',function(){           
            name    = jQuery(this).val();
            jQuery(this).parents('.menu-item').find('.name').val(name);
        });
        
        jQuery(document).delegate('#menuListItemClass','keyup',function(){           
            classname    = jQuery(this).val();
            jQuery(this).parents('.menu-item').find('.cssclass').val(classname);
        });
        
        jQuery('.sortable').nestedSortable({
            forcePlaceholderSize    : true,
            handle                  : 'div',
            helper                  : 'clone',
            items                   : 'li',
            opacity                 : .6,
            placeholder             : 'placeholder',
            revert                  : 250,
            tabSize                 : 25,
            tolerance               : 'pointer',
            toleranceElement        : '> div',
            maxLevels               : 3,
            isTree                  : true,
            expandOnHover           : 700,
            startCollapsed          : true
        }).on( "sortupdate", function( event, ui ){            
            jQuery.each(jQuery('#menuItemsList li'),function(index,element){                   
                    jQuery(this).attr('oss-menu-id',index);
            });
        });
        
        jQuery('body').delegate('.remove_link','click',function(){            
            jQuery(this).parents('li').first().remove();
            jQuery.each(jQuery('#menuItemsList li'),function(index,element){                   
                    jQuery(this).attr('oss-menu-id',index);
            });
        });
        
        jQuery(document).delegate('#addMenuBtn','click',function(){            
           parent.jQuery.fn.ossMenuResponse = jQuery.fn.blackmenu.getMenu('menuItemsList');          
           parent.jQuery.fancybox.close(); 
        });
        
    });
    
    var j;    
    
    jQuery.fn.blackmenu = {
        id              : '',
        menuItemWalker  : [],
        generateTree    : function (elementId,parentId){
            var children , i;
            
            element = jQuery('li[oss-menu-id="' + elementId + '"]');

            if(j == 0  && parentId == 0){                  
                parentId = parentId;
            }else{
                parentId = "list_" + parentId;
            }
            
            this.menuItemWalker.push({
                parentId    : parentId,
                itemId      : "list_" + elementId,
                name        : element.find('.name').val(),
                ara_name    : element.find('.ara_name').val(),
                cssclass    : element.find('.cssclass').val(),
                linktype    : element.find('.linktype').val(),
                link        : element.find('.link').val()            
            });
            children = jQuery('li[oss-menu-id="'+ elementId +'"] ol > li');
            
            if(children.length>0){      
                j++;                
                for(i=0;i<children.length;i++){                   
                    this.generateTree(jQuery(children[i]).attr('oss-menu-id'),elementId);
                }
            }else{
                j = 0;
            }
        },
        getMenu         : function(id){            
            j                   = 0;
            this.id             = id;
            this.menuItemWalker = Array();
            elements            = jQuery('#' + this.id + ' > li');
            for(count=0;count<elements.length;count++)               
                this.generateTree(jQuery(elements[count]).attr('oss-menu-id'),0);

            return JSON.stringify(this.menuItemWalker);
        },
        drawmenu        :function(id){
            this.id = id;
            eval("items = " + jQuery('#hierarchy').val());
            ol = jQuery(id);
            jQuery.each(items,function(index,element){

            })
        }
    }

</script>
<?php 
$this->end();
echo $this->Form->create('Menu',array(
    'url'   => array(
        'controller'    => 'menus',
        'action'        => 'index'
    )
));
?>
<?php
    echo $this->Form->hidden('menu_id'      ,array('id'=>'menu_id'));
    echo $this->Form->hidden('action'       ,array('id'=>'action'));
    echo $this->Form->hidden('hierarchy'    ,array('id'=>'hierarchy'));
?>
<?php if(!$isFancybox){?>
<div class="inner-content"><div class="row-fluid">
    <div class="widget">            
        
        <div class="widget-header">
            <h5><?php echo __('Select Menu'); ?></h5>
        </div>
        
        <div class="widget-content">
            <div class="form-row"><div class="span12">
                <label class="field-name"><?php echo __('Select Menu');?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->select('menuList',$menuList,array(
                          'label'         => false,
                          'div'           => null,
                          'id'            => 'menuList',
                          'class'         => 'span4',
                          'empty'         => __('Select Menu'),                                                                                
                      ));
                    ?>
                    <?php 
                        echo $this->Form->button(__('Load Menu'),array(
                            'type'          => 'button',
                            'id'            => 'loadMenuBtn',
                            'class'         => 'button button-orange'
                        ));
                    ?>
                    <?php 
                        echo $this->Form->button(__('Create new Menu'),array(
                            'type'          => 'button',
                            'id'            => 'createMenuBtn',
                            'class'         => 'button button-yellow',
                        ));
                    ?>                    
                </div>
                </div></div>
                <div class="clearfix">&nbsp;</div>
        </div>            
        
    </div>
</div></div>
<?php } ?>
<?php 
        $width  = ($isFancybox)?'862px':'auto'; 
        $height = ($isFancybox)?'800px':'auto'; 
?>
<div class="inner-content" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>">
    <div class="row-fluid">  
        
    <div class="widget span4 form-horizontal">
        
        <div class="widget-header">
            <h5><?PHP echo __('Menu Item'); ?></h5>
        </div>
        
        <div class="widget-content no-padding">                                                            
            
            <div class="form-row">
                <label class="field-name menuItemLabel"><?php echo __('Name'); ?>:</label>
                <div class="field">
                    <input type="text" name="menuItemName" id="menuItemName" class="span8" 
                    placeHolder="<?php echo __('Name of the menu item') ?>"/>
                </div>
            </div>
            
            <div class="form-row">
                <label class="field-name menuItemLabel"><?php echo __('Link Type'); ?>:</label>
                <div class="field">
                    <label>                       
                        <input type="radio" name="linkType" value="internal"/>
                        <?php echo __('Internal');?>                        
                    </label>
                    <label>                        
                        <input type="radio" name="linkType" value="custom"/>
                        <?php echo __('Custom');?>                        
                    </label>
                </div>
            </div>
            
            <div id="customLinkingDiv" class="linkDetailDiv">
                <div class="form-row">
                    <label class="field-name menuItemLabel"><?php echo __('Link'); ?>:</label>
                    <div class="field"><input type="text" name="customLink" id="customLink" /></div>
                </div>
            </div>
            
            <div id="internalLinkingDiv" class="linkDetailDiv">
                <div class="form-row">
                    <label class="field-name menuItemLabel"><?php echo __('Controller'); ?>:</label>
                    <div class="field"><select id="controllerList" class="span8"></select></div>
                </div>
                <div class="form-row actionList">
                    <label class="field-name menuItemLabel"><?php echo __('Actions'); ?>:</label>
                    <div class="field"><select id="actionList" class="span8"></select></div>
                </div>
            </div>
                              
            <div class="form-row">
                <label class="field-name menuItemLabel"><?php echo __('Classes'); ?>:</label>
                <div class="field">
                    <input type="text" name="menuItemClass" id="menuItemClass" placeHolder="<?php echo __('Class of the menu item') ?>"
                           value=""    class="span8 menuItemClass" />
                </div>
            </div>
            
            <div class="form-row">                
                <div class="field">
                    <button id="addMenuItemBtn" class="button button-green" type="button">
                        <i class="icon-ok icon-white"></i><?php echo __('Add'); ?>
                    </button>
                </div>
            </div>
            
        </div>     
    </div>     
        
  
    <div class="widget span8">
        <?php if(!$isFancybox){?>
        <div id="MenuDetailDiv" class="widget-header">
            <div class="form-row">
                <div class="span6">
                    <label class="span4"><?php echo __('Menu name');?></label>
                    <div class="span8">
                        <?php 
                            echo $this->Form->input('name',array(
                                'label'         => false,
                                'div'           => null,
                                'type'          => 'text',
                                'id'            => 'menuName',
                                'class'         => 'span12',
                                'placeHolder'   => __('Name of the menu'), 
                            ));
                        ?>         
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="span6">
                    <label class="span4"><?php echo __('Slug');?></label>
                    <div class="span8">
                        <?php 
                            echo $this->Form->input('slug',array(
                                'label'         => false,
                                'div'           => null,
                                'id'            => 'menuSlug',
                                'type'          => 'text',
                                'class'         => 'span12',
                                'placeHolder'   => __('Slug for the menu'), 
                            ));
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>               
            </div>
            <div class="form-row">
                <div class="span6">
                    <label class="span4"><?php echo __('Object');?></label>
                    <div class="span8">
                         <?php 
                                echo $this->Form->select('role_slug',$roles,array(
                                       'class'         => 'span12 selectObject',
                                       'id'            => 'selectObjectType',
                                       'empty'         => __('Select Object Type'),                            
                                   ));
                           ?>             
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>          
            <div class="clearfix"></div>
        </div>
        <?php }else{ ?>
        <div class="widget-header">
            <h5><?php echo __('Menu Items List'); ?></h5>
        </div>
        <?php } ?>
        <div class="widget-content">
            
            <div class="inputDetails" id="menuItemsListContainer">   
                <div class="oss_loading" id="menuLoader"><?php  echo $this->Html->image("big-loader.gif");?></div>  
                <ol id="menuItemsList" class="sortable"></ol>
            </div>  
            
            <div class="actionBtns">
              <?php if($isFancybox){ ?>
                <button id="addMenuBtn" name="add" class="button button-blue" type="button">
                    <i class="icon-ok icon-white"></i><?php echo __('Add Menu');?>
                </button>
              <?php }else{ ?>
                <button id="saveMenu" name="save" class="button button-blue" type="button">
                    <i class="icon-ok icon-white"></i><?php echo __('Save');?>
                </button>
              <?php } ?>
            </div>
            
        </div>
    </div>
        
    <div class="clearfix"></div>
    
</div></div>

<?php
echo $this->Form->end();
