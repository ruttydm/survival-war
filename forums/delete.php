<?php
/**
 * Delete Forum Message
 *
 * Allows users to delete their own messages (or admins to delete any message)
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/delete.latte', $templateData);
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

    // Check if form was submitted
    if (isset($_POST['submit'])) {
        $messageID = filter_var($_POST['msgid'], FILTER_VALIDATE_INT);
        if (!$messageID) {
            die("Invalid message ID");
        }

        // Get message info
        $stmt = $db->prepare("SELECT * FROM km_messages WHERE msgid = :msgid");
        $stmt->execute(['msgid' => $messageID]);
        $message = $stmt->fetch();

        if (!$message) {
            die("Could not get message");
        }

        // Check if it's a parent message (topic) or reply
        if ($message['parentid'] == 0) {
            // Delete topic and all replies
            $totalposts = $message['numreplies'] + 1;

            $stmt = $db->prepare("DELETE FROM km_messages WHERE parentid = :parentid");
            $stmt->execute(['parentid' => $messageID]);

            $stmt = $db->prepare("DELETE FROM km_messages WHERE msgid = :msgid");
            $stmt->execute(['msgid' => $messageID]);

            $stmt = $db->prepare("UPDATE km_forums SET numposts = numposts - :totalposts, numtopics = numtopics - 1 WHERE forumID = :forumID");
            $stmt->execute([
                'totalposts' => $totalposts,
                'forumID' => $message['forumparent'],
            ]);
        } else {
            // Delete single reply
            $stmt = $db->prepare("DELETE FROM km_messages WHERE msgid = :msgid");
            $stmt->execute(['msgid' => $messageID]);

            $stmt = $db->prepare("UPDATE km_messages SET numreplies = numreplies - 1 WHERE msgid = :msgid");
            $stmt->execute(['msgid' => $message['parentid']]);

            $stmt = $db->prepare("UPDATE km_forums SET numposts = numposts - 1 WHERE forumID = :forumID");
            $stmt->execute(['forumID' => $message['forumparent']]);
        }

        $templateData = [
            'isLoggedIn' => true,
            'showForm' => false,
            'success' => true,
        ];
    } else {
        // Validate message ID
        $messageID = filter_var($_GET['ID'] ?? 0, FILTER_VALIDATE_INT);
        if (!$messageID) {
            die("Invalid message ID");
        }

        // Get message
        $stmt = $db->prepare("SELECT * FROM km_messages WHERE msgid = :msgid");
        $stmt->execute(['msgid' => $messageID]);
        $message = $stmt->fetch();

        if (!$message) {
            die("Message not found");
        }

        // Check permissions
        if ($userstats['ID'] == $message['posterid'] || $userstats['status'] == 3) {
            // Show confirmation form
            $templateData = [
                'isLoggedIn' => true,
                'messageID' => $messageID,
                'showForm' => true,
            ];
        } else {
            die("You cannot edit this");
        }
    }

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/delete.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/delete.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
