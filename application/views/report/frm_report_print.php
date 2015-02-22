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
    .report-print{    
        background: white;
        font-family:Arial, "times New Roman", tahoma;        
        /*width: 100%;*/
        page-break-inside: avoid;
    } 
    .header-report{
        font-size: 6pt;
    }
    .title{      
        font-size: 6pt;        
    }
    .sub-title{      
        font-size: 4pt;        
    }
    .text-content{
        font-size: 5pt;
    }
    .note{
        font-size: 4pt;
    }
    @media all
    {
        .report-print	{ display:none; }         
    }
    @media print
    {
        .report-print	{ display:block;height:1px; page-break-before:always; }

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
<div class="container hidden-print">
    <div class="row well" style="padding-bottom: 5%;">
        <?php
        $seller_station_name = '';
        $route_name = '';
        foreach ($reports as $route) {
            $RCode = $route['RCode'];
            $VTID = $route['VTID'];
            $num_along_road = count($route['cost_along_road']);
            $seller_station_id = $route['seller_station_id'];
            $seller_station_name = $route['seller_station_name'];
            $route_name = $route['RouteName'];

            $total = 0;

            $form_id = "form_report_$RCode$VTID$seller_station_id";
            echo form_open("report/send/$RCode/$VTID/$seller_station_id", array('class' => '', 'id' => "$form_id", 'name' => "$form_id"));
            ?>
            <div class="col-md-12 text-center">
                <p class="lead">จุดจอด&nbsp;:&nbsp;<?= $seller_station_name ?></p>
                <input type="hidden" name="RCode" value="<?= $RCode ?>">
                <input type="hidden" name="VTID" value="<?= $VTID ?>">
                <input type="hidden" name="SID" value="<?= $seller_station_id ?>">
            </div>
            <?php
            foreach ($route['routes_detail'] as $rd) {
                $total_rd = 0;
                ?>
                <div class="col-md-6  <?= ($num_along_road > 0 ) ? 'col-md-offset-1' : '' ?>">
                    <p class=" text-center"><u><?= $rd['RouteName'] ?></u></p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%">รอบเวลา</th>
                                <th style="width: 60%">รายการ</th>
                                <th style="width: 20%">จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rd['schedules'] as $schedule) {
                                $TSID = $schedule['TSID'];
                                $VCode = $schedule['VCode'];
                                $num_ticket = count($schedule['tickets']);
                                $num_income = count($schedule['Income']);
                                $num_outcome = count($schedule['Outcome']);
                                $total_data_row = $num_ticket + $num_income + $num_outcome;
                                $row_span = '';
                                if ($total_data_row > 0) {
                                    $row_span = $total_data_row + 2;
                                }
                                if ($num_outcome <= 0) {
                                    $row_span--;
                                }
                                $total +=$schedule['Total'] + $schedule['TotalAlongRoad'];
                                $total_rd+=$schedule['Total'];
                                ?>
                                <tr>
                                    <td class="text-center" rowspan="<?= $row_span ?>">
                                        <?= $schedule['TimeDepart'] ?>
                                        <br>
                                        <?= "($VCode)" ?>
                                        <input type="hidden" name="TSID[]" value="<?= $TSID ?>">                                        
                                    </td>
                                    <td class="text-left" colspan="2"><strong><?= ($num_ticket > 0 || $num_income > 0) ? 'รายรับ' : '' ?></strong></td>                                    

                                </tr>
                                <?php
                                if ($num_ticket > 0) {
                                    foreach ($schedule['tickets']as $ticket) {
                                        $total_ticket_price = $ticket['Total'];
                                        ?>
                                        <tr>
                                            <td class="text-left bg-success"><i>ไป</i>&nbsp;<strong><?= $ticket['DestinationName'] ?></strong>&nbsp;(&nbsp;<?= $ticket['NumberTicket'] ?> &nbsp;ที่นั่ง&nbsp;)</td>
                                            <td class="text-right bg-success"><?= number_format($total_ticket_price, 1) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>                                        
                                <?php
                                if ($num_income > 0) {
                                    foreach ($schedule['Income']as $income) {
                                        $total_income = $income['Total'];
                                        ?>
                                        <tr>
                                            <td class="text-left bg-success"><?= $income['CostDetail'] ?></td>
                                            <td class="text-right bg-success"><?= number_format($total_income, 1) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                                <?php
                                if ($num_outcome > 0) {
                                    echo '<tr> <td class="text-left" colspan="2"><strong>รายจ่าย</strong></td></tr>';
                                    foreach ($schedule['Outcome']as $outcome) {
                                        $total_outcome = $outcome['Total'];
                                        ?>
                                        <tr>
                                            <td class="text-left bg-warning"><?= $outcome['CostDetail'] ?></td>
                                            <td class="text-right bg-warning"><?= number_format($total_outcome, 2) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <td colspan="2" class="text-center"><strong>คงเหลือ</strong></td>
                                <td class="text-right"><strong><?= number_format($total_rd, 1) ?></strong></td>
                            </tr>

                        </tfoot>
                    </table>
                </div>
                <?php
            }
            $total_along_road = 0;
            if ($num_along_road > 0) {
                ?>
                <div class="col-md-4">
                    <p class="text-center"><u>รายทาง</u></p>                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 60%;">รอบเวลา</th>                            
                                <th style="width: 40%">จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($route['cost_along_road']['schedules'] as $schedule) {

                                $income_along_road = 0;
                                $TSID = '';
                                if (count($schedule['AlongRoad']) > 0) {
                                    $TSID = $schedule['TSID'];
                                    $income_along_road = $schedule['AlongRoad'][0]['Total'];
                                    $total_along_road+=$income_along_road;
                                }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <strong><?= $schedule['TimeDepart'] ?></strong>                                        
                                    </td>
                                    <td class="text-right"><?= number_format($income_along_road, 1) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <td class="text-center"><strong>รวม</strong></td>
                                <td class="text-right"><strong><?= number_format($total_along_road, 1) ?></strong></td>
                            </tr>
                        </tfoot>

                    </table>

                </div>

                <?php
            }
            $total +=$total_along_road;
            ?>
            <hr>
            <br>
            <div class="col-md-6 col-md-offset-3">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">รวม</label>
                        <div class="col-sm-5">
                            <input type="text" readonly=""  class="form-control input-lg" id="Total" name="Total"  placeholder="ยอดรวม" value="<?= floor($total); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-3 control-label">ค่าตอบแทน</label>
                        <div class="col-sm-3">
                            <input type="text" readonly="" class="form-control input-lg" id="Vage" name="Vage" placeholder="เบี้ยเลี้ยง" value="<?= $route['Vage'] ?>">                                
                        </div>                        
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">คงเหลือ</label>
                        <div class="col-sm-8">
                            <input type="text" readonly=""  class="form-control input-lg" id="Net" name="Net" placeholder="ยอดคงเหลือ" value="<?= $route['Net'] ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">หมายเหตุ</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" readonly="" rows="3" id="ReportNote" name="ReportNote" placeholder="<?= $route['ReportNote'] ?>" value="<?= $route['ReportNote'] ?>"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-12 text-center">                             
                        <?php
                        $cancle = array(
                            'type' => "button",
                            'class' => "btn btn-lg btn-danger",
                        );
                        echo anchor(($previous_page == NULL) ? 'report/' : $previous_page, '<i class="fa fa-times" ></i>&nbsp;กลับ', $cancle) . '  ';
                        ?>
                        <button type="button" class="btn btn-lg btn-info" onclick="print_report()"><i class="fa fa-print"></i>&nbsp;พิมพ์รายงานส่งเงิน</button>
                    </div>                       
                </div>        
            </div>   


        <?php } ?>          
    </div>
</div>
<div class="container report-print">
    <?php
    foreach ($reports as $report) {
        $num_along_road = count($report['cost_along_road']);
        $seller_station_id = $report['seller_station_id'];
        $seller_station_name = $report['seller_station_name'];
        $route_name = $report['RouteName'];
        ?>
        <div class="row text-center ">
            <div class="col-md-12 header-report">
                <u>รายงาน</u>
            </div>
            <div class="col-md-12 header-report">
                <?= $route_name ?>  
            </div>
            <div class="col-md-12 header-report">
                <strong>
                    จุดจอด&nbsp;:&nbsp;<?= $seller_station_name ?>  
                </strong>
            </div> 
            <div class="col-md-12 note">
                <?= $this->m_datetime->getDateThaiStringShort() ?>
                &nbsp;:&nbsp;
                <?= $this->m_datetime->getTimeNow() ?>
            </div>

        </div>

        <div class="row">
            <?php
            foreach ($report['routes_detail'] as $rd) {
                $total_rd = 0;
                ?>
                <div class="col-md-12">
                    <span class="text sub-title"><u><?= $rd['RouteName'] ?></u></span>
                </div>
                <div class="col-md-12">                    
                    <table class="table-bordered">
                        <thead>   
                            <tr class="title">
                                <th colspan="3"></th>
                            </tr>
                            <tr class="sub-title">
                                <th style="width: 20%">รอบเวลา</th>
                                <th style="width: 55%">รายการ</th>
                                <th style="width: 25%">เป็นเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $TotalIncome = 0;
                            $TotalOutcome = 0;
                            foreach ($rd['schedules'] as $schedule) {
                                $TSID = $schedule['TSID'];
                                $VCode = $schedule['VCode'];
                                $num_ticket = count($schedule['tickets']);
                                $num_income = count($schedule['Income']);
                                $num_outcome = count($schedule['Outcome']);
                                $total_data_row = $num_ticket + $num_income + $num_outcome;
                                $row_span = '';
                                if ($total_data_row > 0) {
                                    $row_span = $total_data_row + 2;
                                }
                                if ($num_outcome <= 0) {
                                    $row_span--;
                                }
                                ?>
                                <tr>
                                    <td class="text-center sub-title text" rowspan="<?= $row_span ?>">
                                        <?= $schedule['TimeDepart'] ?>   
                                        <br>
                                        <?= "($VCode)" ?>
                                    </td>
                                    <td class="text-left sub-title" colspan="2"><strong><?= ($num_ticket > 0 || $num_income > 0) ? 'รายรับ' : '' ?></strong></td>                                    

                                </tr>
                                <?php
                                if ($num_ticket > 0) {
                                    foreach ($schedule['tickets']as $ticket) {
                                        $total_ticket_price = $ticket['Total'];
                                        $TotalIncome += $total_ticket_price;
                                        ?>
                                        <tr class="sub-title">
                                            <td class="text-left"><i>ไป</i>&nbsp;<strong><?= $ticket['DestinationName'] ?>&nbsp;</strong>(<?= $ticket['NumberTicket'] ?>)</td>
                                            <td class="text-right"><?= number_format($total_ticket_price) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>                                        
                                <?php
                                if ($num_income > 0) {
                                    foreach ($schedule['Income']as $income) {
                                        $total_income = $income['Total'];
                                        $TotalIncome += $total_income;
                                        ?>
                                        <tr class="sub-title">
                                            <td class="text-left"><?= $income['CostDetail'] ?></td>
                                            <td class="text-right"><?= number_format($total_income) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                                <?php
                                if ($num_outcome > 0) {
                                    echo '<tr> <td class="text-left sub-title" colspan="2"><strong>รายจ่าย</strong></td></tr>';
                                    foreach ($schedule['Outcome']as $outcome) {
                                        $total_outcome = $outcome['Total'];
                                        $TotalOutcome+=$total_outcome;
                                        ?>
                                        <tr class="sub-title">
                                            <td class="text-left"><?= $outcome['CostDetail'] ?></td>
                                            <td class="text-right">-<?= number_format($total_outcome) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="sub-title">
                                <td class="text-right" colspan="2">รายรับ&nbsp;&nbsp;:&nbsp;</td>
                                <td class="text-right"><strong><?= number_format($TotalIncome) ?></strong></td>
                            </tr>
                            <tr class="sub-title">
                                <td class="text-right" colspan="2">รายจ่าย&nbsp;&nbsp;:&nbsp;</td>
                                <td class="text-right"><strong><?= number_format($TotalOutcome) ?></strong></td>
                            </tr>                            
                            <tr class="">
                                <td class="text-right sub-title" colspan="2">คงเหลือ&nbsp;&nbsp;:&nbsp;</td>
                                <td class="text-right text-content"><strong><?= number_format($TotalIncome - $TotalOutcome) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php } ?>
        </div>
        <?php if ($num_along_road > 0 && count($report['cost_along_road']['schedules']) > 0) {
            ?>
            <div class="row" style="padding-top: 2%;">
                <div class="col-md-12">
                    <span class="sub-title">รายทาง</span>
                </div>
                <div class="col-md-12">
                    <table class="table-bordered">
                        <thead>
                            <tr class="sub-title">
                                <th style="width: 60%;">รอบเวลา</th>                            
                                <th style="width: 40%">จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_income_along_road = 0;
                            foreach ($report['cost_along_road']['schedules'] as $schedule) {
                                $income_along_road = 0;
                                $VCode = $schedule['VCode'];
                                if (count($schedule['AlongRoad']) > 0) {

                                    $income_along_road = $schedule['AlongRoad'][0]['Total'];
                                    $total_income_along_road+=$income_along_road;
                                }
                                ?>
                                <tr class="sub-title">
                                    <td class="text-center">
                                        <strong>
                                            <?= $schedule['TimeDepart'] ?>
                                            <br>
                                            (<?= $VCode ?>)
                                        </strong>                                        
                                    </td>
                                    <td class="text-right"><?= number_format($income_along_road) ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="sub-title">
                                <td class="text-center">รวม</td>
                                <td class="text-right"><strong><?= number_format($total_income_along_road) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="padding-top: 3%;">
            <hr>            
            <div class="col-md-12">
                <table class="" style="width: 100%;">
                    <thead class="hidden-print">
                        <tr>
                            <th style="width: 60%"></th>  
                            <th style="width: 40%"></th> 
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right  title">รวม&nbsp;&nbsp;:&nbsp;</td>
                            <td class="text-right title"><strong><?= number_format($report['Total']) ?></strong></td>

                        </tr>
                        <tr>
                            <td class="text-right  title">เบี้ยเลี้ยง&nbsp;&nbsp;:&nbsp; </td>
                            <td class="text-right title"><strong><?= number_format($report['Vage']) ?></strong></td>

                        </tr>
                        <tr class="active">
                            <td class="text-right title" >ยอดคงเหลือ&nbsp;&nbsp;:&nbsp;</td>
                            <td class="lead text-right"><strong><?= number_format($report['Net']) ?></strong></td>
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
        <div class="row" style="font-size: 3pt ;padding-top: 2px;"> 
            
            <div class="col-xs-6 text-center">
                <?= $this->m_user->get_user_full_name() ?>
            </div>
            <div class="col-xs-6 text-right">
                <?= $this->m_datetime->getDateTimeNow() ?>
            </div>  
            <div class="col-xs-12 <?= ($report['ReportNote'] == NULL) ? 'hidden-print' : '' ?>" style="padding-top: 2px;">                                          
                <blockquote class="small"><?= $report['ReportNote'] ?></blockquote>                        
            </div>
        </div>
    <?php } ?>

</div>


