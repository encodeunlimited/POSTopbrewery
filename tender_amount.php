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
        <label for="change" class="control-label fs-4 fw-bold">Change</label>
        <input type="text" id="change" class="form-control form-control-lg text-end" value="0" disabled>
    </div>
    <!-- <div class="form-group">
        <label for="customer" class="control-label fs-4 fw-bold">Customer Name</label>
        <input type="text" step="any" id="customer" class="form-control form-control-lg text-end" value="" >
    </div> -->
    <div class="form-group">
        <label for="customer" class="control-label fs-4 fw-bold">Payment Method</label>
        <!-- <select id="customer" class="form-control form-control-lg text-end">
            <option value="cash">Cash</option>
            <option value="qr">QR</option>
            <option value="online">Online</option>
            <option value="food_panda">Food Panda</option>
            <option value="card">Card</option>
        </select> -->
        <div id="payment-methods" class="form-group">
            <button type="button" class="btn btn-mm btn-danger" data-method="cash"><i class="fas fa-money-bill-alt"></i><br> Cash</button>
            <button type="button" class="btn btn-mm btn-primary" data-method="qr"><i class="fas fa-qrcode"></i><br> QR</button>
            <button type="button" class="btn btn-mm btn-primary" data-method="online"><i class="fas fa-globe"></i><br> Online</button>
            <button type="button" class="btn btn-mm btn-primary" data-method="food_panda"><i class="fas fa-pizza-slice"></i><br> Food Panda</button>
            <button type="button" class="btn btn-mm btn-primary" data-method="card"><i class="fas fa-credit-card"></i><br> Card</button>
        </div>


        <input type="hidden" id="customer" name="customer" value="">

    </div>

    <div class="form-group">
        <!-- <label for="vehical_no" class="control-label fs-4 fw-bold">Vehical No</label> -->
        <input type="hidden" step="any" id="vehical_no" class="form-control form-control-lg text-end" value="">
    </div>
    <div class="form-group">
        <input type="hidden" id="discount" class="form-control form-control-lg text-end" value="100" disabled>
    </div>
    <div class="form-group">
        <!-- <label for="arrears" class="control-label fs-4 fw-bold">Amount in arrears</label> -->
        <input type="hidden" step="any" id="arrears" class="form-control form-control-lg text-end" value="0">
    </div>
    <div class="form-group">
        <label for="tender" class="control-label fs-4 fw-bold">Tendered Amount</label>
        <input type="number" step="any" id="tender" class="form-control form-control-lg text-end" value="0" disabled>
    </div>
    <div class="w-100 d-flex justify-content-end mt-2">
        <button class="btn btn-sm btn-primary me-2 rounded-0" type="button" id="save_trans">Save</button>
        <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
    </div>
</div>
<script>
    // $(function() {

    //     $('#uni_modal').on('shown.bs.modal', function() {
    //         if ($(this).find('#tender').length > 0)
    //             $('#tender').trigger('focus').select();

    //     })
    //     $('#tender').on('keydown', function(e) {
    //         if (e.which == 13) {
    //             e.preventDefault()
    //             $('#save_trans').trigger('click')
    //         }
    //     })
    //     $('#tender').on('keypress input', function() {
    //         var tender = $(this).val() > 0 ? $(this).val() : 0;
    //         var amount = $('#amount').val().replace(/,/gi, "")
    //         var ts_profit = $('#tprofit').val()
    //         var s_des = $('#s_des').val().replace(/,/gi, "")
    //         var tt_profit = ts_profit - s_des
    //         //var arrears = $('#arrears').val().replace(/,/gi,"")
    //         // var customer = $('#customer').val()
    //         // var vehical_no = $('#vehical_no').val()

    //         $('[name="tendered_amount"]').val(tender)
    //         var change = parseFloat(tender) - (parseFloat(amount) - parseFloat(s_des))
    //         $('#change').val(parseFloat(change).toLocaleString('en-US'))
    //         $('[name="change"]').val(parseFloat(change))
    //         $('[name="s_desc"]').val(parseFloat(s_des))
    //         $('[name="customer"]').val($('#customer').val())
    //         $('[name="vehical_no"]').val($('#vehical_no').val())
    //         $('[name="arrears"]').val(parseFloat($('#arrears').val()))
    //         $('[name="t_profit"]').val(parseFloat(tt_profit))


    //     })
    //     $('#tender').focusout(function() {
    //         if ($(this).val() <= 0)
    //             $(this).val(0);
    //     })
    //     $('#save_trans').click(function() {
    //         $('#change').removeClass('border-danger')
    //         if ($('[name="change"]').val() < 0) {
    //             $('#change').addClass('border-danger')
    //         } else if ($('[name="tendered_amount"]').val() <= 0) {
    //             $('#tender').trigger('focus')
    //         } else {
    //             $('#uni_modal').modal('hide')
    //             $('#transaction-form').submit()
    //         }
    //     })
    // })

    $(document).ready(function() {

        $('#payment-methods button').click(function() {
            var method = $(this).data('method');
            if (method === 'cash') {
                $('#tender').prop('disabled', false);
                // $('#tender').focus();
                $('#tender').on('focus', function() {
                    // Reset form fields
                    $('#tender').val('');
                });
            } else {

                $('#tender').prop('disabled', true);
                $('#save_trans').focus();
            }
        });
        // $('[data-method="cash"]').click();
    });

    $(function() {
        $('#uni_modal').on('shown.bs.modal', function() {
            if ($('#tender').length > 0)
                $('#tender').trigger('focus').select();
        });

        // Handle enter key press on #tender
        $('#tender').on('keydown', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#save_trans').trigger('click');
            }
        });

        $('#s_des').on('input', function() {

            var s_des = $(this).val() > 0 ? $(this).val() : 0;
            var amount = $('#amount').val().replace(/,/gi, "");
            var ts_profit = $('#tprofit').val();
            var tt_profit = parseFloat(ts_profit) - parseFloat(s_des);
            var change = 0;
            $('#change').val(0);

            // Update form fields
            $('#change').val(parseFloat(change).toLocaleString('en-US'));
            $('[name="change"]').val(parseFloat(change));
            $('[name="s_desc"]').val(parseFloat(s_des));
            $('[name="t_profit"]').val(parseFloat(tt_profit));
            $('[name="total"]').val(parseFloat(amount-s_des));
            updateForm();
            // $('#save_trans').focus();
        });

        // Handle input change on #tender
        $('#tender').on('input', function() {

            var tender = $(this).val() > 0 ? $(this).val() : 0;
            var amount = $('#amount').val().replace(/,/gi, "");
            var ts_profit = $('#tprofit').val();
            var s_des = $('#s_des').val().replace(/,/gi, "");
            var tt_profit = parseFloat(ts_profit) - parseFloat(s_des);

            // Update form fields
            $('[name="tendered_amount"]').val(tender);
            var change = parseFloat(tender) - (parseFloat(amount) - parseFloat(s_des));
            $('#change').val(parseFloat(change).toLocaleString('en-US'));
            $('[name="change"]').val(parseFloat(change));
            $('[name="s_desc"]').val(parseFloat(s_des));
            $('[name="t_profit"]').val(parseFloat(tt_profit));
            $('[name="total"]').val(parseFloat(amount-s_des));
            updateForm();
            // $('#save_trans').focus();
        });

        // Focus out event on #tender
        $('#tender').focusout(function() {
            if ($(this).val() <= 0)
                $(this).val(0);
        });

        // Click event on payment method buttons
        $('#payment-methods button').click(function() {
            var paymentMethod = $(this).data('method');
            $('#customer').val(paymentMethod);
            updateForm();
        });

        // Click event on #save_trans button
        $('#save_trans').click(function() {
            $('#change').removeClass('border-danger');
            if ($('[name="change"]').val() < 0) {
                $('#change').addClass('border-danger');
            } else if ($('[name="tendered_amount"]').val() < 0) {
                $('#tender').trigger('focus');
            } else {
                $('#uni_modal').modal('hide');
                $('#transaction-form').submit();
            }
        });

        // Function to update form values
        function updateForm() {
            $('[name="customer"]').val($('#customer').val());
            $('[name="vehical_no"]').val($('#vehical_no').val());
            $('[name="arrears"]').val(parseFloat($('#arrears').val()));
        }
    });
</script>