<script>
    $(window).load(function () {
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

    function addSeat(seat_no) {
        var timer;
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
        row += '<td class="text-center">';
        row += '<button type="button" class="btn btn-sm btn-danger" onclick="removeSeat(\'' + seat_no + '\')">';
        row += '<i class="fa fa-times"></i>';
        row += '</button>';
        row += '</td>';
        row += '<td class="text-center" id ="' + seat_no + '" >' + seat_no;
        row += '<input type="hidden" name="Seat[]" value="' + seat_no + '">';
        row += '</td>';
        row += '<td class="text-center">' + fare_type + '</td>';        
        row += '</tr>';

        if (document.getElementById(seat_no) === null) {

            $('#guest_info').removeAttr('class');
            $('#guest_info').addClass('col-lg-12 animated zoomIn');

            var seat_id = $('#seat_' + seat_no);
            seat_id.css("background-color", "#FFE5CC");

            tb_ticket_body.append(row);
            var info = '<i class="fa fa-user fa-2x"></i><br>';
            info += $('#DestinationName').val();
            seat_info.html(info);
            calTotalFare();
        }
    }
    function removeSeat(seat_no) {
        var num = $('#TicketSale tbody tr').length;
        var row_id = 'row_' + seat_no;

        var seat_id = $('#seat_' + seat_no);
        seat_id.css("background-color", "#33FF99");

        var seat_info = $('#seat_info_' + seat_no);
        var row = document.getElementById(row_id);
        row.parentNode.removeChild(row);
        var info = seat_no;
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
        }
        $('#txt_total').val(total);
    }
</script>
<style>  
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
        background: #33FF99;
    }
    .bg-reserve{
        background: #FFFF99;
    }
    .bg-busy{
        background: #FFA5A5;
    }
    #txt_total{
        width: 100%;
        height: 60px;
        margin: 0px;
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
    .seat span {
        display: table-cell;
        vertical-align: middle;
        /*font-size:1.14em;*/        
    }
    .seat:hover { 
        opacity: 0.6; 
        filter: alpha(opacity=30);
        
         -moz-box-shadow: 0 0 10px #ccc; 
         -webkit-box-shadow: 0 0 10px #ccc; 
         box-shadow: 0 0 10px #ccc;
    }

</style>
<div id="" class="container-fluid">
    <div class="row-fluid animated fadeInUp">        
        <div class="col-lg-12">
            <ol class="progtrckr" data-progtrckr-steps="4">
  <!--                <li class="progtrckr-done"><span class="lead">สถานีต้นทาง</span></li>
                --><li id="step1" class="progtrckr-done"><span class="lead" id="menu-toggle">เลือกเส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-done"><span class="lead">เลือกเที่ยวเวลาเดินทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-todo"><span class="lead">เลือกที่นั่งการเดินทาง </span></li><!--                 
                --><li id="step4" class="progtrckr-todo"><span class="lead">พิมพ์บัตรโดยสาร</span></li>
            </ol>
        </div>
    </div>
</div>
<div id="wrapper" class="container-fluid" style="margin-top: 0%;">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="#">
                    จุดขึ้นรถ :
                </a>
            </li>
            <li>
                <a href="#">Dashboard</a>
            </li>
            <li>
                <a href="#">Shortcuts</a>
            </li>
            <li>
                <a href="#">Overview</a>
            </li>
            <li>
                <a href="#">Events</a>
            </li>
            <li>
                <a href="#">About</a>
            </li>
            <li>
                <a href="#">Services</a>
            </li>
            <li>
                <a href="#">Contact</a>
            </li>            
        </ul>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper" onclick="">
        <div id="" class="container-fluid">
            <?php echo $form['form']; ?>
            <?php echo validation_errors()?>
            <div class="row-fluid">
                <div id="select_time" class="col-lg-4 col-lg-offset-4">
                    <div id="route_info"  class="col-lg-12">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label for="">เส้นทาง</label>
                                <?php echo $form['route_name'] ?>
                                <?php echo $form['RID'] ?>
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
                                    $schedule_seq_no = $sd['SeqNo'];
                                    $start_time = $sd['TimeDepart'];
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
                                    <a class="list-group-item <?= $class ?>" name="<?= "schedulet$tsid" ?>" href="<?= base_url("sale/step1/$rid/$source_id/$destination_id") . "/$tsid" ?>">  
                                        <span class="badge badge-success"><?= $schedule_seq_no ?></span>                          
                                        <?= $time_depart ?>
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
                            </div> 
                            <div class="col-md-4">
                                <label for="">เบอร์รถ</label>                                                         
                                <?php echo $form['VCode'] ?>
                            </div>
                        </div>  
                    </div>
                    <div id="seat_list" class="col-lg-12" style="padding-top: 5%;">
                        <span class="text">
                            เลือกที่นั่ง
                        </span>
                        <table class="" border="0" >
                            <tbody>
                                <?php
                                $n = 1;
                                $num_row = 5;
                                for ($row = 1; $row <= 5; $row++) {
                                    ?>
                                    <tr>
                                        <?php
                                        for ($col = 1; $col <= 4; $col++) {
                                            $seat_info = "";
                                            $on_click = '';
                                            $user = '';
                                            $id_seat_info = '';
                                            if ($row == 1 && ($col == 3 || $col == 4)) {
                                                $class = '';
                                            } elseif ($row == 2 && ($col == 1)) {
                                                $class = '';
                                            } elseif (($row == 3 || $row == 4) && ($col == 2)) {
                                                $class = '';
                                            } else {
                                                $class = 'seat ';
                                                $seat_no = "V$n";
                                                $seat_info = $seat_no;
                                                $add_click = 'addSeat(\'' . $seat_no . '\')';
                                                $remove_click = 'removeSeat(\'' . $seat_no . '\')';
//                                                        $user = '<i class="fa fa-user fa-3x"></i><br>';                                                
                                                $id_seat = "seat_$seat_no";
                                                $id_seat_info = "seat_info_$seat_no";
//                                              $seat_status
                                                $class.= " bg-blank ";

                                                $n++;
                                            }
                                            ?>
                                            <td style="width:<?= 100 / $num_row ?>%; height: 100px;padding:1%;">                                                
                                                <div id="<?= $id_seat ?>" onclick="<?= $add_click ?>" ondblclick="<?= $remove_click ?>" class="<?php echo $class; ?>">
                                                    <span id="<?= $id_seat_info ?>">
                                                        <?php echo $user; ?> 
                                                        <?php echo $seat_info; ?> 
                                                    </span>
                                                </div>
                                            </td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                <?php } ?> 
                            </tbody>
                        </table> 
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
                    <div id="guest_info" class="hidden" style="margin-top: 2%;">  
                        <div id="guest_list" class="col-lg-12" style="max-height: 400px;overflow-y: scroll;">
                            <table id="ticket_guest" class="table">
                                <thead>
                                    <tr>
                                        <th colspan="3">ค่าโดยสาร</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 10%"></th>
                                        <th style="width: 30%">เลขที่นั่ง</th>
                                        <th style="width: 40%">ราคา</th>                                                
                                    </tr>
                                </thead>
                                <tbody>  
                                </tbody>                               
                            </table>
                        </div>
                        <div class="col-lg-12" style="margin-top: 0%;">
                            <div class="col-sm-6 text-right">
                                <span class="lead">รวม</span>
                            </div> 
                            <div class="col-sm-6">
                                <input type="text" class="text-right" id="txt_total" placeholder="" value="0"> 
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
    </div>
    <!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Menu Toggle Script -->

<!-- Menu Toggle Script -->
<script>
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
</script>