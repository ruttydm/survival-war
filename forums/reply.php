<?php
/**
 * Reply to Forum Thread
 *
 * Allows users to reply to an existing thread
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/reply.latte', $templateData);
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
        if (!isset($_POST['themessage'])) {
            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'forumid' => $forumid,
                'showForm' => false,
                'error' => 'You did not put a message.',
            ];
        } else {
            // Process the reply
            $subject = strip_tags($_POST['subject']);
            $themessage = strip_tags($_POST['themessage']);
            $fid = filter_var($_POST['fid'], FILTER_VALIDATE_INT);
            $messageID = filter_var($_GET['ID'] ?? 0, FILTER_VALIDATE_INT);

            if (!$fid) {
                die("Invalid forum ID");
            }
            if (!$messageID) {
                die("Invalid message ID");
            }

            $unixtime = time();
            $realtime = date("D M d, Y H:i:s");

            // Insert reply
            $stmt = $db->prepare("INSERT INTO km_messages (posterid, time, realtime, subject, message, parentid, forumparent) VALUES (:posterid, :time, :realtime, :subject, :message, :parentid, :forumparent)");
            $stmt->execute([
                'posterid' => $userstats['ID'],
                'time' => $unixtime,
                'realtime' => $realtime,
                'subject' => $subject,
                'message' => $themessage,
                'parentid' => $messageID,
                'forumparent' => $fid,
            ]);

            // Update parent message
            $stmt = $db->prepare("UPDATE km_messages SET realtime = :realtime, lastreplied = :lastreplied, numreplies = numreplies+1, time = :time WHERE msgid = :msgid");
            $stmt->execute([
                'realtime' => $realtime,
                'lastreplied' => $userstats['playername'],
                'time' => $unixtime,
                'msgid' => $messageID,
            ]);

            // Update forum stats
            $stmt = $db->prepare("UPDATE km_forums SET timelastpost = :timelastpost, lastposter = :lastposter, numposts = numposts+1, realtimelastpost = :realtimelastpost WHERE forumID = :forumID");
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
                'messageID' => $messageID,
                'showForm' => false,
                'success' => true,
                'redirectUrl' => "messages.php?forumid=" . htmlspecialchars($fid, ENT_QUOTES, 'UTF-8') . "&ID=" . htmlspecialchars($messageID, ENT_QUOTES, 'UTF-8'),
            ];
        }
    } else {
        // Validate message ID
        $messageID = filter_var($_GET['ID'] ?? 0, FILTER_VALIDATE_INT);

        if (!$messageID) {
            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'forumid' => $forumid,
                'showForm' => false,
                'error' => 'You did not specify a thread to reply to.',
            ];
        } else {
            // Show form
            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'forumid' => $forumid,
                'messageID' => $messageID,
                'showForm' => true,
            ];
        }
    }

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/reply.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/reply.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
