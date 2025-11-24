<?php

require_once 'includes/bootstrap.php';
include 'up_html.php';

function render_battle_logs(PDO $db): void {
  // Check if user is logged in
  $player_name = $_SESSION['player'] ?? null;
  if ($player_name === null) {
    return;
  }

  // Fetch user stats
  $statement = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
  $statement->execute(['player' => $player_name]);
  $user_stats = $statement->fetch();

  if (!$user_stats) {
    die("Could not get user stats");
  }

  // Handle attack notifications
  $attack_count = (int)$user_stats['numberattck'];
  if ($attack_count > 0) {
    echo "You have survived {$attack_count} attacks since your last login.<br><br>";

    // Reset attack counter
    $reset_statement = $db->prepare("UPDATE km_users SET numberattck = '0' WHERE playername = :player");
    $reset_statement->execute(['player' => $player_name]);
  }

  // Close previous table context (legacy structure)
  echo "</td></tr></table><br><br>";

  // Render Battle Records Table
  echo "<table class='maintable'>";
  echo "<tr class='headline'><td colspan='4'><center>Battle records</center></td></tr>";
  echo "<tr class='mainrow'>
          <td>Attacker ID</td>
          <td>Attacker name</td>
          <td>Result</td>
          <td>Land lost</td>
        </tr>";

  $records_query = $db->query("SELECT * FROM km_battlerecords");
  while ($record = $records_query->fetch()) {
    echo "<tr class='mainrow'>
            <td>{$record['attid']}</td>
            <td>{$record['attname']}</td>
            <td>{$record['result']}</td>
            <td>{$record['landlost']}</td>
          </tr>";
  }
  echo "</table><br><br>";
}

render_battle_logs($db);

include 'down_html.php';