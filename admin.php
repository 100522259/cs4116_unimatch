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
            <?php include "./sidebar.php"; ?>

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
                                echo "<tr>";

                                echo '<td><form action="user_view.php" method="post">';
                                echo '<input type="submit" name="view'.$i.'" value="'.$row["u_reported"].'">';
                                echo '<input type="hidden" name="target_id" value="'.$row["id_reported"].'">';
                                echo "</form></td>";

                                echo '<td><form action="user_view.php" method="post">';
                                echo '<input type="submit" name="view'.$i.'" value="'.$row["u_reportee"].'">';
                                echo '<input type="hidden" name="target_id" value="'.$row["id_reportee"].'">';
                                echo "</form></td>";

                                echo "<td>{$row["category"]}</td><td>{$row["timestamp"]}</td></tr>";
                            }
                            echo "</tbody></table>";
                            ?>
                        </div>
                        <div class="stat-tables">
                            <h4>Offenses</h4>
                            <?php

                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>User</th><th>#Offenses</th><th>Reported</th><th>Ban time</th></tr></thead>";
                            echo "<tbody>";
                            while ($row = $res_offense->fetch_assoc()) {
                                echo "<tr>";
                                
                                echo '<td><form action="user_view.php" method="post">';
                                echo '<input type="submit" name="view'.$i.'" value="'.$row["username"].'">';
                                echo '<input type="hidden" name="target_id" value="'.$row["user_id"].'">';
                                echo "</form></td>";
                                
                                echo "<td>{$row["offence_num"]}</td>";
                                echo "<td>";
                                // If user has been reported, display Y, else display N
                                if ($row["reported"] == 1) echo "Y</td>";
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
                            <h4>Banned Users</h4>
                            <?php
                            session_start();
                            $_SESSION["location"] = "admin.php";

                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>User</th><th>View</th><th>Unban</th></tr></thead>";
                            echo "<tbody>";
                            $i=0;
                            while ($row = $res_offense2->fetch_assoc()) {
                                if ($row["blocked"] == 1) {
                                    echo '<tr><td>'.$row["username"].'</td>';

                                    echo '<td><form action="user_view.php" method="post">';
                                    echo '<input type="submit" name="view'.$i.'" value="View">';
                                    echo '<input type="hidden" name="target_id" value="'.$row["user_id"].'">';
                                    echo "</form></td>";

                                    echo '<td><form action="logic_ban.php" method="post">';
                                    echo '<input type="submit" name="unban'.$i.'" value="Unban">';
                                    echo '<input type="hidden" name="target_id" value="'.$row["user_id"].'">';
                                    echo "</form></td></tr>";
                                }
                            }
                            echo "</tbody></table>";
                            
                            ?>
                        </div>
                        <div class="stat-tables">
                            <h4>Matches Data</h4>
                            <?php
                            echo "<table class=\"scroll\">";
                            echo "<thead><tr><th>#Matches</th><th>#Friends</th><th>#Pending</th><th>#Both</th></tr></thead>";
                            echo "<tbody>";
                            $int_num = (int) $num_rel["num_rel"];
                            echo "<tr><td>{$int_num}</td>";
                            $int_num = (int) $num_fr["num_fr"];
                            echo "<td>{$int_num}</td>";
                            $int_num = $f_pen["f_pen"] + $r_pen["r_pen"];
                            echo "<td>{$int_num}</td>";
                            $int_num = (int)$num_both["num_both"];
                            echo "<td>{$int_num}</td></tr>";
                            echo "</tbody></table>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </body>
</html>