<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>unimatch - admin page</title>

        <!--Bootstrap css--
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet">
        <link rel="stylesheet" 
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap-grid.min.css" 
            integrity="sha512-dOjUSaLkr6G2pwQ7ry9juX+iXw5602zg1kg8yH+guR3uSEidGyCnOEQnGlr7xwu/8WE+pVm1ZNqaIs5ETTIJQg==" 
            crossorigin="anonymous" referrerpolicy="no-referrer"/>-->

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

                <form name="credentials_settings" action="settings.php" method="post" autocomplete="off">
                    <fieldset><legend>Credentials</legend><br>
                        <?php
                        include "user_queries.php";
                        
                        echo '<label for="username">Username: </label>';
                        echo '<input type="text" name="username" pattern="[A-Za-z0-9]{5,}" maxlength="20" 
                                size="20" title="Write your username, between 5 and 20 characters" 
                                value="'.$creds["username"].'">';
                        echo '<br><br>';

                        echo '<label for="password">Password: </label>';
                        echo '<input type="password" name="password" id="myPsw" pattern="[^;]+" minlength="8" maxlength="20" 
                        		size="20" title="Write your password, between 8 and 20 characters"
                                value="'.$creds["password"].'">';
                        echo '<br>';

                        echo '<label for="visibility">Show Password </label>';
                        echo '<input type="checkbox" name="visibility" onclick="toggleVisib()">';
                        echo '<br><br>';

                        echo '<label for="password2">Confirm new Password: </label>';
                        echo '<input type="password" name="password2" id="myPsw2" pattern="[^;]+" minlength="8" maxlength="20" size="20" 
                        		title="Write your password again, between 8 and 20 characters">';
                        echo '<br>';

                        echo '<label for="visibility2">Show Password </label>';
                        echo '<input type="checkbox" name="visibility2" onclick="toggleVisib2()">';
                        echo '<br><br>';

                        ?>

                    </fieldset>
                    <input type="submit" name="submit_cred" value="Save Changes">
                </form><br><br>


                <!--Form for the user to change their user details-->
                <form name="basic_settings" action="" method="post" autocomplete="off">
                    <fieldset><legend>Personal Info</legend><br>
                    <?php
                        include "user_queries.php";

                        echo '<label for="fname">First Name: </label>';
                        echo '<input type="text" name="fname" pattern="[A-Za-z]{2,30}" maxlength="30" 
                                size="30" title="Write your first name, between 2 and 30 characters"
                                value="'.$pers_info["first_name"].'">';
                        echo '<br><br>';

                        echo '<label for="lname">Last name: </label>';
                        echo '<input type="text" name="lname" pattern="[A-Za-z]{2,30}" maxlength="30" 
                                size="30" title="Write your last name, between 2 and 30 characters"
                                value="'.$pers_info["last_name"].'">';
                        echo '<br><br>';

                        echo '<label for="age">Age: </label>';
                        echo '<select id="age" name="age">';
                        echo '<option value="'.$pers_info["age"].'" >'.$pers_info["age"].'</option>';
                        
                        for ($i = 17; $i <= 30; $i++) {
                            echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                        echo '</select>';
                        echo '<br><br>';

                        echo '<label for="county">County:</label>';
                        echo '<input type="text" name="county" pattern="[A-Za-z]{2,30}" maxlength="30" 
                                size="30" title="Write your county, between 2 and 30 characters"
                                value="'.$pers_info["county"].'">';
                        echo '<br><br>';

                        echo '<label for="nationality">Nationality: </label>';
                        echo '<input type="text" name="nationality" pattern="[A-Za-z]{2,30}" maxlength="30" 
                                size="30" title="Write your nationality, between 2 and 30 characters"
                                value="'.$pers_info["nationality"].'">';
                        echo '<br><br>';

                        echo '<label for="gender">Gender: </label>';
                        echo '<select id="gender" name="gender">';
                        echo '<option value="'.$pers_info["gender"].'" selected>'.$pers_info["gender"].'</option>';
                        echo '<option value="male">Male</option>';
                        echo '<option value="female">Female</option>';
                        echo '<option value="non-binary">Non-binary</option>';
                        echo '<option value="prefer-not-to-say">Prefer not to say</option>';
                        echo '</select>';
                        echo '<br><br>';

                        echo '<label for="bio">Bio: </label><br>';
                        echo '<textarea name="bio" rows="5" cols="50" pattern="[^;]*" maxlength="2500" size="100"
                                title="Write a short bio">';
                        echo $pers_info["bio"];
                        echo "</textarea>";
                        echo '<br><br>';
                    ?>
                    </fieldset>
                    <input type="submit" name="submit_pers" value="Save Changes">
                </form><br><br>


                <form name="academic_info" action="" method="post" autocomplete="off">
                    <fieldset><legend>Academic Info</legend><br>
                    <?php
                        include "user_queries.php";

                        echo '<label for="course">Course: </label>';
                        echo '<input type="text" name="course" pattern="[A-Za-z ]{2,20}" maxlength="20" 
                                size="20" title="Write your course, between 2 and 20 characters"
                                value="'.$uni["course"].'">';
                        echo '<br><br>';

                        echo '<label for="year">Year: </label>';
                        echo '<input type="text" name="year" pattern="[1-9]" maxlength="1" size="5" 
                                title="Write your course year, between 1 and 9"
                                value="'.$uni["c_year"].'">';
                        echo '<br><br>';
                    ?>
                    </fieldset>
                    <input type="submit" name="submit_acad" value="Save Changes">
                </form><br><br>


                <?php // here we process the forms:
                    include "connect_server.php";
                    include "user_queries.php";
                    
                    // 1. FORM CREDENTIALS
                    if(isset($_POST["submit_cred"])) {

                        // a. Check that username is changed
                        if ($_POST["username"] != $creds["username"]) {
                            $stmt = $conn->prepare("UPDATE credentials SET username = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("s", $_POST["username"]);
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

                    // refresh for clean update of the form    
                    header("Refresh:0");
                    }

                    // 2. FORM PERSONAL INFO
                    if (isset($_POST["submit_pers"])) {
                        // a. First name:
                        if ($_POST["fname"] != $pers_info["first_name"]) {
                            $stmt = $conn->prepare("UPDATE personal_info SET first_name = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("s", $_POST["fname"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }

                        // b. Last name:
                        if ($_POST["lname"] != $pers_info["last_name"]) {
                            $stmt = $conn->prepare("UPDATE personal_info SET last_name = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("s", $_POST["lname"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }

                        // c. Age
                        if ($_POST["age"] != $pers_info["age"]) {
                            $stmt = $conn->prepare("UPDATE personal_info SET age = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("i", $_POST["age"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }
                        
                        // d. County
                        if ($_POST["county"] != $pers_info["county"]) {
                            //if ($_POST["county"] == "") $_POST["county"] = null;
                            $stmt = $conn->prepare("UPDATE personal_info SET county = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("s", $_POST["county"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }

                        // e. nationality
                        if ($_POST["nationality"] != $pers_info["nationality"]) {
                            $stmt = $conn->prepare("UPDATE personal_info SET nationality = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("s", $_POST["nationality"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }

                        // f. gender
                        if ($_POST["gender"] != $pers_info["gender"]) {
                            $stmt = $conn->prepare("UPDATE personal_info SET gender = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("s", $_POST["gender"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }

                        // g. bio (would be too expensive to compare, just refresh)
                        $stmt = $conn->prepare("UPDATE personal_info SET bio = ? WHERE user_id = {$user_id};");
                        $stmt->bind_param("s", $_POST["bio"]);
                        if ($stmt === false) echo "Something bad happened :( <br>";
                        $stmt->execute();
                        

                    // Refresh page
                    header("Refresh:0");
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
                        if ($_POST["year"] != $uni["c_year"]) {
                            $stmt = $conn->prepare("UPDATE academic_info SET c_year = ? WHERE user_id = {$user_id};");
                            $stmt->bind_param("i", $_POST["year"]);
                            if ($stmt === false) echo "Something bad happened :( <br>";
                            $stmt->execute();
                        }

                        header("Refresh:0");
                    }

                    // 4. FORM INTERESTS
                    if (isset($_POST["submit_int"])) {
                        $interests = array("drink", "smoke", "food_lifestyle", "food_display", 
                            "personality", "personality_display", "sexuality", "sexuality_display", 
                            "interest1", "inserest2", "interest3", "interest4", "interest5");
                        // TODO: DISPLAY BOX NOW WORKING
                        foreach($interests as $val) {
                            if ($_POST[$val] != $uni[$val]) {
                                $stmt = $conn->prepare("UPDATE interests SET {$val} = ? WHERE user_id = {$user_id};");
                                $stmt->bind_param("s", $_POST[$val]);
                                if ($stmt === false) echo "Something bad happened :( <br>";
                                $stmt->execute();
                            }
                        }

                        header("Refresh:0");
                    }
                    
                ?>


                <form name="interests" action="" method="post" autocomplete="off">
                    <fieldset><legend>Interests</legend><br>
                    <?php
                        include "user_queries.php";

                        
                        echo '<label for="drink">Drinking habits: </label>';
                        echo '<select name="drink">';
                        echo '<option value="'.$ints["drink"].'" selected>'.$ints["drink"].'</option>';
                        echo '<option value="no">No</option>';
                        echo '<option value="yes">Yes</option>';
                        echo '<option value="socially">Socially</option>';
                        echo '<option value="occasionally">Occasionally</option>';
                        echo '</select>';
                        echo '<br><br>';

                        echo '<label for="smoke">Smoking habits: </label>';
                        echo '<select name="smoke">';
                        echo '<option value="'.$ints["smoke"].'" selected>'.$ints["drink"].'</option>';
                        echo '<option value="no">No</option>';
                        echo '<option value="yes">Yes</option>';
                        echo '<option value="socially">Socially</option>';
                        echo '<option value="occasionally">Occasionally</option>';
                        echo '</select>';
                        echo '<br><br>';

                        echo '<label for="food">Food Lifestyle: </label>';
                        echo '<select name="food">';
                        echo '<option value="'.$ints["food_lifestyle"].'" selected>'.$ints["food_lifestyle"].'</option>';
                        echo '<option value="normal">Normal</option>';
                        echo '<option value="vegetarian">Vegetarian</option>';
                        echo '<option value="vegan">Vegan</option>';
                        echo '<option value="pescatarian">Pescatarian</option>';
                        echo '<option value="other">Other</option>';
                        echo '</select>';
                        echo '<br>';
                        echo '<label for="disp_lifestyle">Display: </label>';
                        echo '<input type="checkbox" name="disp_lifestyle">';
                        echo '<br><br>';

                        echo '<label for="personality">Personality: </label>';
                        echo '<select name="personality">';
                        echo '<option value="'.$ints["personality"].'" selected>'.$ints["personality"].'</option>';
                        echo '<option value="extrovert">Extrovert</option>';
                        echo '<option value="introvert">Introvert</option>';
                        echo '<option value="ambivert">Ambivert</option>';
                        echo '</select>';
                        echo '<br>';
                        echo '<label for="disp_personality">Display: </label>';
                        echo '<input type="checkbox" name="disp_personality">';
                        echo '<br><br>';

                        echo '<label for="sexuality">Sexuality: </label>';
                        echo '<select name="sexuality">';
                        echo '<option value="'.$ints["sexuality"].'" selected>'.$ints["sexuality"].'</option>';
                        echo '<option value="straight">Straight</option>';
                        echo '<option value="gay">Gay</option>';
                        echo '<option value="lesbian">Lesbian</option>';
                        echo '<option value="bisexual">Bisexual</option>';
                        echo '<option value="pansexual">Pansexual</option>';
                        echo '<option value="asexual">Asexual</option>';
                        echo '<option value="other">Other</option>';
                        echo '</select>';
                        echo '<br>';
                        echo '<label for="disp_sexuality">Display: </label>';
                        echo '<input type="checkbox" name="disp_sexuality">';
                        echo '<br><br>';

                        // Section for interest, repeated five times
                        $interest_ops = array("Sports", "Music", "Gaming", "Reading",
                        "Travel", "Cooking", "Fitness", "Photography", "Art", "Technology",
                        "Movies", "Fashion", "Nature", "Dance", "Writing");
                        for ($i=1; $i<6; $i++) {
                            echo '<label for="interest'.$i.'">Interest '.$i.': </label>';
                            echo '<select name="interest'.$i.'">';
                            echo '<option value="'.$ints["interest{$i}"].'" selected>'.$ints["interest{$i}"].'</option>';
                            foreach ($interest_ops as $int) {
                                echo '<option value="'.$int.'">'.$int.'</option>';
                            }
                            echo '</select><br><br>';
                        }
                        ?>                      
                        
                    </fieldset>
                    <input type="submit" name="submit_int" value="Save Changes">
                </form><br><br>

                <form>
                    <fieldset><legend>Image Upload</legend><br>
                    <?php
                        echo '<label for="pfp">Upload Profile Photo: </label>';
                        echo '<input type="file" name="pfp"><br><br>';
                    
                        for ($i=1; $i<6; $i++) {
                            echo '<label for="image'.$i.'">Upload Image '.$i.': </label>';
                            echo '<input type="file" name="image'.$i.'"><br><br>';
                        }
                    ?>
                    </fieldset>
                </form><br><br>
            </div>
        </div>
        
        
        <script>
            // Makes password visible on toggle -- taken from w3schools tutorial
            function toggleVisib() {
                var x = document.getElementById("myPsw");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }

            function toggleVisib2() {
                var x = document.getElementById("myPsw2");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
        </script>
    </body>
</html>