
<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `pay_list` where pay_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="pay-form">
        <input type="hidden" name="id" value="<?php echo isset($pay_id) ? $pay_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                    <input type="hidden" name="transaction_id" value="<?php echo $_GET['transaction_id'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="check_no" class="control-label">Cheque No</label>
                        <input type="text" step="any" name="check_no"  id="check_no" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($check_no) ? $check_no : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="amount" class="control-label">Amount</label>
                        <input type="number" step="any" name="amount"  id="amount" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($amount) ? $amount : '' ?>">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#pay-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_pay',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($stock_id) ?>" != 1)
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>