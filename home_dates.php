<?php

include "./connect_server.php";

/**
 * CRITERIA FOR FRIENDSHIP MATCH:
 * at least 3 common interests 
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
                a.course, p.gender
        FROM interests AS i
        INNER JOIN academic_info AS a
             ON i.user_id = a.user_id
        INNER JOIN personal_info as p
            ON p.user_id = a.user_id
        WHERE i.user_id = {$user_id}";

$result1 = $conn->query($sql1);

$user = $result1->fetch_assoc();

// general user info
$sql2 = "SELECT i.user_id, i.food_lifestyle, i.sexuality,
                i.interest1, i.interest2, i.interest3, i.interest4, i.interest5,
                a.course
        FROM interests AS i
        INNER JOIN academic_info AS a
             ON i.user_id = a.user_id
        WHERE i.user_id != {$user_id}";

$result2 = $conn->query($sql2);
$base = array("food_lifestyle", "course");

$user_ints = array($user["interest1"], $user["interest2"], 
    $user["interest3"], $user["interest4"], $user["interest5"]);

// to store the matches
$match = [];
while($row = $result2->fetch_assoc()) {
    // CHECK FIRST AND FOREMOST: SEXUALITY VS MATCH
    // a. straight woman: target -> non lesbian &| non fem
    if (strtolower($user["gender"]) ==  "female" && 
            strtolower($user["sexuality"]) == "straight" &&
            (strtolower($row["gender"]) == "female" || 
            strtolower($row["sexuality"] == "lesbian"))) {
        echo "straight woman - fem/lesbian - skip<br>";
        continue; // skip
    }
    // b. straight man: target -> non gay &| non-masc
    if (strtolower($user["gender"]) ==  "male" && 
            strtolower($user["sexuality"]) == "straight" &&
            (strtolower($row["gender"]) == "male" || 
            strtolower($row["sexuality"]) == "gay")) {
        echo "straight man - male/gay - skip<br>";
        continue; // skip
    }
    
    // c. lesbian (non-male): target -> non straight, non male
    if (strtolower($user["gender"]) !=  "male" && 
            strtolower($user["sexuality"]) == "lesbian" &&
            (strtolower($row["gender"]) == "male" || 
            strtolower($row["sexuality"]) == "straight") ||
            strtolower($row["sexuality"]) == "gay") {
        echo "lesbian - male/straight - skip<br>";
        continue; // skip
    }
    
    // d. gay (non-female): target -> non straight, non fem
    if (strtolower($user["gender"]) !=  "female" && 
            strtolower($user["sexuality"]) == "gay" &&
            (strtolower($row["gender"]) == "female" || 
            strtolower($row["sexuality"]) == "straight" ||
            strtolower($row["sexuality"]) == "lesbian")) {
        echo "gay - fem/straight - skip<br>";
        continue; // skip
    } 

    // e. rest: gender/sexuality is irrelevant

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
}
