<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "<h2>Admin Login Debug Tool</h2>";

// Check if admin exists
echo "<h3>1. Check if Admin Exists</h3>";
$stmt = $conn->prepare("SELECT user_id, name, email, password, is_admin FROM users WHERE email = ?");
$adminEmail = 'admin@example.com';
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color: red;'>❌ Admin account does NOT exist in database</p>";
    echo "<p><a href='create_admin.php'>Click here to create admin account</a></p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Admin account found in database</p>";
    $admin = $result->fetch_assoc();
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>User ID</td><td>{$admin['user_id']}</td></tr>";
    echo "<tr><td>Name</td><td>{$admin['name']}</td></tr>";
    echo "<tr><td>Email</td><td>{$admin['email']}</td></tr>";
    echo "<tr><td>Password Hash</td><td>" . substr($admin['password'], 0, 30) . "...</td></tr>";
    echo "<tr><td>Is Admin</td><td>" . ($admin['is_admin'] ? 'YES (1)' : 'NO (0)') . "</td></tr>";
    echo "</table>";
}
$stmt->close();

// Test password verification
echo "<h3>2. Test Password Verification</h3>";
$testPassword = 'admin123';
$storedHash = $admin['password'];

if (password_verify($testPassword, $storedHash)) {
    echo "<p style='color: green;'>✅ Password 'admin123' matches the stored hash</p>";
} else {
    echo "<p style='color: red;'>❌ Password 'admin123' does NOT match the stored hash</p>";
    echo "<p>This means the password in database is wrong. Let's fix it:</p>";
    
    if (isset($_POST['fix_password'])) {
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $newHash, $adminEmail);
        
        if ($updateStmt->execute()) {
            echo "<p style='color: green;'>✅ Password has been fixed! <a href='test_admin_login.php'>Refresh this page</a></p>";
        }
        $updateStmt->close();
    } else {
        echo "<form method='POST'>";
        echo "<button type='submit' name='fix_password'>Fix Admin Password</button>";
        echo "</form>";
    }
}

// Test actual login
echo "<h3>3. Test Login Process</h3>";

if (isset($_POST['test_login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    echo "<p><strong>Attempting login with:</strong></p>";
    echo "<ul>";
    echo "<li>Email: " . htmlspecialchars($email) . "</li>";
    echo "<li>Password: " . htmlspecialchars($password) . "</li>";
    echo "</ul>";
    
    $stmt = $conn->prepare("SELECT user_id, name, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<p style='color: red;'>❌ No user found with email: $email</p>";
    } else {
        $user = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ User found in database</p>";
        
        if (password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✅ Password verification SUCCESS!</p>";
            
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            echo "<p style='color: green;'>✅ Session variables set successfully!</p>";
            echo "<p><strong>Session Data:</strong></p>";
            echo "<pre>" . print_r($_SESSION, true) . "</pre>";
            
            if ($user['is_admin']) {
                echo "<p style='color: green;'>✅ User is ADMIN</p>";
                echo "<p><a href='../admin.html'>Go to Admin Dashboard</a></p>";
            } else {
                echo "<p>User is NOT admin</p>";
                echo "<p><a href='../events.html'>Go to Events Page</a></p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Password verification FAILED!</p>";
            echo "<p>The password you entered does not match the hash in database.</p>";
        }
    }
    $stmt->close();
}

?>

<h3>4. Try Login Here</h3>
<form method="POST">
    <p>
        <label>Email:</label><br>
        <input type="email" name="email" value="admin@example.com" style="width: 300px; padding: 8px;">
    </p>
    <p>
        <label>Password:</label><br>
        <input type="password" name="password" value="admin123" style="width: 300px; padding: 8px;">
    </p>
    <button type="submit" name="test_login" style="padding: 10px 20px; background: #667eea; color: white; border: none; cursor: pointer; border-radius: 5px;">Test Login</button>
</form>

<hr>
<p><a href="../login.html">Go to Main Login Page</a></p>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin: 10px 0;
    }
    th, td {
        text-align: left;
        padding: 8px;
    }
    th {
        background-color: #667eea;
        color: white;
    }
</style>

<?php
$conn->close();
?>