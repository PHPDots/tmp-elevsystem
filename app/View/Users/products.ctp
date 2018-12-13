<div class="inner-content"><div class="row-fluid"><div class="span12">    
    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php   echo __('Products'); ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table width="100%" style="font-size:14px;margin-top:20px;" id="example">                
                <tr class="table_heading">
                    <th><?php echo __('No.');?></th>                               
                    <th align="left"><?php echo __('Product Name'); ?></th>
                    <th align="left"><?php echo __('Activity Number'); ?></th>
                    <th align="left"><?php echo __('Price'); ?></th>                    
                </tr>                
                <?php 
                $i = 1;
                if(!empty($products)) {
                    foreach($products as $product) {
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center"><?php echo $i; ?></td>
                            <td align="left"><?php echo $product['Product']['name']; ?></td>
                            <td align="left"><?php echo $product['Product']['activity_number']; ?></td>
                            <td align="left"><?php echo $product['Product']['price']; ?></td>                    
                        </tr>
                        <?php   
                        $i++;
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No products availabel.'); ?></td>
                    </tr>
                <?php 
                }
                ?>               
            </table>
        </div>
    </div>
</div></div></div>