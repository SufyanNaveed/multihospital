
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        <section class="col-md-7 row">
            <header class="panel-heading">
                <?php
                 
                    echo 'Stock Transfer';

                ?>
            </header>
            <div class="panel-body">
                <div class="adv-table editable-table ">
                    <div class="col-lg-12">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-6">
                            <?php echo validation_errors(); ?>
                        </div>
                        <div class="col-lg-3"></div>
                    </div>
                    <form role="form" action="stock/transfer" method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo 'From Hospital'; ?></label>
                            <!-- <input type="text" class="form-control" name="name" id="exampleInputEmail1" value='<?php if (!empty($hospital->name)) { echo $hospital->name; } ?>' placeholder=""> -->
                            <select class="form-control" name="from_hospital" id="exampleInputEmail1">
                                <option value="">Select Hospital</option>
                                <?php if($hospitals){ foreach($hospitals as $hospital){ ?>
                                    <option value="<?php echo $hospital->id; ?>"><?php echo $hospital->name; ?></option>
                                <?php } } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo 'Medicine'; ?></label>
                            <select class="form-control select2" name="medicines[]" id="medicines" multiple>
                                <option value="">Select Medicine</option>
                                <?php if($medicines){ foreach($medicines as $medicine){ ?>
                                    <option value="<?php echo $medicine->id; ?>"><?php echo $medicine->name; ?></option>
                                <?php } } ?>
                            </select>
                        </div> 

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo 'Quantity'; ?></label>
                            <input type="text" class="form-control" name="qty" id="exampleInputEmail1" value='' placeholder="Enter quantity comma seperated if you transfer multiple stock 5,10,20,50,100">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo 'To Hospital'; ?></label>
                            <!-- <input type="text" class="form-control" name="name" id="exampleInputEmail1" value='<?php if (!empty($hospital->name)) { echo $hospital->name; } ?>' placeholder=""> -->
                            <select class="form-control" name="to_hospital" id="exampleInputEmail1">
                                <option value="">Select Hospital</option>
                                <?php if($hospitals){ foreach($hospitals as $hospital){ ?>
                                    <option value="<?php echo $hospital->id; ?>"><?php echo $hospital->name; ?></option>
                                <?php } } ?>
                            </select>
                        </div>

                        <div class="panel col-md-12">
                            <button type="submit" name="submit" class="btn btn-info pull-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </form>


                </div>
            </div>
        </section>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
<!--footer start-->




<script src="common/js/codearistos.min.js"></script> 

<script>
    $(document).ready(function () {
        <?php if (!empty($hospital->id)) { if (empty($hospital->package)) { ?>
            $('.pos_client').show();
        <?php } else { ?>
            $('.pos_client').hide();
        <?php } } else { ?>
            $('.pos_client').hide();
        <?php } ?>

        $(document.body).on('change', '#pos_select', function () {
            var v = $("select.pos_select option:selected").val()
            if (v == '') {
                $('.pos_client').show();
            } else {
                $('.pos_client').hide();
            }
        });

    });

     

    $(document).ready(function () {
        $('#medicines').select2();
    });
</script>