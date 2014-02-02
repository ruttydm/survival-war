<?php
//killmonster amin index
session_start();
include "up_html.php";

 print "<table class='maintable'><tr class='headline'><td><center>Buy Land</center></td></tr>";
      print "<tr class='mainrow'><td>Land Costs $500 per acre(very expensive).<br>";
      print "<form action='' method='post'>";
      print "<input type='text' placeholder='Arces to buy' name='landacres' class='inline_text_inp' style='width:150px;' >&nbsp;";
      print "<input type='submit' name='submit' value='buy' class='RedButton' style='width:93px'></form></td></tr></table><br><br>";
      print "<td valign='top'>";


      

if (isset($_SESSION['player'])) 
  {
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    if(isset($_POST['submit']))
    {
       print "<center>";
       print "<table class='maintable'>";
       print "<tr class='headline'><td><center>Buy Land</center></td></tr>";
       print "<tr class='mainrow'><td>";
       $landacres=$_POST['landacres'];
       $landacres=strip_tags($landacres);
       $total=$landacres*500;
       if($landacres<0)
       {
          print "You cannot buy negative land. <A href='index.php'>Go back to main</a>.";
       }
       else if($userstats3[numturns]<1)
       {
          die("You must have at least 1 turn to buy land. Go back to <A href='index.php'>Main</a>");
       }
       else if($userstats3[gold]<$total)
       {
          print "You do not have enough cash to buy that many acres. <A href='index.php'>Go back to main</a>.";
       }
       else if($userstats3[gold]>=$total)
       {
          $getland="update km_users set gold=gold-'$total', land=land+'$landacres', numturns=numturns-1 where ID='$userstats3[ID]'";
          mysql_query($getland) or die("Could not buy land");
          print "You bought $landacres acres of land. <A href='index.php'>Go back to main</a>.";
       }
       print "</td></tr></table>";       
   
       print "<font size='1'>Script Produced by © <A href='http://www.chipmunk-scripts.com'>Chipmunk Scripts</a></font>";
    }
  }
else
  {
     echo "<script>
window.location.href = 'login.php';
</script>";
  
  }
include "down_html.php";

?>

