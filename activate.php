<?php
/**
 * User Account Activation
 *
 * Activates user account via email activation link
 */

require_once 'includes/bootstrap.php';

print "<link rel='stylesheet' href='style.css' type='text/css'>";

try {
    // Sanitize inputs
    $username = Validator::sanitizeString($_GET['player'] ?? '', 50);
    $keynode = Validator::sanitizeString($_GET['keynode'] ?? '', 64);
    $password = $_GET['password'] ?? ''; // Legacy parameter, optional

    if (empty($username) || empty($keynode)) {
        print "<table class='maintable'>";
        print "<tr class='headline'><td><center>Registering...</center></td></tr>";
        print "<tr class='forumrow'><td><center>";
        print "Invalid activation link.";
        print "</center></td></tr></table>";
        exit;
    }

    // Build query based on available parameters
    if (!empty($password)) {
        // Legacy activation link with password
        $stmt = $db->prepare("
            SELECT * FROM km_users
            WHERE playername = :username
            AND validkey = :keynode
        ");
        $stmt->execute([
            'username' => $username,
            'keynode' => $keynode
        ]);
        $user = $stmt->fetch();

        // Verify password for legacy links
        if ($user) {
            $passwordValid = false;

            // Check if password is MD5 (legacy) or modern hash
            if (strlen($user['password']) === 32 && ctype_xdigit($user['password'])) {
                // Legacy MD5 password
                if (md5($password) === $user['password']) {
                    $passwordValid = true;
                }
            } else {
                // Modern password hash
                if (password_verify($password, $user['password'])) {
                    $passwordValid = true;
                }
            }

            if (!$passwordValid) {
                $user = null; // Invalidate if password doesn't match
            }
        }
    } else {
        // Modern activation link (only username and keynode)
        $stmt = $db->prepare("
            SELECT * FROM km_users
            WHERE playername = :username
            AND validkey = :keynode
        ");
        $stmt->execute([
            'username' => $username,
            'keynode' => $keynode
        ]);
        $user = $stmt->fetch();
    }

    if (!$user) {
        print "<table class='maintable'>";
        print "<tr class='headline'><td><center>Registering...</center></td></tr>";
        print "<tr class='forumrow'><td><center>";
        print "No such user or invalid activation link.";
        print "</center></td></tr></table>";
    } else {
        // Activate the account
        $updateStmt = $db->prepare("UPDATE km_users SET validated = '1' WHERE playername = :username");
        $updateStmt->execute(['username' => $username]);

        print "<table class='maintable'>";
        print "<tr class='headline'><td><center>Registering...</center></td></tr>";
        print "<tr class='forumrow'><td><center>";
        print "Account activated successfully!<br><br>";
        print "<a href='login.php'>Click here to login</a>";
        print "</center></td></tr></table>";
    }
} catch (PDOException $e) {
    error_log("Activation error: " . $e->getMessage());
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Registering...</center></td></tr>";
    print "<tr class='forumrow'><td><center>";
    print "An error occurred during activation. Please try again or contact support.";
    print "</center></td></tr></table>";
}
?>
