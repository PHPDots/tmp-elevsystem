<div class="inner-content">
    <div class="row-fluid">
        <div class="span12">      
            <div class="widget">
                <div class="widget-header">
                    <h5><?php echo __('Activity Log : Search By Date'); ?></h5>
                </div>

                <div class="tableLicense form-horizontal">
                    <div class="form-row">                
                        <div class="span6">
                            <label class="field-name"><?php echo __('From Date'); ?></label>
                            <div class="field">
                                <?php 
                                    echo $this->Form->input('from_date',array(
                                        'label'         => false,
                                        'div'           => null,
                                        'id'            => 'from_date',
                                        'class'         => 'span12 datepicker',
                                        'placeHolder'   => __('From Date'),
                                        'value'         => $from_date
                                    ));
                                    ?>
                               <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="span6">
                            <label class="field-name"><?php echo __('To Date'); ?></label>
                            <div class="field">
                                <?php 
                                    echo $this->Form->input('to_date',array(
                                        'label'         => false,
                                        'div'           => null,
                                        'id'            => 'to_date',
                                        'class'         => 'span12 datepicker',
                                        'placeHolder'   => __('To Date'),
                                        'value'         => $to_date
                                    ));
                                    ?>
                               <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">      
            <div class="widget">
                <div class="widget-header">
                    <h5><?php echo __('Date Wise Results'); ?></h5>
                </div>

                <div class="tableLicense">
                    <table class="table table-bordered table-hover" id="list_data" style="margin-bottom: 0px;">
                        <thead>
                            <tr>
                                <th width="85">Date</th>
                                <th width="200">Type</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3">No Data To Show</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-modal-lg" id="modal_view_modal" role="basic" aria-hidden="true" style="left: 5%; margin-left: 0px; width:90%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <span> &nbsp;&nbsp;Loading... </span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        getData();

        $('.datepicker').datepicker().on('changeDate', function(e) { getData(); });
    });

    function getData(){
        jQuery.ajax({
            type: 'GET',
            url:  "<?php echo $this->Html->url(array('controller'   => 'Activity','action'  => 'getData')) .'?type=all&from_date='; ?>" + jQuery('#from_date').val() + '&to_date=' + jQuery('#to_date').val(),
            dataType : 'JSON',
            beforeSend:function(){
                jQuery('#list_data tbody').html('<tr><td colspan="3" class="text-center"><i class="fa fa-2x fa-spin fa-spinner"></i> Loading Data....</td></tr>');
            },
            success: function(data) {

                if(data.from_date != ''){
                    jQuery('#from_date').val(data.from_date);
                }

                if(data.to_date != ''){
                    jQuery('#to_date').val(data.to_date);
                }

                if(data.status == 'success'){
                    if(data.table != ''){
                        jQuery('#list_data tbody').html(data.table);
                    } else {
                        jQuery('#list_data tbody').html('<tr><td colspan="3" class="text-center">No Data found to show !!</td></tr>');
                    }
                } else {
                    jQuery('#list_data tbody').html('<tr><td colspan="3" class="text-center">No Data found to show !!</td></tr>');
                }
            }
        });
    }
</script>