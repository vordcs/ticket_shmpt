<script>
    var net = 0;
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnReport").addClass("active");
    });
    function calNet() {
        var total = document.getElementById('Total').value;
        var vage = document.getElementById('Vage').value;
        net = document.getElementById('Net');

        if (vage === '' || vage === 0) {
            document.getElementById('Vage').value = '0';
        } else if (vage !== 0) {
            net.value = total - vage;
        } else {
            net.value = total;
        }
        return true;
    }
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

</script>
<div class="container">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?> 
                <br>
                <font color="#777777">
                <span style="font-size: 18px; line-height: 23.399999618530273px;"><?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
</div>
<div class="container">
    <div class="widget">  
        <div class="widget-content">
            <div class="row">   
                <?php
                $x = 1;
                foreach ($routes as $route) {
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

                    /*
                     * ตรวจสอบข้อมูลพนักงานขายตั๋ว 
                     * ว่าเป็นจุดเริ่มต้นหรือว่าสุดท้าย
                     * ถ้าเป็นจุดต้นทาง ให้แสดง เฉพาะ S
                     * ถ้าเป็นจุดปลายทาง ให้แสดง เฉพาะ D
                     */

                    foreach ($stations as $station) {
                        if ($seller_station_id == $station['SID']) {
                            $seller_station_seq = $station['Seq'];
                        }
                    }
                    $form_id = "form_report_$rcode$vtid$seller_station_id";
                    echo form_open("report/send/$rcode/$vtid/$seller_station_id", array('class' => '', 'id' => "$form_id", 'name' => "$form_id"));

                    echo validation_errors();
                    ?>

                    <div class="col-md-12 text-center">
                        <h3>
                            <?= "จุดจอด : $seller_station_name" ?>
                            <input type="hidden" name="RCode" value="<?= $rcode ?>">
                            <input type="hidden" name="VTID" value="<?= $vtid ?>">
                            <input type="hidden" name="SID" value="<?= $seller_station_id ?>">

                        </h3>
                    </div>
                    <?php
                    $total = 0;
                    foreach ($routes_detail as $rd) {
                        $rid = $rd['RID'];
                        $source = $rd['RSource'];
                        $destination = $rd['RDestination'];
                        $schedule_type = $rd["ScheduleType"];
                        $start_point = $rd['StartPoint'];
                        $route_time = $rd['Time'];

                        $stations_in_route = array();

                        if ($start_point == "S") {
                            $n = 0;
                            foreach ($stations as $station) {
                                if ($rcode == $station['RCode'] && $vtid == $station['VTID']) {
                                    $stations_in_route[$n] = $station;
                                    $n++;
                                }
                            }
                        }
                        if ($start_point == "D") {
                            $n = 0;
                            for ($i = $num_station; $i >= 0; $i--) {
                                foreach ($stations as $station) {
                                    if ($rcode == $station['RCode'] && $vtid == $station['VTID'] && $station['Seq'] == $i) {
                                        $stations_in_route[$n] = $station;
                                        $n++;
                                    }
                                }
                            }
                        }

                        $schedules_in_route = array();
                        foreach ($schedules as $sd) {
                            if ($rid == $sd['RID']) {
                                array_push($schedules_in_route, $sd);
                            }
                        }


                        $class = ' ';
                        if ($seller_station_seq == 1 && $start_point == 'S') {
                            $class = ' col-md-offset-3 ';
                        } elseif ($seller_station_seq == $num_station && $start_point == 'D') {
                            $class = ' col-md-offset-3 ';
                        } elseif ($seller_station_seq == 1 || $seller_station_seq == $num_station) {
                            $class = 'hidden';
                        }
                        ?>                 
                        <div class="col-md-6 <?= $class ?>">
                            <legend>
                                <i>ไป</i>
                                <strong>
                                    <?= $destination ?>   
                                </strong>
                            </legend>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">เวลาออก</th>                                                                                                                                                                         
                                        <th rowspan="2" style="width: 15%">รายรับ</th>
                                        <th rowspan="2" style="width: 15%">รายจ่าย</th>
                                        <th rowspan="2" style="width: 15%">คงเหลือ</th>                                                                   
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_outcome = 0;
                                    $total_income = 0;
                                    foreach ($schedules_in_route as $schedule) {
                                        $tsid = $schedule['TSID'];
                                        $start_time = $schedule['TimeDepart'];
                                        $time_depart = '';
                                        $temp = 0;
                                        foreach ($stations_in_route as $s) {
                                            if ($s['IsSaleTicket'] == '1') {
                                                $station_name = $s['StationName'];
                                                $travel_time = $s['TravelTime'];
                                                if ($s['Seq'] == '1' || $s['Seq'] == $num_station) {
//                                                                                  สถานีต้นทาง
                                                    $time = strtotime($start_time);
                                                } else {
                                                    $temp+=$travel_time;
                                                    $time = strtotime("+$temp minutes", strtotime($start_time));
                                                }
                                                if ($seller_station_id == $s['SID']) {
                                                    $time_depart = date('H:i', $time);
                                                    break;
                                                }
                                            }
                                        }
                                        $report_id = $schedule['ReportID'];
                                        /*
                                         * สถานะการส่งรายงาน
                                         */

                                        /*
                                         * รายรับ,รายจ่าย
                                         */

                                        $income = 0;

                                        $outcome = 0;
                                        foreach ($cost_types as $cost_type) {
                                            $cost_type_id = $cost_type['CostTypeID'];
                                            foreach ($costs as $cost) {
                                                $CostValue = $cost['CostValue'];
                                                if ($tsid == $cost['TSID'] && $cost_type_id == $cost['CostTypeID'] && $report_id == NULL) {
                                                    if ($cost_type_id == '1') {
                                                        //รายรับ
                                                        $income+=(int) $CostValue;
                                                        $total_income+=$CostValue;
                                                        $total +=(int) $CostValue;
                                                    } else {
                                                        //รายจ่าย
                                                        $outcome+=(int) $CostValue;
                                                        $total_outcome+=$CostValue;
                                                        $total -=(int) $CostValue;
                                                    }
                                                }
                                            }
                                        }
                                        if ($report_id == NULL && ($income > 0 || $outcome > 0 )) {
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?= $time_depart; ?>
                                                    <input type="hidden" name="TSID[]" value="<?= $tsid ?>">
                                                </td>                                           
                                                <td class="text-center"><?= number_format($income) ?></td>
                                                <td class="text-center"><?= number_format($outcome) ?> </td>
                                                <td class="text-right"><strong><?= number_format($income - $outcome) ?></strong></td>                                       
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"><strong>รวม</strong></td>                                           
                                        <td class="text-center"><strong><?= number_format($total_income) ?></strong></td>
                                        <td class="text-center"><strong><?= number_format($total_outcome) ?></strong> </td>
                                        <td class="text-right"><strong><?= number_format($total_income - $total_outcome) ?></strong></td>                                       
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <hr>
            <div class="row">  
                <div class="col-md-6 col-md-offset-3">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">รวม</label>
                            <div class="col-sm-5">
                                <input type="text" readonly=""  class="form-control input-lg" id="Total" name="Total"  placeholder="ยอดรวม" value="<?= number_format($total) ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-sm-3 control-label">ค่าตอบแทน</label>
                            <div class="col-sm-3">
                                <input type="text"  class="form-control input-lg" id="Vage" name="Vage" placeholder="ค่าตอบเเทน" value="<?= (set_value('Vage') == NULL) ? 0 : set_value('Vage') ?>" onkeypress="return isNumberKey(event)" onchange="calNet()">                                
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-default" onclick="calNet()">ดูยอดคงเหลือ</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">คงเหลือ</label>
                            <div class="col-sm-8">
                                <input type="text" readonly=""  class="form-control input-lg" id="Net" name="Net" placeholder="ยอดคงเหลือ" value="<?= (set_value('Net') == NULL) ? $total : set_value('Net') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">หมายเหตุ</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" id="ReportNote" name="ReportNote" placeholder="หมายเหตุ" value="<?= (set_value('ReportNote') == NULL) ? set_value('ReportNote') : set_value('ReportNote') ?>"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 text-center">                             
                                <?php
                                $cancle = array(
                                    'type' => "button",
                                    'class' => "btn btn-lg btn-danger",
                                );

                                $save = array(
                                    'class' => "btn btn-lg btn-success",
                                    'title' => "$page_title",
                                    'data-id' => "5",
                                    'data-title' => "ส่งรายงาน จุดจอด : $seller_station_name",
                                    'data-sub_title' => "$route_name",
                                    'data-info' => "",
                                    'data-toggle' => "modal",
                                    'data-target' => "#confirm",
                                    'data-href' => "",
                                    'data-form_id' => "$form_id",
                                );
                                echo anchor(($previous_page == NULL) ? 'report/' : $previous_page, '<i class="fa fa-times" ></i>&nbsp;ยกเลิก', $cancle) . '  ';
                                echo anchor('#', '<span class="fa fa-save">&nbsp;&nbsp;ส่ง</span>', $save);
                                ?>  
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
        </div>        
    </div>
</div>
