<?php
/**
 * Reset Game
 *
 * Admin interface to reset the game (delete all users)
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Reset game (delete all users)
    try {
        $db->exec("DELETE FROM km_users");
        $message = "Game reset successfully";
    } catch (PDOException $e) {
        error_log("Error resetting game: " . $e->getMessage());
        $message = "Error resetting game. Please try again.";
    }

    $template = TemplateEngine::getInstance();
    $template->display('admin/reset_result.latte', [
        'message' => $message
    ]);
} else {
    // Show confirmation form
    $template = TemplateEngine::getInstance();
    $template->display('admin/reset_confirm.latte');
}
