<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCost").addClass("active");
        var CostDetailID = $("#CostDetailID").val();
        if (CostDetailID === '999') {
            $('#other').show();
        } else {
            $('#other').hide();
        }
        $("select[name='CostDetailID']").on('change', function () {
            if (this.value === '999') {
                $('#other').show();
            } else {
                $('#other').hide();
            }
        });
    });

</script>
<br>
<br>
<div class="container" style="">
    <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2">
        <div class="widget ">
            <div class="widget-header">
                <?php echo $page_title; ?> 
            </div>
            <div class="widget-content">
                <?php if ($page_title_small != '' || $page_title_small != NULL) {
                    ?>
                    <p class="lead"><?= $page_title_small ?></p>
                <?php }
                ?>

                <?php echo $form['form']; ?>                
                <?php validation_errors(); ?>  
                <div class="form-group <?= (form_error('CostDate')) ? 'has-error' : '' ?>">
                    <label for="" class="col-sm-3 control-label">วันที่</label>
                    <div class="col-sm-4">
                        <?php echo $form['CostDate']; ?>
                        <?php echo $form['DateTH']; ?>
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_error('CostDate', '<font color="error">', '</font>'); ?>
                    </div>
                </div>                
                <div class="form-group <?= (form_error('RCode')) ? 'has-error' : '' ?>">
                    <label for="" class="col-sm-3 control-label">เส้นทาง</label>
                    <div class="col-sm-6">
                        <?php echo $form['RCode']; ?>
                        <?php echo $form['RouteName']; ?>
                    </div>
                    <div class="col-sm-3">
                        <?php echo form_error('RCode', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
                <div class="form-group <?= (form_error('TimeDepart')) ? 'has-error' : '' ?>">
                    <label for="" class="col-sm-3 control-label">รอบเวลา</label>
                    <div class="col-sm-4">
                        <?php echo $form['TimeDepart']; ?>
                        <?php echo $form['TSID']; ?>
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_error('CostDate', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
                <div class="form-group <?= (form_error('VCode')) ? 'has-error' : '' ?>">  
                    <label for="" class="col-sm-3  control-label">เบอร์รถ</label>
                    <div class="col-sm-3">
                        <?php echo $form['VCode']; ?>
                        <?php echo $form['VID']; ?>
                    </div> 
                    <div class="col-sm-4">
                        <?php echo form_error('VCode', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
                <div class="form-group <?= (form_error('CostDetailID')) ? 'has-error' : '' ?>">
                    <label for="" class="col-sm-3 control-label">รายการ</label>
                    <div class="col-sm-5">
                        <?php echo $form['CostDetailID']; ?>
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_error('CostDetailID', '<font color="error">', '</font>'); ?>
                    </div>

                </div>   
                <div id="other" class="form-group <?= (form_error('OtherDetail')) ? 'has-error' : '' ?>">
                    <label for="" class="col-sm-3 control-label"></label>
                    <div class="col-sm-6">
                        <?php echo $form['OtherDetail']; ?>
                    </div>
                    <div class="col-sm-3">
                        <?php echo form_error('OtherDetail', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
                <div class="form-group <?= (form_error('CostValue')) ? 'has-error' : '' ?>">
                    <label for="" class="col-sm-3  control-label">จำนวน</label>
                    <div class="col-sm-4">
                        <?php echo $form['CostValue']; ?>
                    </div>
                    <div class="col-sm-1">
                        <span class="text-left">บาท</span>                            
                    </div>
                    <div class="col-sm-4">
                        <?php echo form_error('CostValue', '<font color="error">', '</font>'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">หมายเหตุ</label>
                    <div class="col-sm-8">
                        <?php echo $form['CostNote']; ?>
                    </div>
                </div>              
                <div class="col-md-12 text-center">
                    <?php
                    $cancle = array(
                        'type' => "button",
                        'class' => "btn btn-lg btn-danger",
                    );
                    $save = array(
                        'type' => "submit",
                        'class' => "btn btn-lg btn-success",
                        'value' => '',
                        'content' => '<span class="fa fa-save">&nbsp;&nbsp;บันทึก</span>'
                    );
                    echo anchor('cost/', '<i class="fa fa-times" ></i>&nbsp;ยกเลิก', $cancle) . '  ';
                    echo form_button($save);
                    ?>                  
                </div> 
                <?php echo form_close() ?>  
            </div>
        </div>      
    </div>
</div>