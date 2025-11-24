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

        echo "<center><h3>Kill Monster Admin</h3></center><br>";
        echo "<center>";
        echo "<table border='0' width='70%' cellspacing='20'>";
        echo "<tr><td width='25%' valign='top'>";
        include 'left.php';
        echo "</td>";
        echo "<td valign='top' width='75%'>";
        echo "User Deleted";
        echo "</td></tr></table>";
        echo "</center>";
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        echo "Error deleting user. Please try again.";
    }
} else {
    // List users with pagination
    echo "<center><h3>Kill Monster Admin</h3></center><br>";
    echo "<center>";
    echo "<table border='0' width='70%' cellspacing='20'>";
    echo "<tr><td width='25%' valign='top'>";
    include 'left.php';
    echo "</td>";
    echo "<td valign='top' width='75%'>";
    echo "All users listed in ABC order";

    $start = $_GET['start'] ?? 0;
    $start = (int)$start;

    try {
        // Select users with pagination
        $stmt = $db->prepare("SELECT * FROM km_users ORDER BY playername ASC LIMIT :start, 20");
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->execute();

        echo "<table border='1' bordercolor='white' bgcolor='#e1e1e1'>";
        echo "<tr><td>Username</td><td>E-mail</td><td>Delete</td></tr>";

        while ($user = $stmt->fetch()) {
            $username = htmlspecialchars($user['playername']);
            $email = htmlspecialchars($user['email']);
            $userId = (int)$user['ID'];
            echo "<tr><td>$username</td><td>$email</td><td><a href='manageuser.php?ID=$userId'>Delete</a></td></tr>";
        }

        echo "</table>";
        echo "</td></tr></table>";
        echo "</center>";

        // Pagination
        $countStmt = $db->query("SELECT COUNT(*) as total FROM km_users");
        $total = $countStmt->fetch()['total'];

        echo "Page: ";
        $pageNum = 1;
        for ($i = 0; $i < $total; $i += 20) {
            echo "<a href='manageuser.php?start=$i'>$pageNum</a> ";
            $pageNum++;
        }
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        echo "Error loading users. Please try again.";
    }
}
