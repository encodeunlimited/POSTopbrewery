<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="image/favicon.ico">
    <link rel="stylesheet" href="./Font-Awesome-master/css/all.min.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./Font-Awesome-master/js/all.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        :root {
            --bs-success-rgb: 71, 222, 152 !important;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        main {
            height: 100%;
            display: flex;
            flex-flow: column;
        }

        #page-container {
            flex: 1 1 auto;
            overflow: auto;
        }

        #topNavBar {
            flex: 0 1 auto;
        }

        .thumbnail-img {
            width: 50px;
            height: 50px;
            margin: 2px
        }

        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }

        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }

        @media (max-width:720px) {

            .modal-dialog.large {
                width: 100% !important;
                max-width: unset;
            }

            .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }

        }

        .display-select-image {
            width: 60px;
            height: 60px;
            margin: 2px
        }

        img.display-image {
            width: 100%;
            height: 45vh;
            object-fit: cover;
            background: black;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .img-del-btn {
            right: 2px;
            top: -3px;
        }

        .img-del-btn>.btn {
            font-size: 10px;
            padding: 0px 2px !important;
        }

        .body-bg {
            /* background-color: bisque; */
            background-color: #f1f1f1;
        }
    </style>
</head>

<body class="body-bg">

    <?php
    session_start();
    require_once("DBConnection.php");


    // Retrieve parameters
    $dfrom = isset($_GET['date_from']) ? str_replace('T', ' ', $_GET['date_from']) : date("Y-m-d H:i:s");
    $dto = isset($_GET['date_to']) ? str_replace('T', ' ', $_GET['date_to']) : date("Y-m-d H:i:s");
    $sql = "SELECT t.*, u.fullname 
    FROM transaction_list t 
    INNER JOIN user_list u ON t.user_id = u.user_id 
    WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) 
    BETWEEN '{$dfrom}' AND '{$dto}' 
    ORDER BY strftime('%s', t.date_added) DESC;";
    $qry = $conn->query($sql);

    $tot_item = 0;
    $product_counts = [];  // Array to hold product-wise count with names
    $category_counts = []; // Array to hold category-wise count with names
    $payment_type_counts = []; // Array to hold payment type-wise item counts and total amounts
    $t_discount_count = 0;
    $t_discount_sum = 0.0;
    $s_desc_count = 0;
    $s_desc_sum = 0.0;
    $gross_total = 0.0;  // To hold the sum of all transaction totals
    $gross_item_count = 0;  // To hold the total count of items across all transactions
    $gross_profit_sum = 0.0;  // To hold the sum of all t_profit values

    $refund_total = 0.0;  // To hold the sum of all refund totals
    $refund_item_count = 0;  // To hold the count of refunded items

    while ($row = $qry->fetchArray()) {
        // Get total items for the transaction
        $items = $conn->query("SELECT SUM(quantity) as `count` 
                       FROM `transaction_items` 
                       WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['count'];
        $tot_item += $items;
        $gross_item_count += $items;

        // Aggregate item count and total amount based on payment type
        $payment_type = $row['customer'];
        $transaction_total = $conn->query("SELECT SUM(price * quantity) as `total` 
                                   FROM `transaction_items` 
                                   WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['total'];
        $gross_total += $transaction_total;

        if (!isset($payment_type_counts[$payment_type])) {
            $payment_type_counts[$payment_type] = [
                'item_count' => 0,
                'total_amount' => 0.0
            ];
        }
        $payment_type_counts[$payment_type]['item_count'] += $items;
        $payment_type_counts[$payment_type]['total_amount'] += $transaction_total;

        // Check for transaction discount (t_discount)
        if ($row['t_discount'] > 0) {
            $t_discount_count++;
            $t_discount_sum += $row['t_discount'];
        }

        // Check for special discount (s_desc)
        if ($row['s_desc'] > 0) {
            $s_desc_count++;
            $s_desc_sum += $row['s_desc'];
        }

        // Aggregate gross profit
        $gross_profit_sum += $row['t_profit'];

        // Get product-wise count for the transaction with product names
        $product_query = $conn->query("SELECT pl.product_id, pl.name as product_name, SUM(ti.quantity) as `count` 
                               FROM `transaction_items` ti 
                               INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
                               WHERE ti.transaction_id = '{$row['transaction_id']}' 
                               GROUP BY pl.product_id, pl.name");

        while ($product_row = $product_query->fetchArray()) {
            $product_id = $product_row['product_id'];
            $product_name = $product_row['product_name'];
            $product_count = $product_row['count'];

            if (!isset($product_counts[$product_id])) {
                $product_counts[$product_id] = [
                    'name' => $product_name,
                    'count' => 0
                ];
            }
            $product_counts[$product_id]['count'] += $product_count;
        }

        // Get category-wise count for the transaction with category names
        $category_query = $conn->query("SELECT cl.category_id, cl.name as category_name, SUM(ti.quantity) as `count`, SUM(ti.price * ti.quantity) as `ctot_price`
                                FROM `transaction_items` ti 
                                INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
                                INNER JOIN `category_list` cl ON pl.category_id = cl.category_id 
                                WHERE ti.transaction_id = '{$row['transaction_id']}' 
                                GROUP BY cl.category_id, cl.name");

        while ($category_row = $category_query->fetchArray()) {
            $category_id = $category_row['category_id'];
            $category_name = $category_row['category_name'];
            $category_count = $category_row['count'];
            $category_sum = $category_row['ctot_price'];

            if (!isset($category_counts[$category_id])) {
                $category_counts[$category_id] = [
                    'name' => $category_name,
                    'count' => 0,
                    'ctot_price' => 0.0
                ];
            }
            $category_counts[$category_id]['count'] += $category_count;
            $category_counts[$category_id]['ctot_price'] += $category_sum;
        }
    }

    // echo "<strong>Product-wise counts:</strong><br>";
    // foreach ($product_counts as $product_id => $product_info) {
    //     echo "Product: {$product_info['name']} (ID: $product_id) - Count: {$product_info['count']}<br>";
    // }

    // Get refund data from refund_transaction_list and refund_transaction_items
    $refund_sql = "SELECT r.*, u.fullname 
               FROM refund_transaction_list r 
               INNER JOIN user_list u ON r.user_id = u.user_id 
               WHERE strftime('%Y-%m-%d %H:%M:%S', r.date_added) 
               BETWEEN '{$dfrom}' AND '{$dto}' 
               ORDER BY strftime('%s', r.date_added) DESC;";
    $refund_qry = $conn->query($refund_sql);

    while ($refund_row = $refund_qry->fetchArray()) {
        $refund_items = $conn->query("SELECT SUM(quantity) as `count`, SUM(price * quantity) as `total`
                                  FROM `refund_transaction_items`
                                  WHERE transaction_id = '{$refund_row['transaction_id']}'")->fetchArray();
        $refund_item_count += $refund_items['count'];
        $refund_total += $refund_items['total'];
    }

    

    ?>

    <style>
        #uni_modal .modal-footer {
            display: none !important;
        }

        .grid-container {
            width: 100%;
            border-collapse: collapse;
        }

        .cell {
            padding: 10px;
            text-align: center;
        }

        tr:first-child .cell {
            text-align: center;
        }

        tr:last-child .cell {
            text-align: left;
        }
    </style>
    <?php
    $dateFromObj = new DateTime($dfrom);
    $dfromFormatted = $dateFromObj->format('d-m-Y H:i:s');

    // Convert $dto to "d-m-Y H:i:s"
    $dateToObj = new DateTime($dto);
    $dtoFormatted = $dateToObj->format('d-m-Y H:i:s');
    ?>
    <div class="container-fluid">
        <div id="outprint_receipt">
            <div class="text-center fs-6 fs-bold">
                <span><img src="image\logoabc.png" width='100' height='100'></span><br>
                <span>
                    <h6 class="fw-bold">201303306948 (002282392-A)</h6>
                </span>
                <small>Tel: 03-42856778 | 019-6000671</small><br>
                <small>E-mail : topbrewery@gmail.com</small><br>
                <small>No. 24, jalan bunga tanjung 8a, taman muda, 68000 ampang, selangor.</small><br>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr class="text-dark">
                        <th class="py-0 px-1" style="font-size: small; text-align: center;">

                        </th>
                    </tr>
                    <tr>
                        <td style="font-size: small;  text-align: center;">
                            Period : <?php echo $dfromFormatted . " | " . $dtoFormatted ?><br>
                        </td>
                    </tr>
                </thead>
            </table>
            <!-- <?php echo "<strong>PRICE WISE SUMMERY</strong><br>"; ?>
            <table class="table table-striped">
                <colgroup>
                    <col width="70%">
                    <col width="10%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr class="text-dark">
                        <th class="py-0 px-1">Item</th>
                        <th class="py-0 px-1">Qty</th>
                        <th class="py-0 px-1">Amount</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    foreach ($payment_type_counts as $payment_type => $data) {
                    ?>
                        <tr>
                            <td class="px-1 py-0 align-middle"><?php echo $payment_type ?></td>
                            <td class="px-1 py-0 align-middle"><?php echo $data['item_count'] ?></td>
                            <td class="px-1 py-0 align-middle"><?php echo number_format($data['total_amount'], 2) ?></td>

                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th class="px-1 py-0">Gross Total</th>
                        <th class="px-1 py-0 "><?php echo $gross_item_count ?></th>
                        <th class="px-1 py-0 "><?php echo number_format($gross_total, 2) ?></th>
                    </tr>
                    <tr>
                        <th class="px-1 py-0">Gross Profit</th>
                        <th class="px-1 py-0 "></th>
                        <th class="px-1 py-0 "><?php echo number_format($gross_profit_sum, 2) ?></th>
                    </tr>
                </tfoot>
            </table> -->
            <?php echo "<strong>CATEGORY WISE SUMMERY</strong><br>"; ?>
            <table class="table table-striped">
                <colgroup>
                    <col width="70%">
                    <col width="10%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr class="text-dark">
                        <th class="py-0 px-1">Item</th>
                        <th class="py-0 px-1">Qty</th>
                        <th class="py-0 px-1">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($category_counts as $category_id => $category_info) {
                    ?>
                        <tr>
                            <td class="px-1 py-0 align-middle"><?php echo $category_info['name'] . " | " . $category_id ?></td>
                            <td class="px-1 py-0 align-middle"><?php echo $category_info['count'] ?></td>
                            <td class="px-1 py-0 align-middle"><?php echo number_format($category_info['ctot_price'], 2) ?></td>

                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
                <?php $net_total = $gross_total - $t_discount_sum-$s_desc_sum; ?>
                <tfoot>
                    <tr>
                        <th class="px-1 py-0">Gross Total</th>
                        <th class="px-1 py-0 "><?php echo $gross_item_count ?></th>
                        <th class="px-1 py-0 "><?php echo number_format($gross_total, 2) ?></th>
                    </tr>
                    <tr>
                        <th class="px-1 py-0">Discount Total (-)</th>
                        <th class="px-1 py-0 "><?php echo ($t_discount_count + $s_desc_count + $refund_item_count) ?></th>
                        <th class="px-1 py-0 "><?php echo number_format($t_discount_sum + $s_desc_sum + $refund_total, 2) ?></th>
                    </tr>
                    <tr>
                        <th class="px-1 py-0">Net Total</th>
                        <th class="px-1 py-0 "><?php echo $gross_item_count ?></th>
                        <th class="px-1 py-0 "><?php echo number_format($net_total, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
            <?php echo "<strong>DISCOUNT & REFUND SUMMERY</strong><br>"; ?>
            <table class="table table-striped">
                <colgroup>
                    <col width="70%">
                    <col width="10%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr class="text-dark">
                        <th class="py-0 px-1">Item</th>
                        <th class="py-0 px-1">Qty</th>
                        <th class="py-0 px-1">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-1 py-0 align-middle"><?php echo "Item Discount" ?></td>
                        <td class="px-1 py-0 align-middle"><?php echo $t_discount_count ?></td>
                        <td class="px-1 py-0 align-middle"><?php echo number_format($t_discount_sum, 2) ?></td>

                    </tr>
                    <tr>
                        <td class="px-1 py-0 align-middle"><?php echo "Special Discount" ?></td>
                        <td class="px-1 py-0 align-middle"><?php echo $s_desc_count ?></td>
                        <td class="px-1 py-0 align-middle"><?php echo number_format($s_desc_sum, 2) ?></td>

                    </tr>
                    <tr>
                        <td class="px-1 py-0 align-middle"><?php echo "Refund" ?></td>
                        <td class="px-1 py-0 align-middle"><?php echo $refund_item_count ?></td>
                        <td class="px-1 py-0 align-middle"><?php echo number_format($refund_total, 2) ?></td>

                    </tr>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
            <div class="text-center">

                <span class="fw-bolder">Thank You!</span><br>
                <small class="fw-bold lh-1">Software By</small><br>
                <small class="fw-bold lh-1">EncodeUnlimited | 0706291709 | www.encodeunlimited.com</small><br>
            </div>
        </div>

    </div>

</body>

</html>