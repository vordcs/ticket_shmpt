<script>
    $(window).load(function () {
        $("ol.progtrckr").each(function () {
            $(this).attr("data-progtrckr-steps",
                    $(this).children("li").length);
        });
    });
    function print_ticket() {
        window.print();
    }
    function print_sucess() {
        var url = '<?= base_url() . "sale/booking/$rid/$source_id/$destination_id/$tsid" ?>'
        $('.ticket-print').each(function () {
            var ticket_id = $(this).attr('id');
            ticket_sale(ticket_id);
        });
        window.location = url;
    }

    function ticket_sale(ticket_id) {
        var data_ticket = {
            'TicketID': ticket_id
        };
        $.ajax({
            url: '<?= base_url() . "sale/sale_seat" ?>',
            type: 'POST',
            ContentType: 'application/json',
            data: data_ticket
        }).done(function (response) {
            if (response !== '') {
                return true;
            } else {
                return false;
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            return false;
        });
    }
</script>
<style> 
    .vertical-container {
        display: -webkit-flex;
        display: flex;
        /*height: 300px;*/
    }
    .vertically-centered {
        display: table;        
        margin: auto;
    }  
    .ticket-print{    
        background: white;
        /*font-family:Arial, "times New Roman", tahoma;*/
        font-family:'Times New Roman',Times,serif;
        width: 100%;
        page-break-inside: avoid;
    }       
    table .title{      
        font-size: 6pt;        
    }
    table .detail{ 
        padding-bottom: 5px;
        text-align: center;
        font-size: 8pt;        
    }
    table thead{       
        text-align: center;
        font-size: 6pt;
    }
    table tbody .route-name{  
        border-bottom: 1px solid; 
        padding-top: 5px;
        padding-bottom: 10px;
        text-align: center;
        font-size: 8pt;
    }

    table tbody .vcode{ 
        padding-bottom: 5px;
        border-right: 1px solid;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 16pt;        
        margin: auto;
    }
    table tbody .source{
        padding-bottom: 5px;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 10pt;        
        margin: auto;
    }
    table tbody .destination{
        padding-bottom: 5px;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 16pt;        
        margin: auto;
    }
    table tbody .seat{
        border-right: 1px solid;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 14pt;
        margin: auto;
    }
    table tbody .time-depart{ 
        border-bottom: 1px solid;
        text-align: center;
        font-size: 16pt;
    }
    table tbody .seat-price{
        font-size: 20pt;
        margin: auto;
    }

    .verhicle-type-name{
        text-align: center;
        font-size: 16pt;        
    }   

    table tfoot .cut-foot{
        width: 100%;        
        padding-bottom: 30px;
        border-top: 1px dotted;
    }
    img { 
        display:block; 
        margin:0 auto; 
    }
    footer {        
        position: fixed;
        bottom: 0;
        left: 0;
        height: 80px;
        background-color: white;
        width: 100%;
        padding-top: 1%;
        vertical-align: middle;
        text-align: center;
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
<div id="" class="container-fluid hidden-print">
    <div class="row-fluid animated fadeInUp">        
        <div class="col-lg-12" style="padding-bottom: 2%;" >
            <ol class="progtrckr" data-progtrckr-steps="4">
  <!--                <li class="progtrckr-done"><span class="lead">สถานีต้นทาง</span></li>id="menu-toggle"
                --><li id="step1" class="progtrckr-done"><span class="lead" >เลือกเส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-done"><span class="lead">เลือกเที่ยวเวลาเดินทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-done"><span class="lead">เลือกที่นั่งการเดินทาง </span></li><!--                 
                --><li id="step4" class="progtrckr-done"><span class="lead">พิมพ์บัตรโดยสาร</span></li>
            </ol>
        </div>
    </div>
</div>

<!--html preview-->
<div id="" class="container-fluid hidden-print" >  
    <?= form_open("sale/print_ticket/$tsid", array('class' => 'form', 'id' => 'form_print_ticket')) ?>
    <div class="row" style="padding-bottom: 10%">
        <?php
        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $route_name = "$rcode " . $route['RSource'] . '-' . $route['RDestination'];
        foreach ($tickets as $t) {
            $ticket_id = $t['TicketID'];
            $source_name = $t['SourceName'];
            $destination_name = $t['DestinationName'];
            $vcode = $t['VCode'];
            $seat = $t['Seat'];
            $time_depart = date('H:i', strtotime($t['TimeDepart']));
            $time_arrive = date('H:i', strtotime($t['TimeArrive']));
            $date = $this->m_datetime->DateThai($t['DateSale']);
            $price = $t['PriceSeat'];
            $name_seller = $this->session->userdata('EID');
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 well" style="border:1px solid black;padding-left: 20px;padding-right: 20px;margin: 5px 5px auto;">
                <fieldset disabled >
                    <div class="ticket" id="">                    
                        <div class="ticket-title" style="text-align: center; margin-bottom: 15px;">
                            <?= img('ticket_logo_small.png') ?>                                          
                        </div>
                        <div class="ticket-body">  
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-10 col-md-offset-1 text-center">
                                        <input type="text" class="form-control text-center" id="" name="Route" placeholder= "รถตู้ xxx ต้นทาง - ปลายทาง" value="<?= $route_name ?>">  
                                        <input type="hidden" class="form-control text-center" id="TicketID" name="TicketID" placeholder="" value="<?php echo $ticket_id ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="">เบอร์รถ</label>                                   
                                        <input type="text" class="form-control text-center" id="" placeholder="xxx-x" value="<?php echo $vcode ?>">
                                        <label for="">เลขที่นั่ง</label>
                                        <input type="text" class="form-control text-center" id="" placeholder="ที่นั่ง" value="<?php echo $seat ?>">                        
                                    </div>
                                    <div class="col-xs-6">
                                        <label for="">เวลาออก</label>
                                        <input type="text" class="form-control input-lg  text-center" id="" placeholder="เวลาออก" value="<?php echo $time_depart ?>">                                    
                                        <label for="">เวลาถึง</label>
                                        <input type="text" class="form-control text-center" id="" placeholder="เวลาถึง" value="<?php echo $time_arrive ?>">                              
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="">ต้นทาง</label>
                                        <input type="text" class="form-control text-center" id="" placeholder="ต้นทาง" value="<?php echo $source_name ?>">
                                    </div>
                                    <div class="col-xs-6">
                                        <label for="">ปลายทาง</label>                                       
                                        <input type="text" class="form-control text-center" id="" placeholder="ปลายทาง" value="<?php echo $destination_name ?>">
                                    </div>
                                </div>                               
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="">วันที่เดินทาง</label>
                                        <input type="text" class="form-control text-center" id="" placeholder="วันที่เดินทาง" value="<?php echo $date ?>">
                                    </div>
                                    <div class="col-xs-6">
                                        <label for="">ราคา</label>
                                        <input type="text" class="form-control input-lg text-center" id="" placeholder="ราคา" value="<?php echo $price . '  .-' ?>">
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <div class="col-xs-6 text-left">
                                        <span class="small"><?php echo $this->m_datetime->getDatetimeNow(); ?></span>                               
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <span class="small">ผู้ชายตั๋ว</span>                                 
                                    </div>
                                </div>
                            </div>
                        </div>                                        
                    </div>
                </fieldset>   

            </div>
        <?php } ?>              
    </div>
    <?= form_close(); ?>
</div>

<!--html print--> 
<div class="vertical-container">
    <div class="vertically-centered">
        <?php
        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $route_name = "$vt_name $rcode " . $route['RSource'] . '-' . $route['RDestination'];

        foreach ($tickets as $t) {
            $ticket_id = $t['TicketID'];
            $source_name = $t['SourceName'];
            $destination_name = $t['DestinationName'];
            $vcode = $t['VCode'];
            $seat = $t['Seat'];
            $time_depart = date('H:i', strtotime($t['TimeDepart']));
            $time_arrive = date('H:i', strtotime($t['TimeArrive']));
            $date = $this->m_datetime->DateThai($t['DateSale']);
            $price = $t['PriceSeat'];
            $barcode = $this->m_barcode->gen_barcode($ticket_id);
            $qrcode = $this->m_qrcode->gen_qrcode($ticket_id);
            $name_seller = $this->session->userdata('EID');
            ?>
            <div class="page-break">
                <div class="ticket-print" id="<?= $ticket_id ?>">
                    <table class="" border='0' style="width: 98%;margin: 0 auto;" >
                        <thead>   
                        <th colspan="3">
                            <?= img('ticket_logo.png') ?>   
                        </th>
                        </thead>
                        <tbody>                         
                            <tr class="">
                                <td class="route-name" colspan="2"><strong><?= $route_name ?></strong></td>
                            </tr>
                            <tr class="title">
                                <td class="" style=" width: 50%;border-right: 1px solid; padding-left:5px;" colspan="">เบอร์รถ : </td>
                                <td class="" style=" width: 50%;padding-left: 5px" colspan="">เวลาออก/<small>Depart</small> : </td>
                            </tr>
                            <tr>                            
                                <td class="vcode" rowspan="1"><strong><?= $vcode ?></strong></td>                            
                                <td class="time-depart" rowspan=""><strong><?= $time_depart ?></strong></td>
                            </tr>
                            <tr class="title">
                                <td class="" style="border-right: 1px solid; padding-left:5px;" colspan="">เลขที่นั่ง/<small>Seat</small> : </td>
                                <td class="" style="padding-left:5px;" colspan="">เวลาถึง/<small>Arrive</small> : </td>
                            </tr>    
                            <tr class=""> 
                                <td class="detail seat " colspan=""><strong><?= $seat ?></strong></td>
                                <td class="detail" style="border-bottom: 1px solid;"><strong><?= $time_arrive ?></strong></td>
                            </tr>   
                            <tr>
                                <td class="detail" colspan="2" style="border-bottom: 1px solid;"><strong><?= $date ?></strong></td>   
                            </tr>
                            <tr class="title">
                                <td class="" colspan="2" style="padding-left:5px;">ต้นทาง  : </td>                            
                            </tr> 
                            <tr class="">
                                <td class="source" colspan="2"><strong class=""><?= $source_name ?></strong></td>                            
                            </tr> 
                            <tr class="title">
                                <td class="" colspan="2" style="padding-left:5px;">ปลายทาง : </td>
                            </tr>
                            <tr>
                                <td class="destination" colspan="2"><strong><?= $destination_name ?></strong></td>
                            </tr>
                            <tr>
                                <td  class="text-center verhicle-type-name" rowspan="3">                                
                                    <strong><?= $vt_name ?></strong>                                 
                                </td>
                            </tr>                       
                            <tr class="title">                            
                                <td class="" colspan="">ราคา&nbsp;<small>Price</small> : </td>
                            </tr>
                            <tr>
                                <td class="detail text-right" style="">                                
                                    <strong class="seat-price"><?= $price ?> </strong> <small>บาท</small>                            
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="<?= $barcode ?>" class="" width="100%" height="30px" alt=""> 
                                </td>
                            </tr>                             
                            <tr class="title" style="font-size: 4pt ! important;">   
                                <td class="text-center">
                                    **ขอสงวนสิทธิ์**
                                    <br>
                                    ไม่รับเปลี่ยน
                                    <br>
                                    หรือ
                                    <br>
                                    คืนตั๋วโดยสาร
                                </td>
                                <td class="text-center" >
                                    <img src="<?= $qrcode ?>" class="" width="50px" height="50px" alt=""> 
                                </td>
                            </tr>   

                            <tr style="font-size: 4pt ! important;">
                                <td class="text-center"><?= date('Y-m-d H:i:s') ?></td>
                                <td class="text-center"><?= $name_seller ?></td>
                            </tr>
                            <tr>
                                <td colspan="">&nbsp;</td>
                            </tr>                            
                            <tr class="title">
                                <td colspan="2" class="cut-foot">
                                    <span></span>
                                </td>
                            </tr> 

                        </tbody>
                        <tfoot>
                            <tr class="title">
                                <td colspan="2" class="cut-foot">
                                    <span></span>
                                </td>
                            </tr> 
                            <tr class="title">
                                <td class="text-center" colspan="2"><?= "" . $route_name ?></td>
                            </tr>    
                            <tr class="title">
                                <td class="text-center" colspan=""><?= $source_name ?></td>
                                <td class="text-center" colspan=""><?= "$destination_name" ?></td>
                            </tr>
                            <tr class="title">
                                <td colspan="2" class="text-center">
                                    <img src="<?= $barcode ?>" class="" width="100%" height="30px" alt=""> 
                                </td>
                            </tr>  
                            <tr class="title">   
                                <td class="text-center">
                                    <small>
                                        <?= $time_depart ?>
                                    </small>                                    
                                    <br>
                                    <?= $vcode ?>
                                    <br>
                                    <?= $vt_name ?>
                                </td>
                                <td class="text-center">
                                    <img src="<?= $qrcode ?>" class="" width="50px" height="50px" alt=""> 
                                </td>
                            </tr>
                            <tr style="font-size: 4pt ! important;">
                                <td class="text-center"><?= date('Y-m-d H:i:s') ?></td>
                                <td class="text-center"><?= $name_seller ?></td>
                            </tr>

                        </tfoot>
                    </table>
                </div>  
            </div>
        <?php } ?>        
    </div>
</div>
<footer class="hidden-print" style=""> 
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <?php
                $cancle = array(
                    'type' => "button",
                    'class' => "btn btn-warning pull-left",
                );
                echo anchor(($previous_page == NULL) ? 'sale/' : $previous_page, '<i class="fa fa-plus" ></i>&nbsp;เพิ่มที่นั่ง', $cancle) . '  ';
                ?>  
                <button type="button" class="btn btn-lg btn-info"  onclick="print_ticket()"><span class="fa fa-print fa-2x"></span>&nbsp;พิมพ์ตั๋วโดยสาร</button> 
                <button type="button" class="btn btn-success pull-right"  onclick="print_sucess()"><span class="fa fa-check"></span>&nbsp;พิมพ์สำเร็จ</button> 
            </div>
        </div>
    </div>

</footer>
