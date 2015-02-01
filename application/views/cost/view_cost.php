<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCost").addClass("active");
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
<?php
$tsid = $data['TSID'];
$rid = $data['RID'];
$IsReport = '';
if ($data['ReportID'] != NULL) {
    $IsReport = 'disabled';
}
?>
<div class="container">
    <div class="row">
        <div class="widget">
            <div class="widget-header">
                <?= $data['RouteName'] ?>
            </div>
            <div class="widget-content">
                <div class="col-md-12">
                    <p class="lead text-center">
                        <span>รอบเวลา : <strong><?= $TimeDepart ?></strong></span>
                        &nbsp;&nbsp;
                        <span>รถเบอร์ : <strong><?= $VCode ?></strong></span>
                    </p>
                    <div class="stats-box">
                        <div class="col-md-4">
                            <div class="stats-box-title">รายรับ</div>
                            <div class="stats-box-all-info"><i class="fa fa-arrow-circle-o-down" style="color:#3366cc;"></i><?= number_format($data['Income']) ?></div>                            
                        </div>

                        <div class="col-md-4">
                            <div class="stats-box-title">รายจ่าย</div>
                            <div class="stats-box-all-info"><i class="fa fa-arrow-circle-o-up" style="color:#F30"></i><?= number_format($data['Outcome']) ?></div>                         
                        </div>

                        <div class="col-md-4">
                            <div class="stats-box-title">คงเหลือ</div>
                            <div class="stats-box-all-info"><i class="fa fa-shopping-cart" style="color:#3C3"></i><?= number_format($data['Total']) ?></div>                            
                        </div>                                
                    </div> 
                </div>
                <?php
                foreach ($cost_types as $cost_type) {
                    $total = 0;
                    $cost_type_id = $cost_type['CostTypeID'];
                    $cost_type_name = $cost_type['CostTypeName'];
                    $cost_in_schedule = array();
                    if ($cost_type_id == 1) {
                        $cost_in_schedule = $data['Costs']['Income'];
                    }
                    if ($cost_type_id == 2) {
                        $cost_in_schedule = $data['Costs']['Outcome'];
                    }
                    ?>
                    <div class="col-md-6">
                        <legend class="text-center"><?= $cost_type_name ?></legend>
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>                    
                                    <th style="width: 60%">รายการ</th>
                                    <th style="width: 20%">จำนวนเงิน</th> 
                                    <th style="width: 20%"></th> 
                                </tr>                
                            </thead>
                            <tbody>
                                <!--ตั๋วโดยสาร-->
                                <?php
                                $num_ticket = count($data['Tickets']);
                                if ($num_ticket > 0 && $cost_type_id == 1) {
                                    foreach ($data['Tickets'] as $ticket) {
                                        $num = $ticket['num_ticket'];
                                        $total_price_ticket = $ticket['total_price_ticket'];
                                        $total+=$total_price_ticket;
                                        ?>
                                        <tr>
                                            <td class="text-left">ตั๋วโดยสารรอบเวลา : <?= $data['TimeDepart'] . " ( $num ที่นั่ง) " ?></td>
                                            <td class="text-right" colspan=""><?= number_format($total_price_ticket, 1) ?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <!--ค่าใช้จ่ายที่เกิดขึ้น-->
                                <?php
                                foreach ($cost_in_schedule as $cost) {
                                    $CostID = $cost['CostID'];
                                    $CostDetail = $cost['CostDetail'];
                                    if ($cost['OtherCostDetail'] != NULL) {
                                        $CostDetail = $cost['OtherCostDetail'];
                                    }

                                    $CostValue = $cost['CostValue'];
                                    $total +=$CostValue;

                                    $edit = array(
                                        'type' => 'button',
                                        'class' => "btn btn-warning btn-sm $IsReport",
                                        'data-toggle' => "tooltip",
                                        'data-placement' => "left",
                                        'title' => "แก้ไขข้อมูล $cost_type_name : $CostDetail  ",
                                    );
                                    $delete = array(
                                        'type' => 'button',
                                        'class' => "btn btn-danger btn-sm $IsReport",
                                        'data-toggle' => "tooltip",
                                        'data-placement' => "right",
                                        'title' => "ลบข้อมูล $cost_type_name : $CostDetail",
                                        'data-id' => "2",
                                        'data-title' => "ลบข้อมูล $cost_type_name",
                                        'data-sub_title' => "$CostDetail",
                                        'data-info' => "$CostValue",
                                        'data-toggle' => "modal",
                                        'data-target' => "#confirm",
                                        'data-href' => "cost/delete/$CostID/$TSID",
                                    );

                                    $action_edit = anchor("cost/edit/$CostID/$TSID", '<i class="fa fa-edit"></i>', $edit);
                                    $action_delete = anchor("#", '<i class="fa fa-trash-o"></i>', $delete)
                                    ?>
                                    <tr>
                                        <td><?= $CostDetail ?></td>
                                        <td class="text-right"><?= number_format($CostValue, 1) ?></td>
                                        <td class="text-center">
                                            <?= $action_edit ?>
                                            &nbsp;
                                            <?= $action_delete ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr class="success">
                                    <td class="text-center"><strong>รวม</strong></td>
                                    <td class="text-center" colspan="2"><strong><?= number_format($total, 1) ?></strong></td>                                      
                                </tr>
                            </tbody>
                        </table>
                        <div class="col-md-12 text-right">
                            <?php
                            $add = array(
                                'type' => "button",
                                'class' => "btn btn-info $IsReport",
                                'data-toggle' => "tooltip",
                                'data-placement' => "top",
                                'title' => "เพิ่ม $cost_type_name รถเบอร์ $VCode รอบเวลา $TimeDepart",
                            );
                            echo anchor("cost/add/$cost_type_id/$tsid/", '<span class="fa fa-plus"></span>&nbsp;' . $cost_type_name, $add) . '  ';
                            ?> 

                        </div>
                    </div>
                    <?php
                }
                ?>  
            </div>
        </div>
    </div>
</div>
