<?php

session_start();
include "connect_server.php"; // provides $conn (mysqli)

// only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: html/login.html');
    exit;
}

// collect input
$email    = trim($_POST['email']    ?? '');
$password =      $_POST['password'] ?? '';

// presence check
if (empty($email) || empty($password)) {
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
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// verify password hash (password_hash used at registration)
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: html/login.html?error=' . urlencode('Invalid email or password.'));
    exit;
}

// fetch admin flag from personal_info
$stmt2 = $conn->prepare(
    'SELECT admin
       FROM personal_info
      WHERE user_id = ?
      LIMIT 1'
);
$stmt2->bind_param('i', $user['user_id']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$info    = $result2->fetch_assoc();
$stmt2->close();

$conn->close();

// store session variables
$_SESSION['user_id']  = (int)$user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email']    = $email;
$_SESSION['is_admin'] = (bool)($info['admin'] ?? false);

// redirect to home
header('Location: index.php');
exit;
