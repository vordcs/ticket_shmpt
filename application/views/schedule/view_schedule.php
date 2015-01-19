<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnSchedule").addClass("active");

        $('.datepicker').datepicker({
            language: 'th-th',
            format: 'yyyy-m-d'
        });
    });
    function remove_schedule(schedule_id, start_point) {
        var elem = document.getElementById(schedule_id);
        elem.remove();
        var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "REMOVE_TSID_" + start_point + "[]").val(schedule_id);
        $('#form_main').append($(input));
    }

</script>

<style> 
    .connected, .sortable, .exclude, .handles {
        margin: auto;
        padding: 0;
        width: 100%;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .sortable.grid {
        overflow: hidden;
    }
    .connected li, .sortable li, .exclude li, .handles li {
        text-align: center;
        list-style: none;
        border: 1px solid #CCC;
        background: #F6F6F6;
        font-family: "Tahoma";
        /*color: #1C94C4;*/
        margin: 5px;
        padding: 5px;
        height: 35px;        
    }
    .handles span {
        cursor: move;
    }
    li.disabled {
        opacity: 0.7;
    }  
    .sortable.grid li {
        line-height: 80px;
        float: left;
        width: 100%;
        height: 100px;
        text-align: center;
    }
    li.highlight {
        background: #FEE25F;
    }
    #connected {
        width: 440px;
        overflow: hidden;
        margin: auto;
    }
    .connected {
        float: left;
        width: 200px;
    }
    .connected.no2 {
        float: right;
    }
    li.sortable-placeholder {
        border: 1px dashed #CCC;
        background: none;
    }
</style>
<div class="container">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;"><?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">ค้นหา</h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">วันที่</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control datepicker" id="" placeholder="">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Submit</button>
                            </div>                            
                        </form>  
                    </div>
                    <div class="col-md-6">
                        <div class="clock" id="clock">
                            <div id="Date"></div>
                            <ul id="time">
                                <li id="hours"> </li>
                                <li id="point">:</li>
                                <li id="min"> </li>
                                <li id="point">:</li>
                                <li id="sec"> </li>
                            </ul>
                        </div> 
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?= $form_open ?>
        <?php
        foreach ($route_detail as $rd) {
            $rid = $rd['RID'];
            $rcode = $rd['RCode'];
            $vtid = $rd['VTID'];
            $source = $rd['RSource'];
            $destination = $rd['RDestination'];
            $start_point = $rd['StartPoint'];
            ?>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <i class="fa">ไป</i>
                        <span><?php echo $destination; ?></span>
                    </div>
                    <div class="widget-content">                        
                        <div class="col-md-12">     
                            <?php
                            $add = array(
                                'type' => "button",
                                'class' => "btn btn-info pull-right",
                            );
                            echo anchor("schedule/add/$rcode/$vtid/$rid", "<i class=\"fa fa-plus\"></i>&nbsp;&nbsp; ไป $destination ", $add);
                            ?>

                        </div>

                        <div class="col-md6 col-xs-6">
                            <legend>เวลาออก</legend>
                            <ul id="list_schedules" class="exclude list-group">                                
                                <?php
                                $time_min_late = 10 * 60;
                                $time_now = date('H:i', time() + $time_min_late);
                                foreach ($schedules as $schedule) {
                                    $tsid = $schedule['TSID'];
                                    $seq_no = $schedule['SeqNo'];
                                    $time = $schedule['TimeDepart'];
                                    $time_depart = date('H:i', strtotime($time));
                                    if ($time_depart > $time_now) {
                                        $class_li = 'highlight';
                                        $class_btn = '';
                                        $flag_use = 'name="TSID_' . $start_point . '[]"' . ' value="' . $tsid . '"';
                                    } else {
                                        //do something
                                        $class_li = '';
                                        $class_btn = 'hidden';
                                        $flag_use = 'name="DIS_TSID_' . $start_point . '[]"' . ' value="' . $tsid . '"';
                                    }

                                    if ($rid == $schedule['RID']) {
                                        ?>   
                                        <li class="disabled <?= $class_li ?>" id="<?= $tsid ?>">
                                            <input type="hidden" <?= $flag_use ?> />
                                            <button class="btn btn-danger btn-xs pull-left <?= $class_btn ?>" onclick="remove_schedule('<?= $tsid ?>', '<?= $start_point ?>')">
                                                <span class="fa fa-times"></span>
                                            </button>
                                            <strong>
                                                <?= $time_depart ?>
                                            </strong>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul> 
                        </div>
                        <div class="col-md6 col-xs-6">
                            <legend>เบอร์รถ</legend>
                            <ul id="list_vehicles" class="exclude list-group">
                                <?php
                                $i = 0;
                                foreach ($schedules as $schedule) {
                                    if ($rid == $schedule['RID']) {
                                        $tsid = $schedule['TSID'];
                                        $vid = $schedule['VID'];
                                        $vcode = $schedule['VCode'];
                                        if ($vcode == '') {
                                            $vcode = '-';
                                        }
                                        $time = $schedule['TimeDepart'];
                                        $time_depart = date('H:i', strtotime($time));

                                        if ($time_depart > $time_now) {
                                            $class_li = '';
                                            $flag_use = 'name="VID_' . $start_point . '[]"' . ' value="' . $vid . '"';
                                        } else {
                                            //do something
                                            $class_li = 'disabled';
                                            $flag_use = 'name="DIS_VID_' . $start_point . '[]"' . ' value="' . $vid . '"';
                                        }
                                        ?>
                                        <li class="<?= $class_li ?>" id="">
                                            <input type="hidden" <?= $flag_use ?> />
                                            <?= $vcode ?>
                                            <?php
                                            echo anchor('schedule/change_vehicle/' . $tsid, '<span class="fa fa-pencil-square-o"></span>', array('class' => 'btn btn-default btn-xs pull-right'));
                                            ?>
                                        </li>
                                        <?php
                                    }
                                    $i++;
                                }
                                ?>
                            </ul> 
                        </div>

                    </div>
                </div> 
            </div>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-md-12 text-center panel panel-default" style="padding: 20px;">
            <?= anchor('schedule', '<span class="btn btn-lg btn-danger">ย้อนกลับ</span>') ?>
            <button class="btn btn-lg btn-success" type="submit">บันทึก</button>
        </div>
    </div>
    <?= $form_close ?>
</div>
<?php echo js('jquery.sortable.js?v=' . $version); ?>
<script>
    $(function () {
        $('.sortable').sortable();
        $('.handles').sortable({
            handle: 'span'
        });
        $('.connected').sortable({
            connectWith: '.connected'
        });
        $('.exclude').sortable({
            items: ':not(.disabled)'
        });
    });
</script>
