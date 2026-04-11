<?php

function blocked_user($user_id, $target_id) {
    include "connect_server.php";
    // query to see if user is blocked or has blocked target:
    $stmt_block = $conn->prepare("SELECT * FROM blocked
        WHERE (user_id = ? AND student_blocked = ?) OR (user_id = ? AND student_blocked = ?)");

    $stmt_block->bind_param("iiii", $user_id, $target_id, $target_id, $user_id);
    $stmt_block->execute();
    $result = $stmt_block->get_result();
    $blocked = $result->fetch_assoc(); // is this variable exists, user has blocked or is blocked by target

    return $blocked;
}

