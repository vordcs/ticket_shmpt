<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <meta name="description"
              content="ระบบขายตั๋วโดยสาร">
        <meta name="author" content="VoRDCSCBNUKE">


        <title><?php echo $title; ?></title>
        <!-- Favicons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144"
              href="<?= asset_url() ?>img/apple-touch-icon-144-precomposed.png<?= '?v=' . $version ?>">
        <link rel="shortcut icon" href="<?= asset_url() ?>img/favicon.ico<?= '?v=' . $version ?>">
        <!-- Bootstrap core CSS ans JS -->
        <?php echo css('bootstrap.css?v=' . $version); ?>
        <?php echo css('bootflat.min.css?v=' . $version); ?>
        <?php echo css('theme.css?v=' . $version); ?>
        <?php echo css('animate.css?v=' . $version); ?>
        <?php echo css('font-awesome.css?v=' . $version); ?>
        <?php echo css('customCSS.css?v=' . $version); ?>
        <?php echo js('jquery.js?v=' . $version); ?>
        <?php echo js('bootstrap.js?v=' . $version); ?>

        <?php echo js('customJS.js?v=' . $version); ?>

        <?php echo css('sale/style_sale.css?v=' . $version); ?>

        <?php echo css('sale/navmenu-push.css?v=' . $version); ?>
        <?php echo css('sale/jasny-bootstrap.min.css?v=' . $version); ?>


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
                $('#alert').delay(3000).fadeOut();
            });
        </script>
        <style>
            body{
                padding-top: 1%;
            }
            .message-box{
                position: fixed;
                bottom: 0;
                left: 0;        
                opacity: 0.7;
                width: 30%;        
                vertical-align: middle; 
                margin-right:20%;
                z-index: 50px;

                -webkit-transition: all .2s ease-in-out;
                -moz-transition: all .2s ease-in-out;
                -o-transition: all .2s ease-in-out;
                -ms-transition: all .2s ease-in-out;
            }
            .message-box:hover {
                opacity: 1.0; 

                -webkit-transform: scale(1);
                -moz-transform: scale(1);
                -o-transform: scale(1);
                transform: scale(1);
            } 
            table tbody tr td{               
                color: #333333;
            }
        </style>
    </head>
    <body onload="document.refresh();">        
        <?php
        if (isset($debug) && $debug != NULL) {
            echo '<div class="container hidden-print" style="margin-top: 60px;">';
            print '<pre>';
            print_r($debug);
            print '</pre>';
            echo '</div>';
        }

        if (isset($alert) && $alert != NULL) {
            if ($alert['alert_mode'] == 'success') {
                echo '<div id="alert" class="container alert alert-success animated pulse" style="margin-top: 60px;"><strong>สำเร็จ</strong> ';
            } elseif ($alert['alert_mode'] == 'warning') {
                echo '<div id="alert" class="container alert alert-warning animated pulse" style="margin-top: 60px;"><strong>คำเตือน</strong> ';
            } elseif ($alert['alert_mode'] == 'danger') {
                echo '<div id="alert" class="container alert alert-danger animated pulse" style="margin-top: 60px;"><strong>ผิดพลาด</strong> ';
            } else {
                echo '<div class="container alert alert-info animated pulse" style="margin-top: 60px;"><strong>เพิ่มเติม</strong> ';
            }
            echo $alert['alert_message'];
            echo '</div>';
        }
        ?>
