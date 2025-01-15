<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Sanitize input
    $filePath = 'uploads/' . $file;

    if (file_exists($filePath)) {
         // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');  // Or set a more specific MIME type
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($filePath));  // Corrected to use $filePath
        flush(); // Flush system output buffer
        
        // Read the file in chunks and send to the user
        $fileHandle = fopen($filePath, 'rb');  // Open the file in binary mode (corrected to use $filePath)
        while (!feof($fileHandle)) {
            echo fread($fileHandle, 1024 * 8);  // Read and output 8KB at a time
            flush();  // Flush the output buffer to ensure it's sent
        }
        fclose($fileHandle);  // Close the file after sending
        exit;
    } else {
        echo "Error: File not found." . $filePath;
    }
} else {
    echo "Error: No file specified.";
}
?>
