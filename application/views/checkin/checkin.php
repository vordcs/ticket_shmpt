<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCheckIn").addClass("active");
    });
</script>
<div class="container hidden" style="">
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
<br>
<br>
<div class="container" style=""> 
    <div class="row">
        <span class="pull-right clock" id="clock">
            <div id="Date"></div>
            <ul id="time">
                <li id="hours"> </li>
                <li id="point">:</li>
                <li id="min"> </li>
                <li id="point">:</li>
                <li id="sec"> </li>
            </ul>
        </span>
    </div>
    <div class="row">        
        <div class="col-md-12">
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
//                    echo "<legend>$vt_name</legend>";
                    if ($num_route == 1) {
                        $class = 'in';
                    }
                    ?> 
                    <div class="panel-group panel-group-lists collapse in" id="accordionRoute" role="tablist" aria-multiselectable="true">
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

                            $station_check_in_id = $route['SID'];
                            $station_check_in_name = $route['StationName'];
                            $station_source_seq = $route['Seq'];
                            if ($route['SellerNote'] != NULL) {
                                $note = $route['SellerNote'];
                                $station_check_in_name .= " ($note) ";
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
                            $is_first_station = FALSE;
                            $is_last_station = FALSE;
                            foreach ($stations as $station) {
                                if ($station_check_in_id == $station['SID']) {
                                    $station_check_in_seq = $station['Seq'];
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
                                            <p class="lead">จุดจอด : <strong><?= $station_check_in_name ?></strong></p>
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
                                                $last_seq_station = 0;


                                                $schedules_in_route = array();
                                                foreach ($schedules as $sd) {
                                                    if ($rid == $sd['RID']) {
                                                        array_push($schedules_in_route, $sd);
                                                    }
                                                }
                                                $class = ' ';
                                                $flag = TRUE;
                                                if ($station_check_in_seq == 1 && $start_point == 'S') {
                                                    $class = ' col-md-offset-3 ';
                                                } elseif ($station_check_in_seq == $num_station && $start_point == 'D') {
                                                    $class = ' col-md-offset-3 ';
                                                } elseif ($station_check_in_seq == 1 || $station_check_in_seq == $num_station) {
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
                                                                <th style="width: 20%">ออกจาก <?= $source ?></th> 
                                                                <th style="width: 20%">รถเบอร์</th>                                                                                                                                  
                                                                <th style="width: 20%"><?= $station_check_in_name ?> </th>
                                                                <th style="width: 10%"></th>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    if (count($schedules_in_route) > 0) {
                                                                        foreach ($schedules_in_route as $sd) {
                                                                            $tsid = $sd['TSID'];
                                                                            $time_depart = date('H:i', strtotime($sd['TimeDepart']));

                                                                            $vid = $sd['VID'];
                                                                            $vcode = $sd['VCode'];

                                                                            if ($vcode == '') {
                                                                                $vcode = '-';
                                                                            }
                                                                            $time_check_in = $sd['TimeCheckIn'];
                                                                            if ($time_check_in == NULL) {
                                                                                $time_check_in = '-';
                                                                            } else {
                                                                                $time_check_in = date('H:i', strtotime($sd['TimeCheckIn']));
                                                                            }

                                                                            if ($sd['TimeCheckIn'] == NULL) {
                                                                                $add = array(
                                                                                    'class' => 'btn btn-sm btn-success',
                                                                                    'type' => "button",
                                                                                    'data-id' => "1",
                                                                                    'data-title' => "ลงเวลา รถเบอร์ $vcode",
                                                                                    'data-sub_title' => "รอบเวลา",
                                                                                    'data-info' => "$time_depart (ออกจาก $source)",
                                                                                    'data-toggle' => "modal",
                                                                                    'data-target' => "#confirm",
                                                                                    'data-href' => "checkin/add/$rid/$tsid/$vid/$station_check_in_id",
                                                                                );
                                                                                $action = anchor('#', '<i class="fa fa-check-square-o"></i>', $add);
                                                                            } else {

                                                                                $edit = array(
                                                                                    'class' => 'btn btn-sm btn-warning ',
                                                                                    'type' => "button",
                                                                                    'data-id' => "1",
                                                                                    'data-title' => "แก้ไข เวลา รถเบอร์ $vcode",
                                                                                    'data-sub_title' => "รอบเวลา",
                                                                                    'data-info' => "$time_depart (ออกจาก $source)",
                                                                                    'data-toggle' => "modal",
                                                                                    'data-target' => "#confirm",
                                                                                    'data-href' => "checkin/edit/$tsid/$station_check_in_id",
                                                                                );
                                                                                $action = anchor('#', '<i class="fa fa-edit"></i>', $edit);
                                                                            }
                                                                            ?>
                                                                            <tr>
                                                                                <td class="text-center"><?= $time_depart ?></td>
                                                                                <td class="text-center"><?= $vcode ?></td>
                                                                                <td class="text-center"><strong><?= $time_check_in ?></strong></td>
                                                                                <td class="text-center">
                                                                                    <?= $action ?>                                                                                                                                                               
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div> 
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