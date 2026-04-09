<?php

session_start();
include "connect_server.php"; // provides $conn (mysqli)

// only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: html/login.html');
    exit;
}

// collect input — use different variable names to avoid collision
$login_email    = trim($_POST['email']    ?? '');
$login_password =      $_POST['password'] ?? '';

// presence check
if (empty($login_email) || empty($login_password)) {
    header('Location: html/login.html?error=' . urlencode('Please fill in all fields.'));
    exit;
}

// look up user in credentials table
$stmt = $conn->prepare(
    'SELECT user_id, username, password
       FROM credentials
      WHERE email = ?
      LIMIT 1'
);
$stmt->bind_param('s', $login_email);
$stmt->execute();
$stmt->bind_result($db_user_id, $db_username, $db_password);
$found = $stmt->fetch();
$stmt->close();

// verify credentials
if (!$found || $login_password !== $db_password) {
    header('Location: html/login.html?error=' . urlencode('Invalid email or password.'));
    exit;
}

// fetch admin flag from personal_info
$stmt2 = $conn->prepare(
    'SELECT administrator
       FROM personal_info
      WHERE user_id = ?
      LIMIT 1'
);
$stmt2->bind_param('i', $db_user_id);
$stmt2->execute();
$stmt2->bind_result($db_administrator);
$stmt2->fetch();
$stmt2->close();

$conn->close();

// store session variables
$_SESSION['user_id']  = (int)$db_user_id;
$_SESSION['username'] = $db_username;
$_SESSION['email']    = $login_email;
$_SESSION['is_admin'] = (bool)($db_administrator ?? false);

// redirect to home
header('Location: home.php');
exit;
