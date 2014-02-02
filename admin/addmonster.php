<?php
//killmonster admin main page, from here you can add and delete monsters
include "connect.php";
session_start();
?>
<?

if (isset($_SESSION['isadmin'])) //if there is an administrative session
  {

     if(isset($_POST['submit'])) //if submit has been pushed to create a monster
     {
       print "<center><h3>Kill Monster Admin</h3></center><br>";
       print "<center>";
       print "<table border='0' width='70%' cellspacing='20'>";
       print "<tr><td width='25%' valign='top'>";
       include 'left.php';
       print "</td>";
       print "<td valign='top' width='75%'>";
       $image=$_POST['image'];
       $monstername=$_POST['monstername'];
	   $energycost=$_POST['energycost'];
       $skillpts=$_POST['skillpts'];
       $killpts=$_POST['killpts'];
       $gold=$_POST['goldpts'];
       $checkname="SELECT * from km_monsters where name='$monstername'";
       $checkname2=mysql_query($checkname) or die("Could not query monsters");
       while($checkname3=mysql_fetch_array($checkname2))
       {
         $themonster=$checkname3[name]; //set a variable if there is already a monster of the same name
       }
       if($themonster) 
       {
         print "Sorry there is already a monster of that name";
       }
       else //if there is no such monster, then create one
       {
        $createmonster="INSERT into km_monsters (name, skill, pointsifkilled, goldworth, energycost, image) VALUES 
        ('$monstername', '$skillpts', '$killpts','$gold', '$energycost' '$image')";
        
        mysql_query($createmonster) or die("Could not create monster");
        print "Monster created successfully<br>";
       }
 
       print "</td></tr></table>";    
       print "</center>";
 

     }
     
     else
     {

       print "<center><h3>Kill Monster Admin</h3></center><br>";
       print "<center>";
       print "<table border='0' width='70%' cellspacing='20'>";
       print "<tr><td width='25%' valign='top'>";
       include 'left.php';
       print "</td>";
       print "<td valign='top' width='75%'>";
       print "In creating a monster, you will specify the monster's name, skill points if the monster has, and skill points gained if they monster is killed, only integers please, otherwise it will round down<br>";
       print "<form action='addmonster.php' method='post'>";
       print "Monster's name:<br>";
       print "<input type='text' name='monstername' size='15'><br>";
       print "Monster's skill points:<br>";
       print "<input type='text' name='skillpts' size='6'><br>";
       print "Image:<br>";
       print "<input type='text' name='image'><br>";
       print "Skill points gained by players if killed:<br>";
       print "<input type='text' name='killpts' size='6'><br>";
       print "Energy losed by if killed:<br>";
       print "<input type='text' name='energycost' size='6'><br>";		 
       print "Gold if killed:<br>";
       print "<input type='text' name='goldpts' size='6'><br>";
       print "<input type='submit' name='submit' value='Create Monster'>";
       print "</form>";
       print "</td></tr></table>";    
       print "</center>";
     }
  }
else //if not logged in as admin
  {
    print "Sorry, not logged in as administrator, please <A href='login.php'>Login</a>";
  }

?>