<?php
header('Content-Type: application/json');
include 'db.php';

// POST data
$farmer_id = $_POST['farmer_id'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

if(empty($farmer_id)) {
    echo json_encode(['status' => 'error', 'message' => 'farmer_id is required']);
    exit;
}

// Check farmer exists
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'farmer'");
$stmt->execute([$farmer_id]);
$farmer = $stmt->fetch();

if(!$farmer) {
    echo json_encode(['status' => 'error', 'message' => 'Farmer not found']);
    exit;
}

// Update fields
try {
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $farmer_id]);

    echo json_encode(['status' => 'success', 'message' => 'Farmer info updated successfully']);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update user: ' . $e->getMessage()]);
}
?>
