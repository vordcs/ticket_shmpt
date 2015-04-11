<script>
    var net = 0;
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnReport").addClass("active");
    });
    function calNet() {
        var total = document.getElementById('Total').value.replace(',', '.');
        var vage = document.getElementById('Vage').value.replace(',', '.');
        net = document.getElementById('Net');
        if (vage === '' || vage === 0) {
            document.getElementById('Vage').value = '0';
        } else if (vage !== 0) {
            net.value = total - vage;
        } else {
            net.value = total;
        }
        return true;
    }
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
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
        font-size: 8pt;
    }
    .title{      
        font-size: 6pt;        
    }
    .sub-title{
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
<div class="container hidden-print">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?> 
                <br>
                <font color="#777777">
                <span style="font-size: 18px; line-height: 23.399999618530273px;"><?php echo $page_title_small; ?></span>                
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
        foreach ($data['data'] as $route) {
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
                                        <?="($VCode)"?>
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
                                        <input type="hidden" name="TSID[]" value="<?= $TSID ?>">
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
                            <?=$data['form']['Total']?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-3 control-label">ค่าตอบแทน</label>
                        <div class="col-sm-3">
                            <?=$data['form']['Vage']?>
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-default" onclick="calNet()">ดูยอดคงเหลือ</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">คงเหลือ</label>
                        <div class="col-sm-8">
                            <?=$data['form']['Net']?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">หมายเหตุ</label>
                        <div class="col-sm-8">
                            <?=$data['form']['ReportNote']?>
                        </div>
                    </div>

                    <div class="col-sm-12 text-center">                             
                        <?php
                        $cancle = array(
                            'type' => "button",
                            'class' => "btn btn-lg btn-danger",
                        );

                        $save = array(
                            'class' => "btn btn-lg btn-success",
                            'title' => "$page_title",
                            'data-id' => "5",
                            'data-title' => "ส่งรายงาน จุดจอด : $seller_station_name",
                            'data-sub_title' => "$route_name",
                            'data-info' => "",
                            'data-toggle' => "modal",
                            'data-target' => "#confirm",
                            'data-href' => "",
                            'data-form_id' => "$form_id",
                        );
                        echo anchor(($previous_page == NULL) ? 'report/' : $previous_page, '<i class="fa fa-times" ></i>&nbsp;ยกเลิก', $cancle) . '  ';
                        echo anchor('#', '<span class="fa fa-save">&nbsp;&nbsp;ส่งรายงาน</span>', $save);
                        ?>  
                    </div>                       
                </div>        
            </div>   

            <?php
            echo form_close();
        }
        ?>          
    </div>
</div>
