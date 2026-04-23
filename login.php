<?php
session_start();
include "connect_server.php";

// only accept POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: html/login.html');
    exit;
}

// connect_server.php sets $username and $password, so use different names here
$login_email = trim($_POST['email']);
$login_password = $_POST['password'];

if ($login_email == '' || $login_password == '') {
    header('Location: html/login.html?error=' . urlencode('Please fill in all fields.'));
    exit;
}

// look up the user by email
// NOTE: can't use get_result() on infinityfree (no mysqlnd), so we use bind_result
$stmt = $conn->prepare("SELECT user_id, username, password FROM credentials WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $login_email);
$stmt->execute();
$stmt->bind_result($db_user_id, $db_username, $db_password);
$found = $stmt->fetch();
$stmt->close();

if (!$found || !password_verify($login_password, $db_password)) {
    header('Location: html/login.html?error=' . urlencode('Invalid email or password.'));
    exit;
}

// check if user is an admin
$stmt2 = $conn->prepare("SELECT administrator FROM personal_info WHERE user_id = ? LIMIT 1");
$stmt2->bind_param('i', $db_user_id);
$stmt2->execute();
$stmt2->bind_result($db_administrator);
$stmt2->fetch();
$stmt2->close();

$conn->close();

$_SESSION['user_id'] = (int)$db_user_id;
$_SESSION['username'] = $db_username;
$_SESSION['email'] = $login_email;
$_SESSION['is_admin'] = (bool)$db_administrator;

header('Location: home.php');
exit;
