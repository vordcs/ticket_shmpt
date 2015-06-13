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
            height: 90px;
            width: 90px;
            margin: auto;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .pace.pace-inactive .pace-activity {
            display: none;
        }

        .pace .pace-activity {
            position: fixed;
            z-index: 2000;
            display: block;
            position: absolute;
            left: -30px;
            top: -30px;
            height: 100px;
            width: 100px;
            display: block;
            border-width: 30px;
            border-style: double;
            border-color: #29d transparent transparent;
            border-radius: 50%;

            -webkit-animation: spin 1s linear infinite;
            -moz-animation: spin 1s linear infinite;
            -o-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }

        .pace .pace-activity:before {
            content: ' ';
            position: absolute;
            top: 10px;
            left: 10px;
            height: 50px;
            width: 50px;
            display: block;
            border-width: 10px;
            border-style: solid;
            border-color: #29d transparent transparent;
            border-radius: 50%;
        }

        @-webkit-keyframes spin {
            100% { -webkit-transform: rotate(359deg); }
        }

        @-moz-keyframes spin {
            100% { -moz-transform: rotate(359deg); }
        }

        @-o-keyframes spin {
            100% { -moz-transform: rotate(359deg); }
        }

        @keyframes spin {
            100% {  transform: rotate(359deg); }
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
