<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCost").addClass("active");
    });

    function add() {
        var num_row = $('#cost_list tbody tr').length;
        var table_body = $('#cost_list tbody');
        var row = '';
        row += '<tr>';
        row += '<td class="text-center">' + num_row + '</td>';
        row += '<td></td>';
        row += '<td></td>';
        row += '<td></td>';
        row += '</tr>';

        table_body.appendTo(row);
        alert(num_row);
    }

</script>
<br>
<br>
<div class="container" style="">
    <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3">
        <div class="widget ">
            <div class="widget-header">
                <?php echo $page_title; ?> 
            </div>
            <div class="widget-content">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">เส้นทาง</label>
                        <div class="col-sm-7">
                            <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group <?= (form_error('CostDate')) ? 'has-error' : '' ?>">
                        <label for="" class="col-sm-3 control-label">วันที่</label>
                        <div class="col-sm-4">
                            <?php echo $form['CostDate']; ?>
                        </div>
                        <div class="col-sm-4">
                            <?php echo form_error('CostDate', '<font color="error">', '</font>'); ?>
                        </div>
                    </div>
                    <div class="form-group <?= (form_error('CostDetailID')) ? 'has-error' : '' ?>">
                        <label for="" class="col-sm-3 control-label">รายการ</label>
                        <div class="col-sm-6">
                            <?php echo $form['CostDetailID']; ?>
                        </div>
                        <div class="col-sm-4">
                            <?php echo form_error('CostDetailID', '<font color="error">', '</font>'); ?>
                        </div>
                    </div>

                    <div class="form-group <?= (form_error('VCode')) ? 'has-error' : '' ?>">  
                        <label for="" class="col-sm-3  control-label">เบอร์รถ</label>
                        <div class="col-sm-3">
                            <?php echo $form['VCode']; ?>
                        </div> 
                        <div class="col-sm-4">
                            <?php echo form_error('VCode', '<font color="error">', '</font>'); ?>
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
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button class="btn btn-danger "><i class="fa fa-times"></i>&nbsp;ยกเลิก</button>
                            <button class="btn btn-success"><i class="fa fa-save"></i>&nbsp;บันทึก</button>
                        </div>
                    </div>
                </form>   
            </div>
        </div>      
    </div>
</div>