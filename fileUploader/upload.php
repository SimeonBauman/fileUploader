<?php
$uploadDir = 'uploads/'; // Directory to save files
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the uploaded chunk
    $chunk = $_FILES['chunk'];
    $fileName = $_POST['fileName'];
    $chunkIndex = $_POST['chunkIndex'];
    $totalChunks = $_POST['totalChunks'];

    // Generate a unique temporary file path
    $tempFile = $uploadDir . $fileName . '.part';
    $finalFile = $uploadDir . $fileName;

    try {
        // Append the chunk to the temp file
        $chunkContent = file_get_contents($chunk['tmp_name']);
        file_put_contents($tempFile, $chunkContent, FILE_APPEND);

        // Check if all chunks have been uploaded
        if ($chunkIndex == $totalChunks - 1) {
            // Rename temp file to the final file name
            rename($tempFile, $finalFile);
            echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully']);
        } else {
            echo json_encode(['status' => 'partial', 'message' => 'Chunk received']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error writing chunk: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
