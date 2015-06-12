<script>
    function change_destination() {
        var DestinationID = document.getElementById("DestinationID").value;
        var time_arrive = get_time_arrive(DestinationID);
        if (time_arrive !== false) {
            document.getElementById("TimeArrive").value = time_arrive;
        }
    }
    function get_time_arrive(DestinationID) {
        var seat_info = {
            'TSID': document.getElementById("TSID").value,
            'RID': document.getElementById("RID").value,
            'SourceID': document.getElementById("SourceID").value,
            'DestinationID': DestinationID,
        };
        var result = $.ajax({
            url: '<?= base_url() . "sale/get_time_arrive" ?>',
            type: 'POST',
            ContentType: 'application/json',
            data: seat_info,
            async: false
        }).responseText;
        if (parseInt(result) === 0) {
            return false;
        } else {
            var data = JSON.parse(result);
            return data;
        }
    }
</script>
<div id="" class="container">
    <div class="row-fluid animated fadeInUp">             
        <div class="col-lg-12">
            <ol class="progtrckr" data-progtrckr-steps="2">                                
                <li id="step1" class="progtrckr-done"><span class="lead">ข้อมูลพัสดุ</span></li><!--                 
                --><li id="step2" class="progtrckr-todo"><span class="lead">พิมพ์ใบเสร็จ</span></li>
            </ol>
        </div>
    </div>
</div>
<?= $form_parcel['form'] ?>
<div class="container">
    <div class="row text-center">
        <h3>
            เพิ่มพัสดุ&nbsp;:&nbsp;<?= $form_parcel['RouteName'] ?>
        </h3>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
              <?php
            $save = array(
                'class' => "btn btn-info btn-lg pull-right",
                'title' => "$page_title",
                'data-id' => "5",
                'data-title' => "$page_title : ".$form_parcel['Code'],
                'data-sub_title' => "รอบเวลา",
                'data-info' => $form_parcel['Time'],
                'data-content'=>$form_parcel['RouteName'],
                'data-toggle' => "modal",
                'data-target' => "#confirm",
                'data-href' => "",
                'data-form_id' => "form_parce",
            );
            echo anchor('#', '<i class="fa fa-print fa-fw"></i>&nbsp;บันทึกและพิมพ์ใบเสร็จ', $save);
            ?> 
            <!--<button type="submit" class="btn btn-info btn-lg pull-right"><i class="fa fa-print fa-fw"></i>&nbsp;บันทึกและพิมพ์ใบเสร็จ</button>-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">ต้นทาง</label>
                    <?= $form_parcel['SourceID'] ?>
                    <?= $form_parcel['SourceName'] ?>
                </div>
                <div class="form-group">
                    <label for="">รถเบอร์</label>
                    <?= $form_parcel['VID'] ?>
                    <?= $form_parcel['VCode'] ?>
                </div>
                <div class="form-group">
                    <label for="">เวลาออก</label>
                    <?= $form_parcel['RID'] ?>
                    <?= $form_parcel['TSID'] ?>
                    <?= $form_parcel['TimeDepart'] ?>
                </div> 
                <div class="form-group">
                    <label for="">เวลาถึง</label>                   
                    <?= $form_parcel['TimeArrive'] ?>
                </div> 
            </div>
            <div class="col-md-8">
                <div class="col-md-10 col-md-offset-1">
                    <div class="form-group">
                        <label for="">ปลายทาง</label>
                        <?= $form_parcel['DestinationID'] ?>
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-2">
                    <div class="form-group <?= (form_error('CostValue')) ? 'has-error' : '' ?>">
                        <label for="">ค่าบริการ</label>
                        <?= $form_parcel['CostValue'] ?> 
                        <?php echo form_error('CostValue', '<font color="error">', '</font>'); ?>
                    </div>
                </div>                
                <div class="col-md-4">
                    <div class="form-group <?= (form_error('Number')) ? 'has-error' : '' ?>">
                        <label for="">จำนวนชิ้น</label>
                        <?= $form_parcel['Number'] ?>
                        <?php echo form_error('Number', '<font color="error">', '</font>'); ?>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-group <?= (form_error('SenderName')) ? 'has-error' : '' ?>">
                        <label for="">ชื่อผู้ส่ง</label>
                        <?= $form_parcel['SenderName'] ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= (form_error('SenderPhone')) ? 'has-error' : '' ?>">
                        <label for="">เบอร์โทรผู้ส่ง</label>
                        <?= $form_parcel['SenderPhone'] ?>
                        <?php echo form_error('SenderPhone', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group <?= (form_error('ReceiverName')) ? 'has-error' : '' ?>">
                        <label for="">ชื่อผู้รับ</label>
                        <?= $form_parcel['ReceiverName'] ?>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="form-group <?= (form_error('ReceiverPhone')) ? 'has-error' : '' ?>">
                        <label for="">เบอร์โทรผู้รับ</label>
                        <?= $form_parcel['ReceiverPhone'] ?>
                        <?php echo form_error('ReceiverPhone', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-md-offset-2">
                <div class="form-group">
                    <label for="">หมายเหตุ</label>
                    <?= $form_parcel['CostNote'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
             <?php
            $cancle = array(
                'type' => "button",
                'class' => "btn btn-danger btn-lg",
            );
            echo anchor(($previous_page == NULL) ? 'sale/' : $previous_page, '<i class="fa fa-times fa-lg" ></i> ขายตั๋วโดยสาร', $cancle) . '  ';
            ?> 
        </div>
    </div>
</div>
<?=
$form_parcel['form_close']?>