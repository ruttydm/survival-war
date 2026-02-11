<?php
/**
 * User Management
 *
 * Admin interface to view, verify, and delete users
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

$action = $_GET['action'] ?? 'list';
$userId = $_GET['id'] ?? null;

// Handle actions
if ($action === 'verify' && $userId) {
    // Verify user
    try {
        $stmt = $db->prepare("UPDATE km_users SET validated = '1' WHERE ID = :id");
        $stmt->execute(['id' => $userId]);
        
        $_SESSION['admin_message'] = 'User verified successfully';
        $_SESSION['admin_message_type'] = 'success';
    } catch (PDOException $e) {
        error_log("Error verifying user: " . $e->getMessage());
        $_SESSION['admin_message'] = 'Error verifying user';
        $_SESSION['admin_message_type'] = 'danger';
    }
    header('Location: manageuser.php');
    exit;
}

if ($action === 'delete' && $userId) {
    // Delete user
    try {
        $stmt = $db->prepare("DELETE FROM km_users WHERE ID = :id");
        $stmt->execute(['id' => $userId]);
        
        $_SESSION['admin_message'] = 'User deleted successfully';
        $_SESSION['admin_message_type'] = 'success';
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        $_SESSION['admin_message'] = 'Error deleting user';
        $_SESSION['admin_message_type'] = 'danger';
    }
    header('Location: manageuser.php');
    exit;
}

// List users with filtering and pagination
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';
$start = (int)($_GET['start'] ?? 0);
$perPage = 25;

try {
    // Build query based on filter
    $whereClause = '';
    $params = [];
    
    if ($filter === 'unverified') {
        $whereClause = "WHERE validated = '0'";
    } elseif ($filter === 'verified') {
        $whereClause = "WHERE validated = '1'";
    } elseif ($filter === 'dead') {
        $whereClause = "WHERE dead = '1'";
    } elseif ($filter === 'alive') {
        $whereClause = "WHERE dead = '0'";
    }
    
    if (!empty($search)) {
        $whereClause .= ($whereClause ? ' AND ' : 'WHERE ');
        $whereClause .= "(playername LIKE :search OR email LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    // Get total count
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM km_users $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Get users
    $stmt = $db->prepare("SELECT ID, playername, email, validated, dead, land, honor, lastaction, ip 
                          FROM km_users $whereClause 
                          ORDER BY playername ASC 
                          LIMIT :start, :perPage");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    // Calculate pagination
    $totalPages = ceil($total / $perPage);
    $currentPage = floor($start / $perPage) + 1;
    
    // Get admin message if exists
    $message = $_SESSION['admin_message'] ?? null;
    $messageType = $_SESSION['admin_message_type'] ?? 'info';
    unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
    
    $template = TemplateEngine::getInstance();
    $template->display('admin/manageuser.latte', [
        'users' => $users,
        'total' => $total,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'perPage' => $perPage,
        'start' => $start,
        'filter' => $filter,
        'search' => $search,
        'message' => $message,
        'messageType' => $messageType
    ]);
} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $template = TemplateEngine::getInstance();
    $template->display('admin/manageuser.latte', [
        'users' => [],
        'total' => 0,
        'message' => 'Error loading users. Please try again.',
        'messageType' => 'danger',
        'filter' => $filter,
        'search' => $search
    ]);
}
