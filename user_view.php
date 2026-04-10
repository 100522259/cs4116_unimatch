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
                    include "user_queries.php";

                    $icons = array("home", "matches","chat",
                                    "user","settings");
                    foreach ($icons as $icon) {
                        echo "<a href=\"{$icon}.php\">";    
                        echo "<div class=\"pages\">";
                        echo "<div class=\"icons\"><img src=\"images\\{$icon}.png\"></div>";
                        echo "<div class=\"icons\"><p>{$icon}</p></div>";
                        echo "</div></a>";
                    }
                    if ($admin) {
                        echo "<a href=\"admin.php\">";    
                        echo "<div class=\"pages\">";
                        echo "<div class=\"icons\"><img src=\"images\\admin.png\"></div>";
                        echo "<div class=\"icons\"><p>admin</p></div>";
                        echo "</div></a>";
                    }
                    ?>
                
                </div>
            </div>
            
            <!--Container for the rest of the page-->
            <div class="main">
                <div class="main_info">               
                <?php
                if (isset($_POST["target_id"])) {
                    session_start();
                    $_SESSION["target_id"] = $_POST["target_id"];
                    require ("user_view_queries.php");
                    
                    // Div pfp
                    echo "<div class=\"pfp\">";
                    echo "<img src=\"/unimatch/images/{$target_images["profile_pic"]}\" alt=\"profile pic\">";
                    echo "</div>";
                    // close pfp

                    // Div info
                    echo "<div class=\"info\">";

                    // Div title
                    echo "<div class=\"user_title\">"; 
                    echo "<div class=\"name\"><h1>{$target_pers_info["first_name"]}</h1></div>";
                    echo "<div class=\"age\"><h4>{$target_pers_info["age"]}</h4></div></div>"; 
                    // close user title
                    
                    echo "<div class=\"bio\"><p>{$target_pers_info["bio"]}</p></div>";
                
                    // Div interests (1)
                    echo "<div class=\"interests\">";
                    // Special interests boxes: display or not?
                    if ($target_ints["food_display"]) echo "<div class=\"int_box\">Food: {$target_ints["food_lifestyle"]}</div>";
                    if ($target_ints["personality_display"]) echo "<div class=\"int_box\">{$target_ints["personality"]}</div>";
                    if ($target_ints["sexuality_display"]) echo "<div class=\"int_box\">{$target_ints["sexuality"]}</div>";
                    echo "</div>";
                    // close interests (1)

                    // Div interests (2)
                    echo "<div class=\"interests\">";
                    // Display normal interests
                    // A query would be done here to obtain the interests
                    for ($i = 0; $i < 5; $i++) {
                        $num = $i + 1;
                        if ($target_ints["interest{$num}"] != null) {
                            echo "<div class=\"int_box\">{$target_ints["interest{$num}"]}</div>";
                        } 
                    }
                    echo "</div>"; 
                    // close interests (2)

                    // Div about uni
                    echo "<div class=\"about_uni\"";
                    echo "<p>Degree: {$target_uni["course"]} --- ";
                    echo "Year: {$target_uni["c_year"]}</p>";
                    echo "</div>"; 
                    // close about uni

                    // Div interests (3)
                    echo "<div class=\"interests\">";
                    echo "<div class=\"int_box\">Gender: {$target_pers_info["gender"]}</div>";
                    echo "<div class=\"int_box\">Nationality: {$target_pers_info["nationality"]}</div>";
                    if ($target_pers_info["county"] != null) {
                        echo "<div class=\"int_box\">County: {$target_pers_info["county"]}</div>";
                    }
                    echo "</div>"; 
                    // Close interests (3)

                    echo "</div>";
                    // Close info
                

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
                        session_start();

                        require ("user_view_queries.php");
                        require ("user_queries.php");

                        // only display as many photos as session user has; except if user is admin
                        if (!$admin) {
                            // display min between target user and session user number of photos
                            $num_photos = min($target_images["pic_num"], $images["pic_num"]);
                        } else $num_photos = $target_images["pic_num"];
                        
                        
                        if ($num_photos == 0) echo "Oh, seems like {$target_pers_info["first_name"]} doesn't have any photos yet!";
                        for($i=0; $i < $num_photos; $i++) {
                            $num = $i+1;
                            echo "<div class=\"photo\">";
                            echo "<img src=\"/unimatch/images/{$target_images["pic_$num"]}\" alt=\"photo" . $num . "\">";
                            echo "</div>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>