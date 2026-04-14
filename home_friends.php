<?php

include "./connect_server.php";
include_once "./logic_blocked_user.php";

/**
 * CRITERIA FOR FRIENDSHIP MATCH:
 * at least 3 common interests 
 * (including food lifestyle, sexuality, course)
 */
// Following query for session user info
$sql1 = "SELECT i.user_id, i.food_lifestyle, i.sexuality,
                i.interest1, i.interest2, i.interest3, i.interest4, i.interest5,
                a.course
        FROM interests AS i
        INNER JOIN academic_info AS a
             ON i.user_id = a.user_id
        WHERE i.user_id = {$user_id};";

$result1 = $conn->query($sql1);

$user = $result1->fetch_assoc();

// general user info
$sql2 = "SELECT i.user_id, i.food_lifestyle, i.sexuality,
                i.interest1, i.interest2, i.interest3, i.interest4, i.interest5,
                a.course
        FROM interests AS i
        INNER JOIN academic_info AS a
             ON i.user_id = a.user_id
        WHERE i.user_id != {$user_id};";

$result2 = $conn->query($sql2);
$base = array("food_lifestyle", "sexuality", "course");

$user_ints = array($user["interest1"], $user["interest2"], 
    $user["interest3"], $user["interest4"], $user["interest5"]);

// to store the matches
$match = [];
while($row = $result2->fetch_assoc()) {

    //echo $row["user_id"].' - ';
    $target_id = $row["user_id"];
    
    // 1. check if student is or has been blocked: prevent display:
    $has_blocked = blocked_user($user_id, $target_id);
    $is_blocked = blocked_user($target_id, $user_id);
    if ($has_blocked || $is_blocked) {
        //echo "$target_id blocked...<br>";
        continue; // skip!
    }

    // 2. Interests
    $score = 0;
    foreach($base as $field) {
        if ($user[$field] == $row[$field]) {
            $score++;
        }
    }

    // compare interests regardless of order
    $candidate_interests = array($row["interest1"], $row["interest2"], 
        $row["interest3"], $row["interest4"], $row["interest5"]);
    
    // array_intersect() -> returns the values in common to both arrays
    $shared = array_intersect($user_ints, $candidate_interests);
    $score += count($shared); // count returns the number of values in the array

    if ($score >= 3) {
        $match[] = $row["user_id"];
    }
}

// from ids stored in match, query the relevant info: name, age, pfp, interest 1 and 2
// we need $match to be a string, so we use the implode function, to concatenate 
// and convert the array into a string
if (!empty($match)) {
    $match_str = implode(",", $match);
    $sql = "SELECT U.user_id, U.first_name, U.age, I.interest1, 
            I.interest2, P.profile_pic FROM 
            personal_info AS U
            INNER JOIN interests AS I
                ON U.user_id = I.user_id
            INNER JOIN images as P
            ON U.user_id = P.user_id
            WHERE U.user_id IN ({$match_str});";
    $f_result = $conn->query($sql);
} else echo "Oh... no matches<br>";

$conn->close();
