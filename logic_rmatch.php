<?php
include "connect_server.php";
session_start();
$sess_id = (int)$_SESSION["user_id"];
$loc = $_SESSION["location"];


/** 
 * If match button has been pressed we need to take the following actions: 
 * - check if row with both user ids exist match already exists: UPDATE
 * - if it does check: romantic:
 *  ** if 0 -> update, set to 1; r_status (0) !!
 *  ** if 1 -> check: r_status
 *      -- status = match (1) -> do nothing
 *      -- status = pending (0) -> check session_id position: !!
 *          ++ id is user_id1 do nothing
 *          ++ id is user_id2 -> set status to 1
 * 
 * - if row doesn't exists, create new row !!
 *  ** user_id1 = sess_id
 *  ** user_id2 = target id
 *  ** status: pending
 *  
*/

// we a series of prepared statements for each case:
// 1. create new row (romantic (1), friendship (0), r_status (0), f_status (0), created at (timestamp)
$stmt_insert = $conn->prepare("INSERT INTO relationship (user_id1, user_id2, romantic, friendship, r_status, f_status, created_at) 
    VALUES (?, ?, 1, 0, 0, 0, CURRENT_TIMESTAMP)");
    // id1 = sess id; id2 = target id

// 2. row exists, romantic is 0; 
$stmt_upd1 = $conn->prepare("UPDATE relationship SET romantic=1 WHERE
    user_id1 = ? AND user_id2 = ?"); 
    // case 1: id1 is sess id, case 2: id2 is sess id

// 3. row exists, romantic is 1, status is 0; sess id is id2
$stmt_upd2 = $conn->prepare("UPDATE relationship SET r_status=1 WHERE
    user_id1 = ? AND user_id2 = ?");
    // id1 = target id, id2 = sess id


// In addition, we need the queries to see if the row exists
$stmt_exist = $conn->prepare("SELECT romantic, r_status from relationship WHERE
    user_id1 = ? AND user_id2 = ?");
    // this query might be done twice, first to see if id1 = sess id
    // if not, invert ids and query. if none, row doesn't exist
    // Plus, we get to know if id1 is sess id or not


// for each possible matches: -> insert in database a new match

if (isset($_POST["target_id"])) { // submit has been pressed, target_id has been set
    $target_id = (int)$_POST["target_id"]; // set target id, set through hidden input field
    
    // 1. Does row exist?
    $stmt_exist->bind_param("ii", $sess_id, $target_id);
    $stmt_exist->execute();
    $result = $stmt_exist->get_result();
    $exist = $result->fetch_assoc();

    if (!$exist) { // if row doesn't exist with user_id1 == sess_id...
        $stmt_exist->bind_param("ii", $target_id, $sess_id);
        if (!$stmt_exist->execute()) {
            die("EXIST failed: " . $stmt_exist->error);
        }
        $result = $stmt_exist->get_result();
        $exist = $result->fetch_assoc();

        if (!$exist) { // ROW DOESN'T EXIST: INSERT
            $stmt_insert->bind_param("ii", $sess_id, $target_id);
            if (!$stmt_insert->execute()) {
                die("INSERT failed: " . $stmt_insert->error);
            }   

            // redirect to home
            header("Location: {$loc}");
            exit;

        } else $id1 = 0; // id1 not sess id
    } else $id1 = 1; // id1 is sess id

    // If we are here, row exists
    // next step: check romantic bit:
    // if romantic=0 change to 1
    if ($exist["romantic"] == 0) {
        // update:
        if ($id1) { // id1 = sess id
            $stmt_upd1->bind_param("ii", $sess_id, $target_id);
        } else $stmt_upd1->bind_param("ii", $target_id, $sess_id);

        if (!$stmt_upd1->execute()) {
            die("UPDATE failed: " . $stmt_upd1->error);
        }

    // if r_status=1: do nothing
    // if r_status=0 and id1=1: do nothing
    // if r_status=0 and id1=0: update r_status to 1
    } elseif ($exist["r_status"] == 0 && $id1==0) { // update
            // id1 = target id, id2 = sess id
            $stmt_upd2->bind_param("ii", $target_id, $sess_id);
            if (!$stmt_upd2->execute()) {
                die("UPDATE failed: " . $stmt_upd2->error);
            }
    }
}

// In case of error, always return home
// redirect to home
header("Location: {$loc}");
exit;