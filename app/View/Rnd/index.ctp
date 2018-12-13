<?php $this->append('script'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#printReport').click(function(){
            event.preventDefault();
            window.open('<?php echo $this->Html->url(array('action' => 'index','print')); ?>', "_blank", "width=1300,height=500,scrollbars=yes");
        });
    });
</script>
<?php $this->end(); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <?php
        echo $this->Form->button('<i class="fa fa-print"></i> &nbsp; Print',array(
            'type'  => 'button',
            'class' => 'button button-green pull-right',
            'id'    => 'printReport'
        ));
    ?>
    <div class="clearfix"></div>
    <div class="widget" id="tablePrint">
    <div class="widget-header">
        <h5><?PHP echo __('Receipt Statistics'); ?></h5>             
    </div>
    <div class="tableLicense">
        <table cellpading="0" cellspacing="0" border="0" class="default-table">                
            <?php                
            $i=0;
            if(!empty($statistics)) {          
                foreach($statistics as $statistic){                
            ?>
            <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>">                      
                <?php  foreach($statistic as $value){  ?>                        
                    <td>
                        <?PHP echo $value; ?>
                    </td>                                                                
                <?php } ?>                
            </tr>              
            <?php } } ?>               
        </table>
    </div>
</div>
</div></div></div>
