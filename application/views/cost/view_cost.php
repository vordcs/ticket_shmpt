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
$route_name = ' เส้นทาง ' . $route['VTDescription'] . '  ' . $route ['RCode'] . '  ' . $route['RSource'] . ' - ' . $route['RDestination'];
$rid = $route['RID'];
$income = 0;
$outcome = 0;
foreach ($cost_types as $cost_type) {
    $cost_type_id = $cost_type['CostTypeID'];
    foreach ($costs as $cost) {
        $CostValue = $cost['CostValue'];
        if ($cost_type_id == $cost['CostTypeID']) {
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
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-header"><?= $route_name ?></div>
                <div class="widget-content">
                    <p class="lead text-center">
                        <span>รอบเวลา : <strong><?= $TimeDepart ?></strong></span>
                        &nbsp;&nbsp;
                        <span>รถเบอร์ : <strong><?= $VCode ?></strong></span>
                    </p>
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
            </div>  
        </div>
        <?php
        foreach ($cost_types as $cost_type) {
            $cost_type_id = $cost_type['CostTypeID'];
            $cost_type_name = $cost_type['CostTypeName'];
            ?>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <?= $cost_type_name ?>
                    </div>
                    <div class="widget-content">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>                    
                                    <th style="width: 60%">รายการ</th>
                                    <th style="width: 20%">จำนวนเงิน</th> 
                                    <th style="width: 20%"></th> 
                                </tr>                
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                foreach ($costs as $cost) {
                                    $CostID = $cost['CostID'];
                                    $CostDetail = $cost['CostDetail'];
                                    if ($cost['OtherCostDetail'] != NULL) {
                                        $CostDetail = $cost['OtherCostDetail'];
                                    }
                                    $CostValue = $cost['CostValue'];
                                    if ($cost_type_id == $cost['CostTypeID']) {
                                        $total +=(int) $CostValue;
                                        $edit = array(
                                            'type' => 'button',
                                            'class' => 'btn btn-warning btn-sm',
                                            'data-toggle' => "tooltip",
                                            'data-placement' => "left",
                                            'title' => "แก้ไขข้อมูล $cost_type_name : $CostDetail  ",
                                        );
                                        $delete = array(
                                            'type' => 'button',
                                            'class' => 'btn btn-danger btn-sm',
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

                                        $action_edit = anchor("cost/edit/$cost_type_id/$CostID/$rid/$TSID", '<i class="fa fa-edit"></i>', $edit);
                                        $action_delete = anchor("#", '<i class="fa fa-trash-o"></i>', $delete)
                                        ?>
                                        <tr>
                                            <td class="text-left"><?= $CostDetail ?></td>
                                            <td class="text-right"><?= number_format($CostValue) ?></td>
                                            <td class="text-center">
                                                <?= $action_edit ?>
                                                &nbsp;
                                                <?= $action_delete ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="text-center"><strong>รวม</strong></td>
                                    <td class="text-center" colspan="2"><strong><?= number_format($total); ?></strong></td>                                      
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>  
            <?php
        }
        ?>

    </div>
</div>