<?php

session_start();
require_once __DIR__ . '/db_connection.php';

// only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: html/login.html');
    exit;
}

// collect input
$email = trim($_POST['email'] ??'');
$password = $_POST['password'] ??'';

// presence check
if (empty($email) || empty($password)) {
    header('Location: html/login.html?error' . urlencode('please fill in all fields.'));
    exit;
}

// look up userin credentials table
$stmt = $db->prepare(
    'SELECT user_id, username, password
        FROM credentials
        WHERE email = :email
        LIMIT 1'
);
$stmt->bindValue(':email', $email, SQLITE3_TEXT);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

// verify password hash used at registration
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: html/login.html?error=' . urlencode('Invalide email or password.'));
    exit;
}

// fetch admin flag from personal_info
$stmt2 = $db->prepare(
    'SELECT admin
        FROM personal_info
        WHERE user_id = :uid
        LIMIT 1'
);
$stmt2->bindValue(':uid', (int)$user['user_id'], SQLITE3_INTEGER);
$result2 = $stmt2->execute();
$info = $result2->fetchArray(SQLITE3_ASSOC);

// store session variable
$_SESSION['user_id'] = (int)$user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $email;
$_SESSION['is_admin'] = (bool)($info['admin'] ?? false);

// redirect to home
header('Location: index.php');
exit;