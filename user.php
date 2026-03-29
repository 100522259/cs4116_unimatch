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
                        echo "<div class=\"icons\"><img src=\"/unimatch/images/{$icon}.png\"></div>";
                        echo "<div class=\"icons\"><p>{$icon}</p></div>";
                        echo "</div></a>";
                    }
                    ?>
                
                </div>
            </div>
            <!--Container for the rest of the page-->
            <div class="main">
                <div class="main_info">               
                    
                    <?php
                        require ("user_queries.php");
                        
                        // Div pfp
                        echo "<div class=\"pfp\">";
                        echo "<img src=\"/unimatch/images/{$images["profile_pic"]}\" alt=\"profile pic\">";
                        echo "</div>";
                        // close pfp

                        // Div info
                        echo "<div class=\"info\">";

                        // Div title
                        echo "<div class=\"user_title\">"; 
                        echo "<div class=\"name\"><h1>{$pers_info["first_name"]}</h1></div>";
                        echo "<div class=\"age\"><h4>{$pers_info["age"]}</h4></div></div>"; 
                        // close user title
                        
                        echo "<div class=\"bio\"><p>{$pers_info["bio"]}</p></div>";
                    
                        // Div interests (1)
                        echo "<div class=\"interests\">";
                        // Special interests boxes: display or not?
                        // if ($ints["drink_display"]) echo "<div class=\"int_box\">{$ints["drink"]}</div>";
                        // if ($ints["smoke_display"]) echo "<div class=\"int_box\">{$ints["smoke"]}</div>";
                        if ($ints["food_display"]) echo "<div class=\"int_box\">Food: {$ints["food_lifestyle"]}</div>";
                        if ($ints["personality_display"]) echo "<div class=\"int_box\">{$ints["personality"]}</div>";
                        if ($ints["sexuality_display"]) echo "<div class=\"int_box\">{$ints["sexuality"]}</div>";
                        echo "</div>";
                        // close interests (1)

                        // Div interests (2)
                        echo "<div class=\"interests\">";
                        // Display normal interests
                        // A query would be done here to obtain the interests
                        for ($i = 0; $i < 5; $i++) {
                            $num = $i + 1;
                            if ($ints["interest{$num}"] != null) {
                                echo "<div class=\"int_box\">{$ints["interest{$num}"]}</div>";
                            } 
                        }
                        echo "</div>"; 
                        // close interests (2)

                        // Div about uni
                        echo "<div class=\"about_uni\"";
                        echo "<>Degree: {$uni["course"]} --- ";
                        echo "Year: {$uni["c_year"]}</p>";
                        echo "</div>"; 
                        // close about uni

                        // Div interests (3)
                        echo "<div class=\"interests\">";
                        echo "<div class=\"int_box\">Gender: {$pers_info["gender"]}</div>";
                        echo "<div class=\"int_box\">Nationality: {$pers_info["nationality"]}</div>";
                        if ($pers_info["county"] != null) {
                            echo "<div class=\"int_box\">County: {$pers_info["county"]}</div>";
                        }
                        echo "</div>"; 
                        // Close interests (3)

                        echo "</div>";
                        // Close info
                    
                        // If user is an admin, this div will be visible:
                        if ($pers_info["administrator"]) {
                            // Div admin
                            echo "<div class=\"admin\">";

                            $admin_activities = array("report","block","edit bio","edit name","rmv photo",
                                "rmv interest","disable acc");
                                foreach ($admin_activities as $act) {
                                    echo "<div class=\"admin_activity\">";
                                    echo "<div class=\"admin_act\"><img src=\"images\\$act.png\"></div>";
                                    echo "<div class=\"admin_act\"><p>{$act}</p></div>";
                                    echo "</div>";
                                }
                            echo "</div>";
                            // Close admin
                        }
                    ?>

                </div> <!--Close main info -->
                <div class="my_images_txt">
                    <h1>My photos</h1>
                </div>
                <div class="my_images">
                <!--temp code; using php we'd request the number of photos and create as many
                    divs as photos available-->
                    <?php
                        require ("user_queries.php");
                        $num_photos = $images["pic_num"];
                        if ($num_photos == 0) echo "Oh, seems like you don't have any photos yet!";
                        for($i=0; $i < $num_photos; $i++) {
                            $num = $i+1;
                            echo "<div class=\"photo\">";
                            echo "<img src=\"/unimatch/images/{$images["pic_$num"]}\" alt=\"photo" . $num . "\">";
                            echo "</div>";
                        }
                        
                    ?>
                    
                </div>
            </div>
        </div>
    </body>
</html>