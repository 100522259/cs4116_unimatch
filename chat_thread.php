<?php
include 'db.php';
$user_id = 1;
$other_id = intval($_GET['user_id'] ?? 0);

// Fetch messages with the selected user
$messages = $database->query("
SELECT m.body, m.from_user_id, p.display_name AS sender, m.sent_at
FROM messages m
JOIN profiles p ON p.user_id = m.from_user_id
WHERE (m.from_user_id = $user_id AND m.to_user_id = $other_id)
   OR (m.from_user_id = $other_id AND m.to_user_id = $user_id)
ORDER BY m.sent_at ASC
");

// Fetch the other user's name for header
$other_user = $database->query("SELECT display_name FROM profiles WHERE user_id=$other_id")->fetchArray();
$other_name = $other_user['display_name'] ?? 'Chat';
?>
<!DOCTYPE html>
<html>
<head>
    <title>UniMatch - Chat with <?php echo htmlspecialchars($other_name); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Chat thread specific styling */
        .chat-container {
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: white;
            display: flex;
            flex-direction: column;
            height: 70vh;
        }

        .chat-header {
            background: #2e7d32;
            color: white;
            padding: 10px 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            font-weight: bold;
        }

        .chat-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background: #e8f5e9;
        }

        .message-bubble {
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 20px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .sent-bubble {
            background: #4caf50;
            color: white;
            margin-left: auto;
            text-align: right;
        }

        .received-bubble {
            background: #c8e6c9;
            color: black;
            margin-right: auto;
            text-align: left;
        }

        .chat-input {
            display: flex;
            border-top: 1px solid #ccc;
            padding: 10px;
            background: #f1f8f1;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .chat-input input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }

        .chat-input button {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background: #2e7d32;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .chat-input button:hover {
            background: #1b5e20;
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
    <div class="chat-container">
        <div class="chat-header">Chat with <?php echo htmlspecialchars($other_name); ?></div>
        <div class="chat-messages">
            <?php while($row = $messages->fetchArray()): ?>
                <?php $class = ($row['from_user_id'] == $user_id) ? 'sent-bubble' : 'received-bubble'; ?>
                <div class="message-bubble <?php echo $class; ?>">
                    <strong><?php echo htmlspecialchars($row['sender']); ?>:</strong> <?php echo htmlspecialchars($row['body']); ?><br>
                    <small><?php echo date("H:i", strtotime($row['sent_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="chat-input">
            <input type="text" placeholder="Type a message..." />
            <button>Send</button>
        </div>
    </div>
</div>
</body>
</html>
