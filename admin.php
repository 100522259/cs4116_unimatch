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
                <div class="header-text">
                    <h1>admin dashboard</h1>
                </div>
                <div class="data">
                    <div class="data-tables">
                        <div class="stat-tables">
                            <h4>Reported Users</h4>
                            <?php
                            include "admin_queries.php";

                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>User</th><th>Reported by</th><th>Category</th><th>Date</th></tr></thead>";
                            echo "<tbody>";
                            while ($row = $res_reports->fetch_assoc()) {
                                echo "<tr><td>{$row["username1"]}</td><td>{$row["username"]}</td>";
                                echo "<td>{$row["category"]}</td><td>{$row["timestamp"]}</td>";
                            }
                            echo "</tbody></table>";
                            ?>
                        </div>
                        <div class="stat-tables">
                            <h4>Banned Users</h4>
                            <?php
                            include "admin_queries.php";

                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>User</th><th>#Offenses</th><th>Reported</th><th>Ban time</th></tr></thead>";
                            echo "<tbody>";
                            while ($row = $res_offense->fetch_assoc()) {
                                echo "<tr><td>{$row["username"]}</td><td>{$row["offence_num"]}</td>";
                                echo "<td>";
                                // If user has been reported, display Y, else display N
                                if ($row["reported"] == 1) echo "Y";
                                else echo "N</td>"; 
                                echo "<td>";
                                // If user has no ban time, display 0;
                                if ($row["ban_time"] == null) echo "0";
                                else echo "{$row["ban_time"]}";
                                echo "</td></tr>";
                            }
                            echo "</tbody></table>";
                            ?>
                        </div>
                    </div>
                    <div class="data-statistics">
                        <div class="stat-tables">
                            <h4>User Info</h4>
                            <?php
                            for ($i = 0; $i < 5; $i++) {
                                echo "<p>Something will go here</p>";
                            }
                            ?>
                        </div>
                        <div class="stat-tables">
                            <h4>Matches</h4>
                            <?php
                            for ($i = 0; $i < 5; $i++) {
                                echo "<p>Something will go here</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </body>
</html>