<?php
include 'db.php';
$user_id = 1;

// Fetch distinct chat partners with last message preview
$chats = $database->query("
SELECT 
    CASE 
        WHEN from_user_id = $user_id THEN to_user_id 
        ELSE from_user_id 
    END AS chat_user_id,
    p.display_name,
    (SELECT body FROM messages m2 
     WHERE (m2.from_user_id = $user_id AND m2.to_user_id = CASE WHEN from_user_id = $user_id THEN to_user_id ELSE from_user_id END)
        OR (m2.from_user_id = CASE WHEN from_user_id = $user_id THEN to_user_id ELSE from_user_id END AND m2.to_user_id = $user_id)
     ORDER BY sent_at DESC LIMIT 1) AS last_message
FROM messages m
JOIN profiles p ON p.user_id = CASE 
    WHEN from_user_id = $user_id THEN to_user_id 
    ELSE from_user_id 
END
WHERE m.from_user_id = $user_id OR m.to_user_id = $user_id
GROUP BY chat_user_id
ORDER BY (SELECT sent_at FROM messages m3 
          WHERE (m3.from_user_id = $user_id AND m3.to_user_id = CASE WHEN from_user_id = $user_id THEN to_user_id ELSE from_user_id END)
             OR (m3.from_user_id = CASE WHEN from_user_id = $user_id THEN to_user_id ELSE from_user_id END AND m3.to_user_id = $user_id)
          ORDER BY sent_at DESC LIMIT 1) DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>UniMatch - Chat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-thread-card {
            display: flex;
            align-items: center;
            background: white;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .chat-thread-card:hover {
            background: #c8e6c9;
        }

        .chat-thread-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .chat-thread-card .chat-info {
            flex: 1;
        }

        .chat-thread-card .chat-info h3 {
            margin: 0;
            font-size: 16px;
        }

        .chat-thread-card .chat-info p {
            margin: 2px 0 0;
            font-size: 14px;
            color: #555;
        }
    </style>
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

    <h2 class="section-title">Your Chats</h2>

    <?php while($chat = $chats->fetchArray()): ?>
        <a class="chat-thread-card" href="chat_thread.php?user_id=<?php echo $chat['chat_user_id']; ?>">
            <img src="https://via.placeholder.com/50" alt="Profile Picture">
            <div class="chat-info">
                <h3><?php echo htmlspecialchars($chat['display_name']); ?></h3>
                <p><?php echo htmlspecialchars($chat['last_message'] ?? 'No messages yet'); ?></p>
            </div>
        </a>
    <?php endwhile; ?>

</div>
</body>
</html>
