<script>
    $(window).load(function () {
        $("ol.progtrckr").each(function () {
            $(this).attr("data-progtrckr-steps",
                    $(this).children("li").length);
        });
        print_recept();

    });
    function print_recept() {
        window.print();
    }
</script>
<style>
    .parcel-print{    
        background: white;        
        font-family: 'sans-serif';
    }       
    table .title{      
        font-size: 10pt; 
    }
    table .sub-title{         
        font-size: 6pt; 
    }
    table .title-content{     
        font-size: 6pt; 
    }
    table .content{     
        font-size: 8pt; 
    }
    table .content-small{     
        font-size: 7pt; 
    }
    table .table-header{     
        font-size: 12pt; 
    }
    table .note{     
        font-size: 5pt;  
    }
    .border_right{
        border-right:1px solid gray;
    }
    .border_left{
        border-left: 1px solid gray;
    }
    .border_top{
        border-top: 1px solid gray;
    }
    .border_bottom{
        border-bottom: 1px solid gray;
    }
    .padding_left{
        padding-left: 5px;    
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
<div class="container hidden-print" style="padding-bottom: 2%;">
    <div class="row-fluid animated fadeInUp">             
        <div class="col-lg-12">
            <ol class="progtrckr" data-progtrckr-steps="2">                                
                <li id="step1" class="progtrckr-done"><span class="lead">ข้อมูลพัสดุ</span></li><!--                 
                --><li id="step2" class="progtrckr-done"><span class="lead">พิมพ์ใบเสร็จ</span></li>
            </ol>
        </div>
    </div>
</div>
<?php
$sender = '';
$receiver = '';
$note = '';
if ($data['SenderName'] != NULL) {
    $sender .=$data['SenderName'];
}
if ($data['SenderPhone'] != NULL) {
    $sender .= '  ' . $data['SenderPhone'];
}
if ($data['ReceiverName'] != NULL) {
    $receiver .=$data['ReceiverName'];
}
if ($data['ReceiverPhone'] != NULL) {
    $receiver .= '  ' . $data['ReceiverPhone'];
}
if ($data['CostNote'] != NULL) {
    $note = $data['CostNote'];
}
?>
<div class="container hidden-print" style="padding-bottom: 3%;">
    <div class="col-md-6 col-md-offset-3">
        <div class="row">
            <div class="col-md-4 text-left"><?= $data['Date'] ?></div>
            <div class="col-md-4 text-center">
                <img src="<?= base_url() . "assets/img/ticket_logo.png" ?>" class="" width="90%" height="100px" alt="">                                 
            </div>
            <div class="col-md-4 text-right"><?= $data['ReceiptID'] ?></div>
        </div>       
        <div class="row">
            <div class="col-md-12 text-center lead">
                <?= "<strong>" . $data['VTName'] . "</strong> " . $data['RouteName'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 border_top">จาก/From&nbsp;:</div>
            <div class="col-md-6 border_top">ถึง/To&nbsp;:</div>
        </div>
        <div class="row">
            <div class="col-md-6 text-center lead"><?= $data['SourceName'] ?></div>
            <div class="col-md-6 text-center lead"><strong><?= $data['DestinationName'] ?></strong></div>
        </div>
        <div class="row">
            <div class="col-md-6 border_top">ผู้ส่ง/Sender&nbsp;:</div>
            <div class="col-md-6 border_top">ผู้รับ/Receiver&nbsp;:</div>
        </div>
        <div class="row">
            <div class="col-md-6 text-center"><h4><?= $sender ?></h4></div>
            <div class="col-md-6 text-center"><h4><?= $receiver ?></h4></div>
        </div>
        <div class="row">
            <div class="col-md-4 border_top border_left border_right">ออกเวลา/Depart&nbsp;:</div>
            <div class="col-md-4 border_top border_right">ถึงเวลา/Arrive&nbsp;:</div>
            <div class="col-md-4 border_top border_right">รถเบอร์/No.&nbsp;:</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center border_bottom border_left border_right lead"><strong><?= $data['TimeDepart'] ?></strong></div>
            <div class="col-md-4 text-center border_bottom border_right lead"><?= $data['TimeArrive'] ?></div>
            <div class="col-md-4 text-center border_bottom border_right lead"><strong><?= $data['VCode'] ?></strong></div>
        </div>
        <div class="row">
            <div class="col-md-4">จำนวน/Number</div>
            <div class="col-md-4 col-md-offset-2">ราคา/Price</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center"><label><?= $data['Number'] ?></label></div>          
            <div class="col-md-4 col-md-offset-2 text-center"><h4><?= $data['CostValue'] ?>&nbsp;-.</h4></div>
        </div> 
        <div class="row <?= ($note == '') ? 'hidden' : '' ?>">
            <div class="col-md-12 text-center" style="color: red;">
                <h5>
                    *&nbsp;<?= $note ?>&nbsp;*
                </h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center" style="padding-top: 1%;">
            <button class="btn btn-info btn-lg" onclick="print_recept()"><i class="fa fa-print"></i>&nbsp;พิมพ์ใบเสร็จ</button>
            <?php
            $cancle = array(
                'type' => "button",
                'class' => "btn btn-danger btn-lg",
            );
            echo anchor(($previous_page == NULL) ? 'sale/' : $previous_page, '<i class="fa fa-ticket fa-lg" ></i>&nbsp;ขายตั๋วโดยสาร', $cancle) . '  ';
            ?>  
        </div>
    </div>
</div>
<div class="parcel-print">
    <?php $i = 1; ?>
    <div class="page-break<?= ($i == 1) ? "-no" : "" ?>">
        <table border="0" style="width: 100%;">
            <thead class="">
                <tr class="">
                    <th style="width: 25%;"></th>
                    <th style="width: 25%;"></th>
                    <th style="width: 25%;"></th>
                    <th style="width: 25%;"></th>
                </tr>                
            </thead>
            <tbody>  
                <tr>
                    <td class="text-center note"><?= $data['Date'] ?></td>   
                    <td class="text-center" colspan="2">
                        <img src="<?= base_url() . "assets/img/ticket_logo.png" ?>" class="" width="80%" height="40px" alt="">                                 
                    </td>
                    <td class="text-center note"><?= $data['ReceiptID'] ?> </td>
                </tr>
                <tr class="">                     
                    <td class="text-center content-small" colspan="4"><?= "<strong>" . $data['VTName'] . "</strong> " . $data['RouteName'] ?></td>
                </tr>               
                <tr class="border_top">
                    <td class="text-left sub-title padding_left border_right" colspan="2">จาก/From&nbsp;:</td>
                    <td class="text-left sub-title padding_left" colspan="2">เวลาออก/Depart&nbsp;:</td>
                </tr>
                <tr>
                    <td class="text-center content-small border_right border_bottom" colspan="2"><?= $data['SourceName'] ?></td>                   
                    <td class="text-center content border_bottom" colspan="2"><strong><?= $data['TimeDepart'] ?></strong></td>
                </tr>
                <tr>
                    <td class="text-left sub-title padding_left border_right" colspan="2"><strong>ถึง/To&nbsp;:</strong></td>
                    <td class="text-left sub-title padding_left" colspan="2">เวลาถึง/Arrive&nbsp;:</td>
                </tr>
                <tr>
                    <td class="text-center content border_bottom border_right" colspan="2"><strong><?= $data['DestinationName'] ?></strong></td>
                    <td class="text-center sub-title border_bottom" colspan="2"><strong><?= $data['TimeArrive'] ?></strong></td>
                </tr>
                <tr>
                    <td class="text-left sub-title padding_left border_right" colspan="2">เบอร์รถ/No.&nbsp;:</td>
                    <td class="text-left sub-title padding_left" colspan="2">ราคา/Price&nbsp;:</td>
                </tr>  
                <tr>
                    <td class="text-center border_bottom border_right" colspan="2"><strong class="title"><?= $data['VCode'] ?></strong></td>
                    <td class="text-center content  border_bottom" colspan="2"><?= $data['CostValue'] ?>&nbsp;-.</td>
                </tr>
                <tr class="">
                    <td class="text-center border_bottom" rowspan="4"><span class="title"><?= $data['Number'] ?></span><br><small class="note">(กล่อง/ซอง)</small></td>                                     
                    <td class="text-left note padding_left border_left" colspan="3">ผู้รับ/Receiver&nbsp;:</td>
                </tr>
                <tr>
                    <td class="text-left content padding_left border_bottom border_left" colspan="3"><strong><?= $receiver ?></strong></td>
                </tr>
                <tr>
                    <td class="text-left note padding_left border_left" colspan="3">ผู้ส่ง/Sender&nbsp;:</td>
                </tr>
                <tr>
                    <td class="text-left content padding_left border_bottom border_left" colspan="3"><strong><?= $receiver ?></strong></td>
                </tr>               
                <tr class= " <?= ($note == '') ? 'hidden' : '' ?>">                    
                    <td class="text-center content-small" colspan="4" >*&nbsp;<?= $note ?>&nbsp;*</td>
                </tr>
                <tr>

                </tr>
            </tbody>
        </table>
    </div> 
    <?php
    $number = $data['Number'];
    for ($i = 2, $n = 1; $i <= $number + 1; $i++, $n++) {
        ?>
        <div class="page-break<?= ($i == 1) ? "-no" : "" ?>">
            <table border="0">
                <thead>
                    <tr>
                        <th style="width: 25%;"></th>
                        <th style="width: 10%;"></th>
                        <th style="width: 40%;"></th>
                        <th style="width: 25%;"></th>
                    </tr> 
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">                                
                            <img src="<?= base_url() . "assets/img/ticket_logo.png" ?>" class="" width="90%" height="20px" alt="">                                 
                        </td>
                        <td class="text-center content-small" colspan="3"><?= "<strong>" . $data['VTName'] . "</strong> <br>" . $data['RouteName'] ?></td>
                    </tr>
                    <tr class="border_top hidden-print">
                        <td class="text-right note">ผู้ส่ง/Sender&nbsp;:</td>
                        <td class="text-left sub-title padding_left"colspan="3"><?= $sender ?></td>
                    </tr>
                    <tr class="border_top">
                        <td class="text-right sub-title">ผู้รับ/Receiver&nbsp;:</td>
                        <td class="text-left title padding_left"colspan="3"><strong><?= $receiver ?></strong></td>
                    </tr>
                    <tr class="border_top">
                        <td class="text-center table-header border_right border_bottom" rowspan="2"><strong><?= $data['VCode'] ?></strong></td>
                        <td class="text-right note border_bottom">จาก/From&nbsp;:</td>
                        <td class="text-left content padding_left border_bottom" colspan="2"><strong><?= $data['SourceName'] ?></strong>&nbsp;<?= $data['TimeDepart'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-right content">ถึง/To&nbsp;:</td>
                        <td class="text-right content" colspan="3"><?= "$n/$number" ?></td>
                    </tr>
                    <tr>
                        <td class="text-center note border_right border_bottom">
                            <strong>
                                <?= $data['Date'] ?>
                            </strong>
                            <br>
                            <?= $data['ReceiptID'] ?>
                        </td>
                        <td class="text-center table-header border_bottom" colspan="3"><strong><?= $data['DestinationName'] ?></strong></td>
                    </tr>
                    <tr class="<?= ($note == '') ? '' : '' ?>">
                        <td class="text-center note" colspan="4"><?= $note ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
    ?>

</div>

