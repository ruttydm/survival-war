<?php
/**
 * User Management
 *
 * Admin interface to view and delete users
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

$ID = $_GET['ID'] ?? null;

if ($ID) {
    // Delete user
    try {
        $stmt = $db->prepare("DELETE FROM km_users WHERE ID = :id");
        $stmt->execute(['id' => $ID]);

        $latte->render(__DIR__ . '/../templates/admin/manageuser_delete.latte');
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        $latte->render(__DIR__ . '/../templates/admin/manageuser_delete.latte', [
            'error' => "Error deleting user. Please try again."
        ]);
    }
} else {
    // List users with pagination
    $start = $_GET['start'] ?? 0;
    $start = (int)$start;

    try {
        // Select users with pagination
        $stmt = $db->prepare("SELECT * FROM km_users ORDER BY playername ASC LIMIT :start, 20");
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Pagination
        $countStmt = $db->query("SELECT COUNT(*) as total FROM km_users");
        $total = $countStmt->fetch()['total'];

        $pages = [];
        $pageNum = 1;
        for ($i = 0; $i < $total; $i += 20) {
            $pages[$pageNum] = $i;
            $pageNum++;
        }

        $latte->render(__DIR__ . '/../templates/admin/manageuser_list.latte', [
            'users' => $users,
            'pages' => $pages
        ]);
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        $latte->render(__DIR__ . '/../templates/admin/manageuser_delete.latte', [
            'error' => "Error loading users. Please try again."
        ]);
    }
}
