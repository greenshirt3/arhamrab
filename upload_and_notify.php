<?php
// ==========================================
// ARHAM PRINTERS API - FILE UPLOAD HANDLER
// Path: public_html/api/upload_and_notify.php
// ==========================================

// 1. CORS HEADERS (CRITICAL)
// This allows your GitHub site (arhamprinters.pk) to talk to this RabHost file.
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle Browser Pre-flight checks
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// 2. CONFIGURATION
// We will save files in public_html/api/uploads/
$uploadDir = 'uploads/'; 
$maxFileSize = 50 * 1024 * 1024; // 50MB Limit
$allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'ppt', 'pptx', 'webp', 'cdr', 'ai', 'psd', 'tiff'];

$response = ['success' => false, 'message' => 'Unknown error'];

// 3. CREATE UPLOAD FOLDER IF MISSING
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Server Error: Could not create upload folder on RabHost.']);
        exit;
    }
}

// 4. HANDLE UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize Order ID
    $orderId = isset($_POST['order_id']) ? preg_replace('/[^a-zA-Z0-9-]/', '', $_POST['order_id']) : 'NO-ID';

    // Check if file exists
    if (isset($_FILES['documentFile']) && $_FILES['documentFile']['error'] === UPLOAD_ERR_OK) {
        
        $file = $_FILES['documentFile'];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate Size
        if ($fileSize > $maxFileSize) {
            echo json_encode(['success' => false, 'message' => 'File too large. Max 50MB.']);
            exit;
        }

        // Validate Type
        if (!in_array($fileExt, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            exit;
        }

        // Generate Safe Filename: ORDER-ID_TIMESTAMP_FILENAME
        $cleanName = preg_replace('/[^a-zA-Z0-9-_\.]/', '', basename($fileName));
        $newFileName = $orderId . '_' . time() . '_' . $cleanName;
        $destination = $uploadDir . $newFileName;

        // Move File
        if (move_uploaded_file($fileTmp, $destination)) {
            
            // SUCCESS
            echo json_encode([
                'success' => true, 
                'message' => 'File uploaded to RabHost successfully!',
                'file_name' => $newFileName,
                'path' => '/api/uploads/' . $newFileName
            ]);

        } else {
            echo json_encode(['success' => false, 'message' => 'Server Error: Failed to move file to uploads folder.']);
        }

    } else {
        $err = $_FILES['documentFile']['error'] ?? 'No File';
        echo json_encode(['success' => false, 'message' => 'Upload failed. Error Code: ' . $err]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request Method.']);
}
?>