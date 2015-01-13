<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnSchedule").addClass("active");
    });
</script>
<div class="container" style="">
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
<div class="container">
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
        ?>
        <div class="row">
            <legend><?= $vt_name ?></legend>
            <?php
            if ($num_route <= 0) {
                echo '<div class="col-md-12">';
                echo ' <div class="well" style="padding-bottom: 50px;padding-top: 50px;">';
                echo '   <p class="lead text-center">ไม่พบข้อมูล</p>';
                echo ' </div>';
                echo '</div> ';
            } else {
                ?>
                <ul class="nav nav-tabs nav-justified" role="tablist" id="TabSche">
                    <?php
                    foreach ($routes as $r) {
                        $rcode = $r['RCode'];
                        $route_name = "$vt_name  $rcode" . ' ' . $r['RSource'] . ' - ' . $r['RDestination'];
                        $id = $vtid . '_' . $rcode;
                        ?>
                        <li class="">
                            <a href="#<?= $id ?>" role="tab" data-toggle="tab"><?= $route_name ?></a>
                        </li>
                        <?php
                    }
                    ?>

                </ul>

                <div class="tab-content">
                    <?php
                    foreach ($routes as $r) {
                        $rcode = $r['RCode'];
                        $vtid = $r['VTID'];
                        $vt_name = $r['VTDescription'];
                        $route_name = "$vt_name  $rcode" . ' ' . $r['RSource'] . ' - ' . $r['RDestination'];
                        $id = $vtid . '_' . $rcode;
                        ?>
                        <div role="tabpanel" class="tab-pane" id="<?= $id ?>">
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
                                    ?>
                                    <p class="lead text-center"> <?= $route_name ?></p>
                                    <table class="table table-hover table-striped table-bordered">
                                        <thead>
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
                                                            $width = 80 / $num_sale_station;
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
                                                            $width = 80 / $num_sale_station;
                                                            echo "<th style=\"width: $width%\">$pre_string $station_name</th>";
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        <th style="width: 10%">เบอร์รถ</th>

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
                        </div>  
                        <?php
                    }
                    ?>


                </div>
            <?php } ?>
        </div>
        <?php
    }
    ?>


</div>


<script>
    $(function () {
        $('#myTab a:last').tab('show')
    })
</script>