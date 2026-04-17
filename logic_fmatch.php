<?php
include "connect_server.php";
session_start();
$sess_id = (int)$_SESSION["user_id"];
$loc = $_SESSION["location"];

echo "here";
/** 
 * Consider table matches.
 * 1) Friendship match has 2 requirements for U1 - U2
 *  a. Row 1: id1 = U1, id2 = U2, friendship = 1
 *  b. Row 2: id2 = U2, id2 = U1, friendship = 1
 * 
 * 2) Friend-ing involves:
 *  a. Create a row such that:
 *      id1 = U1, id2 = U2, friendship = 1
 *  b. Update the row where id1 = U1 and id2 = to friendship = 1
 * 
 * 3) If already matches -> unmatch (set to 0)
*/

// we a series of prepared statements for each case:
// 1. create new row (romantic (0), friendship (1), created at (timestamp)
$stmt_insert = $conn->prepare("INSERT INTO matches (user_id1, user_id2, romantic, friendship, created_at) 
    VALUES (?, ?, 0, 1, CURRENT_TIMESTAMP)");
    // id1 = sess id; id2 = target id
echo " stmt_insert";

// 2. row exists, friendship is 0; 
$stmt_upd = $conn->prepare("UPDATE matches SET friendship=1, created_at=CURRENT_TIMESTAMP WHERE
    user_id1 = ? AND user_id2 = ?"); 
    // case 1: id1 is sess id, case 2: id2 is sess id
echo " stmt_upd";

// 3. row exists, romantic is 1: unmatch
$stmt_unm = $conn->prepare("UPDATE matches SET friendship=0, created_at=CURRENT_TIMESTAMP
    WHERE user_id1 = ? AND user_id2 = ?");
echo " stmt_upd 2";


// In addition, we need the queries to see if the row exists
$stmt_exist = $conn->prepare("SELECT friendship from matches WHERE
    user_id1 = ? AND user_id2 = ?");
// we don't care about the case where id1 = U2 and id2 = U1
// if friendship = 1, don't do anything
echo " stmt_exist<br>";


// for each possible matches: -> insert in database a new match
if (isset($_POST["target_id"])) {
    echo "here<br>";
    $target_id = (int)$_POST["target_id"]; // set target id, set through hidden input field
    echo "got target<br>";
    // 1. Does row exist?
    $stmt_exist->bind_param("ii", $sess_id, $target_id); // id1 = U1, id2 = U2
    echo "bind stmt<br>";
    if (!$stmt_exist->execute()) {
            die("SELECT failed: " . $stmt_exist->error);
    }
    echo "executed stmt<br>";
    $result = $stmt_exist->get_result();
    $exist = $result->fetch_assoc();
    echo "queried sel<br>";
    
    if (!$exist) { // if row doesn't exist we insert row:
        echo "no exists<br>";
        $stmt_insert->bind_param("ii", $sess_id, $target_id);
        if (!$stmt_insert->execute()) {
            die("INSERT failed: " . $stmt_insert->error);
        }
    } else {
        echo "exist<br>";
        if ($exist["friendship"] == 0) {
            echo "no friends yet<br>";
        	$stmt_upd->bind_param("ii", $sess_id, $target_id);
            echo "bind<br>";
        	if (!$stmt_upd->execute()) {
            	die("UPDATE failed: " . $stmt_upd->error);
        	} echo "done upd<br>";
    	} else {
            echo "already friends - unmatch!";
            $stmt_unm->bind_param("ii", $sess_id, $target_id);
            if (!$stmt_unm->execute()) {
            	die("UPDATE failed: " . $stmt_unm->error);
        	} echo "done unm<br>";
        }
    }
}

// redirect to original location
header("Location: {$loc}");
exit;