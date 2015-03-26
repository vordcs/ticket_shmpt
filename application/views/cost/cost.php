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
            <div class="row-fluid">   
                <div class="col-md-12">
                    <legend><?= $vt_name ?></legend>
                </div>                
            </div> 
            <div class="row">
                <div class="col-md-12">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" role="tablist" id="TabRoute<?= $vtid ?>">
                        <?php
                        foreach ($data['costs'] as $r) {
                            if ($vtid == $r['VTID']) {
                                $rcode = $r['RCode'];
                                $route_name = $r['RouteName'];
                                $id = $rcode . '_' . $vtid;
                                $id_active = $this->session->flashdata('RCode') . '_' . $this->session->flashdata('VTID');
                                if ($this->session->flashdata('RCode') == $rcode && $this->session->flashdata('VTID') == $vtid) {
                                    $class_tab = 'active';
                                } elseif ($num_route == 1) {
                                    $class_tab = 'active';
                                } else {
                                    $class_tab = '';
                                }
                                ?>
                                <li class="<?= $class_tab ?>">
                                    <a href="#<?= $id ?>" role="tab" data-toggle="tab"><?= $route_name ?></a>
                                </li>
                                <?php
                            }
                        }
                        ?>    
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content"> 
                        <?php
                        foreach ($data['costs'] as $r) {
                            if ($vtid == $r['VTID']) {
                                $rcode = $r['RCode'];
                                $route_name = $r['RouteName'];
                                $id = $rcode . '_' . $vtid;
                                $seller_station_name = $r['seller_station_name'];

                                if ($this->session->flashdata('RCode') == $rcode && $this->session->flashdata('VTID') == $vtid) {
                                    $class_tab_content = 'active in';
                                } elseif ($num_route == 1) {
                                    $class_tab_content = 'active in';
                                } else {
                                    $class_tab_content = '';
                                }
                                ?>
                                <div role="tabpanel" class = "tab-pane fade <?= $class_tab_content ?>" id="<?= $id ?>">
                                    <div class="col-md-12 text-center">
                                        <h3><?= $route_name ?></h3>
                                        <p class="lead">จุดจอด : <strong><?= $seller_station_name ?></strong></p>
                                    </div>

                                    <?php
                                    $routes_detail = $r['routes_detail'];
                                    $num_route_detail = count($r['routes_detail']);
                                    $source_form = '';

                                    foreach ($routes_detail as $rd) {
                                        $rid = $rd['RID'];
                                        $source = $rd['RSource'];
                                        $destination = $rd['RDestination'];
                                        $class = '';
                                        if ($num_route_detail < 2) {
                                            $class = 'col-md-offset-1';
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
                                                            $schedules = $rd['schedules'];
                                                            foreach ($schedules as $schedule) {

                                                                $tsid = $schedule['TSID'];
                                                                $time_depart = $schedule['TimeDepart'];
                                                                $vid = $schedule['VID'];
                                                                $vcode = $schedule['VCode'];

                                                                if ($vcode == '') {
                                                                    $vcode = '-';
                                                                }

                                                                $income = $schedule['Income'];
                                                                $outcome = $schedule['Outcome'];
                                                                $total = $schedule['Total'];

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
                                                                    <td class="text-right"><strong><?= number_format($total) ?></strong></td>
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
                                                            <?php } ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    $cost_along_road = $r['costs_along_road'];
                                    $num_cost_along_road = count($cost_along_road);
                                    if ($num_route_detail == 1 && $num_cost_along_road > 0) {
                                        $source_form = $cost_along_road['RSource'];
                                        ?>
                                        <div class="col-md-4 well">                                
                                            <legend>รายทาง</legend>
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%">ออกจาก</th> 
                                                        <th rowspan="2" style="width: 15%">รถเบอร์</th>                                                                                                                                  
                                                        <th rowspan="2" style="width: 20%">จำนวนเงิน</th>
                                                        <th rowspan="2" style="width: 20%"></th>
                                                    </tr>
                                                    <tr>
                                                        <th><?= $source_form ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rid_along_road = $cost_along_road['RID'];
                                                    foreach ($cost_along_road['schedules'] as $schedule) {
                                                        $tsid = $schedule['TSID'];
                                                        $time_depart = $schedule['TimeDepart'];
                                                        $vid = $schedule['VID'];
                                                        $vcode = $schedule['VCode'];
                                                        $report_id = $schedule['ReportID'];
                                                        $cost_id = $schedule['CostID'];

                                                        if ($vcode == '') {
                                                            $vcode = '-';
                                                        }

                                                        $along_road = $schedule['AlongRoad'];
                                                        $IsReport = '';
                                                        if ($schedule['ReportID'] != NULL) {
                                                            $IsReport = 'disabled';
                                                        }
                                                        $add = array(
                                                            'class' => "btn btn-info btn-sm $IsReport",
                                                            'data-toggle' => "tooltip",
                                                            'data-placement' => "top",
                                                            'title' => "เพิ่มรายรับ รายทาง รถเบอร์ $vcode รอบเวลา $time_depart",
                                                        );
                                                        $edit = array(
                                                            'class' => "btn btn-warning btn-sm $IsReport",
                                                            'data-toggle' => "tooltip",
                                                            'data-placement' => "top",
                                                            'title' => "แก้ไขรายรับ รายทาง รถเบอร์ $vcode รอบเวลา $time_depart",
                                                        );
                                                        if ($cost_id == NULL) {
                                                            $action = anchor("cost/add/1/$tsid/1", '<span class="fa fa-plus"></span>', $add);
                                                        } else {
                                                            $action = anchor("cost/edit/$cost_id/$tsid/1", '<span class="fa fa-edit"></span>', $edit);
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"><strong><?= $time_depart ?></strong></td>
                                                            <td class="text-center"><?= $vcode ?></td>
                                                            <td class="text-right"><?= number_format($along_road) ?></td>
                                                            <td class="text-center"><?= $action ?> </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                    <?php
                                }
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