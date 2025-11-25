<?php
/**
 * Edit Forum Message
 *
 * Allows users to edit their own messages (or admins to edit any message)
 */

require_once '../includes/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['player'])) {
    $templateData = [
        'isLoggedIn' => false,
    ];

    $template = TemplateEngine::getInstance();
    $template->display('forums/edit.latte', $templateData);
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

        $themessage = strip_tags($_REQUEST['themessage']);

        // Update message
        $stmt = $db->prepare("UPDATE km_messages SET message = :message WHERE msgid = :msgid");
        $stmt->execute([
            'message' => $themessage,
            'msgid' => $messageID,
        ]);

        $templateData = [
            'isLoggedIn' => true,
            'playername' => $userstats['playername'],
            'showForm' => false,
            'success' => true,
            'messageID' => $messageID,
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
            die("Could not get message");
        }

        // Check permissions
        if ($userstats['ID'] == $message['posterid'] || $userstats['status'] == 3) {
            // Show form
            $templateData = [
                'isLoggedIn' => true,
                'playername' => $userstats['playername'],
                'messageID' => $messageID,
                'messageText' => $message['message'],
                'showForm' => true,
            ];
        } else {
            die("You cannot edit this");
        }
    }

    // Render template
    $template = TemplateEngine::getInstance();
    $template->display('forums/edit.latte', $templateData);

} catch (PDOException $e) {
    error_log("Error in forums/edit.php: " . $e->getMessage());
    echo "<p>An error occurred. Please try again later.</p>";
}
