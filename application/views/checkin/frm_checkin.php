
<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCheckIn").addClass("active");
    });
</script>
<div class="container" style="">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;">&nbsp;&nbsp;<?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
</div>
<div class="container">
    <div class="row">      
        <div class="col-md-8 col-md-offset-2">     

            <?= $form['form'] ?>
            <div class="form-group hidden <?= (form_error('VTID')) ? 'has-error' : '' ?>">  
                <label for="" class="col-md-3 control-label">ประเภทรถ</label>
                <div class="col-md-4">
                    <?= $form['VTID'] ?>
                </div>
            </div>
            <div class="form-group <?= (form_error('RCode')) ? 'has-error' : '' ?>">                             
                <label for="" class="col-md-3 control-label">เส้นทาง</label>
                <div class="col-md-8">
                    <?= $form['RCode'] ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-md-3 control-label">เบอร์รถ</label>
                <div class="col-md-3">
                    <?= $form['VCode'] ?>
                </div>                            
            </div>
            <div class="form-group">
                <label for="" class="col-md-3 control-label">รอบเวลา</label>
                <div class="col-md-3">
                    <?= $form['VCode'] ?>
                </div>                            
            </div>
            <div class="form-group">                            
                <label for="" class="col-md-3 control-label">จุดจอด</label>
                <div class="col-md-5">
                    <?= $form['SID'] ?>
                </div>
            </div>

            <div class="col-sm-4 col-md-offset-4">
                <div class="clock" id="clock">                
                    <ul id="time">
                        <li id="hours"> </li>
                        <li id="point">:</li>
                        <li id="min"> </li>
                        <li id="point">:</li>
                        <li id="sec"> </li>
                    </ul>               
                </div>  
            </div>
            <div class="col-md-12 text-center"> 
                <hr>                     
                <?php
                $cancle = array(
                    'type' => "button",
                    'class' => "btn btn-danger btn-lg",
                );
                $save = array(
                    'type' => "submit",
                    'class' => "btn btn-success btn-lg",
                    'content' => '<spand class="fa fa-save" >&nbsp;บันทึก</spand>'
                );


                echo anchor('checkin/', '<i class="fa fa-times" ></i>&nbsp;ยกเลิก', $cancle) . ' ';
                echo form_button($save);
                ?>                     
            </div>
            </form>            

        </div>
    </div>
    <br>
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Panel title</h3>
                </div>
                <div class="panel-body">
                    Panel content
                </div>
            </div>
        </div>
    </div>
</div>
