<?php
include "connect_server.php";
session_start();
$sess_id = (int)$_SESSION["user_id"];
$loc = $_SESSION["location"];


/** 
 * Unmatch: set relationship romantic and r_status to 0
 * User will only have unmatch option if they are previously matched
*/

$stmt = $conn->prepare("UPDATE relationship SET romantic=0, r_status=0
    WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)");

if (isset($_POST["target_id"])) { // submit has been pressed, target_id has been set
    $target_id = (int)$_POST["target_id"]; // set target id, set through hidden input field
    $stmt->bind_param("iiii", $sess_id, $target_id, $target_id, $sess_id);
    $stmt->execute();
}

// In case of error, always return home
// redirect to home
header("Location: {$loc}");
exit;