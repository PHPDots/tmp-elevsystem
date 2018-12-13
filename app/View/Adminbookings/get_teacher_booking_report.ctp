<div class="inner-content">
    <div class="row-fluid"><div class="span12">      
        <div class="widget">
            <div class="widget-header">
                <h5>Lærer Timer Rapport</h5>
            </div>
            <div class="tableLicense form-horizontal">
                <div class="form-row" style="">
                    <div class="span6 formField" id="date_from">
                        <label class="span4 control-label" id="labelDateFrom">fra dato:</label>
                        <div class="span8">
                            <input name="date_from" id="startDate" class="span12 datepicker" minyear="50" maxyear="0" value="<?php echo date('d.m.Y'); ?>" type="text">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="span6 formField" id="date_to">
                        <label class="span4 control-label" id="labelDateTo">Dato Til:</label>
                        <div class="span8">
                            <input name="date_to" id="endDate" class="span12 datepicker" minyear="50" maxyear="0" value="<?php echo date('d.m.Y'); ?>" type="text">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <table cellpading="0" cellspacing="0" border="0" class="default-table" id="list-data">
                    <thead>
                        <tr>
                            <th align="left">Teacher</th>
                            <th align="left">City</th>     
                            <th align="right">Køretimer</th>     
                            <th align="right">Køreprøver</th>
                            <th align="right">Kørelektioner total</th>
                            <th align="right">Teori</th>
                            <th align="right">Baner</th>
                            <th align="right">Andet</th>
                            <th align="right">T/B/A Total</th>
                            <th align="right">Total all</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="10">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(e){
        getData();

        jQuery(document).on('change', '.datepicker', function(e) {
            getData();
        });
    });

    function getData(){
        var $from_date  = jQuery('#startDate').val();
        var $end_date   = jQuery('#endDate').val();

        jQuery.ajax({
            url         : '<?php echo $this->Html->url(array('controller'=>'adminbookings','action'=>'getTeacherBookingReportData')); ?>?from_date=' + $from_date  + '&end_date=' + $end_date,
            dataType    : "html",
            beforeSend  : function() {
                jQuery('#list-data tbody').html('<tr><td colspan="10"><i class="fa fa-spin fa-2x fa-cog"></i></td></tr>');
            },
            success     : function(data) {
                jQuery('#list-data tbody').html( data);
            }
        });
    }
</script>