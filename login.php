<?php
session_start();
include 'up_html.php';
if(isset($_SESSION['player']))
{
echo "<script>
window.location.href = 'index.php';
</script>";
}
else
{	
?>
<form method='POST' action='authenticate.php'>
<input placeholder="Username" type='text' name='player' size='15' class='inline_text_inp' style='width:150px'><br>
<input placeholder="Password" type='password' name='password' size='15' mask='x' style='width:150px'><br><input type='submit' value='Login' name='submit' class='RedButton' style='width:80px'></form><br><br><br><br>



<br>
<br>

<form action='getpass.php'>
    <input class='RedButton' style='width:120px' type='submit' value='Get Password'>
</form>

<form action='register.php'>
    <input class='RedButton' style='width:120px' type='submit' value='Register'>
</form>


<?php
};
include 'down_html.php'; 

?>
