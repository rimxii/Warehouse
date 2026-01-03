<?php
header('Content-Type: application/json');
include 'db.php';

// POST action: add, remove, list, reset_password
$action = $_POST['action'] ?? '';

// Only system admin should perform these actions
$performer_id = $_POST['performer_id'] ?? '';
if(empty($performer_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Performer ID required']);
    exit;
}

// Verify performer is system admin
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'system'");
$stmt->execute([$performer_id]);
$admin = $stmt->fetch();

if(!$admin) {
    echo json_encode(['status' => 'error', 'message' => 'Only system admin can perform this action']);
    exit;
}

try {
    if($action === 'add') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'warehouse'; // default warehouse

        if(empty($name) || empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Name, email, and password are required']);
            exit;
        }

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role]);

        echo json_encode(['status' => 'success', 'message' => 'Admin added successfully']);

    } elseif($action === 'remove') {
        $admin_id = $_POST['admin_id'] ?? '';
        if(empty($admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Admin ID required']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role IN ('system','warehouse')");
        $stmt->execute([$admin_id]);

        echo json_encode(['status' => 'success', 'message' => 'Admin removed successfully']);

    } elseif($action === 'list') {
        $stmt = $conn->query("SELECT id, name, email, role, created_at FROM users WHERE role IN ('system','warehouse')");
        $admins = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'admins' => $admins]);

    } elseif($action === 'reset_password') {
        $admin_id = $_POST['admin_id'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        if(empty($admin_id) || empty($new_password)) {
            echo json_encode(['status' => 'error', 'message' => 'Admin ID and new password required']);
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role IN ('system','warehouse')");
        $stmt->execute([$hashed_password, $admin_id]);

        echo json_encode(['status' => 'success', 'message' => 'Password reset successfully']);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Operation failed: ' . $e->getMessage()]);
}
?>
