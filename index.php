<?php
//start session
session_start();

//include html
include'up_html.php'; 

//include connect
include 'connect.php';

//if user is online
if (isset($_SESSION['player'])) 
  {
  	
  	//playerstats
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    
    //if user is dead
    if($userstats3[dead]=='Yes')
    {
    	
	  ?>
	  <p class='td_width_bold'>You have been slain by <?php echo $userstats3[killer]; ?></p>
      <form action='revive.php' method='post'>
      <input type='hidden' name='ID' value='<?php echo $userstats3[ID]; ?>'>
      <input type='submit' name='revives' value='revive' class='RedButton' style='width:80px'></form>
	  </div><br><br><br>
	  <?php
	  
    }
    
    //if user is alive
    else
    {
      $updaterefresh="update km_users set justattacked=0 where ID='$userstats3[ID]'";
      mysql_query($updaterefresh) or die("Died");

      
		//index content		
?>
        <p>Welcome, <?php echo $player; ?> into the world of survivors!</p>
		<table class='maintable'><tr class='headline'><td><center>Statistics</center></td></tr>
        <tr class='mainrow'><td><b>Turns: <?php echo $userstats3[numturns]; ?></b></td></tr>
		<tr class='mainrow'><td><b>skill pts: <?php echo $userstats3[skillpts]; ?></b></td></tr>
		<tr class='mainrow'><td><b>Honor: <?php echo $userstats3[honor]; ?></b></td></tr>
		<tr class='mainrow'><td><b>Gold: <?php echo $userstats3[gold]; ?></b></td></tr>
		<tr class='mainrow'><td><b>Land: <?php echo $userstats3[land]; ?></b></td></tr>
		<tr class='mainrow'><td><b>offensive army: <?php echo $userstats3[offarmy]; ?></b></td></tr>
		<tr class='mainrow'><td><b>Defensive army: <?php echo $userstats3[dffarmy]; ?></b></td></tr>
		<tr class='mainrow'><td><b>training pts: <?php echo $userstats3[science]; ?></b></p></td></tr></table><br>
		
		<table class='maintable'><tr class='mainrow'><td><tr class='headline'><td><center>User settings:</center></td></tr>
		<tr class="mainrow"><td><br><br><b><a href='../setpass.php'>Change password</a></b><br><br></td></tr>
		</table>
<?php

    }
    
    //if user is not logged in
  }
else
  {
     echo "<script>
window.location.href = 'login.php';
</script>";
  
  }
  
//include html  
include'down_html.php';
?>



