<?php 

//include connect
include 'connect.php';

//start session
session_start();

//include html
include "up_html.php";

if (isset($_SESSION['player'])) 
   {
	  $player=$_SESSION['player'];
      $playerstats1="SELECT * from km_users where playername='$player'";
      $playerstats2=mysql_query($playerstats1) or die ("Could not find player");
      $playerstats3=mysql_fetch_array($playerstats2);
      $monstername=$_POST['monstername'];
      $monstername=strip_tags($monstername);
      $selmonster="SELECT * from km_monsters where name='$monstername'";
      $selmonster2=mysql_query($selmonster) or die ("Cannot select Monster");
      $selmonster3=mysql_fetch_array($selmonster2);
      $energy0=($playerstats3[numturns] - $selmonster3[energycost]);
      $energycost=$selmonster3[energycost];
//if sumbit was pressed
    if(isset($_POST['submit']))
    {
     
     //if the player's turns is empty
		 if($energy0<0)
		 {
			 echo "<div id='dialog'><h2><strong><center>You have not enough turns!</center></strong></h2></div>";
		 }
		 
		 //if the player's energy isn't empty
		 else
		 {            
         //if the animal's not exist
         if (!$selmonster3)
         {
           print "There is not a Animal of that name";
         }
         
         //if it exist
         else
         {
           $totalskill=$playerstats3[skillpts]+$selmonster3[skill];
           $randomnumber=rand(1,$totalskill);
           
           //if the player won
           if($randomnumber<=$playerstats3[skillpts])
           {
             $gained=$selmonster3[pointsifkilled];
             $gold=$selmonster3[goldworth];
             $updateplayerstats="Update km_users set skillpts=skillpts+'$gained', gold=gold+'$gold', numturns=numturns-'$energycost' where playername='$player'";
             mysql_query($updateplayerstats) or die("Could not update player stats");			 
             print "<div id='dialog' title='Won!'><center><img src='images/knight.gif'></center>";
             print "You have succesfully hunted the $selmonster3[name] and you have gained $gained skillpts</div>";
           }
           
           //if the player lose
           else
           {
			 $updateplayerstats="Update km_users set numturns=numturns-'$energycost', skillpts=skillpts+'$gained' where playername='$player'";
             mysql_query($updateplayerstats) or die("Could not update player stats");
             print "<div id='dialog' title='Lose!'><center><img src='images/defeat.gif'></center>";
             print "You failed to hunt the $selmonster3[name]!<br><br></div>";
           }
		 }
                   
		 }
     
   }
	
      print "<table class='maintable'>";
      print "<tr class='headline'><td><center>Select Monster to Slay</center></td></tr>";
      print "<tr class='mainrow'><td>";
      print "<form method='post' action=''>";

      $monster1="SELECT * from km_monsters order by skill asc";
      $monster2=mysql_query($monster1) or die("Could not select Animal");
      while ($monster3=mysql_fetch_array($monster2))
      {
		$totalskill=$playerstats3[skillpts]+$monster3[skill];
		$chance=($playerstats3[skillpts] / $totalskill * 100);
		$chance2=round($chance, 0, PHP_ROUND_HALF_UP);  
		  print "<img src='$monster3[image]' height='100'><br><INPUT TYPE = 'radio' NAME = 'monstername' VALUE = $monster3[name] CHECKED><B>$monster3[name]</B><br><p>$chance2% chance, 
		  reward:$monster3[goldworth] gold and skillpoints, you lose:$monster3[energycost]% energy</p><hr>";
      }
	  print "<br>";
      print "<input type='submit' name='submit' value='Hunt Animal'></form>";
      print "</td></tr></table><br>";	
}

else //not logged in
   {

    print "You are not logged in, please <A href='login.php'>Login</a>";

   }
	

include "down_html.php";
?>
