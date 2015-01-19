<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnReport").addClass("active");
    });
</script>
<style>
    .info-box {
        background:#ffffff;
        border:1px solid #c9c9c9;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;

        margin-bottom: 30px;
    }

    .stats-box {
        margin:40px 0px;
        color:#5f5f5f;
    }
    .stats-box-title {
        text-align:center;
        font-weight:bold;
    }
    .stats-box-all-info {
        text-align:center;
        font-weight:bold;
        font-size:48px;
        margin-top:20px;
        margin-bottom: 40px;
    }
    .stats-box-all-info i{
        width:60px;
        height:60px;
    }
</style>
<div class="container-fluid">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;"><?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
</div>
<div class="container-fluid">   
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
            ?>
            <div class="row-fluid ">   
                <!--<legend><? $vt_name ?></legend>-->              
                <?php
                foreach ($routes as $r) {

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
                    /*
                     * สรุปข้อมูลรายรับรายจ่าย 
                     */
                    $income = 0;
                    $outcome = 0;
                    foreach ($cost_types as $cost_type) {
                        $cost_type_id = $cost_type['CostTypeID'];
                        foreach ($costs as $cost) {
                            $CostValue = $cost['CostValue'];
                            if ($cost_type_id == $cost['CostTypeID'] && $seller_station_id == $cost['SID']) {
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
                    ?>                         
                    <div class="widget">
                        <div class="widget-header">
                            <?= $route_name ?>
                        </div>
                        <div class="widget-content">
                            <div class="col-md-12 text-center">
                                <h3><?= $route_name ?></h3>
                                <p class="lead">จุดจอด : <strong><?= $seller_station_name ?></strong></p>
                            </div>
                            <div class="col-md-12" style="padding-bottom: 0%">
                                <div class="stats-box">
                                    <div class="col-md-4">
                                        <div class="stats-box-title">รายรับ</div>
                                        <div class="stats-box-all-info"><i class="fa fa-arrow-circle-o-down" style="color:#3366cc;"></i><?= number_format($income) ?></div>                            
                                    </div>

                                    <div class="col-md-4">
                                        <div class="stats-box-title">รายจ่าย</div>
                                        <div class="stats-box-all-info"><i class="fa fa-arrow-circle-o-up" style="color:#F30"></i><?= number_format($outcome) ?></div>                         
                                    </div>

                                    <div class="col-md-4">
                                        <div class="stats-box-title">คงเหลือ</div>
                                        <div class="stats-box-all-info"><i class="fa fa-shopping-cart" style="color:#3C3"></i><?= number_format($income - $outcome) ?></div>                            
                                    </div>                                
                                </div>    
                            </div>  
                            <div class="col-md-12 text-right">
                                <?php
                                $send_report = array(
                                    'class' => "btn btn-lg btn-info",
                                    'type' => "button",
                                    'data-toggle' => "tooltip",
                                    'data-placement' => "top",
                                    'title' => "ส่งรายงาน $route_name",
                                );
                                echo anchor("report/send/$rcode/$vtid/$seller_station_id", '<i class="fa fa-send-o"></i>&nbsp;&nbsp;ส่งรายงาน', $send_report);
                                ?>                                      
                            </div>

                            <?php
                            foreach ($routes_detail as $rd) {
                                if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                    $rid = $rd['RID'];
                                    $source = $rd['RSource'];
                                    $destination = $rd['RDestination'];
                                    $start_point = $rd['StartPoint'];


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

                                    $class = ' ';
                                    if ($seller_station_seq == 1 || $seller_station_seq == $num_station) {
                                        $class = 'hidden';
                                    }
                                    ?>

                                    <div class="col-md-12 <?= $class ?>">
                                        <legend><?= '<i> ไป </i> ' . $destination ?></legend>
                                    </div>
                                    <div class="col-md-12 <?= $class ?>">
                                        <div class="col-md-6">  
                                            <table class="table table-hover table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%">รอบเวลา</th>
                                                        <th style="width: 20%">รายรับ</th>
                                                        <th style="width: 20%">รายจ่าย</th> 
                                                        <th style="width: 20%">คงเหลือ</th> 
                                                        <th style="width: 20%">สถานะ</th>                                                   
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $data_graph = array();
                                                    foreach ($schedules_in_route as $schedule) {
                                                        $tsid = $schedule['TSID'];
                                                        $start_time = strtotime($schedule['TimeDepart']);
                                                        $time_depart = ' - ';
                                                        $travel_time = 0;
                                                        $report_id = $schedule['ReportID'];
                                                        $i = 0;
                                                        foreach ($stations_in_route as $s) {
                                                            if ($s['IsSaleTicket'] == '1') {
                                                                $station_id = $s['SID'];
                                                                $station_name = $s['StationName'];
                                                                $station_seq = $s['Seq'];
                                                                $temp_travel_time = $s['TravelTime'];

                                                                if ($station_seq == '1' || $station_seq == $num_station) {
                                                                    $time = $start_time;
                                                                } else {
                                                                    $travel_time+=$temp_travel_time;
                                                                    $time = strtotime("+$travel_time minutes", $start_time);
                                                                }
                                                                if ($seller_station_id == $station_id) {
                                                                    $time_depart = date('H:i', $time);
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        /*
                                                         * รายรับ,รายจ่าย
                                                         */
                                                        $income = 0;
                                                        $outcome = 0;
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

                                                        $temp_data_graph = array(
                                                            'TSID' => $tsid,
                                                            'SID' => $seller_station_id,
                                                            'TimeDepart' => $time_depart,
                                                            'Income' => $income,
                                                            'Outcome' => $outcome,
                                                        );
                                                        array_push($data_graph, $temp_data_graph);
                                                        $status_report = '<i class="fa fa-square-o fa-lg" style="color:  #DA4453" ></i>&nbsp;ยังไม่ส่ง';
                                                        if ($report_id != NULL) {
                                                            $status_report = '<i class="fa fa-check-square-o fa-lg" style="color: #37BC9B" ></i>&nbsp;ส่งเเล้ว';
                                                        }
                                                        ?>

                                                        <tr>    
                                                            <td class="text-center"><?= $time_depart ?></td>
                                                            <td class="text-center"><?= number_format($income) ?></td>
                                                            <td class="text-center"><?= number_format($outcome) ?> </td>
                                                            <td class="text-right"><strong><?= number_format($income - $outcome) ?></strong></td>
                                                            <td class="text-center"><?= $status_report ?></td>
                                                        </tr> 
                                                        <?php
                                                    }
                                                    ?>  
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <?"จำนวนข้อมูล : " . count($data_graph) ?>
                                            <table class="highchart" data-graph-container-before="1" data-graph-type="column" style="display:none">
                                                <thead>
                                                    <tr>                                  
                                                        <th>รอบเวลา</th>
                                                        <th>รายรับ</th>
                                                        <th>รายจ่าย</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($data_graph) > 0) {
                                                        foreach ($data_graph as $data) {
                                                            ?>
                                                            <tr>
                                                                <td><?= $data['TimeDepart'] ?></td>
                                                                <td><?= $data['Income'] ?></td>
                                                                <td><?= $data['Outcome'] ?></td>
                                                            </tr>

                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <?php ?>
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
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }
    ?>
</div>


<script>
    $(document).ready(function () {
        $('table.highchart').highchartTable();

    });

</script>