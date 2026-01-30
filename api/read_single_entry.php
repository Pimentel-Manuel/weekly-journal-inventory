<?php
/**
 * GET endpoint to fetch a single journal entry by ID.
 *
 * @param int $id The ID of the journal entry to fetch.
 * @return json Response containing the journal entry data or an error message.
 */

header('Content-Type: application/json');

$journalEntries = [
    1 => ['title' => 'First Entry', 'content' => 'This is the first journal entry.'],
    2 => ['title' => 'Second Entry', 'content' => 'This is the second journal entry.'],
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (array_key_exists($id, $journalEntries)) {
    echo json_encode($journalEntries[$id]);
} else {
    echo json_encode(['error' => 'Journal entry not found.']);
}