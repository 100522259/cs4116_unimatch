<?php

include "connect_server.php";
// start session
session_start();

$target_id = $_SESSION["target_id"];
$sess_id = $_SESSION["user_id"];


// 1. select image content
$sql = "select * from images where user_id={$target_id};";
$result = $conn->query($sql);
$images = $result->fetch_assoc();

$sql = "select pic_num from images where user_id={$sess_id};";
$result = $conn->query($sql);
$sess_images = $result->fetch_assoc();
$sess_num_images = $sess_images["pic_num"];


// 2. select profile content:
$sql = "select * from personal_info where user_id={$target_id};";
$result = $conn->query($sql);
$pers_info = $result->fetch_assoc();

// 3. Interests:
$sql = "select * from interests where user_id={$target_id};";
$result = $conn->query($sql);
$ints = $result->fetch_assoc();

// 4. about uni:
$sql = "select * from academic_info where user_id={$target_id};";
$result = $conn->query($sql);
$uni = $result->fetch_assoc();

// 5. credentials:
$sql = "select * from credentials where user_id={$target_id};";
$result = $conn->query($sql);
$creds = $result->fetch_assoc();
