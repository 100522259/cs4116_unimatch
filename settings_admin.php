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
            <?php 
                include "./sidebar.php"; 
                //echo $_SESSION["target_id"];
            ?>

            
            <!--Container for the rest of the page-->
            <div class="main">
                <?php
                    // coming from user_view file: button to return to it...
                    if (isset($_POST["target_id"])) {
                        $_SESSION["target_id"] = $_POST["target_id"];
                    }
                    $target_id = $_SESSION["target_id"];

                    echo '<form action="user_view.php" method="post"">';
                    // so target is not lost...
                    echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                    echo '<input type="submit" name="return" value="Return to User View">';
                    echo '</form><br>';
                    
                ?>
                <!--Password cannot be modified by admin-->
                <form class="settings" name="credentials_settings" action="settings_handler.php" method="post" autocomplete="off">
                    <fieldset><legend>Credentials</legend><br>
                        <?php
                        include "user_view_queries.php";
                        echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                        echo '<label for="username">Username: </label>';
                        echo '<input id="username" type="text" name="username" pattern="^[A-Za-z0-9._\-]{5,20}$" maxlength="20" 
                                size="20" title="Write your username, between 5 and 20 characters" 
                                value="'.$creds["username"].'">';
                        echo '<br><br>';
                        ?>
                    </fieldset>
                    <input type="submit" name="submit_cred" value="Save Changes">
                </form><br><br>

                <!--Form for the user to change their user details-->
                <form class="settings" name="basic_settings" action="settings_handler.php" method="post" autocomplete="off">
                    <fieldset><legend>Personal Info</legend><br>
                    <?php
                        // ADMIN CAN EDIT USER PROFILE, DETERMINED BY SUBMIT "EDIT"
                        // see user_view.php - line 174 !!
                        
                        // hiden input with target id to also let handler know we're not working on sess user
                        echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                            

                        echo '<label for="first_name">First Name: </label>';
                        echo '<input type="text" name="first_name" pattern="[A-Za-z]{2,30}" maxlength="30" 
                                size="30" title="Write your first name, between 2 and 30 characters"
                                value="'.$pers_info["first_name"].'">';
                        echo '<br><br>';

                        echo '<label for="last_name">Last name: </label>';
                        echo '<input type="text" name="last_name" pattern="[A-Za-z]{2,30}" maxlength="30" 
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

                        $gender = array("Male", "Female", "Non-Binary", "Other");
                        echo '<label for="gender">Gender: </label>';
                        echo '<select name="gender">';
                        echo '<option value="'.$pers_info["gender"].'" selected>'.$pers_info["gender"].'</option>';
                        foreach($gender as $val) {
                            echo '<option value="'.$val.'">'.$val.'</option>';
                        }
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


                <form class="settings" name="academic_info" action="settings_handler.php" method="post" autocomplete="off">
                    <fieldset><legend>Academic Info</legend><br>
                    <?php
                        // ADMIN CAN EDIT USER PROFILE, DETERMINED BY SUBMIT "EDIT"
                        // see user_view.php - line 174 !!
                        include "user_view_queries.php";
                        // hiden input with target id to also let handler know we're not working on sess user
                        echo '<input type="hidden" name="target_id" value="'.$target_id.'">';

                        echo '<label for="course">Course: </label>';
                        echo '<input type="text" name="course" pattern="[A-Za-z ]{2,20}" maxlength="20" 
                                size="20" title="Write your course, between 2 and 20 characters"
                                value="'.$uni["course"].'">';
                        echo '<br><br>';

                        echo '<label for="c_year">Year: </label>';
                        echo '<input type="text" name="c_year" pattern="[1-9]" maxlength="1" size="5" 
                                title="Write your course year, between 1 and 9"
                                value="'.$uni["c_year"].'">';
                        echo '<br><br>';
                    ?>
                    </fieldset>
                    <input type="submit" name="submit_acad" value="Save Changes">
                </form><br><br>

                <form class="settings" name="interests" action="settings_handler.php" method="post" autocomplete="off">
                    <fieldset><legend>Interests</legend><br>
                    <?php
                        // ADMIN CAN EDIT USER PROFILE, DETERMINED BY SUBMIT "EDIT"
                        // see user_view.php - line 174 !!
                        include "user_view_queries.php";
                        // hiden input with target id to also let handler know we're not working on sess user
                        echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                        
                        $opts = array("No", "Yes", "Socially", "Occasionally");
                        echo '<label for="drink">Drinking habits: </label>';
                        echo '<select id="drink" name="drink">';
                        echo '<option value="'.$ints["drink"].'" selected>'.$ints["drink"].'</option>';
                        foreach($opts as $val) {
                            echo '<option value="'.$val.'">'.$val.'</option>';    
                        }
                        echo '</select>';
                        echo '<br><br>';

                        echo '<label for="smoke">Smoking habits: </label>';
                        echo '<select id="smoke" name="smoke">';
                        echo '<option value="'.$ints["smoke"].'" selected>'.$ints["smoke"].'</option>';
                        foreach($opts as $val) {
                            echo '<option value="'.$val.'">'.$val.'</option>';    
                        }
                        echo '</select>';
                        echo '<br><br>';

                        $food = array("Normal", "Vegetarian", "Vegan", "Pescatarian", "Other");
                        echo '<label for="food">Food Lifestyle: </label>';
                        echo '<select id="food" name="food_lifestyle">';
                        echo '<option value="'.$ints["food_lifestyle"].'" selected>'.$ints["food_lifestyle"].'</option>';
                        foreach($food as $val) {
                            echo '<option value="'.$val.'">'.$val.'</option>';
                        }
                        echo '</select>';
                        echo '<br>';
                        echo '<label for="food_display">Display: </label>';
                        echo '<input id="food_display" type="checkbox" checked name="food_display" value="true">';
                        echo '<br><br>';

                        echo '<label for="personality">Personality: </label>';
                        echo '<select id="personality" name="personality">';
                        echo '<option value="'.$ints["personality"].'" selected>'.$ints["personality"].'</option>';
                        echo '<option value="Extrovert">Extrovert</option>';
                        echo '<option value="Introvert">Introvert</option>';
                        echo '<option value="Ambivert">Ambivert</option>';
                        echo '</select>';
                        echo '<br>';
                        echo '<label for="personality_display">Display: </label>';
                        echo '<input id="personality_display" type="checkbox" checked name="personality_display" value="true">';
                        echo '<br><br>';

                        $sexuality = array("Straight", "Gay", "Lesbian", "Bisexual", "Pansexual", "Asexual", "Other");
                        echo '<label for="sexuality">Sexuality: </label>';
                        echo '<select id="sexuality" name="sexuality">';
                        echo '<option value="'.$ints["sexuality"].'" selected>'.$ints["sexuality"].'</option>';
                        foreach($sexuality as $val) {
                            echo '<option value="'.$val.'">'.$val.'</option>';
                        }
                        echo '</select>';
                        echo '<br>';
                        echo '<label for="sexuality_display">Display: </label>';
                        echo '<input id0"sexuality_display" type="checkbox" checked name="sexuality_display" value="true">';
                        echo '<br><br>';
                        // Section for interest, repeated five times
                        $interest_ops = array("Sports", "Music", "Gaming", "Reading",
                        "Travel", "Cooking", "Fitness", "Photography", "Art", "Technology",
                        "Movies", "Fashion", "Nature", "Dance", "Writing", null);
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

                <form class="settings" name="images" action="settings_handler.php" method="post" autocomplete="off"
                        enctype="multipart/form-data">
                    <fieldset><legend>Image Settings</legend><br>
                    <?php       
                        // hiden input with target id to also let handler know we're not working on sess user
                        echo '<input type="hidden" name="target_id" value="'.$target_id.'">';

                        echo '<label for="pfp">Upload Profile Photo: </label>';
                        echo '<input id="pfp" type="file" name="profile_pic" accept=".gif, .jpg, .jpeg, .png"><br><br>';
                        //echo '<input type="submit" name="rmv'.$i.'" value="Remove image"><br><br>';
                        // not actual removal, but sets an empty photo??
                    
                        for ($i=1; $i<6; $i++) {
                            echo '<label for="image'.$i.'">Manage Image '.$i.': </label>';

                            if ($images["pic_$i"] != null) {
                            echo '<input type="submit" name="rmv'.$i.'" value="Remove image">';
                            } else echo 'No image uploaded!';
                            echo '<br><br>';
                            
                        }
                    ?>
                    </fieldset>
                    <input type="submit" name="submit_img" value="Save Changes">
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