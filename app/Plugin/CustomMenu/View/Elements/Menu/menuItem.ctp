<div class="widget menu-item">
    <div class="menu-widget-header">
        <div class="leftMenuItemEdit">
            <?php echo $element['name'];?>
            <input type="hidden" value="<?php echo $element['name']?>" class="name" />            
            <input type="hidden" value="<?php echo $element['cssclass']?>" class="cssclass" />
            <input type="hidden" value="<?php echo $element['linktype']?>" class="linktype" />
            <?php 
            if($element['linktype'] == 'internal'){               
            ?>    
              <input type="hidden" value="<?php echo $element['link']; ?>" class="link" />
            <?php
            }elseif($element['linktype'] == 'custom'){   
            ?>
                <input type="hidden" value="<?php echo $element['link']; ?>" class="link" />               
            <?php  
            }
            ?>
        </div>
        <div class="rightMenuItemEdit">
            <?php if($isUserModule != 1){?>
            <button class="button button-red remove_link removeMenuItem" type="button"><i class="icon-remove icon-white"></i></button>
            <?php }?>
        </div>
    </div>
    <div class="menu-slide"><div class="widget-content form-horizontal">
        <div class="form-row">
            <div class="span6" >
                <label class="field-name menuLabel"><?php echo __('Navigation Label: &nbsp;');?></label>
                <input type="text" class="span12" id="menuListItemName" placeholder="Navigation Label" value="<?php echo $element['name']?>" />
            </div>
            <div class="span6">
                <label class="field-name menuLabel"><?php echo __('Class Name: &nbsp;');?></label>
                <input type="text" class="span12" id="menuListItemClass" placeholder="Classes" value="<?php echo $element['cssclass']?>" />
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="form-row">
            <label class="field-name menuLabel"><?php echo __('Link: &nbsp;');?><a href="#" target="blank"><?php echo $element['link']; ?></a></label>
        </div>
    </div></div>
</div>