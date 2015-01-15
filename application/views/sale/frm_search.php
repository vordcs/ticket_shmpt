<script>
    jQuery(document).ready(function () {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnSale").addClass("active");
    });
</script>
<div id="" class="container-fluid" style="padding-top: 50px;">
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
            <div class="col-lg-12">
                <div class="panel-group" id="accordionRoute" role="tablist" aria-multiselectable="true">
                    <?php
                    foreach ($routes as $route) {
                        $rcode = $route['RCode'];
                        $vtid = $route['VTID'];
                        $vt_name = $route['VTDescription'];
                        $source = $route['RSource'];
                        $destination = $route['RDestination'];
                        $route_name = "$vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                        $id = $rcode . "_" . $vtid;

                        $SourceID = $route['SID'];
                        $source_name = $route['StationName'];
                        $station_source_seq = $route['Seq'];
                        if ($route['SellerNote'] != NULL) {
                            $note = $route['SellerNote'];
                            $source_name .= " ($note) ";
                        }
                        ?>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading<?= $id ?>">
                                <h4 class="panel-title" style="padding-left: 3%;">
                                    <a data-toggle="collapse" data-parent="#accordionRoute" href="#collapse<?= $id ?>" aria-expanded="true" aria-controls="collapse<?= $id ?>">
                                        <?= $route_name ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?= $id ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?= $id ?>">
                                <div class="panel-body">
                                    <p class="lead">จุดขึ้นรถ : <strong><?= $source_name ?></strong></p>
                                    <p class="text">ปลายทาง</p>
                                    <?php
                                    $seq_start_id = '';
                                    foreach ($stations as $s) {
                                        if ($SourceID == $s['SID'] && $rcode == $s['RCode'] && $vtid == $s['VTID']) {
                                            $seq_start_id = $s['Seq'];
                                        }
                                    }
                                    foreach ($routes_detail as $rd) {
                                        if ($rcode == $rd['RCode'] && $vtid == $rd['VTID']) {
                                            $rid = $rd['RID'];
                                            $source = $rd['RSource'];
                                            $destination = $rd['RDestination'];
                                            $schedule_type = $rd["ScheduleType"];
                                            $start_point = $rd['StartPoint'];
                                            $route_time = $rd['Time'];
                                            $route_name = " ตารางเวลาเดิน $vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                                            $last_seq_station = 0;

                                            //นับจำนวนสถานี
                                            $num_station = 0;
                                            $num_sale_station = 0;
                                            foreach ($stations as $s) {
                                                if ($rcode == $s['RCode'] && $vtid == $s['VTID']) {
                                                    $num_station ++;
                                                    if ($s['IsSaleTicket'] == '1') {
                                                        $num_sale_station ++;
                                                    }
                                                }
                                            }

                                            $stations_in_route = array();

                                            if ($start_point == "S") {
                                                $n = 0;
                                                foreach ($stations as $station) {
                                                    if ($rcode == $station['RCode'] && $vtid == $station['VTID'] && $station['Seq'] > $station_source_seq) {
                                                        $stations_in_route[$n] = $station;
                                                        $n++;
                                                    }
                                                }
                                            }
                                            if ($start_point == "D") {
                                                $n = 0;
                                                for ($i = $num_station; $i >= 0; $i--) {
                                                    foreach ($stations as $station) {
                                                        if ($rcode == $station['RCode'] && $vtid == $station['VTID'] && $station['Seq'] == $i && $station['Seq'] < $station_source_seq) {
                                                            $stations_in_route[$n] = $station;
                                                            $n++;
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="col-lg-6">
                                                <div class="widget widget-nopad">
                                                    <div class="widget-header">                     
                                                        <span class=""><?= "ไป $destination" ?></span>
                                                    </div>   
                                                    <div class="list-group" style="border: 1px;">
                                                        <?php
                                                        if (count($stations_in_route) > 0) {
                                                            foreach ($stations_in_route as $station) {
                                                                $destination_id = $station['SID'];
                                                                $station_name = $station['StationName'];
                                                            }
                                                            $go_to_booking = array(
                                                                'class' => "list-group-item",
                                                            );
                                                            echo anchor("sale/booking/$rid/$SourceID/$destination_id/", $station_name, $go_to_booking);
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

                        <?php
                    }
                    ?>                  
                </div>

            </div>       
        </div> 

    </div>

</div>


