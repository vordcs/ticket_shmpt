
<!--หน้าต่างแสดงค่าใช้ที่เดิดขึ้นของผู้ใช้งาน ที่จุดข้ายตั๋ว-->
<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCost").addClass("active");
    });
</script>
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
        <div class="col-lg-12 col-md-12">
            <div class="widget">
                <div class="widget-header">
                    <i class="fa fa-search"></i>
                    <span>ค้นหา</span>
                </div>
                <div class="widget-content">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">เส้นทาง</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">วันที่</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">จุดจำหน่ายตั๋ว</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-default">ค้นหา</button>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="widget">
                <div class="col-md-12 text-right">   
                    <?php
                    $income = array(
                        'type' => "button",
                        'class' => "btn btn-info btn-lg",
                    );
                    $outcome = array(
                        'type' => "button",
                        'class' => "btn btn-warning btn-lg",
                    );
                    echo anchor('cost/add/1', '<span class="fa fa-plus">&nbsp;&nbsp;รายรับ</span>', $income) . '  ';
                    echo anchor('cost/add/2', '<span class="fa fa-minus">&nbsp;&nbsp;รายจ่าย</span>', $outcome);
                    ?> 
                </div>  
            </div>
        </div>
    </div>    
    <div class="row">
        <div class="widget">
            <div class="widget-header">
                <i class="fa"></i>
                <span>สรุปค่าใช้จ่าย </span>
            </div>
            <div class="widget-content">
                <div id="big_stats" class="cf">
                    <div class="stat"> 
                        <i class="fa">รายได้</i>
                        <span class="value">851</span> 
                    </div>
                    <div class="stat"> 
                        <i class="fa">รายจ่าย</i>
                        <span class="value">851</span> 
                    </div>  
                    <div class="stat"> 
                        <i class="fa">คงเหลือ</i>
                        <span class="value">851</span> 
                    </div>
                </div>            
            </div> 
        </div>     

        <div class="row">
            <div class="widget">
                <div class="widget-header">
                    <i class=""></i>
                    <span>เส้นทาง</span>
                </div>
                <div class="widget-content">
                    <div class="panel-group panel-group-lists collapse in" id="accordion" role="tablist" aria-multiselectable="true">  
                        <?php
                        foreach ($routes as $r) {
                            $rcode = $r['RCode'];
                            $vtid = $r['VTID'];
                            $vt_name = $r['VTDescription'];
                            $route_name = "$vt_name เส้นทาง $rcode" . ' ' . $r['RSource'] . ' - ' . $r['RDestination'];
                            $id = $vtid . '_' . $rcode;
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading<?= $id ?>">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $id ?>" aria-expanded="true" aria-controls="collapse<?= $id ?>">
                                            <?= $route_name ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse<?= $id ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?= $id ?>">
                                    <div class="panel-body">
                                        <?php
                                        foreach ($route_details as $rd) {
                                            if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                                $rid = $rd['RID'];
                                                $source = $rd['RSource'];
                                                $destination = $rd['RDestination'];
                                                $route_name = "$vt_name เส้นทาง $rcode" . ' ' . $rd['RSource'] . ' - ' . $rd['RDestination'];
                                                ?>
                                                <div class="col-md-12">
                                                    <p class="lead"><?= $source ?>&nbsp;&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;&nbsp;<?= $destination ?></p>
                                                </div>
                                                <div class="col-md-12">
                                                    <table class="overflow-y" border="1">
                                                        <thead>
                                                            <tr>
                                                                <th>รอบเวลา</th>
                                                                <th>เบอร์รถ</th>
                                                                <th>รายได้</th>
                                                                <th>รายจ่าย</th>
                                                                <th>คงเหลือ</th> 
                                                                <th></th> 
                                                            </tr> 
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($schedules as $schedule) {
                                                                $TSID = $schedule['TSID'];
                                                                $VID = $schedule['VID'];
//                                                                if ($rid == $schedule['VID']) {
                                                                $VCode = '';
                                                                $sum_income = 0;
                                                                $sum_outcome = 0;

                                                                foreach ($costs as $c) {
                                                                    $temp = $c['CostValue'];
                                                                    if ($c['CostTypeID'] == 1 && $c['VID'] = $VID) {
//                                                                        รายได้ 
                                                                        $sum_income += $temp;
                                                                    } else {
//                                                                         รายจ่าย
                                                                        $sum_outcome += $temp;
                                                                    }
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <th><?= $TSID ?></th>
                                                                    <td class="text-center"><?= $VID ?> </td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                                <?php
//                                                                }
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
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>





