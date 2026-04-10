<?php
include "connect_server.php";
session_start();
$sess_id = (int)$_SESSION["user_id"];
$loc = $_SESSION["location"];


/** 
 * User blocked... Or unblocked
 * ** If row doesn't exist, insert new (block)
 * ** If row exists... delete (unblock)
 *  
*/

// we have series of prepared statements for each case:
// 1. create new row
$stmt_ins = $conn->prepare("INSERT INTO blocked (user_id1, user_id2) 
    VALUES (?, ?)");
    // id1 = sess id; id2 = target id

// 2. select existing
$stmt_sel = $conn->prepare("SELECT FROM blocked 
    WHERE user_id = ? AND student_blocked = ?");

// 3. delete existing row:
$stmt_del = $conn->prepare("DELETE FROM blocked 
    WHERE user_id = ? AND student_blocked = ?");



if (isset($_POST["target_id"])) { // submit has been pressed, target_id has been set
    $target_id = (int)$_POST["target_id"]; // set target id, set through hidden input field

    // 1. check if row exists (student already blocked)
    $stmt_sel->bind_param("ii", $sess_id, $target_id);
    if (!$stmt_sel->execute()) {
        die("SELECT failed: " . $stmt_sel->error);
    }
    $result = $stmt_sel->get_result();
    $exist = $result->fetch_assoc();
    if (!$exist) { // BLOCK
        $stmt_ins->bind_param("ii", $sess_id, $target_id);
        if (!$stmt_ins->execute()) {
            die("INSERT failed: " . $stmt_ins->error);
        }
    } else { // UNBLOCK
        $stmt_del->bind_param("ii", $sess_id, $target_id);
        if (!$stmt_del->execute()) {
            die("DELETE failed: " . $stmt_del->error);
        }
    }   
}


// redirect to location origin
header("Location: {$loc}");
exit;