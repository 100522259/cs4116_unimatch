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

date_default_timezone_set('Europe/Dublin');

$current_user_id = (int)$_SESSION['user_id'];
$username        = htmlspecialchars($_SESSION['username'] ?? 'User');

// Relationship matches
$relationship_matches = [];
$stmt = $conn->prepare(
    "SELECT m.user_id2 as other_id, p.first_name, p.age, i.interest1, i.interest2, img.profile_pic
    FROM matches m
    INNER JOIN matches m2
        ON m.user_id1 = m2.user_id2 AND m.user_id2 = m2.user_id1
    INNER JOIN personal_info p
        ON p.user_id = m.user_id2
    LEFT JOIN interests i
        ON i.user_id = p.user_id
    LEFT JOIN images img
        ON img.user_id = p.user_id
    WHERE m.user_id1 = ? AND m.romantic = 1
    ORDER BY m.created_at DESC");
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $relationship_matches[] = $row;
$stmt->close();

// Friendship matches
$friendship_matches = [];
$stmt = $conn->prepare(
    "SELECT m.user_id2 as other_id, p.first_name, p.age, i.interest1, i.interest2, img.profile_pic
    FROM matches m
    INNER JOIN matches m2
        ON m.user_id1 = m2.user_id2 AND m.user_id2 = m2.user_id1
    INNER JOIN personal_info p
        ON p.user_id = m.user_id2
    LEFT JOIN interests i
        ON i.user_id = p.user_id
    LEFT JOIN images img
        ON img.user_id = p.user_id
    WHERE m.user_id1 = ? AND m.friendship = 1
    ORDER BY m.created_at DESC");
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $friendship_matches[] = $row;
$stmt->close();

// Pending
$pending = [];
$stmt = $conn->prepare(
    "SELECT A.u1 as other_id, p.first_name, p.age, i.interest1, i.interest2, img.profile_pic,
         A.romantic, B.friendship FROM
        (select user_id1 as u1, user_id2 as u2, romantic, friendship, created_at from matches where romantic=1 or friendship=1) as A
        LEFT OUTER JOIN
        (select user_id1 as u2, user_id2 as u1, romantic, friendship from matches) as B
        ON A.u1 = B.u1 and A.u2 = B.u2
        JOIN personal_info p   
        	ON p.user_id = A.u1
        LEFT JOIN interests i
        	ON i.user_id = p.user_id
        LEFT JOIN images img   
        	ON img.user_id = p.user_id
        WHERE (B.u2 is null OR B.romantic=0 OR B.friendship=0) AND A.u2 = ?
        ORDER BY A.created_at DESC");

$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (!($row['romantic'] == 1 || $row['friendship'] == 1)) {
        $pending[] = $row;
    }
}
$stmt->close();
$conn->close();

function avatarHtml(string $name, ?string $pic): string {
    $initial = strtoupper(substr($name, 0, 1));
    $colors  = ['#2e7d32','#1565c0','#6a1b9a','#c62828','#e65100','#00695c','#4527a0'];
    $color   = $colors[ord($initial) % count($colors)];
    if (!empty($pic)) {
        return '<img src="' . htmlspecialchars($pic) . '" alt="' . htmlspecialchars($name) . '" class="card-avatar">';
    }
    return '<div class="card-avatar avatar-initials" style="background:' . $color . ';">' . $initial . '</div>';
}

function renderCard(array $m, bool $isPending = false): string {
    $name    = htmlspecialchars($m['first_name'] ?? 'Unknown');
    $age     = !empty($m['age']) ? (int)$m['age'] : null;
    $int1    = !empty($m['interest1']) ? htmlspecialchars($m['interest1']) : '';
    $int2    = !empty($m['interest2']) ? htmlspecialchars($m['interest2']) : '';
    $other   = (int)$m['other_id'];
    $avatar  = avatarHtml($m['first_name'] ?? '?', $m['profile_pic'] ?? null);

    $tags = '';
    if ($int1) $tags .= '<span class="tag">' . $int1 . '</span>';
    if ($int2) $tags .= '<span class="tag">' . $int2 . '</span>';

    if ($isPending) {
        $label  = (!empty($m['romantic']) && empty($m['r_status'])) ? 'Relationship request' : 'Friendship request';
        $action = '<div class="pending-notice"><span class="pending-dot"></span>' . $label . ' — go to <a href="home.php">Home</a> to match back</div>';
    } else {
        $action = '<a href="messaging.php?with=' . $other . '" class="msg-btn">Message</a>
                   <a href="user_view.php?id=' . $other . '" class="profile-btn">View Profile</a>';
    }

    return '
    <div class="match-card' . ($isPending ? ' match-card-pending' : '') . '">
        <div class="card-avatar-wrap">' . $avatar . '</div>
        <div class="card-body">
            <div class="card-name">' . $name . ($age ? '<span class="card-age">' . $age . '</span>' : '') . '</div>
            ' . ($tags ? '<div class="card-tags">' . $tags . '</div>' : '') . '
            <div class="card-actions">' . $action . '</div>
        </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches — UniMatch</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="css\profile.css" rel="stylesheet">
    <link href="css\profile_mobile.css" rel="stylesheet">
    
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        .um-nav {
            background: #1B5E3B; padding: 0 28px;
            display: flex; align-items: center; justify-content: space-between;
            height: 62px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
        }
        .um-nav-brand {
            color: white; font-size: 22px; font-weight: 700;
            text-decoration: none; display: flex; align-items: center; gap: 8px;
        }
        .um-nav-links { display: flex; gap: 4px; }
        .um-nav-links a {
            color: rgba(255,255,255,0.82); text-decoration: none;
            padding: 7px 15px; border-radius: 20px; font-size: 14px;
            font-weight: 500; transition: background 0.2s;
        }
        .um-nav-links a:hover  { background: rgba(255,255,255,0.13); color: white; }
        .um-nav-links a.active { background: rgba(255,255,255,0.18); color: white; }
        .um-nav-links a.logout { color: rgba(255,255,255,0.5); }

        .page-wrap  { max-width: 820px; margin: 36px auto; padding: 0 18px 40px; }
        .page-title { font-size: 28px; font-weight: 700; color: #1B5E3B; margin: 0 0 4px; }
        .page-sub   { font-size: 14px; color: #6B7280; margin: 0 0 28px; }

        .tabs { display: flex; gap: 10px; margin-bottom: 26px; flex-wrap: wrap; }
        .tab-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 22px; border-radius: 28px;
            border: 2px solid #c8e6c9; background: white;
            font-size: 14px; font-weight: 600; color: #2e7d32;
            cursor: pointer; font-family: Arial, sans-serif; transition: all 0.2s;
        }
        .tab-btn:hover  { border-color: #2e7d32; background: #f1f8f2; }
        .tab-btn.active { background: #1B5E3B; border-color: #1B5E3B; color: white; }
        .tab-count {
            font-size: 11px; font-weight: 700; padding: 2px 8px;
            border-radius: 12px; background: #e8f5e9; color: #2e7d32;
        }
        .tab-btn.active .tab-count { background: rgba(255,255,255,0.22); color: white; }
        .tab-btn.pending-tab { color: #bf360c; border-color: #ffccbc; }
        .tab-btn.pending-tab .tab-count { background: #fbe9e7; color: #bf360c; }
        .tab-btn.pending-tab.active { background: #bf360c; border-color: #bf360c; color: white; }
        .tab-btn.pending-tab.active .tab-count { background: rgba(255,255,255,0.22); color: white; }

        .tab-pane        { display: none; }
        .tab-pane.active { display: block; }

        .match-card {
            background: white; border-radius: 16px; padding: 20px;
            margin-bottom: 14px; display: flex; align-items: center; gap: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1.5px solid transparent;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
        }
        .match-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.10); transform: translateY(-2px); border-color: #c8e6c9; }
        .match-card-pending { border-color: #ffe0b2; background: #fffdf9; }
        .match-card-pending:hover { border-color: #ffb74d; }

        .card-avatar-wrap { flex-shrink: 0; }
        .card-avatar {
            width: 78px; height: 78px; border-radius: 50%;
            object-fit: cover; border: 3px solid #a5d6a7; display: block;
        }
        .card-avatar.avatar-initials {
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 28px; font-weight: 700;
        }

        .card-body  { flex: 1; min-width: 0; }
        .card-name  {
            font-size: 20px; font-weight: 700; color: #111827;
            display: flex; align-items: baseline; gap: 8px;
            flex-wrap: wrap; margin-bottom: 8px;
        }
        .card-age   { font-size: 14px; font-weight: 400; color: #9CA3AF; }
        .card-tags  { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
        .tag {
            background: #e8f5e9; color: #2e7d32; border-radius: 20px;
            padding: 4px 13px; font-size: 12px; font-weight: 600;
        }

        .card-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .msg-btn {
            display: inline-flex; align-items: center;
            background: #1B5E3B; color: white; text-decoration: none;
            padding: 9px 22px; border-radius: 24px; font-size: 13px; font-weight: 600;
            transition: background 0.2s, transform 0.15s;
        }
        .msg-btn:hover { background: #2e7d32; transform: translateY(-1px); color: white; }
        .profile-btn {
            display: inline-flex; align-items: center;
            background: transparent; color: #2e7d32; text-decoration: none;
            padding: 9px 22px; border-radius: 24px; font-size: 13px; font-weight: 600;
            border: 1.5px solid #c8e6c9; transition: background 0.2s, border-color 0.2s;
        }
        .profile-btn:hover { background: #f1f8f2; border-color: #2e7d32; }

        .pending-notice {
            font-size: 13px; color: #bf360c;
            display: flex; align-items: center; gap: 7px; font-style: italic;
        }
        .pending-notice a { color: #bf360c; font-weight: 700; }
        .pending-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #bf360c; flex-shrink: 0;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.3; } }

        .empty-state {
            text-align: center; padding: 60px 20px; background: white;
            border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .empty-icon { font-size: 52px; margin-bottom: 14px; }
        .empty-state p { color: #9CA3AF; font-size: 15px; margin: 0 0 18px; }
        .empty-state a {
            display: inline-block; background: #1B5E3B; color: white;
            text-decoration: none; padding: 10px 26px;
            border-radius: 24px; font-size: 14px; font-weight: 600;
        }

        @media (max-width: 540px) {
            .um-nav { padding: 0 14px; }
            .um-nav-links a { padding: 6px 10px; font-size: 12px; }
            .match-card { flex-direction: column; align-items: flex-start; gap: 14px; }
            .card-avatar { width: 64px; height: 64px; font-size: 22px; }
            .tab-btn { padding: 8px 14px; font-size: 13px; }
        }
    </style>
</head>
<body>

<div class="container">
    <?php include "./sidebar.php"; ?>
    
    <div class="main">
        <h1 class="page-title">Your Matches</h1>
        <p class="page-sub">Welcome back, <?php echo $_SESSION["username"]; ?></p>

        <div class="tabs" role="tablist">
            <button class="tab-btn active" id="btn-relationship" onclick="switchTab('relationship')">
                ❤️ Relationships <span class="tab-count"><?php echo count($relationship_matches); ?></span>
            </button>
            <button class="tab-btn" id="btn-friendship" onclick="switchTab('friendship')">
                🤝 Friendships <span class="tab-count"><?php echo count($friendship_matches); ?></span>
            </button>
            <button class="tab-btn pending-tab" id="btn-pending" onclick="switchTab('pending')">
                ⏳ Pending <span class="tab-count"><?php echo count($pending); ?></span>
            </button>
        </div>

        <div class="tab-pane active" id="tab-relationship">
            <?php if (empty($relationship_matches)): ?>
                <div class="empty-state">
                    <div class="empty-icon">💛</div>
                    <p>No relationship matches yet</p>
                    <a href="home.php">Keep Exploring</a>
                </div>
            <?php else: ?>
                <?php foreach ($relationship_matches as $m): echo renderCard($m); endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="tab-pane" id="tab-friendship">
            <?php if (empty($friendship_matches)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🤝</div>
                    <p>No friendship matches yet</p>
                    <a href="home.php">Keep Exploring</a>
                </div>
            <?php else: ?>
                <?php foreach ($friendship_matches as $m): echo renderCard($m); endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="tab-pane" id="tab-pending">
            <?php if (empty($pending)): ?>
                <div class="empty-state">
                    <div class="empty-icon">⏳</div>
                    <p>No pending requests right now</p>
                </div>
            <?php else: ?>
                <?php foreach ($pending as $m): echo renderCard($m, true); endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-pane').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('btn-' + tab).classList.add('active');
}
</script>
</body>
</html>