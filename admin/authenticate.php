<?php
/**
 * Admin Authentication
 *
 * Handles admin login with support for legacy MD5 passwords
 * and modern password_hash() passwords.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (isset($_POST['submit'])) {
    try {
        // Sanitize inputs
        $isadmin = Validator::sanitizeString($_POST['isadmin'] ?? '', 50);
        $password = $_POST['password'] ?? '';

        if (empty($isadmin) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            // Query admin user by playername with status=3 (admin status)
            $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :playername AND status = '3' AND validated = '1'");
            $stmt->execute(['playername' => $isadmin]);
            $admin = $stmt->fetch();

            if ($admin) {
                $passwordValid = false;

                // Check if password is MD5 (legacy) or modern hash
                if (strlen($admin['password']) === 32 && ctype_xdigit($admin['password'])) {
                    // Legacy MD5 password
                    if (md5($password) === $admin['password']) {
                        $passwordValid = true;

                        // Rehash password with modern algorithm
                        $newHash = password_hash($password, PASSWORD_ARGON2ID);
                        $updateStmt = $db->prepare("UPDATE km_users SET password = :password WHERE id = :id");
                        $updateStmt->execute(['password' => $newHash, 'id' => $admin['id']]);
                    }
                } else {
                    // Modern password hash
                    if (password_verify($password, $admin['password'])) {
                        $passwordValid = true;

                        // Check if rehash is needed (algorithm change)
                        if (password_needs_rehash($admin['password'], PASSWORD_ARGON2ID)) {
                            $newHash = password_hash($password, PASSWORD_ARGON2ID);
                            $updateStmt = $db->prepare("UPDATE km_users SET password = :password WHERE id = :id");
                            $updateStmt->execute(['password' => $newHash, 'id' => $admin['id']]);
                        }
                    }
                }

                if ($passwordValid) {
                    // Set admin session variables
                    Session::setAdmin($admin['playername']);
                    $_SESSION['adminname'] = $admin['playername']; // Legacy compatibility
                    $_SESSION['isadmin'] = 'IAMADMIN'; // Legacy compatibility

                    // Redirect to admin index
                    echo "<script>
window.location.href = 'index.php';
</script>";
                    exit;
                } else {
                    $error = "Wrong username or password.";
                }
            } else {
                $error = "Wrong username or password.";
            }
        }
    } catch (PDOException $e) {
        error_log("Admin authentication error: " . $e->getMessage());
        $error = "An error occurred. Please try again.";
    }

    // If we got here, authentication failed
    if (isset($error)) {
        $template = TemplateEngine::getInstance();
        $template->display('admin/authenticate.latte', [
            'error' => $error
        ]);
    }
}
