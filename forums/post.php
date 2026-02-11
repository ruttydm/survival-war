<?php
/**
 * Create New Forum Topic
 *
 * Allows users to create a new thread in a forum
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/post.latte', $templateData);
    exit;
}

try {
    $player = $_SESSION['player'];

    // Get user stats
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $stmt->execute(['player' => $player]);
    $userstats = $stmt->fetch();

    if (!$userstats) {
        die("Could not get user stats");
    }

    // Validate forum ID
    $forumid = filter_var($_GET['forumid'] ?? 0, FILTER_VALIDATE_INT);
    if (!$forumid) {
        die("Invalid forum ID");
    }

    // Check if form was submitted
    if (isset($_POST['submit'])) {
        // Validate input
        if (strlen($_POST['subject'] ?? '') < 1) {
            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'forumid' => $forumid,
                'showForm' => false,
                'error' => 'There is not subject.',
            ];
        } elseif (!isset($_POST['themessage'])) {
            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'forumid' => $forumid,
                'showForm' => false,
                'error' => 'There is no message.',
            ];
        } else {
            // Process the post
            $subject = strip_tags($_POST['subject']);
            $themessage = strip_tags($_POST['themessage']);
            $fid = filter_var($_POST['fid'], FILTER_VALIDATE_INT);

            if (!$fid) {
                die("Invalid forum ID");
            }

            $unixtime = time();
            $realtime = date("D M d, Y H:i:s");

            // Insert message
            $stmt = $db->prepare("INSERT INTO km_messages (posterid, time, realtime, subject, message, forumparent) VALUES (:posterid, :time, :realtime, :subject, :message, :forumparent)");
            $stmt->execute([
                'posterid' => $userstats['ID'],
                'time' => $unixtime,
                'realtime' => $realtime,
                'subject' => $subject,
                'message' => $themessage,
                'forumparent' => $fid,
            ]);

            // Update forum stats
            $stmt = $db->prepare("UPDATE km_forums SET timelastpost = :timelastpost, lastposter = :lastposter, numposts = numposts+1, numtopics = numtopics+1, realtimelastpost = :realtimelastpost WHERE forumID = :forumID");
            $stmt->execute([
                'timelastpost' => $realtime,
                'lastposter' => $userstats['playername'],
                'realtimelastpost' => $unixtime,
                'forumID' => $fid,
            ]);

            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'forumid' => $fid,
                'showForm' => false,
                'success' => true,
                'redirectUrl' => "forum.php?ID=" . htmlspecialchars($fid, ENT_QUOTES, 'UTF-8'),
            ];
        }
    } else {
        // Show form
        $templateData = [
            'isLoggedIn' => true,
            'playername' => $userstats['playername'],
            'forumid' => $forumid,
            'showForm' => true,
        ];
    }

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/post.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/post.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
