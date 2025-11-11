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

// GET - Fetch events or single event
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Get single event
        $eventId = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        echo json_encode($event);
        $stmt->close();
    } else {
        // Get all events
        $result = $conn->query("SELECT * FROM events ORDER BY date DESC");
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        echo json_encode($events);
    }
}

// POST - Create, Update, or Delete event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'create') {
        // Create new event
        $title = trim($_POST['title']);
        $date = $_POST['date'];
        $venue = trim($_POST['venue']);
        $organizer = trim($_POST['organizer']);
        $description = trim($_POST['description']);
        
        $stmt = $conn->prepare("INSERT INTO events (title, date, venue, organizer, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $date, $venue, $organizer, $description);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create event']);
        }
        $stmt->close();
    }
    
    elseif ($action === 'update') {
        // Update event
        $eventId = intval($_POST['event_id']);
        $title = trim($_POST['title']);
        $date = $_POST['date'];
        $venue = trim($_POST['venue']);
        $organizer = trim($_POST['organizer']);
        $description = trim($_POST['description']);
        
        $stmt = $conn->prepare("UPDATE events SET title=?, date=?, venue=?, organizer=?, description=? WHERE event_id=?");
        $stmt->bind_param("sssssi", $title, $date, $venue, $organizer, $description, $eventId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update event']);
        }
        $stmt->close();
    }
    
    elseif ($action === 'delete') {
        // Delete event
        $eventId = intval($_POST['event_id']);
        
        $stmt = $conn->prepare("DELETE FROM events WHERE event_id=?");
        $stmt->bind_param("i", $eventId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
        }
        $stmt->close();
    }
}

$conn->close();
?>