<style>
    #uni_modal .modal-footer {
        display: none !important;
    }
</style>
<div class="container-fluid">
    <div class="form-group">
        <label for="amount" class="control-label fs-4 fw-bold">Payable Amount</label>
        <input type="text" id="amount" class="form-control form-control-lg text-end" value="<?php echo $_GET['amount'] ?>" disabled>
        <input type="hidden" id="tprofit" class="form-control form-control-lg text-end" value="<?php echo $_GET['tprofit'] ?>" disabled>
    </div>
    <div class="form-group">
        <label for="s_des" class="control-label fs-4 fw-bold">Special Discount</label>
        <input type="number" step="any" id="s_des" class="form-control form-control-lg text-end" value="0">
    </div>

    <div class="form-group">
        <!-- <label for="change" class="control-label fs-4 fw-bold">Change</label> -->
        <input type="hidden" id="change" class="form-control form-control-lg text-end" value="0" disabled>
    </div>
    <div class="form-group">
        <!-- <label for="client_id" class="control-label fs-4 fw-bold">Customer Name</label> -->
        <!-- <input type="text" step="any" id="customer" class="form-control form-control-lg text-end" value="" > -->
    </div>
    <div class="form-group">
        <label for="vehical_no" class="control-label fs-4 fw-bold">Vehical No</label>
        <input type="text" step="any" id="vehical_no" class="form-control form-control-lg text-end" value="">
    </div>
    <div class="form-group">
        <label for="dnic" class="control-label fs-4 fw-bold">Driver NIC</label>
        <input type="text" step="any" id="dnic" class="form-control form-control-lg text-end" value="">
    </div>
    <div class="form-group">
        <label for="dname" class="control-label fs-4 fw-bold">Driver Name</label>
        <input type="text" step="any" id="dname" class="form-control form-control-lg text-end" value="">
    </div>
    <div class="form-group">
        <input type="hidden" id="discount" class="form-control form-control-lg text-end" value="100" disabled>
    </div>
    <div class="form-group">
        <label for="arrears" class="control-label fs-4 fw-bold">Amount in Credit</label>
        <!-- <input type="number" step="any" id="arrears" class="form-control form-control-lg text-end" value="0"> -->
        <input type="test" id="arrears" class="form-control form-control-lg text-end" value="0" disabled>
    </div>
    <div class="form-group">
        <!-- <label for="tender" class="control-label fs-4 fw-bold">Tendered Amount</label> -->
        <input type="hidden" step="any" id="tender" class="form-control form-control-lg text-end" value="0" disabled>
        <!-- <input type="text" id="tender" class="form-control form-control-lg text-end" value="<?php echo $_GET['amount'] ?>" disabled> -->
    </div>
    <div class="w-100 d-flex justify-content-end mt-2">
        <button class="btn btn-sm btn-primary me-2 rounded-0" type="button" id="save_trans">Save</button>
        <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
    </div>
</div>
<script>
    $(function() {
        $('#uni_modal').on('shown.bs.modal', function() {
            if ($(this).find('#tender').length > 0)
                $('#tender').trigger('focus').select();

        })
        $('#tender').on('keydown', function(e) {
            if (e.which == 13) {
                e.preventDefault()
                $('#save_trans').trigger('click')
            }
        })
        $('#tender').on('keypress input', function() {




        })
        $('#save_trans').mouseover(function() {
            var amount = $('#amount').val().replace(/,/gi, "")
            var ts_profit = $('#tprofit').val()
            var s_des = $('#s_des').val().replace(/,/gi, "")
            var tt_profit = ts_profit - s_des
            //var arrears = $('#arrears').val().replace(/,/gi,"")
            // var customer = $('#customer').val()
            // var vehical_no = $('#vehical_no').val()


            $('[name="tendered_amount"]').val(tender)
            //var change = parseFloat(tender) - (parseFloat(amount) - parseFloat(s_des))
            $('#change').val(parseFloat(change).toLocaleString('en-US'))
            $('#arrears').val(parseFloat(amount - s_des).toLocaleString('en-US'))
            $('[name="s_desc"]').val(parseFloat(s_des))
            //$('[name="client_id"]').val($('#client_id').val())
            $('[name="vehical_no"]').val($('#vehical_no').val())
            $('[name="dnic"]').val($('#dnic').val())
            $('[name="dname"]').val($('#dname').val())
            $('[name="arrears"]').val(parseFloat(amount - s_des))
            $('[name="t_profit"]').val(parseFloat(tt_profit))
            $('[name="tendered_amount"]').val(parseFloat(amount - s_des))

        })
        // $('#tender').focusout(function() {
        //     if ($(this).val() <= 0)
        //         $(this).val(0);

        // })
        $('#save_trans').click(function() {
            $('#uni_modal').modal('hide')
            $('#transaction-form').submit()
        })
    })
</script>