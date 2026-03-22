<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>unimatch - admin page</title>
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
                <div class="header-text">
                    <h1>admin dashboard</h1>
                </div>
                <div class="data">
                    <div class="data-tables">
                        <div class="stat-tables">
                            <h4>Reported Users</h4>
                            <?php
                            // IN THIS SECTION WE'D QUERY FROM THE DB TO SELECT APPROPIATE TABLE CONTENT
                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>User</th><th>Reported by</th><th>Category</th><th>Date</th></tr></thead>";
                            echo "<tbody>";
                            for ($i = 0; $i < 5; $i++) {
                                echo "<tr><td>Username</td><td>Username</td><td>Some text</td><td>Some date</td>";
                            }

                            echo "</tbody></table>";
                            ?>
                        </div>
                        <div class="stat-tables">
                            <h4>Banned Users</h4>
                            <?php
                            // IN THIS SECTION WE'D QUERY FROM THE DB TO SELECT APPROPIATE TABLE CONTENT
                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>User</th><th>Reported by</th><th>Category</th><th>Date</th></tr></thead>";
                            echo "<tbody>";
                            for ($i = 0; $i < 5; $i++) {
                                echo "<tr><td>Username</td><td>Username</td><td>Some text</td><td>Some date</td>";
                            }

                            echo "</tbody></table>";
                            ?>
                        </div>
                    </div>
                    <div class="data-statistics">
                        <div class="stat-tables">
                            <h4>User Info</h4>
                        </div>
                        <div class="stat-tables">
                            <h4>Matches</h4>
                        </div>
                    </div>
                </div>
            </div>
                
    </body>
</html>