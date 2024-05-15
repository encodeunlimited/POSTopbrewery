<?php
// Assuming $dfrom and $dto are in the format "YYYY-MM-DD"
// Modify the format to "YYYY-MM-DD HH:mm:ss"

// $dfrom = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d H:i:s", strtotime(date("Y-m-d") . " -1 week"));

// Assuming $dfrom and $dto are in the format "YYYY-MM-DDTHH:mm:ss"
// Replace the "T" character with a space
$dfrom = isset($_GET['date_from']) ? str_replace('T', ' ', $_GET['date_from']) : date("Y-m-d H:i:s");
$dto = isset($_GET['date_to']) ? str_replace('T', ' ', $_GET['date_to']) : date("Y-m-d H:i:s");

$pay_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';
// echo $pay_method;

if ($pay_method == "all") {
    $pay_query = "";
    $pay_query1 = "";
} else {
    $pay_query = "AND t.customer = '{$pay_method}'";
    $pay_query1 = "AND customer = '{$pay_method}'";
}



// If you want to set the time part to "11:21:09" for both $dfrom and $dto
// $time_part = "11:21:09";
// $dfrom = date("Y-m-d", strtotime($dfrom)) . " " . $time_part;
// $dto = date("Y-m-d", strtotime($dto)) . " " . $time_part;
?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Sales Report</h3>
    </div>
    <div class="card-body">
        <h5>Filter</h5>
        <div class="row align-items-end">

            <div class="form-group col-md-2">
                <label for="date_from" class="control-label">Date From</label>
                <input type="datetime-local" name="date_from" id="date_from" value="<?php echo $dfrom ?>" class="form-control rounded-0">
            </div>
            <div class="form-group col-md-2">
                <label for="date_to" class="control-label">Date To</label>
                <input type="datetime-local" name="date_to" id="date_to" value="<?php echo $dto; ?>" class="form-control rounded-0">
            </div>
            <div class="form-group col-md-2">
                <label for="payment_method" class="control-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-control rounded-0">
                    <option value="all">All</option>
                    <option value="qr">QR</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="food_panda">Food Panda</option>
                </select>
            </div>
            <div class="form-group col-md-4 d-flex">
                <div class="col-auto">
                    <button class="btn btn-primary rounded-0" id="filter" type="button"><i class="fa fa-filter"></i> Filter</button>
                    <button class="btn btn-success rounded-0" id="print" type="button"><i class="fa fa-print"></i> Print</button>
                    <?php if ($_SESSION['type'] == 1) { ?>
                    <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="print_data">Print Data</button>
                    <?php }  ?>

                </div>
            </div>
        </div>
        <hr>
        <div class="clear-fix mb-2"></div>
        <div id="outprint">
            <table class="table table-hover table-striped table-bordered" id="print">
                <colgroup>
                    <col width="5%">
                    <col width="10%">
                    <col width="10%">
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <!-- <col width="15%"> -->
                    <col width="10%">
                    <!-- <col width="10%"> -->
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center p-0">#</th>
                        <th class="text-center p-0">Date</th>
                        <th class="text-center p-0">Receipt No</th>
                        <th class="text-center p-0">Items</th>
                        <th class="text-center p-0">Method</th>
                        <th class="text-center p-0">Total Amount</th>
                        <th class="text-center p-0">Total Profit</th>
                        <!-- <th class="text-center p-0">Amount in arrears</th> -->
                        <th class="text-center p-0">Processed By</th>
                        <!-- <th class="text-center p-0">Arrears Done</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_where = "";
                    if ($_SESSION['type'] != 1) {
                        $user_where = " and t.user_id = '{$_SESSION['user_id']}' ";
                        $user_c = " and user_id = '{$_SESSION['user_id']}' ";
                    }

                    $sql = "SELECT t.*, u.fullname FROM transaction_list t INNER JOIN user_list u ON t.user_id = u.user_id WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$user_where} {$pay_query} ORDER BY strftime('%s', t.date_added) DESC;";
                    $qry = $conn->query($sql);

                    // echo $sql;
                    $tot_item = 0;

                    $i = 1;
                    while ($row = $qry->fetchArray()) :
                        $items = $conn->query("SELECT sum(quantity) as `count` FROM `transaction_items` where transaction_id = '{$row['transaction_id']}'  ")->fetchArray()['count'];

                    ?>

                        <tr>
                            <td class="text-center p-0"><?php echo $i++; ?></td>
                            <td class="py-0 px-1"><?php echo date("Y-m-d H:m:s", strtotime($row['date_added'])) ?></td>
                            <td class="py-0 px-1"><a href="javascript:void(0)" class="view_data" data-id="<?php echo $row['transaction_id'] ?>"><?php echo $row['receipt_no'] ?></a></td>
                            <td class="py-0 px-1 text-end"><?php echo number_format($items) ?></td>
                            <td class="py-0 px-1 text-end"><?php echo $row['customer'] ?></td>
                            <td class="py-0 px-1 text-end"><?php echo number_format($row['total'], 2) ?></td>
                            <td class="py-0 px-1 text-end"><?php echo number_format($row['t_profit'], 2) ?></td>
                            <!-- <td class="py-0 px-1 text-end"><?php echo number_format($row['arrears'], 2) ?></td> -->
                            <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                            <!-- <td class="py-0 px-1">
                                <?php echo $row['arrears'] > 0 ? "<a href='javascript:void(0)' class='arrears me-1' data-tid = '" . $row['transaction_id'] . "' data-resno = '" . $row['receipt_no'] . "' data-arrears = '" . $row['arrears'] . "'> Arrears Done</a>" : '' ?>
                            </td> -->
                        </tr>
                    <?php

                        $tot_item += $items;

                    endwhile; ?>
                    <?php if (!$qry->fetchArray()) : ?>
                        <th colspan="6">
                            <center>No Transaction listed in selected date.</center>
                        </th>
                    <?php endif; ?>

                </tbody>

                <tr class="bg-dark bg-opacity-25 text-bold">
                    <td class="px-1 py-0 text-end" colspan='3'>
                        <H6>Total</H6>
                    </td>
                    <td class="px-1 py-0 text-end"><?php echo $tot_item ?>
                    </td>
                    <td></td>
                    <td class="px-1 py-0 text-end"><?php
                                                    if ($_SESSION['type'] == 1) {
                                                        $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$pay_query1}")->fetchArray()['total'];
                                                    } else {
                                                        $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$user_c} {$pay_query1}")->fetchArray()['total'];
                                                    }
                                                    echo number_format($total, 2);
                                                    ?>
                    </td>
                    <td class="px-1 py-0 text-end"><?php
                                                    if ($_SESSION['type'] == 1) {
                                                        $t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$pay_query1}")->fetchArray()['total_profit'];
                                                    } else {
                                                        $t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$user_c} {$pay_query1}")->fetchArray()['total_profit'];
                                                    }
                                                    echo number_format($t_profit, 2);
                                                    ?>
                    </td>
                    <!-- <td class="px-1 py-0 text-end"><?php
                                                        if ($_SESSION['type'] == 1) {
                                                            $arrears = $conn->query("SELECT sum(arrears) as `total_arrears` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$pay_query1}")->fetchArray()['total_arrears'];
                                                        } else {
                                                            $arrears = $conn->query("SELECT sum(arrears) as `total_arrears` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$user_c} {$pay_query1}")->fetchArray()['total_arrears'];
                                                        }
                                                        echo number_format($arrears, 2);
                                                        ?>
                    </td> -->
                </tr>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        //addnew
        $('.arrears').click(function() {
            // uni_modal('Arrears Done for <span class="text-primary">'+$(this).attr('data-resno')+" - Rs."+$(this).attr('data-arrears')+"</span>","manage_arrears.php?transaction_id="+$(this).attr('data-tid'))
            _conf("Are you sure to Done Arrears Payment Rs. <b>" + $(this).attr('data-arrears') + "</b> from Recipt no <b>" + $(this).attr('data-resno') + "</b> ?", 'done_data', [$(this).attr('data-tid')])
        })

        /////////////////

        $('.view_data').click(function() {
            uni_modal('Receipt', "view_receipt.php?view_only=true&id=" + $(this).attr('data-id'), '')
        })
        $('#filter').click(function() {
            location.href = "./?page=sales_report&date_from=" + $('#date_from').val() + "&date_to=" + $('#date_to').val() + "&payment_method=" + $('#payment_method').val();
        })

        $('table td,table th').addClass('align-middle')

        $('#print').click(function() {
            var h = $('head').clone()
            var p = $('#outprint').clone()
            var el = $('<div>')
            el.append(h)
            if ('<?php echo $dfrom ?>' == '<?php echo $dto ?>') {
                date_range = "<?php echo date('M d, Y', strtotime($dfrom)) ?>";
            } else {
                date_range = "<?php echo date('M d, Y H:m:s', strtotime($dfrom)) . ' - ' . date('M d, Y H:m:s', strtotime($dto)) ?>";
            }
            el.append("<div class='text-center lh-1 fw-bold'> Top Bevery Sales Report<br/>As of<br/>" + date_range + "</div><hr/>")
            p.find('a').addClass('text-decoration-none')
            el.append(p)
            var nw = window.open("", "", "width=900,height=900")
            nw.document.write(el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                }, 150);
            }, 200);
        })

        $('table').dataTable({
            columnDefs: [{
                orderable: false,
                targets: 3
            }]
        })
    })


    //new
    function done_data($id) {
        $('#confirm_modal button').attr('disabled', true)
        $.ajax({
            url: './Actions.php?a=done_arreas',
            method: 'POST',
            data: {
                id: $id
            },
            dataType: 'JSON',
            error: err => {
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled', false)
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload()
                } else {
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled', false)
                }
            }
        })
    }
    /////////////////////////////

    $(document).ready(function() {
        $('#print_data').click(function() {
            // Retrieve values of elements
            var date_from = $('#date_from').val(); // Assuming you have input fields with ids 'date_from' and 'date_to'
            var date_to = $('#date_to').val();
            var payment_method = $('#payment_method').val(); // Assuming you have a select element with id 'payment_method'

            // Construct URL with query parameters
            var url = 'process_data.php?date_from=' + encodeURIComponent(date_from) +
                '&date_to=' + encodeURIComponent(date_to) +
                '&payment_method=' + encodeURIComponent(payment_method);

            // Open a new window with the constructed URL
            var printWindow = window.open(url, '_blank');
            // printWindow.focus();
            printWindow.print();
        });
    });
    
</script>