<?php
/**
 * Add Forum Category
 *
 * Admin interface to create new forum categories
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Process form submission
    $forumname = Validator::sanitizeString($_POST['forumname'] ?? '', 100);
    $forumdesc = Validator::sanitizeString($_POST['forumdesc'] ?? '', 500);

    if (strlen($forumname) < 1) {
        $message = "You did not enter a forum name.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO km_forums (forumname, descrip) VALUES (:forumname, :forumdesc)");
            $stmt->execute([
                'forumname' => $forumname,
                'forumdesc' => $forumdesc
            ]);
            $message = "Forum created.<br>";
        } catch (PDOException $e) {
            error_log("Error creating forum: " . $e->getMessage());
            $message = "Error creating forum. Please try again.";
        }
    }

    $latte->render(__DIR__ . '/../templates/admin/addforum_result.latte', [
        'message' => $message
    ]);
} else {
    // Show form
    $latte->render(__DIR__ . '/../templates/admin/addforum_form.latte');
}
