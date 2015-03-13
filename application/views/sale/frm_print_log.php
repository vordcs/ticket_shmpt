<script>
    function print_log() {
        window.print();
    }

</script>
<style>    
    .log-print{    
        background: white;        
        font-family: 'THChakraPetch','Times New Roman', sans-serif;
    }       
    table .title{      
        font-size: 5pt;        
    }
    table .sub-title{         
        font-size: 4pt;   
        height: 10px;
    }
    table .content{     
        font-size: 4pt;  
        height: 12px;
    }
    table .table-header{     
        font-size: 2pt;  
        height: 8px;        
    }
    table .note{     
        font-size: 2pt;     
        height: 8px; 
    }
    /* css ส่วนสำหรับการแบ่งหน้าข้อมูลสำหรับการพิมพ์ */
    @media all
    {
        .page-break	{ display:none; }
        .page-break-no{ display:none; }  
    }
    @media print
    {
        .page-break	{ display:block;height:1px; page-break-before:always; }
        .page-break-no{ display:block;height:1px; page-break-after:avoid; }   
    }
</style>
<div class="container" style="padding-bottom: 5%;">
    <div class="row hidden-print">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;">&nbsp;&nbsp;<?php echo $page_title_small; ?></span>                
                </font>
                <button type="button" class="btn btn-info btn-lg pull-right" onclick="print_log()"><i class="fa fa-print fa-lg"></i>&nbsp;พิมพ์ใบล๊อก</button>

            </h3>        
        </div>

    </div>
    <div class="row hidden-print">       
        <div class="col-md-12">
            <div class="col-md-3 text-center">
                <img src="<?= base_url() . "/assets/img/ticket_logo.png" ?>" class="" width="70%" height="200px" alt="">   
            </div>  
            <div class="col-md-9">    
                <div class="col-md-12 text-center lead clearfix" >                    
                    บริษัท สหมิตรภาพ(2512) จำกัด 
                </div>
                <div class="col-md-12 text-center text clearfix" style="">                                          
                    <?= $data['VTName'] ?> &nbsp; สาย &nbsp; <?= $data['RouteName'] ?>                      
                </div>
                <div class="col-md-12 text-center text">                                          
                    จุดจอด&nbsp;:&nbsp;   <?= $data['SallerStationName'] ?>  
                </div>
                <div class="col-md-12 form-horizontal" style="padding-top: 3%;">
                    <div class="col-md-5" >                
                        <div class="form-group">
                            <label for="" class="col-sm-5 control-label">เวลาออก</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" disabled="" value="<?= $data['TimeDepart'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-5 control-label">รถเบอร์</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" disabled="" value="<?= $data['VCode'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">พนักงานขับรถ</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" disabled="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">พนักงานบริการ</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" disabled="">
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>        
    </div>
    <div class="row hidden-print">
        <div class="col-md-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 30%;">เลขที่นั่ง</th>
                        <th style="width: 60%;">ปลายทาง</th>
                        <th style="width: 10%;">จำนวน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sum_seat = 0;
                    foreach ($data['reports'] as $report) {
                        $seat = '';
                        $n = 0;
                        foreach ($report['SeatNo'] as $seat_no) {
                            if ($n != 0) {
                                $seat .= " , ";
                            }
                            $seat .= "$seat_no";
                            $n++;
                        }
                        $sum_seat+=$n;
                        ?>
                        <tr>
                            <td class="text-center"><strong><?= $seat ?></strong></td>
                            <td class=""><?= $report['DestinationName'] ?></td>
                            <td class="text-right"><?= $report['NumberTicket'] ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text text-center">รวม</td>
                        <td class="text text-right"><?= $sum_seat ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-md-12 text-center">
            <?php
            $cancle = array(
                'type' => "button",
                'class' => "btn btn-danger btn-lg",
            );
            echo anchor(($previous_page == NULL) ? 'sale/' : $previous_page, '<i class="fa fa-times fa-lg" ></i>กลับ', $cancle) . '  ';
            ?>  
        </div>
    </div>
    <div class="row log-print">
        <div class="col-md-8 col-md-offset-2 page-break-no">
            <table class="" cellspacing="100px">
                <thead>
                    <tr class="hidden-print">
                        <th style="width: 50%"></th>
                        <th style="width: 50%"></th>
                    </tr>                   
                    <tr class="title">                        
                        <th colspan="2">
                            บริษัท สหมิตรภาพ(2512) จำกัด
                        </th>
                    </tr>
                    <tr class="sub-title">
                        <th colspan="2">
                            <strong><u><?= $data['VTName'] ?></u></strong>                            
                        </th>
                    </tr> 
                    <tr class="sub-title">
                        <th colspan="2">                            
                            <?= $data['RouteName'] ?>
                        </th>
                    </tr>                   
                </thead>
                <tbody>
                    <tr class="sub-title">
                        <th colspan="2" class="text-center">
                            จุดจอด&nbsp;:&nbsp;<strong><?= $data['SallerStationName'] ?> </strong>               
                        </th>
                    </tr>
                    <tr class="table-header">
                        <td class="text-center" style="padding-left: 2%;">เวลาออก</td>                        
                        <td class="text-center" style="padding-left: 2%;">รถเบอร์</td> 
                    </tr>
                    <tr class="title">
                        <td class="text-center"><strong><?= $data['TimeDepart'] ?></strong></td>                        
                        <td class="text-center"><strong><?= $data['VCode'] ?></strong></td> 
                    </tr> 
                    <tr class="content">
                        <td colspan="2" class="text-center">
                            <?= $data['Date'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table-bordered">
                <thead>
                    <tr class="table-header">
                        <th style="width: 50%">เลขที่นั่ง</th>
                        <th style="width: 50%">ปลายทาง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data['reports'] as $report) {
                        $seat = '';
                        $n = 0;
                        foreach ($report['SeatNo'] as $seat_no) {
                            if ($n != 0) {
                                $seat .= " , ";
                            }
                            $seat .= "$seat_no";
                            $n++;
                        }
                        ?>
                        <tr class="content">
                            <td class="text-center"><strong><?= $seat ?></strong></td>
                            <td class="text-left" style="padding-left: 2%;"><?= $report['DestinationName'] ?></td>                            
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class=" text-center note" style="padding-top: 10px;">
                            ลงชื่อ___________________________พนักงานขาย
                            <br>
                            <?= $this->m_user->get_user_full_name() ?>
                            <br>
                            <?= $this->m_datetime->getDateTimeNow() ?>
                        </td>
                    </tr>
                </tfoot>
            </table>           
        </div>
    </div>
</div>

