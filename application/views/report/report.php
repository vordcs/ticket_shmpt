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
<div class="container">
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
                    $rcode = $r['RCode'];
                    $route_name = "$vt_name  $rcode" . ' ' . $r['RSource'] . ' - ' . $r['RDestination'];
                    $id = $vtid . '_' . $rcode;
                    ?>                         
                    <div class="widget">
                        <div class="widget-header">
                            <?= $route_name ?>
                        </div>
                        <div class="widget-content">
                            <div class="col-md-12" style="padding-bottom: 2%">
                                <div class="stats-box">
                                    <div class="col-md-4">
                                        <div class="stats-box-title">Vizitor</div>
                                        <div class="stats-box-all-info"><i class="icon-user" style="color:#3366cc;"></i> 555K</div>
                                        <div class="wrap-chart">

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="stats-box-title">Likes</div>
                                        <div class="stats-box-all-info"><i class="icon-thumbs-up" style="color:#F30"></i> 66.66</div>
                                        <div class="wrap-chart">                                           

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="stats-box-title">Orders</div>
                                        <div class="stats-box-all-info"><i class="icon-shopping-cart" style="color:#3C3"></i> 15.55</div>
                                        <div class="wrap-chart">
                                        </div>
                                    </div>                                
                                </div>                            
                            </div>                          

                            <?php
                            foreach ($routes_detail as $rd) {
                                if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                    $rid = $rd['RID'];
                                    $source = $rd['RSource'];
                                    $destination = $rd['RDestination'];
                                    $schedule_type = $rd["ScheduleType"];
                                    $start_point = $rd['StartPoint'];
                                    $route_time = $rd['Time'];
                                    $route_name = '<i> ไป </i> ' . $destination;

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
                                    <div class="col-md-12">
                                        <legend><?= $route_name ?></legend>
                                    </div>
                                    <div class="col-md-6">                                        
                                        <table class="table-bordered overflow-y">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">รอบเวลา</th>
                                                    <th style="width: 20%">รายรับ</th>
                                                    <th style="width: 20%">รายจ่าย</th> 
                                                    <th style="width: 20%">คงเหลือ</th> 
                                                    <th style="width: 25%">เบอร์</th>                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($schedules as $schedule) {
                                                    if ($rid == $schedule['RID']) {
                                                        $tsid = $schedule['TSID'];
                                                        $seq_no_schedule = $schedule['SeqNo'];
                                                        $start_time = strtotime($schedule['TimeDepart']);
                                                        $vcode = $schedule['VCode'];
                                                        if ($vcode == '') {
                                                            $vcode = '-';
                                                        }

                                                        $time_depart = date('H:i', $start_time);
                                                        ?>
                                                        <tr>    
                                                            <td class="text-center"><?= $time_depart ?></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-center"><strong><?= $vcode ?></strong></td>
                                                        </tr> 
                                                        <?php
                                                    }
                                                }
                                                ?>  
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
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
                                                foreach ($schedules as $schedule) {
                                                    if ($rid == $schedule['RID']) {
                                                        $tsid = $schedule['TSID'];
                                                        $seq_no_schedule = $schedule['SeqNo'];
                                                        $start_time = strtotime($schedule['TimeDepart']);
                                                        $vcode = $schedule['VCode'];
                                                        if ($vcode == '') {
                                                            $vcode = '-';
                                                        }

                                                        $time_depart = date('H:i', $start_time);
                                                        $income = 1567;
                                                        $outcome = 123;
                                                        ?>
                                                        <tr>    
                                                            <td class="text-center"><?= $time_depart ?></td>                                                           
                                                            <td><?= $income ?></td>
                                                            <td><?= $outcome ?></td>
                                                        </tr> 
                                                        <?php
                                                    }
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