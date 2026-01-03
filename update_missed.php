<?php
header('Content-Type: application/json');
include 'db.php';

// POST data
$farmer_id = $_POST['farmer_id'] ?? '';
$increment = $_POST['increment'] ?? 1; // default increment by 1

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

// Update missed appointments
$new_missed = $farmer['missed_appointments'] + $increment;
$suspended = ($new_missed >= 3) ? 1 : 0;

try {
    $stmt = $conn->prepare("UPDATE users SET missed_appointments = ?, suspended = ? WHERE id = ?");
    $stmt->execute([$new_missed, $suspended, $farmer_id]);

    $message = "Missed appointments updated. Farmer " . ($suspended ? "suspended" : "active");
    echo json_encode(['status' => 'success', 'message' => $message, 'missed_appointments' => $new_missed, 'suspended' => $suspended]);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update missed appointments: ' . $e->getMessage()]);
}
?>
