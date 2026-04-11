<div class="index">
    <div class="logo">
        <img class="logo" src="./images\logo5.png">
    </div>

    <div class="idx_icons">
        <?php
        include "user_queries.php";

        $icons = array("home", "matches","messaging",
                        "user","settings");
        
        foreach ($icons as $icon) {
            echo "<a href=\"{$icon}.php\">";    
            echo "<div class=\"pages\">";
            echo "<div class=\"icons\"><img src=\"images\\{$icon}.png\"></div>";
            echo "<div class=\"icons\"><p>{$icon}</p></div>";
            echo "</div></a>";
        }
        
        if ($admin == 1) {
            echo "<a href=\"admin.php\">";    
            echo "<div class=\"pages\">";
            echo "<div class=\"icons\"><img src=\"images\\admin.png\"></div>";
            echo "<div class=\"icons\"><p>admin</p></div>";
            echo "</div></a>";
        }
        ?>
    </div>
</div>