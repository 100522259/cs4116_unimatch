<?php
session_start();
include 'db.php';

$from = $_SESSION['user_id'] ?? 1;
$to = $_POST['to_user_id'];
$msg = $_POST['message'];

// IMPORTANT → use "body" not "content"
$database->exec("
INSERT INTO messages (from_user_id, to_user_id, body)
VALUES ($from, $to, '$msg')
");

header("Location: chat_thread.php?user=$to");
exit();