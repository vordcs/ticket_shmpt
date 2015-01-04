<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCheckIn").addClass("active");
    });
</script>
<div class="container" style=""> 
    <div class="row">  
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-header">
                    <span><?php echo $page_title; ?> : <?php echo $page_title_small; ?> </span>
                </div>
                <div class="widget-content">
                    <div class="col-md-6">
                        <div class="col-md-12 clock" id="clock">
                            <div id="Date"></div>
                            <ul id="time">
                                <li id="hours"> </li>
                                <li id="point">:</li>
                                <li id="min"> </li>
                                <li id="point">:</li>
                                <li id="sec"> </li>
                            </ul>
                        </div> 
                        <div class="col-md-12">
                            <legend>ลงเวลา</legend>
                            <div class="form-group">
                                <label >เส้นทาง</label>
                                <input type="text" class="form-control" placeholder="RCode" value="">
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label >รถเบอร์</label>
                                    <input type="text" class="form-control" placeholder="RCode" value="">
                                </div>
                                <div class="col-md-6">
                                    <label >วันที่</label>
                                    <input type="text" class="form-control" placeholder="RCode" value="">
                                </div>
                            </div>   
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>รถเบอร์</th>
                                    <th>Model</th>
                                    <th>Color</th>
                                    <th>Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="filterable-cell">Ford</td>
                                    <td class="filterable-cell">Escort</td>
                                    <td class="filterable-cell">Blue</td>
                                    <td class="filterable-cell">2000</td>
                                </tr>
                                <tr>
                                    <td class="filterable-cell">Ford</td>
                                    <td class="filterable-cell">Escort</td>
                                    <td class="filterable-cell">Blue</td>
                                    <td class="filterable-cell">2000</td>
                                </tr>
                                <tr>
                                    <td class="filterable-cell">Ford</td>
                                    <td class="filterable-cell">Escort</td>
                                    <td class="filterable-cell">Blue</td>
                                    <td class="filterable-cell">2000</td>
                                </tr>
                                <tr>
                                    <td class="filterable-cell">Ford</td>
                                    <td class="filterable-cell">Escort</td>
                                    <td class="filterable-cell">Blue</td>
                                    <td class="filterable-cell">2000</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>