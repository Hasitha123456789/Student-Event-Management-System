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

$stats = [];

// Count events
$result = $conn->query("SELECT COUNT(*) as count FROM events");
$stats['events'] = $result->fetch_assoc()['count'];

// Count users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $result->fetch_assoc()['count'];

// Count registrations
$result = $conn->query("SELECT COUNT(*) as count FROM registrations");
$stats['registrations'] = $result->fetch_assoc()['count'];

echo json_encode($stats);
$conn->close();
?>