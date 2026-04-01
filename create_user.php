<?php
require_once 'config.php';
require_once 'db.php';

// Create a new user with known password
$username = 'testuser';
$email = 'test@test.com';
$password = 'password123';

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Delete if exists
$mysqli->query("DELETE FROM users WHERE username = '$username'");

// Insert new user
$stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hash);

if ($stmt->execute()) {
    echo "<h2>User created successfully!</h2>";
    echo "<p>Username: <strong>testuser</strong></p>";
    echo "<p>Password: <strong>password123</strong></p>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
    
    // Also update admin password
    $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt2 = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
    $stmt2->bind_param("s", $admin_hash);
    $stmt2->execute();
    echo "<p>Admin password also reset to: <strong>admin123</strong></p>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>
