<?php

include "connect.php";

if (isset($_POST['submit'])) // name of submit button
{
    $player=$_POST['player'];
    $password=$_POST['password'];
    $player=strip_tags($player);
    $password=md5($password);
    $query = "select * from km_users where playername='$player' and password='$password' and validated='1'"; 
    $result = mysql_query($query) or die("No te Gusta") ;
    $result2=mysql_fetch_array($result);
    if($result2)
    {
       session_start();
       $_SESSION['player']=$player;
       echo "<script>
window.location.href = 'index.php';
</script>";
    }
    else
    {
       print "Wrong username or password or non-activated account.";
    }
}

?>