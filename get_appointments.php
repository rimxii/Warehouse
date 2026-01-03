<?php
header('Content-Type: application/json');
include 'db.php';

// Optional filter: farmer_id
$farmer_id = $_GET['farmer_id'] ?? '';

try {
    if(!empty($farmer_id)) {
        $stmt = $conn->prepare("SELECT * FROM appointments WHERE farmer_id = ? ORDER BY date DESC");
        $stmt->execute([$farmer_id]);
    } else {
        $stmt = $conn->query("SELECT a.*, u.name AS farmer_name, u.email AS farmer_email FROM appointments a JOIN users u ON a.farmer_id = u.id ORDER BY date DESC");
    }

    $appointments = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'appointments' => $appointments]);

} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch appointments: ' . $e->getMessage()]);
}
?>
