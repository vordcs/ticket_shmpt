<script>
    jQuery(document).ready(function () {
        setInterval(function () {
            window.location.reload();
        }, 2*60000);
    });
    $(window).load(function () {
        $("ol.progtrckr").each(function () {
            $(this).attr("data-progtrckr-steps",
                    $(this).children("li").length);
        });
        print_ticket();

    });
    function print_ticket() {
        window.print();
    }
    function print_sucess() {
        var url = '<?= base_url() . "sale/booking/$rid/$source_id/$destination_id/$tsid" ?>';
        $('.print-ticket').each(function () {
            var ticket_id = $(this).attr('id');
            ticket_sale(ticket_id);
        });
        window.location = url;
    }

    function ticket_sale(ticket_id) {
        var data_ticket = {
            'TicketID': ticket_id
        };
        var result = $.ajax({
            url: '<?= base_url() . "sale/sale_seat" ?>',
            type: 'POST',
            ContentType: 'application/json',
            data: data_ticket,
            async: false
        }).responseText;
        if (parseInt(result) === 1) {
            return true;
        } else {
            return false;
        }

    }
</script>
<style>    
    .ticket-print{    
        background: white;        
        font-family:'THNiramitAS','Times New Roman',Times,serif;        
        page-break-inside: avoid;        
    }
    table .title{      
        font-size: 10pt;        
    }
    table .detail{ 
        padding-bottom: 5px;
        text-align: center;
        font-size: 12pt;        
    }

    table .route-name{  
        border-bottom: solid; 
        padding-top: 5px;
        padding-bottom: 10px;
        text-align: center;
        font-size: 12pt;
    }

    table tbody .vcode{ 
        padding-bottom: 5px;
        border-right: 1px solid gray;
        border-bottom: 1px solid gray;
        text-align: center;
        font-size: 14pt;        
        margin: auto;
    }
    table tbody .date{
        font-size: 12pt;  
    }
    table tbody .source{
        padding-bottom: 5px;
        border-bottom: 1px solid gray;
        text-align: center;
        font-size: 16pt;        
        margin: auto;
    }
    table tbody .destination{
        padding-bottom: 5px;
        border-bottom: 1px solid gray;
        text-align: center;
        font-size: 18pt;        
        margin: auto;
    }
    table tbody .seat{
        border-right: 1px solid gray;
        border-bottom: 1px solid gray;
        text-align: center;
        font-size: 16pt;
        margin: auto;
    }
    table tbody .time-depart{ 
        border-bottom: solid;
        text-align: center;
        font-size: 12pt;
    }
    table tbody .seat-price{
        font-size: 14pt;
        margin: auto;
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
    /*-------- style for ticket print---------------*/
    .print-ticket{    
        background: white;        
        font-family:'THNiramitAS','Times New Roman',Times,serif;        
        page-break-inside: avoid;
    }

    table .name-route{
        text-align: center;
        padding-top: 2px;        
        font-size: 6pt;
    }
    table .name-route-small{
        padding-top: 2px;
        font-size: 4pt;
    }
    table .date-info{
        text-align: center;
        padding-top: 2px;
        font-size: 5pt;

    }
    table .source-name{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 2px;
        font-size: 10pt;
        font-weight: lighter;
    }
    table .source-name-small{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 2px;
        font-size: 6pt;
        font-weight: lighter;

    }
    table .destination-name{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 2px;
        font-size: 12pt;
        font-weight: lighter;
    }
    table .destination-name-small{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 2px;
        font-size: 6pt;
        font-weight: lighter;
    }
    table .price-info{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 2px;
        font-size: 15pt;
        font-weight: bold;
    }
    table .price-small-info{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 2px;
        font-size: 6pt;
        font-weight: bold;
    }
    table .title-info{
        padding-top: 3px;
        padding-left: 5px;
        text-align: left;
        font-size: 6pt;
        font-weight: lighter;
    }
    table .content-info{
        text-align:center;
        font-size: 12pt;
        font-weight: lighter;

    }
    table .content-small-info{
        text-align: center;
        font-size: 8pt;
    }
    .verhicle-type-name{
        text-align: center;
        font-size: 12pt;        
    } 
    table .note{
        text-align: center;
        padding-top: 2px;
        padding-bottom: 2px;
        font-size: 5pt;  
    }
    table .note-footer{
        text-align: center;
        padding-top: 3px;
        padding-bottom: 1px;
        font-size: 4pt;  
    }

    table .border_top{
        border-top: 1px solid gray;
    }
    table .border_right{
        border-right: 1px solid gray;
    }
    table tfoot .cut-foot{
        width: 100%;        
        padding-bottom: 30px;
        border-top: 1px dotted;
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
<div class="container">
    <div class="row">
        <?php
        $n = 1;
        foreach ($data as $ticket) {
            ?> 
            <div class="page-break<?= ($n == 1) ? "-no" : "" ?>">
                <div class="col-md-3 print-ticket" id="<?= $ticket['TicketID'] ?>">
                    <table width="500px" border="0" align="center" cellpadding="0" cellspacing="0"> 
                        <thead>
                            <tr class="hidden-print">
                                <th style="width: 50%;"></th> 
                                <th style="width: 50%;"></th> 
                            </tr> 
                            <tr>
                                <th colspan="2">
                                    <img src="<?= base_url() . "/assets/img/ticket_logo.png" ?>" class="" width="70%" height="60px" alt="">                                 
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="name-route"><?= $ticket['RouteName'] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="date">
                                <td colspan="2" class="date-info"><?= $ticket['Date'] ?></td>                        
                            </tr>
                            <tr class="border_top">
                                <td style="width: 50%" class="title-info border_right"><strong>เบอร์รถ</strong>/No. : </td>
                                <td style="width: 50%" class="title-info"><strong>เวลาออก</strong>/Depart : </td>
                            </tr>
                            <tr>
                                <td style="width: 50%" class="content-info border_right"><strong><?= $ticket['VCode'] ?></strong></td>
                                <td style="width: 50%" class="content-info"><strong><?= $ticket['TimeDepart'] ?></strong></td>
                            </tr>
                            <tr class="border_top">
                                <td style="width: 50%" class="title-info border_right">เลขที่นั่ง/Seat No. : </td>
                                <td style="width: 50%" class="title-info">เวลา/Arrive : </td>
                            </tr>
                            <tr>
                                <td style="width: 50%" class="content-small-info border_right"><strong><?= $ticket['Seat'] ?></strong></td>
                                <td style="width: 50%" class="content-small-info"><strong><?= $ticket['TimeArrive'] ?></strong></td>
                            </tr>
                            <tr class="border_top">
                                <td colspan="2" class="title-info">ต้นทาง/Form : </td>                            
                            </tr>
                            <tr>
                                <td colspan="2" class="source-name"><strong><?= $ticket['SourceName'] ?></strong></td>                                       
                            </tr>
                            <tr class="border_top">
                                <td colspan="2" class="title-info"><strong>ปลายทาง</strong>/To : </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="destination-name"><strong><?= $ticket['DestinationName'] ?></strong></td>                        
                            </tr>
                            <tr class="border_top">
                                <td rowspan="2" class="verhicle-type-name"><strong><?= $ticket['VTName'] ?></strong></td>
                                <td class="title-info"><strong>ราคา</strong>/Price : </td>
                            </tr>
                            <tr>                        
                                <td class="text-center"><span class="price-info"><strong><?= $ticket['Price'] ?></strong></span>&nbsp;<small class="note-footer">บาท/B</small></td>
                            </tr> 
                            <tr class="text-center note-footer <?= ($ticket['Note'] == NULL) ? 'hidden' : '' ?>">
                                <td colspan="2" style="padding-bottom: 5px;" ><strong><?= $ticket['Note'] ?></strong></td>
                            </tr>
                            <tr>                        
                                <td colspan="2">
                                    <img src="<?= $ticket['BarCode'] ?>" width="100%" height="25px" alt="BarCode"> 
                                </td>
                            </tr>
                            <tr>
                                <td class="note">
                                    **&nbsp;ขอสงวนสิทธิ์&nbsp;**
                                    <br>
                                    ไม่รับเปลี่ยน
                                    <br>
                                    หรือ
                                    <br>
                                    คืนตั๋วโดยสาร
                                </td>
                                <td class="text-center">
                                    <img src="<?= $ticket['QrCode'] ?>" class="" width="50px" height="45px" alt="QRCode">
                                </td>
                            </tr>
                            <tr>
                                <td class="note-footer"><?= $ticket['DateSale'] ?></td>
                                <td class="note-footer"><?= $ticket['SellerName'] ?></td>
                            </tr>   
                            <tr class="">
                                <td colspan="2" class="cut-foot" style="border-bottom: 1px dotted;padding-top: 10px;"></td>                        
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-info">
                                <td colspan="2" class="note-footer" style="padding-top: 15px; padding-bottom: 10px;">
                                    <i class="fa fa-arrow-down fa-lg"></i> สำหรับพนักงานขับรถ <i class="fa fa-arrow-down fa-lg"></i>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="name-route"><strong><?= $ticket['RouteName'] ?></strong></td>
                            </tr>
                            <tr>
                                <td class="source-name-small"><?= $ticket['SourceName'] ?></td>
                                <td class="destination-name-small"><strong><?= $ticket['DestinationName'] ?></strong></td>
                            </tr>
                            <tr>                        
                                <td colspan="2">
                                    <img src="<?= $ticket['BarCode'] ?>" width="100%" height="35px" alt="BarCode"> 
                                </td>
                            </tr>
                            <tr>
                                <td class="note">
                                    <strong><?= $ticket['TimeDepart'] ?></strong>
                                    <br>
                                    <?= $ticket['VTName'] ?>&nbsp;:&nbsp;<strong><?= $ticket['VCode'] ?></strong>
                                    <br>
                                    ราคา&nbsp;:&nbsp;<strong class="price-small-info"><?= $ticket['Price'] ?></strong>&nbsp;<small>บ.</small>                            
                                </td>
                                <td class="text-center">
                                    <img src="<?= $ticket['QrCode'] ?>" class="" width="50px" height="50px" alt="QRCode">
                                </td>
                            </tr>
                            <tr>
                                <td class="note-footer"><?= $ticket['DateSale'] ?></td>
                                <td class="note-footer"><?= $ticket['SellerName'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div> 

            <?php
            $n++;
        }
        ?>
    </div>    
</div>
<div class="container hidden-print" style="padding-bottom: 15%;">  
    <div class="row" style="padding-bottom: 2%;">
        <div class="col-md-12">
            <ol class="progtrckr" data-progtrckr-steps="4">
                <li id="step1" class="progtrckr-done"><span class="lead" >เลือกเส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-done"><span class="lead">เลือกเที่ยวเวลาเดินทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-done"><span class="lead">เลือกที่นั่งการเดินทาง </span></li><!--                 
                --><li id="step4" class="progtrckr-done"><span class="lead">พิมพ์บัตรโดยสาร</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <?php
        foreach ($data as $ticket) {
            ?>
            <div class="col-md-3 well ">
                <table width="500px" border="0" align="center" cellpadding="0" cellspacing="0"> 
                    <thead>
                        <tr class="hidden-print">
                            <th style="width: 50%"></th>
                            <th style="width: 50%"></th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <img src="<?= base_url() . "/assets/img/ticket_logo.png" ?>" class="" width="70%" height="80px" alt="">                                 
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2" class="route-name" style="border-bottom: 1px solid gray;padding-bottom: 5px;">
                                <?= $ticket['RouteName'] ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr class="title">
                            <td class="text-left" style="width: 50% ; border-right: 1px solid gray; padding-left:5px;">เบอร์รถ : </td>
                            <td class="text-left" style="width: 50% ; padding-left: 5px" >เวลาออก : </td>                            
                        </tr>
                        <tr>
                            <td class="text-center vcode" style="width: 50% ;"><strong><?= $ticket['VCode'] ?></strong></td>
                            <td class="text-center time-depart" style="width: 50%;border-bottom: 1px solid gray;"><strong><?= $ticket['TimeDepart'] ?></strong></td>
                        </tr>
                        <tr class="title">
                            <td class="text-left" style="width: 50% ;border-right: 1px solid gray; padding-left:5px;">เลขที่นั่ง :</td>
                            <td class="text-left" style="width: 50% ;padding-left:5px;">เวลาถึง :</td>
                        </tr>
                        <tr>
                            <td class="text-center seat" style="width: 50% ;border-bottom: 1px solid gray ;"><strong><?= $ticket['Seat'] ?></strong></td>
                            <td class="text-center detail" style="width: 50%;border-bottom: 1px solid gray;"><strong><?= $ticket['TimeArrive'] ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center date" style="border-bottom: 1px solid gray;"><strong><?= $ticket['Date'] ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-left title" style="padding-left: 5px;">ต้นทาง :</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center source"><strong><?= $ticket['SourceName'] ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-left title" style="padding-left: 5px;">ปลายทาง :</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center destination"><strong><?= $ticket['DestinationName'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-center" rowspan="2"><strong><?= $ticket['VTName'] ?></strong></td>
                            <td class="text-left title" style="padding-left: 5px">ราคา :</td>
                        </tr>
                        <tr>                           
                            <td class="text-center"><strong class="seat-price"><?= $ticket['Price'] ?></strong>&nbsp;&nbsp;<small class="note">บาท</small> </td>
                        </tr>
                        <tr>
                            <td class="text-center" colspan="2" id="barcode">
                                <img src="<?= $ticket['BarCode'] ?>" class="" width="100%" height="40px" alt=""> 
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center note">
                                **ขอสงวนสิทธิ์**
                                <br>
                                ไม่รับเปลี่ยน
                                <br>
                                หรือ
                                <br>
                                คืนตั๋วโดยสาร
                            </td>
                            <td class="text-center" id="qrcode">
                                <img src="<?= $ticket['QrCode'] ?>" class="" width="70px" height="70px" alt="QRCode"> 
                            </td>
                        </tr>                                               
                        <tr class="text-center title">
                            <td colspan="2" style="padding-bottom: 5px;" ><strong><?= $ticket['Note'] ?></strong></td>
                        </tr>    
                        <tr class="note">
                            <td class="text-center" style="padding-bottom: 10px;"><?= $ticket['DateSale'] ?></td>
                            <td class="text-center" style="padding-bottom: 10px;"><?= $ticket['SellerName'] ?></td>
                        </tr>                        
                    </tbody> 
                </table>  
            </div>
            <?php
        }
        ?>
    </div>  
</div>

<footer class="hidden-print"> 
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <?php
                $cancle = array(
                    'type' => "button",
                    'class' => "btn btn-warning btn-lg pull-left",
                );
                echo anchor(($previous_page == NULL) ? 'sale/' : $previous_page, '<i class="fa fa-plus" ></i>&nbsp;เพิ่มที่นั่ง', $cancle) . '  ';
                ?>  
                <button type="button" class="btn btn-lg btn-info"  onclick="print_ticket()"><span class="fa fa-print fa-lg"></span>&nbsp;พิมพ์ตั๋วโดยสาร</button> 
                <button type="button" class="btn btn-success btn-lg pull-right"  onclick="print_sucess()"><span class="fa fa-check"></span>&nbsp;พิมพ์สำเร็จ</button> 
            </div>
        </div>
    </div>
</footer>
