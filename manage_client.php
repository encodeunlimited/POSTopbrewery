<?php
require_once("DBConnection.php");
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM `cclient_list` where client_id = '{$_GET['id']}'");
    foreach ($qry->fetchArray() as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="client-form">
        <input type="hidden" name="id" value="<?php echo isset($client_id) ? $client_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="control-label">Full Name</label>
                        <input type="text" name="name" autofocus id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="address" class="control-label">Address</label>
                        <textarea name="address" id="address" cols="30" rows="3" class="form-control rounded-0" required><?php echo isset($address) ? $address : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contact</label>
                        <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($contact) ? $contact : '' ?>">
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
    $(function() {
        $('#client-form').submit(function(e) {
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
            _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled', true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url: './Actions.php?a=save_client',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#uni_modal button').attr('disabled', false)
                    $('#uni_modal button[type="submit"]').text('Save')
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal', function() {
                            location.reload()
                        })
                        if ("<?php echo isset($client_id) ?>" != 1)
                            _this.get(0).reset();
                    } else {
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#uni_modal button').attr('disabled', false)
                    $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>