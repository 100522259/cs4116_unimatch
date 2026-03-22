<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>unimatch - user profile page</title>
        <!--Bootstrap css-->
        
        <link href="css\profile.css" rel="stylesheet">
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
            <div class="page">
                <div class="main_info">
                    <div class="pfp">
                        <img src="https://dummyimage.com/400x400/636363/fff.png&text=profile+photo+(400x400)">
                    </div>
                    <div class="info">
                        <div class="user_title">
                            <div class="name"><h1>Name</h1></div>
                            <div class="age"><h4>Age</h4></div></div>
                        <div class="bio"><p>This is some bio text</p></div>
                        <div class="interests">
                            <?php
                            for ($i = 0; $i < 5; $i++) {
                                echo "<div class=\"int_box\">Interest</div>";
                            }
                            ?>
                        </div>
                        <div class="about_uni"><p>interests and information</p></div>
                    </div>
                    <!--Will be in a php section, query if user admin, it will display this-->
                    <div class="admin">
                        <?php
                            $admin_activities = array("report","block","edit bio","edit name","rmv photo",
                            "rmv interest","disable acc");
                            foreach ($admin_activities as $act) {
                                echo "<div class=\"admin_activity\">";
                                echo "<div class=\"admin_act\"><img src=\"images\\$act.png\"></div>";
                                echo "<div class=\"admin_act\"><p>{$act}</p></div>";
                                echo "</div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="my_images_txt">
                    <h1>My photos</h1>
                </div>
                <div class="my_images">
                <!--temp code; using php we'd request the number of photos and create as many
                    divs as photos available-->
                    <div class="photo">
                        <img src="https://dummyimage.com/200x200/636363/fff.png&text=my+photos+(200x200)">
                    </div>
                    <div class="photo">
                        <img src="https://dummyimage.com/200x200/636363/fff.png&text=my+photos+(200x200)">
                    </div>
                    <div class="photo">
                        <img src="https://dummyimage.com/200x200/636363/fff.png&text=my+photos+(200x200)">
                    </div>
                    <div class="photo">
                        <img src="https://dummyimage.com/200x200/636363/fff.png&text=my+photos+(200x200)">
                    </div>
                    <div class="photo">
                        <img src="https://dummyimage.com/200x200/636363/fff.png&text=my+photos+(200x200)">
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>