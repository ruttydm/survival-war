<?php
/**
 * Delete Forum Category
 *
 * Admin interface to delete forum categories
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Delete forum
    $ID = $_POST['ID'] ?? null;

    if ($ID) {
        try {
            $stmt = $db->prepare("DELETE FROM km_forums WHERE forumID = :id");
            $stmt->execute(['id' => $ID]);
            $message = "Forum deleted.";
        } catch (PDOException $e) {
            error_log("Error deleting forum: " . $e->getMessage());
            $message = "Error deleting forum. Please try again.";
        }
    } else {
        $message = "No forum ID specified.";
    }

    $template = TemplateEngine::getInstance();
    $template->display('admin/delete_result.latte', [
        'message' => $message
    ]);
} else {
    // Show confirmation form
    $ID = $_GET['ID'] ?? null;

    if ($ID) {
        $template = TemplateEngine::getInstance();
        $template->display('admin/delete_confirm.latte', [
            'ID' => $ID
        ]);
    } else {
        $template = TemplateEngine::getInstance();
        $template->display('admin/delete_confirm.latte', [
            'error' => "No forum ID specified."
        ]);
    }
}
