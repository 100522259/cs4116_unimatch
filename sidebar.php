<div class="index">
    <div class="logo">
        <img class="logo" src="./images/logo5.png" alt="unimatch_logo" title="unimatch">
    </div>

    <div class="idx_icons">
        <?php
        include "user_queries.php";

        // count unread messages for the badge
        $stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE to_user_id = ? AND is_read = 0");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($unread_count);
        $stmt->fetch();
        $stmt->close();

        $icons = array("home", "matches","messaging",
                        "user","settings", "admin", "logout");

        foreach ($icons as $icon) {
            if ($icon == "admin" && $admin == 0) continue; // do not show admin section if not admin
            echo '<a href="'. $icon . '.php" style="position:relative;">';
            echo '<div class="pages">';
            echo '<div class="icons"><img src="/unimatch/images/'. $icon .'.png"
                    alt="'.$icon.'" title="'.$icon.'"></div>';
            // show red badge on messaging icon if there are unread messages
            if ($icon == "messaging" && $unread_count > 0) {
                echo '<span class="msg-badge">' . $unread_count . '</span>';
            }
            echo '</div></a>';
        }

        ?>
    </div>
</div>