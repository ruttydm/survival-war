<?php
/**
 * Forum Topics Listing
 *
 * Displays topics in a specific forum
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/forum.latte', $templateData);
    exit;
}

try {
    $player = $_SESSION['player'];

    // Get user stats
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $stmt->execute(['player' => $player]);
    $userstats = $stmt->fetch();

    if (!$userstats) {
        die("Could not get user info");
    }

    // Update user activity
    $thedate = time();
    $checktime = $thedate - 200;

    $stmt = $db->prepare("UPDATE km_users SET lasttime = :lasttime WHERE ID = :id");
    $stmt->execute(['lasttime' => $thedate, 'id' => $userstats['ID']]);

    if ($userstats['tsgone'] < $checktime) {
        $stmt = $db->prepare("UPDATE km_users SET tsgone = :tsgone, oldtime = :oldtime WHERE ID = :id");
        $stmt->execute([
            'tsgone' => $thedate,
            'oldtime' => $userstats['tsgone'],
            'id' => $userstats['ID']
        ]);
    }

    // Validate forum ID
    $forumID = filter_var($_GET['ID'] ?? 0, FILTER_VALIDATE_INT);
    if (!$forumID) {
        die("Invalid forum ID");
    }

    // Pagination
    $numtopicsperpage = 15;
    $start = filter_var($_GET['start'] ?? 0, FILTER_VALIDATE_INT);
    if ($start === false || $start < 0) {
        $start = 0;
    }

    // Get topics
    $stmt = $db->prepare("SELECT * FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.parentid='0' AND a.forumparent = :forumID ORDER BY a.time DESC LIMIT :start, 20");
    $stmt->bindValue(':forumID', $forumID, PDO::PARAM_INT);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->execute();

    $topics = [];
    while ($topic = $stmt->fetch()) {
        $subject = $topic['subject'];
        $subject = str_replace("';", "@", $subject);
        $subject = str_replace('";', '@', $subject);
        $subject = strip_tags($subject);

        $topics[] = [
            'msgid' => $topic['msgid'],
            'subject' => $subject,
            'playername' => $topic['playername'],
            'numreplies' => $topic['numreplies'],
            'realtime' => $topic['realtime'],
            'hasNewPosts' => ($topic['time'] > $userstats['oldtime']),
        ];
    }

    // Get total topic count for pagination
    $stmt = $db->prepare("SELECT COUNT(*) FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.parentid='0' AND a.forumparent = :forumID ORDER BY time DESC");
    $stmt->execute(['forumID' => $forumID]);
    $totalTopics = $stmt->fetchColumn();

    // Build pagination
    $prev = $start - 20;
    $next = $start + 20;
    $pages = [];
    $f = 0;
    $d = 0;
    $g = 1;

    while ($f < $totalTopics) {
        if ($f % 20 == 0) {
            if ($f >= $start - 3 * 20 && $f <= $start + 7 * 20) {
                $pages[] = [
                    'number' => $g,
                    'start' => $d,
                ];
                $g++;
            }
        }
        $d = $d + 1;
        $f++;
    }

    // Prepare template data
    $templateData = [
        'isLoggedIn' => true,
        'forumID' => $forumID,
        'topics' => $topics,
        'pagination' => [
            'start' => $start,
            'prev' => $prev,
            'next' => $next,
            'pages' => $pages,
            'totalTopics' => $totalTopics,
            'showPrev' => ($start >= 20),
            'showNext' => ($start <= $totalTopics - $numtopicsperpage),
            'last' => $totalTopics - 20,
        ],
    ];

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/forum.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/forum.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
