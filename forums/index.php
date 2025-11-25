<?php
/**
 * Forum Listing
 *
 * Displays all available forums with topics and post counts
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/index.latte', $templateData);
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

    // Get all forums
    $stmt = $db->query("SELECT * FROM km_forums ORDER BY forumorder ASC");
    $forums = [];

    while ($forum = $stmt->fetch()) {
        $forums[] = [
            'forumID' => $forum['forumID'],
            'forumname' => $forum['forumname'],
            'descrip' => $forum['descrip'],
            'numtopics' => $forum['numtopics'],
            'numposts' => $forum['numposts'],
            'lastposter' => $forum['lastposter'],
            'timelastpost' => $forum['timelastpost'],
            'hasNewPosts' => ($forum['realtimelastpost'] > $userstats['oldtime']),
        ];
    }

    // Prepare template data
    $templateData = [
        'isLoggedIn' => true,
        'forums' => $forums,
    ];

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/index.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/index.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
