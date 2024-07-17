<?php
require_once("DBConnection.php");
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM `product_list` where product_id = '{$_GET['id']}'");
    foreach ($qry->fetchArray() as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="product-form">
        <input type="hidden" name="id" value="<?php echo isset($product_id) ? $product_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="product_code" class="control-label">Code</label>
                        <input type="text" name="product_code" autofocus id="product_code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($product_code) ? $product_code : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="category_id" class="control-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select form-select-sm rounded-0" required>
                            <option <?php echo (!isset($category_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $cat_qry = $conn->query("SELECT * FROM category_list where `status` = 1  order by `name` asc");
                            while ($row = $cat_qry->fetchArray()) :
                            ?>
                                <option value="<?php echo $row['category_id'] ?>" <?php echo (isset($category_id) && $category_id == $row['category_id']) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>


                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" name="name" id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="price" class="control-label">Price</label>
                        <input type="number" step="any" name="price" id="price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($price) ? $price : '0' ?>">
                    </div>
                    <div class="form-group">
                        <label for="buyprice" class="control-label">Wholesale Price</label>
                        <input type="number" step="any" name="whoprice" id="whoprice" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($whoprice) ? $whoprice : '0' ?>">
                    </div>
                    <div class="form-group">
                        <label for="buyprice" class="control-label">Buy Price</label>
                        <input type="number" step="any" name="buyprice" id="buyprice" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($buyprice) ? $buyprice : '0' ?>">
                    </div>
                    <div class="form-group">
                        <label for="creditprice" class="control-label">Credit Price</label>
                        <input type="number" step="any" name="creditprice" id="creditprice" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($creditprice) ? $creditprice : '0' ?>">
                    </div>
                    <?php

                    ?>
                    <div class="form-group">
                        <label for="discount" class="control-label">Discount</label>
                        <input type="number" step="any" name="discount" id="discount" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($discount) ? $discount : '0' ?>">
                    </div>
                    <div class="form-group">
                        <input type="hidden" step="any" name="re_profit" id="re_profit" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($re_profit) ? $price - $buyprice : '0' ?>">
                    </div>
                    <div class="form-group">
                        <input type="hidden" step="any" name="wh_profit" id="wh_profit" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($wh_profit) ? $whoprice - $buyprice : '0' ?>">
                    </div>
                    <div class="form-group">
                        <input type="hidden" step="any" name="cr_profit" id="cr_profit" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($cr_profit) ? $creditprice - $buyprice : '0' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" id="description" cols="30" rows="3" class="form-control rounded-0" required><?php echo isset($description) ? $description : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0" required>
                            <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

    // $(function() {
    //     $('#product-form').submit(function(e) {
    //         e.preventDefault();
    //         $('.pop_msg').remove()
    //         var _this = $(this)
    //         var _el = $('<div>')
    //         _el.addClass('pop_msg')
    //         $('#uni_modal button').attr('disabled', true)
    //         $('#uni_modal button[type="submit"]').text('submitting form...')
    //         $.ajax({
    //             url: './Actions.php?a=save_product',
    //             data: new FormData($(this)[0]),
    //             cache: false,
    //             contentType: false,
    //             processData: false,
    //             method: 'POST',
    //             type: 'POST',
    //             dataType: 'json',
    //             error: err => {
    //                 console.log(err)
    //                 _el.addClass('alert alert-danger')
    //                 _el.text("An error occurred.")
    //                 _this.prepend(_el)
    //                 _el.show('slow')
    //                 $('#uni_modal button').attr('disabled', false)
    //                 $('#uni_modal button[type="submit"]').text('Save')
    //             },
    //             success: function(resp) {
    //                 if (resp.status == 'success') {
    //                     _el.addClass('alert alert-success')
    //                     $('#uni_modal').on('hide.bs.modal', function() {
    //                         location.reload()
    //                     })
    //                     if ("<?php echo isset($product_id) ?>" != 1)
    //                         _this.get(0).reset();
    //                 } else {
    //                     _el.addClass('alert alert-danger')
    //                 }
    //                 _el.text(resp.msg)

    //                 _el.hide()
    //                 _this.prepend(_el)
    //                 _el.show('slow')
    //                 $('#uni_modal button').attr('disabled', false)
    //                 $('#uni_modal button[type="submit"]').text('Save')
    //             }
    //         })
    //     })
    // })

    $(function() {
    $('#product-form').submit(function(e) {
        e.preventDefault();
        $('.pop_msg').remove();

        // Check all number input fields and set their value to '0' if they are empty
        $(this).find('input[type="number"]').each(function() {
            if ($(this).val() === '') {
                $(this).val('0');
            }
        });

        var _this = $(this);
        var _el = $('<div>');
        _el.addClass('pop_msg');
        $('#uni_modal button').attr('disabled', true);
        $('#uni_modal button[type="submit"]').text('submitting form...');
        
        $.ajax({
            url: './Actions.php?a=save_product',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
                console.log(err);
                _el.addClass('alert alert-danger');
                _el.text("An error occurred.");
                _this.prepend(_el);
                _el.show('slow');
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    _el.addClass('alert alert-success');
                    $('#uni_modal').on('hide.bs.modal', function() {
                        location.reload();
                    });
                    if ("<?php echo isset($product_id) ?>" != 1)
                        _this.get(0).reset();
                } else {
                    _el.addClass('alert alert-danger');
                }
                _el.text(resp.msg);

                _el.hide();
                _this.prepend(_el);
                _el.show('slow');
                $('#uni_modal button').attr('disabled', false);
                $('#uni_modal button[type="submit"]').text('Save');
            }
        });
    });
});
</script>