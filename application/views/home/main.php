<script>
    $(document).ready(function () {

    });
</script>
<br>
<div class="container">     
    <div class="jumbotron" style="padding-bottom: 2%;">
        <div class="container">
            <div class="col-md-12">
                <h1>ยินดีต้อนรับ พนักงานขายตั๋ว สถานีขอนแก่น</h1>                
                <p></p> 
            </div>
            <div class="col-md-12">            
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">

        </div>
    </div>

    <div class="row">
        <div class="col-md-4" style="" >
            <div class="widget widget-nopad">
                <div class="widget-header">                     
                    <span class="fa fa-list-alt">  รายรับ-รายจ่าย</span>
                </div>
                <!-- /widget-header -->
                <div class="widget-content">
                    <div class="widget big-stats-container">
                        <div class="widget-content">
                            <h6 class="bigstats">A fully responsive premium quality admin template built on Twitter Bootstrap by <a href="http://www.egrappler.com" target="_blank">EGrappler.com</a>.  These are some dummy lines to fill the area.</h6>
                            <div id="big_stats" class="cf">                             
                                <!-- .stat -->
                                <div class="stat"> <i class="fa fa-thumbs-up"></i> <span class="value">423</span> </div>
                                <!-- .stat -->
                                <div class="stat"> <i class="fa fa-twitter"></i> <span class="value">922</span> </div>
                                <!-- .stat -->
                                <div class="stat"> <i class=" fa fa-bullhorn"></i> <span class="value">25%</span> </div>
                                <!-- .stat --> 
                            </div>
                            <a class="btn btn-link pull-right">ดูเพิ่มเติม...</a>
                        </div>    
                    </div>
                </div>
            </div>         
        </div>
        <div class="col-md-8">
            <div class="widget widget-nopad">
                <div class="widget-header">                      
                    <span class="fa fa-user">&nbsp;ข้อมูลผู้ใช้งาน</span>
                </div>
                <div class="widget-content">
                    <div class="col-md-12" style="padding-top: 1%;padding-bottom: 2%;">
                        <div class="col-md-3 text-center">
                            <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
                            <br>
                            รหัสพนักงาน : 1234567891011012
                        </div>
                        <div class="col-md-9">
                            <span class="lead" >ชื่อ นามสกุล</span>
                            <strong></strong>                                   
                            <table class="table table-condensed table-responsive">
                                <tbody>
                                    <tr>
                                        <td>User level:</td>
                                        <td>Administrator</td>
                                    </tr>
                                    <tr>
                                        <td>Registered since:</td>
                                        <td>11/12/2013</td>
                                    </tr>
                                    <tr>
                                        <td>Topics</td>
                                        <td>15</td>
                                    </tr>
                                    <tr>
                                        <td>Warnings</td>
                                        <td>0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <a class="btn btn-primary pull-right" href="" role="button">ข้อมูลพนักงาน</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">   
        <div class="widget widget-nopad">
            <div class="widget-header">                     
                <span class="">เส้นทางเดินรถ</span>
            </div>
            <div class="widget-content">
                <div class="col-md-12" style="padding-top: 1%;">
                    <div id="accordion2" class="panel-group panel-group-lists collapse" style="">
                        <?php
                        foreach ($vehicle_types as $type) {
                            $vtid = $type['VTID'];
                            $vt_name = $type['VTDescription'];
                            ?>
                            <legend><?= $vt_name ?></legend>
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
                                        <div class="panel">
                                            <div class="panel-heading"> 
                                                <h4 class="panel-title">
                                                    <a class="collapsed" href="#collapse<?= "$rcode$vtid" ?>" data-parent="#accordion<?= $vtid ?>" data-toggle="collapse">
                                                        <?= $route_name ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse<?= "$rcode$vtid" ?>" class="panel-collapse collapse" style="height: 0px;">
                                                <div class="panel-body">
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
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>  
                            <?php
                        }
                        ?>                    

                    </div>
                </div>
            </div>     
        </div> 
    </div>


    <?php echo js('docs.min.js?v=' . $version); ?>  

