<?php
ob_start();
require_once 'config.php';
ob_end_clean();

header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get all registrations with user and event details
$sql = "SELECT r.reg_id, r.timestamp, u.name as student_name, e.title as event_title 
        FROM registrations r
        JOIN users u ON r.user_id = u.user_id
        JOIN events e ON r.event_id = e.event_id
        ORDER BY r.timestamp DESC";

$result = $conn->query($sql);

$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}

echo json_encode($registrations);
$conn->close();
?>