<!DOCTYPE html>
<?php $version = 1; ?>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>เข้าสู่ระบบ</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <!-- Favicons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144"
              href="<?= asset_url() ?>img/apple-touch-icon-144-precomposed.png<?= '?v=' . $version ?>">
        <link rel="shortcut icon" href="<?= asset_url() ?>img/favicon.ico<?= '?v=' . $version ?>"> 
        <!-- Bootstrap core CSS ans JS -->

        <?php echo css('bootstrap.css?v=' . $version); ?>
        <?php echo css('bootflat.min.css?v=' . $version); ?>
        <?php echo css('font-awesome.css?v=' . $version); ?>
        <?php echo css('animate.css?v=' . $version); ?>
        <?php echo css('signin.css?v=' . $version); ?>
        <?php echo js('jquery.js?v=' . $version); ?>
        <?php echo js('bootstrap.js?v=' . $version); ?>



    </head>
    <body>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">บริษัท สหมิตรภาพ(2512) จำกัด</a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                    <p class="navbar-text navbar-right">Signed in as Mark Otto</p>
                </div>
            </div>
        </div>

        <div class="login-container">



            <!--<form class="form-signin" role="form">-->
                <?= $form_action ?>
                <h2 class="form-signin-heading">Member Login</h2>

                <?= $form_input['user'] ?>
                <?= $form_input['pass'] ?>               
                <button class="btn btn-lg btn-success btn-block" type="submit">Sign in</button>
                <?= form_close() ?>
<!--            </form>-->

            <!-- /container -->


        </div> <!-- /login-container -->

    </body>

</html>
