<?php
header('Content-Type: application/json');
include 'db.php';

// POST data
$farmer_id = $_POST['farmer_id'] ?? '';
$date = $_POST['date'] ?? ''; // format: YYYY-MM-DD HH:MM:SS

if(empty($farmer_id) || empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'farmer_id and date are required']);
    exit;
}

// Check if farmer exists
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'farmer'");
$stmt->execute([$farmer_id]);
$farmer = $stmt->fetch();

if(!$farmer) {
    echo json_encode(['status' => 'error', 'message' => 'Farmer not found']);
    exit;
}

// Insert appointment
try {
    $stmt = $conn->prepare("INSERT INTO appointments (farmer_id, date) VALUES (?, ?)");
    $stmt->execute([$farmer_id, $date]);

    echo json_encode(['status' => 'success', 'message' => 'Appointment requested successfully']);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to request appointment: ' . $e->getMessage()]);
}
?>
