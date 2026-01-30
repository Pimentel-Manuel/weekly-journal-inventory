<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM journal_entries ORDER BY created_at DESC";
    $result = $mysqli->query($query);

    if ($result) {
        $entries = array();
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
        echo json_encode($entries);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

$mysqli->close();
?>