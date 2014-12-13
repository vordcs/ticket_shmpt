<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords"
              content="thaihubhosting, hosting, host, high perfomance">
        <meta name="description"
              content="เว็บโฮสติ้งคุณภาพสูง ที่ได้รับการออกแบบติดตั้งและผู้แลจากผู้เชี่ยวชาญตลอด 24 ชั่วโมง">
        <meta name="author" content="CBNUKE">


        <title><?php echo $title; ?></title>
        <!-- Favicons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144"
              href="<?= asset_url() ?>img/apple-touch-icon-144-precomposed.png<?= '?v=' . $version ?>">
        <link rel="shortcut icon" href="<?= asset_url() ?>img/favicon.ico<?= '?v=' . $version ?>">
        <!-- Bootstrap core CSS ans JS -->
        <?php echo js('pace.min.js?v=' . $version); ?>
        <?php echo css('bootstrap.css?v=' . $version); ?>
        <?php echo css('bootflat.min.css?v=' . $version); ?>
        <?php echo css('pace.css?v=' . $version); ?>
        <?php echo css('theme.css?v=' . $version); ?>
        <?php echo css('label.min.css?v=' . $version); ?>
        <?php echo css('segment.min.css?v=' . $version); ?>
        <?php echo css('font-awesome.css?v=' . $version); ?>
        <?php echo css('animate.css?v=' . $version); ?>
        <?php echo css('customCSS.css?v=' . $version); ?>
        <?php echo css('site.min.css?v=' . $version); ?>
        <?php echo js('jquery.js?v=' . $version); ?>
        <?php echo js('bootstrap.js?v=' . $version); ?>
        <?php echo js('customJS.js?v=' . $version); ?>
        <?php echo js('site.min.js?v=' . $version); ?>

        <!--time picker-->    
        <?php echo css('bootstrap-timepicker.min.css?v=' . $version); ?>  
        <?php echo js('bootstrap-timepicker.min.js?v=' . $version); ?> 

        <!--date picker-->    
        <?php echo css('datepicker.css?v=' . $version); ?>         
        <?php echo js('bootstrap-datepicker.js?v=' . $version); ?> 
        <!-- thai extension -->
        <?php echo js('bootstrap-datepicker-thai.js?v=' . $version); ?>  
        <?php echo js('/locales/bootstrap-datepicker.th.js?v=' . $version); ?>  

        <script type="text/javascript">
            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) { //use `this`, not `document`
                    $('#top-nav').fadeOut();
                    $(".pace-progress").css("margin-top", "58px");
                } else {
                    $('#top-nav').fadeIn();
                    $(".pace-progress").css("margin-top", "91px");
                }
                if ($(this).scrollTop() > $(window).height() / 2) {
                    $('#scroll-top').removeClass('hidden');
                } else {
                    $('#scroll-top').addClass('hidden');
                }
            });
            jQuery(window).load(function () {
                $('.alert').delay(3000).fadeOut();
            });
        </script>
    </head>
    <body>
        <!-- Fixed navbar -->
        <div class="navbar navbar-fixed-top sh-nav" role="navigation">
            <div id="top-nav" class="sh-top-nav">          
                <h4 class="color-white" style="margin: 4px 0px 4px 4px;">บริษัท สหมิตรภาพ(2512) จำกัด</h4>
                <p class="text-right" style="margin-top: -20px;">
                    <a id="popoverMail" href="#" data-content="ติดต่อกับผู้ให้บริการ ผ่าน support@thaihubhosting.com" rel="popover" data-placement="bottom" data-original-title="ติดต่อผ่านอีเมล์" data-trigger="hover"><i class="fa fa-envelope"></i> Support |</a>
                    <a id="popoverFacebook" class="hidden-xs" href="#" data-content="ติดต่อกับผู้ให้บริการ ผ่าน Facebookpage ThaiHubHosting" rel="popover" data-placement="bottom" data-original-title="ติดต่อผ่านเพจ" data-trigger="hover"> <i class="fa fa-facebook"></i> ThaiHubHosting |</a>
                    <a id="popoverMobile" href="#" data-content="ติดต่อกับผู้ให้บริการ ผ่านเบอร์ <?= lang('phone_number1') ?>" rel="popover" data-placement="bottom" data-original-title="ติดต่อผ่านสายตรง" data-trigger="hover"><i class="fa fa fa-mobile-phone"></i> <?= lang('phone_number1') ?></a>
                </p>
            </div>
            <div class="navbar-collapse collapse">
                <div class="subnavbar">
                    <div class="subnavbar-inner">
                        <div class="container" id="mainmenu">
                            <ul class="mainnav">
                                <li class="active"><a href="<?= base_url('home/') ?>"><i class="fa fa-dashboard"></i><span>Dashboard</span> </a> </li>
                                <li id="btnCandidate"><a href="<?= base_url('candidate/') ?>"><i class="fa fa-bullhorn"></i><span>สมัครงาน</span> </a> </li>
                                <li id="btnHR"><a href="<?= base_url('hr/') ?>"><i class="fa fa-users"></i><span>งานบุคคล</span> </a> </li>
                                <li id="btnVehicle"><a href="<?= base_url('vehicle/') ?>"><i class="fa fa-car"></i><span>รถ</span> </a> </li>
                                <li id="btnRoute"><a href="<?= base_url('route/') ?>"><i class="fa fa-share-alt"></i><span>เส้นทาง</span></a></li>
                                <li id="btnSchedule"><a href="<?= base_url('schedule/') ?>"><i class="fa fa-list"></i><span>ตารางเดินรถ</span></a></li>
                                <li id="btnCost"><a href="<?= base_url('cost/') ?>"><i class="fa fa-pencil-square-o"></i><span>ค่าใช้จ่าย</span></a></li>
                                <li id="btnReport"><a href="<?= base_url('report/') ?>"><i class="fa fa-calendar-o"></i><span>รายงาน</span> </a> </li>                              
                            </ul>
                        </div>
                        <!-- /container --> 
                    </div>
                    <!-- /subnavbar-inner --> 
                </div>
            </div>
            <!--/.nav-collapse -->
        </div>

        <?php
        if (isset($debug) && $debug != NULL) {
            echo '<div class="container" style="margin-top: 60px;">';
            print '<pre>';
            print_r($debug);
            print '</pre>';
            echo '</div>';
        }

        if (isset($alert) && $alert != NULL) {
            if ($alert['alert_mode'] == 'success'){
                echo '<div class="container alert alert-success animated pulse" style="margin-top: 60px;"><strong>สำเร็จ</strong> ';
            }
            elseif ($alert['alert_mode'] == 'warning'){
                echo '<div class="container alert alert-warning animated pulse" style="margin-top: 60px;"><strong>คำเตือน</strong> ';
            }
            elseif ($alert['alert_mode'] == 'danger'){
                echo '<div class="container alert alert-danger animated pulse" style="margin-top: 60px;"><strong>ผิดพลาด</strong> ';
            }
            else{
                echo '<div class="container alert alert-info animated pulse" style="margin-top: 60px;"><strong>เพิ่มเติม</strong> ';
            }
            echo $alert['alert_message'];
            echo '</div>';
        }
        ?>