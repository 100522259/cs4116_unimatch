<?php
session_start();
include "./connect_server.php";

define('TZ_OFFSET', 28800); // 8 hours in seconds

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthenticated']);
    exit;
}

$current_user_id = (int)$_SESSION['user_id'];
$with_user_id    = isset($_GET['with']) ? (int)$_GET['with'] : 0;

if ($with_user_id === 0) {
    echo json_encode(['messages' => []]);
    $conn->close();
    exit;
}

$check = $conn->prepare(
    "SELECT id FROM relationship
      WHERE (user_id1 = ? AND user_id2 = ? AND (r_status = 1 OR f_status = 1))
         OR (user_id1 = ? AND user_id2 = ? AND (r_status = 1 OR f_status = 1))
      LIMIT 1"
);
$check->bind_param('iiii', $current_user_id, $with_user_id, $with_user_id, $current_user_id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    $check->close(); $conn->close();
    echo json_encode(['messages' => []]);
    exit;
}
$check->close();

$stmt = $conn->prepare(
    "SELECT from_user_id, body, sent_at FROM messages
      WHERE (from_user_id = ? AND to_user_id = ?)
         OR (from_user_id = ? AND to_user_id = ?)
      ORDER BY sent_at ASC"
);
$stmt->bind_param('iiii', $current_user_id, $with_user_id, $with_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $ts = strtotime($row['sent_at']) + TZ_OFFSET;
    $messages[] = [
        'from_user_id' => (int)$row['from_user_id'],
        'body'         => $row['body'],
        'sent_at'      => date("H:i", $ts),
        'date'         => date("D j M", $ts),
    ];
}
$stmt->close();
$conn->close();
echo json_encode(['messages' => $messages]);