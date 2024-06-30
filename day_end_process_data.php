<?php
session_start();
require_once("DBConnection.php");

if (isset($_POST['move_data'])) {
    $response = '';

    // Insert data from transaction_list to transaction_list_old
    $sqlDelete = "DELETE FROM transaction_list_old;";
        if ($conn->query($sqlDelete)) {
            $response = 'Data moved successfully!';
        } else {
            $response = 'Failed to delete data from transaction_list: ';
        }

    exit;
}

// Fetch and display data logic if needed
?>
