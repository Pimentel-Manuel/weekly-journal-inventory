<?php
// api/read_entries.php

header('Content-Type: application/json');

// Example journal entries data
$journalEntries = [
    ['id' => 1, 'content' => 'First journal entry', 'date' => '2026-01-01'],
    ['id' => 2, 'content' => 'Second journal entry', 'date' => '2026-01-15'],
];

// Fetch all journal entries
echo json_encode($journalEntries);
?>