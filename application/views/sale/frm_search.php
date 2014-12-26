<script>
    jQuery(document).ready(function () {
        $("select[name='VTID']").change(function () {
            var url_post = "<?= base_url(); ?>index.php/sale/get_route_by_vehicle_type";
            var vtid = $("select[name='VTID'] option:selected").val();
            var vt_name = $("select[name='VTID'] option:selected").text();
//            alert(vtid + '  ' + vt_name + '  ' + url_post );
            $.ajax({
                url: url_post,
                data: {'VTID': vtid},
                dataType: 'json',
                type: "post",
                success: function (data) {
                    $(".result").html(data);
//                    response = jQuery.parseJSON(data);
//                    console.log(response);
                }
            });
        });
        $("select[name='RCode']").change(function () {
            var rcode = $("select[name='RCode'] option:selected").val();
            var route_name = $("select[name='RCode'] option:selected").text();
//            alert(rcode + '  ' + route_name);
            $('#form_search_route').submit();
        });
        $("select[name='SourceID']").change(function () {
            var s_id = $("select[name='SourceID'] option:selected").val();
            var s_name = $("select[name='SourceID'] option:selected").text();
//            alert(s_id + '  ' + s_name);
            $('#form_search_route').submit();
        });
    });
</script>
<div id="" class="container-fluid">
    <div id="progress_step" class="row-fluid animated fadeInUp">        
        <div class="col-lg-12" style="padding-bottom: 2%;" >
            <ol class="progtrckr" data-progtrckr-steps="4">
  <!--                <li class="progtrckr-done"><span class="lead">สถานีต้นทาง</span></li>
                --><li id="step1" class="progtrckr-done"><span class="lead">เลือกเส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-todo"><span class="lead">เลือกเที่ยวเวลาเดินทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-todo"><span class="lead">เลือกที่นั่งการเดินทาง </span></li><!--                 
                --><li id="step4" class="progtrckr-todo"><span class="lead">พิมพ์บัตรโดยสาร</span></li>
            </ol>
        </div>
    </div>
</div>
<div id="" class="container"> 
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 well">
            <div class="col-lg-12 text-center">
                <h3 class="fs-title">เลือกเส้นทาง</h3>
                <p class="fs-subtitle"></p>                    
            </div> 
            <?php echo $from_search['form']; ?>  
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <label class="control-label">เส้นทาง</label>
                    <?php echo $from_search['RCode']; ?> 
                </div>
            </div>  
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <label class="control-label">จุดขึ้นรถ</label>
                    <?php echo $from_search['SourceID']; ?> 
                </div>
            </div>            
            <div class="col-lg-12">
                <p class="text">ปลายทาง</p>
            </div>
            <?php echo form_close(); ?>
            <hr>
            <?php
            if (count($route_detail) == 0 || $route_detail == '') {
                ?>
                <div class="col-md-12">
                    <div class="" style="padding-bottom: 100px;padding-top: 100px;">
                        <p class="lead text-center">กรุณาเลือกเส้นทาง</p>
                    </div>
                </div>  
                <?php
            } else {
                $seq_start_id = '';
                foreach ($stations as $s) {
                    if ($SourceID == $s['SID']) {
                        $seq_start_id = $s['Seq'];
                    }
                }
                foreach ($route_detail as $rd) {
                    $rcode = $rd['RCode'];
                    $vtid = $rd['VTID'];
                    $vt_name = $rd['VTDescription'];
                    $rid = $rd['RID'];
                    $source = $rd['RSource'];
                    $destination = $rd['RDestination'];
                    $schedule_type = $rd["ScheduleType"];
                    $start_point = $rd['StartPoint'];
                    $route_time = $rd['Time'];
                    $route_name = " ตารางเวลาเดิน $vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                    $last_seq_station = 0;
                    ?>                   
                    <div class="col-lg-6">
                        <div class="widget widget-nopad">
                            <div class="widget-header">                     
                                <span class=""><?= "ไป $destination" ?></span>
                            </div>   
                            <div class="list-group" style="border: 1px;">
                                <?php
//                                ออกจาก ขอนแก่น
                                if ($start_point == 'S') {
                                    foreach ($stations as $s) {
                                        if ($s['Seq'] > $seq_start_id) {
                                            $destination_id = $s['SID'];
                                            $station_name = $s['StationName'];
                                            $go_to_booking = array(
                                                'class' => "list-group-item",
                                            );
                                            echo anchor("sale/booking/$rid/$SourceID/$destination_id", $station_name, $go_to_booking);
                                        }
                                    }
                                } else {
                                    for ($i = count($stations) - 1; $i >= 0; $i--) {
                                        $destination_id = $stations[$i]['SID'];
                                        $station_name = $stations[$i]['StationName'];
                                        if ($stations[$i]['Seq'] < $seq_start_id) {
                                            $go_to_booking = array(
                                                'class' => "list-group-item",
                                            );
                                            echo anchor("sale/booking/$rid/$SourceID/$destination_id", $station_name . "", $go_to_booking);
                                        }
                                    }
                                }
                                ?>                               
                            </div>

                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        </div> 

    </div>


    <div id="select_route" class="row hidden">       
        <div class="col-md-12 well ">    
            <div class="col-md-12 text-center">
                <h3 class="fs-title">ค้นหาเที่ยวรถ</h3>
                <p class="fs-subtitle lead">This is step 1</p>                    
            </div>   
            <div class="col-md-8 col-md-offset-2">
                <?php echo $from_search['form']; ?>  
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">เส้นทาง</label>
                        <?php echo $from_search['RCode']; ?> 
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">จุดขึ้นรถ</label>
                        <?php echo $from_search['SourceID']; ?> 
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
            <hr>
            <?php
            if (count($route_detail) == 0 || $route_detail == '') {
                ?>
                <div class="col-md-12">
                    <div class="" style="padding-bottom: 100px;padding-top: 100px;">
                        <p class="lead text-center">กรุณาเลือกเส้นทาง</p>
                    </div>
                </div>  
                <?php
            } else {
                $seq_start_id = '';
                foreach ($stations as $s) {
                    if ($SourceID == $s['SID']) {
                        $seq_start_id = $s['Seq'];
                    }
                }
                foreach ($route_detail as $rd) {
                    $rcode = $rd['RCode'];
                    $vtid = $rd['VTID'];
                    $vt_name = $rd['VTDescription'];
                    $rid = $rd['RID'];
                    $source = $rd['RSource'];
                    $destination = $rd['RDestination'];
                    $schedule_type = $rd["ScheduleType"];
                    $start_point = $rd['StartPoint'];
                    $route_time = $rd['Time'];
                    $route_name = " ตารางเวลาเดิน $vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                    $last_seq_station = 0;
                    ?>                   
                    <div class="col-md-6">
                        <div class="widget widget-nopad">
                            <div class="widget-header">                     
                                <span class=""><?= " เที่ยวไป $destination" ?></span>
                            </div>   
                            <div class="list-group" style="border: 1px;">
                                <?php
//                                ออกจาก ขอนแก่น
                                if ($start_point == 'S') {
                                    foreach ($stations as $s) {
                                        if ($s['Seq'] > $seq_start_id) {
                                            $destination_id = $s['SID'];
                                            $station_name = $s['StationName'];
                                            $go_to_step2 = array(
                                                'class' => "list-group-item",
                                            );
                                            echo anchor("sale/booking/$rid/$SourceID/$destination_id", $station_name, $go_to_step2);
                                        }
                                    }
                                } else {
                                    for ($i = count($stations) - 1; $i >= 0; $i--) {
                                        $destination_id = $stations[$i]['SID'];
                                        $station_name = $stations[$i]['StationName'];
                                        if ($stations[$i]['Seq'] < $seq_start_id) {
                                            $go_to_step2 = array(
                                                'class' => "list-group-item",
                                            );
                                            echo anchor("sale/booking/$rid/$SourceID/$destination_id", $station_name . " -> $destination_id ", $go_to_step2);
                                        }
                                    }
                                }
                                ?>                               
                            </div>

                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>      
    </div>  
</div>


