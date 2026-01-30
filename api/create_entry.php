<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (isset($input['week_number']) && isset($input['month']) && 
        isset($input['title']) && isset($input['content'])) {
        
        $week_number = intval($input['week_number']);
        $month = intval($input['month']);
        $title = $input['title'];
        $content = $input['content'];
        $image_url = isset($input['image_url']) ? $input['image_url'] : null;
        
        // Connect to database
        $mysqli = connect_db();
        
        // Prepare an SQL statement to prevent SQL injection
        $stmt = $mysqli->prepare('INSERT INTO journal_entries (week_number, month, title, content, image_url) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iisss', $week_number, $month, $title, $content, $image_url);

        // Execute the statement
        if ($stmt->execute()) {
            $entryId = $stmt->insert_id;
            echo json_encode([
                'status' => 'success', 
                'message' => 'Entry created successfully.',
                'id' => $entryId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create entry: ' . $stmt->error]);
        }
        
        $stmt->close();
        close_db($mysqli);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields: week_number, month, title, or content.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
