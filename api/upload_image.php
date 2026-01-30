<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Check if an image file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Validate the file type (JPG, PNG, GIF, WebP)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
        exit;
    }

    // Validate file size (5MB = 5 * 1024 * 1024 bytes)
    $maxFileSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxFileSize) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'File size exceeds 5MB limit.']);
        exit;
    }

    // Set the upload directory
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate a unique filename to avoid collisions
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueFileName = uniqid('img_', true) . '.' . $fileExtension;
    $uploadFilePath = $uploadDir . $uniqueFileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        // Return the relative path to the uploaded image
        $relativeImagePath = 'uploads/' . $uniqueFileName;
        echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully.', 'image_url' => $relativeImagePath]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
    }
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No image uploaded.']);
} 
?>