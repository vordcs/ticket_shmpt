<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnSchedule").addClass("active");

        $('.datepicker').datepicker({
            language: 'th-th',
            format: 'yyyy-m-d'
        });
    });
</script>
<br>
<br>
<div class="container">
    <div class="row">
        <?php if (validation_errors() != NULL) { ?>
            <div class="row animated pulse">
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>ผิดพลาด ! <small> ข้อมูลเวลา </small></h4>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-8 col-md-offset-2">

        </div>
        <div class="row">
            <?= $form['open'] ?>
            <div class="col-md-8 col-md-offset-2">
                <div class="widget">    
                    <div class="widget-header">
                        <span> <?php echo "$page_title $page_title_small" ?> </span>
                    </div>
                    <div class="widget-content">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="RID" class="control-label">เส้นทาง</label>
                                <p>
                                    <?= $detail['ScheduleNote'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">    
                                <label for="" class="control-label">วันที่</label>
                                <p>
                                    <?= $detail['Date'] ?>
                                </p>                  
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label for="" class="control-label">เวลาออก</label>
                                <p>
                                    <?= $detail['TimeDepart'] ?>
                                </p>
                            </div>                   
                        </div>     
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label for="" class="control-label">เวลาถึง</label>
                                <p>
                                    <?= $detail['TimeArrive'] ?>
                                </p>
                            </div>                   
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label for="" class="control-label">รถคันเดิมที่รับผิดชอบคิวนี้</label>
                                <p>
                                    <?= $detail['VCode'] ?>
                                </p>
                            </div>                   
                        </div> 
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">เปลี่ยนรถที่วิ่งคิวนี้</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="" class="control-label">หมายเลขรถที่จะเปลี่ยน</label>
                                            <p>
                                                <?= $form['input']['VID'] ?>
                                            </p>
                                        </div>   
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="" class="control-label">เหตุผลที่ต้องเปลี่ยนมาแทน</label>
                                            <p>
                                                <?= $form['input']['ScheduleNote'] ?>
                                            </p>
                                        </div>   
                                    </div>   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center" style="margin-top: 5%;">                   
                            <?php
                            $cancle = array(
                                'class' => "btn btn-lg btn-danger",
                            );
                            $save = array(
                                'type' => "submit",
                                'class' => "btn btn-lg btn-success",
                                'value' => '',
                                'content' => '<span class="fa fa-save">&nbsp;&nbsp;บันทึก</span>'
                            );
                            echo anchor('schedule/view/' . $detail['RCode'] . '/' . $detail['VTID'], 'ย้อนกลับ', $cancle);
                            echo ' ';
                            echo form_button($save);
                            ?> 
                        </div> 
                    </div>
                </div>
            </div>
            <?= $form['close'] ?>
        </div>
    </div>
</div>

