<script>
    $(document).ready(function () {

    });
</script>
<div id="" class="container-fluid">
    <div class="row-fluid animated fadeInUp">        
        <div class="col-lg-12">

        </div>
    </div>
</div>
<br>
<div class="container">    
    <div class="row">    
        <div class="col-lg-8 col-lg-offset-2 well ">    
            <div class="col-lg-12 text-center">
                <h3 class="fs-title">ค้นหาเที่ยวรถ</h3>
                <p class="fs-subtitle lead">This is step 1</p>
            </div>
            <div class="col-lg-12">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">ประเภทรถ</label>
                        <div class="col-sm-8">
                            <?php echo $from_search['VTID'] ?>
                        </div>
                    </div>   
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">ต้นทาง</label>
                        <div class="col-sm-8">
                            <?php echo $from_search['RSource'] ?>
                        </div>
                    </div>     
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">ปลายทาง</label>
                        <div class="col-sm-8">
                            <?php echo $from_search['RDestination'] ?>
                        </div>
                    </div>                     
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">ค้นหา</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-12 text-center">
                <?php
                $sale = array(
                    'type' => "button",
                    'class' => "btn  btn-info btn-lg",
                );
                anchor('sale/step1', 'ขายตั๋ว', $sale);
                ?>            
            </div>
        </div>

    </div>
</div>
