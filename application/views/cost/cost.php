
<!--หน้าต่างแสดงค่าใช้ที่เดิดขึ้นของผู้ใช้งาน ที่จุดข้ายตั๋ว-->
<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCost").addClass("active");
    });
</script>
<div class="container-fluid" style="">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;">&nbsp;&nbsp;<?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <?php
        foreach ($vehicle_types as $type) {
            $vtid = $type['VTID'];
            $vt_name = $type['VTDescription'];
            $num_route = 0;
            foreach ($routes as $route) {
                if ($vtid == $route['VTID']) {
                    $num_route++;
                }
            }
            if ($num_route > 0) {
//                echo "<legend>$vt_name</legend>";
                if ($num_route == 1) {
                    $class = 'in';
                }
                ?> 
                <div class="panel-group panel-group-lists collapse in" id="accordionRoute" role="tablist" aria-multiselectable="true">
                    <?php
                    $x = 1;
                    $flag = TRUE;
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
                        ?>
                        <div class="panel">
                            <div class="panel-heading" role="tab" id="heading<?= $id ?>">
                                <h4 class="panel-title" style="padding-left: 3%;">
                                    <a data-toggle="collapse" data-parent="#accordionRoute" href="#collapse<?= $id ?>" aria-expanded="true" aria-controls="collapse<?= $id ?>">
                                        <?= $route_name ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?= $id ?>" class="panel-collapse collapse <?= ($x == 1 ) ? 'in' : '' ?> " role="tabpanel" aria-labelledby="heading<?= $id ?>">
                                <div class="panel-body">
                                    <div class="col-md-12 text-center">
                                        <h3><?= $route_name ?></h3>
                                        <p class="lead">จุดจอด : <strong><?= $seller_station_name ?></strong></p>
                                    </div>
                                    <?php
                                    $x++;
                                    foreach ($routes_detail as $rd) {
                                        if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                            $rid = $rd['RID'];
                                            $source = $rd['RSource'];
                                            $destination = $rd['RDestination'];
                                            $schedule_type = $rd["ScheduleType"];
                                            $start_point = $rd['StartPoint'];
                                            $route_time = $rd['Time'];
                                            $route_name = "เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;

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
                                                $class = ' col-md-offset-1 ';
                                            } elseif ($seller_station_seq == $num_station && $start_point == 'D') {
                                                $class = ' col-md-offset-1 ';
                                            } elseif ($seller_station_seq == 1 || $seller_station_seq == $num_station) {
                                                $class = 'hidden';
                                            }
                                            ?>        
                                            <div class="col-md-6 <?= $class ?>">
                                                <div class="widget">
                                                    <div class="widget-header">
                                                        <i>ไป</i>
                                                        <strong>
                                                            <?= $destination ?>   
                                                        </strong>
                                                    </div>
                                                    <div class="widget-content">
                                                        <table class="table table-striped table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 20%">เวลาออก</th> 
                                                                    <th rowspan="2" style="width: 15%">รถเบอร์</th>                                                                                                                                  
                                                                    <th rowspan="2" style="width: 15%">รายรับ</th>
                                                                    <th rowspan="2" style="width: 15%">รายจ่าย</th>
                                                                    <th rowspan="2" style="width: 15%">คงเหลือ</th>
                                                                    <th rowspan="2" style="width: 20%"></th>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 20%"><?= $seller_station_name ?></th> 
                                                                </tr>

                                                            </thead>
                                                            <tbody>
                                                                <?php
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


                                                                    $vid = $schedule['VID'];
                                                                    $vcode = $schedule['VCode'];

                                                                    if ($vcode == '') {
                                                                        $vcode = '-';
                                                                    }
                                                                    /*
                                                                     * รายรับ,รายจ่าย
                                                                     */
                                                                    $income = 0;
                                                                    $outcome = 0;
                                                                    /*
                                                                     * รายการรายรับที่เกิดจากการซื้อตั๋ว
                                                                     */
                                                                    foreach ($tickets as $ticket) {
                                                                        if ($tsid == $ticket['TSID']) {
                                                                            $income+=$ticket['PriceSeat'];
                                                                        }
                                                                    }

                                                                    foreach ($cost_types as $cost_type) {
                                                                        $cost_type_id = $cost_type['CostTypeID'];
                                                                        foreach ($costs as $cost) {
                                                                            $CostValue = $cost['CostValue'];
                                                                            if ($tsid == $cost['TSID'] && $cost_type_id == $cost['CostTypeID']) {
                                                                                if ($cost_type_id == '1') {
                                                                                    //รายรับ
                                                                                    $income+=(int) $CostValue;
                                                                                } else {
                                                                                    //รายจ่าย
                                                                                    $outcome+=(int) $CostValue;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    $view = array(
                                                                        'type' => "button",
                                                                        'class' => "btn btn-link btn-block",
                                                                        'data-toggle' => "tooltip",
                                                                        'data-placement' => "top",
                                                                        'title' => "ดูค่าใช้จ่าย รอบเวลา $time_depart รถเบอร์ $vcode ",
                                                                    );
                                                                    $IsReport = '';
                                                                    if ($schedule['ReportID'] != NULL) {
                                                                        $IsReport = 'disabled';
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="text-center"><?= anchor("cost/view/$tsid/", $time_depart, $view) . '  '; ?></td>
                                                                        <td class="text-center"><?= $vcode ?></td>
                                                                        <td class="text-center"><?= number_format($income) ?></td>
                                                                        <td class="text-center"><?= number_format($outcome) ?> </td>
                                                                        <td class="text-right"><strong><?= number_format($income - $outcome) ?></strong></td>
                                                                        <td class="text-center">
                                                                            <?php
                                                                            $add_income = array(
                                                                                'type' => "button",
                                                                                'class' => "btn btn-info btn-sm $IsReport",
                                                                                'data-toggle' => "tooltip",
                                                                                'data-placement' => "top",
                                                                                'title' => "เพิ่มรายรับ รถเบอร์ $vcode รอบเวลา $time_depart",
                                                                            );
                                                                            $add_outcome = array(
                                                                                'type' => "button",
                                                                                'class' => "btn btn-warning btn-sm $IsReport",
                                                                                'data-toggle' => "tooltip",
                                                                                'data-placement' => "top",
                                                                                'title' => "เพิ่มรายจ่าย รถเบอร์ $vcode รอบเวลา $time_depart",
                                                                            );
                                                                            echo anchor("cost/add/1/$tsid/", '<span class="fa fa-plus"></span>', $add_income) . '  ';
                                                                            echo anchor("cost/add/2/$tsid/", '<span class="fa fa-minus"></span>', $add_outcome);
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div> 
                                                </div>
                                            </div>  
                                            <?php
                                            $class_beteen = '';
                                            if ($seller_station_seq == 1 && $start_point == 'S') {
                                                
                                            } elseif ($seller_station_seq == $num_station && $start_point == 'D') {
                                                
                                            } else {
                                                $class_beteen = 'hidden';
                                            }
                                            ?>
                                            <div class="col-md-4 <?= $class_beteen ?>">
                                                <div class="well">
                                                    <legend>รายทาง</legend>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 30%">ออกจาก</th>
                                                                <th style="width: 30%">จำนวนเงิน</th>
                                                                <th style="width: 40%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?> 
    </div>
</div>
<div class="container hidden">  
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="widget">
                <div class="widget-header">
                    <i class="fa fa-search"></i>
                    <span>ค้นหา</span>
                </div>
                <div class="widget-content">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">เส้นทาง</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">วันที่</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">จุดจำหน่ายตั๋ว</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-default">ค้นหา</button>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="widget">
                <div class="col-md-12 text-right">   
                    <?php
                    $income = array(
                        'type' => "button",
                        'class' => "btn btn-info btn-lg",
                    );
                    $outcome = array(
                        'type' => "button",
                        'class' => "btn btn-warning btn-lg",
                    );
                    echo anchor('cost/add/1', '<span class="fa fa-plus">&nbsp;&nbsp;รายรับ</span>', $income) . '  ';
                    echo anchor('cost/add/2', '<span class="fa fa-minus">&nbsp;&nbsp;รายจ่าย</span>', $outcome);
                    ?> 
                </div>  
            </div>
        </div>
    </div>    
    <div class="row">
        <div class="widget">
            <div class="widget-header">
                <i class="fa"></i>
                <span>สรุปค่าใช้จ่าย </span>
            </div>
            <div class="widget-content">
                <div id="big_stats" class="cf">
                    <div class="stat"> 
                        <i class="fa">รายได้</i>
                        <span class="value">851</span> 
                    </div>
                    <div class="stat"> 
                        <i class="fa">รายจ่าย</i>
                        <span class="value">851</span> 
                    </div>  
                    <div class="stat"> 
                        <i class="fa">คงเหลือ</i>
                        <span class="value">851</span> 
                    </div>
                </div>            
            </div> 
        </div>     

        <div class="row">
            <div class="widget">
                <div class="widget-header">
                    <i class=""></i>
                    <span>เส้นทาง</span>
                </div>
                <div class="widget-content">
                    <div class="panel-group panel-group-lists collapse in" id="accordion" role="tablist" aria-multiselectable="true">  
                        <?php
                        foreach ($routes as $r) {
                            $rcode = $r['RCode'];
                            $vtid = $r['VTID'];
                            $vt_name = $r['VTDescription'];
                            $route_name = "$vt_name เส้นทาง $rcode" . ' ' . $r['RSource'] . ' - ' . $r['RDestination'];
                            $id = $vtid . '_' . $rcode;
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading<?= $id ?>">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $id ?>" aria-expanded="true" aria-controls="collapse<?= $id ?>">
                                            <?= $route_name ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse<?= $id ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?= $id ?>">
                                    <div class="panel-body">
                                        <?php
                                        foreach ($route_details as $rd) {
                                            if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                                $rid = $rd['RID'];
                                                $source = $rd['RSource'];
                                                $destination = $rd['RDestination'];
                                                $route_name = "$vt_name เส้นทาง $rcode" . ' ' . $rd['RSource'] . ' - ' . $rd['RDestination'];
                                                ?>
                                                <div class="col-md-12">
                                                    <p class="lead"><?= $source ?>&nbsp;&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;&nbsp;<?= $destination ?></p>
                                                </div>
                                                <div class="col-md-12">
                                                    <table class="overflow-y" border="1">
                                                        <thead>
                                                            <tr>
                                                                <th>รอบเวลา</th>
                                                                <th>เบอร์รถ</th>
                                                                <th>รายได้</th>
                                                                <th>รายจ่าย</th>
                                                                <th>คงเหลือ</th> 
                                                                <th></th> 
                                                            </tr> 
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($schedules as $schedule) {
                                                                $TSID = $schedule['TSID'];
                                                                $VID = $schedule['VID'];
//                                                                if ($rid == $schedule['VID']) {
                                                                $VCode = '';
                                                                $sum_income = 0;
                                                                $sum_outcome = 0;

                                                                foreach ($costs as $c) {
                                                                    $temp = $c['CostValue'];
                                                                    if ($c['CostTypeID'] == 1 && $c['VID'] = $VID) {
//                                                                        รายได้ 
                                                                        $sum_income += $temp;
                                                                    } else {
//                                                                         รายจ่าย
                                                                        $sum_outcome += $temp;
                                                                    }
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <th><?= $TSID ?></th>
                                                                    <td class="text-center"><?= $VID ?> </td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td>
                                                                        <?php
                                                                        $income = array(
                                                                            'type' => "button",
                                                                            'class' => "btn btn-info btn-lg",
                                                                        );
                                                                        $outcome = array(
                                                                            'type' => "button",
                                                                            'class' => "btn btn-warning btn-lg",
                                                                        );
                                                                        echo anchor('cost/add/1', '<span class="fa fa-plus">&nbsp;&nbsp;รายรับ</span>', $income) . '  ';
                                                                        echo anchor('cost/add/2', '<span class="fa fa-minus">&nbsp;&nbsp;รายจ่าย</span>', $outcome);
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
//                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>                                 
                                                </div>   
                                                <?php
                                            }
                                        }
                                        ?>


                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>





