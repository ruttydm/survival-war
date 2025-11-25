<?php
/**
 * Module to display the top users by land
 */
require_once 'includes/bootstrap.php';

if (!isset($start)) {
    $start = 0;
}

// Build pagination
$order = "SELECT * from km_users";
$order2 = $db->query($order);
$d = 0;
$f = 0;
$g = 1;
$pages = [];
while ($order3 = $order2->fetch()) {
    if ($f % 20 == 0) {
        $pages[] = [
            'start' => $d,
            'number' => $g
        ];
        $g++;
    }
    $d = $d + 1;
    $f++;
}

// Get top players for current page
$topplayers = $db->prepare("SELECT * from km_users order by land DESC Limit :start, 20");
$topplayers->execute(['start' => (int)$start]);
$topPlayers = [];
while ($topplayer3 = $topplayers->fetch()) {
    $topPlayers[] = [
        'ID' => $topplayer3['ID'],
        'playername' => strip_tags($topplayer3['playername']),
        'land' => $topplayer3['land']
    ];
}

// Prepare template data
$templateData = [
    'pages' => $pages,
    'topPlayers' => $topPlayers
];

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/topland.latte', $templateData);
?>


