<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnReport").addClass("active");
    });
    $(document).ready(function () {

    });
</script>
<div class="container">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;"><?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
</div>
<div class="container">
    <div class="row">  
        <table class="highchart" data-graph-container-before="1" data-graph-type="column" style="display:none">
            <thead>
                <tr>                                  
                    <th>Month</th>
                    <th>Sales</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>January</td>
                    <td>8000</td>
                </tr>
                <tr>
                    <td>February</td>
                    <td>12000</td>
                </tr>
                <tr>
                    <td>March</td>
                    <td>18000</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <table class="highchart" data-graph-container-before="1" data-graph-type="pie" style="display:none">
    <thead>
        <tr>                                  
            <th>Month</th>
            <th>Sales</th>
        </tr>
     </thead>
     <tbody>
        <tr>
            <td>January</td>
            <td>8000</td>
        </tr>
        <tr>
            <td>February</td>
            <td>12000</td>
        </tr>
        <tr>
            <td>March</td>
            <td>18000</td>
        </tr>
    </tbody>
</table>
    </div>
</div>


<script>
    $(document).ready(function() {
  $('table.highchart').highchartTable();
});
    </script>