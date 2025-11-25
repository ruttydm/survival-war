<?php
/**
 * Edit Forum Category
 *
 * Admin interface to edit forum categories
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Update forum
    $ID = $_POST['ID'] ?? null;
    $title = Validator::sanitizeString($_POST['title'] ?? '', 100);
    $description = Validator::sanitizeString($_POST['description'] ?? '', 500);

    if (strlen($title) < 1) {
        $message = "You did not enter a title.";
    } else {
        try {
            $stmt = $db->prepare("UPDATE km_forums SET forumname = :title, descrip = :description WHERE forumID = :id");
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'id' => $ID
            ]);
            $message = "Forum updated.";
        } catch (PDOException $e) {
            error_log("Error updating forum: " . $e->getMessage());
            $message = "Error updating forum. Please try again.";
        }
    }

    $template = TemplateEngine::getInstance();
    $template->display('admin/edit_result.latte', [
        'message' => $message
    ]);
} else {
    // Show edit form
    $ID = $_GET['ID'] ?? null;

    if ($ID) {
        try {
            $stmt = $db->prepare("SELECT * FROM km_forums WHERE forumID = :id");
            $stmt->execute(['id' => $ID]);
            $forum = $stmt->fetch();

            if ($forum) {
                $template = TemplateEngine::getInstance();
                $template->display('admin/edit_form.latte', [
                    'forum' => $forum
                ]);
            } else {
                $template = TemplateEngine::getInstance();
                $template->display('admin/edit_form.latte', [
                    'error' => "Forum not found."
                ]);
            }
        } catch (PDOException $e) {
            error_log("Error fetching forum: " . $e->getMessage());
            $template = TemplateEngine::getInstance();
            $template->display('admin/edit_form.latte', [
                'error' => "Error loading forum. Please try again."
            ]);
        }
    } else {
        $template = TemplateEngine::getInstance();
        $template->display('admin/edit_form.latte', [
            'error' => "No forum ID specified."
        ]);
    }
}
