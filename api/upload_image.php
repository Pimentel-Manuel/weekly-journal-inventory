<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Check if an image file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File size exceeds server limit.',
            UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
        ];
        $message = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Unknown upload error.';
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit;
    }

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
        mkdir($uploadDir, 0755, true);
    }

    // Determine file extension from MIME type to prevent extension manipulation
    $mimeToExtension = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $fileExtension = $mimeToExtension[$file['type']];
    
    // Generate a unique filename to avoid collisions
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