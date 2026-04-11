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
                    <div class="header">
                        <h1>Search</h1>
                    </div>
                    <div class="search">
                        <!--The following form will allow search via username or name
                            ONLY USERNAME FOR NOW!! -->
                        <form name="search" action="search.php" method="post" autocomplete="on">
                            <input type="text" name="uname" pattern="[A-Za-z0-9]{1,20}" maxlength="20"
                                size="50" title="Write target username" required placeholder="Search users...">
                            <input type="submit" name="search" value="Search">
                        </form>
                    </div>
                    <div class="filters">
                        Advanced Filters
                        <form name="filters" action="search.php" method="post" autocomplete="on">
                            <?php
                            // 1. gender
                            echo '<select name="gender">';
                            echo '<option value="gender" selected disabled>Gender</option>';
                            $gender = array("male", "female", "non-binary", "other");
                            foreach ($gender as $val) {
                                echo '<option value="'.$val.'">'.$val.'</option>';
                            }
                            echo '</select>';
                            
                            // 2. min age
                            echo '<label>Age range</label>';
                            echo '<select name="min_age">';
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

                            // 4. course
                            echo '<input type="text" name="course" pattern="[A-Za-z"]{2,20}
                                    maxlength="20" size="20" title="Write course you are looking for"
                                    placeholder="Filter by course...">';
                            
                            
                            // 5. Filter by up to 2 interests
                            $interest_ops = array("Sports", "Music", "Gaming", "Reading",
                            "Travel", "Cooking", "Fitness", "Photography", "Art", "Technology",
                            "Movies", "Fashion", "Nature", "Dance", "Writing");
                            echo '<label>Interests</label>';
                            for ($i=1; $i<3; $i++) {
                                echo '<select name="interest'.$i.'">';
                                echo '<option value="interest '.$i.'" selected disabled>Interest '.$i.'</option>';

                                foreach($interest_ops as $int) {
                                    echo '<option value="'.$int.'">'.$int.'</option>';
                                }
                                echo '</select>';
                            }
                            ?>

                            <input type="submit" name="filter" value="Apply Filters">
                            <br><br>
                        </form>
                    </div>
                </div>
                
                <div class="possible-match">
                    <div class="match-display">
                        <?php
                        include "connect_server.php";
                        session_start();
                        $user_id = $_SESSION["user_id"];

                        // A. search by username...
                        if (isset($_POST["search"])) {
                            echo "via username...<br><br>";
                            // check that the submitted value is not empty
                            
                            // Use normal sql string, a select doesn't have injection risk
                            $sql = "SELECT user_id FROM credentials where
                                LOWER(username) LIKE LOWER('%{$_POST["uname"]}%')
                                AND user_id != {$user_id};";

                            $result = $conn->query($sql);
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
                                    I.interest2, P.profile_pic FROM 
                                    personal_info AS U
                                    INNER JOIN interests AS I
                                        ON U.user_id = I.user_id
                                    INNER JOIN images as P
                                    ON U.user_id = P.user_id
                                    WHERE U.user_id IN ({$ids_str});";
                                
                                $us_result = $conn->query($sql);

                                $count=0;
                                while ($usr = $us_result->fetch_assoc()) {

                                    echo '<div class="user">';
                                    echo '<img src="./images/small_pfp.png" alt="pfp"><br>'; // not real pfp stored, will work on that
                                    echo '<p>'.$usr["first_name"].', '.$usr["age"].'</p>';
                                    echo '<p>';
                                    if ($usr["interest1"] != null) {
                                        echo $usr["interest1"].' ';
                                    }
                                    if ($usr["interest2"] != null) {
                                        echo ' - '.$usr["interest2"];
                                    }
                                    echo '</p>';
                                    // form for user to friend-match
                                    echo '<form name="f_match'.$count.'" action="logic_fmatch.php" method="post">';
                                    echo '<input type="submit" name="f'.$count.'" value="Friend">';
                                    echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
                                    echo '</form>';
                                    
                                    // form for user to date-match
                                    echo '<form name="r_match'.$count.'" action="logic_rmatch.php" method="post">';
                                    echo '<input type="submit" name="r'.$count.'" value="Match">';
                                    echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
                                    echo '</form>';
                                    
                                    // form for user to view profile
                                    echo '<form name="r_match'.$count.'" action="user_view.php" method="post">';
                                    echo '<input type="submit" name="v'.$count.'" value="View">';
                                    echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
                                    echo '</form>';
                                    echo '</div><br>';

                                    $count++; // count number of friend matches
                                }
                                $_SESSION["location"] = "search.php"; // to go back to home or search page
                            }
                               
                        }

                        // B. Filter
                        if (isset($_POST["filter"])) {
                            echo "via filter...<br><br>";
                            // generate the sql query, that will have more or less conditons
                            // based on the filters selected

                            // 1. taking from personal info
                            $sql = "SELECT p.user_id FROM personal_info AS p 
                                INNER JOIN interests AS i
                                    ON p.user_id = i.user_id 
                                INNER JOIN academic_info AS a
                                    ON p.user_id = a.user_id"; 
                            
                            $conditions = []; // variable to store the string sql conditions
                            if (isset($_POST["gender"])) {
                                // add according "where statement
                                $conditions[] = "gender LIKE '{$_POST["gender"]}'";
                            }
                            if (isset($_POST["min_age"])) {
                                $conditions[] = "age >= {$_POST["min_age"]}";
                            }
                            if (isset($_POST["max_age"])) {
                                $conditions[] = "age <= {$_POST["max_age"]}";
                            }
                            if ($_POST["course"] != "") {
                                $conditions[] = "LOWER(course) LIKE LOWER('%{$_POST["course"]}%')";
                            }
                            
                            $ints = []; // to store interests
                            if (isset($_POST["interest1"])) {
                                $ints[] = $_POST["interest1"];
                            }
                            if (isset($_POST["interest2"])) {
                                $ints[] = $_POST["interest2"];
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
                                        I.interest2, P.profile_pic FROM 
                                        personal_info AS U
                                        INNER JOIN interests AS I
                                            ON U.user_id = I.user_id
                                        INNER JOIN images as P
                                        ON U.user_id = P.user_id
                                        WHERE U.user_id IN ({$ids_str});";
                                    
                                    $us_result = $conn->query($sql);

                                    $count = 0;
                                    while ($usr = $us_result->fetch_assoc()) {

                                        echo '<div class="user">';
                                        echo '<img src="./images/small_pfp.png" alt="pfp"><br>'; // not real pfp stored, will work on that
                                        echo '<p>'.$usr["first_name"].', '.$usr["age"].'</p>';
                                        echo '<p>';
                                        if ($usr["interest1"] != null) {
                                            echo $usr["interest1"].' ';
                                        }
                                        if ($usr["interest2"] != null) {
                                            echo ' - '.$usr["interest2"];
                                        }
                                        echo '</p>';
                                        // form for user to friend-match
                                        echo '<form name="f_match'.$count.'" action="logic_fmatch.php" method="post">';
                                        echo '<input type="submit" name="f'.$count.'" value="Friend">';
                                        echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
                                        echo '</form>';

                                        // form for user to date-match
                                        echo '<form name="r_match'.$count.'" action="logic_rmatch.php" method="post">';
                                        echo '<input type="submit" name="r'.$count.'" value="Match">';
                                        echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
                                        echo '</form>';

                                        // form for user to view profile
                                        echo '<form name="r_match'.$count.'" action="user_view.php" method="post">';
                                        echo '<input type="submit" name="v'.$count.'" value="View">';
                                        echo '<input type="hidden" name="target_id" value="'.$usr["user_id"].'">';
                                        echo '</form>';

                                        echo '</div><br>';

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