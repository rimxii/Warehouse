<?php
header('Content-Type: application/json');
include 'db.php'; // include the database connection

// Get POST data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

// Fetch user by email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if(!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Email not found']);
    exit;
}

// Verify password
if(!password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect password']);
    exit;
}

// Farmer-specific suspension logic
if($user['role'] === 'farmer') {
    if($user['suspended'] == 1) {
        echo json_encode(['status' => 'error', 'message' => 'You are suspended due to missed appointments']);
        exit;
    } elseif($user['missed_appointments'] >= 3) {
        // Suspend farmer now
        $conn->prepare("UPDATE users SET suspended = 1 WHERE id = ?")->execute([$user['id']]);
        echo json_encode(['status' => 'error', 'message' => 'You have been suspended due to missed appointments']);
        exit;
    }
}

// Successful login
echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role']
    ]
]);
?>
