<?php
session_start();

// stop people coming here without having done step 1
if (empty($_SESSION['user_id'])) {
    header('Location: html/signup_step1.html');
    exit;
}

include "connect_server.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: html/signup_step2.html');
    exit;
}

function fail2($msg) {
    header('Location: html/signup_step2.html?error=' . urlencode($msg));
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];

// bio
$bio = trim($_POST['bio']);
if (strlen($bio) > 2500) {
    $bio = substr($bio, 0, 2500);
}

// interests 1-5, all optional
$interests = array();
for ($i = 1; $i <= 5; $i++) {
    $val = trim($_POST["interest$i"]);
    if ($val == '') {
        $interests[$i] = null;
    } else {
        $interests[$i] = $val;
    }
}

// handle the profile photo upload
$new_pic_path = null;

if (!empty($_FILES['profile_pic']['name'])) {
    $file = $_FILES['profile_pic'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    if ($file['error'] != UPLOAD_ERR_OK) {
        fail2('File upload failed, please try again.');
    }
    if ($file['size'] > $max_size) {
        fail2('Profile photo must be under 5 MB.');
    }

    // only allow actual image files
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($ext, $allowed_ext)) {
        fail2('Only JPG, PNG or GIF images are allowed.');
    }

    // save as pfp.ext inside the user's folder (created in step 1)
    $filename = 'pfp.' . $ext;
    $upload_dir = __DIR__ . '/user/' . $username . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        fail2('Could not save the photo, please try again.');
    }

    // we only store the filename in the DB, same convention as settings_handler.php
    $new_pic_path = $filename;
}

// now update the DB with everything the user filled in
$conn->begin_transaction();

try {
    // save the bio
    $stmt = $conn->prepare("UPDATE personal_info SET bio = ? WHERE user_id = ?");
    $stmt->bind_param('si', $bio, $user_id);
    $stmt->execute();
    $stmt->close();

    // save the 5 interests
    $stmt = $conn->prepare("UPDATE interests SET interest1 = ?, interest2 = ?, interest3 = ?, interest4 = ?, interest5 = ? WHERE user_id = ?");
    $stmt->bind_param('sssssi',
        $interests[1], $interests[2], $interests[3], $interests[4], $interests[5],
        $user_id);
    $stmt->execute();
    $stmt->close();

    // save the profile pic if they uploaded one
    if ($new_pic_path != null) {
        $stmt = $conn->prepare("UPDATE images SET profile_pic = ? WHERE user_id = ?");
        $stmt->bind_param('si', $new_pic_path, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    fail2('Could not save profile, please try again.');
}

$conn->close();

unset($_SESSION['signup_in_progress']);

// send them to the login page now that the account is done
header('Location: html/login.html');
exit;
