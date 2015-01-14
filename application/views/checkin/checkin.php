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
    <div class="row hidden">
        <div class="widget">
            <div class="widget-header">
                <span>ค้นหา</span>              
            </div>
            <div class="widget-content">                
                <div class="col-md-6 col-md-offset-1">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label >ประเภทรถ</label>
                            <input type="text" class="form-control" placeholder="RCode" value="">
                        </div>
                    </div> 
                    <div class="col-md-8">
                        <div class="form-group">
                            <label >เส้นทาง</label>
                            <input type="text" class="form-control" placeholder="RCode" value="">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label >เบอร์รถ</label>
                            <input type="text" class="form-control" placeholder="RCode" value="">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label >วันที่</label>
                            <input type="text" class="form-control" placeholder="RCode" value="">
                        </div>
                    </div> 
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn">ค้นหา</button>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="col-md-12 clock text-center" id="clock">
                        <div id="Date"></div>
                        <ul id="time">
                            <li id="hours"> </li>
                            <li id="point">:</li>
                            <li id="min"> </li>
                            <li id="point">:</li>
                            <li id="sec"> </li>
                        </ul>
                    </div>  
                    <div class="col-md-12">
                        <br>                        
                        <p class="text-center">
                            <?php
                            $add = array(
                                'class' => "btn btn-lg btn-success",
                            );
                            foreach ($vehicle_types as $type) {
                                $vtid = $type['VTID'];
                                $vt_name = $type['VTDescription'];

                                echo anchor("checkin/add/$vtid", "<span class=\"fa fa-check-square-o\">&nbsp;ลงเวลา&nbsp;$vt_name</span>", $add) . ' ';
                            }
                            ?>

                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="row">
        <div class="widget">
            <div class="widget-header">
                <span>ข้อมูลตารางเวลาเดินรถ</span>              
            </div>
            <div class="widget-content">
                <?php
                foreach ($vehicle_types as $type) {
                    $vtid = $type['VTID'];
                    $vt_name = $type['VTDescription'];
                    echo "<legend>$vt_name</legend>";
                    $num_route = 0;
                    foreach ($routes as $route) {
                        if ($vtid == $route['VTID']) {
                            $num_route++;
                        }
                    }
                    if ($num_route <= 0) {
                        echo '<div class="col-md-12">';
                        echo ' <div class="well" style="padding-bottom: 50px;padding-top: 50px;">';
                        echo '   <p class="lead text-center">ไม่พบข้อมูล</p>';
                        echo ' </div>';
                        echo '</div> ';
                    } else {
                        ?> 
                        <?php
                        foreach ($routes as $route) {
                            if ($vtid == $route['VTID']) {
                                $rcode = $route['RCode'];
                                $source = $route['RSource'];
                                $destination = $route['RDestination'];
                                $schedule_type = $route["ScheduleType"];
                                $route_name = "$vt_name  " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                                ?>
                                <?php
                                foreach ($routes_detail as $rd) {
                                    if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                        $rid = $rd['RID'];
                                        $source = $rd['RSource'];
                                        $destination = $rd['RDestination'];
                                        $schedule_type = $rd["ScheduleType"];
                                        $start_point = $rd['StartPoint'];
                                        $route_time = $rd['Time'];
                                        $route_name = "$vt_name  " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;


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
                                        $stations_sale_ticket = array();
                                        $n = 0;
                                        if ($start_point == 'S') {
                                            for ($i = 0; $i <= $num_station; $i++) {
                                                foreach ($stations as $s) {
                                                    if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1' && $s['Seq'] == $i) {
                                                        $stations_sale_ticket[$n] = $s;
                                                        $n++;
                                                    }
                                                }
                                            }
                                        } else {
                                            for ($i = $num_station; $i >= 0; $i--) {
                                                foreach ($stations as $s) {
                                                    if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1' && $s['Seq'] == $i) {
                                                        $stations_sale_ticket[$n] = $s;
                                                        $n++;
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <table class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th colspan="<?= (int) $num_sale_station + 4; ?>"><?= $route_name ?></th>
                                                </tr>
                                                <?php
                                                if ($start_point == "S") {
                                                    //start point s
                                                    for ($i = 0; $i <= $num_station; $i++) {
                                                        foreach ($stations as $s) {
                                                            if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1' && $s['Seq'] == $i) {
                                                                $station_name = $s['StationName'];
                                                                $last_seq_station = $s['Seq'];
                                                                $pre_string = "";
                                                                if ($s['Seq'] == 1) {
                                                                    $pre_string = "ออกจาก "; //$pre_string, $station_name;
                                                                }
                                                                $width = 60 / $num_sale_station;
                                                                echo "<th style=\"width: $width%\"> $pre_string $station_name</th>";
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    //start point D
                                                    for ($i = $num_station; $i >= 0; $i--) {
                                                        foreach ($stations as $s) {
                                                            if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1' && $s['Seq'] == $i) {
                                                                $station_name = $s['StationName'];
                                                                $last_seq_station = $s['Seq'];
                                                                $pre_string = "";
                                                                if ($s['Seq'] == $num_station) {
                                                                    $pre_string = "ออกจาก "; //$pre_string, $station_name;
                                                                }
                                                                $width = 60 / $num_sale_station;
                                                                echo "<th style=\"width: $width%\">$pre_string $station_name</th>";
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                            <th  style="width: 10%;">รถเบอร์</th>
                                            <th  style="width: 10%;">เวลามาถึง</th>
                                            <th  style="width: 5%;"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($schedules as $sd) {
                                                    if ($rid == $sd['RID']) {
                                                        $seq_no_schedule = $sd['SeqNo'];
                                                        $start_time = $sd['TimeDepart'];
                                                        $vcode = $sd['VCode'];
                                                        if ($vcode == '') {
                                                            $vcode = '-';
                                                        }
                                                        ?>
                                                        <tr>                                                                         
                                                            <?php
                                                            $temp = 0;
                                                            foreach ($stations as $s) {
                                                                if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1') {
                                                                    $station_name = $s['StationName'];
                                                                    $travel_time = $s['TravelTime'];
                                                                    if ($s['Seq'] == '1') {
//                                                                                  สถานีต้นทาง
                                                                        $time_depart = strtotime($start_time);
                                                                    } elseif ($s['Seq'] == $num_station) {
//                                                                                  สถานีปลายทาง
                                                                        $time_depart = strtotime("+$route_time minutes", strtotime($start_time));
                                                                    } else {
                                                                        $temp+=$travel_time;
                                                                        $time_depart = strtotime("+$temp minutes", strtotime($start_time));
                                                                    }
                                                                    $time_depart = date('H:i', $time_depart);
                                                                    ?>
                                                                    <td class="text-center"><?= $time_depart ?></td>   
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            <td class="text-center"><strong><?= $vcode ?></strong></td>
                                                            <td></td>
                                                            <td class="text-center"> 
                                                                <a class="btn btn-success"><i class="fa fa-check-square-o"></i></a>
                                                            </td>
                                                        </tr> 
                                                        <?php
                                                    }
                                                }
                                                ?>                                                   

                                            </tbody>
                                        </table>
                                        <?php
                                    }
                                }
                                ?>

                                <?php
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="widget">
            <div class="widget-header">
                <span>ข้อมูลเวลา</span>              
            </div>
            <div class="widget-content">
                <?php
                foreach ($vehicle_types as $type) {
                    $vtid = $type['VTID'];
                    $vt_name = $type['VTDescription'];
                    echo "<legend>$vt_name</legend>";
                    $num_route = 0;
                    foreach ($routes as $route) {
                        if ($vtid == $route['VTID']) {
                            $num_route++;
                        }
                    }
                    if ($num_route <= 0) {
                        echo '<div class="col-md-12">';
                        echo ' <div class="well" style="padding-bottom: 50px;padding-top: 50px;">';
                        echo '   <p class="lead text-center">ไม่พบข้อมูล</p>';
                        echo ' </div>';
                        echo '</div> ';
                    } else {
                        ?>  
                        <div id="accordion<?= $vtid ?>" class="panel-group panel-group-lists collapse" style="">
                            <?php
                            foreach ($routes as $route) {
                                if ($vtid == $route['VTID']) {
                                    $rcode = $route['RCode'];
                                    $source = $route['RSource'];
                                    $destination = $route['RDestination'];
                                    $schedule_type = $route["ScheduleType"];
                                    $route_name = "$vt_name  " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="heading<?= $rcode . '_' . $vtid ?>">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion<?= $vtid ?>" href="#collapse<?= $rcode . '_' . $vtid ?>" aria-expanded="true" aria-controls="collapse<?= $rcode . '_' . $vtid ?>">
                                                    <?= $route_name ?>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse<?= $rcode . '_' . $vtid ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?= $rcode . '_' . $vtid ?>">
                                            <div class="panel-body">
                                                <?php
                                                foreach ($routes_detail as $rd) {
                                                    if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                                        $rid = $rd['RID'];
                                                        $source = $rd['RSource'];
                                                        $destination = $rd['RDestination'];
                                                        $schedule_type = $rd["ScheduleType"];
                                                        $start_point = $rd['StartPoint'];
                                                        $route_name = "$vt_name  " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;


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
                                                        $stations_sale_ticket = array();
                                                        $n = 0;
                                                        if ($start_point == 'S') {
                                                            for ($i = 0; $i <= $num_station; $i++) {
                                                                foreach ($stations as $s) {
                                                                    if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1' && $s['Seq'] == $i) {
                                                                        $stations_sale_ticket[$n] = $s;
                                                                        $n++;
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            for ($i = $num_station; $i >= 0; $i--) {
                                                                foreach ($stations as $s) {
                                                                    if ($rcode == $s['RCode'] && $vtid == $s['VTID'] && $s['IsSaleTicket'] == '1' && $s['Seq'] == $i) {
                                                                        $stations_sale_ticket[$n] = $s;
                                                                        $n++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <table class="table table-hover table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="<?= (int) $num_sale_station + 2; ?>"><?= $route_name ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th  style="width: 15%;">เวลาออก</th>                                                                    
                                                                    <?php
                                                                    $width = 70 / $num_sale_station;
                                                                    foreach ($stations_sale_ticket as $station) {
                                                                        $station_name = $station['StationName'];
                                                                        echo "<th style=\"width: $width%\">$station_name</th>";
                                                                    }
                                                                    ?>
                                                                    <th  style="width: 10%;">รถเบอร์</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
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

</div>