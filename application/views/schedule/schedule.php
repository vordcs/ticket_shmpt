<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnSchedule").addClass("active");
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
    <?php
    $num_routes_seller = count($routes_seller);
    ?>
    <div class="row <?= ($num_routes_seller <= 0) ? 'hidden' : '' ?>">        
        <legend>จัดตารางเวลาเดินรถ</legend>
        <?php
        foreach ($routes_seller as $vehicle_type) {
            $VTID = $vehicle_type['VTID'];
            ?>
            <div class="col-md-6 <?= ($num_routes_seller == 1) ? 'col-md-offset-3' : '' ?>">
                <div class="panel panel-info">
                    <!-- Default panel contents -->
                    <div class="panel-heading"><?= $vehicle_type['VTName'] ?></div>                    
                    <!-- Table -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 80%">เส้นทาง</th>
                                <th style="width: 20%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($vehicle_type['routes'] as $route) {
                                $RCode = $route['RCode'];
                                ?>
                                <tr>
                                    <td class="text text-center"><?= $route['RouteName'] ?></td>
                                    <td class="text-center"><?= anchor("schedule/view/$RCode/$VTID", 'จัดตาราง', array('class' => 'btn btn-primary')) ?> </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="row">
        <legend class="<?= ($num_routes_seller <= 0) ? 'hidden' : '' ?>">ข้อมูลตารางเวลาเดินรถ</legend>
        <?php
        foreach ($data as $vehicle_type) {
            $VTID = $vehicle_type['VTID'];
            ?>
            <div class="col-md-12">
                <p class="lead"><?= $vehicle_type['VTName'] ?></p>
                <ul class="nav nav-tabs nav-justified" role="tablist" id="TabSchedule">
                    <?php
                    foreach ($vehicle_type['routes'] as $route) {
                        $RCode = $route['RCode'];
                        $id_tab = $VTID . '_' . $RCode;
                        ?>
                        <li class="">
                            <a href="#<?= $id_tab ?>" role="tab" data-toggle="tab"><?= $route['RouteName'] ?></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <div class="tab-content">
                    <?php
                    foreach ($vehicle_type['routes'] as $route) {
                        $RCode = $route['RCode'];
                        $id_content = $VTID . '_' . $RCode;
                        ?>
                        <div role="tabpanel" class="tab-pane" id="<?= $id_content ?>">
                            <?php
                            foreach ($route['routes_detail'] as $rd) {
                                ?>
                                <div class="col-md-12">
                                    <p class="lead">ตารางเวลาเดิน <strong><?= $rd['RouteName'] ?></strong></p>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <?php
                                                $width = 85 / $rd['NumStation'];
                                                foreach ($rd['stations'] as $station) {
                                                    ?>
                                                    <th style="width: <?= $width ?>%"><?= $station['StationName'] ?></th>
                                                <?php } ?>
                                                <th style="width: 15%;">รถเบอร์</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($rd['schedules'] as $schedule) {
                                                $TSID = $schedule['TSID'];
                                                $VCode = $schedule['VCode'];
                                                echo '<tr>';
                                                foreach ($schedule['stations'] as $station) {
                                                    ?>
                                                <td class="text-center"><?= $station['TimeDepart'] ?></td>
                                                <?php
                                            }
                                            echo "<td class=\"text-center\"><strong>$VCode</strong></td>";
                                            echo '</tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
        ?>

    </div>

</div>

