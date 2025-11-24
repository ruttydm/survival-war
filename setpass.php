<?php
/**
 * Password Change Module
 *
 * Allows logged-in users to change their password
 */

require_once 'includes/bootstrap.php';

include "up_html.php";

if (isset($_SESSION['player'])) {
    $player = $_SESSION['player'];

    if (isset($_POST['submit']) || isset($_POST['password'])) {
        try {
            $password = $_POST['password'] ?? '';

            // Validate password length
            if (strlen($password) < 1) {
                print "You need to enter a password.";
            } elseif (strlen($password) < 6) {
                print "Password must be at least 6 characters long.";
            } else {
                // Hash password with modern algorithm
                $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

                // Update password in database
                $stmt = $db->prepare("UPDATE km_users SET password = :password WHERE playername = :player");
                $stmt->execute([
                    'password' => $hashedPassword,
                    'player' => $player
                ]);

                // Clear session to force re-login
                Session::destroy();

                print "<div style='margin: 20px;'>";
                print "Password changed successfully!<br><br>";
                print "For security, you have been logged out. Please <a href='login.php'>login</a> with your new password.";
                print "</div>";
            }
        } catch (PDOException $e) {
            error_log("Password change error for user '$player': " . $e->getMessage());
            print "An error occurred while changing your password. Please try again.";
        }
    } else {
        // Display password change form
        print "<table class='maintable'>";
        print "<tr class='headline'><td><center>Change Password</center></td></tr>";
        print "<tr class='mainrow'><td>";
        print "<form action='' method='post'>";
        print "<div style='padding: 20px;'>";
        print "<label for='password'>Type new password:</label><br>";
        print "<input type='password' name='password' id='password' size='20' minlength='6' required><br><br>";
        print "<small>Password must be at least 6 characters long</small><br><br>";
        print "<input type='submit' name='submit' value='Change Password' class='RedButton'>";
        print "</div>";
        print "</form>";
        print "</td></tr></table><br><br>";
    }
} else {
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Access Denied</center></td></tr>";
    print "<tr class='mainrow'><td><center>";
    print "You are not logged in.<br><br>";
    print "<a href='login.php'>Click here to login</a>";
    print "</center></td></tr></table>";
}

include "down_html.php";
?>
