<div class="span6">
    <div class="widget">
        <div class="widget-header">            
            <h5>
                <?php echo __("System Statistics")?>
            </h5>            
        </div>
        <div class="widget-content tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">     
                <thead>
                    <tr>
                        <th align="left"><?php echo __('Status'); ?></th>
                        <th align="left"><?php echo __('Count'); ?></th>
                    </tr>
                </thead>
                <tbody>   
                    <tr class="even">
                        <td><?php echo __('Family'); ?></td>
                        <td><?php echo $systemStatistics['familyCount']; ?></td>                      
                    </tr> 
                    <tr class="odd">
                        <td><?php echo __('Male'); ?></td>
                        <td><?php echo $systemStatistics['male']; ?></td>                      
                    </tr> 
                    <tr class="even">
                        <td><?php echo __('Female');; ?></td>
                        <td><?php echo $systemStatistics['female']; ?></td>                      
                    </tr> 
                    <tr class="odd">
                        <td><?php echo __('Total Members'); ?></td>
                        <td><?php echo $systemStatistics['memberCount']; ?></td>                      
                    </tr> 
                </tbody>
            </table>
        </div>  
    </div>
</div>
