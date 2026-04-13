<div class="index">
    <div class="logo">
        <img class="logo" src="./images\logo5.png">
    </div>

    <div class="idx_icons">
        <?php
        include "user_queries.php";

        $icons = array("home", "matches","messaging",
                        "user","settings", "admin", "logout");
        
        foreach ($icons as $icon) {
            if ($icon == "admin" && $admin == 0) continue; // do not show admin section if not admin
            echo '<a href="'. $icon . '.php">';    
            echo '<div class="pages">';
            echo '<div class="icons"><img src="/unimatch/images/'. $icon .'.png"></div>';
            echo '<div class="icons"><p>' . $icon . '</p></div>';
            echo '</div></a>';
        }

        ?>
    </div>
</div>