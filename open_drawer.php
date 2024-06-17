<?php
function openCashDrawer($printerName)
{
    // ESC/POS command to open the cash drawer
    $openDrawerCommand = chr(27) . chr(112) . chr(0) . chr(25) . chr(250);

    // Save the command to a temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'escpos');
    file_put_contents($tempFile, $openDrawerCommand);

    // Command to send the file to the printer
    $cmd = 'copy /B ' . escapeshellarg($tempFile) . ' ' . escapeshellarg($printerName);

    // Execute the command
    $output = shell_exec($cmd);

    // Remove the temporary file
    unlink($tempFile);

    // Check if the command executed successfully
    if (strpos($output, '1 file(s) copied') !== false) {
        return "Cash drawer opened successfully.";
    } else {
        return "Failed to open cash drawer.";
    }
}

// Set the correct printer name as shown in Control Panel -> Devices and Printers
$printerName = "\\\\localhost\\XP-80C"; // Replace with your printer's name

try {
    echo openCashDrawer($printerName);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

