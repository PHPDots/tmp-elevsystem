<div class="inner-content">
    <div class="row-fluid"><div class="span12">  
        <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'   => $this->request->query['report_type'],
                'datetime_from' => $this->request->query['datetime_from'],
                'datetime_to'   => $this->request->query['datetime_to'],
                'csv'           => 'true',
            )),array(
                'class'     => 'button button-green',
                'escape'    => FALSE,
        ));
        ?>
    </div>    
     <div class="clearfix"></div>
    
        <div class="widget">
            <div class="widget-header">
                <h5>Lærer Timer Rapport</h5>
            </div>
            <div class="tableLicense form-horizontal">


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
                        <?php 
                        if( !empty($details) ) {
            $html = '';

            foreach ($details as $detail) { 
                $html .= '<tr>';
                    $html .= '<td>' . $detail['name'] . '</td>';
                    $html .= '<td>' . $detail['city'] . '</td>';

                    $koretime = ( !empty($detail['status']['kretime']) ) ? $detail['status']['kretime'] / 60 : 0;
                    $koreprove = ( !empty($detail['status']['kreprve']) ) ? $detail['status']['kreprve'] / 60 : 0;

                    $html .= '<td align="right">' . $koretime . '</td>';
                    $html .= '<td align="right">' . $koreprove . '</td>';

                    $html .= '<td align="right"><strong>' . ($koretime + $koreprove)  . '</strong></td>';

                    $teori = ( !empty($detail['status']['teori']) ) ? $detail['status']['teori'] / 60 : 0;
                    $privat = ( !empty($detail['status']['privat']) ) ? $detail['status']['privat'] / 60 : 0;
                    $track = ( !empty($detail['status']['track']) ) ? $detail['status']['track'] / 60 : 0;

                    $html .= '<td align="right">' . $teori . '</td>';
                    $html .= '<td align="right">' . $track . '</td>';
                    $html .= '<td align="right">' . $privat . '</td>';
                    $html .= '<td align="right"><strong>' . ($teori + $track + $privat)  . '</strong></td>';

                    $html .= '<td align="right"><strong>' . ($koretime + $koreprove + $teori + $track + $privat)  . '</strong></td>';
                $html .= '</tr>';
            }
            echo $html;
        }else{ ?>
                        <tr>
                            <td colspan="10">No Records Found</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
