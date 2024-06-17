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
            background-color: bisque;
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
    $pay_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : 'all';

    // echo $dfrom ." ".$dto." ".$pay_method;

    // Build pay_query based on payment method
    if ($pay_method == "all") {
        // $pay_query = "";
        $pay_query1 = "";
    } else {
        // $pay_query = "AND t.customer = '{$pay_method}'";
        $pay_query1 = "AND customer = '{$pay_method}'";
    }

    // Retrieve data from the GET request
    // $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    // $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    // $payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';

    // Now you can use $date_from, $date_to, and $payment_method as needed
    // For example:
    echo "<H2>Top Brewery</H2>";
    echo "<small>Tel: +60196000671 | 0342856778</small>";
    echo "<br>";
    echo "<small>E-mail: topbrewery@gmail.com</small>";
    echo "<br>";
    echo "<small>No24. JLN BUNGA TANJUNG 8A</small>";
    echo "<br>";
    echo "<small>TMN MUDA 56100 SELANGOR</small>";
    echo "<br>";

    // echo "<H2>OUTLET - 04</H2>";
    // echo "<br>";
    echo "<hr>";
    echo "Date From: " . $dfrom . "<br>";
    echo "Date To: " . $dto . "<br>";
    // echo "Payment Method: " . $pay_method . "<br>";

    echo "<hr>";
    // Execute queries
    // if ($_SESSION['type'] == 1) {
    //     $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$pay_query1}")->fetchArray()['total'];
    // } else {
    //     $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$user_c} {$pay_query1}")->fetchArray()['total'];
    // }

    $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' AND customer = 'cash'")->fetchArray()['total'];
    echo "Cash Total: ";
    echo number_format($total, 2);
    echo "</br>";


    $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' AND customer = 'qr'")->fetchArray()['total'];
    echo "QR Total: ";
    echo number_format($total, 2);
    echo "</br>";

    $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' AND customer = 'card'")->fetchArray()['total'];
    echo "Card Total: ";
    echo number_format($total, 2);
    echo "</br>";

    $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' AND customer = 'food_panda'")->fetchArray()['total'];
    echo "Food Panda Total: ";
    echo number_format($total, 2);
    echo "</br>";
    echo "</br>";

    $total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}'")->fetchArray()['total'];
    echo "Gross Total: ";
    echo number_format($total, 2);
    echo "</br>";

    // AND customer = 'all'

    // if ($_SESSION['type'] == 1) {
    //     $t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$pay_query1}")->fetchArray()['total_profit'];
    // } else {
    //     $t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' {$user_c} {$pay_query1}")->fetchArray()['total_profit'];
    // }
    $t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}' ")->fetchArray()['total_profit'];
    echo "Total Profit: ";
    echo number_format($t_profit, 2);

    echo "</br>";
    echo "<hr>";


    // $sql = "SELECT t.*, u.fullname FROM transaction_list t INNER JOIN user_list u ON t.user_id = u.user_id WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) BETWEEN '{$dfrom}' AND '{$dto}' ORDER BY strftime('%s', t.date_added) DESC;";
    // $qry = $conn->query($sql);

    // // echo $sql;
    // $tot_item = 0;

    // $i = 1;
    // while ($row = $qry->fetchArray()) :
    //     $items = $conn->query("SELECT sum(quantity) as `count` FROM `transaction_items` where transaction_id = '{$row['transaction_id']}'  ")->fetchArray()['count'];
    //     $tot_item += $items;
    // endwhile;


    // $sql = "SELECT t.*, u.fullname 
    //         FROM transaction_list t 
    //         INNER JOIN user_list u ON t.user_id = u.user_id 
    //         WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) 
    //         BETWEEN '{$dfrom}' AND '{$dto}' 
    //         ORDER BY strftime('%s', t.date_added) DESC;";
    // $qry = $conn->query($sql);

    // $tot_item = 0;
    // $product_counts = [];  // Array to hold product-wise count with names
    // $category_counts = []; // Array to hold category-wise count with names

    // while ($row = $qry->fetchArray()) {
    //     // Get total items for the transaction
    //     $items = $conn->query("SELECT SUM(quantity) as `count` 
    //                            FROM `transaction_items` 
    //                            WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['count'];
    //     $tot_item += $items;

    //     // Get product-wise count for the transaction with product names
    //     $product_query = $conn->query("SELECT pl.product_id, pl.name as product_name, SUM(ti.quantity) as `count` 
    //                                    FROM `transaction_items` ti 
    //                                    INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
    //                                    WHERE ti.transaction_id = '{$row['transaction_id']}' 
    //                                    GROUP BY pl.product_id, pl.name");

    //     while ($product_row = $product_query->fetchArray()) {
    //         $product_id = $product_row['product_id'];
    //         $product_name = $product_row['product_name'];
    //         $product_count = $product_row['count'];

    //         if (!isset($product_counts[$product_id])) {
    //             $product_counts[$product_id] = [
    //                 'name' => $product_name,
    //                 'count' => 0
    //             ];
    //         }
    //         $product_counts[$product_id]['count'] += $product_count;
    //     }

    //     // Get category-wise count for the transaction with category names
    //     $category_query = $conn->query("SELECT cl.category_id, cl.name as category_name, SUM(ti.quantity) as `count`, SUM(ti.price * ti.quantity) as `ctot_price`
    //                                 FROM `transaction_items` ti 
    //                                 INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
    //                                 INNER JOIN `category_list` cl ON pl.category_id = cl.category_id 
    //                                 WHERE ti.transaction_id = '{$row['transaction_id']}' 
    //                                 GROUP BY cl.category_id, cl.name");

    //     while ($category_row = $category_query->fetchArray()) {
    //         $category_id = $category_row['category_id'];
    //         $category_name = $category_row['category_name'];
    //         $category_count = $category_row['count'];
    //         $category_sum = $category_row['ctot_price'];

    //         if (!isset($category_counts[$category_id])) {
    //             $category_counts[$category_id] = [
    //                 'name' => $category_name,
    //                 'count' => 0,
    //                 'ctot_price' => 0.0
    //             ];
    //         }
    //         $category_counts[$category_id]['count'] += $category_count;
    //         $category_counts[$category_id]['ctot_price'] += $category_sum;
    //     }
    // }

    $sql = "SELECT t.*, u.fullname 
        FROM transaction_list t 
        INNER JOIN user_list u ON t.user_id = u.user_id 
        WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) 
        BETWEEN '{$dfrom}' AND '{$dto}' 
        ORDER BY strftime('%s', t.date_added) DESC;";
    $qry = $conn->query($sql);

    // $tot_item = 0;
    // $product_counts = [];  // Array to hold product-wise count with names
    // $category_counts = []; // Array to hold category-wise count with names
    // $payment_type_counts = []; // Array to hold payment type-wise item counts

    // while ($row = $qry->fetchArray()) {
    //     // Get total items for the transaction
    //     $items = $conn->query("SELECT SUM(quantity) as `count` 
    //                        FROM `transaction_items` 
    //                        WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['count'];
    //     $tot_item += $items;

    //     // Aggregate item count based on payment type
    //     $payment_type = $row['customer'];
    //     if (!isset($payment_type_counts[$payment_type])) {
    //         $payment_type_counts[$payment_type] = 0;
    //     }
    //     $payment_type_counts[$payment_type] += $items;

    //     // Get product-wise count for the transaction with product names
    //     $product_query = $conn->query("SELECT pl.product_id, pl.name as product_name, SUM(ti.quantity) as `count` 
    //                                FROM `transaction_items` ti 
    //                                INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
    //                                WHERE ti.transaction_id = '{$row['transaction_id']}' 
    //                                GROUP BY pl.product_id, pl.name");

    //     while ($product_row = $product_query->fetchArray()) {
    //         $product_id = $product_row['product_id'];
    //         $product_name = $product_row['product_name'];
    //         $product_count = $product_row['count'];

    //         if (!isset($product_counts[$product_id])) {
    //             $product_counts[$product_id] = [
    //                 'name' => $product_name,
    //                 'count' => 0
    //             ];
    //         }
    //         $product_counts[$product_id]['count'] += $product_count;
    //     }

    //     // Get category-wise count for the transaction with category names
    //     $category_query = $conn->query("SELECT cl.category_id, cl.name as category_name, SUM(ti.quantity) as `count`, SUM(ti.price * ti.quantity) as `ctot_price`
    //                             FROM `transaction_items` ti 
    //                             INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
    //                             INNER JOIN `category_list` cl ON pl.category_id = cl.category_id 
    //                             WHERE ti.transaction_id = '{$row['transaction_id']}' 
    //                             GROUP BY cl.category_id, cl.name");

    //     while ($category_row = $category_query->fetchArray()) {
    //         $category_id = $category_row['category_id'];
    //         $category_name = $category_row['category_name'];
    //         $category_count = $category_row['count'];
    //         $category_sum = $category_row['ctot_price'];

    //         if (!isset($category_counts[$category_id])) {
    //             $category_counts[$category_id] = [
    //                 'name' => $category_name,
    //                 'count' => 0,
    //                 'ctot_price' => 0.0
    //             ];
    //         }
    //         $category_counts[$category_id]['count'] += $category_count;
    //         $category_counts[$category_id]['ctot_price'] += $category_sum;
    //     }
    // }

    // echo "<strong>Total items:</strong> $tot_item<br>";

    // echo "<strong>Product-wise counts:</strong><br>";
    // foreach ($product_counts as $product_id => $product_info) {
    //     echo "Product: {$product_info['name']} (ID: $product_id) - Count: {$product_info['count']}<br>";
    // }

    // echo "<strong>Category-wise counts and total prices:</strong><br>";
    // foreach ($category_counts as $category_id => $category_info) {
    //     echo "Category: {$category_info['name']} (ID: $category_id) - Count: {$category_info['count']} - Total Price: {$category_info['ctot_price']}<br>";
    // }

    // echo "<strong>Payment type-wise item counts:</strong><br>";
    // foreach ($payment_type_counts as $payment_type => $count) {
    //     echo "Payment Type: $payment_type - Item Count: $count<br>";
    // }

    $tot_item = 0;
    $product_counts = [];  // Array to hold product-wise count with names
    $category_counts = []; // Array to hold category-wise count with names
    $payment_type_counts = []; // Array to hold payment type-wise item counts and total amounts
    $t_discount_count = 0;
    $t_discount_sum = 0.0;
    $s_desc_count = 0;
    $s_desc_sum = 0.0;
    
    while ($row = $qry->fetchArray()) {
        // Get total items for the transaction
        $items = $conn->query("SELECT SUM(quantity) as `count` 
                               FROM `transaction_items` 
                               WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['count'];
        $tot_item += $items;
    
        // Aggregate item count and total amount based on payment type
        $payment_type = $row['customer'];
        $transaction_total = $conn->query("SELECT SUM(price * quantity) as `total` 
                                           FROM `transaction_items` 
                                           WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['total'];
        
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
    
    echo "<strong>Total items:</strong> $tot_item<br>";
    
    echo "<strong>Product-wise counts:</strong><br>";
    foreach ($product_counts as $product_id => $product_info) {
        echo "Product: {$product_info['name']} (ID: $product_id) - Count: {$product_info['count']}<br>";
    }
    
    echo "<strong>Category-wise counts and total prices:</strong><br>";
    foreach ($category_counts as $category_id => $category_info) {
        echo "Category: {$category_info['name']} (ID: $category_id) - Count: {$category_info['count']} - Total Price: {$category_info['ctot_price']}<br>";
    }
    
    echo "<strong>Payment type-wise item counts and total amounts:</strong><br>";
    foreach ($payment_type_counts as $payment_type => $data) {
        echo "Payment Type: $payment_type - Item Count: {$data['item_count']} - Total Amount: {$data['total_amount']}<br>";
    }
    
    echo "<strong>Transaction discounts (t_discount):</strong><br>";
    echo "Count of transactions with t_discount: $t_discount_count<br>";
    echo "Sum of t_discount: $t_discount_sum<br>";
    
    echo "<strong>Special discounts (s_desc):</strong><br>";
    echo "Count of transactions with s_desc: $s_desc_count<br>";
    echo "Sum of s_desc: $s_desc_sum<br>";
    



    // // Now $product_counts and $category_counts hold the required data with names
    // // For example, to print them:
    // echo "<strong>Product-wise counts:</strong>";
    // echo "</br>";
    // foreach ($product_counts as $product_id => $product_info) {
    //     echo "Product: {$product_info['name']} (ID: $product_id) - Count: {$product_info['count']}";
    //     echo "</br>";
    // }

    // echo "</br>";

    // echo "<strong>Category-wise counts and total prices:</strong>";
    // echo "</br>";
    // foreach ($category_counts as $category_id => $category_info) {
    //     echo "Category: {$category_info['name']} (ID: $category_id) - Count: {$category_info['count']} - Total Price: {$category_info['ctot_price']}";
    //     echo "</br>";
    // }
    // echo "<hr>";
    // echo "Total Item Sales: " . $tot_item;
    // echo "<hr>";
    // echo "<strong>Refund Summery:</strong>";
    // echo "</br>";
    // $total = $conn->query("SELECT sum(total) as `total` FROM `refund_transaction_list` WHERE strftime('%Y-%m-%d %H:%M:%S', date_added) BETWEEN '{$dfrom}' AND '{$dto}'")->fetchArray()['total'];
    // echo "Gross Refund: ";
    // echo number_format($total, 2);
    // echo "</br>";

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
    <div class="container-fluid">
        <div id="outprint_receipt">
            <div class="text-center fs-6 fs-bold">
                <span><img src="image\logoabc.png" width='250' height='100'></span><br>
                <span>
                    <h3 class="fw-bold">Top Brewery</h1>
                </span>
                <small style="line-height:normal;" class="fw-bold">Tel: +60196000671 | 0342856778</small><br>
                <small style="line-height:normal;" class="fw-bold ">E-mail : topbrewery@gmail.com</small><br>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr class="text-dark">
                        <th class="py-0 px-1" style="font-size: small; text-align: center;">
                            <small class="fw-bold">No24. JLN BUNGA TANJUNG 8A</small><br>
                            <small class="fw-bold">TMN MUDA 56100 SELANGOR</small>
                        </th>
                        <!-- <th class="py-0 px-1" style="font-size: small; text-align: center;"><small class="fw-bold">Bopitiya Branch<br>
                    <small class="fw-bold">No 115/5/A,Bopitiya,Pamunugama</small>
                </th> -->
                    </tr>
                    <tr>
                        <td style="font-size: small;  text-align: center;">
                            Period : <?php echo $dfrom ." | ".$dto ?><br>
                        </td>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

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
                            <td class="px-1 py-0 align-middle"><?php echo $row['quantity'] ?></td>
                            <td class="px-1 py-0 align-middle">
                                <div class="fw light lh-1">
                                    <small><?php echo $row['product_code'] ?></small><br>
                                    <small><?php echo $row['pname'] ?></small>

                                    <small>(<?php echo number_format($row['price'], 2) ?>)</small>
                                </div>
                            </td>
                            <td class="px-1 py-0 align-middle text-end"><?php echo number_format($row['price'] * $row['quantity'], 2) ?></td>
                            <td class="px-1 py-0 align-middle text-end"><?php echo number_format($row['discount'] * $row['quantity'], 2) ?></td>

                        </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="px-1 py-0 " colspan="3">Total</th>
                        <th class="px-1 py-0 text-end"><?php echo number_format($t_discount + $total, 2) ?></th>
                    </tr>
                    <?php if ($t_discount != 0) : ?>
                        <tr>
                            <th class="px-1 py-0" colspan="3">Your Discount(-)</th>
                            <th class="px-1 py-0 text-end"><?php echo number_format($t_discount, 2) ?></th>
                        </tr>
                    <?php endif; ?>
                    <?php if ($s_desc != 0) : ?>
                        <tr>
                            <th class="px-1 py-0" colspan="3">Special Discount(-)</th>
                            <th class="px-1 py-0 text-end"><?php echo number_format($s_desc, 2) ?></th>
                        </tr>
                        <tr>
                            <th class="px-1 py-0" colspan="3">Total</th>
                            <th class="px-1 py-0 text-end"><?php echo number_format($total - $s_desc, 2) ?></th>
                        </tr>
                    <?php endif; ?>
                    <?php if ($arrears != 0) : ?>
                        <tr>
                            <th class="px-1 py-0" colspan="3">Your Arrears(-)</th>
                            <th class="px-1 py-0 text-end"><?php echo number_format($arrears, 2) ?></th>
                        </tr>
                        <tr>
                            <th class="px-1 py-0" colspan="3">Gross Total</th>
                            <th class="px-1 py-0 text-end"><?php echo number_format($total - $arrears, 2) ?></th>
                        </tr>
                    <?php endif; ?>
                    <!-- <tr>
                    <th class="px-1 py-0" colspan="3">Tendered</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($tendered_amount, 2) ?></th>
                </tr>
                <tr>
                    <th class="px-1 py-0" colspan="3">Change</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($change, 2) ?></th>
                </tr> -->
                </tfoot>
            </table>
            <div class="text-center">

                <span class="fw-bolder">Thank You, Come Again !</span><br>
                <small class="fw-bold lh-1">Software By</small><br>
                <small class="fw-bold lh-1">EncodeUnlimited | 0706291709 | www.encodeunlimited.com</small><br>
            </div>
        </div>

        <!-- <div class="w-100 d-flex justify-content-end mt-2">
        <?php if (isset($_GET['view_only']) && $_GET['view_only'] == true && $_SESSION['type'] == 1) : ?>
            <button class="btn btn-sm btn-danger me-2 rounded-0" type="button" id="delete_data"><i class="fa fa-undo"></i> Refund</button>
        <?php endif; ?>
        <button class="btn btn-sm btn-success me-2 rounded-0" type="button" id="print_receipt"><i class="fa fa-print"></i> Print</button>
        <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
    </div> -->

    </div>

</body>
</html>