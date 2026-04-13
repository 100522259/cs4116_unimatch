<?php

session_start();

// Guard - user should have completed the step1
if (empty($_SESSION['user_id'])) {
    header('Location: html/signup_step1.html');
    exit;
}

include "connect_server.php";

// only process POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: html/signup_step2.html');
    exit;
}

// Helper 
function fail2(string $msg) {
    header('Location: html/signup_step2.html?error=' . urlencode($msg));
    exit;
}

$user_id  = (int)    $_SESSION['user_id'];
$username = (string) $_SESSION['username'];

// Bio
$bio = trim($_POST['bio'] ?? '');
if (strlen($bio) > 2500) {
    $bio = substr($bio, 0, 2500);
}

// Interests (1-5, all optional)
$interests = [];
for ($i = 1; $i <= 5; $i++) {
    $val           = trim($_POST["interest$i"] ?? '');
    $interests[$i] = $val !== '' ? $val : null;
}

// profile photo upload
$new_pic_path = null;

if (!empty($_FILES['profile_pic']['name'])) {
    $file     = $_FILES['profile_pic'];
    $max_size = 5 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        fail2('File upload failed (code ' . $file['error'] . '). Please try again.');
    }

    if ($file['size'] > $max_size) {
        fail2('Profile photo must be under 5 MB.');
    }

    // validate MIME type
    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mime, $allowed, true)) {
        fail2('Only JPG, PNG, GIF or WEBP images are allowed.');
    }

    // Build safe filename: pfp.<ext>
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $ext      = preg_replace('/[^a-z0-9]/', '', $ext);
    $filename = 'pfp.' . $ext;

    // Save into user/{username}/ folder (created at step 1)
    $upload_dir = __DIR__ . '/user/' . $username . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        fail2('Could not save the photo. Please try again.');
    }

    // Store just the filename in the DB (matches settings_handler.php convention)
    $new_pic_path = $filename;
}

// Database updates
$conn->begin_transaction();

try {

    // Update bio in personal_info
    $stmt = $conn->prepare(
        'UPDATE personal_info SET bio = ? WHERE user_id = ?'
    );
    $stmt->bind_param('si', $bio, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update interest1–interest5 in interests table
    $stmt = $conn->prepare(
        'UPDATE interests
            SET interest1 = ?,
                interest2 = ?,
                interest3 = ?,
                interest4 = ?,
                interest5 = ?
          WHERE user_id = ?'
    );
    $stmt->bind_param(
        'sssssi',
        $interests[1],
        $interests[2],
        $interests[3],
        $interests[4],
        $interests[5],
        $user_id
    );
    $stmt->execute();
    $stmt->close();

    // Update profile picture if one was uploaded
    if ($new_pic_path !== null) {
        $stmt = $conn->prepare(
            'UPDATE images SET profile_pic = ?, pic_num = pic_num + 1 WHERE user_id = ?'
        );
        $stmt->bind_param('si', $new_pic_path, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    error_log('signup_step2.php DB error: ' . $e->getMessage());
    fail2('Profile save failed due to a server error. Please try again.');
}

$conn->close();

// Clean up signup session flag
unset($_SESSION['signup_in_progress']);

// Redirect to login so the user can sign in with their new account
header('Location: html/login.html');
exit;
