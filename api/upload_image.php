<?php

// Check if an image file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Validate the file type (e.g., jpeg, png)
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only JPG and PNG are allowed.']);
        exit;
    }

    // Set the upload directory
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file to the uploads directory
    $uploadFilePath = $uploadDir . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        // Successful upload
        echo json_encode(['success' => 'Image uploaded successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file.']);
    }
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(['error' => 'No image uploaded.']);
} 
?>