<?php

//include connect
require_once 'includes/bootstrap.php';

// Check authentication
if (!isset($_SESSION['player'])) {
    header('Location: login.php');
    exit;
}

$resultMessage = null;
$monsters = [];

try {
    $player = $_SESSION['player'];

    // Get player stats
    $playerstats1 = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $playerstats1->execute(['player' => $player]);
    $playerstats3 = $playerstats1->fetch(PDO::FETCH_ASSOC);

    if (!$playerstats3) {
        die("Could not find player");
    }

    $monstername = isset($_POST['monstername']) ? Validator::sanitizeString($_POST['monstername'], 50) : null;

    $selmonster3 = null;
    $energy0 = 0;
    $energycost = 0;

    if ($monstername) {
        // Get selected monster
        $selmonster = $db->prepare("SELECT * FROM km_monsters WHERE name = :monstername");
        $selmonster->execute(['monstername' => $monstername]);
        $selmonster3 = $selmonster->fetch(PDO::FETCH_ASSOC);

        if ($selmonster3) {
            $energy0 = ($playerstats3['numturns'] - $selmonster3['energycost']);
            $energycost = $selmonster3['energycost'];
        }
    }

    //if sumbit was pressed
    if(isset($_POST['submit']))
    {

     //if the player's turns is empty
         if($energy0 < 0)
         {
             $resultMessage = "<div id='dialog'><h2><strong><center>You have not enough turns!</center></strong></h2></div>";
         }

         //if the player's energy isn't empty
         else
         {
         //if the animal's not exist
         if (!$selmonster3)
         {
           $resultMessage = "There is not a Animal of that name";
         }

         //if it exist
         else
         {
           $totalskill = $playerstats3['skillpts'] + $selmonster3['skill'];
           $randomnumber = rand(1, $totalskill);

           //if the player won
           if($randomnumber <= $playerstats3['skillpts'])
           {
             $gained = $selmonster3['pointsifkilled'];
             $gold = $selmonster3['goldworth'];

             // Update player stats
             $updateplayerstats = $db->prepare("UPDATE km_users SET skillpts = skillpts + :gained, gold = gold + :gold, numturns = numturns - :energycost WHERE playername = :player");
             $updateplayerstats->execute([
                 'gained' => $gained,
                 'gold' => $gold,
                 'energycost' => $energycost,
                 'player' => $player
             ]);

             $resultMessage = "<div id='dialog' title='Won!'><center><img src='images/knight.gif'></center>";
             $resultMessage .= "You have succesfully hunted the " . Validator::escapeHtml($selmonster3['name']) . " and you have gained " . Validator::escapeHtml($gained) . " skillpts</div>";
           }

           //if the player lose
           else
           {
             // Update player stats (lose scenario)
             // Note: Original code has $gained variable used here but it's not set in lose condition
             // Preserving original logic even though $gained is undefined in this branch
             $gained = 0; // Initialize to avoid undefined variable
             $updateplayerstats = $db->prepare("UPDATE km_users SET numturns = numturns - :energycost, skillpts = skillpts + :gained WHERE playername = :player");
             $updateplayerstats->execute([
                 'energycost' => $energycost,
                 'gained' => $gained,
                 'player' => $player
             ]);

             $resultMessage = "<div id='dialog' title='Lose!'><center><img src='images/defeat.gif'></center>";
             $resultMessage .= "You failed to hunt the " . Validator::escapeHtml($selmonster3['name']) . "!<br><br></div>";
           }
         }

         }

   }

    // Get all monsters for display
    $monster1 = $db->prepare("SELECT * FROM km_monsters ORDER BY skill ASC");
    $monster1->execute();

    while ($monster3 = $monster1->fetch(PDO::FETCH_ASSOC))
    {
        $totalskill = $playerstats3['skillpts'] + $monster3['skill'];
        $chance = ($playerstats3['skillpts'] / $totalskill * 100);
        $chance2 = round($chance, 0, PHP_ROUND_HALF_UP);

        $monsters[] = [
            'image' => $monster3['image'],
            'name' => $monster3['name'],
            'chance' => $chance2,
            'goldworth' => $monster3['goldworth'],
            'energycost' => $monster3['energycost'],
        ];
    }

} catch (PDOException $e) {
    ErrorHandler::handleException($e);
    die("A database error occurred. Please try again later.");
} catch (Exception $e) {
    ErrorHandler::handleException($e);
    die("An error occurred. Please try again later.");
}

// Prepare template data
$templateData = [
    'resultMessage' => $resultMessage,
    'monsters' => $monsters,
];

$template = TemplateEngine::getInstance();
$template->display('pages/slaymonster.latte', $templateData);
?>
