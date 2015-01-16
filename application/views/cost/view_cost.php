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
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-header"></div>
                <div class="widget-content">
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
                                    $CostValue = $cost['CostValue'];
                                    $total +=(int) $CostValue;
                                    if ($cost_type_id == $cost['CostTypeID']) {
                                        ?>
                                        <tr>
                                            <td class="text-left"><?= $CostDetail ?></td>
                                            <td class="text-right"><?= number_format($CostValue) ?></td>
                                            <td></td>
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