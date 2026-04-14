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
                <div class="main_info">               
                <?php
                session_start();
                if (isset($_POST["target_id"])) {
                    $_SESSION["target_id"] = $_POST["target_id"];;
                    $_SESSION["location"] = "user_view.php";
                }
                $target_id = $_SESSION["target_id"];
                include "./user_view_queries.php";
                include "./logic_blocked_user.php";
                $blocked = blocked_user($sess_id, $target_id); // sess id comde from view queries
                
                // blocked  admin   no display
                //    0     0 -> 1        0
                //    0     1 -> 0        0
                //    1     0 -> 1        1
                //    1     1 -> 0        0
                $no_display = $blocked && !$admin;

                $banned = banned_user($target_id);
                $path_images = "/unimatch/user/" . $creds["username"] . "/";
                
                // Div pfp
                echo "<div class=\"pfp\">";
                echo '<img src="' . $path_images . $images["profile_pic"] . '" alt="profile pic">';
                echo "</div>";
                // close pfp

                // Div info
                echo "<div class=\"info\">";

                // Div title
                echo "<div class=\"user_title\">"; 
                echo "<div class=\"name\"><h1>{$pers_info["first_name"]}</h1></div>";
                echo "<div class=\"age\"><h4>{$pers_info["age"]}</h4></div></div>"; 
                // close user title
                
                if ($blocked && !$admin) echo "<div class=\"bio\">...</div>";
                else echo "<div class=\"bio\"><p>{$pers_info["bio"]}</p></div>";
            
                // Div interests (1)
                echo "<div class=\"interests\">";
                if ($blocked && !$admin) {
                    echo "<div class=\"int_box\">???</div>";
                } else {
                    // Special interests boxes: display or not?
                    if ($ints["food_display"]) echo "<div class=\"int_box\">Food: {$ints["food_lifestyle"]}</div>";
                    if ($ints["personality_display"]) echo "<div class=\"int_box\">{$ints["personality"]}</div>";
                    if ($ints["sexuality_display"]) echo "<div class=\"int_box\">{$ints["sexuality"]}</div>";
                }
                echo "</div>";
                // close interests (1)

                // Div interests (2)
                echo "<div class=\"interests\">";
                // Display normal interests
                // A query would be done here to obtain the interests
                for ($i = 0; $i < 5; $i++) {
                    $num = $i + 1;
                    if ($blocked && !$admin) {
                        echo "<div class=\"int_box\">???</div>"; 
                        break;
                    }
                    elseif ($ints["interest{$num}"] != null) {
                        echo "<div class=\"int_box\">{$ints["interest{$num}"]}</div>";
                    } 
                }
                echo "</div>"; 
                // close interests (2)

                // Div about uni
                echo "<div class=\"about_uni\"";
                
                if ($blocked && !$admin) {
                    echo "<p>...</p>";
                }
                else {
                    echo "<p>Degree: {$uni["course"]} --- ";
                    echo "Year: {$uni["c_year"]}</p>";
                }
                echo "</div>"; 
                // close about uni

                // Div interests (3)
                echo "<div class=\"interests\">";
                if ($blocked && !$admin) echo "<div class=\"int_box\">???</div>";
                else {
                    echo "<div class=\"int_box\">Gender: {$pers_info["gender"]}</div>";
                    echo "<div class=\"int_box\">Nationality: {$pers_info["nationality"]}</div>";
                    if ($pers_info["county"] != null) {
                        echo "<div class=\"int_box\">County: {$pers_info["county"]}</div>";
                    }
                }
                echo "</div>"; 
                // Close interests (3)

                echo "</div>";
                // Close info
                

                // Div admin
                echo "<div class=\"admin\">";

                $act_user = array("Friend", "Match", "Block");
                $act_admin = array(3=>"Ban", "Edit");

                $act_php = array("logic_fmatch.php", "logic_rmatch.php", 
                    "logic_block.php", "logic_ban.php", "settings_admin.php");
                $act_icons = array("friends", "matches", "block", "disable acc", "edit");
                
                foreach ($act_user as $key=>$act) {
                    echo '<div class="admin_activity">';
                    
                    echo '<div class="admin_act"><img src="images\\'.$act_icons[$key].'.png"></div>';
                    // form:
                    echo '<form name="'.$sct.'" action="'.$act_php[$key].'" method="post">';
                    echo '<input type="submit" name="'.$act.'" value="';
                    if ($act == "Block" && $blocked) {
                        // if we are displaying the blocked button and user is blocked, write Unblock
                        echo "Unblock";
                    } else echo $act; // otherwise, display activity as appears in array
                    echo '">';
                    echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                    echo '</form>';
                    echo '</div>';
                }

                // report is special (has more fields)
                $report_categories = array("Harassment", "Fake Profile", "Spam", "Inappropriate Messages",
                    "Inappropriate Photos", "Hate Speech", "Bullying", "Underage User", "Impersonation",
                    "Threats / Violence", "Suspicious Behavior", "Other");

                echo '<div class="admin_activity">';
                echo '<div class="admin_act"><img src="images\report.png"></div>';
                // form:
                echo '<form name="report" action="logic_report.php" method="post">';
                echo '<fieldset>';
                // section for user to select category---required---; and message
                echo '<select name="category" required>';
                echo '<option value="" selected disabled>Category</option>';
                foreach ($report_categories as $cat) {
                    echo '<option value="'.$cat.'">'.$cat.'</option>';
                }
                echo '</select><br>';
                echo '<input type=text name="msg" required pattern="[^;]*" minlength="10" maxlength="200" size=20><br>';
                echo '<input type="submit" name="report" value="Report">';
                echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                echo '</fieldset></form>';
                echo '</div>';
                
                // if user is admin, more options will appear:
                if ($admin) {
                    foreach ($act_admin as $key=>$act) {
                        echo '<div class="admin_activity">';
                        
                        echo '<div class="admin_act"><img src="images\\'.$act_icons[$key].'.png"></div>';
                        // form:
                        echo '<form name="'.$act.'" action="'.$act_php[$key].'" method="post">';
                        echo '<input type="submit" name="'.$act.'" value="';
                        if ($act == "Ban" && $banned) {
                            // if we are displaying the blocked button and user is blocked, write Unblock
                            echo "Unban";
                        } else echo $act;
                        echo '">';
                        echo '<input type="hidden" name="target_id" value="'.$target_id.'">';
                        echo '</form>';
                        echo '</div>';
                    }
                }

                echo "</div>";
                // Close admin
                
                
                ?>

                </div> <!--Close main info -->
                <div class="my_images_txt">
                    <h1>My photos</h1>
                </div>
                <div class="my_images">
                <!--temp code; using php we'd request the number of photos and create as many
                    divs as photos available-->
                    <?php
                        require ("user_view_queries.php");

                        // only display as many photos as session user has; except if user is admin
                        if (!$admin) {
                            // display min between target user and session user number of photos
                            $num_photos = min($images["pic_num"], $sess_num_images);
                            if ($blocked) $num_photos = 0;
                        } else $num_photos = $images["pic_num"];
                        
                        if ($num_photos == 0) echo "Oh, seems like {$pers_info["first_name"]} doesn't have any photos yet!";
                        for($i=0; $i < $num_photos; $i++) {
                            $num = $i+1;
                            echo '<div class="photo">';
                            echo '<img src="' . $path_images . $images["pic_{$num}"] . '" alt="photo' . $num . '">';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>