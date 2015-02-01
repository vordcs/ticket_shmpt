<script>
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
    .ticket-print{    
        background: white;
        /*font-family:Arial, "times New Roman", tahoma;*/
        font-family:'Times New Roman',Times,serif;
        /*width: 100%;*/
        page-break-inside: avoid;
    }       
    table .title{      
        font-size: 4pt;        
    }
    table .detail{ 
        padding-bottom: 5px;
        text-align: center;
        font-size: 6pt;        
    }

    table .route-name{  
        border-bottom: 1px solid; 
        padding-top: 5px;
        padding-bottom: 10px;
        text-align: center;
        font-size: 7pt;
    }

    table tbody .vcode{ 
        padding-bottom: 5px;
        border-right: 1px solid;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 12pt;        
        margin: auto;
    }
    table tbody .date{
        font-size: 8pt;  
    }
    table tbody .source{
        padding-bottom: 5px;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 7pt;        
        margin: auto;
    }
    table tbody .destination{
        padding-bottom: 5px;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 12pt;        
        margin: auto;
    }
    table tbody .seat{
        border-right: 1px solid;
        border-bottom: 1px solid;
        text-align: center;
        font-size: 10pt;
        margin: auto;
    }
    table tbody .time-depart{ 
        border-bottom: 1px solid;
        text-align: center;
        font-size: 12pt;
    }
    table tbody .seat-price{
        font-size: 14pt;
        margin: auto;
    }

    .verhicle-type-name{
        text-align: center;
        font-size: 12pt;        
    }   
    .note{
        font-size: 4pt ! important;  
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
<div class="container" style="padding-bottom: 10%;">
    <div class="row"></div>
    <div class="row">
        <?php
        $i = 1;
        foreach ($data as $ticket) {
            ?> 
        <div class="col-md-3 ticket-print well " id="<?=$ticket['TicketID']?>">
                <div class="page-break<?= ($i == 1) ? "-no" : "" ?>">&nbsp;</div>  
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
                            <th colspan="2" class="route-name" style="border-bottom: 1px solid;padding-bottom: 5px;">
                                <?= $ticket['RouteName'] ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr class="title">
                            <td class="text-left" style="width: 50% ; border-right: 1px solid; padding-left:5px;">เบอร์รถ : </td>
                            <td class="text-left" style="width: 50% ; padding-left: 5px" >เวลาออก : </td>                            
                        </tr>
                        <tr>
                            <td class="text-center vcode" style="width: 50% ;"><strong><?= $ticket['VCode'] ?></strong></td>
                            <td class="text-center time-depart" style="width: 50%;border-bottom: 1px solid;"><strong><?= $ticket['TimeDepart'] ?></strong></td>
                        </tr>
                        <tr class="title">
                            <td class="text-left" style="width: 50% ;border-right: 1px solid; padding-left:5px;">เลขที่นั่ง :</td>
                            <td class="text-left" style="width: 50% ;padding-left:5px;">เวลาถึง :</td>
                        </tr>
                        <tr>
                            <td class="text-center seat" style="width: 50% ;border-bottom: 1px solid;"><strong><?= $ticket['Seat'] ?></strong></td>
                            <td class="text-center detail" style="width: 50%;border-bottom: 1px solid;"><strong><?= $ticket['TimeArrive'] ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center date" style="border-bottom: 1px solid;"><strong><?= $ticket['Date'] ?></strong></td>
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
                                <img src="<?= $ticket['BarCode'] ?>" class="" width="100%" height="20px" alt=""> 
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
                                <img src="<?= $ticket['QrCode'] ?>" class="" width="40px" height="40px" alt=""> 
                            </td>
                        </tr>                                               
                        <tr class="text-center title">
                            <td colspan="2" style="padding-bottom: 5px;" ><strong><?= $ticket['Note'] ?></strong></td>
                        </tr>    
                        <tr class="note">
                            <td class="text-center" style="padding-bottom: 10px;"><?= $ticket['DateSale'] ?></td>
                            <td class="text-center" style="padding-bottom: 10px;"><?= $ticket['SellerName'] ?></td>
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
                            <td class="text-center" colspan="2"><?= $ticket['RouteName'] ?></td>
                        </tr>
                        <tr class="title">
                            <td class="text-center"><?= $ticket['SourceName'] ?></td>
                            <td class="text-center"><?= $ticket['DestinationName'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="<?= $ticket['BarCode'] ?>" class="" width="100%" height="25px" alt=""> 
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center title">
                                <?= $ticket['TimeDepart'] ?>
                                <br>
                                <?= $ticket['VTName'] ?>&nbsp;:&nbsp;<?= $ticket['VCode'] ?>
                                <br>
                                <span class="note">ราคา</span>&nbsp;<strong><?= $ticket['Price'] ?></strong>&nbsp;<span class="note">บ.</span>
                            </td>
                            <td class="text-center">
                                <img src="<?= $ticket['QrCode'] ?>" class="" width="50px" height="50px" alt=""> 
                            </td>
                        </tr>
                        <tr class="note">
                            <td class="text-center"><?= $ticket['DateSale'] ?></td>
                            <td class="text-center"><?= $ticket['SellerName'] ?></td>
                        </tr> 
                    </tfoot>

                </table>  
            </div>
            <?php
            $i++;
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
