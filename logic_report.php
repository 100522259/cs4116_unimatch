<?php
include "connect_server.php";
session_start();
$sess_id = (int)$_SESSION["user_id"];
$loc = $_SESSION["location"];


/** 
 * User reported: we can have as many reports as we want with same id1 and id2
 * Also have to update/insert value in offense table
 *  
*/

// we have series of prepared statements for each case:
// 1. create new row in reports
$stmt_rep = $conn->prepare("INSERT INTO reports (user_id1, user_id2, timestamp, report_msg, category) 
    VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?)");
    // id1 = sess id; id2 = target id

// 2. create new row in offense
$stmt_off = $conn->prepare("INSERT INTO offense (user_id, phone_warning, offence_num, blocked, reported, last_modified)
    VALUES (?, 0, 0, 0, 1, CURRENT_TIMESTAMP)");

// 3. select query to see if user has offense row
$stmt_sel = $conn->prepare("SELECT reported FROM offense 
    WHERE user_id = ?");

// 4. update query if user has to update reported bit
$stmt_upd = $conn->prepare("UPDATE offense SET reported=1
    WHERE user_id = ?");


if (isset($_POST["target_id"])) { // submit has been pressed, target_id has been set
    $target_id = (int)$_POST["target_id"]; // set target id, set through hidden input field
    $msg = $_POST["msg"];
    $category = $_POST["category"];

    // 1. Insert row in REPORTED
    // bind variables from the form: --- id1 = reportee; id2 = reported
    $stmt_rep->bind_param("iiss", $sess_id, $target_id, $msg, $category);
    if (!$stmt_rep->execute()) {
        die("INSERT failed: " . $stmt_rep->error);
    }

    // 2. See if target user has row in OFFENSE
    $stmt_sel->bind_param("i", $target_id);
    if (!$stmt_sel->execute()) {
        die("SELECT failed: " . $stmt_sel->error);
    }
    $result = $stmt_sel->get_result();
    $exist = $result->fetch_assoc();

    if (!$exist) { // user doesn't have an offense row: insert one
        $stmt_off->bind_param("i", $target_id);
        if (!$stmt_off->execute()) {
            die("INSERT failed: " . $stmt_off->error);
        }
    } elseif ($exist["reported"] == 0) { // user has a row, update if reported bit is 0
        $stmt_upd->bind_param("i", $target_id);
        if (!$stmt_upd->execute()) {
            die("UPDATE failed: " . $stmt_upd->error);
        }
    }
}


// redirect to location origin
header("Location: {$loc}");
exit;

