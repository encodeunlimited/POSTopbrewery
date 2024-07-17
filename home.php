<h3>TOP Brewery Point Of Sale System</h3>
<hr>
<div class="col-12">
    <div class="row gx-4 row-cols-md-4">
        <div class="card-deck">
            <div class="card text-white bg-dark mb-4">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-th-list fs-3 text-primary"></span>&nbsp&nbsp&nbsp&nbsp
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Catrgories</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $category = $conn->query("SELECT count(category_id) as `count` FROM `category_list` ")->fetchArray()['count'];
                                echo $category > 0 ? number_format($category) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-deck">
            <div class="card text-white bg-dark mb-4">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-truck-loading fs-3"></span>&nbsp&nbsp&nbsp&nbsp
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Suppliers</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $supplier = $conn->query("SELECT count(supplier_id) as `count` FROM `supplier_list` ")->fetchArray()['count'];
                                echo $supplier > 0 ? number_format($supplier) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-deck">
            <div class="card text-white bg-dark mb-4">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-book fs-3 text-warning"></span>&nbsp&nbsp&nbsp&nbsp
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Products</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php
                                $product = $conn->query("SELECT count(product_id) as `count` FROM `product_list` ")->fetchArray()['count'];
                                $product = $conn->query("SELECT count(product_id) as `count` FROM `product_list` ")->fetchArray()['count'];
                                echo $product > 0 ? number_format($product) : 0;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-deck">
            <div class="card text-white bg-dark mb-4">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-file-alt fs-3 text-info"></span>&nbsp&nbsp&nbsp&nbsp
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Total Stocks</b></div>
                            <div class="fs-6 text-end fw-bold">

                                <?php
                                //$stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` s inner join 'product_list' p on s.product_id = p.product_id where p.category_id NOT IN ( 209) ")->fetchArray()['total'];
                                $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` s inner join 'product_list' p on s.product_id = p.product_id where p.category_id NOT IN (144,209) ")->fetchArray()['total'];
                                $stock_out = $conn->query("SELECT sum(quantity) as `total` FROM `transaction_items` s inner join 'product_list' p on s.product_id = p.product_id where p.category_id NOT IN (144,209)  ")->fetchArray()['total'];
                                $stock_in = $stock_in > 0 ? $stock_in : 0;
                                $stock_out = $stock_out > 0 ? $stock_out : 0;
                                $qty = $stock_in - $stock_out;
                                $qty = $qty > 0 ? $qty : 0;
                                echo $qty > 0 ? number_format($qty) : 0;

                                /* $stock = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where strftime('%s',`expiry_date` || '23:59:59') >= strftime('%s',CURRENT_TIMESTAMP) ")->fetchArray()['total'];
                                echo $stock > 0 ? number_format($stock) : 0 ;*/
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="print_data">Print Data</button>
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="print_data_s">Special Print Data</button>
            <h3>Stock Available</h3>
            <hr>
            <table class="table table-striped table-hover" id="inventory">
                <colgroup>
                    <col width="25%">
                    <col width="25%">
                    <col width="25%">
                    <col width="25%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="py-0 px-1">Category</th>
                        <th class="py-0 px-1">Product Code</th>
                        <th class="py-0 px-1">Product Name</th>
                        <th class="py-0 px-1">Available Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function getMainItemDetails($product_id)
                    {
                        if (strpos($product_id, '*') !== false) {
                            list($main_id, $multiplier) = explode('*', $product_id);
                        } else {
                            $main_id = $product_id;
                            $multiplier = 1; // Default multiplier if no asterisk is found
                        }
                        return [
                            'main_id' => $main_id,
                            'multiplier' => intval($multiplier)
                        ];
                    }

                    $sql = "SELECT p.*,c.name as cname FROM `product_list` p inner join `category_list` c on p.category_id = c.category_id where p.status = 1 AND p.category_id NOT IN (209) order by `name` asc";
                    $qry = $conn->query($sql);
                    while ($row = $qry->fetchArray()) {
                        $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where strftime('%s',`expiry_date` || '23:59:59') >= strftime('%s',CURRENT_TIMESTAMP) and product_id = '{$row['product_id']}' ")->fetchArray()['total'];

                        $stock_out = 0;

                        // Calculate stock out
                        $sql1 = "SELECT c.*, p.product_code as pcode FROM `product_list` p INNER JOIN `transaction_items` c ON p.product_id = c.product_id WHERE p.status = 1 ORDER BY `name` ASC";
                        $qry1 = $conn->query($sql1);

                        while ($row1 = $qry1->fetchArray()) {
                            $details = getMainItemDetails($row1['pcode']);

                            if ($details['main_id'] == $row['product_code']) {
                                if ($details['multiplier'] > 1) {
                                    $stock_out += $details['multiplier'] * $row1['quantity'];
                                } else {
                                    $stock_out += $row1['quantity'];
                                }
                            }
                        }

                        $stock_in = $stock_in > 0 ? $stock_in : 0;
                        $stock_out = $stock_out > 0 ? $stock_out : 0;
                        $qty = $stock_in - $stock_out;
                        $qty = $qty > 0 ? $qty : 0;

                        $details = getMainItemDetails($row['product_code']);

                        if ($details['multiplier'] > 1) {
                        } else {

                    ?>
                            <tr class="<?php echo $qty < 50 ? "bg-danger bg-opacity-25" : '' ?>">
                                <td class="td py-0 px-1"><?php echo $row['cname'] ?></td>
                                <td class="td py-0 px-1"><?php echo $row['product_code'] ?></td>
                                <td class="td py-0 px-1"><?php echo $row['name'] ?></td>
                                <td class="td py-0 px-1 text-end">
                                    <?php if ($_SESSION['type'] == 1) : ?>
                                        <?php echo $qty < 50 ? "<a href='javascript:void(0)' class='restock me-1' data-pid = '" . $row['product_id'] . "' data-name = '" . $row['product_code'] . ' - ' . $row['name'] . "'> Restock</a>" : '' ?>
                                    <?php endif; ?>
                                    <?php echo $qty ?></td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('.restock').click(function() {
            uni_modal('Add New Stock for <span class="text-primary">' + $(this).attr('data-name') + "</span>", "manage_stock.php?pid=" + $(this).attr('data-pid'))
        })
        $('table#inventory').dataTable()
    })
</script>

<script>
    $(document).ready(function() {
        $('#print_data').click(function() {
            // Open a new window with print_inventory.php
            var printWindow = window.open('print_inventory.php', '_blank');
            // printWindow.focus();
            printWindow.print();
        });
        $('#print_data_s').click(function() {
            // Open a new window with print_inventory.php
            var printWindow = window.open('s_print_inventory.php', '_blank');
            // printWindow.focus();
            printWindow.print();
        });
    });
</script>