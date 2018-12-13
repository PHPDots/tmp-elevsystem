<script type="text/javascript">
jQuery(document).ready(function(){
    
    jQuery('#menu_id').val(<?php echo $menu['Menu']['id']; ?>);
    jQuery('#menuName').val('<?php echo $menu['Menu']['name']; ?>');    
    jQuery('#menuSlug').val('<?php echo $menu['Menu']['slug']; ?>');  
    jQuery('#action').val('update');
    jQuery('#selectObjectType').val('<?php echo $menu['Menu']['role_slug']; ?>');
    
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
 });
</script>

<?php 
        $selectedMenu       = ($isEdit)?$menu['Menu']['additional_menu_items']:$menu['Menu']['hierarchy'];        
        $displayMenuItems   = array();
        $menuItems          = json_decode($selectedMenu);
      
        if(isset($menuItems)){
            foreach($menuItems as $menuItem){
                $displayMenuItems[]     = (array)$menuItem;
            }
            if(!empty($displayMenuItems)){
                $count = count($displayMenuItems);                
            }
        }            
?>
<script type="text/javascript">
    menuIdCount = <?php echo $count+1; ?>
</script>
<div class="oss_loading" id="menuLoader"><?php  echo $this->Html->image("big-loader.gif");?></div>  
<?php
        
        if(!empty($displayMenuItems)){
            echo $this->CustomMenuWalker->walk($displayMenuItems,array(
                            'idField'           =>  'itemId',
                            'parentIdField'     =>  'parentId',
            ));
        }else{
?>
<ol id="menuItemsList" class="sortable">
                    
</ol>
<?php
        }
        
?>
