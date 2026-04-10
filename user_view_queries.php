<?php

include "connect_server.php";
// start session
session_start();

$target_id = $_SESSION["target_id"];


// 1. select image content
$sql = "select * from images where user_id={$target_id};";
$result = $conn->query($sql);
$target_images = $result->fetch_assoc();
//echo "{$user_id}: <img src=\"/unimatch/images/{$row["profile_pic"]}\">";

// 2. select profile content:
$sql = "select * from personal_info where user_id={$target_id};";
$result = $conn->query($sql);
$target_pers_info = $result->fetch_assoc();

// 3. Interests:
$sql = "select * from interests where user_id={$target_id};";
$result = $conn->query($sql);
$target_ints = $result->fetch_assoc();

// 4. about uni:
$sql = "select * from academic_info where user_id={$target_id};";
$result = $conn->query($sql);
$target_uni = $result->fetch_assoc();

// 5. credentials:
$sql = "select * from credentials where user_id={$target_id};";
$result = $conn->query($sql);
$target_creds = $result->fetch_assoc();
