<?php
// Clean output buffer
ob_start();

require_once 'config.php';

// Clear any previous output
ob_end_clean();

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $eventId = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    
    if ($eventId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid event']);
        exit;
    }
    
    try {
        // Check if event exists
        $stmt = $conn->prepare("SELECT event_id FROM events WHERE event_id = ?");
        if (!$stmt) {
            throw new Exception("Database error");
        }
        
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Event not found']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
        
        // Check if already registered
        $stmt = $conn->prepare("SELECT reg_id FROM registrations WHERE user_id = ? AND event_id = ?");
        if (!$stmt) {
            throw new Exception("Database error");
        }
        
        $stmt->bind_param("ii", $userId, $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Already registered for this event']);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
        
        // Register for event
        $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
        if (!$stmt) {
            throw new Exception("Database error");
        }
        
        $stmt->bind_param("ii", $userId, $eventId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Successfully registered for the event!']);
        } else {
            throw new Exception("Registration failed");
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        exit;
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>