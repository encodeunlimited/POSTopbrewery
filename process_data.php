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


$sql = "SELECT t.*, u.fullname FROM transaction_list t INNER JOIN user_list u ON t.user_id = u.user_id WHERE strftime('%Y-%m-%d %H:%M:%S', t.date_added) BETWEEN '{$dfrom}' AND '{$dto}' ORDER BY strftime('%s', t.date_added) DESC;";
$qry = $conn->query($sql);

// echo $sql;
$tot_item = 0;

$i = 1;
while ($row = $qry->fetchArray()) :
    $items = $conn->query("SELECT sum(quantity) as `count` FROM `transaction_items` where transaction_id = '{$row['transaction_id']}'  ")->fetchArray()['count'];
    $tot_item += $items;
endwhile;

echo "Total Item Sales: ".$tot_item;


