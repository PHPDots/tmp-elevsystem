<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#PageTitle').blur(function(){
            slug = jQuery(this).val();
            modifiedSlug = slug.toLowerCase().replace(/ /g ,'-');
            jQuery('#PageSlug').val(modifiedSlug);
        });
        
        tinymce.init({
            selector            : "textarea#pagesContent",    
            relative_urls       : false,
            remove_script_host  :false,
            plugins             : [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste responsivefilemanager",
                "code"
            ],
            toolbar             : "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image | code",
            menubar             : false,
            height              : 300,  
            image_advtab        : true ,   
            // external_filemanager_path:"../../../lisabeth/filemanager/",
            // filemanager_title   :"Image Manager" ,
            // external_plugins    : { "filemanager" : "../../../lisabeth/filemanager/plugin.min.js"}
         });
         
         tinymce.triggerSave();
    });
</script>

<div class="inner-content">
    <div class="row-fluid addUserInfo-container">
        <div class="span6">        
            <?php  $title = ($isEdit)?'Edit Documents':'Add Documents'; ?>
            <h5 class="addUserTitle"><?php echo $title; ?></h5>
        </div>
        <div class="clear"></div>
    </div> 
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Page',array(
                'class' => 'form-horizontal',
                'type'  => 'post',
                'type'=>'file'
            ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Page Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <label class="field-name" for="title"><?php echo __('Title'); ?>:</label>
                <div class="field">
                    <span class="span6">
                        <?php 
                        echo $this->Form->input('title',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Title'),
                        ));
                        ?>
                    </span>  
                    <span class="span6">
                        <?php 
                        echo $this->Form->input('slug',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Slug'),
                        ));
                        ?>
                    </span>    
                    <div class="clearfix"></div>
                </div>
            </div>  
            <div class="form-row">
                <label class="field-name" for="title"><?php echo __('Title'); ?>:</label>
                <div class="field">
                    <span class="span6">
                        <?php 
                        echo $this->Form->select('category_code',$category,array(
                            'label'     => false,
                            'div'       => null,
                            'class'     => 'span12',
                            'empty'     => __('Select Category'),
                        ));
                        
                        ?>
                    </span>  
                    <div class="clearfix"></div>
                </div>
            </div>            
            <div class="form-row">
                <label class="field-name"><?php echo __('Body'); ?>:</label>
                <div class="field">
                    <textarea class="span12" id="pagesContent" name="data[Page][body]">
                        <?php echo ($isEdit)?$this->data['Page']['body']:''; ?>
                    </textarea>  
                </div>
            </div>

            <div class="form-row">
                <label class="field-name"><?php echo __('File'); ?>:</label>
                <div class="field">
                        <?php echo  $this->Form->input('filename',array( 'type' => 'file')); ?>
                </div>
            </div>

            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?PHP
                $btnName = ($isEdit)?'Update':'Add';               
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                    'class' => 'button button-green',
                    'type'  => 'submit',
                    'formnovalidate' => TRUE,
                ),
                    array('escape' => FALSE)                            
                );
            ?>           
            <?PHP
                echo $this->Html->link(
                            '<i class="icon-remove icon-white"></i> Cancel',
                            array('action' => 'documents'),
                            array('class' => 'button button-red',
                                'escape' => FALSE,)
                );
            ?>
            </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div></div>
</div>
