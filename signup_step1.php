<?php

session_start();
include "connect_server.php"; // provides $conn (mysqli)

// only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: html/signup_step1.html');
    exit;
}

// redirect with an error message
function fail(string $msg) {
    header('Location: html/signup_step1.html?error=' . urlencode($msg));
    exit;
}

// collect input 
$first_name       = trim($_POST['first_name']       ?? '');
$last_name        = trim($_POST['last_name']        ?? '');
$signup_email     = trim($_POST['email']            ?? '');
$signup_username  = trim($_POST['username']         ?? '');
$signup_password  =      $_POST['password']         ?? '';
$confirm_password =      $_POST['confirm_password'] ?? '';
$age              = trim($_POST['age']              ?? '');
$gender           = trim($_POST['gender']           ?? '');
$sexuality        = trim($_POST['sexuality']        ?? '');

// required fields check
if (
    empty($first_name)      || empty($last_name)        ||
    empty($signup_email)    || empty($signup_username)  ||
    empty($signup_password) || empty($confirm_password) ||
    empty($age)             || empty($gender)           ||
    empty($sexuality)
) {
    fail('All fields are required.');
}

// UL student email check 
if (!str_ends_with($signup_email, '@studentmail.ul.ie')) {
    fail('You must use a valid UL student email ending with @studentmail.ul.ie.');
}

// password rules
if ($signup_password !== $confirm_password) {
    fail('Passwords do not match.');
}

if (strlen($signup_password) < 7) {
    fail('Password must be at least 7 characters long.');
}

// duplicate email check 
$stmt = $conn->prepare('SELECT user_id FROM credentials WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $signup_email);
$stmt->execute();
$stmt->bind_result($existing_id);
$email_exists = $stmt->fetch();
$stmt->close();

if ($email_exists) {
    fail('That email address is already registered.');
}

// duplicate username check
$stmt = $conn->prepare('SELECT user_id FROM credentials WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $signup_username);
$stmt->execute();
$stmt->bind_result($existing_id2);
$username_exists = $stmt->fetch();
$stmt->close();

if ($username_exists) {
    fail('That username is already taken.');
}

$hashed_password = password_hash($signup_password, PASSWORD_DEFAULT);

// cast age to integer for the DB insert
$age_int = (int)$age;

// takes the student id from the student email
$user_id = (int) strstr($signup_email, '@', true);

// insert all rows in a transaction
$conn->begin_transaction();

try {

    // 1. credentials
    $stmt = $conn->prepare(
        'INSERT INTO credentials (user_id, email, username, password)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->bind_param('isss', $user_id, $signup_email, $signup_username, $hashed_password);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare(
        'INSERT INTO personal_info (user_id, first_name, last_name, age, gender, administrator)
         VALUES (?, ?, ?, ?, ?, 0)'
    );
    $stmt->bind_param('issss', $user_id, $first_name, $last_name, $age_int, $gender);
    $stmt->execute();
    $stmt->close();

    // 3. academic_info
    $course_default = '';
    $year_default   = 1;
    $stmt = $conn->prepare(
        'INSERT INTO academic_info (user_id, course, c_year)
         VALUES (?, ?, ?)'
    );
    $stmt->bind_param('isi', $user_id, $course_default, $year_default);
    $stmt->execute();
    $stmt->close();

    // 4. interests
    $default = 'Other';
    $flag    = 0;
    $stmt = $conn->prepare(
        'INSERT INTO interests
             (user_id,
              drink,   drink_display,
              smoke,   smoke_display,
              food_lifestyle, food_display,
              personality,    personality_display,
              sexuality,      sexuality_display)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'isisisisisi',
        $user_id,
        $default, $flag,
        $default, $flag,
        $default, $flag,
        $default, $flag,
        $sexuality, $flag
    );
    $stmt->execute();
    $stmt->close();

    // 5. images 
    $default_pic = 'uploads/default.png';
    $pic_num     = 0;
    $stmt = $conn->prepare(
        'INSERT INTO images (user_id, profile_pic, pic_num)
         VALUES (?, ?, ?)'
    );
    $stmt->bind_param('isi', $user_id, $default_pic, $pic_num);
    $stmt->execute();
    $stmt->close();

    // 6. offense — all flags start at 0
    $now = date('Y-m-d H:i:s');
    $zero = 0;
    $stmt = $conn->prepare(
        'INSERT INTO offense (user_id, phone_warning, offence_num, blocked, reported, last_modified)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('iiiiis', $user_id, $zero, $zero, $zero, $zero, $now);
    $stmt->execute();
    $stmt->close();
    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    error_log('signup_step1.php DB error: ' . $e->getMessage());
    fail('Registration failed due to a server error. Please try again.');
}

$conn->close();

// create per-user folder: user/{username}/
$user_folder = __DIR__ . '/user/' . $signup_username . '/';
if (!is_dir($user_folder)) {
    mkdir($user_folder, 0755, true);
}

// store user_id and username in session for step 2
$_SESSION['user_id']            = $user_id;
$_SESSION['username']           = $signup_username;
$_SESSION['signup_in_progress'] = true;

header('Location: html/signup_step2.html');
exit;
