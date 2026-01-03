<?php
header('Content-Type: application/json');
include 'db.php'; // include the database connection

// Get POST data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

// Basic validation
if(empty($name) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Name, email, and password are required']);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    exit;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Default role is 'farmer'
$role = 'farmer';

try {
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $hashed_password, $role]);

    echo json_encode(['status' => 'success', 'message' => 'Registered successfully']);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>
