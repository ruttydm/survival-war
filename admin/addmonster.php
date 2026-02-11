<?php
/**
 * Add Monster
 *
 * Admin interface to create new monsters
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (!Session::isAdminLoggedIn()) {
    echo "Sorry, not logged in as administrator, please <a href='login.php'>Login</a>";
    exit;
}

if (isset($_POST['submit'])) {
    // Process form submission
    $image = Validator::sanitizeString($_POST['image'] ?? '', 255);
    $monstername = Validator::sanitizeString($_POST['monstername'] ?? '', 100);
    $energycost = (int)($_POST['energycost'] ?? 0);
    $skillpts = (int)($_POST['skillpts'] ?? 0);
    $killpts = (int)($_POST['killpts'] ?? 0);
    $gold = (int)($_POST['goldpts'] ?? 0);

    try {
        // Check if monster already exists
        $stmt = $db->prepare("SELECT * FROM km_monsters WHERE name = :name");
        $stmt->execute(['name' => $monstername]);
        $existingMonster = $stmt->fetch();

        if ($existingMonster) {
            $message = "Sorry there is already a monster of that name";
        } else {
            // Create new monster (fixed SQL syntax - added missing comma)
            $stmt = $db->prepare("INSERT INTO km_monsters (name, skill, pointsifkilled, goldworth, energycost, image)
                                  VALUES (:name, :skill, :pointsifkilled, :goldworth, :energycost, :image)");
            $stmt->execute([
                'name' => $monstername,
                'skill' => $skillpts,
                'pointsifkilled' => $killpts,
                'goldworth' => $gold,
                'energycost' => $energycost,
                'image' => $image
            ]);
            $message = "Monster created successfully<br>";
        }
    } catch (PDOException $e) {
        error_log("Error creating monster: " . $e->getMessage());
        $message = "Error creating monster. Please try again.";
    }

    $template = TemplateEngine::getInstance();
    $template->display('admin/addmonster_result.latte', [
        'message' => $message
    ]);
} else {
    // Show form
    $template = TemplateEngine::getInstance();
    $template->display('admin/addmonster_form.latte');
}
