<?php 
session_start();
include 'up_html.php'; 
?>

<html>
<form method="post" action="reguser.php">
Type Username Here: <input type="text" name="player" size="15" type="text" class='inline_text_inp' style='width:150px'><br>
Type Password Here: <input type="password" name="password" size="15" type="text" class='inline_text_inp' style='width:150px'><br>
Retype password: <input type="password" name="pass2" size="15" type="text" class='inline_text_inp' style='width:150px'><br>
Type E-mail address: <input type="text" name="email" size="20" type="text" class='inline_text_inp' style='width:150px'><br>
<input type="submit" value="submit" class="RedButton" style="width:80px" type="text" class='inline_text_inp' style='width:150px'>
</form>
<br>	
<form method="post" action="login.php">
<input type="submit" value="cancel" class="RedButton" style="width:80px" type="text" class='inline_text_inp' style='width:150px'>
	
</form>
</html>


<?php include 'down_html.php'; ?>