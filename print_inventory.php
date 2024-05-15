<?php
// Include your database connection file if needed

// Start the session if not started already
session_start();
require_once("DBConnection.php");

// Retrieve data from the database and generate the table HTML
$tableHTML = '<table class="table table-striped table-hover">';
$tableHTML .= '<colgroup>
                    <col width="20%">
                    <col width="20%">
                    <col width="40%">
                    <col width="20%">
                </colgroup>';
$tableHTML .= '<thead>
                    <tr>
                        <th class="py-0 px-1">Category</th>
                        <th class="py-0 px-1">Product Code</th>
                        <th class="py-0 px-1">Product Name</th>
                        <th class="py-0 px-1">Available Quantity</th>
                    </tr>
                </thead>';
$tableHTML .= '<tbody>';
$sql = "SELECT p.*, c.name AS cname FROM `product_list` p INNER JOIN `category_list` c ON p.category_id = c.category_id WHERE p.status = 1 AND p.category_id NOT IN (209) ORDER BY `name` ASC";
$qry = $conn->query($sql);
while ($row = $qry->fetchArray()) {
    $stock_in = $conn->query("SELECT SUM(quantity) AS `total` FROM `stock_list` WHERE strftime('%s', `expiry_date` || '23:59:59') >= strftime('%s', CURRENT_TIMESTAMP) AND product_id = '{$row['product_id']}'")->fetchArray()['total'];
    $stock_out = $conn->query("SELECT SUM(quantity) AS `total` FROM `transaction_items` WHERE product_id = '{$row['product_id']}'")->fetchArray()['total'];
    $stock_in = $stock_in > 0 ? $stock_in : 0;
    $stock_out = $stock_out > 0 ? $stock_out : 0;
    $qty = $stock_in - $stock_out;
    $qty = $qty > 0 ? $qty : 0;

    $tableHTML .= '<td class="td py-0 px-1">' . $row['cname'] . '</td>';
    $tableHTML .= '<td class="td py-0 px-1">' . $row['product_code'] . '</td>';
    $tableHTML .= '<td class="td py-0 px-1">' . $row['name'] . '</td>';
    $tableHTML .= '<td class="td py-0 px-1 text-end">' . $qty . '</td>';
    $tableHTML .= '</tr>';
}
$tableHTML .= '</tbody>';
$tableHTML .= '</table>';

echo $tableHTML;
