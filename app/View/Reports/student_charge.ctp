<?php $this->append('script'); ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(document).ready(function() {
            jQuery('#example').DataTable(); 
        });
    });
</script>
<?php $this->end(); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'           => $this->request->query['report_type'],                
                'student_id'            => isset($this->request->query['student_id'])?$this->request->query['student_id']:'',
                'csv'                   => 'true',
            )),array(
                'class'     => 'button button-green',           
                'escape'    => FALSE,
        ));
        ?>
    </div>
    <div class="clearfix"></div>   
    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php 
            $title  = __('Student Lesson Amount'); 
            $title .= (isset($this->request->query['student_id']) && !empty($this->request->query['student_id'])) ? ' '.$this->request->query['student_autosuggest'] : '';
            echo $title;
            ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table" id="example">
                <thead>
                <tr>
                    <th><?php echo __('No.');?></th>
                    <?php if(!isset($this->request->query['student_id']) || empty($this->request->query['student_id'])) { ?>
                    <th align="left"><?php echo __('Student Name');?></th>
                    <?php } ?>
                    <th align="left"><?php echo __('Area');?></th>
                    <th align="left"><?php echo __('Category'); ?></th>
                    <th align="left"><?php echo __('Date'); ?></th>
                    <th align="left"><?php echo __('Amount');?></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $i = 1;
                if(!empty($studentAmount)) {
                    foreach($studentAmount as $student) {
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <?php if(!isset($this->request->query['student_id']) || empty($this->request->query['student_id'])) { ?>
                            <td align="left">
                                <?php echo $student['name']; ?>
                            </td>
                            <?php } ?>
                            <td align="left">
                                <?php echo ($student['category'] == 'Booked Track') ? $areaListArr[$student['area']] : Inflector::humanize($student['text']).' '.__('Lesson'); ?>
                            </td>
                            <td align="left">
                                <?php echo $student['category']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['date']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['price']; ?>
                            </td>
                        </tr>
                        <?php   
                        $i++;
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Pending Student Charges.'); ?></td>
                    </tr>
                <?php 
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div></div></div>