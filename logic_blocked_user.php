<?php

function blocked_user($user_id, $target_id) {
    include "connect_server.php";
    // query to see if user is blocked or has blocked target:
    $stmt_block = $conn->prepare("SELECT * FROM blocked
        WHERE user_id = ? AND student_blocked = ?");

    $stmt_block->bind_param("ii", $user_id, $target_id);
    $stmt_block->execute();
    $result = $stmt_block->get_result();
    $blocked = $result->fetch_assoc(); // is this variable exists, user has blocked or is blocked by target

    return $blocked;
}

function banned_user($target_id) {
    include "connect_server.php";
    // query to see if user is blocked or has blocked target:
    $stmt_block = $conn->prepare("SELECT blocked FROM offense
        WHERE user_id = ?");

    $stmt_block->bind_param("i", $target_id);
    $stmt_block->execute();
    $result = $stmt_block->get_result();
    $row = $result->fetch_assoc(); // is this variable exists, user has blocked or is blocked by target
    if ($row) $banned = $row["blocked"];
    else $banned = 0;
    return $banned;
}

function r_matched($user_id, $target_id) {
    include "connect_server.php";

    // query to see if user is matches romantic
    $stmt = $conn->prepare("SELECT romantic FROM matches WHERE user_id1 = ? AND user_id2 = ?");
    $stmt->bind_param("ii", $user_id, $target_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row) { // if row exists...
        $r_matched = $row["romantic"];
    } else $r_matched = 0;

    return $r_matched;
}


function f_matched($user_id, $target_id) {
    include "connect_server.php";

    // query to see if user is matches romantic
    $stmt = $conn->prepare("SELECT friendship FROM matches WHERE user_id1 = ? AND user_id2 = ?");
    $stmt->bind_param("ii", $user_id, $target_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row) { // if row exists...
        $f_matched = $row["friendship"];
    } else $f_matched = 0;

    return $f_matched;
}