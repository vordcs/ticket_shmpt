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

<div class="container">
    <?php
    foreach ($reports as $report) {
        $VTID = $report['VTID'];
        $VTName = $report['VTName'];
        ?>
        <div class="row">
            <legend><?= $VTName ?></legend>
            <div class="col-md-12">
                <ul class="nav nav-tabs nav-justified" role="tablist" id="TabRoute<?= $VTID ?>">
                    <?php
                    $num_route = count($report['routes']);
                    foreach ($report['routes'] as $route) {
                        $rcode = $route['RCode'];
                        $vtid = $VTID;
                        $route_name = $route['RouteName'];

                        $id = $rcode . "_" . $vtid;
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
                    <?php } ?>                     
                </ul>
                <!-- Tab panes -->
                <div class="tab-content"> 
                    <?php
                    foreach ($report['routes'] as $route) {
                        $rcode = $route['RCode'];
                        $route_name = $route['RouteName'];

                        $seller_station_id = $route['seller_station_id'];
                        $seller_station_name = $route['seller_station_name'];

                        $id = $rcode . "_" . $VTID;

                        if ($this->session->flashdata('RCode') == $rcode && $this->session->flashdata('VTID') == $VTID) {
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
                            <div class="col-md-12 text-right">
                                <?php
                                $send_report = array(
                                    'class' => "btn btn-lg btn-info",
                                    'type' => "button",
                                    'data-toggle' => "tooltip",
                                    'data-placement' => "top",
                                    'title' => "ส่งรายงาน $route_name",
                                );
                                echo anchor("report/send/$rcode/$VTID/$seller_station_id", '<i class="fa fa-send-o"></i>&nbsp;&nbsp;ส่งเงิน ' . $route_name, $send_report);
                                ?>                                      
                            </div>
                            <div class="col-md-12 <?= (count($route['Reports']) > 0) ? '' : 'hidden' ?>" style="padding-top: 3% ; padding-bottom: 3%;">
                                <?php if (count($route['Reports']) > 0) { ?>
                                    <table class="table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 15%">เวลาที่ส่ง</th>
                                                <th style="width: 15%">จำนวนเงิน</th>
                                                <th style="width: 15%">เบี้ยเลี้ยง</th>
                                                <th style="width: 20%">ยอดคงเหลือ</th>
                                                <th style="width: 15%">สถานะ</th>
                                                <th style="width: 15%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total = 0;
                                            $total_vage = 0;
                                            $total_net = 0;
                                            foreach ($route['Reports'] as $report) {
                                                $ReportID = $report['ReportID'];
                                                $ReportTime = date('H:s', strtotime($report['ReportTime']));
                                                $Total = $report['Total'];
                                                $Vage = $report['Vage'];
                                                $Net = $report['Net'];
                                                $print_report = array(
                                                    'class' => "btn",
                                                    'type' => "button",
                                                    'data-toggle' => "tooltip",
                                                    'data-placement' => "top",
                                                    'title' => "พิมพ์",
                                                );

                                                $total+=$Total;
                                                $total_vage+=$Vage;
                                                $total_net+=$Net;
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= $ReportTime ?></td>
                                                    <td class="text-center"><?= number_format($Total) ?></td>
                                                    <td class="text-center"><?= number_format($Vage) ?></td>
                                                    <td class="text-right"> <strong><?= number_format($Net) ?></strong></td>
                                                    <td class="text-center"><i class="fa fa-check fa-lg" style="color: #37BC9B" data-toggle="tooltip" data-placement="left" title="ส่งเเล้ว"></</td>
                                                    <td class="text-center">
                                                        <?= anchor("report/print_report/$rcode/$vtid/$seller_station_id/$ReportID", '<i class="fa fa-print fa-lg"></i> พิมพ์รายงาน', $print_report) ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class=" text text-center">รวม</td>
                                                <td class="text-right"><strong><?= number_format($total) ?></strong></td>
                                                <td class="text-right"><strong><?= number_format($total_vage) ?></strong></td>
                                                <td class="text-right text"><?= number_format($total_net) ?></td>
                                                <td class="text-center" colspan="2">
                                                    <?= anchor("report/print_report/$rcode/$vtid/$seller_station_id/$ReportID", '<i class="fa fa-print fa-lg"></i> พิมพ์รายงานทั้งหมด', $print_report) ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                <?php } ?>
                            </div>
                            <?php
                            $num_route_detail = count($route['routes_detail']);
                            $num_cost_along_road = count($route['cost_along_road']);
                            foreach ($route['routes_detail'] as $rd) {
                                $r_destination_name = $rd['RDestination'];
                                ?>
                                <div class="col-md-6 <?= ($num_cost_along_road > 0) ? 'col-md-offset-1' : '' ?>">
                                    <legend><i>ไป</i>&nbsp;&nbsp;<strong> <?= $r_destination_name ?></strong></legend>
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
                                            foreach ($rd['schedules'] as $schedule) {
                                                $TSID = $schedule['TSID'];
                                                $TimeDepart = $schedule['TimeDepart'];
                                                $Income = $schedule['Income'];
                                                $Outcome = $schedule['Outcome'];
                                                $Total = $schedule['Total'];

                                                $ReportID = $schedule['ReportID'];

                                                if ($ReportID != NULL) {
                                                    $status_report = '<i class="fa fa-check-square-o fa-lg" style="color: #37BC9B" data-toggle="tooltip" data-placement="left" title="ส่งเเล้ว"></i>';
                                                } else {
                                                    $status_report = '<i class="fa fa-square-o fa-lg" style="color:  #DA4453" data-toggle="tooltip" data-placement="left" title="ยังไม่ส่ง" ></i>';
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= $TimeDepart ?></td>
                                                    <td class="text-center"><?= $Income ?></td>
                                                    <td class="text-center"><?= $Outcome ?></td>
                                                    <td class="text-center"><?= $Total ?></td>
                                                    <td class="text-center"><?= $status_report ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                            }
                            if ($num_route_detail == 1 && $num_cost_along_road > 0) {
                                $cost_along_road = $route['cost_along_road'];
                                $source_form = $cost_along_road['RSource'];
                                ?>
                                <div class="col-md-4">
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

                                                $cost_id = $schedule['CostID'];

                                                if ($vcode == '') {
                                                    $vcode = '-';
                                                }
                                                $along_road = $schedule['AlongRoad'];

                                                $ReportID = $schedule['ReportID'];

                                                if ($ReportID != NULL) {
                                                    $status_report = '<i class="fa fa-check-square-o fa-lg" style="color: #37BC9B" data-toggle="tooltip" data-placement="left" title="ส่งเเล้ว"></i>';
                                                } else {
                                                    $status_report = '<i class="fa fa-square-o fa-lg" style="color:  #DA4453" data-toggle="tooltip" data-placement="left" title="ยังไม่ส่ง" ></i>';
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><strong><?= $time_depart ?></strong></td>
                                                    <td class="text-center"><?= $vcode ?></td>
                                                    <td class="text-right"><?= $along_road ?></td>
                                                    <td class="text-center"><?= $status_report ?> </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
