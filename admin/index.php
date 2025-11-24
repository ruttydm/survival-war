<?php
/**
 * Admin Dashboard
 *
 * Main admin panel landing page
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kill Monster Admin</title>
</head>
<body>
    <center><h3>Kill Monster Admin</h3></center><br>
    <center>
        <table border='0' width='70%' cellspacing='20'>
            <tr>
                <td width='25%' valign='top'>
                    <?php include 'left.php'; ?>
                </td>
                <td valign='top' width='75%'>
                    Here is the Kill Monster Admin<br><br>
                    Create Monster -- Lets you Create a Monster<br><br>
                    Delete Monster -- lets you delete a monster<br><br>
                    User Management -- Lets you edit and delete users
                </td>
            </tr>
        </table>
    </center><br><br>
    <font size='1'>Script Produced by © <a href='http://www.chipmunk-scripts.com'>Chipmunk Scripts</a></font>
</body>
</html>
