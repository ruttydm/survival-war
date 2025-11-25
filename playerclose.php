<?php
/**
 * Lists all users close in rank to your range
 */
require_once 'includes/bootstrap.php';

$templateData = [
    'loggedIn' => false,
    'players' => []
];

if (isset($_SESSION['player'])) {
    $player = $_SESSION['player'];
    $getplayerpoints = $db->prepare("SELECT * from km_users where playername=:player");
    $getplayerpoints->execute(['player' => $player]);
    $getplayerpoints3 = $getplayerpoints->fetch();

    if (!$getplayerpoints3) {
        die("Could not get player points");
    }

    $numrows = $db->prepare("SELECT COUNT(*) as count from km_users where skillpts>=:skillpts");
    $numrows->execute(['skillpts' => $getplayerpoints3['skillpts']]);
    $numrows3 = $numrows->fetch()['count'];

    $total = $db->query("SELECT COUNT(*) as count from km_users");
    $total3 = $total->fetch()['count'];

    $numrows4 = $numrows3 + 20;
    if ($numrows4 >= $total3) {
        $numrows4 = $total3;
    }
    $numrows5 = $numrows3 - 20;
    if ($numrows5 < 0) {
        $numrows5 = 0;
    }

    $getrank = $db->prepare("SELECT * from km_users order by skillpts desc limit :offset,:limit");
    $getrank->execute(['offset' => (int)$numrows5, 'limit' => (int)$numrows4]);

    $players = [];
    while ($getrank3 = $getrank->fetch()) {
        $players[] = [
            'ID' => $getrank3['ID'],
            'playername' => $getrank3['playername'],
            'skillpts' => $getrank3['skillpts'],
            'dead' => $getrank3['dead'],
            'isCurrentPlayer' => $getplayerpoints3['ID'] == $getrank3['ID']
        ];
    }

    $templateData['loggedIn'] = true;
    $templateData['players'] = $players;
}

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/playerclose.latte', $templateData);
?>
  
 
