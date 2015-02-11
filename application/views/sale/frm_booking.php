<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnSale").addClass("active");
        $("#wrapper").toggleClass("toggled");
        $("ol.progtrckr").each(function () {
            $(this).attr("data-progtrckr-steps",
                    $(this).children("li").length);
        });
        var schedules_id = '<?= $schedules_id ?>';
        if (schedules_id !== '') {
            var timer;
            $('#select_time').addClass('animated fadeOutLeft');
            $('#select_time').removeAttr('class');
            $('#select_time').addClass('col-lg-4 animated');
            if (timer)
                clearTimeout(timer);
            timer = setTimeout(function () {
                $('#select_seat').removeAttr('class');
                $('#select_seat').addClass('col-lg-4');
                $('#ticket_info').removeAttr('class');
                $('#ticket_info').addClass('col-lg-4');
            }, 500 / 2);
            timer = setTimeout(function () {
                $('#step3').removeAttr('class');
                $('#step3').addClass('progtrckr-done');
            }, 1000);
        }

    });
    var schedules_id = '<?= $schedules_id ?>';
    var NumberSeat = '';
    if (schedules_id !== '') {
        NumberSeat = parseInt('<?= $schedule_select['NumberSeat'] + $schedule_select['NumberTicketsExtra'] ?>');
    }
    function addSeat(seat_no) {
        var tb_ticket_body = $('#ticket_guest tbody');
        var seat_info = $('#seat_info_' + seat_no);
        var num = $('#tb_ticket_sale tbody tr').length;
        var price = $('#Price').val();
        var price_dis = $('#PriceDicount').val();
        var fare = '<select id="FareType' + num + '" name="PriceSeat[]" class="form-control" onchange="calTotalFare()" >';
        fare += '<option value="' + price + '" selected="" >' + price + ' (เต็ม)</option>';
        fare += '<option value="' + price_dis + '">' + price_dis + ' (ลด)</option>';
        fare += '</select>';
        var row = '<tr id ="row_' + seat_no + '">';
        row += '<td class="text-center" id ="' + seat_no + '" >' + seat_no;
        row += '<input type="hidden" name="Seat[]" value="' + seat_no + '">';
        row += '</td>';
        row += '<td class="text-center">' + fare + '</td>';
        row += '<td class="text-center">';
        row += '<button type="button" class="btn btn-sm btn-danger" onclick="removeSeat(\'' + seat_no + '\')">';
        row += '<i class="fa fa-times"></i>';
        row += '</button>';
        row += '</td>';
        row += '</tr>';
        if (document.getElementById(seat_no) === null && booking(seat_no) !== 'false') {

            document.getElementById("user_action").innerHTML = schedules_id + ' กำลังเลือก ' + seat_no;
            document.getElementById("debug").style.background = 'green';
            $('#guest_info').removeAttr('class');
            $('#guest_info').addClass('col-lg-12 animated zoomIn');

            var seat_id = $('#seat_' + seat_no);
            seat_id.removeClass('bg-blank');
            seat_id.addClass('bg-reserve');
            tb_ticket_body.append(row);
            var info = '<i class="fa fa-user fa-2x"></i><br>';
            info += '<span class="badge badge-warning">';
            info += $('#DestinationName').val();
            info += '</span>';
            seat_info.html(info);
            calTotalFare();
        }
        return true;
    }
    function addSeatExtra() {
        var tb_ticket_body = $('#ticket_guest tbody');
        var seat_no = NumberSeat + 1;
        var row = '<tr id ="row_' + seat_no + '">';
        var num = $('#tb_ticket_sale tbody tr').length;
        var price = $('#Price').val();
        var price_dis = $('#PriceDicount').val();
        var fare = '<select id="FareType' + num + '" name="PriceSeat[]" class="form-control" onchange="calTotalFare()" >';
        fare += '<option value="' + price + '" selected="" >' + price + ' (เต็ม)</option>';
        fare += '<option value="' + price_dis + '">' + price_dis + ' (ลด)</option>';
        fare += '</select>';

        row += '<td class="text-center" id ="' + seat_no + '" >' + seat_no;
        row += '<input type="hidden" name="Seat[]" value="' + seat_no + '">';
        row += '</td>';
        row += '<td class="text-center">' + fare + '</td>';
        row += '<td class="text-center">';
        row += '<button type="button" class="btn btn-sm btn-danger" onclick="removeSeatExtra(\'' + seat_no + '\')">';
        row += '<i class="fa fa-times"></i>';
        row += '</button>';
        row += '</td>';
        row += '</tr>';
        if (document.getElementById(seat_no) === null && booking(seat_no) !== 'false') {
            $('#guest_info').removeAttr('class');
            $('#guest_info').addClass('col-lg-12 animated');
            document.getElementById("user_action").innerHTML = 'เพิ่มที่นั่งเสริม -> ' + seat_no;
            document.getElementById("debug").style.background = 'gray';
            tb_ticket_body.append(row);
            var seat = '<div id="seat_' + seat_no + '"  class="col-xs-3 seat-extra">';
            seat += '       <div onclick="" ondblclick="removeSeatExtra(\'' + seat_no + '\')" class="seat bg-reserve">';
            seat += '           <span class="seat-info" id="seat_info_' + seat_no + '"> ';
            seat += '               <i class="fa fa-user"></i><br>';
            seat += '               <span class="badge badge-warning" style="font-size: 8pt;">';
            seat += $('#DestinationName').val();
            seat += '               </span> ';
            seat += '           </span>';
            seat += '       </div>';
            seat += '</div>';

            $('#SeatExtra').append(seat);
            NumberSeat = NumberSeat + 1;
            calTotalFare();
        }
    }
    function removeSeat(seat_no) {
        document.getElementById("user_action").innerHTML = 'ยกเลิกที่นั่ง ' + seat_no;
        document.getElementById("debug").style.background = 'red';
        var row_id = 'row_' + seat_no;
        var seat_id = $('#seat_' + seat_no);
        seat_id.removeClass('bg-reserve');
        seat_id.addClass('bg-blank');
        cancel(seat_no);
        var seat_info = $('#seat_info_' + seat_no);
        var row = document.getElementById(row_id);
        row.parentNode.removeChild(row);
        var info = '<span class="badge badge-info" style="font-size: 8pt;">';
        info += seat_no;
        info += '</span>';
        seat_info.html(info);
        calTotalFare();
    }
    function removeSeatExtra(seat_no) {
        var no = $('#ticket_guest tr:last').attr('id').toString().split('_');
        seat_no = no[1];

        document.getElementById("user_action").innerHTML = 'ยกเลิกที่นั่ง ' + seat_no;
        document.getElementById("debug").style.background = 'red';

        var row_id = 'row_' + seat_no;
        var row = document.getElementById(row_id);
        row.parentNode.removeChild(row);

        var seat_id = 'seat_' + seat_no;
        var seat = document.getElementById(seat_id);
        seat.parentNode.removeChild(seat);

        NumberSeat = NumberSeat - 1;

        cancel(seat_no);
        calTotalFare();

    }
    function calTotalFare() {
        var total = 0;
        $('select').each(function () {
            var selectedOption = $(this).find('option:selected');
//            alert('Value: ' + selectedOption.val() + ' Text: ' + selectedOption.text());
            total += parseInt(selectedOption.val());
        });
        if (total === 0) {
            $('#guest_info').removeAttr('class');
            $('#guest_info').addClass('animated zoomOut');
            document.getElementById("user_action").innerHTML = 'เลือกที่นั่ง';
            document.getElementById("debug").style.background = 'white';
        }
        $('#txt_total').val(total);

        return true;
    }
    function booking(seat_no) {

//        alert(seate_no);      
        var PriceSeat = parseInt($('#Price').val());
        var seat_info = {
            'TSID': schedules_id,
            'Seat': seat_no,
            'VCode': document.getElementById("VCode").value,
            'SourceID': document.getElementById("SourceID").value,
            'SourceName': document.getElementById("SourceName").value,
            'DestinationID': document.getElementById("DestinationID").value,
            'DestinationName': document.getElementById("DestinationName").value,
            'PriceSeat': PriceSeat
        };
        $.ajax({
            url: '<?= base_url() . "sale/booking_seat" ?>',
            type: 'POST',
            ContentType: 'application/json',
            data: seat_info,
            success: function (json) {
                try {
                    var obj = jQuery.parseJSON(json)
                    if (parseInt(obj) === 1) {
                        return true;
                    } else {
                        return false;
                    }

                } catch (e) {
                    return false;
                }
            },
            error: function () {
                return false;
            }
        });

    }
    function cancel(seat_no) {
//        alert(seate_no);
        var seat_info = {
            'TSID': schedules_id,
            'Seat': seat_no,
            'VCode': document.getElementById("VCode").value,
            'SourceID': document.getElementById("SourceID").value,
            'DestinationID': document.getElementById("DestinationID").value
        };
        $.ajax({
            url: '<?= base_url() . "sale/cancle_seat" ?>',
            type: 'POST',
            ContentType: 'application/json',
            data: seat_info,
            success: function (json) {
                try {
                    var obj = jQuery.parseJSON(json)
                    if (parseInt(obj) === 1) {
                        return true;
                    } else {
                        return false;
                    }

                } catch (e) {
                    return false;
                }
            },
            error: function () {
                return false;
            }
        });
    }

    function check_chang() {
        var data = {
            'TSID': '',
            'NumberSeatTotal': '',
        };
        $.ajax({
            url: '<?= base_url() . "sale/booking_seat" ?>',
            type: 'POST',
            ContentType: 'application/json',
            data: data,
            success: function (json) {
                try {
                    return true;
                } catch (e) {
                    return false;
                }
            },
            error: function () {
                return false;
            }
        });
    }


</script>
<style>  
    body{
        padding-top: 1%;
    }
    .form-group .col-md-12{
        margin-top: 2%;
    }
    .form-group .col-md-6{
        margin-top: 2%;
    }
    .form-group .col-md-8{
        margin-top: 2%;
    }
    .form-group .col-md-4{
        margin-top: 2%;
    }

    .bg-van{
        padding-top: 2%; 
        background-image: url(<?= base_url() . 'assets/img/bg_van.jpg' ?>);
        background-size: 100% 100%;
        background-repeat:no-repeat;
    }
    .bg-bus{
    }
    .bg-blank{

        background-image: url(<?= base_url() . 'assets/img/seat_blank.png' ?>);
        background-repeat: no-repeat;
        background-size: 100% 100%;
        /*background: #26C281;*/
    }
    .bg-reserve{
        background-image: url(<?= base_url() . 'assets/img/seat_reserve.png' ?>);
        background-repeat: no-repeat;
        background-size: 100% 100%;
        /*background: #F3C13A;*/
    }
    .bg-busy{
        background-image: url(<?= base_url() . 'assets/img/seat_busy.png' ?>);
        background-repeat: no-repeat;
        background-size: 100% 100%;
        /*background: #FFA5A5;*/
    }
    #txt_total{
        width: 100%;
        height: 60px;
        margin: 0px;
        padding-right: 10px;
        font-size:2em;         
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
    }
    .seat{
        display: table;        
        width: 100%;
        height:100%;
        text-align: center;         
    } 
    .seat-extra{
        height: 50px;
        margin: 0 auto;
        padding: 0 auto;
    }

    .seat-no {
        display: table-cell;
        vertical-align: middle;        
        font-size:1.34em;        
    }
    .seat-info {             
        width: 100%;  
        position: absolute;
        bottom: 2px;
        left: 0px;       
    }
    .seat:hover { 
        cursor:pointer;
        opacity: 0.6; 
        filter: alpha(opacity=30);

        -moz-box-shadow: 0 0 10px #ccc; 
        -webkit-box-shadow: 0 0 10px #ccc; 
        box-shadow: 0 0 10px #ccc;
    }



</style>
<?php

function search_tickets($array, $Seat, $eid = NULL, $SeatStatus = NULL) {
    $rs = NULL;
    foreach ($array as $val) {
        if ($SeatStatus != NULL && $val['StatusSeat'] = $SeatStatus && $eid != NULL && $val['Seller'] == $eid && (int) $val['Seat'] === (int) $Seat) {
            $rs = $val;
        } elseif ($eid != NULL && $val['Seller'] == $eid && (int) $val['Seat'] === (int) $Seat) {
            $rs = $val;
        } elseif ($val['Seat'] == $Seat) {
            $rs = $val;
        }
    }
    return $rs;
}
?>

<nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
    <a class="navmenu-brand" href="<?= base_url('home/') ?>">ระบบขายตั๋วโดยสาร</a> 
    <ul class="nav navmenu-nav">
        <li class=""><a href="<?= base_url('home/') ?>"><i class="fa fa-home"></i>&nbsp;<span>หน้าหลัก</span> </a> </li>
        <li id="btnSale"><a href="<?= base_url('sale/') ?>"><i class="fa fa-bullhorn fa-fw"></i>&nbsp;<span>ขายตั๋วโดยสาร</span> </a> </li>
        <li id="btnSchedule"><a href="<?= base_url('schedule/') ?>"><i class="fa fa-list fa-fw"></i>&nbsp;<span>ตารางเดินรถ</span> </a> </li>                                  
        <li id="btnCheckIn"><a href="<?= base_url('checkin/') ?>"><i class="fa fa-clock-o fa-fw"></i>&nbsp;<span>ลงเวลา</span></a></li>
        <li id="btnCost"><a href="<?= base_url('cost/') ?>"><i class="fa fa-pencil-square-o fa-fw" ></i>&nbsp;<span>ค่าใช้จ่าย</span></a></li>
        <li id="btnReport"><a href="<?= base_url('report/') ?>"><i class="fa fa-calendar-o fa-fw"></i>&nbsp;<span>รายงาน</span> </a> </li> 
        <li class=""><a><i class="fa fa-bus"></i><strong>เส้นทางเดินรถ</strong></a></li>

        <?php
        if (count($routes_seller) > 0) {
            foreach ($routes_seller as $route) {
                $seller_station_id = $route['seller_station_id'];
                $seller_station_name = $route['seller_station_name'];
                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cab fa-fw" ></i>&nbsp;&nbsp;<?= $route['RouteName'] ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu navmenu-nav">
                        <li class="dropdown-header"><?= "จุดขึ้นรถ : $seller_station_name" ?></li>                   
                        <li class="divider"></li>
                        <?php
                        foreach ($route['routes_deatil'] as $rd) {
                            $rid = $rd['RID'];
                            echo ' <li class="dropdown-header">ไป  ' . $rd['DestinationName'] . '</li>';
                            foreach ($rd['stations'] as $station) {
                                $destination_id = $station['SID'];
                                $station_name = $station['StationName'];
                                $go_to_booking = array(
                                    'class' => "",
                                );
                                ?>
                                <li> 
                                    <?= anchor("sale/booking/$rid/$seller_station_id/$destination_id/", '<i class="fa fa-external-link"></i>&nbsp;&nbsp;' . $station_name, $go_to_booking); ?>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
        }
        ?>            
    </ul>
</nav>
<div class="navbar navbar-default navbar-fixed-top">
    <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu" data-canvas="body">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
</div>
<div id="" class="container-fluid">
    <div class="row-fluid animated fadeInUp">             
        <div class="col-lg-12">
            <ol class="progtrckr" data-progtrckr-steps="4">
                <li id="step1" class="progtrckr-done"><span class="lead" >เลือกเส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-done"><span class="lead">เลือกเที่ยวเวลาเดินทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-todo"><span class="lead">เลือกที่นั่งการเดินทาง </span></li><!--                 
                --><li id="step4" class="progtrckr-todo"><span class="lead">พิมพ์บัตรโดยสาร</span></li>
            </ol>
        </div>
    </div>
</div>
<div id="" class="container-fluid" style="padding-bottom: 5%;">
    <?php echo $form['form']; ?>
    <?php echo validation_errors() ?>
    <div class="row-fluid">
        <div id="select_time" class="col-lg-4 col-lg-offset-4">
            <div id="route_info"  class="col-lg-12">
                <div class="form-group">
                    <div class="col-md-12">
                        <label for="">เส้นทาง</label>
                        <?php echo $form['route_name'] ?>
                        <?php echo $form['RID'] ?>
                        <?php echo $form['TSID'] ?>
                    </div>
                </div>  
                <div class="form-group">
                    <div class="col-md-6">
                        <label for="">ต้นทาง</label>                                
                        <?php echo $form['SourceID'] ?>
                        <?php echo $form['SourceName'] ?>
                    </div>
                    <div class="col-md-6">
                        <label for="">ปลายทาง</label>
                        <?php echo $form['DestinationID'] ?>
                        <?php echo $form['DestinationName'] ?>
                    </div>
                </div>  
            </div>
            <div class="col-lg-12" style="padding-top: 2%;padding-bottom: 2%;">
                <span class="text pull-left">เวลาเดินทาง&nbsp;&nbsp; :</span>
                <span class="badge badge-danger pull-right">&nbsp;เต็ม&nbsp;</span>
                <span class="badge badge-success pull-right text" style="color:white;"> &nbsp;ว่าง&nbsp;</span> 
            </div>
            <div id="time_list" class="col-lg-12">                
                <div id = "list">                    
                    <div class = "list-group">
                        <?php
                        if (count($schedules) > 0) {
                            foreach ($schedules as $schedule) {
                                $rid = $schedule['RID'];
                                $source_id = $schedule['SourceID'];
                                $destination_id = $schedule['DestinationID'];
                                $tsid = $schedule['TSID'];

                                $seat_blank = $schedule['NumberSeat'] - $schedule['NumberSeatBook'] - $schedule['NumberSeatSale'];

                                $class_seat = 'badge-success';
                                if ($seat_blank <= 0) {
                                    $class_seat = 'badge-danger';
                                    $seat_blank = 'เต็ม';
                                }

                                $class_li = '';
                                if ($schedule_select['NumberSeat'] > 0) {
                                    if ($tsid == $schedule_select['TSID']) {
                                        $class_li = "active";
                                    }
                                }
                                ?>
                                <a class="list-group-item <?= $class_li ?>" name="<?= "schedule_$tsid" ?>" href="<?= base_url("sale/booking/$rid/$source_id/$destination_id/$tsid"); ?>">  
                                    <i class="fa fa-clock-o fa-lg"></i><span class="text" style="font-size: 16pt"> <?= $schedule['TimeDepart'] ?></span>
                                    <span class="badge <?= $class_seat ?>" style="font-size: 12pt"><?= $seat_blank ?></span>                          
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>          
        </div>
        <?php $schedule = $schedule_select ?>
        <div id="select_seat" class="hidden">
            <div id="time_info" class="col-lg-12">
                <div class="form-group">
                    <div class="col-md-6">
                        <label for="">เวลาออก</label>
                        <?php echo $form['TimeDepart'] ?>
                    </div>
                    <div class="col-md-6">
                        <label for="">เวลาถึง</label>
                        <?php echo $form['TimeArrive'] ?>
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="col-md-8">
                        <label for="">วันที่เดินทาง</label>
                        <?php echo $form['Date'] ?>
                        <?php echo $form['DateTH'] ?>
                    </div> 
                    <div class="col-md-4">
                        <label for="">เบอร์รถ</label>  
                        <?php echo $form['VID'] ?>
                        <?php echo $form['VTID'] ?>
                        <?php echo $form['VCode'] ?>
                    </div>
                </div>  
            </div>
            <div id="seat_info_seller" class="col-lg-12" style="padding-top: 3%;">
                <span class="text">
                    เลือกที่นั่ง
                </span>
                <span class="pull-right">
                    <?php
                    $num_blank_seat = $schedule['NumberSeat'] - $schedule['NumberSeatBook'] - $schedule['NumberSeatSale'];
                    if ($num_blank_seat < 0) {
                        $num_blank_seat = 0;
                    }
                    ?>
                    <span class="badge badge-info">&nbsp;ว่าง&nbsp;:&nbsp;<span class="NumberSeatBlank" style="font-size: 12pt;"><?= $num_blank_seat ?></span></span> 
                    <span class="badge badge-warning">&nbsp;เลือก&nbsp;:&nbsp;<span class="NumberSeatBook"style="font-size: 12pt;"><?= $schedule['NumberSeatBookBySeller'] ?></span></span>
                    <span class="badge badge-danger">&nbsp;ขาย&nbsp;:&nbsp;<span class="NumberSeatSale" style="font-size: 14pt;"><?= $schedule['NumberSeatSaleBySeller'] ?></span></span>  
                </span>
            </div>

            <div id="seat_list" class="col-lg-12" style="padding-top: 2%;padding-bottom: 5%;">

                <?php
                if (count($schedule) > 0) {
                    $num_seat = $schedule['NumberSeat'];
                    $TSID = $schedule['TSID'];
                    $SourceID = $schedule['SourceID'];
                    $DestinationID = $schedule['DestinationID'];
                    if ($schedule['VTID'] == 1) {
                        $num_row = 5;
                        $num_col = 4;
                        ?>                        
                        <div class="bg-van">
                            <br>
                            <div class="row">
                                <table id="SeatingPlanVan" class="" border="0"  style="width:75%;padding-top: 5% ; margin: 0 auto;">
                                    <tbody>
                                        <?php
                                        $width = 100 / $num_col;
                                        $seat_no = 1;

                                        for ($row = 1; $row <= $num_row; $row++) {
                                            echo '<tr>';
                                            for ($col = 1; $col <= $num_col; $col++) {

                                                $seat_info = "";
                                                $class_info = "";

                                                $user = '';

                                                $click_book = '';
                                                $click_remove = '';

                                                echo "<td style=\"width: $width%;height: 70px;padding:1%;\">";
                                                if ($row == 1 && ($col == 3 || $col == 4)) {
                                                    $class_seat = '';
                                                } elseif ($row == 2 && ($col == 1)) {
                                                    $class_seat = '';
                                                } elseif (($row == 3 || $row == 4) && ($col == 2)) {
                                                    $class_seat = '';
                                                } else {
                                                    $class_seat = " bg-blank";
                                                    $class_info = 'badge-info';
                                                    $seat_info = $seat_no;

                                                    $click_book = "addSeat($seat_no)";
                                                    $click_remove = "removeSeat($seat_no)";


                                                    $ticket_ = search_tickets($schedule['Tickets'], $seat_no);
                                                    if ($ticket_ != NULL) {
                                                        $eid = $this->m_user->get_user_id();
                                                        $destination_name = $ticket_['DestinationName'];
                                                        $seller_id = $ticket_['Seller'];
                                                        $user = '<i class="fa fa-user fa-2x"></i><br>';
                                                        $seat_info = $destination_name;
                                                        $seat_status = $ticket_['StatusSeat'];
                                                        if ($seat_status == 2) {
                                                            $class_seat = " bg-reserve ";
                                                            $class_info = "badge-warning";

                                                            if ($eid == $seller_id) {
                                                                $click_book = "addSeat($seat_no)";
                                                                $click_remove = "removeSeat($seat_no)";
                                                            } else {
                                                                $click_book = "";
                                                                $click_remove = "";
                                                            }
                                                        } elseif ($seat_status == 1) {
                                                            $class_seat = " bg-busy ";
                                                            $class_info = "badge-danger";

                                                            $click_book = "";
                                                            $click_remove = "";
                                                        }
                                                    }
                                                    $id_seat = "seat_$seat_no";
                                                    $id_seat_info = "seat_info_$seat_no";
                                                    ?>
                                                <div id="<?= $id_seat ?>" onclick="<?= $click_book ?>" ondblclick="<?= $click_remove ?>" class="col-xs-12 seat <?php echo $class_seat; ?>">
                                                    <span class="seat-info" id="<?= $id_seat_info ?>">  
                                                        <?= $user ?>
                                                        <span class="badge <?= $class_info ?>" style="font-size: 8pt;">
                                                            <?= $seat_info ?>
                                                        </span>                                                                                                     
                                                    </span>
                                                </div>
                                                <?php
                                                $seat_no++;
                                            }
                                            echo "</td>";
                                        }
                                        echo '</tr>';
                                    }
                                    if ($schedule['NumberSeat'] <= $schedule['NumberSeatSale']) {
                                        echo '<tr>';
                                        ?>                              
                                        <td colspan="4" class="text-center"> 
                                            <button type="button"class="btn btn-info" onclick="addSeatExtra()"><i class="fa fa-plus fa-lg"></i>&nbsp;เพิ่ม</button>
                                        </td>
                                        <?php
                                        echo '</tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">

                            </div>
                            <br>
                        </div>
                        <div id="SeatExtra" class="row well" style="width: 85%;margin: 0 auto;">
                            <?php
                            if ($schedule_select['NumberTicketsExtra'] > 0 || ($schedule['NumberSeat'] <= $schedule['NumberSeatSale'])) {
                                foreach ($schedule_select['TicketsExtra'] as $ticket) {
                                    $seat_no = $ticket['Seat'];
                                    $status_seat = $ticket['StatusSeat'];

                                    $user = '<i class="fa fa-user fa-lg"></i><br>';

                                    $DestinationName = $ticket['DestinationName'];

                                    if ($status_seat == 1) {
                                        $class_seat = " bg-busy ";
                                        $class_info = "badge-danger";
                                        $click_remove = "";
                                    } else {
                                        $class_seat = " bg-reserve ";
                                        $class_info = "badge-warning";
                                        $click_remove = "removeSeatExtra($seat_no)";
                                    }
                                    $id_seat = "seat_$seat_no";
                                    $id_seat_info = "seat_info_$seat_no";
                                    ?>
                                    <div id="<?= $id_seat ?>" class="col-xs-3 seat-extra">
                                        <div  onclick="" ondblclick="<?= $click_remove ?>" class="seat <?= $class_seat ?>">
                                            <span class="seat-info" id="<?= $id_seat_info ?>"> 
                                                <?= $user ?>                                                
                                                <span class="badge <?= $class_info ?>" style="font-size: 8pt;">
                                                    <?= $DestinationName ?>
                                                </span>                                                                                                     
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>                                                                    
                        </div>
                        <?php
                    } elseif ($schedule['VTID'] == 2) {
                        $num_row = 12;
                        $num_col = 5;
                        ?>
                        <table id="SeatingPlanBus" class="" border="1"  style="width:90%;padding-top: 5% ; margin: 0 auto;">
                            <tbody>
                                <?php
                                $width = 100 / $num_col;
                                $seat_no = 1;
                                $id_seat = "";
                                for ($row = 1; $row <= $num_row; $row++) {
                                    echo '<tr>';
                                    for ($col = 1; $col <= $num_col; $col++) {
                                        echo "<td style=\"width: $width%;height: 50px;padding:1%;\">";
                                        if ($row != $num_row && $col == 3) {
                                            $class_seat = "";
                                        } else {
                                            $class_seat = " bg-blank ";
                                            $id_seat = "";
                                            ?>
                                        <div id="<?= $id_seat ?>" onclick="" ondblclick="" class="col-xs-12 seat <?php echo $class_seat; ?>">
                                            <div class="">
                                                <span class="seat-info" id="<?= $id_seat_info ?>">                                                
                                                    <? ?>
                                                    <span class="badge">
                                                        <?= $seat_no ?>
                                                    </span>                                                                                                     
                                                </span>
                                            </div>
                                        </div>
                                        <?php
                                        $seat_no++;
                                    }
                                    echo "</td>";
                                }
                                echo '</tr>';
                            }
                            ?>

                            </tbody>
                        </table>
                        <?php
                    }
                }
                ?>
            </div>         
        </div>
        <div id='ticket_info' class="hidden">
            <div id="fare_info" class="col-lg-12">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">ราคาเต็ม</label>   
                        <?php echo $form['Price'] ?>
                    </div>
                    <div class="col-md-6">
                        <label for="">ราคาลด</label>                                
                        <?php echo $form['PriceDicount'] ?>
                    </div>
                </div>
                <br>
                <div class="row hidden">
                    <div class="col-md-6">
                        <div class="well text-center">                                    
                            เวลาออก&nbsp;:
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <a class="btn btn-success"><i class="fa fa-check-square-o fa-lg"></i>&nbsp;ลงเวลาออก</a>
                    </div>
                </div>  
            </div>
            <div id="debug" class="col-lg-12 text-center" style="margin: 2% auto;padding-top: 5%; padding-bottom: 5%;background-color: #FFFF99;">
                <div id="user_action">
                </div>                      
            </div> 
            <div id="guest_info" class="<?= (($schedule_select['NumberSeatBookBySeller'] > 0) ? 'animated zoomIn' : 'hidden') ?>">

                <div class="col-lg-12 " style="max-height: 300px; overflow-y: scroll;">
                    <table id="ticket_guest" class="table table-condensed table-hover">
                        <thead>                           
                            <tr>                                        
                                <th style="width: 30%">เลขที่นั่ง</th>
                                <th style="width: 30%">ราคา</th> 
                                <th style="width: 10%"></th>                                               
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            if ($schedule_select['NumberSeatBookBySeller'] > 0) {
                                foreach ($schedule_select['TicketsBook'] as $ticket) {
                                    $seat_no = $ticket['Seat'];
                                    $drop_down = "id = \"FareType$seat_no \" " . 'class="form-control" onchange="calTotalFare()"';
                                    $total +=$ticket['PriceSeat'];
                                    if ($seat_no > $schedule_select['NumberSeat']) {
                                        $click_remove = "removeSeatExtra($seat_no)";
                                    } else {
                                        $click_remove = "removeSeat($seat_no)";
                                    }
                                    ?>
                                    <tr id="row_<?= $seat_no ?>"> 
                                        <td class="text-center" id ="<?= $seat_no ?>">
                                            <span class="text"> <?= $seat_no ?></span>
                                            <input type="hidden" name="Seat[]" value="<?= $seat_no ?>">
                                        </td>
                                        <td>
                                            <?= form_dropdown("PriceSeat[]", $schedule_select['Fare'], $ticket['PriceSeat'], $drop_down) ?> 
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="<?= $click_remove ?>"><i class="fa fa-times"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
                <div class="col-lg-12" style="margin-top: 0%;">
                    <div class="col-sm-6 text-right">
                        <span class="lead">รวม</span>
                    </div> 
                    <div class="col-sm-6">
                        <input type="text" class="text-right" id="txt_total" placeholder="" value="<?= $total ?>"> 
                    </div>                            
                </div>
                <div class="col-lg-12 col-sm-12 text-center" style="margin-top: 5%;">                            
                    <button type="submit" class="btn btn-block btn-lg">พิมพ์บัตรโดยสาร</button>
                </div>

            </div>
            <div id="TicketSaleData" class="col-lg-12 <?= (array_key_exists('ScheduleReport', $schedule_select)) ? '' : 'hidden' ?>" style="padding-top: 2%;">              
                <p class="text">
                    <span class="pull-left">ข้อมูลการขาย</span>
                    <span></span>
                </p>
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 45%;">รายการ</th>
                            <th style="width: 18%;">ราคา</th>
                            <th style="width: 12%;">จำนวน</th>
                            <th style="width: 25%;">เป็นเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $net = 0;
                        $num = 0;
                        if (count($schedule_select['ScheduleReport']) > 0) {
                            foreach ($schedule_select['ScheduleReport'] as $sr) {
                                $net+=$sr['Total'];
                                $num+=$sr['NumberTicket'];
                                ?>
                                <tr>
                                    <td class="text"><?= $sr['DestinationName'] ?></td>
                                    <td class="text-center"><?= $sr['PriceSeat'] ?></td>
                                    <td class="text-center"><?= $sr['NumberTicket'] ?></td>
                                    <td class="text-right"><?= $sr['Total'] ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <td class="text-center" colspan="2">รวม</td>
                            <td class="text text-center"><?= $num ?></td>
                            <td class="text text-right"><strong><?= $net ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>  
    <?php echo form_close(); ?>
</div>


