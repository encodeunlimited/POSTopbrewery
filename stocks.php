<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Stock List</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button>

        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="15%">
                <col width="15%">
                <col width="25%">
                <col width="10%">
                <col width="15%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date Added</th>
                    <th class="text-center p-0">Supplier</th>
                    <th class="text-center p-0">Product</th>
                    <th class="text-center p-0">Quantity</th>
                    <th class="text-center p-0">Expiry</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT s.*,ss.name as sname,p.name as pname,p.product_code FROM `stock_list` s inner join `product_list` p on s.product_id = p.product_id inner join `supplier_list` ss on s.supplier_id = ss.supplier_id AND p.category_id NOT IN (209) order by strftime('%s',date_added) desc";
                $qry = $conn->query($sql);
                // $data = array();
                $i = 1;
                while ($row = $qry->fetchArray()) :
                    // $data[] = $row;
                ?>
                    <tr class="<?php echo strtotime(date("Y-m-d")) > strtotime($row['expiry_date']) ? 'bg-danger bg-opacity-50' : '' ?>">
                        <td class="text-center p-0"><?php echo $i++; ?></td>
                        <td class="py-0 px-1"><?php echo date("Y-m-d", strtotime($row['date_added'])) ?></td>
                        <td class="py-0 px-1"><?php echo $row['sname'] ?></td>
                        <td class="py-0 px-1">
                            <div class="fs-6 fw-bold truncate-1" title="<?php echo $row['product_code'] ?>"><?php echo $row['product_code'] ?></div>
                            <div class="fs-6 fw-light truncate-1" title="<?php echo $row['pname'] ?>"><?php echo $row['pname'] ?></div>
                        </td>
                        <td class="py-0 px-1 text-end"><?php echo number_format($row['quantity']) ?></td>
                        <td class="py-0 px-1 text-center"><?php echo date("Y-m-d", strtotime($row['expiry_date'])) ?></td>
                        <td class="text-center py-0 px-1">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <!-- <li><a class="dropdown-item view_data" data-id = '<?php echo $row['stock_id'] ?>' href="javascript:void(0)">View Details</a></li> -->
                                    <li><a class="dropdown-item edit_data" data-id='<?php echo $row['stock_id'] ?>' href="javascript:void(0)">Edit</a></li>
                                    <li><a class="dropdown-item delete_data" data-id='<?php echo $row['stock_id'] ?>' data-name='<?php echo $row['product_code'] . " - " . $row['pname'] ?>' href="javascript:void(0)">Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endwhile;
                // header('Content-Type: application/json');
                // json_encode($data);
                ?>

            </tbody>
        </table>
    </div>
</div>


<script>
    $(function() {
        $('#create_new').click(function() {
            uni_modal('Add New Stock', "manage_stock.php")
        })
        $('.edit_data').click(function() {
            uni_modal('Edit Stock Details', "manage_stock.php?id=" + $(this).attr('data-id'))
        })
        $('.view_data').click(function() {
            uni_modal('Stock Details', "view_stock.php?id=" + $(this).attr('data-id'), '')
        })
        $('.delete_data').click(function() {
            _conf("Are you sure to delete <b>" + $(this).attr('data-name') + "</b> from Stock List?", 'delete_data', [$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle')
        $('table').dataTable({
            columnDefs: [{
                orderable: false,
                targets: 3
            }]
        })
    })

    function delete_data($id) {
        $('#confirm_modal button').attr('disabled', true)
        $.ajax({
            url: './Actions.php?a=delete_stock',
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

    // document.getElementById("print_data").addEventListener("click", function() {
    //         fetch('stocks.php')
    //             .then(response => response.json())
    //             .then(data => {
    //                 // Create a new window for printing
    //                 var printWindow = window.open('', '_blank');
    //                 printWindow.document.write('<html><head><title>Print Data</title></head><body>');

    //                 // Write data to the new window
    //                 data.forEach(row => {
    //                     printWindow.document.write('<p>Supplier Name: ' + row.sname + '</p>');
    //                     printWindow.document.write('<p>Product Name: ' + row.pname + '</p>');
    //                     printWindow.document.write('<p>Product Code: ' + row.product_code + '</p>');
    //                     // Add more fields as needed
    //                     printWindow.document.write('<hr>');
    //                 });

    //                 printWindow.document.write('</body></html>');

    //                 // Close the document and initiate printing
    //                 printWindow.document.close();
    //                 printWindow.print();
    //             })
    //             .catch(error => console.error('Error fetching data: ', error));
    //     });

    $(document).ready(function() {
        $('#print_data').click(function() {
            var printWindow = window.open('print_inventory.php', '_blank');
            printWindow.focus();
        });
    });
</script>