<?php
require_once 'includes/bootstrap.php';

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/playerlist.latte', []);
?>
