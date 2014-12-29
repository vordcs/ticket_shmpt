<script>
    $(window).load(function () {
        $("ol.progtrckr").each(function () {
            $(this).attr("data-progtrckr-steps",
                    $(this).children("li").length);
        });
    });
</script>
<script>
    function print_ticket() {
        window.print();
        window.onafterprint = function () {
            var i = 0;
            $('.ticket-print').each(function () {
                var ticket_id = $(this).attr('id');
                console.log('printing ticket ' + ticket);
                i++;
                console.log(ticket_id + "  Printing completed...");
            });
//            alert(i);
        }
        return true;
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
        background-color: red;
        margin: auto;
    }  
    .ticket-print{
        /*padding-left: 5%;*/
        background: white;
        font-family:'Times New Roman',Times,serif;
        /*max-width: 200px;*/
        /*overflow-x: scroll;*/
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
        font-size: 8pt;
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

    @media all {
        .page-break	{ display: none; }
    }

    @media print {
        .page-break	{ 
            display: block; 
            page-break-before: always; 
        }
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
<!--html preview-->
<div id="" class="container hidden-print hidden" >
    <div class="row" style="padding-bottom: 50px;">        
        <?php
        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $route_name = "$rcode " . $route['RSource'] . '-' . $route['RDestination'];
        foreach ($ticket as $t) {
            $ticket_id = $t['TicketID'];
            $source_name = $t['SourceName'];
            $destination_name = $t['DestinationName'];
            $VCode = $t['VCode'];
            $seat = $t['Seat'];
            $time_depart = $t['TimeDepart'];
            $time_arrive = $t['TimeArrive'];
            $barcode = $this->m_barcode->gen_barcode($ticket_id);
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 well">
                <fieldset disabled >
                    <div class="ticket" id="">                    
                        <div class="ticket-title" style="text-align: center;">
                            <img data-src="holder.js/100%x180" alt="...">
                            <p class="lead">บริษัท สหมิตรภาพ จำกัด</p>                       
                        </div>
                        <div class="ticket-body">  
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-xs-12 text-center">
                                        <input type="text" class="form-control text-center" id="" placeholder= "รถตู้ xxx ต้นทาง - ปลายทาง">  
                                        <input type="hidden" class="form-control input-lg text-center" id="" name="ticket_id" placeholder="" value="<?php echo $t['TicketID'] ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="">เบอร์รถ</label>
                                        <input type="text" class="form-control input-lg text-center" id="" placeholder="xxx-x" value="<?php echo $t['VCode'] ?>">
                                        <label for="">เลขที่นั่ง</label>
                                        <input type="text" class="form-control input-lg text-center" id="" placeholder="ที่นั่ง" value="<?php echo $t['Seat'] ?>">
                                    </div>
                                    <div class="col-xs-6">
                                        <label for="">เวลาออก</label>
                                        <input type="text" class="form-control input-lg text-center" id="" placeholder="เวลาออก" value="<?php echo $t['TimeDepart'] ?>">                                    
                                        <label for="">เวลาถึง</label>
                                        <input type="text" class="form-control text-center" id="" placeholder="เวลาถึง" value="<?php echo $t['TimeArrive'] ?>">                              
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <label for="">ต้นทาง</label>
                                        <div class="col-xs-10 col-xs-offset-1">
                                            <input type="text" class="form-control text-center" id="" placeholder="ต้นทาง" value="<?php echo $t['SourceName'] ?>">
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <label for="">ปลายทาง</label>
                                        <div class="col-xs-10 col-xs-offset-1">
                                            <input type="text" class="form-control input-lg text-center" id="" placeholder="ปลายทาง" value="<?php echo $t['DestinationName'] ?>">
                                        </div>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="">วันที่เดินทาง</label>
                                        <input type="text" class="form-control text-center" id="" placeholder="วันที่เดินทาง" value="<?php echo $t['DateSale'] ?>">
                                    </div>
                                    <div class="col-xs-6">
                                        <label for="">ราคา</label>
                                        <input type="text" class="form-control input-lg text-center" id="" placeholder="ราคา" value="<?php echo $t['PriceSeat'] ?>">
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <div class="col-xs-12 text-center" style="padding-top: 5px;"> 
                                        <img src="<?php echo $barcode; ?>">

                                    </div>
                                </div>
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
                        <div class="ticket-footer">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table">
                                        <tbody>
                                            <tr class="">
                                                <td colspan="2" class="cut-foot">
                                                    <span></span>
                                                </td>
                                            </tr> 
                                            <tr class="title">
                                                <td class="text-center" colspan="2"><?= "$vt_name  " . $route_name ?></td>
                                            </tr>
                                            <tr class="title">
                                                <td class="text-center" colspan=""><?= $source_name ?></td>
                                                <td class="text-center" colspan=""><?= "$destination_name" ?></td>
                                            </tr>

                                            <tr class="title">
                                                <td colspan="2" class="text-center">
                                                    <img src="<?= $barcode ?>" class="" alt=""> 
                                                </td>
                                            </tr>                        
                                            <tr class="title">
                                                <td class=" text-left">
                                                    <?php echo $this->m_datetime->getDatetimeNow(); ?>
                                                </td>
                                                <td class="text-center">
                                                    คนขายตั๋ว
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>                    
                    </div>
                </fieldset>    
            </div>
        <?php } ?>              
    </div>
</div>

<!--html print--> 
<div class="vertical-container">
    <div class="vertically-centered">
        <?php
        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $route_name = "$vt_name $rcode " . $route['RSource'] . '-' . $route['RDestination'];

        foreach ($ticket as $t) {
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
            ?>
            <div class="">
                <div class="ticket-print" id="<?= $ticket_id ?>">
                    <table class="" border='0' style="width: 100%" >
                        <thead>
                            <tr>
                                <th colspan="2">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2">
                                    บริษัท สหมิตรภาพ(2512) จำกัด
                                </th>
                            </tr>
                        </thead>
                        <tbody>                         
                            <tr class="">
                                <td class="route-name" colspan="2"><strong><?= $route_name ?></strong></td>
                            </tr>
                            <tr class="title">
                                <td class="" style=" width: 50%;border-right: 1px solid; padding-left:5px;" colspan="">เบอร์รถ : </td>
                                <td class="" style=" width: 50%;padding-left: 5px" colspan="">เวลาออก : </td>
                            </tr>
                            <tr>                            
                                <td class="vcode" rowspan="1"><strong><?= $vcode ?></strong></td>                            
                                <td class="time-depart" rowspan=""><strong><?= $time_depart ?></strong></td>
                            </tr>
                            <tr class="title">
                                <td class="" style="border-right: 1px solid; padding-left:5px;" colspan="">เลขที่นั่ง : </td>
                                <td class="" style="padding-left:5px;" colspan="">เวลาถึง : </td>
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
                                <td class="" colspan="">ราคา : </td>
                            </tr>
                            <tr>
                                <td class="detail text-right" style="">                                
                                    <strong class="seat-price"><?= $price ?> </strong> <small>บาท</small>                            
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="<?= $barcode ?>" class="" alt=""> 
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">
                                    <img src="<?= $barcode ?>" class="img-responsive" alt=""> 
                                </td>
                            </tr>
                            <tr class="title" >
                                <td class=" text-left">
                                    <?php echo $this->m_datetime->getDatetimeNow(); ?>
                                </td>
                                <td class="text-center">
                                    คนขายตั๋ว
                                </td>
                            </tr>                            
                            <tr>
                                <td style="padding-bottom: 20px;"></td>
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
                                    <img src="<?= $barcode ?>" class="" alt=""> 
                                </td>
                            </tr>                        
                            <tr class="title">
                                <td class=" text-left">
                                    <?php echo $this->m_datetime->getDatetimeNow(); ?>
                                </td>
                                <td class="text-center">
                                    คนขายตั๋ว
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>  
            </div>
        <?php } ?>        
    </div>
</div>
<footer class="hidden-print hidden"> 
    <button type="button" class="btn btn-info btn-lg"  onclick="print_ticket()"><span class="fa fa-print fa-2x"></span>&nbsp;พิมพ์ตั๋วโดยสาร</button> 
    <!--<button class="btn"  onclick="PrintElem('<?$ticket_id ?>')"><span class="fa fa-print fa-2x"></span>&nbsp;พิมพ์ตั๋วโดยสาร </button>-->  
</footer>