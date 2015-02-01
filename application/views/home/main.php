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

    <?php if (isset($timeline) && count($timeline) > 0) { ?>
        <div class="row hidden">  
            <div class="col-md-12">
                <div class="timeline">
                    <dl>
                        <dt>รายงานที่ส่งวันนี้ <?php
                        $date = date_create($timeline[0]['ReportDate']);
                        echo date_format($date, 'm / d / Y');
                        ?></dt>
                        <?php
                        for ($i = 0; $i < count($timeline); $i++) {
                            if ($i % 2 == 0) {
                                echo '<dd class="pos-right clearfix">';
                            } else {
                                echo '<dd class="pos-left clearfix">';
                            }
                            $time = date_create($timeline[$i]['CreateDate']);
                            $time = date_format($time, 'H:i');

                            echo '<div class="circ"> </div>';
                            echo '<div class="time">' . $time . '</div>';
                            echo '<div class="events">';
                            echo '<div class="events-body">';
                            echo '<h4 class="events-heading">ส่งเงินครั้งที่ ' . ($i + 1) . ' ของวัน <span class="badge badge-success">สาย ' . $timeline[$i]['RCode'] . '</span></h4>';
                            echo '<p>เงินขายตั๋ว <strong>' . number_format($timeline[$i]['Total']) . '</strong> บาท,';
                            if (number_format($timeline[$i]['Vage']) != 0)
                                echo' ค่าตอบแทน <strong>' . number_format($timeline[$i]['Vage']) . '</strong> บาท,';
                            echo ' เงินสุทธิรวม <strong>' . number_format($timeline[$i]['Net']) . '</strong> บาท</p>';

                            echo '<p>ของรอบต่อไปนี้ ';
                            for ($j = 0; $j < count($timeline[$i]['detail']); $j++) {
                                echo substr($timeline[$i]['detail'][$j]['TimeDepart'], 0, -3);
                                if ($j < (count($timeline[$i]['detail']) - 1))
                                    echo ' | ';
                            }
                            echo '</p>';

                            echo '</div>';
                            echo '</div>';
                            echo '</dd>';
                        }
                        ?>
                        <!--                        <dd class="pos-right clearfix">
                                                        <div class="circ"> </div>
                                                        <div class="time">Apr 14</div>
                                                        <div class="events">
                                                            <div class="pull-left">
                                                                <img class="events-object img-rounded" src="img/photo-1.jpg">
                                                            </div>
                                                            <div class="events-body">
                                                                <h4 class="events-heading">Bootstrap</h4>
                                                                <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
                                                                <p>ของรอบต่อไปนี้ 16.00</p>
                                                            </div>
                                                        </div>
                                                    </dd>
                                                    <dd class="pos-left clearfix">
                                                        <div class="circ"></div>
                                                        <div class="time">Apr 10</div>
                                                        <div class="events">
                                                            <div class="pull-left">
                                                                <img class="events-object img-rounded" src="img/photo-2.jpg">
                                                            </div>
                                                            <div class="events-body">
                                                                <h4 class="events-heading">Bootflat</h4>
                                                                <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
                                                            </div>
                                                        </div>
                                                    </dd>-->

                </div>

            </div>
        </div>
    <?php } ?>
</div>


<?php echo js('docs.min.js?v=' . $version); ?>  

