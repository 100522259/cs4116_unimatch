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
                    include "user_queries.php";

                    $icons = array("home", "dates", "friends","chat",
                                    "user","settings");
                    foreach ($icons as $icon) {
                        echo "<a href=\"{$icon}.php\">";    
                        echo "<div class=\"pages\">";
                        echo "<div class=\"icons\"><img src=\"images\\{$icon}.png\"></div>";
                        echo "<div class=\"icons\"><p>{$icon}</p></div>";
                        echo "</div></a>";
                    }

                    if ($creds["admin"] == 1) {
                        echo "<a href=\"admin.php\">";    
                        echo "<div class=\"pages\">";
                        echo "<div class=\"icons\"><img src=\"images\\admin.png\"></div>";
                        echo "<div class=\"icons\"><p>admin</p></div>";
                        echo "</div></a>";
                    }
                    ?>
                
                </div>
            </div>
        </div>
    </body>
</html>