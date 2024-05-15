<?php
// Establish connection to your database
session_start();
require_once("DBConnection.php");

// Execute the query
$query = "SELECT s.*, ss.name as sname, p.name as pname, p.product_code FROM `stock_list` s
          INNER JOIN `product_list` p ON s.product_id = p.product_id
          INNER JOIN `supplier_list` ss ON s.supplier_id = ss.supplier_id
          WHERE p.category_id NOT IN (209)
          ORDER BY strftime('%s', s.date_added) DESC";

$stmt = $conn->query($query);
$results = $stmt->fetchArray();

// Generate the table HTML
$tableHTML = '<table>';
$tableHTML .= '<tr><th>Column1</th><th>Column2</th><th>...</th></tr>';
foreach ($results as $row) {
    $tableHTML .= '<tr>';
    $tableHTML .= '<td>' . $row['sname'] . '</td>';
    $tableHTML .= '<td>' . $row['pname'] . '</td>';
    // Add other columns as needed
    $tableHTML .= '</tr>';
}
$tableHTML .= '</table>';

// Send the HTML back to the client
echo $tableHTML;
?>

<script>
    $(document).ready(function() {
    $('#printButton').click(function() {
        $.ajax({
            url: 'generate_table.php', // URL to your server-side script
            type: 'POST',
            data: {
                // Include any parameters needed for your query
            },
            success: function(response) {
                // Open a new window with the table content
                var printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print Table</title></head><body>');
                printWindow.document.write(response);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print(); // Print the page
            },
            error: function(xhr, status, error) {
                console.error(status, error);
            }
        });
    });
});

</script>