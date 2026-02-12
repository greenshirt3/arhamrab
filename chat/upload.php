<?php
header('Content-Type: application/json');

// Security: Allow only images
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$filename = $_FILES['file']['name'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (in_array($ext, $allowed)) {
    // Create unique name to prevent overwriting
    $new_name = 'uploads/' . uniqid() . '.' . $ext;
    
    // Ensure uploads folder exists
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $new_name)) {
        echo json_encode(['success' => true, 'url' => $new_name]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Move failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
}
?>