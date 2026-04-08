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
            <div class="index">
                <div class="logo">
                    <img class="logo" src="images\logo5.png">
                </div>
                <div class="idx_icons">
                    <?php
                    $icons = array("home", "dates", "friends","chat",
                                    "user","settings","admin");
                    foreach ($icons as $icon) {
                        echo "<a href=\"{$icon}.php\">";    
                        echo "<div class=\"pages\">";
                        echo "<div class=\"icons\"><img src=\"images\\{$icon}.png\"></div>";
                        echo "<div class=\"icons\"><p>{$icon}</p></div>";
                        echo "</div></a>";
                    }
                    ?>
                
                </div>
            </div>
            
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
                                size="50" title="Write target username" placeholder="Search users...">
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
                    <div class="sub-match">
                        <div class="match-title">See your possible friend matches!</div>
                        <div class="match-display">
                            <!-- Approx two users displayed in a scrollable section-->
                            <?php
                            include "home_friends.php";

                            while ($f_user = $f_result->fetch_assoc()) {
                                echo '<div class="user">';
                                echo '<img src="./images/small_pfp.png" alt="pfp"><br>'; // not real pfp stored, will work on that
                                echo '<p>'.$f_user["first_name"].', '.$f_user["age"].'</p>';
                                echo '<p>';
                                if ($f_user["interest1"] != null) {
                                    echo $f_user["interest1"].' ';
                                }
                                if ($f_user["interest2"] != null) {
                                    echo $f_user["interest2"];
                                }
                                echo '</p>';
                                echo '</div>';
                            }

                            ?>
                        </div>
                    </div>

                    <br><br>
                    
                    <div class="sub-match">
                        <div class="match-title">See your possible date matches!</div>
                        <div class="match-display">
                            <!-- Approx two users displayed in a scrollable section-->
                            <?php
                            include "home_dates.php";

                            while ($f_user = $f_result->fetch_assoc()) {
                                echo '<div class="user">';
                                echo '<img src="./images/small_pfp.png" alt="pfp"><br>'; // not real pfp stored, will work on that
                                echo '<p>'.$f_user["first_name"].', '.$f_user["age"].'</p>';
                                echo '<p>';
                                if ($f_user["interest1"] != null) {
                                    echo $f_user["interest1"].' ';
                                }
                                if ($f_user["interest2"] != null) {
                                    echo $f_user["interest2"];
                                }
                                echo '</p>';
                                echo '</div><br><br>';
                            }

                            ?>
                        </div>
                    </div>
                </div>
    
            </div>
        </div>
    </body>
</html>