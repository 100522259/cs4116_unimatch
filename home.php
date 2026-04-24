<?php
// Check if session is set, if not: go to login:
include 'session_check.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>unimatch - user profile page</title>
        <!--Bootstrap css-->
        
        <link href="css/profile.css" rel="stylesheet">
        <link href="css/profile_mobile.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com" /> 
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
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
                                title="Write target username" placeholder="Search users by username or first name..." 
                                required>
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
                    <div class="sub-match">
                        <div class="match-title">Friend matches!</div>
                        <div class="match-display">
                            <!-- Approx two users displayed in a scrollable section-->
                            <?php
                            session_start();

                            include "home_friends.php";
                            include_once "logic_blocked_user.php";

                            $count = 0;
                            while ($f_user = $f_result->fetch_assoc()) {
                                $pfp = './user/' . $f_user["username"] . '/' . $f_user["profile_pic"];

                                echo '<div class="user">';
                                echo '<img src="' . $pfp . '" alt="pfp">';
                                
                                echo '<p>'.$f_user["first_name"].', '.$f_user["age"].'</p>';

                                if ($f_user["interest1"] != null) {
                                    echo '<p>' . $f_user["interest1"].'</p>';
                                }
                                if ($f_user["interest2"] != null) {
                                    echo '<p>' . $f_user["interest2"] . '</p>';
                                }
                                
                                $friends = f_matched($user_id, $f_user["user_id"]);
                                // form for user to match
                                echo '<div class="dform">';
                                echo '<form name="f_match'.$count.'" action="logic_fmatch.php" method="post">';
                                echo '<input type="submit" name="f'.$count.'" value="';
                                if ($friends) echo 'Unfriend">';
                                else echo 'Friend">';
                                echo '<input type="hidden" name="target_id" value="'.$f_user["user_id"].'">';
                                echo '</form>';

                                // form for user to view profile
                                echo '<form name="r_match'.$count.'" action="user_view.php" method="post">';
                                echo '<input type="submit" name="v'.$count.'" value="View">';
                                echo '<input type="hidden" name="target_id" value="'.$f_user["user_id"].'">';
                                echo '</form>';
                                
                                echo '</div></div><br>';

                                $count++; // count number of friend matches
                            }
                            $_SESSION["location"] = "home.php"; // to go back to home or search page
                            ?>
                        </div>
                    </div>

                    <br><br>
                    
                    <div class="sub-match">
                        <div class="match-title">Date matches!</div>
                        <div class="match-display">
                            <!-- Approx two users displayed in a scrollable section-->
                            <?php
                            include "home_dates.php";
                            include_once "logic_blocked_user.php";

                            $count = 0;
                            while ($f_user = $f_result->fetch_assoc()) {
                                $pfp = './user/' . $f_user["username"] . '/' . $f_user["profile_pic"];

                                echo '<div class="user">';
                                echo '<img src="' . $pfp . '" alt="pfp">';

                                echo '<p>'.$f_user["first_name"].', '.$f_user["age"].'</p>';
                                if ($f_user["interest1"] != null) {
                                    echo '<p>' . $f_user["interest1"].'</p>';
                                }
                                if ($f_user["interest2"] != null) {
                                    echo '<p>' . $f_user["interest2"] . '</p>';
                                }

                                $dates = r_matched($user_id, $f_user["user_id"]);
                                // form for user to match
                                echo '<div class="dform">';
                                echo '<form name="r_match'.$count.'" action="logic_rmatch.php" method="post">';
                                echo '<input type="submit" name="r'.$count.'" value="';
                                if ($dates) echo 'Unmatch">';
                                else echo 'Match">';
                                echo '<input type="hidden" name="target_id" value="'.$f_user["user_id"].'">';
                                echo '</form>';

                                // form for user to view profile
                                echo '<form name="r_match'.$count.'" action="user_view.php" method="post">';
                                echo '<input type="submit" name="v'.$count.'" value="View">';
                                echo '<input type="hidden" name="target_id" value="'.$f_user["user_id"].'">';
                                echo '</form>';
                                echo '</div></div><br>';

                                $count++; // count number of friend matches
                            }
                            $_SESSION["location"] = "home.php"; // to go back to home or search page

                            ?>
                        </div>
                    </div>
                </div>
    
            </div>
        </div>
    </body>
</html>