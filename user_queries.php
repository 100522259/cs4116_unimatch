<?php

include "connect_server.php";

// 1. select image content
$sql = "select * from images where user_id={$user_id};";
$result = $conn->query($sql);
$images = $result->fetch_assoc();
//echo "{$user_id}: <img src=\"/unimatch/images/{$row["profile_pic"]}\">";

// 2. select profile content:
$sql = "select * from personal_info where user_id={$user_id};";
$result = $conn->query($sql);
$pers_info = $result->fetch_assoc();

// 3. Interests:
$sql = "select * from interests where user_id={$user_id};";
$result = $conn->query($sql);
$ints = $result->fetch_assoc();

// 4. about uni:
$sql = "select * from academic_info where user_id={$user_id};";
$result = $conn->query($sql);
$uni = $result->fetch_assoc();

// 5. credentials:
$sql = "select * from credentials where user_id={$user_id};";
$result = $conn->query($sql);
$creds = $result->fetch_assoc();

$conn->close();




