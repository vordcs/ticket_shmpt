<script>
    $(document).ready(function () {

    });
</script>
<br>
<div class="container">     
    <div class="jumbotron" style="padding-bottom: 2%;">
        <div class="container">
            <div class="col-md-12">
                <h1>ยินดีต้อนรับ พนักงานขายตั๋ว สถานีขอนแก่น</h1>                
                <p></p> 
            </div>
            <div class="col-md-12">            
            </div>
        </div>
    </div>
    <div class="row">      
        <div class="col-md-12">
            <div class="widget widget-nopad">
                <div class="widget-header">                      
                    <span class="fa fa-user">&nbsp;ข้อมูลผู้ใช้งาน</span>
                </div>
                <?= form_open('home') ?>
                <div class="widget-content">
                    <div class="col-md-12" style="padding-top: 1%;padding-bottom: 2%;">
                        <div class="col-md-3 text-center">
                            <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
                            <br>

                        </div>
                        <div class="col-md-9">
                            <span class="lead" ><?= $detail['Title'] . $detail['FirstName'] . ' ' . $detail['LastName'] ?></span>
                            <strong></strong>                                   
                            <table class="table table-condensed table-responsive">
                                <tbody>
                                    <tr>
                                        <td>รหัสพนักงาน :</td>
                                        <td><?= $detail['EID'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>รหัสประชาชน :</td>
                                        <td><?= $detail['PersonalID'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>ตำแหน่งงาน :</td>
                                        <td><?= $detail['PositionName'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>เบอร์โทรศัพท์ :</td>
                                        <td><?= $detail['MobilePhone'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary pull-right" type="button" data-toggle="collapse" data-target="#emp_info" aria-expanded="false" aria-controls="collapseExample">
                                เปลี่ยนรหัสผ่าน
                            </button>
                        </div>
                    </div>

                    <div class="col-md-8 col-md-offset-2 collapse" id="emp_info">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">เปลี่ยนรหัสผ่าน</h3>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">รหัสผ่านเก่า</label>
                                        <div class="col-sm-7">
                                            <input type="password" name="old_pass" class="form-control" />
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">รหัสผ่านใหม่</label>
                                        <div class="col-sm-7">
                                            <input type="password" name="new_pass" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-center">
                                <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> เปลี่ยนรหัสผ่าน</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php echo js('docs.min.js?v=' . $version); ?>  

