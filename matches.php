<?php
include 'db.php';
$user_id = 1;

// Friendship matches (seeking = any)
$friend_matches = $database->query("
SELECT p.display_name, c.status 
FROM connections c
JOIN profiles p ON p.user_id = c.to_user_id
WHERE c.from_user_id = $user_id AND p.seeking='any'
");

// Dating matches (seeking != any)
$dating_matches = $database->query("
SELECT p.display_name, c.status 
FROM connections c
JOIN profiles p ON p.user_id = c.to_user_id
WHERE c.from_user_id = $user_id AND p.seeking!='any'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>UniMatch - Matches</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>UniMatch</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="matches.php">Matches</a>
        <a href="chat.php">Chat</a>
    </nav>
</header>
<div class="container">

    <h2 class="section-title">Friendship Matches</h2>
    <?php while($row = $friend_matches->fetchArray()): ?>
        <div class="card">
            <img src="https://via.placeholder.com/80" alt="Profile Picture">
            <div class="card-content">
                <h3><?php echo htmlspecialchars($row['display_name']); ?></h3>
                <span class="badge friendship"><?php echo ucfirst($row['status']); ?></span>
            </div>
            <button>Message</button>
        </div>
    <?php endwhile; ?>

    <h2 class="section-title">Dating Matches</h2>
    <?php while($row = $dating_matches->fetchArray()): ?>
        <div class="card">
            <img src="https://via.placeholder.com/80" alt="Profile Picture">
            <div class="card-content">
                <h3><?php echo htmlspecialchars($row['display_name']); ?></h3>
                <span class="badge dating"><?php echo ucfirst($row['status']); ?></span>
            </div>
            <button>Message</button>
        </div>
    <?php endwhile; ?>

</div>
</body>
</html>
