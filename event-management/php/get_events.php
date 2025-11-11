<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all events with registration status
$sql = "SELECT e.*, 
        (SELECT COUNT(*) FROM registrations WHERE event_id = e.event_id AND user_id = ?) as is_registered
        FROM events e
        ORDER BY e.date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $row['is_registered'] = ($row['is_registered'] > 0);
    $events[] = $row;
}

echo json_encode($events);

$stmt->close();
$conn->close();
?>
