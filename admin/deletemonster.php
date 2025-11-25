<?php
/**
 * Delete Monster
 *
 * Admin interface to delete monsters
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

$ID = $_GET['ID'] ?? null;

if ($ID) {
    // Delete monster
    try {
        $stmt = $db->prepare("DELETE FROM km_monsters WHERE ID = :id");
        $stmt->execute(['id' => $ID]);
        $message = "Monster deleted Successfully";
    } catch (PDOException $e) {
        error_log("Error deleting monster: " . $e->getMessage());
        $message = "Error deleting monster. Please try again.";
    }

    $template = TemplateEngine::getInstance();
    $template->display('admin/deletemonster_result.latte', [
        'message' => $message
    ]);
} else {
    // List monsters
    try {
        $stmt = $db->query("SELECT * FROM km_monsters ORDER BY skill ASC");
        $monsters = $stmt->fetchAll();

        $template = TemplateEngine::getInstance();
        $template->display('admin/deletemonster_list.latte', [
            'monsters' => $monsters
        ]);
    } catch (PDOException $e) {
        error_log("Error fetching monsters: " . $e->getMessage());
        $message = "Error loading monsters. Please try again.";
        $template = TemplateEngine::getInstance();
        $template->display('admin/deletemonster_result.latte', [
            'message' => $message
        ]);
    }
}
