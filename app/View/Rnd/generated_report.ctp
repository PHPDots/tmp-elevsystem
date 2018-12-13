<script type="text/javascript">
        window.print();                        
</script>
<table cellpading="0" cellspacing="0" border="0" style="width: 100%;">                
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