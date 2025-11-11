<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['name'])) {
    echo json_encode([
        'loggedIn' => true,
        'user_id' => $_SESSION['user_id'],
        'name' => $_SESSION['name'],
        'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
        'is_admin' => isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
