<script>
    jQuery(document).ready(function ($) {
       $("#wrapper").toggleClass("toggled");
    });
</script>
<div id="wrapper" class="container-fluid">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="#">
                    เส้นทางเดินรถ
                </a>
            </li>
            <li>
                <a href="#">Dashboard</a>
            </li>
            <li>
                <a href="#">Shortcuts</a>
            </li>
            <li>
                <a href="#">Overview</a>
            </li>
            <li>
                <a href="#">Events</a>
            </li>
            <li>
                <a href="#">About</a>
            </li>
            <li>
                <a href="#">Services</a>
            </li>
            <li>
                <a href="#">Contact</a>
            </li>
        </ul>
    </div>
    <!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">
            <div class="row animated fadeInUp">
                <div class="col-md-12">
                    <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">เลือกเส้นทาง</a>
                </div>
            </div>
            <div class="row animated fadeInUp">
                <div class="col-lg-3">  
                    <div class="col-lg-12">
                        <h3 class="">เลือกเวลา</h3>
                    </div>
                    <div class="col-lg-12">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="badge">14</span>
                                Cras justo odio
                            </li>
                            <li class="list-group-item">
                                <span class="badge badge-default">91</span>
                                Dapibus ac facilisis in
                            </li>
                            <li class="list-group-item">
                                <span class="badge badge-primary">38</span>
                                Morbi leo risus
                            </li>
                            <li class="list-group-item">
                                <span class="badge badge-success">56</span>
                                Porta ac consectetur ac
                            </li>
                            <li class="list-group-item">
                                <span class="badge badge-warning">20</span>
                                Vestibulum at eros
                            </li>
                            <li class="list-group-item">
                                <span class="badge badge-danger">99+</span>
                                Dapibus ac facilisis in
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3">  
                    <div class="col-lg-12">
                        <h3 class="">เลือกจุดจอด</h3>
                    </div>
                </div>
                <div class="col-lg-3">  
                    <div class="col-lg-12">
                        <h3 class="">เลือกที่นั่ง</h3>
                    </div>
                    <div class="col-lg-12">
                        <?php for ($i = 0; $i < 15; $i++) { ?>
                            <div class="col-lg-3 col-md-4 col-xs-6" style="margin: 0px 0px 0px 0px">
                                <a class="thumbnail" href="#">
                                    <img class="img-responsive" src="http://placehold.it/400x300&text=<?= $i + 1 ?>" alt="">
                                </a>
                            </div>
                            <!--<div class="" style="background-color: #009A93;">-->
                            <!--<div style="background-color: red; height: 50px; width: 50px;margin: 10px 10px 10px 10px"><?= $i ?></div>-->   
                            <!--</div>-->
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-3">  
                    <div class="col-lg-12">
                        <h3 class="">ข้อมูลผู้โดยสาร</h3>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Menu Toggle Script -->
<script>
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
</script>

<!--<div class="container">
    TimeLine
================================================== 
    <div class="row">
        <h3 class="example-title">TimeLine</h3>
        <div class="col-md-12">
            <div class="timeline">
                <dl>
                    <dt>Apr 2014</dt>
                    <dd class="pos-right clearfix">
                        <div class="circ"></div>
                        <div class="time">Apr 14</div>
                        <div class="events">
                            <div class="pull-left">
                                <img class="events-object img-rounded" src="img/photo-1.jpg">
                            </div>
                            <div class="events-body">
                                <h4 class="events-heading">Bootstrap</h4>
                                <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
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
                    </dd>
                    <dt>Mar 2014</dt>
                    <dd class="pos-right clearfix">
                        <div class="circ"></div>
                        <div class="time">Mar 15</div>
                        <div class="events">
                            <div class="pull-left">
                                <img class="events-object img-rounded" src="img/photo-3.jpg">
                            </div>
                            <div class="events-body">
                                <h4 class="events-heading">Flat UI</h4>
                                <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
                            </div>
                        </div>
                    </dd>
                    <dd class="pos-left clearfix">
                        <div class="circ"></div>
                        <div class="time">Mar 8</div>
                        <div class="events">
                            <div class="pull-left">
                                <img class="events-object img-rounded" src="img/photo-4.jpg">
                            </div>
                            <div class="events-body">
                                <h4 class="events-heading">UI design</h4>
                                <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
                            </div>
                        </div>
                    </dd>

                </dl>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

        </div>

    </div>   
</div>-->
