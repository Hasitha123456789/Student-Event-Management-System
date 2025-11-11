<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "<h2>Create Admin Account</h2>";

// Check if admin already exists
$checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$adminEmail = 'admin@example.com';
$checkStmt->bind_param("s", $adminEmail);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: orange;'>⚠️ Admin account already exists!</p>";
    echo "<p>Email: admin@example.com</p>";
    
    // Update password
    if (isset($_POST['update_password'])) {
        $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $newPassword, $adminEmail);
        
        if ($updateStmt->execute()) {
            echo "<p style='color: green;'>✅ Admin password has been reset to: admin123</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update password</p>";
        }
        $updateStmt->close();
    }
    
    echo "<form method='POST'>";
    echo "<button type='submit' name='update_password'>Reset Admin Password to 'admin123'</button>";
    echo "</form>";
    
} else {
    // Create admin account
    if (isset($_POST['create_admin'])) {
        $name = 'Admin User';
        $email = 'admin@example.com';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $contact = '1234567890';
        $isAdmin = 1;
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, contact_number, is_admin) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $password, $contact, $isAdmin);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin account created successfully!</p>";
            echo "<p><strong>Login Details:</strong></p>";
            echo "<ul>";
            echo "<li>Email: admin@example.com</li>";
            echo "<li>Password: admin123</li>";
            echo "</ul>";
            echo "<p><a href='../login.html'>Go to Login Page</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin account: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>No admin account found. Click the button below to create one.</p>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='create_admin' style='padding: 10px 20px; background: #667eea; color: white; border: none; cursor: pointer; border-radius: 5px;'>Create Admin Account</button>";
        echo "</form>";
        echo "<hr>";
        echo "<p><strong>Admin credentials will be:</strong></p>";
        echo "<ul>";
        echo "<li>Email: admin@example.com</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
    }
}

$checkStmt->close();
$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
    }
    button {
        padding: 10px 20px;
        background: #667eea;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
    }
    button:hover {
        background: #5568d3;
    }
</style>