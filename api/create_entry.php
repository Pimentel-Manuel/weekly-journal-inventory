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
        
        // Validate week_number (1-8 for 2 months)
        if ($week_number < 1 || $week_number > 8) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Week number must be between 1 and 8.']);
            exit;
        }
        
        // Validate month (1-2)
        if ($month < 1 || $month > 2) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Month must be 1 or 2.']);
            exit;
        }
        
        // Validate image_url format if provided (must start with 'uploads/')
        if ($image_url !== null && strpos($image_url, 'uploads/') !== 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid image URL format.']);
            exit;
        }
        
        // Connect to database
        $mysqli = connect_db();
        
        if (!$mysqli) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
            exit;
        }
        
        // Prepare an SQL statement to prevent SQL injection
        $stmt = $mysqli->prepare('INSERT INTO journal_entries (week_number, month, title, content, image_url) VALUES (?, ?, ?, ?, ?)');
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare database statement.']);
            close_db($mysqli);
            exit;
        }
        
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
            error_log('Database insert error: ' . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create entry.']);
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
