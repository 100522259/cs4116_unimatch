<?php // here we process the forms:
include "user_queries.php";


// 1. FORM CREDENTIALS
if(isset($_POST["submit_cred"])) {
    
    // a. Check that username is changed
    // TODO: CHECK AGAINST EXISTING USERNAMES
    if ($_POST["username"] != $creds["username"]) {
        $stmt = $conn->prepare("UPDATE credentials SET username = ? WHERE user_id = ?;");
        $stmt->bind_param("si", $_POST["username"], $user_id);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }


    // b. password:
    if ($_POST["password"] != $creds["password"] &&
            $_POST["password"] == $_POST["password2"]) {
        // if new and both are equal:
        $stmt = $conn->prepare("UPDATE credentials SET password = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("s", $_POST["password"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
}


// 2. FORM PERSONAL INFO
if (isset($_POST["submit_pers"])) {
    $p_info = array("first_name", "last_name", "county", "nationality", "gender", "bio");
    
    foreach($p_info as $val) {
        if ($_POST[$val] != $pers_info[$val]) {
            $stmt = $conn->prepare("UPDATE personal_info SET {$val} = ? WHERE user_id = {$user_id};");
            $stmt->bind_param("s", $_POST[$val]);
            if ($stmt === false) echo "Something bad happened :( <br>";
            $stmt->execute();
        }
    }

    // Age (becase it's an int)
    if ($_POST["age"] != $pers_info["age"]) {
        $stmt = $conn->prepare("UPDATE personal_info SET age = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("i", $_POST["age"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
}


// 3. FORM ACADEMIC INFO
if (isset($_POST["submit_acad"])) {
    // a. course:
    if ($_POST["course"] != $uni["course"]) {
        $stmt = $conn->prepare("UPDATE academic_info SET course = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("s", $_POST["course"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
    
    // b. year
    if ($_POST["C_year"] != $uni["c_year"]) {
        $stmt = $conn->prepare("UPDATE academic_info SET c_year = ? WHERE user_id = {$user_id};");
        $stmt->bind_param("i", $_POST["year"]);
        if ($stmt === false) echo "Something bad happened :( <br>";
        $stmt->execute();
    }
}


// 4. FORM INTERESTS
if (isset($_POST["submit_int"])) {
    $interests = array("drink", "smoke", "food_lifestyle", "personality", 
        "sexuality", "interest1", "interest2", "interest3", 
        "interest4", "interest5");

    foreach($interests as $val) {
        if ($_POST[$val] != $ints[$val]) {
            $stmt = $conn->prepare("UPDATE interests SET {$val} = ? WHERE user_id = {$user_id};");
            $stmt->bind_param("s", $_POST[$val]);
            if ($stmt === false) echo "Something bad happened :( <br>";
            $stmt->execute();
        }
    }

    $display = array("food_display", "personality_display", "sexuality_display");
    foreach($display as $val) {
        if (!isset($_POST[$val])) { // see if box is unchcked
            echo $val." unchecked<br>";
            // if unchecked: (it doesn't exist in post)
            $bit = 0; // bit set to 0
        } else $bit = 1; // bit set to 1
        // now update the set:
        if ($bit != $ints[$val]) { // update if different
            $stmt = $conn->prepare("UPDATE interests SET {$val} = ? WHERE user_id = {$user_id};");
            $stmt->bind_param("i", $bit);
            if ($stmt === false) echo "Something bad happened :( <br>";
            $stmt->execute();
        }
    }
}

// redirect back to settings
header('Location: settings.php');
exit;
