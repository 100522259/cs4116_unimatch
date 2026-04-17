<?php
function echo_user($usr, $user_id, $count) {
    $pfp = './user/' . $usr["username"] . '/' . $usr["profile_pic"];

    echo '<div class="user">';
    echo '<img src="' . $pfp . '" alt="pfp">';    

    echo '<p>'.$usr["first_name"].', '.$usr["age"].'</p>';
    if ($usr["interest1"] != null) {
        echo '<p>' . $usr["interest1"].'</p>';
    }
    if ($usr["interest2"] != null) {
        echo '<p>' . $usr["interest2"] . '</p>';
    }

    $friends = f_matched($user_id, $usr["user_id"]);
    // form for user to friend-match
    echo '<div class="dform">';
    echo '<form name="f_match'.$count.'" action="logic_fmatch.php" method="post">';
    echo '<input type="submit" name="f'.$count.'" value="';
    if ($friends) echo 'Unfriend">';
    else echo 'Friend">';
    echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
    echo '</form>';
    
    $dates = r_matched($user_id, $usr["user_id"]);
    // form for user to date-match
    echo '<form name="r_match'.$count.'" action="logic_rmatch.php" method="post">';
    echo '<input type="submit" name="r'.$count.'" value="';
    if ($dates) echo 'Unmatch">';
    else echo 'Match">';
    echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
    echo '</form>';
    
    // form for user to view profile
    echo '<form name="r_match'.$count.'" action="user_view.php" method="post">';
    echo '<input type="submit" name="v'.$count.'" value="View">';
    echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
    echo '</form>';
    echo '</div></div><br>';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>unimatch - user profile page</title>
        <!--Bootstrap css-->
        
        <link href="css\profile.css" rel="stylesheet">
        <link href="css\profile_mobile.css" rel="stylesheet">
    </head>

    <body>
        <div class="container">

            <!--vertical container for page index-->
            <?php include "./sidebar.php"; ?>
            
            <!--Container for the rest of the page-->
            <div class="main">
                <div class="main-search">
                    <!--A form to search will appear here-->
                    <div class="header-text">
                        <h1>Search</h1>
                    </div>
                    <div class="search">
                        <form name="search" action="search.php" method="post" autocomplete="on">
                            <input id="sbar" type="text" name="uname" minlength="1" maxlength="20"
                                title="Write target username" placeholder="Search users..." required>
                            <input class="submit" type="submit" name="search" value="Search">
                        </form>
                    </div>
                    <div class="filters">
                        <form name="filters" action="search.php" method="post" autocomplete="on">
                        <fieldset><legend>Advanced Filters</legend>
                        <div class="f-container">
                            <?php
                            // 1. gender
                            echo '<div class="f-section">';
                            echo '<select name="gender">';
                            echo '<option value="gender" selected disabled>Gender</option>';
                            $gender = array("Male", "Female", "Non-binary", "Other");
                            foreach ($gender as $val) {
                                echo '<option value="'.$val.'">'.$val.'</option>';
                            }
                            echo '</select>';
                            
                            // 2. min age
                            echo '<select id="min_age" name="min_age">';
                            echo '<option value="min_age" selected disabled>Min Age</option>';
                            for ($i=18; $i<=30; $i++) {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            echo '</select>';

                            // 3. max age
                            echo '<select name="max_age">';
                            echo '<option value="max_age" selected disabled>Max Age</option>';
                            for ($i=18; $i<=30; $i++) {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            echo '</select>';
                            echo '</div>';

                            echo '<div class="f-section">';
                            // 4. course
                            echo '<input type="text" name="course" pattern="[A-Za-z"]{2,20}
                                    maxlength="20" size="20" title="Write course you are looking for"
                                    placeholder="Filter by course...">';
                            
                            
                            // 5. Filter by up to 2 interests
                            $interest_ops = array("Sports", "Music", "Gaming", "Reading",
                            "Travel", "Cooking", "Fitness", "Photography", "Art", "Technology",
                            "Movies", "Fashion", "Nature", "Dance", "Writing");
                            for ($i=1; $i<3; $i++) {
                                echo '<select name="interest'.$i.'">';
                                echo '<option value="interest '.$i.'" selected disabled>Interest '.$i.'</option>';

                                foreach($interest_ops as $int) {
                                    echo '<option value="'.$int.'">'.$int.'</option>';
                                }
                                echo '</select>';
                            }
                            echo '</div>';
                            ?>
                            
                            <div class="f-section">
                            <input class="submit" type="submit" name="filter" value="Apply Filters">
                            </div>
                        </div>
                        </fieldset></form>
                    </div>
                </div>
                
                <div class="possible-match">
                    <div class="match-display">
                        <?php
                        include "connect_server.php";
                        include_once "logic_blocked_user.php";
                        
                        $admin = $_SESSION["is_admin"];
                        $user_id = $_SESSION["user_id"];

                        // store session: so that if we do any actions like friend or match, 
                        // the view is still displaying the users we searched for
                        if (isset($_POST["search"])) { 
                            $_SESSION["search"] = "search";
                            $_SESSION["uname"] = $_POST["uname"];
                        }
                        if (isset($_POST["filter"])) {
                            $_SESSION["search"] = "filter";
                            if (isset($_POST["gender"])) $_SESSION["gender"] = $_POST["gender"];
                            else unset($_SESSION["gender"]);

                            if (isset($_POST["min_age"])) $_SESSION["min_age"] = $_POST["min_age"];
                            else unset($_SESSION["min_age"]);

                            if (isset($_POST["max_age"])) $_SESSION["max_age"] = $_POST["max_age"];
                            else unset($_SESSION["max_age"]);

                            if (isset($_POST["course"])) $_SESSION["course"] = $_POST["course"];
                            else unset($_SESSION["course"]);

                            if (isset($_POST["interest1"])) $_SESSION["interest1"] = $_POST["interest1"];
                            else unset($_SESSION["interest1"]);
                            
                            if (isset($_POST["interest2"])) $_SESSION["interest2"] = $_POST["interest2"];
                            else unset($_SESSION["interest2"]);

                            }


                        // A. search by username...
                        if ($_SESSION["search"] == "search") {
                            // check that the submitted value is not empty
                            
                            // Use normal sql string, a select doesn't have injection risk
                            $sname = $_SESSION["uname"];
                            $search = '%' . $sname . '%';

                            $stmt = $conn->prepare("SELECT P.user_id FROM credentials AS C 
                                    INNER JOIN personal_info as P
                                        ON C.user_id = P.user_id
                                    WHERE (LOWER(C.username) LIKE LOWER(?)
                                    OR LOWER(P.first_name) LIKE LOWER(?)
                                    OR LOWER(P.last_name) LIKE LOWER(?))
                                    AND C.user_id != ?");
                            $stmt->bind_param("sssi", $search, $search, $search, $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $ids = [];

                            while ($row = $result->fetch_assoc()) {
                                $is_banned = banned_user($row["user_id"]);
                                if (!$is_banned || $admin) {
                                    $ids[] = $row["user_id"];
                                }
                            }
                            
                            if (empty($ids)) { // list empty
                                echo '<p>Uh-oh... Couldn\'t fine any matches...<p>';
                            } else {
                                $ids_str = implode(",", $ids);
                                //echo $ids_str;
                                
                                // We query user details to print out
                                $sql = "SELECT U.user_id, U.first_name, U.age, I.interest1, 
                                    I.interest2, P.profile_pic, C.username FROM 
                                    personal_info AS U
                                    INNER JOIN interests AS I
                                        ON U.user_id = I.user_id
                                    INNER JOIN images as P
                                        ON U.user_id = P.user_id
                                    INNER JOIN credentials as C
                                        ON U.user_id = C.user_id
                                    WHERE U.user_id IN ({$ids_str});";
                                
                                $us_result = $conn->query($sql);

                                $count=0;
                                while ($usr = $us_result->fetch_assoc()) {

                                    echo_user($usr, $user_id, $count);

                                    $count++; // count number of friend matches
                                }
                                $_SESSION["location"] = "search.php"; // to go back to home or search page
                            }
                               
                        }

                        // B. Filter
                        if ($_SESSION["search"] == "filter") {
                            // generate the sql query, that will have more or less conditons
                            // based on the filters selected

                            // 1. taking from personal info
                            $sql = "SELECT p.user_id FROM personal_info AS p 
                                INNER JOIN interests AS i
                                    ON p.user_id = i.user_id 
                                INNER JOIN academic_info AS a
                                    ON p.user_id = a.user_id"; 
                            
                            $conditions = []; // variable to store the string sql conditions
                            if (isset($_SESSION["gender"])) {
                                // add according "where statement
                                $conditions[] = "gender LIKE '{$_SESSION["gender"]}'";
                            }
                            if (isset($_SESSION["min_age"])) {
                                $conditions[] = "age >= {$_SESSION["min_age"]}";
                            }
                            if (isset($_SESSION["max_age"])) {
                                $conditions[] = "age <= {$_SESSION["max_age"]}";
                            }
                            if ($_SESSION["course"] != "") {
                                $conditions[] = "LOWER(course) LIKE LOWER('%{$_SESSION["course"]}%')";
                            }
                            
                            $ints = []; // to store interests
                            if (isset($_SESSION["interest1"])) {
                                $ints[] = $_SESSION["interest1"];
                            }
                            if (isset($_POST["interest2"])) {
                                $ints[] = $_SESSION["interest2"];
                            } 

                            if (!empty($ints)) {
                                // we need to quote the interest values otherwise they will be stored like (sport, music)
                                // we want: ('sport', 'music')
                                $ints_str = implode("','", $ints); // convert to string
                                $ints_str = "'" . $ints_str . "'";
                                
                                $conditions[] = "(interest1 IN ({$ints_str}) OR interest2 IN ({$ints_str}) 
                                    OR interest3 IN ({$ints_str}) OR interest4 IN ({$ints_str}) 
                                    OR interest5 IN ({$ints_str}))";
                            }
                            
                            // if we have conditions, add it to our query, with ANDs in between
                            if (empty($conditions)) {
                                echo '<p>Uh-oh... No filters set...<p>';
                            } else {
                                $sql .= " WHERE " . implode(" AND ", $conditions) ." AND p.user_id != {$user_id};";
                                $result = $conn->query($sql);

                                //echo "done";
                                $ids = [];

                                while ($row = $result->fetch_assoc()) {
                                    $ids[] = $row["user_id"];
                                }
                                
                                if (empty($ids)) { // list empty
                                    echo '<p>Uh-oh... Couldn\'t fine any matches...<p>';
                                } else {
                                    $ids_str = implode(",", $ids);
                                    //echo $ids_str;
                                    
                                    // We query user details to print out
                                    $sql = "SELECT U.user_id, U.first_name, U.age, I.interest1, 
                                        I.interest2, P.profile_pic, C.username FROM 
                                        personal_info AS U
                                        INNER JOIN interests AS I
                                            ON U.user_id = I.user_id
                                        INNER JOIN images as P
                                            ON U.user_id = P.user_id
                                        INNER JOIN credentials as C
                                            ON U.user_id = C.user_id    
                                        WHERE U.user_id IN ({$ids_str});";
                                    
                                    $us_result = $conn->query($sql);

                                    $count = 0;
                                    while ($usr = $us_result->fetch_assoc()) {

                                        echo_user($usr, $user_id, $count);

                                        $count++; // count number of friend matches
                                    }
                                    $_SESSION["location"] = "search.php"; // to go back to home or search page
                                }
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>