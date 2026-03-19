<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>unimatch - user profile page</title>
        <!--Bootstrap css-->
        
        <link href="css\profile.css" rel="stylesheet">
    </head>

    <body>
        <div class="container">

            <!--vertical container for page index-->
            <div class="index">
                <div class="logo">
                    <img class="logo" src="images\logo3.png">
                </div>
                <div class="idx_icons">
                    <div class="pages">
                        <div class="icons"><img src="images\home.png"></div>
                        <div class="text">Home</div>
                    </div>
                    <div class="pages">
                        <div class="icons"><img src="images\heart.png"></div>
                        <div class="text">Dates</div>
                    </div>
                    <div class="pages">
                        <div class="icons"><img src="images\star.png"></div>
                        <div class="text">Friends</div>
                    </div>
                    <div class="pages">
                        <div class="icons"><img src="images\chat.png"></div>
                        <div class="text">Chat</div>
                    </div>
                    <div class="pages">
                        <div class="icons"><img src="images\account.png"></div>
                        <div class="text">User</div>
                    </div>
                    <div class="pages">
                        <div class="icons"><img src="images\settings.png"></div>
                        <div class="text">Settings</div>
                    </div>
                </div>
            </div>
            <!--Container for the rest of the page-->
            <div class="page">
                <div class="main_info">
                    <div class="pfp">
                        <img src="https://dummyimage.com/400x400/636363/fff.png&text=profile+photo+(400x400)">
                    </div>
                    <div class="info">
                        <div class="user_title"><p>User name and age</p></div>
                        <div class="bio"><p>This is some bio text</p></div>
                        <div class="interests">
                            <?php
                            for ($i = 0; $i < 5; $i++) {
                                echo "<div class=\"int_box\">Interest</div>";
                            }
                            ?>
                        </div>
                        <div class="about_uni"><p>display interests and information</p></div>
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