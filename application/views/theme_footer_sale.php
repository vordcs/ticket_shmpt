<ul class="nav pull-right scroll-top hidden-print hidden" style="z-index: 1;" id="scroll-top">
    <li><a href="#" id="top" style="background-color: transparent;"><i class="fa fa-arrow-circle-up fa-3x"></i></a></li>
</ul>

<div class="container-fluid hidden-print" style="z-index: 1;background-color: transparent;">
    <div class="row message-box hidden">
        <div class="col-md-4 col-xs-4 pull-right" style="">
            <div id="message_warning" class="alert alert-warning alert-dismissable hidden">
                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>                
                <strong><i class="fa fa-warning"></i>&nbsp;<span id="msg-title" class="msg-title">เเจ้งตือน ! </span></strong>
                <span id="msg"></span>
            </div>
            <div id="message_info" class="alert alert-info alert-dismissable hidden">
                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                <strong><i class="fa fa-info-circle"></i>&nbsp;<span id="msg-title" class="msg-title">เพิ่มเติม</span>&nbsp;</strong>
                <span id="msg" class="msg">You successfully read this important alert message.</span>
            </div>
            <div id="message_success" class="alert alert-success alert-dismissable hidden">
                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                <strong><i class="fa fa-check-circle"></i>&nbsp;<span id="msg-title" class="msg-title">สำเร็จ ! </span>&nbsp;</strong>
                <span id="msg" class="msg"></span> This alert needs your attention, but it's not super important.
            </div>
            <div id="message_danger" class="alert alert-danger hidden">
                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                <strong><i class="fa fa-warning"></i>&nbsp;<span id="msg-title" class="msg-title">ผิดพลาด !</span>&nbsp;</strong>
                <span id="msg" class="msg">Change a few things up and try submitting again.</span>
            </div>
        </div>
    </div>
</div>
<div class="th-footer-bottom hidden hidden-print">COPYRIGHT &copy; 2014</div>
<script>
    jQuery(document).ready(function ($) {
        $('#top').click(function () {
            $("html, body").animate({scrollTop: 0}, 500);
        });
    });
</script>
</body>
</html>