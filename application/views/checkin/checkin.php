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
        <div class="col-md-12">
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
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel-group panel-group-lists collapse in" id="accordionRoutes" role="tablist" aria-multiselectable="true">
                <?php
                $n = 1;
                foreach ($data as $route) {
                    $rcode = $route['RCode'];
                    $vtid = $route['VTID'];
                    $route_name = $route['RouteName'];

                    $seller_station_id = $route['seller_station_id'];
                    $seller_station_name = $route['seller_station_name'];

                    $id = $rcode . "_" . $vtid;

                    $rcode_active = $this->session->flashdata('RCode');
                    $vtid_active = $this->session->flashdata('VTID');

                    if (count($data) == 1 || ($rcode == $rcode_active && $vtid == $vtid_active)) {
                        $class_active = 'in';
                    } elseif ($n == 1) {
                        $class_active = '';
                    } else {
                        $class_active = '';
                    }
                    $n++;
                    ?>
                    <div class="panel">
                        <div class="panel-heading" role="tab" id="heading<?= $id ?>">
                            <h4 class="panel-title" style="padding-left: 1%;">
                                <a data-toggle="collapse" data-parent="#accordionRoutes" href="#collapse<?= $id ?>" aria-expanded="true" aria-controls="collapse<?= $id ?>" style="font-size: 1.4em;">
                                    <?= $route_name ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?= $id ?>" class="panel-collapse collapse <?= $class_active ?> " role="tabpanel" aria-labelledby="heading<?= $id ?>">
                            <div class="panel-body">
                                <div class="col-md-12 text-center">
                                    <h3><?= $route_name ?></h3>
                                    <p class="lead">จุดจอด : <strong><?= $seller_station_name ?></strong></p>
                                </div>
                                <?php
                                foreach ($route['routes_detail'] as $rd) {
                                    $source_name = $rd['RSource'];
                                    $destination_name = $rd['RDestination'];
                                    ?>
                                    <div class="col-md-6 <?= (count($route['routes_detail']) == 1) ? 'col-md-offset-3' : '' ?>">
                                        <div class="widget">
                                            <div class="widget-header">
                                                <i>ไป</i>
                                                <strong>
                                                    <?= $destination_name ?>   
                                                </strong>
                                            </div>
                                            <div class="widget-content">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%">ออกจาก <?= $source_name ?></th> 
                                                            <th style="width: 20%">รถเบอร์</th>                                                                                                                                  
                                                            <th style="width: 20%"><?= $seller_station_name ?> </th>
                                                            <th style="width: 10%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($rd['schedules'] as $schedule) {
                                                            $TSID = $schedule['TSID'];
                                                            $TimeDepart = $schedule['TimeDepart'];
                                                            $ReportID = $schedule['ReportID'];
                                                            $VCode = $schedule['VCode'];
                                                            $CheckInID = $schedule['CheckInID'];
                                                            $TimeCheckIn = $schedule['TimeCheckIn'];


                                                            $class_checkin = '';

                                                            if ($ReportID != NULL) {
                                                                $class_checkin = 'disabled';
                                                            }

                                                            $action = '';
                                                            if ($CheckInID == NULL) {
                                                                $add = array(
                                                                    'class' => "btn btn-sm btn-success $class_checkin",
                                                                    'type' => "button",
                                                                    'data-id' => "1",
                                                                    'data-title' => "ลงเวลา รถเบอร์ $VCode",
                                                                    'data-sub_title' => "รอบเวลา",
                                                                    'data-info' => "$TimeDepart (ออกจาก : $seller_station_name )",
                                                                    'data-toggle' => "modal",
                                                                    'data-target' => "#confirm",
                                                                    'data-href' => "checkin/add/$rcode/$vtid/$TSID/$seller_station_id",
                                                                );
                                                                $action = anchor('#', '<i class="fa fa-check-square-o"></i>', $add);
                                                            } else {
                                                                $edit = array(
                                                                    'class' => "btn btn-sm btn-warning $class_checkin",
                                                                    'type' => "button",
                                                                    'data-id' => "1",
                                                                    'data-title' => "แก้ไข เวลา รถเบอร์ $VCode",
                                                                    'data-sub_title' => "รอบเวลา",
                                                                    'data-info' => "$TimeDepart (ออกจาก $seller_station_name)",
                                                                    'data-toggle' => "modal",
                                                                    'data-target' => "#confirm",
                                                                    'data-href' => "checkin/edit/$rcode/$vtid/$CheckInID",
                                                                );
                                                                $action = anchor('#', '<i class="fa fa-edit"></i>', $edit);
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td class="text-center"><?= $TimeDepart ?></td>
                                                                <td class="text-center"><?= $VCode ?></td>
                                                                <td class="text-center"><strong><?= $TimeCheckIn ?></strong></td>
                                                                <td class="text-center"><?= $action ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>        
    </div>
</div>
