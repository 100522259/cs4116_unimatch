<?php
// Check if session is set, if not: go to login:
include 'session_check.php';
?>

<?php
session_start();
include "./connect_server.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: html/login.html');
    exit;
}

define('TZ_OFFSET', 28800); // 8 hours in seconds

$current_user_id = (int)$_SESSION['user_id'];
$username        = htmlspecialchars($_SESSION['username'] ?? 'User');
$active_chat_id  = isset($_GET['with']) ? (int)$_GET['with'] : 0;
$send_error      = '';

// Phone number detection — catches Irish and international numbers
function containsPhoneNumber(string $text): bool {
    $patterns = [
        '/\b0\d{9}\b/',                               // 08xxxxxxxxx - irish number
        '/\b0\d{2}[\s\-]?\d{3}[\s\-]?\d{4}\b/',      // 083 123 4567
        '/\+353[\s\-]?\d{2}[\s\-]?\d{3}[\s\-]?\d{4}/', // +353 83 123 4567
        '/\+\d{1,3}[\s\-]?\d{6,12}/',                 // International 
        '/\b\d{3}[\s\-]\d{3}[\s\-]\d{4}\b/',          // 123 456 7890
        '/\b\d{10,11}\b/',                             // 10-11 digit number
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $text)) return true;
    }
    return false;
}

// Handle plain POST send
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_user_id = (int)($_POST['to_user_id'] ?? 0);
    $body       = trim($_POST['body'] ?? '');
    if ($to_user_id > 0 && $body !== '') {
        if (containsPhoneNumber($body)) {
            $send_error = 'Your message appears to contain a phone number. For your safety, please keep conversations on UniMatch and do not share personal contact details.';
            $active_chat_id = $to_user_id;
        } else {
            $ins = $conn->prepare("INSERT INTO messages (from_user_id, to_user_id, body) VALUES (?, ?, ?)");
            $ins->bind_param('iis', $current_user_id, $to_user_id, $body);
            $ins->execute();
            $ins->close();
            header("Location: messaging.php?with={$to_user_id}");
            $conn->close();
            exit;
        }
    }
}

// Fetch confirmed matches for sidebar
$conversations = [];
$stmt = $conn->prepare(
    "SELECT
         CASE WHEN r.user_id1 = ? THEN r.user_id2 ELSE r.user_id1 END AS other_id,
         p.first_name, img.profile_pic,
         (SELECT body FROM messages m
           WHERE (m.from_user_id = ? AND m.to_user_id = CASE WHEN r.user_id1 = ? THEN r.user_id2 ELSE r.user_id1 END)
              OR (m.to_user_id   = ? AND m.from_user_id = CASE WHEN r.user_id1 = ? THEN r.user_id2 ELSE r.user_id1 END)
           ORDER BY m.sent_at DESC LIMIT 1
         ) AS last_message,
         (SELECT COUNT(*) FROM messages m2
           WHERE m2.from_user_id = CASE WHEN r.user_id1 = ? THEN r.user_id2 ELSE r.user_id1 END
             AND m2.to_user_id = ?
             AND m2.is_read = 0
         ) AS unread_count
       FROM relationship r
       JOIN personal_info p ON p.user_id = CASE WHEN r.user_id1 = ? THEN r.user_id2 ELSE r.user_id1 END
       LEFT JOIN images img ON img.user_id = p.user_id
      WHERE (r.user_id1 = ? OR r.user_id2 = ?)
        AND (r.r_status = 1 OR r.f_status = 1)
      ORDER BY r.created_at DESC"
);

$stmt->bind_param('iiiiiiiiii',
    $current_user_id,
    $current_user_id, $current_user_id,
    $current_user_id, $current_user_id,
    $current_user_id, $current_user_id,
    $current_user_id,
    $current_user_id, $current_user_id
);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $conversations[] = $row;
$stmt->close();

if ($active_chat_id === 0 && !empty($conversations)) {
    $active_chat_id = (int)$conversations[0]['other_id'];
}

// Mark messages as read when opening a conversation
if ($active_chat_id > 0) {
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE from_user_id = ? AND to_user_id = ? AND is_read = 0");
    $stmt->bind_param('ii', $active_chat_id, $current_user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch messages
$messages         = [];
$active_chat_name = '';
$active_chat_pic  = '';

if ($active_chat_id > 0) {
    $nm = $conn->prepare(
        "SELECT p.first_name, img.profile_pic
           FROM personal_info p
           LEFT JOIN images img ON img.user_id = p.user_id
          WHERE p.user_id = ? LIMIT 1"
    );
    $nm->bind_param('i', $active_chat_id);
    $nm->execute();
    $nm->bind_result($active_chat_name, $active_chat_pic);
    $nm->fetch();
    $nm->close();
    $active_chat_name = htmlspecialchars($active_chat_name ?? '');
    $active_chat_pic  = htmlspecialchars($active_chat_pic  ?? '');

    $stmt = $conn->prepare(
        "SELECT from_user_id, body, sent_at FROM messages
          WHERE (from_user_id = ? AND to_user_id = ?)
             OR (from_user_id = ? AND to_user_id = ?)
          ORDER BY sent_at ASC"
    );
    $stmt->bind_param('iiii', $current_user_id, $active_chat_id, $active_chat_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $messages[] = $row;
    $stmt->close();
}
$conn->close();

function convAvatar(string $name, ?string $pic, int $size = 44): string {
    $initial = strtoupper(substr($name, 0, 1));
    $colors  = ['#2e7d32','#1565c0','#6a1b9a','#c62828','#e65100','#00695c','#4527a0'];
    $color   = $colors[ord($initial) % count($colors)];
    if (!empty($pic)) {
        return '<img src="' . htmlspecialchars($pic) . '" alt="' . htmlspecialchars($name) . '"
                     style="width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;object-fit:cover;flex-shrink:0;display:block;">';
    }
    return '<div style="width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:' . $color . ';
                        color:white;font-size:' . round($size*0.38) . 'px;font-weight:700;display:flex;align-items:center;
                        justify-content:center;flex-shrink:0;">' . $initial . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — UniMatch</title>
    
    <link href="css/profile.css" rel="stylesheet">
    <link href="css/profile_mobile.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        .um-nav {
            background: #1B5E3B; padding: 0 28px;
            display: flex; align-items: center; justify-content: space-between;
            height: 62px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
        }
        .um-nav-brand { color: white; font-size: 22px; font-weight: 700; text-decoration: none; }
        .um-nav-links { display: flex; gap: 4px; }
        .um-nav-links a {
            color: rgba(255,255,255,0.82); text-decoration: none;
            padding: 7px 15px; border-radius: 20px; font-size: 14px;
            font-weight: 500; transition: background 0.2s;
        }
        .um-nav-links a:hover  { background: rgba(255,255,255,0.13); color: white; }
        .um-nav-links a.active { background: rgba(255,255,255,0.18); color: white; }
        .um-nav-links a.logout { color: rgba(255,255,255,0.5); }

        .msg-layout {
            display: flex; height: calc(100vh - 62px);
            max-width: 1060px; margin: 0 auto;
            background: white; box-shadow: 0 0 40px rgba(0,0,0,0.10);
        }

        .conv-sidebar {
            width: 310px; min-width: 240px;
            border-right: 1px solid #e8f5e9;
            display: flex; flex-direction: column; flex-shrink: 0;
        }
        .sidebar-header {
            padding: 22px 18px 16px; font-size: 18px; font-weight: 700;
            color: #1B5E3B; border-bottom: 1px solid #e8f5e9;
        }
        .conv-list { overflow-y: auto; flex: 1; }
        .conv-item {
            display: flex; align-items: center; gap: 13px;
            padding: 14px 17px; text-decoration: none; color: inherit;
            border-bottom: 1px solid #f1f8f2; transition: background 0.15s;
            position: relative;
        }
        .conv-item:hover  { background: #f6faf6; }
        .conv-item.active { background: #e8f5e9; }
        .conv-item.active::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px; background: #2e7d32; border-radius: 0 3px 3px 0;
        }
        .conv-text { flex: 1; min-width: 0; }
        .conv-name {
            font-weight: 600; font-size: 14px; color: #111827;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .conv-preview {
            font-size: 12px; color: #9CA3AF; margin-top: 3px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .conv-name.unread {
            font-weight: 800;
            color: #000;
        }

        .conv-badge {
            background: #e63946;
            color: white;
            font-size: 11px;
            font-weight: bold;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .no-conv {
            padding: 40px 20px; text-align: center;
            color: #9CA3AF; font-size: 14px; line-height: 1.7;
        }
        .no-conv a { color: #2e7d32; font-weight: 600; text-decoration: none; }

        .msg-panel { flex: 1; display: flex; flex-direction: column; min-width: 0; background: #f9fbf9; }

        .msg-header {
            padding: 15px 22px; border-bottom: 1px solid #e8f5e9;
            display: flex; align-items: center; gap: 13px;
            background: white; flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .msg-header-info { flex: 1; min-width: 0; }
        .msg-header-name { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
        .msg-header-sub  { font-size: 12px; color: #9CA3AF; margin: 2px 0 0; }

        /* In-app error toast */
        .error-toast {
            display: none;
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: #c62828;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            z-index: 200;
            max-width: 90%;
            text-align: center;
            animation: fadeInUp 0.2s ease;
        }
        .error-toast.visible { display: block; }

        /* Server-side error banner */
        .error-banner {
            background: #fce4e4;
            border-left: 4px solid #c62828;
            color: #c62828;
            padding: 12px 18px;
            font-size: 13px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateX(-50%) translateY(10px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        .msg-list {
            flex: 1; overflow-y: auto; padding: 24px 22px;
            display: flex; flex-direction: column; gap: 10px;
            position: relative;
        }

        .bubble-wrap { display: flex; flex-direction: column; }
        .bubble-wrap.sent     { align-items: flex-end; }
        .bubble-wrap.received { align-items: flex-start; }

        .bubble {
            max-width: 62%; padding: 11px 17px; border-radius: 20px;
            font-size: 14px; line-height: 1.55; word-break: break-word;
        }
        .bubble.sent     { background: #1B5E3B; color: white; border-bottom-right-radius: 5px; }
        .bubble.received { background: white; color: #1F2937; border-bottom-left-radius: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .bubble-time { font-size: 11px; margin-top: 4px; opacity: 0.6; }
        .bubble-wrap.sent .bubble-time     { color: #6B7280; text-align: right; }
        .bubble-wrap.received .bubble-time { color: #9CA3AF; text-align: left; }

        .date-divider { text-align: center; font-size: 11px; color: #9CA3AF; margin: 6px 0; }

        .msg-empty {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center; color: #9CA3AF; gap: 12px;
        }
        .msg-empty .e-icon { font-size: 52px; }
        .msg-empty p { font-size: 15px; margin: 0; }

        .msg-input-bar { padding: 14px 18px; border-top: 1px solid #e8f5e9; background: white; flex-shrink: 0; position: relative; }
        .msg-input-bar form { display: flex; gap: 10px; align-items: center; }
        .msg-input-bar input[type="text"] {
            flex: 1; border: 1.5px solid #c8e6c9; border-radius: 26px;
            padding: 11px 18px; font-size: 14px; font-family: Arial, sans-serif;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s; background: #f9fbf9;
        }
        .msg-input-bar input[type="text"]:focus {
            border-color: #2e7d32; background: white;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.10);
        }
        .msg-input-bar input[type="text"].input-error {
            border-color: #c62828;
            box-shadow: 0 0 0 3px rgba(198,40,40,0.10);
        }
        .send-btn {
            background: #1B5E3B; color: white; border: none; border-radius: 50%;
            width: 46px; height: 46px; min-width: 46px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s, transform 0.15s; flex-shrink: 0;
        }
        .send-btn:hover  { background: #2e7d32; transform: scale(1.05); }
        .send-btn:active { transform: scale(0.97); }
        .send-icon { width: 18px; height: 18px; fill: white; }

        .back-btn {
            display: none; background: none; border: none;
            color: #2e7d32; font-size: 22px; cursor: pointer; padding: 0; margin-right: 4px;
        }

        @media (max-width: 640px) {
            .um-nav { padding: 0 14px; }
            .um-nav-links a { padding: 6px 10px; font-size: 12px; }
            .conv-sidebar { width: 100%; border-right: none; }
            .conv-sidebar.hidden-mobile { display: none; }
            .msg-panel.hidden-mobile    { display: none; }
            .back-btn { display: inline-flex; }
            .bubble { max-width: 80%; }
        }
    </style>
</head>
<body>

<div class="container">
    <?php include "./sidebar.php"; ?>

    <div class="main">
    <div class="msg-layout">

        <div class="conv-sidebar" id="conv-sidebar">
            <div class="sidebar-header">Messages</div>
            <div class="conv-list">
                <?php if (empty($conversations)): ?>
                    <div class="no-conv">No matches yet.<br><a href="matches.php">Find matches →</a></div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv):
                        $oid      = (int)$conv['other_id'];
                        $cname    = htmlspecialchars($conv['first_name'] ?? '?');
                        $preview  = htmlspecialchars($conv['last_message'] ?? 'Say hello! 👋');
                        $isActive = ($oid === $active_chat_id) ? 'active' : '';
                        $av       = convAvatar($conv['first_name'] ?? '?', $conv['profile_pic'] ?? null, 46);
                        $conv_unread = (int)($conv['unread_count'] ?? 0);
                    ?>
                        <a class="conv-item <?php echo $isActive; ?>" href="messaging.php?with=<?php echo $oid; ?>">
                            <?php echo $av; ?>
                            <div class="conv-text">
                                <div class="conv-name <?php if ($conv_unread > 0) echo 'unread'; ?>"><?php echo $cname; ?></div>
                                <div class="conv-preview"><?php echo $preview; ?></div>
                            </div>
                            <?php if ($conv_unread > 0): ?>
                                <span class="conv-badge"><?php echo $conv_unread; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="msg-panel" id="msg-panel">
            <?php if ($active_chat_id > 0 && $active_chat_name !== ''): ?>

                <div class="msg-header">
                    <button class="back-btn" onclick="showSidebar()">&#8592;</button>
                    <?php echo convAvatar($active_chat_name, $active_chat_pic ?: null, 42); ?>
                    <div class="msg-header-info">
                        <p class="msg-header-name"><?php echo $active_chat_name; ?></p>
                        <p class="msg-header-sub">UniMatch</p>
                    </div>
                </div>

                <?php if ($send_error): ?>
                    <div class="error-banner">
                        ⚠️ <?php echo htmlspecialchars($send_error); ?>
                    </div>
                <?php endif; ?>

                <div class="msg-list" id="msg-list">
                    <!-- In-app error toast (client-side) -->
                    <div class="error-toast" id="error-toast">
                        ⚠️ Your message appears to contain a phone number. Please keep conversations on UniMatch.
                    </div>

                    <?php
                    $last_date = '';
                    foreach ($messages as $msg):
                        $cls      = ((int)$msg['from_user_id'] === $current_user_id) ? 'sent' : 'received';
                        $body     = htmlspecialchars($msg['body']);
                        $ts       = strtotime($msg['sent_at']) + TZ_OFFSET;
                        $time     = date("H:i", $ts);
                        $msg_date = date("D j M", $ts);
                        if ($msg_date !== $last_date):
                            $last_date = $msg_date;
                    ?>
                            <div class="date-divider"><?php echo $msg_date; ?></div>
                    <?php endif; ?>
                        <div class="bubble-wrap <?php echo $cls; ?>">
                            <div class="bubble <?php echo $cls; ?>"><?php echo $body; ?></div>
                            <span class="bubble-time"><?php echo $time; ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?>
                        <div style="text-align:center;color:#9CA3AF;font-size:14px;margin:auto;">
                            No messages yet — say hello! 👋
                        </div>
                    <?php endif; ?>
                </div>

                <div class="msg-input-bar">
                    <form method="POST" action="messaging.php?with=<?php echo $active_chat_id; ?>" onsubmit="return validateMessage()">
                        <input type="hidden" name="to_user_id" value="<?php echo $active_chat_id; ?>">
                        <input type="text" name="body" id="msg-input"
                            placeholder="Message <?php echo $active_chat_name; ?>…"
                            autocomplete="off" required>
                        <button type="submit" class="send-btn" aria-label="Send">
                            <svg class="send-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 21l21-9L2 3v7l15 2-15 2z"/>
                            </svg>
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="msg-empty">
                    <div class="e-icon">💬</div>
                    <p>Select a conversation to start messaging</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

<script>
var CURRENT_USER_ID = <?php echo $current_user_id; ?>;
var ACTIVE_CHAT_ID  = <?php echo $active_chat_id; ?>;

(function() {
    var list = document.getElementById('msg-list');
    if (list) list.scrollTop = list.scrollHeight;
})();

// Client-side phone number detection
function containsPhoneNumber(text) {
    var patterns = [
        /\b0\d{9}\b/,
        /\b0\d{2}[\s\-]?\d{3}[\s\-]?\d{4}\b/,
        /\+353[\s\-]?\d{2}[\s\-]?\d{3}[\s\-]?\d{4}/,
        /\+\d{1,3}[\s\-]?\d{6,12}/,
        /\b\d{3}[\s\-]\d{3}[\s\-]\d{4}\b/,
        /\b\d{10,11}\b/
    ];
    for (var i = 0; i < patterns.length; i++) {
        if (patterns[i].test(text)) return true;
    }
    return false;
}

var errorToast   = document.getElementById('error-toast');
var errorTimeout = null;

function showErrorToast() {
    if (!errorToast) return;
    errorToast.classList.add('visible');
    clearTimeout(errorTimeout);
    errorTimeout = setTimeout(function() {
        errorToast.classList.remove('visible');
    }, 4000); // auto-hide after 4 seconds
}

function validateMessage() {
    var input = document.getElementById('msg-input');
    if (!input) return true;
    var body = input.value.trim();
    if (containsPhoneNumber(body)) {
        input.classList.add('input-error');
        showErrorToast();
        return false;
    }
    input.classList.remove('input-error');
    return true;
}

var msgInput = document.getElementById('msg-input');
if (msgInput) {
    msgInput.addEventListener('input', function() {
        this.classList.remove('input-error');
        if (errorToast) errorToast.classList.remove('visible');
    });
}

function fetchMessages() {
    if (ACTIVE_CHAT_ID === 0) return;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'msg_poll.php?with=' + ACTIVE_CHAT_ID + '&t=' + Date.now());
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                if (data.messages) renderMessages(data.messages);
            } catch(e) {}
        }
    };
    xhr.send();
}

function renderMessages(msgs) {
    var list = document.getElementById('msg-list');
    if (!list) return;
    var atBottom = (list.scrollHeight - list.clientHeight - list.scrollTop) < 60;
    var toast = document.getElementById('error-toast');
    list.innerHTML = '';
    if (toast) list.appendChild(toast); // keep the toast inside msg-list
    var lastDate = '';
    msgs.forEach(function(msg) {
        if (msg.date && msg.date !== lastDate) {
            lastDate = msg.date;
            var div = document.createElement('div');
            div.className = 'date-divider';
            div.textContent = msg.date;
            list.appendChild(div);
        }
        var wrap = document.createElement('div');
        wrap.className = 'bubble-wrap ' + (msg.from_user_id == CURRENT_USER_ID ? 'sent' : 'received');
        var bubble = document.createElement('div');
        bubble.className = 'bubble ' + (msg.from_user_id == CURRENT_USER_ID ? 'sent' : 'received');
        bubble.textContent = msg.body;
        var time = document.createElement('span');
        time.className = 'bubble-time';
        time.textContent = msg.sent_at || '';
        wrap.appendChild(bubble);
        wrap.appendChild(time);
        list.appendChild(wrap);
    });
    if (atBottom) list.scrollTop = list.scrollHeight;
}

if (ACTIVE_CHAT_ID > 0) setInterval(fetchMessages, 4000);

function showSidebar() {
    document.getElementById('conv-sidebar').classList.remove('hidden-mobile');
    document.getElementById('msg-panel').classList.add('hidden-mobile');
}
</script>

</body>
</html>