<?php
header('Content-Type: application/json');
include 'db.php';

// POST data
$appointment_id = $_POST['appointment_id'] ?? '';
$action = $_POST['action'] ?? ''; // "approve" or "reject"

if(empty($appointment_id) || empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'appointment_id and action are required']);
    exit;
}

// Fetch appointment
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch();

if(!$appointment) {
    echo json_encode(['status' => 'error', 'message' => 'Appointment not found']);
    exit;
}

// Update appointment status
try {
    if($action === 'approve' || $action === 'reject') {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$action, $appointment_id]);

        echo json_encode(['status' => 'success', 'message' => "Appointment $action successfully"]);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update appointment: ' . $e->getMessage()]);
}
?>
<?php
header('Content-Type: application/json');
include 'db.php';

$appointment_id = $_POST['appointment_id'] ?? '';
$action = $_POST['action'] ?? ''; // approve or reject

if(empty($appointment_id) || empty($action)) {
    echo json_encode(['status'=>'error','message'=>'appointment_id and action are required']);
    exit;
}

// Check appointment exists
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch();

if(!$appointment) {
    echo json_encode(['status'=>'error','message'=>'Appointment not found']);
    exit;
}

// Update status
try {
    if($action === 'approve' || $action === 'reject') {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$action, $appointment_id]);
        echo json_encode(['status'=>'success','message'=>"Appointment $action successfully"]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['status'=>'error','message'=>'Failed to update appointment: '.$e->getMessage()]);
}
?>
