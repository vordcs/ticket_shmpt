<script>
    jQuery(document).ready(function ($) {
//        $("#wrapper").toggleClass("toggled");
    });
</script>
<div id="" class="container-fluid">
    <div class="row-fluid animated fadeInUp">        
        <div class="col-lg-12">
            <ol class="progtrckr" data-progtrckr-steps="4">
<!--                <li class="progtrckr-done"><span class="lead">สถานีต้นทาง</span></li>
                --><li id="step1" class="progtrckr-done"><span class="lead">เส้นทาง</span></li><!--                
                --><li id="step2" class="progtrckr-done"><span class="lead">สถานีปลายทาง</span></li><!--                 
                --><li id="step3" class="progtrckr-todo"><span class="lead">เวลา,ที่นั่ง,ค่าโดยสาร</span></li><!--                 
                --><li id="step4" class="progtrckr-todo"><span class="lead">พิมพ์บัตรโดยสาร</span></li>

            </ol>
        </div>
    </div>
</div>
<div id="wrapper" class="container-fluid">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="#">
                    Start Bootstrap
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
            <div class="row">
                <div class="col-lg-12">
                    <h1>Simple Sidebar</h1>
                    <p>This template has a responsive menu toggling system. The menu will appear collapsed on smaller screens, and will appear non-collapsed on larger screens. When toggled using the button below, the menu will appear/disappear. On small screens, the page content will be pushed off canvas.</p>
                    <p>Make sure to keep all page content within the <code>#page-content-wrapper</code>.</p>
                    <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
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
