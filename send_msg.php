<?php
session_start();
include "./connect_server.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'unauthenticated']);
    exit;
}

$current_user_id = (int)$_SESSION['user_id'];
$to_user_id      = (int)($_POST['to_user_id'] ?? 0);
$body            = trim($_POST['body'] ?? '');

if ($to_user_id === 0 || $body === '') {
    echo json_encode(['ok' => false, 'error' => 'missing fields']);
    exit;
}

$check = $conn->prepare(
    "SELECT id FROM relationship
      WHERE (user_id1 = ? AND user_id2 = ? AND (r_status = 1 OR f_status = 1))
         OR (user_id1 = ? AND user_id2 = ? AND (r_status = 1 OR f_status = 1))
      LIMIT 1"
);
$check->bind_param('iiii', $current_user_id, $to_user_id, $to_user_id, $current_user_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $check->close(); $conn->close();
    echo json_encode(['ok' => false, 'error' => 'not matched']);
    exit;
}
$check->close();

$ins = $conn->prepare("INSERT INTO messages (from_user_id, to_user_id, body) VALUES (?, ?, ?)");
$ins->bind_param('iis', $current_user_id, $to_user_id, $body);
$ins->execute();
$ins->close();
$conn->close();

echo json_encode(['ok' => true]);