<?php
/**
 * Forum Thread Messages
 *
 * Displays all messages in a thread
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/messages.latte', $templateData);
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

    // Validate message ID
    $messageID = filter_var($_GET['ID'] ?? 0, FILTER_VALIDATE_INT);
    if (!$messageID) {
        die("Invalid message ID");
    }

    // Get main message
    $stmt = $db->prepare("SELECT * FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.msgid = :msgid");
    $stmt->execute(['msgid' => $messageID]);
    $mainMessage = $stmt->fetch();

    if (!$mainMessage) {
        die("Could not get message");
    }

    // Get forum info
    $stmt = $db->prepare("SELECT forumname FROM km_forums WHERE forumID = :forumID");
    $stmt->execute(['forumID' => $mainMessage['forumparent']]);
    $forum = $stmt->fetch();

    // Process main message
    $message = stripslashes($mainMessage['message']);
    $message = strip_tags($message);
    $message = nl2br($message);

    $mainMessageData = [
        'msgid' => $mainMessage['msgid'],
        'playername' => $mainMessage['playername'],
        'message' => $message,
        'realtime' => $mainMessage['realtime'],
        'isAdmin' => ($mainMessage['status'] == 3),
        'canEdit' => ($userstats['ID'] == $mainMessage['posterid'] || $userstats['status'] == 3),
    ];

    // Get replies
    $stmt = $db->prepare("SELECT * FROM km_messages a, km_users b WHERE b.ID=a.posterid AND a.parentid = :parentid");
    $stmt->execute(['parentid' => $messageID]);

    $replies = [];
    while ($reply = $stmt->fetch()) {
        $replyMessage = stripslashes($reply['message']);
        $replyMessage = strip_tags($replyMessage);
        $replyMessage = nl2br($replyMessage);

        $replies[] = [
            'msgid' => $reply['msgid'],
            'playername' => $reply['playername'],
            'message' => $replyMessage,
            'realtime' => $reply['realtime'],
            'isAdmin' => ($reply['status'] == 3),
            'canEdit' => ($userstats['ID'] == $reply['posterid'] || $userstats['status'] == 3),
        ];
    }

    // Prepare template data
    $templateData = [
        'isLoggedIn' => true,
        'forumID' => $mainMessage['forumparent'],
        'forumName' => $forum['forumname'],
        'messageID' => $messageID,
        'mainMessage' => $mainMessageData,
        'replies' => $replies,
    ];

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/messages.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/messages.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
