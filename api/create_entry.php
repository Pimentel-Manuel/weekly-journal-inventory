<?php
// api/create_entry.php

// Include database configuration file
include_once('../config.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Prepare the SQL statement to prevent SQL injection
    if (isset($input['title']) && isset($input['content'])) {
        $title = $input['title'];
        $content = $input['content'];
        $date_created = date('Y-m-d H:i:s');
        
        // Prepare an SQL statement
        $stmt = $conn->prepare('INSERT INTO journal_entries (title, content, date_created) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $title, $content, $date_created);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Entry created successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create entry.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
