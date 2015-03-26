<script>
    var net = 0;
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnReport").addClass("active");
    });
    function print_report() {
        window.print();
    }
</script>
<style>    
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
    /*----------------------*/
    .print-report{    
        background: white;        
        font-family: 'sans-serif';
    }  
    .header-report-lg{
        text-align: center;
        font-size: 7pt;
    } 
    .header-report-sm{
        text-align: center;
        font-size: 6pt;
    }
    .header-report-xs{
        text-align: center;
        font-size: 4pt;
    }
    .title-lg{
        text-align: center;
        font-size: 7pt;
    }
    .title-sm{
        text-align: center;
        font-size: 6pt;
    }
    .title-xs{
        text-align: center;
        font-size: 6pt;
    }
    .sub-title-lg{
        padding-top: 2px;
        font-size: 6pt;
    }
    .sub-title-sm{
        padding-top: 2px;
        font-size: 5pt;        
    }
    .sub-title-xs{
        padding-top: 2px;
        font-size: 4pt;
    }
    .text-body-lg{
        padding-top: 2px;
        padding-left: 2px;
        font-size: 9pt;
    }
    .text-body{
        padding-top: 2px;
        padding-left: 3px;
        font-size: 6pt;
    }
    .text-body-small{
        padding-top: 2px;
        padding-left: 2px;
        font-size: 4pt;
    }
    .note{
        padding-top: 2px;
        font-size: 4pt;
    }
    @media all
    {
        .print-report	{ display:none; }         
    }
    @media print
    {
        .print-report	{ display:block;height:1px; page-break-before:always; }
    }
</style>
<div class="container">
    <div class="row hidden-print">        
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
<div class="container print-report">
    <div class="row">        
        <div class="col-md-3 col-md-offset-6 header-report-sm">
            <strong>รายงานส่งเงิน</strong>
        </div>
        <div class="col-md-3 col-md-offset-6 header-report-lg">
            <?= $data_reports['RouteName'] ?>  
        </div>
        <div class="col-md-3 col-md-offset-6 header-report-lg">
            <strong>
                <?= $data_reports['SellerStationName'] ?>  
            </strong>
        </div> 
        <div class="col-md-3 col-md-offset-6 header-report-xs">
            <?= $this->m_datetime->getDateThaiStringShort() ?>
            &nbsp;|&nbsp;
            <?= $this->m_datetime->getTimeNow() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-offset-6">
            <?php foreach ($data_reports['details']as $detail) { ?>
                <span class="title-sm"><i>ไป</i>&nbsp;<strong><?= $detail['DestinationName'] ?></strong></span>
                <?php
                foreach ($detail['schedules'] as $schedule) {
                    ?>
                    <table>
                        <thead>
                            <tr class="hidden-print">
                                <th style="width: 20%"></th>
                                <th style="width: 30%"></th>
                                <th style="width: 20%"></th>
                                <th style="width: 30%"></th>
                            </tr>                        
                        </thead>
                        <tbody>
                            <tr class="">
                                <td class="text-right sub-title-xs">เวลาออก :</td>
                                <td class="text-left sub-title-lg"><strong><?= $schedule['TimeDepart'] ?></strong></td>
                                <td class="text-right sub-title-xs">เบอร์รถ :</td>
                                <td class="text-left sub-title-lg"><strong><?= $schedule['VCode'] ?></strong></td>
                            </tr>                       
                        </tbody>
                    </table>
                    <table class="table-bordered">
                        <thead>
                            <tr class="">                                
                                <th style="width: 60%" class="sub-title-xs text-center">รายการ</th>
                                <th style="width: 10%" class="sub-title-xs text-center">จำนวน</th>
                                <th style="width: 20%" class="sub-title-xs text-center">เป็นเงิน</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="<?= (count($schedule['tickets']) > 0 || count($schedule['Incomes']) > 0) ? "" : "hidden" ?>">
                                <td colspan="3" class="sub-title-sm"><u>รายรับ</u></td>
                        </tr>
                        <?php
                        $total_income = 0;
                        $total_outcome = 0;
                        foreach ($schedule['tickets'] as $ticket) {
                            $NumberTicket = $ticket['NumberTicket'];
                            $NumberPriceFull = $ticket['NumberPriceFull'];
                            $NumberPriceDiscount = $ticket['NumberPriceDiscount'];
                            $DestinationName = $ticket['DestinationName'];
                            if ($NumberTicket != $NumberPriceFull) {
                                $DestinationName .="  ($NumberPriceFull/$NumberPriceDiscount)";
                            }
                            $Total = $ticket['Total'];
                            $total_income+=$Total;
                            ?>
                            <tr>
                                <td class="text-body"><strong><?= $DestinationName ?></strong></td>
                                <td class="text-center text-body-small"><?= $NumberTicket ?></td>
                                <td class="text-right text-body"><?= number_format($Total) ?></td>
                            </tr>
                        <?php } ?>
                        <?php
                        foreach ($schedule['Incomes'] as $income) {
                            $CostDetail = $income['CostDetail'];
                            if ($income['OtherCostDetail'] != NULL) {
                                $CostDetail = $income['OtherCostDetail'];
                            }
                            $Total = $income['Total'];
                            $total_income+=$Total;
                            ?>
                            <tr>
                                <td class="text-body-small"><?= $CostDetail ?></td>
                                <td class="text-center text-body-small"><?= $income['Number'] ?></td>
                                <td class="text-right text-body"><?= number_format($Total) ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="<?= (count($schedule['Outcomes']) > 0) ? "" : "hidden" ?>">
                            <td colspan="3" class="sub-title-sm"><u>รายจ่าย</u></td>
                        </tr>
                        <?php
                        foreach ($schedule['Outcomes'] as $outcome) {
                            $CostDetail = $outcome['CostDetail'];
                            if ($outcome['OtherCostDetail'] != NULL) {
                                $CostDetail = $outcome['OtherCostDetail'];
                            }
                            $Total = $outcome['Total'];
                            $total_outcome+=$Total;
                            ?>
                            <tr>
                                <td class="text-body-small"><?= $CostDetail ?></td>
                                <td class="text-center text-body-small"><?= $outcome['Number'] ?></td>
                                <td class="text-right text-body">-<?= number_format($Total) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="hidden">
                                <td colspan="2" class="text-right text-body-small">รายรับ</td>
                                <td class="text-right text-body"><?= number_format($total_income) ?></td>
                            </tr>
                            <tr class="hidden">
                                <td colspan="2" class="text-right text-body-small">รายจ่าย</td>
                                <td class="text-right text-body"><?= number_format($total_outcome) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right text-body-small">คงเหลือ</td>
                                <td class="text-right text-body"><u><?= number_format($total_income - $total_outcome) ?></u></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>                 
            <?php } ?>
        </div>     
    </div>
    <div class="row" style="padding-top: 0%;">
        <hr>
        <div class="col-md-3 col-md-offset-6">
            <table class="" style="width: 100%;">
                <thead class="hidden-print">
                    <tr>
                        <th style="width: 60%"></th>  
                        <th style="width: 40%"></th> 
                    </tr>
                </thead>
                <tbody>
                    <tr class="<?= ($data_reports['Total'] == $data_reports['Net'] ) ? 'hidden' : '' ?>">
                        <td class="text-right text-body">รวม&nbsp;&nbsp;:&nbsp;</td>
                        <td class="text-right text-body"><strong><?= number_format($data_reports['Total']) ?></strong></td>
                    </tr>
                    <tr class="<?= ($data_reports['Vage'] == 0 ) ? 'hidden' : '' ?>">
                        <td class="text-right text-body">เบี้ยเลี้ยง&nbsp;&nbsp;:&nbsp; </td>
                        <td class="text-right text-body"><strong><?= number_format($data_reports['Vage']) ?></strong></td>

                    </tr>
                    <tr class="active">
                        <td class="text-right text-body" >ยอดคงเหลือ&nbsp;&nbsp;:&nbsp;</td>
                        <td class="text-right text-body-lg"><strong><?= number_format($data_reports['Net']) ?></strong></td>
                    </tr>                         
                </tbody>
                <tfoot>
                    <tr class="note">
                        <td class="text-center"><br>_______________________ <br>ผู้ส่ง</td>
                        <td class="text-center"><br>_______________________ <br>ผู้รับ</td>
                    </tr>                       
                </tfoot>
            </table>
        </div> 
    </div>
</div>
<div class="container hidden-print" style="padding-bottom: 5%;">
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-lg btn-info pull-right" onclick="print_report()"><i class="fa fa-print"></i>&nbsp;พิมพ์รายงานส่งเงิน</button>
        </div>
        <div class="col-md-12 text-center">
            <p class="lead"><strong><?= $data_reports['RouteName'] ?> </strong></p>
            <p class="lead">จุดจอด&nbsp;:&nbsp;<strong><?= $data_reports['SellerStationName'] ?> </strong></p>  
            <?= $this->m_datetime->getDateThaiString() ?>
            &nbsp;|&nbsp;
            <?= $this->m_datetime->getTimeNow() ?>
        </div>  
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="stats-box">                  
                <div class="col-md-4">
                    <div class="stats-box-title">ยอดรวม</div>
                    <div class="stats-box-all-info"><?= number_format($data_reports['Total']) ?></div>                            
                </div>
                <div class="col-md-4">
                    <div class="stats-box-title">เบี้ยเลี้ยง</div>
                    <div class="stats-box-all-info"><?= number_format($data_reports['Vage']) ?></div>                         
                </div>
                <div class="col-md-4">
                    <div class="stats-box-title">คงเหลือ</div>
                    <div class="stats-box-all-info"><?= number_format($data_reports['Net']) ?></div>                            
                </div>
            </div>       
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <legend>ข้อมูลรายงานการส่งเงิน</legend>   
        </div>
        <?php
        $num_detail = count($data_reports['details']);
        foreach ($data_reports['details'] as $detail) {
            $DestinationName = $detail['DestinationName'];
            $class_route_detail = "";            
            if ($num_detail == 1) {
                $class_route_detail = 'col-md-offset-3';
            }
            ?>
            <div class="col-md-6 <?= $class_route_detail ?>">
                <div class="widget">
                    <div class="widget-header"><i>ไป</i>&nbsp;<strong><?= $DestinationName ?></strong></div>
                    <div class="widget-nopad">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 20%">รอบเวลา</th>
                                    <th style="width: 50%">รายการ</th>
                                    <th style="width: 10%">จำนวน</th>
                                    <th style="width: 20%">เป็นเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($detail['schedules'] as $schedule) {
                                    $TimeDepart = $schedule['TimeDepart'];
                                    $VCode = $schedule['VCode'];
                                    $num_tickets = count($schedule['tickets']);
                                    $num_income = count($schedule['Incomes']);
                                    $num_outcome = count($schedule['Outcomes']);
                                    $row_span = $num_tickets + $num_income + $num_outcome + 3;
                                    if ($num_tickets > 0 || $num_income > 0) {
                                        $row_span+=2;
                                    }
                                    if ($num_outcome > 0) {
                                        $row_span+=1;
                                    }
                                    ?>
                                    <tr>
                                        <td class="text text-center" rowspan="<?= $row_span ?>"><?= $TimeDepart ?><br><?= "($VCode)" ?></td>
                                    </tr>
                                    <tr class="<?= ($num_tickets > 0 || $num_income > 0) ? "bg-success" : "hidden" ?>">
                                        <td colspan="3" class="text"><u><strong>รายรับ</strong></u></td>
                                    </tr>
                                <?php
                                $total_income = 0;
                                $total_outcome = 0;
                                foreach ($schedule['tickets'] as $ticket) {
                                    $NumberTicket = $ticket['NumberTicket'];
                                    $NumberPriceFull = $ticket['NumberPriceFull'];
                                    $NumberPriceDiscount = $ticket['NumberPriceDiscount'];
                                    $DestinationName = $ticket['DestinationName'];
                                    if ($NumberTicket != $NumberPriceFull) {
                                        $DestinationName .="  ($NumberPriceFull/$NumberPriceDiscount)";
                                    }
                                    $Total = $ticket['Total'];
                                    $total_income+=$Total;
                                    ?>
                                    <tr class="bg-success">
                                        <td class="text"><strong><?= $DestinationName ?></strong></td>
                                        <td class="text-center"><?= $NumberTicket ?></td>
                                        <td class="text-right text"><?= number_format($Total) ?></td>
                                    </tr>
                                <?php } ?>
                                <?php
                                foreach ($schedule['Incomes'] as $income) {
                                    $CostDetail = $income['CostDetail'];
                                    if ($income['OtherCostDetail'] != NULL) {
                                        $CostDetail = $income['OtherCostDetail'];
                                    }
                                    $Total = $income['Total'];
                                    $total_income+=$Total;
                                    ?>
                                    <tr class="bg-success">
                                        <td class="text"><?= $CostDetail ?></td>
                                        <td class="text-center"><?= $income['Number'] ?></td>
                                        <td class="text-right"><strong><?= number_format($Total) ?></strong></td>
                                    </tr>
                                <?php } ?>
                                <tr class="<?= ($num_outcome > 0) ? "bg-warning" : "hidden" ?>">
                                    <td colspan="3" class="text"><u><strong>รายจ่าย</strong></u></td>
                                </tr>
                                <?php
                                foreach ($schedule['Outcomes'] as $outcome) {
                                    $CostDetail = $outcome['CostDetail'];
                                    if ($outcome['OtherCostDetail'] != NULL) {
                                        $CostDetail = $outcome['OtherCostDetail'];
                                    }
                                    $Total = $outcome['Total'];
                                    $total_outcome+=$Total;
                                    ?>
                                    <tr class="bg-warning">
                                        <td class="text"><?= $CostDetail ?></td>
                                        <td class="text-center"><?= $outcome['Number'] ?></td>
                                        <td class="text-right"><strong><?= number_format($Total) ?></strong></td>
                                    </tr>
                                <?php } ?>
                                <tr class="">
                                    <td colspan="2" class="text-right text">รายรับ</td>
                                    <td class="text-right"><strong><?= number_format($total_income) ?></strong></td>
                                </tr>
                                <tr class="">
                                    <td colspan="2" class="text-right text">รายจ่าย</td>
                                    <td class="text-right"><strong><?= number_format($total_outcome) ?></strong></td>
                                </tr>
                                <tr class="active">
                                    <td colspan="2" class="text-right text">คงเหลือ</td>
                                    <td class="text-right text"><u><strong><?= number_format($total_income - $total_outcome) ?></strong></u></td>
                                </tr>
                            <?php } ?>
                            </tbody>                            
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="row">
        <hr>        
        <div class="col-sm-12 text-center">                             
            <?php
            $cancle = array(
                'type' => "button",
                'class' => "btn btn-lg btn-danger",
            );
            echo anchor(($previous_page == NULL) ? 'report/' : $previous_page, '<i class="fa fa-times" ></i>&nbsp;กลับ', $cancle) . '  ';
            ?>
        </div> 
    </div>
</div>




