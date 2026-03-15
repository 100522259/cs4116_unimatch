<?php
include 'db.php';
$user_id = 1;

// Fetch user profile
$profile = $database->query("SELECT * FROM profiles WHERE user_id = $user_id")->fetchArray();

// Count matches and messages
$match_count = $database->query("SELECT COUNT(*) as c FROM connections WHERE from_user_id=$user_id AND status='matched'")->fetchArray()['c'];
$msg_count   = $database->query("SELECT COUNT(*) as c FROM messages WHERE from_user_id=$user_id OR to_user_id=$user_id")->fetchArray()['c'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>UniMatch - Home</title>
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

    <div class="banner">
        <h2>Welcome back, <?php echo htmlspecialchars($profile['display_name'] ?? 'User'); ?>!</h2>
        <p>Connect with friends and find potential dates at the University of Limerick</p>
    </div>

    <div class="card">
        <img src="https://via.placeholder.com/80" alt="Profile Picture">
        <div class="card-content">
            <h3><?php echo htmlspecialchars($profile['display_name'] ?? 'User'); ?></h3>
            <p><strong>Bio:</strong> <?php echo htmlspecialchars($profile['bio'] ?? 'I love meeting new people and exploring new hobbies!'); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($profile['location'] ?? 'Not set'); ?></p>
            <p><strong>Seeking:</strong> <?php echo htmlspecialchars($profile['seeking'] ?? 'any'); ?></p>

            <div class="stats">
                <div class="stat"><strong>Matches:</strong> <?php echo $match_count; ?></div>
                <div class="stat"><strong>Messages:</strong> <?php echo $msg_count; ?></div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
