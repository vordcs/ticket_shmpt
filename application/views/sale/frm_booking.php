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
                $('#select_seat').addClass('col-lg-4 animated zoomIn');

                $('#ticket_info').removeAttr('class');
                $('#ticket_info').addClass('col-lg-4 animated zoomIn');
            }, 500);


            timer = setTimeout(function () {
                $('#step3').removeAttr('class');
                $('#step3').addClass('progtrckr-done');
            }, 1000);
        }

    });
    var schedules_id = '<?= $schedules_id ?>';
    function addSeat(seat_no) {
        var tb_ticket_body = $('#ticket_guest tbody');
        var num = $('#tb_ticket_sale tbody tr').length;
        var seat_info = $('#seat_info_' + seat_no);
        var price = $('#Price').val();
        var price_dis = $('#PriceDicount').val();
        var fare_type = '<select id="FareType' + num + '" name="PriceSeat[]" class="form-control" onchange="calTotalFare()" >';
        fare_type += '<option value="' + price + '" selected="" >' + price + '(เต็ม)</option>';
        fare_type += '<option value="' + price_dis + '">' + price_dis + '(ลด)</option>';
        fare_type += '</select>';
        var row = '<tr id ="row_' + seat_no + '">';
        row += '<td class="text-center" id ="' + seat_no + '" >' + seat_no;
        row += '<input type="hidden" name="Seat[]" value="' + seat_no + '">';
        row += '</td>';
        row += '<td class="text-center">' + fare_type + '</td>';
        row += '<td class="text-center">';
        row += '<button type="button" class="btn btn-sm btn-danger" onclick="removeSeat(\'' + seat_no + '\')">';
        row += '<i class="fa fa-times"></i>';
        row += '</button>';
        row += '</td>';
        row += '</tr>';
        if (document.getElementById(seat_no) === null) {

            document.getElementById("user_action").innerHTML = schedules_id + ' กำลังเลือก ' + seat_no;
            document.getElementById("debug").style.background = 'green';


            $('#guest_info').removeAttr('class');
            $('#guest_info').addClass('col-lg-12 animated zoomIn');

            booking(seat_no);

            var seat_id = $('#seat_' + seat_no);
            seat_id.removeClass('bg-blank');
            seat_id.addClass('bg-reserve');
//            seat_id.css("background-color", "#F3C13A");
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
        var info = '<span class="badge badge-info">';
        info += seat_no;
        info += '</span>';
        seat_info.html(info);
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
            data: seat_info
        }).done(function (response) {
            if (response !== '') {
                show_message('message_success', ' เลือกที่นั่ง ' + seat_no);
                return true;
            } else {
                show_message('message_danger', 'ไม่สามารถเลือกที่นั่ง ' + seat_no);
                return false;
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            show_message('message_danger', 'ไม่สามารถเลือกที่นั่ง ' + seat_no);
            return false;
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
            data: seat_info
        }).done(function (response) {
            show_message('message_warning', ' --ยกเลิกที่นั่ง ' + seat_no);

        }).fail(function (jqXHR, textStatus, errorThrown) {
            show_message('message_danger', 'ไม่สามารถยกเลิกที่นั่ง ' + seat_no);
            return false;
        });
    }

    function show_message(alert_name, msg) {
        var message_box = document.getElementById('message-box');
        message_box.removeAttribute('class', 'hidden');
        var alert = document.getElementById(alert_name);
        alert.setAttribute('style', 'visibility:visible');

        document.getElementById(alert_name + "-msg").innerHTML = msg;
        setTimeout(function () {
            alert.setAttribute('style', 'visibility:hidden');
            message_box.setAttribute('class', 'hidden');
        }, 5000);
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
    .seat-icon {
        display: table-cell;             
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

<nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
    <a class="navmenu-brand" href="<?= base_url('home/') ?>">ระบบขายตั๋วโดยสาร</a> 
    <ul class="nav navmenu-nav">
        <li class=""><a href="<?= base_url('home/') ?>"><i class="fa fa-home"></i>&nbsp;<span>หน้าหลัก</span> </a> </li>
        <li id="btnSale"><a href="<?= base_url('sale/') ?>"><i class="fa fa-bullhorn fa-fw"></i>&nbsp;<span>ขายตั๋วโดยสาร</span> </a> </li>
        <li id="btnSchedule"><a href="<?= base_url('schedule/') ?>"><i class="fa fa-list fa-fw"></i>&nbsp;<span>ตารางเดินรถ</span> </a> </li>                                  
        <li id="btnCheckIn"><a href="<?= base_url('checkin/') ?>"><i class="fa fa-clock-o fa-fw"></i>&nbsp;<span>ลงเวลา</span></a></li>
        <li id="btnCost"><a href="<?= base_url('cost/') ?>"><i class="fa fa-pencil-square-o fa-fw" ></i>&nbsp;<span>ค่าใช้จ่าย</span></a></li>
        <li id="btnReport"><a href="<?= base_url('report/') ?>"><i class="fa fa-calendar-o fa-fw"></i>&nbsp;<span>รายงาน</span> </a> </li> 
        <?php
        foreach ($routes_seller as $route) {
            $rcode = $route['RCode'];
            $vtid = $route['VTID'];
            $vt_name = $route['VTDescription'];
            $source = $route['RSource'];
            $destination = $route['RDestination'];
            $route_name = "$vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
            $id = $rcode . "_" . $vtid;

            $seller_station_id = $route['SID'];
            $seller_station_name = $route['StationName'];
            $seller_station_seq = $route['Seq'];
            if ($route['SellerNote'] != NULL) {
                $note = $route['SellerNote'];
                $seller_station_name .= " ($note) ";
            }
            ?>
            <li class=""><a><i class="fa fa-bus"></i><strong>เส้นทางเดินรถ</strong></a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $route_name ?> <b class="caret"></b></a>
                <ul class="dropdown-menu navmenu-nav">
                    <li class="dropdown-header"><?= "จุดขึ้นรถ : $seller_station_name" ?></li>                   
                    <li class="divider"></li>
                    <?php
                    foreach ($routes_detail as $rd) {
                        if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                            $rid = $rd['RID'];
                            $source = $rd['RSource'];
                            $destination = $rd['RDestination'];
                            $schedule_type = $rd["ScheduleType"];
                            $start_point = $rd['StartPoint'];
                            $route_time = $rd['Time'];
                            $route_name = " ตารางเวลาเดิน $vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;


                            //นับจำนวนสถานี
                            $num_station = 0;
                            $num_sale_station = 0;
                            foreach ($stations as $s) {
                                if ($rcode == $s['RCode'] && $vtid == $s['VTID']) {
                                    $num_station ++;
                                    if ($s['IsSaleTicket'] == '1') {
                                        $num_sale_station ++;
                                    }
                                }
                            }
                            echo ' <li class="dropdown-header">ไป  ' . $destination . '</li>';

                            $stations_in_route = array();

                            if ($start_point == "S") {
                                $n = 0;
                                foreach ($stations as $station) {
                                    if ($rcode == $station['RCode'] && $vtid == $station['VTID'] && $seller_station_seq < $station['Seq']) {
                                        $stations_in_route[$n] = $station;
                                        $n++;
                                    }
                                }
                            }
                            if ($start_point == "D") {
                                $n = 0;
                                for ($i = $num_station; $i >= 0; $i--) {
                                    foreach ($stations as $station) {
                                        if ($rcode == $station['RCode'] && $vtid == $station['VTID'] && $station['Seq'] == $i && $seller_station_seq > $station['Seq']) {
                                            $stations_in_route[$n] = $station;
                                            $n++;
                                        }
                                    }
                                }
                            }

                            if (count($stations_in_route) > 0) {
                                foreach ($stations_in_route as $station) {
                                    $destination_id = $station['SID'];
                                    $station_name = $station['StationName'];

                                    $go_to_booking = array(
                                        'class' => "",
                                    );
                                    $action_booking = anchor("sale/booking/$rid/$seller_station_id/$destination_id/", $station_name, $go_to_booking);
                                    ?>
                                    <li><?= $action_booking ?></li>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>


                </ul>
            </li>
            <?php
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
  <!--                <li class="progtrckr-done"><span class="lead">สถานีต้นทาง</span></li>id="menu-toggle"
                --><li id="step1" class="progtrckr-done"><span class="lead" >เลือกเส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-done"><span class="lead">เลือกเที่ยวเวลาเดินทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-todo"><span class="lead">เลือกที่นั่งการเดินทาง </span></li><!--                 
                --><li id="step4" class="progtrckr-todo"><span class="lead">พิมพ์บัตรโดยสาร</span></li>
            </ol>
        </div>
    </div>
</div>
<div id="" class="container-fluid">
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
            <div id="time_list" class="col-lg-12"> 
                <div style="">                        
                    <?php
                    $rid = $route['RID'];
                    $route_name = $route['VTDescription'] . "  เส้นทาง " . $route['RCode'] . ' ' . ' ' . $route['RSource'] . ' - ' . $route['RDestination'];
                    $route_first_seq = '1';

                    $route_last_seq = count($stations);
                    $num_station = count($stations);

                    $source_id = $s_station['SID'];
                    $source_name = $s_station['StationName'];
                    $source_seq = $s_station['Seq'];
                    $source_travel_time = $s_station['TravelTime'];

                    $destination_id = $d_station['SID'];
                    $destination_name = $d_station['StationName'];
                    $destination_seq = $d_station['Seq'];
                    ?>
                    <table class="table" >   
                        <thead>
                            <tr>
                                <th colspan="2">
                                    <?php echo $date; ?>     
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="">
                                <td colspan="" class="">                              
                                    <span class="text">เวลาเดินทาง&nbsp;&nbsp; :</span>                                
                                </td>
                                <td class="list-group-item">
                                    <span class="badge badge-danger">&nbsp;เต็ม&nbsp;</span>
                                    <span class="badge badge-success">&nbsp;ว่าง&nbsp;</span> 
                                </td>
                            </tr>                
                        </tbody>                                             
                    </table>                  
                </div>             
                <div id = "list">
                    <div class = "list-group">
                        <?php
                        foreach ($schedules_detail as $sd) {
                            $tsid = $sd['TSID'];
                            $rid = $sd['RID'];
                            $schedule_seq_no = $sd['SeqNo'];
                            $seat_blank = $sd['VSeat'];
                            $start_time = $sd['TimeDepart'];
                            foreach ($tickets_today as $ticket) {
                                if ($tsid == $ticket['TSID']) {
                                    $seat_blank--;
                                }
                            }
                            $class_seat = 'badge-success';
                            if ($seat_blank <= 0) {
                                $class_seat = 'badge-danger';
                                $seat_blank = 'เต็ม';
                            }

//                      สถานีต้นทาง หรือ สถานีปลายทาง ไม่บวกเวลาเดินทางเพิ่ม
                            if ($source_seq == '1' || $source_seq == $num_station) {
                                $time_depart = strtotime($start_time);
                            } else {
                                $temp = $source_travel_time;
                                $time_depart = strtotime("+$temp minutes", strtotime($start_time));
                            }
//                        เวลาออกจากสถานีต้นทาง
                            $time_depart = date('H:i', $time_depart);

                            $class = "";
                            if ($schedules_id == $tsid) {
                                $class = "active";
                            }
                            ?>
                            <a class="list-group-item <?= $class ?>" name="<?= "schedulet$tsid" ?>" href="<?= base_url("sale/booking/$rid/$source_id/$destination_id") . "/$tsid" ?>">  
                                <?= $time_depart ?>
                                <span class="badge <?= $class_seat ?>"><?= $seat_blank ?></span>                          
                            </a> 
                        <?php } ?>
                    </div>  
                </div> 
            </div>
        </div>
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
                        <?php echo $form['VCode'] ?>
                    </div>
                </div>  
            </div>
            <div id="seat_list" class="col-lg-12" style="padding-top: 5%;">
                <span class="text">
                    เลือกที่นั่ง
                </span>
                <p class="pull-right">
                    <span class="badge badge-info">&nbsp;ว่าง&nbsp;</span> 
                    <span class="badge badge-warning">&nbsp;กำลังเลือก&nbsp;</span>
                    <span class="badge badge-danger">&nbsp;ขาย&nbsp;</span>  
                </p>

                <div class="" style="
                     padding-top: 2%; 
                     background-image: url(<?= base_url() . 'assets/img/bg_van.jpg' ?>);
                     background-size: 100% 100%;
                     background-repeat:no-repeat;
                     ">
                    <br>
                    <table id="SeatingPlanVan" class="" border="0"  style="width:75%;padding-top: 5% ; margin: 0 auto;">
                        <tbody>
                            <?php
                            $num_row = 5;
                            $num_col = 4;
                            $seat_no = 1;
                            for ($row = 1; $row <= $num_row; $row++) {
                                echo '<tr>';
                                for ($col = 1; $col <= $num_col; $col++) {
                                    $width = 100 / $num_col;
                                    $class = '';
                                    $seat_info = "";
                                    $add_click = '';
                                    $remove_click = '';
                                    $user = '';
                                    $id_seat = "";
                                    $id_seat_info = '';
                                    $seat_status = '';
                                    echo "<td style=\"width: $width%;height: 70px;padding:1%;\">";
                                    if ($row == 1 && ($col == 3 || $col == 4)) {
                                        $class = '';
                                    } elseif ($row == 2 && ($col == 1)) {
                                        $class = '';
                                    } elseif (($row == 3 || $row == 4) && ($col == 2)) {
                                        $class = '';
                                    } else {
                                        $seat_info = $seat_no;
                                        $add_click = 'addSeat(\'' . $seat_no . '\')';
                                        $remove_click = 'removeSeat(\'' . $seat_no . '\')';
                                        $id_seat = "seat_$seat_no";
                                        $id_seat_info = "seat_info_$seat_no";
                                        $class_seat = " bg-blank ";
                                        $class_info = "badge-info";

                                        /*
                                         * 0=ว่าง ,
                                         * 1=ไม่ว่าง,ขายเเล้ว, 
                                         * 2=กำลังจอง
                                         */
                                        foreach ($tickets as $ticket) {
                                            $ticket_seat_no = $ticket['Seat'];
                                            $status_seat = $ticket['StatusSeat'];
                                            if ($seat_no == $ticket['Seat'] && ($status_seat == '1' )) {
                                                $add_click = '';
                                                $remove_click = '';
                                                $user = '<i class="fa fa-user fa-2x"></i><br>';
                                                $seat_info = $ticket['DestinationName'];
                                                $class_seat = " bg-busy ";
                                                $class_info = "badge-danger";
                                            } elseif ($seat_no == $ticket['Seat'] && $status_seat == '2') {
                                                $add_click = '';
                                                $remove_click = '';
                                                $user = '<i class="fa fa-user fa-2x"></i><br>';
                                                $seat_info = 'ไม่ว่าง';
                                                $class_seat = " bg-reserve ";
                                                $class_info = "badge-warning";
                                            }
                                        }

                                        /*
                                         * ตรวจสอบว่ามีการจองตั๋วแล้วยังไม่ปริ้นหรือไม่
                                         * หรือถูกจองโดยผู้ใช้อื่นหรือไม่ในขณะทำรายการ
                                         */
                                        if (count($tickets_by_seller) > 0) {
                                            foreach ($tickets_by_seller as $ticket) {
                                                $ticket_seat_no = $ticket['Seat'];
                                                $status_seat = $ticket['StatusSeat'];
                                                $destination_name = $ticket['DestinationName'];
                                                if ($seat_no == $ticket['Seat'] && $status_seat == '2') {
                                                    $add_click = 'addSeat(\'' . $seat_no . '\')';
                                                    $remove_click = 'removeSeat(\'' . $seat_no . '\')';
                                                    $user = '<i class="fa fa-user fa-2x"></i><br>';
                                                    $seat_info = $destination_name;
                                                    $class_seat = " bg-reserve ";
                                                    $class_info = "badge-warning";
                                                }
                                            }
                                        }
                                        ?>
                                    <div id="<?= $id_seat ?>" onclick="<?= $add_click ?>" ondblclick="<?= $remove_click ?>" class="col-xs-12 seat <?php echo $class_seat; ?>">
                                        <span class="seat-info" id="<?= $id_seat_info ?>">                                                
                                            <?php echo $user; ?>
                                            <span class="badge <?= $class_info ?>">
                                                <?php echo $seat_info; ?>
                                            </span>                                                                                                     
                                        </span>
                                    </div>
                                    <?php
                                    $seat_no++;
                                }
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>       
                </div>


            </div>
        </div>
        <div id='ticket_info' class="hidden">
            <div id="fare_info" class="col-lg-12">
                <div  class="form-group ">                         
                    <div class="col-md-6">
                        <label for="">ราคาเต็ม</label>   
                        <?php echo $form['Price'] ?>
                    </div>
                    <div class="col-md-6">
                        <label for="">ราคาลด</label>                                
                        <?php echo $form['PriceDicount'] ?>
                    </div>
                </div>  
            </div>
            <div id="debug" class="col-lg-12 text-center" style="margin: 2% auto;padding-top: 5%; padding-bottom: 5%;background-color: #FFFF99;">
                <div id="user_action">
                </div>                      
            </div>        
            <?php
            $num_tickets_by_seller = 0;
            foreach ($tickets_by_seller as $ticket) {
                if ($ticket['StatusSeat'] != 2) {
                    $num_tickets_by_seller++;
                }
            }
            ?>
            <div id="guest_info" class="<?= ((count($tickets_by_seller) > 0) ? 'animated zoomIn' : 'hidden') ?>" style="margin-top: 2%;">  
                <div id="guest_list" class="col-lg-12" style="max-height: 200px;overflow-y: scroll;">
                    <table id="ticket_guest" class="table">
                        <thead>
                            <tr>
                                <th colspan="3">ค่าโดยสาร</th>
                            </tr>
                            <tr>                                        
                                <th style="width: 30%">เลขที่นั่ง</th>
                                <th style="width: 30%">ราคา</th> 
                                <th style="width: 10%"></th>                                               
                            </tr>
                        </thead>
                        <tbody>  
                            <?php
                            $total = 0;
                            if (count($tickets_by_seller) > 0) {
                                foreach ($tickets_by_seller as $ticket) {
                                    $status_seat = $ticket['StatusSeat'];
                                    if ($status_seat != 1) {
                                        $seat_no = $ticket['Seat'];
                                        $price_seat = $ticket['PriceSeat'];
                                        $is_discount = $ticket['IsDiscount'];
                                        $select_full = 'selected = ""';
                                        $select_dis = '';
                                        $total+=$price_seat;
                                        if ($is_discount == 1 || $is_discount == '1') {
                                            $select_full = '';
                                            $select_dis = 'selected = ""';
                                        }
                                        ?>
                                        <tr id="row_<?= $seat_no ?>">                                               
                                            <td class="text-center" id ="<?= $seat_no ?>">
                                                <?= $seat_no ?>
                                                <input type="hidden" name="Seat[]" value="<?= $seat_no ?>">
                                            </td>
                                            <td class="text-center">
                                                <select id="FareType<?= $seat_no ?>" name="PriceSeat[]" class="form-control" onchange="calTotalFare()" >
                                                    <option value="<?= $fare['Price'] ?>" <?= $select_full ?> ><?= $fare['Price'] ?>(เต็ม)</option>
                                                    <option value="<?= $fare['PriceDicount'] ?>" <?= $select_dis ?> ><?= $fare['PriceDicount'] ?>(ลด)</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeSeat('<?= $seat_no ?>')"><i class="fa fa-times"></i></button>
                                            </td>
                                        </tr>

                                        <?php
                                    }
                                }
                            }
                            ?>
                            <?php ?>
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
        </div>
    </div>  
    <?php echo form_close(); ?>
</div>


