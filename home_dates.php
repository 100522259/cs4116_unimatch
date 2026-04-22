<?php

include "./connect_server.php";
include_once "./logic_blocked_user.php";

/**
 * CRITERIA FOR DATES MATCH:
 * at least 2 common interests 
 * (including food lifestyle, course)
 * Sexuality must match with user gender.
 * * Straight male -> returns only gender=female or other, non-lesbian
 * * Straight female -> returns only gender=male or other, non-gay
 * * Bisexual, Pansexual, Ace (might still be romantically interested in people) -> gender irrelevant
 * * Gay -> returns non-straight non-female
 * * Lesbian -> returns non-straight non-male
 * 
 * If user has "other" or gender sexuality doesn't apply
 * 
 */
// Following query for session user info
$sql1 = "SELECT i.user_id, i.food_lifestyle, i.sexuality,
                i.interest1, i.interest2, i.interest3, i.interest4, i.interest5,
                a.course, p.gender, img.profile_pic
        FROM interests AS i
        INNER JOIN academic_info AS a
             ON i.user_id = a.user_id
        INNER JOIN personal_info as p
            ON p.user_id = i.user_id
        INNER JOIN images as img
            ON img.user_id = i.user_id
        WHERE i.user_id = {$user_id};";

$result1 = $conn->query($sql1);

$user = $result1->fetch_assoc();

// general user info
$sql2 = "SELECT i.user_id, i.food_lifestyle, i.sexuality,
                i.interest1, i.interest2, i.interest3, i.interest4, i.interest5,
                a.course, p.gender
        FROM interests AS i
        INNER JOIN academic_info AS a
             ON i.user_id = a.user_id
        INNER JOIN personal_info as p
            ON p.user_id = i.user_id
        WHERE i.user_id != {$user_id};";

$result2 = $conn->query($sql2);
$base = array("food_lifestyle", "course");

$user_ints = array($user["interest1"], $user["interest2"], 
    $user["interest3"], $user["interest4"], $user["interest5"]);


$user_gender = strtolower($user["gender"]);
$user_sexuality = strtolower($user["sexuality"]);


// to store the matches
$match = [];
while($row = $result2->fetch_assoc()) {
    //echo $row["user_id"].' - ';
    $target_id = $row["user_id"];
    
    // 1. check if student is or has been blocked: prevent display:
    $has_blocked = blocked_user($user_id, $target_id);
    $is_blocked = blocked_user($target_id, $user_id);
    $is_banned = banned_user($target_id);
    $is_matched = r_matched($user_id, $target_id);

    if ($has_blocked || $is_blocked || $is_banned || $is_matched) {
        //echo "$target_id blocked...<br>";
        continue; // skip!
    }

    $target_gender = strtolower($row["gender"]);
    $target_sexuality = strtolower($row["sexuality"]);
  
    // Logic for USER attraction to TARGET USER
    $user_likes = (($user_sexuality == "straight" && $user_gender != $target_gender) ||
        ($user_sexuality == "gay" && $user_gender != "female" && $target_gender != "female") ||
        ($user_sexuality == "lesbian" && $user_gender != "male" && $target_gender != "male") ||
        $user_sexuality == "asexual" || $user_sexuality == "bisexual" || 
        $user_sexuality == "pansexual" || $user_sexuality == "other");
    //echo "user likes $user_likes - $target_id<br>";

    // 2. Logic for TARGET USER attraction to USER
    $target_likes = (($target_sexuality == "straight" && $target_gender != $user_gender) ||
            ($target_sexuality == "gay" && $target_gender != "female" && $user_gender != "female") ||
            ($target_sexuality == "lesbian" && $target_gender != "male" && $user_gender != "male") ||
            $target_sexuality == "asexual" || $target_sexuality == "bisexual" || 
            $target_sexuality == "pansexual" || $target_sexuality == "other");
	//echo "target likes $target_likes<br>";

    // if both are 1, then there's a gender-sexuality match; if not, skip to next iteration
    if ($user_likes == 0 || $target_likes == 0) {
        //echo "skip<br>";
        continue;
    }

    // 3. Compare interests
    $score = 0;
    foreach($base as $field) {
        if ($user[$field] == $row[$field]) {
            $score++;
        }
    }

    // 4. compare interests regardless of order
    $candidate_interests = array($row["interest1"], $row["interest2"], 
        $row["interest3"], $row["interest4"], $row["interest5"]);
    
    // array_intersect() -> returns the values in common to both arrays
    $shared = array_intersect($user_ints, $candidate_interests);
    $score += count($shared); // count returns the number of values in the array

    if ($score >= 2) {
        //echo "match :)<br>";
        $match[] = $row["user_id"];
    } //else echo "no match :(<br>";
}

// from ids stored in match, query the relevant info: name, age, pfp, interest 1 and 2
// we need $match to be a string, so we use the implode function, to concatenate 
// and convert the array into a string
if (!empty($match)) {
    $match_str = implode(",", $match);
    $sql = "SELECT U.user_id, U.first_name, U.age, I.interest1, 
            I.interest2, P.profile_pic, C.username FROM 
            personal_info AS U
            INNER JOIN interests AS I
                ON U.user_id = I.user_id
            INNER JOIN images as P
            ON U.user_id = P.user_id
            INNER JOIN credentials as C
                ON U.user_id = C.user_id
            WHERE U.user_id IN ({$match_str});";
    $f_result = $conn->query($sql);
} else echo "Oh... No matches...";
