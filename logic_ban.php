<?php

include "connect_server.php";
session_start();
$sess_id = (int)$_SESSION["user_id"];
$loc = $_SESSION["location"];


/** 
 * User banned: update ban table
 * * Have to check if row exists
 *  -- If exist: update blocked bit
 *      ** if bit = 1 --> user already banned; unban (set to 0)
 *      ** if bit = 0 --> user not banned; ban (set to 1)
 *          Don't set ban time (indefinite ban)
 * -- If not exist: create row
 *  
*/

// we have series of prepared statements for each case:
// 1. create new row in offense (1 -> blocked)
$stmt_ins = $conn->prepare("INSERT INTO offense (user_id, phone_warning, offence_num, blocked, reported, last_modified)
    VALUES (?, 0, 0, 1, 0, CURRENT_TIMESTAMP)");

// 2. select query to see if user has offense row
$stmt_sel = $conn->prepare("SELECT blocked FROM offense 
    WHERE user_id = ?");

// 3. update query (toggle) if user has to update blocked bit
$stmt_upd = $conn->prepare("UPDATE offense SET blocked=?, last_modified=CURRENT_TIMESTAMP
    WHERE user_id = ?");
    // undefined reported -> toggle

if (isset($_POST["target_id"])) { // submit has been pressed, target_id has been set
    $target_id = (int)$_POST["target_id"]; // set target id, set through hidden input field

    // 1. select row:
    $stmt_sel->bind_param("i", $target_id);
    if (!$stmt_sel->execute()) {
        die("SELECT failed: " . $stmt_sel->error);
    }
    $result = $stmt_sel->get_result();
    $exist = $result->fetch_assoc();

    // 2. row doesn't exist, create it
    if (!$exist) { 
        $stmt_ins->bind_param("i", $target_id);
        if (!$stmt_ins->execute()) {
            die("INSERT failed: " . $stmt_ins->error);
        }

    // 3. row exists, toggle blocked (ban) bit
    } else { 
        $bit = !$exist["blocked"]; // togle bit
        $stmt_upd->bind_param("ii", $bit, $target_id);
        if (!$stmt_upd->execute()) {
            die("UPDATE failed: " . $stmt_upd->error);
        }
    }
}


// redirect to location origin
header("Location: {$loc}");
exit;
