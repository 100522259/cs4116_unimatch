<?php

$servername = "sql108.infinityfree.com";
$username   = "if0_41207740";
$password   = "hpQ2aHXoodn";
$dbname     = "if0_41207740_unimatch_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
