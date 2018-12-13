<?php
    setlocale(LC_ALL,'da_DK');
      $danishMonths = Configure::read('danishMonths');
    $week_days = array("Sun"=>"Søndag",
                        "Mon"=>"Mandag",
                        "Tue"=>"Tirsdag",
                        "Wed"=>"Onsdag",
                        "Thu"=>"Torsdag",
                        "Fri"=>"Fredag",
                        "Sat"=>"lørdag",);
    $time = array(
                '6'     => array( 'title'=>'06:00', 'st' => '06:00', 'et' => '07:00' ),
                '7'     => array( 'title'=>'07:00', 'st' => '07:00', 'et' => '08:00' ),
                '8'     => array( 'title'=>'08:00', 'st' => '08:00', 'et' => '09:00' ),
                '9'     => array( 'title'=>'09:00', 'st' => '09:00', 'et' => '10:00' ),
                '10'    => array( 'title'=>'10:00', 'st' => '10:00', 'et' => '11:00' ),
                '11'    => array( 'title'=>'11:00', 'st' => '11:00', 'et' => '12:00' ),
                '12'    => array( 'title'=>'12:00', 'st' => '12:00', 'et' => '13:00' ),
                '13'    => array( 'title'=>'13:00', 'st' => '13:00', 'et' => '14:00' ),
                '14'    => array( 'title'=>'14:00', 'st' => '14:00', 'et' => '15:00' ),
                '15'    => array( 'title'=>'15:00', 'st' => '15:00', 'et' => '16:00' ),
                '16'    => array( 'title'=>'16:00', 'st' => '16:00', 'et' => '17:00' ),
                '17'    => array( 'title'=>'17:00', 'st' => '17:00', 'et' => '18:00' ),
                '18'    => array( 'title'=>'18:00', 'st' => '18:00', 'et' => '19:00' ),
                '19'    => array( 'title'=>'19:00', 'st' => '19:00', 'et' => '20:00' ),
                '20'    => array( 'title'=>'20:00', 'st' => '20:00', 'et' => '21:00' ),
                '21'    => array( 'title'=>'21:00', 'st' => '21:00', 'et' => '22:00' ),
                '22'    => array( 'title'=>'22:00', 'st' => '22:00', 'et' => '23:00' ),
                '23'    => array( 'title'=>'23:00', 'st' => '23:00', 'et' => '00:00' ),
                '24'    => array( 'title'=>'00:00', 'st' => '00:00', 'et' => '01:00' )
            );

    $minutes = array(0, 15, 30, 45, 60);
?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<style type="text/css">
    .row_box{
        border-bottom: 1px solid #ddd;
        padding: 10px;
    }
    .time_slot{
        float: left;
    }
    .time_slot_description{
        margin-left: 15px;
        float: left;
        width: 90%;
        position: relative;
    }
    .padding_bottom{
        margin-bottom: 19px;
    }
    .btn_add{
        text-align: center;

        padding: 10px ;
        background: #fff;

        border: 1px solid #ddd;
        margin-left: 5px;
        width: 110px !important;
    }
    .btn_holder{
        margin: 0px auto;
        padding: 10px 0px;

    }
    .stop-scrolling {
     /* height: 150%;
      overflow: hidden;
    */
    }
    .margin_bottom5{
        margin-bottom: 5px;
    }
    hr {
        margin: 7px 0px 14px !important;
        border: 0;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ffffff;
    }
    .padder{
        padding: 20px !important;
    }
    .btn_add_time{
        background: #30B30F;
        width: 100%;
        text-align: center;
        display: inline-block;
        color: #fff;
        text-transform: uppercase;
    }
    .btn_active{
        background: #30B30F !important;
        color: #fff  !important;
        border: 1px solid #30B30F;  
    }
    #add_data_frm{
        display: none;
    }
    .btn_set_booking{
        background: #fff;
       
        text-align: center;
        display: inline-block;
        color: #30B30F;
        border: 1px solid #ddd;
        text-transform: uppercase;
        padding: 12px !important;
    }
    .btn_active_set{
        background: #30B30F !important;
        color: #fff  !important;
        border: 1px solid #30B30F;
        cursor: pointer;
    }
    .number{
        height: 42px !important;
        font-size: 18px !important;
    }
    .right_0{

    }
    .right_10{

        margin-right: 7px !important;
    }
    .ui-timepicker-container{
        z-index: 99999999999 !important;
    }
    .lbl_padding{
        padding-top: 3px;
        margin:0px; 
    }
    .loader{
        position: absolute;
        top: 70px;
        text-align: center;
        z-index: 9999;
        width: 470px;
       margin-right: 0px auto;
       display: none;
       
    }
    .error_msg{
        color: #c10000;
        font-size: 11px;
        padding: 3px 0px;
        display: none;
    }
    .success-add{
        background: #30B30F !important;
        height: 300px;
        width: 250px;
        text-align:center; 
        padding: 50px !important;
        margin: 0px auto !important;
        display: none;
    }
    .btn_sucess{
        width: 200px !important;
        padding: 7px;
    }
    .txt_sucess{
        color: #fff;
        font-size: 20px;
        text-transform: uppercase;
    }
    .txt_sucess span{
        display: inline-block;
        padding:10px 0px 20px; 
        font-size: 30px;
        text-transform: uppercase;
    }
    .error-add{
        background: #c10000 !important;
        height: 300px;
        text-align:center; 
        padding: 50px 20px 30px !important;
        margin: 0px auto !important;
        display: none;
    }
    .txt_error{
         color: #fff;
        font-size: 12px;
        text-transform: uppercase;
    }
        

    .txt_error span{
        display: inline-block;
        padding:10px 0px 30px; 
        font-size: 35px;
        text-transform: uppercase;
    }
    #dialog-add{
        display: none;
    }

    .booking-type {
        position: absolute;
        right: 0px;
        padding: 0 10px;
    }

    .booking-slot {
        position: absolute;
        width: 100%;
    }

    .booking-Køretime {
        border: 1px solid green;
    }

    .booking-Køretime .booking-type {
        background-color: green;
        color: #ffffff;
    }

    .booking-Teori {
        border: 1px solid #4B77BE;
    }

    .booking-Teori .booking-type {
        background-color: #4B77BE;
        color: #ffffff;
    }

    .booking-Køreprøve {
        border: 1px solid black;
    }

    .booking-Køreprøve .booking-type {
        background-color: black;
        color: #ffffff;
    }

    .booking-Privat {
        border: 1px solid #f3c200;
    }

    .booking-Privat .booking-type {
        background-color: #f3c200;
        color: #ffffff;
    }

    .tmp-rana {
        height: 10px;
    }
    .cal_link{
            font-size:  18px;
            padding: 8px 15px;
           display: inline-block;
           border:1px solid #ddd;
           margin-right: 10px !important;
            color:#000;  
    }
    .box_ic{

        border:1px solid #ddd;
        padding: 8px 15px;
        font-size: 18px; 
        color:#000;
        font-weight: bold;
    }
    .heaing_tbl{
        background-color: #349c1a;
        color: #ffffff;
        font-size: 20px;
        padding:10px 5px; 

    }
    .cal_box{
        box-shadow: 1px 2px 2px 5px #ddd;
        
    }
    .tbl_view{

        padding:5px;
    }
    .table-cal { 
        width: 100%;
        border-collapse: separate;
        border-spacing: 5px 5px;

     }
     .days_td{
         height: 40px;
         padding: 0px !important;

     }
    .days_text{
        margin:  3px;
        font-size: 16px !important;
        padding: 5px  !important;
    }
     .table-cal td{
        border:1px solid #ddd;
        padding: 35px 10px;
        font-size: 35px;
        text-align: center;

     }
     .table-cal td a{

        color:#000;

     }
     .heading_cal_1{
        font-weight: bold;
        font-size: 22px;

     }
     .current_day{
        color:  #30B30F;
     }
       .active_day{
        background-color: #ccc;  
     }
</style>




<div class="inner-content">

           
           
    <div class="row-fluid">
    <div>
    <form action="<?php echo $this->Html->url(array('controller'=>'adminbookings','action'=>'TeacherListBookings')); ?>" method="get">
        <label>Vælg lærer for at se kalender visning : </label> 

            <select onchange="this.form.submit();" name="teacher" id="teacher_sel">
                <option value="">Select Teacher</option>
                <?php if(count($teachers_list)>0){
                        foreach($teachers_list as $id=>$teacher){
                            $selected = "";
                            if($_GET['teacher']){
                                if($id == $_GET['teacher']){
                                    $selected = "selected='selected'";
                                }else{
                                    $selected = "";
                                }
                            }
                            ?>
                             <option <?php echo $selected ;?> value="<?php echo $id;?>">
                                 <?php echo $teacher['name']?>
                             </option>       
                            <?php
                        }
                }
                ?>
            </select> 
            <?php 
            echo $this->Html->link(__('Eller vis alle i en liste'),array(
                'controller'    => 'adminbookings',
                'action'        => 'getTeacherBookings'
            ));
            ?>

            </form>
    </div>
    <?php if(isset($_GET['teacher']) && $_GET['teacher']>0){ ?>
    <?php if($calview){  
     $q = "";
         if($_GET['teacher']){
            $q = '&teacher='.$_GET['teacher'];
            echo "<div class='alert'>Resultater fundet for <strong>" .$teachers_list[$_GET['teacher']]['name']."</strong></div>";    
         }
    $current_month = date("n");

    $month = (isset($_GET['m'])) ? $_GET['m'] : date("n");
    $year = (isset($_GET['y'])) ? $_GET['y'] : date("Y");

    $previous_month = ($month - 1);
    $next_month = ($month + 1);

    $previous_year = $year;
    $next_year = $year;

    if($previous_month==0)
    {
        $previous_month = 12;
        $previous_year = $year-1;
    }

    if($next_month>12)
    {
        $next_month = 1;
        $next_year = $year+1;
    }




        $date = (!isset($_GET['m']) && !isset($_GET['y'])) ? time() : strtotime($_GET['m'] . '/1/' . $_GET['y']);
            

        /* Set the date */
       

        $day = date('d', $date);
        $month = date('m', $date);

         $m = date('F', $date);
        $year = date('Y', $date);
      
        $firstDay = mktime(0,0,0,$month, 1, $year);
        $title = strftime('%B', $firstDay);
        $dayOfWeek = date('D', $firstDay);
        $daysInMonth = cal_days_in_month(0, $month, $year);
        switch ($dayOfWeek) {
                case "Mon": $blank = 0;
                    break;
                case "Tue": $blank = 1;
                    break;
                case "Wed": $blank = 2;
                    break;
                case "Thu": $blank = 3;
                    break;
                case "Fri": $blank = 4;
                    break;
                case "Sat": $blank = 5;
                    break;
                case "Sun": $blank = 6;
                    break;
            }
        /* Get the name of the week days */
        $timestamp = strtotime('next Monday');
        $weekDays = array();
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = strftime('%a', $timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        $blank = date('w', strtotime("{$year}-{$month}-01"));
        ?>
        <div class="span12" style="padding-bottom: 15px;">
            <div style="float:left;width:20%">
                <div  class="  heading_cal_1 "><?php 
               
                echo  $danishMonths[$m];?> <?php echo $year;?></div>

            </div>
        
            <div style="float:right;text-align: right; margin-top: -23px;" >
                
                <a style="margin-top:15px"  href="<?php echo $this->request->webroot . 'Bookings/TeacherListBookings?calview=yes&m=' .$previous_month; ?>&y=<?php echo $previous_year.$q?>" class="box_ic fa fa-angle-left " ></a>
                <a href="<?php echo $this->request->webroot . 'Bookings/TeacherListBookings?calview=yes&m=' .$next_month; ?>&y=<?php echo $next_year.$q?>"  class="  box_ic fa fa-angle-right "></a>
            </div>
             <div style="clear:both"></div>
        </div>
        <div style="clear:both"></div>
        <div class="cal_box">

            <div class="heaing_tbl">DATO</div>
                 <div   class="tbl_view">
                    <table cellspacing="10" border="0" class=' table-cal' style="table-layout: fixed;">
                   
                    <tr>
                        <?php foreach($weekDays as $key => $weekDay) : ?>
                            <td class="text-center days_td">
                                    <div class="days_text">
                                            <?php echo $week_days[$weekDay]; ?>
                                     </div>                   
                            </td>
                        <?php endforeach ?>
                    </tr>
                    <tr>
                        <?php for($i = 0; $i < $blank; $i++): ?>
                            <td></td>
                        <?php endfor; ?>
                        <?php for($i = 1; $i <= $daysInMonth; $i++): ?>
                           
                                <td <?php if($day == $i){ echo "class='active_day'";} ?>>
                                    <a href="<?php echo $this->request->webroot . 'Bookings/TeacherListBookings?date=' .$i; ?>-<?php echo $month?>-<?php echo $year.$q?>"><?php echo $i ?></a>
                                </td>
                           
                            <?php if(($i + $blank) % 7 == 0): ?>
                                </tr><tr>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php for($i = 0; ($i + $blank + $daysInMonth) % 7 != 0; $i++): ?>
                            <td></td>
                        <?php endfor; ?>
                    </tr>
                    </table>
                </div>
        </div>
<?php } else {
         $q = "";
         if($_GET['teacher']){
            $q = '&teacher='.$_GET['teacher'];
            echo "<div class='alert'>Resultater fundet for <strong>" .$teachers_list[$_GET['teacher']]['name']."</strong></div>";    
         }
    ?>

        <div style="text-align: right"  class=" padding_bottom ">
            <a href="<?php echo $this->request->webroot . 'Bookings/TeacherListBookings?calview=yes'. $q ; ?>" class="fa  fa-3x cal_link" >Hop til dato</a>
            <a style="margin-top:15px" href="<?php echo $this->request->webroot . 'Bookings/TeacherListBookings?date=' . date('d-m-Y', strtotime($prev_date)). $q ; ?>" class="box_ic fa fa-angle-left " ></a>
            <a href="<?php echo $this->request->webroot . 'Bookings/TeacherListBookings?date=' . date('d-m-Y', strtotime($next_date . ' +1 days')).$q ; ?>"  class="  box_ic fa fa-angle-right "></a>
        </div>

        <div class="">
            <div class="span6">
                <div class="widget">
                    <div class="widget-header">
                        <h5>
                            <?php
                              
                                echo date('d',  strtotime($date)).' '.$danishMonths[date('F',  strtotime($date))].', '.date('Y',  strtotime($date));
                            ?>
                        </h5>
                    </div>
                    
                    <?php foreach($time as $k=>$t) { ?>
                        <div class="row-fluid">
                            <div class="row_box">
                                <div class="time_slot"> 
                                    <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo $date;?>"><?php echo $t['title'];?></a>
                                </div>

                                <div class="time_slot_description">
                                    <?php /*foreach ($minutes as $min) { ?>
                                        <div class="tmp-rana <?php echo $k . '-' . $min ?>"></div>
                                    <?php }*/ ?>
                                    <?php
                                        $has = false;
                                        if( $current_bookings && count( $current_bookings ) ) {
                                            foreach ($current_bookings as $current) {
                                                $cst = strtotime( $date . ' ' . $t['st'] );
                                                $cet = strtotime( $date . ' ' . $t['et'] );
                                                $bst = strtotime( $current['date'] . ' ' . $current['start_time'] );
                                                $bet = strtotime( $current['date'] . ' ' . $current['end_time'] );

                                                if( $bst > $cst && $bst < $cet ) {
                                                    $has = true;
                                                    echo '<div class="booking-slot booking-'. $current['booking_type'] .'">';
                                                    echo '<span class="booking-type">' . $current['booking_type'] . '</span>';
                                                    echo $current['start_time'] . ' - ' . $current['end_time'] . '</br>';
                                                    echo $current['note'];
                                                    echo '</div>';
                                                }
                                            }
                                        }

                                        if( $has == false ) {
                                            foreach ($minutes as $min) {
                                                echo '<div class="tmp-rana '. $k . '-' . $min .'"></div>';
                                            }
                                        }
                                    ?>
                                </div>

                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="span6">
                <div class="widget">
                    <div class="widget-header">
                        <h5>
                            <?php
                                $danishMonths = Configure::read('danishMonths');
                                echo date('d',  strtotime($next_date)).' '.$danishMonths[date('F',  strtotime($next_date))].', '.date('Y',  strtotime($next_date));
                            ?>
                        </h5>
                    </div>

                    <?php foreach(  $time as $t) { ?>
                        <div class="row-fluid">
                            <div class="row_box">
                                <div class="time_slot">    
                                    <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo $next_date;?>"><?php echo $t['title'];?></a>
                                </div>
                                <div class="time_slot_description">
                                    <?php /*foreach ($minutes as $min) { ?>
                                        <div class="tmp-rana <?php echo $k . '-' . $min ?>"></div>
                                    <?php }*/ ?>
                                    <?php
                                        $has = false;
                                        if( $next_bookings && count( $next_bookings ) ) {
                                            foreach ($next_bookings as $next) {
                                                $cst = strtotime( $next_date . ' ' . $t['st'] );
                                                $cet = strtotime( $next_date . ' ' . $t['et'] );
                                                $bst = strtotime( $next['date'] . ' ' . $next['start_time'] );
                                                $bet = strtotime( $next['date'] . ' ' . $next['end_time'] );

                                                if( $bst > $cst && $bst < $cet ) {
                                                    $has = true;
                                                    echo '<div class="booking-slot booking-'. $next['booking_type'] .'">';
                                                    echo '<span class="booking-type">' . $next['booking_type'] . '</span>';
                                                    echo $next['start_time'] . ' - ' . $next['end_time'] . '</br>';
                                                    echo $next['note'];
                                                    echo '</div>';
                                                }
                                            }
                                        }

                                        if( $has == false ) {
                                            foreach ($minutes as $min) {
                                                echo '<div class="tmp-rana '. $k . '-' . $min .'"></div>';
                                            }
                                        }
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
        <?php } ?>
        <?php } ?>
    </div>
</div>      