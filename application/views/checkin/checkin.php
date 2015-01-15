<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCheckIn").addClass("active");
    });
</script>
<div class="container" style="">
    <div class="row">        
        <div class="page-header">    
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
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;">&nbsp;&nbsp;<?php echo $page_title_small; ?></span>                
                </font>
            </h3>              
        </div>
    </div>
</div>
<div class="container" style=""> 
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
                    echo "<legend>$vt_name</legend>";

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

                            $station_check_in = $route['SID'];
                            $source_name = $route['StationName'];
                            $station_source_seq = $route['Seq'];
                            if ($route['SellerNote'] != NULL) {
                                $note = $route['SellerNote'];
                                $source_name .= " ($note) ";
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
                                                ?>        
                                                <div class="col-md-6">
                                                    <div class="widget">
                                                        <div class="widget-header">
                                                            <i>ไป</i>
                                                            <strong>
                                                                <?= $destination ?>   
                                                            </strong>
                                                        </div>
                                                        <div class="widget-content">
                                                            <table class="table table-striped table-hover table-bordered">
                                                                <thead>
                                                                <th style="width: 20%">ออกจาก <?= $source ?></th> 
                                                                <th style="width: 20%">รถเบอร์</th>                                                                                                                                  
                                                                <th style="width: 20%">เวลาถึง </th>
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
                                                                            $controller = 'add';
                                                                            $info = '<i class="fa fa-check-square-o"></i>';
                                                                            $class = array(
                                                                                'class' => 'btn btn-sm btn-success',
                                                                            );
                                                                            if ($time_check_in == NULL) {
                                                                                
                                                                            } else {
                                                                                
                                                                            }
                                                                            ?>
                                                                            <tr>
                                                                                <td class="text-center"><?= $time_depart ?></td>
                                                                                <td class="text-center"><?= $vcode ?></td>
                                                                                <td class="text-center"><strong><?= $time_check_in ?></strong></td>
                                                                                <td class="text-center">
                                                                                    <?= anchor("checkin/$controller/$rid/$tsid/$vid/$station_check_in", $info, $class) ?>                                                                                                                                                               
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
</div>