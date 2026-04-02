<?php
// Connect to server, to avoid repetition
//echo "<h1>Trying to query</h1>";
$servername = "sql108.infinityfree.com";
$username = "if0_41207740";
$password = "hpQ2aHXoodn";

$dbname = "if0_41207740_unimatch_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: ".$conn->connect_error);
}

//echo "Connected successfully<br>";

// FOllowing user_id is for testing, a session would be used with the stored id
$user_id = 200000004;