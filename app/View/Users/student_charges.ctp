<?php $this->append('script'); ?>
<script type="text/javascript">
//    jQuery(document).ready(function() {
//        jQuery(document).ready(function() {
//            jQuery('#example').DataTable(); 
//        });
//    });
</script>
<?php $this->end(); ?>
<?php $User       = CakeSession::read("Auth.User"); ?>
<div class="col-xs-12 col-sm-12 info">
        <p>
            Nedenfor kan du hvilke ydelser vi har registreret på din elev konto.
Vi gør opmærksom på at der kan opstå tidsforskydninger i registrering af ydelser og betalinger,
og at hjemmesiden er under opbygning og test, hvorfor din saldo vil blive endeligt opgjort af
bogholderiet der har de faktuelle tal. 
        </p>
</div>
<div class="clearfix"></div>
<div class="inner-content"><div class="row-fluid"><div class="span12">  

    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php 
			$city_data['city_code'] = 71;
            $title  = __('Elev lektion saldo'); 
            $title .= (isset($this->request->query['student_id']) && !empty($this->request->query['student_id'])) ? ' '.$this->request->query['student_autosuggest'] : '';
            echo "<span>".$title."</span>";
            $title = (isset($city_data) && !empty($city_data)) ? '+'.$city_data['city_code']."  ".$User['student_number']."  ".$city_data['fik'] : '';            
            ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table width="100%" border="0" style="font-size:14px;margin-top:20px;" id="example">                
                <tr class="table_heading">
                    <th><?php echo __('Stk');?></th>
                    <th align="left"><?php echo __('Ydelse'); ?></th>
                    <th align="left"><?php echo __('Dato'); ?></th>
                    <th style="text-align: right;"><?php echo __('Pris');?></th>
                </tr>                
                <?php 
                $i = 1;
                if(!empty($studentAmount)) {
                    $g_total = 0;
                    foreach($studentAmount as $student) {
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="left">
                                <?php echo $student['count']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['text']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['date']; ?>
                            </td>
                            <td align="right">
                                <?php echo $student['price']; ?>
                            </td>
                        </tr>
                        <?php   
                        // $g_total += floatval(preg_replace('/[^\d.]/', '', $student['price']));
						$student['price'] = str_replace(',','', $student['price']);
                        $totalPrice = number_format($student['price'], 2, '.', '');
                        $g_total +=  $totalPrice;						
                        $i++;
                    } ?>
                    <tr class="table_heading"><td colspan="4"></td></tr>
                    <tr >
                        <td></td>
                        <td>Ydelser i alt</td>
                        <td></td>
                        <td align="right">							
							<?php echo number_format($g_total,2 , '.', ''); ?>
						</td>
                    </tr>
                    <!-- <tr>
                        <?php $bal = $User['balance'] - $g_total; ?>
                        <td></td>
                        <td>Betalinger</td>
                        <td></td>
                        <td align="right"><?php echo $bal; ?></td>
                    </tr> -->
                    <!-- <tr>
                        <td></td>
                        <?php if($bal < 0){ ?>
                        <td>Du mangler at betale</td>
                        <?php }else{ ?>
                        <td>Disponibelt til ydelser</td>
                        <?php } ?>
                        <td></td>
                        <td align="right"><?php echo $User['balance']; ?></td>
                    </tr> -->
                <?php } else { ?>
                    <tr>
                        <td colspan="4" align="center" class="index_msg"><?php  echo __('No Pending Student Charges.'); ?></td>
                    </tr>
                <?php 
                } 
                ?>               
            </table>
        </div>

        <div class="widget-header">
            <h5>Seneste Betalinger</h5>  
        </div>
        <div class="tableLicense">
            <table width="100%" style="font-size:14px;margin-top:20px;" cellpading="0" cellspacing="0" border="0" >
                <tr class="table_heading">
                    <th align="left">Dato</th>
                    <th style="text-align: right;">Beløb</th>
                </tr>

                <?php $i=0;
                $g_total_1 = 0;
                if(!empty($Payments)){
                foreach ($Payments as $key => $Payment) {
                    $Payment = (object)$Payment['LatestPayments']; ?>
                    
                    <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?> " align="center" >
                        <td align="left">
                            <?php echo date('d.m.Y',strtotime($Payment->PosteringsDato)); ?>
                        </td>
                        <td align="right">
                            <?php echo $tmp_g_total_1 = round($Payment->Kredit); ?>
                            <?php $g_total_1 = $g_total_1 + $tmp_g_total_1; ?>
                        </td>
                    </tr>
                            
                <?php } ?>
                <tr class="table_heading"><td colspan="4"></td></tr>
                    <tr >
                        <td>Faktisk afholdt saldo</td>
                        <td align="right"><?php echo $g_total - $g_total_1; ?></td>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td colspan="6" align="center" class="index_msg"><?php  echo __('Ingen Seneste Betalinger'); ?></td>
                    </tr>
                <?php                   
                }
                ?>
            </table>
        </div>
        <div class="widget-header">
            <h5>Bestilte endnu ikke afholdte ydelser (futiure bookings)</h5>  
        </div>
        <div class="tableLicense">
            <table width="100%" style="font-size:14px;margin-top:20px;" cellpading="0" cellspacing="0" border="0">
                <tr class="table_heading">
                    <th align="left"><?php echo __('Stk');?></th>
                    <th align="left"><?php echo __('Area');?></th>
                    <th align="left"><?php echo __('Dato');?></th>
                    <th style="text-align: right;"><?php echo __('Price');?></th>
                </tr>
                <?php                
                $i=0;
                $gtotal = 0;
                if(!empty($Systembooking)) {
                    foreach($Systembooking as $booking){
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?> " align="center" >
                            <td align="left">
                                <?php echo $type = ($booking['Systembooking']['lesson_type']  != '') ? $booking['Systembooking']['lesson_type'] : '1' ;?>
                            </td>
                            <td align="left">
                                <?php 
                                $type1 = ($type != '') ? $type." x " : '';
                                echo $type1.ucfirst($booking['Systembooking']['booking_type']); ?>
                            </td>
                            <td align="left">
                                <?php echo date('d.m.Y',strtotime($booking['Systembooking']['start_time'])); ?>
                            </td>
                            <td align="right">
                                <?php echo $total =  $type*500;
                                $gtotal = $gtotal + $total;
                                 ?>
                            </td>
                           
                        </tr>
                    <?php
                    } ?>
                    <tr class="table_heading"><td colspan="4"></td></tr>
                    <tr >
                        <td>Fremtidige ydelse i alt</td>
                        <td></td>
                        <td></td>
                        <td align="right"><?php echo $gtotal; ?></td>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td colspan="5" align="center" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
                    </tr>
                <?php                   
                }
                ?>
            </table>
        </div>
        <div class="widget-header">
            <h5>Din saldo i alt
                <span style="float: right;">
                    <?php echo ($g_total - $g_total_1) + $gtotal; ?>
                </span>
            </h5>  
        </div>
        <div class="widget-header">
            <?php 
                echo "<span>FIK/Girokort code : ".$title."</span>";
            ?>
        </div>
        <!-- <div class="widget-header">
            <h5><?php echo __('Ydelser'); ?></h5>  
        </div>
        <div class="tableLicense">
            <table  width="100%" style="font-size:14px;margin-top:20px;" cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr class="table_heading">
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Tekst');?></th>
                    <th align="left"><?php echo __('PosteringsDato');?></th>
                    <th align="left"><?php echo __('Antal');?></th>
                    <th align="right"><?php echo __('SatsInclMoms');?></th>
                    <th align="right"><?php echo __('BeloebInclMoms');?></th>
                </tr>
                <?php $i=0; $UserServices_total = 0;
                if(!empty($UserServices)) {
                    foreach($UserServices as $UserService){ ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?> " align="center" >
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <td align="left">
                                <?php echo $UserService['UserServices']['description']; ?>
                            </td>
                            <td align="left">
                                <?php echo date('d.m.Y',strtotime($UserService['UserServices']['posting_date'])); ?>
                            </td>
                            <td align="left">
                                <?php echo number_format($UserService['UserServices']['qty'], 2, '.', ''); ?>
                            </td>
                            <td align="right">
                                <?php echo number_format($UserService['UserServices']['price'], 2, '.', ''); ?>
                            </td>
                            <td align="right">
                                <?php 
                                echo $total_price = number_format($UserService['UserServices']['total_price'], 2, '.', '');
                                $UserServices_total +=  $total_price; ?>
                            </td>
                        </tr>
                    <?php
                    }
                }else{ ?>
                    <tr>
                        <td colspan="6" align="center" class="index_msg"><?php  echo __('Ingen Ydelser tilføjet.'); ?></td>
                    </tr>
                <?php                   
                }
                ?>
            </table>
            <?php if($UserServices_total > 0){ ?>
                <div class="widget-header">
                    <h5><?php echo __('Total'); ?></h5>  
                    <h5 style="float: right;padding-right: 10px;"><?php echo number_format($UserServices_total,2 , '.', ''); ?></h5>
                </div>
            <?php } ?>
        </div> -->
    </div>
</div></div></div>