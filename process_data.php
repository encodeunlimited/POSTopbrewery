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
// echo "<strong>Top Brewery</strong>";
// echo "<br>";
// echo "<small>Tel: +60196000671 | 0342856778</small>";
// echo "<br>";
// echo "<small>E-mail: topbrewery@gmail.com</small>";
// echo "<br>";
// echo "<small>No24. JLN BUNGA TANJUNG 8A</small>";
// echo "<br>";
// echo "<small>TMN MUDA 56100 SELANGOR</small>";
// echo "<br>";
echo "<strong>OUTLET - 04</strong>";
echo "<br>";
echo "<br>";
echo "<br>";

echo "Date From: " . $dfrom . "<br>";
echo "Date To: " . $dto . "<br>";
// echo "Payment Method: " . $pay_method . "<br>";

echo "</br>";
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
echo "</br>";


// $sql = "SELECT t.*, u.fullname FROM transaction_list t INNER JOIN user_list u ON t.user_id = u.user_id WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) BETWEEN '{$dfrom}' AND '{$dto}' ORDER BY strftime('%s', t.date_added) DESC;";
// $qry = $conn->query($sql);

// // echo $sql;
// $tot_item = 0;

// $i = 1;
// while ($row = $qry->fetchArray()) :
//     $items = $conn->query("SELECT sum(quantity) as `count` FROM `transaction_items` where transaction_id = '{$row['transaction_id']}'  ")->fetchArray()['count'];
//     $tot_item += $items;
// endwhile;


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

while ($row = $qry->fetchArray()) {
    // Get total items for the transaction
    $items = $conn->query("SELECT SUM(quantity) as `count` 
                           FROM `transaction_items` 
                           WHERE transaction_id = '{$row['transaction_id']}'")->fetchArray()['count'];
    $tot_item += $items;

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
    $category_query = $conn->query("SELECT cl.category_id, cl.name as category_name, SUM(ti.quantity) as `count` 
                                    FROM `transaction_items` ti 
                                    INNER JOIN `product_list` pl ON ti.product_id = pl.product_id 
                                    INNER JOIN `category_list` cl ON pl.category_id = cl.category_id 
                                    WHERE ti.transaction_id = '{$row['transaction_id']}' 
                                    GROUP BY cl.category_id, cl.name");

    while ($category_row = $category_query->fetchArray()) {
        $category_id = $category_row['category_id'];
        $category_name = $category_row['category_name'];
        $category_count = $category_row['count'];

        if (!isset($category_counts[$category_id])) {
            $category_counts[$category_id] = [
                'name' => $category_name,
                'count' => 0
            ];
        }
        $category_counts[$category_id]['count'] += $category_count;
    }
}

// Now $product_counts and $category_counts hold the required data with names
// For example, to print them:
echo "Product-wise counts:";
echo "</br>";
foreach ($product_counts as $product_id => $product_info) {
    echo "Product: {$product_info['name']} (ID: $product_id) - Count: {$product_info['count']}";
    echo "</br>";
}

echo "</br>";

echo "Category-wise counts:";
echo "</br>";
foreach ($category_counts as $category_id => $category_info) {
    echo "Category: {$category_info['name']} (ID: $category_id) - Count: {$category_info['count']}";
    echo "</br>";
}

echo "</br>";
echo "Total Item Sales: ".$tot_item;


