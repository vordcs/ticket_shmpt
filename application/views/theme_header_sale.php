<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">        
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <meta name="description"
              content="ระบบขายตั๋วโดยสาร">
        <meta name="author" content="VoRDcsCBNUKE">


        <title><?php echo $title; ?></title>
        <!-- Favicons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144"
              href="<?= asset_url() ?>img/apple-touch-icon-144-precomposed.png<?= '?v=' . $version ?>">
        <link rel="shortcut icon" href="<?= asset_url() ?>img/favicon.ico<?= '?v=' . $version ?>">
        <!-- font -->
        <link rel="stylesheet" href="<?= asset_url() ?>fonts/th_niramit_as.css" />

        <!-- Bootstrap core CSS ans JS -->
        <?php echo css('bootstrap.css?v=' . $version); ?>
        <?php echo css('bootflat.min.css?v=' . $version); ?>
        <?php echo css('theme.css?v=' . $version); ?>
        <?php echo css('animate.css?v=' . $version); ?>
        <?php echo css('font-awesome.css?v=' . $version); ?>  

        <?php echo css('sale/jquery.fs.selecter.min.css?v=' . $version); ?>
        <?php echo css('sale/style_sale.css?v=' . $version); ?>

        <?php echo css('sale/navmenu-push.css?v=' . $version); ?>
        <?php echo css('sale/jasny-bootstrap.min.css?v=' . $version); ?>

        <?php echo js('jquery.js?v=' . $version); ?>
        <?php echo js('bootstrap.js?v=' . $version); ?>

        <?php echo js('pace.min.js?v=' . $version); ?>

        <?php echo js('customJS.js?v=' . $version); ?>
        <?php js('site.min.js?v=' . $version); ?>
        <?php echo js('jquery.fs.selecter.min.js?v=' . $version); ?>   
        <style>
            body{
                padding-top: 1%;
            }            
            table tbody tr td{               
                color: #333333;
            }

            .selecter.custom { max-width: 600px; margin: 0 0 auto;padding: 0 0 auto}
            .selecter.custom .selecter-selected { border-width: 1px; }
            .selecter.custom .selecter-options { border-width: 0 2px 2px; padding: 1%; }
            .selecter.custom .selecter-item { border-radius: 3px !important; border: none; margin: 0 0 auto; }
            .selecter.custom .selecter-item:last-child { margin: 0; }

        </style>
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
//                setInterval(function () {                    
//                    window.location.reload();
//                }, 30000/2);
                $('#alert').delay(3000).fadeOut();
                $("select").selecter({
                    customClass: "custom"
                });
            });

        </script>     
    </head>
    <style>
        .pace {
            -webkit-pointer-events: none;
            pointer-events: none;

            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;

            z-index: 2000;
            position: fixed;
            margin: auto;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 15px;
            width: 300px;
            background: #fff;
            border: 1px solid #29d;

            overflow: hidden;
        }

        .pace .pace-progress {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;

            -webkit-transform: translate3d(0, 0, 0);
            -moz-transform: translate3d(0, 0, 0);
            -ms-transform: translate3d(0, 0, 0);
            -o-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);

            max-width: 200px;
            position: fixed;
            z-index: 2000;
            display: block;
            position: absolute;
            top: 0;
            right: 100%;
            height: 100%;
            width: 100%;
            background: #29d;
        }

        .pace.pace-inactive {
            display: none;
        }
    </style>
    <body>
        <?php
        ?>
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
                echo '<div id="alert" class="container alert alert-success animated pulse hidden-print" style="margin-top: 60px;"><strong>สำเร็จ</strong> ';
            } elseif ($alert['alert_mode'] == 'warning') {
                echo '<div id="alert" class="container alert alert-warning animated pulse hidden-print" style="margin-top: 60px;"><strong>คำเตือน</strong> ';
            } elseif ($alert['alert_mode'] == 'danger') {
                echo '<div id="alert" class="container alert alert-danger animated pulse hidden-print" style="margin-top: 60px;"><strong>ผิดพลาด</strong> ';
            } else {
                echo '<div class="container alert alert-info animated pulse hidden-print" style="margin-top: 60px;"><strong>เพิ่มเติม</strong> ';
            }
            echo $alert['alert_message'];
            echo '</div>';
        }
        ?>
