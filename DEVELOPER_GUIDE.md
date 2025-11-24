# Survival War - Developer Guide

**Version:** 1.0 Beta
**Type:** Browser-based Multiplayer Strategy Game
**Tech Stack:** PHP 8.5+, MySQL 8.0+, PDO, jQuery
**Architecture:** MPA (Multi-Page Application) with server-side rendering

---

## Table of Contents

1. [Game Overview](#game-overview)
2. [Project Structure](#project-structure)
3. [Database Schema](#database-schema)
4. [Core Architecture](#core-architecture)
5. [Game Mechanics](#game-mechanics)
6. [Getting Started](#getting-started)
7. [Adding New Features](#adding-new-features)
8. [Code Patterns](#code-patterns)
9. [API Reference](#api-reference)

---

## Game Overview

### What is Survival War?

Survival War is a turn-based, persistent multiplayer strategy game set in a post-apocalyptic world. Players compete for dominance by:

- **Building armies** to attack and defend
- **Acquiring land** to support larger armies
- **Hunting animals** for resources and skill points
- **Challenging other players** in death matches
- **Gaining honor** through victories
- **Developing science** to improve combat effectiveness

### Core Game Loop

1. **Regenerate Turns** - Players gain 10 turns per hour (max 100)
2. **Gather Resources** - Hunt animals for gold and skill points
3. **Build Forces** - Spend gold on armies, land, and science
4. **Combat** - Attack other players or challenge them to death matches
5. **Progression** - Climb leaderboards for skill, honor, or land
6. **Community** - Interact via forums

### Win Conditions

There's no definitive "win" - players compete for:
- **Top skill points** (gained from hunting and challenges)
- **Most honor** (gained from PvP victories)
- **Largest land holdings** (purchased or captured)

---

## Project Structure

### Directory Layout

```
survival-war/
├── includes/              # Core infrastructure (NEW - modernized)
│   ├── bootstrap.php      # Application initialization
│   ├── config.php         # Configuration management
│   ├── Database.php       # PDO singleton
│   ├── Security.php       # CSRF & security headers
│   ├── Validator.php      # Input validation & sanitization
│   ├── Session.php        # Secure session management
│   ├── ErrorHandler.php   # Error handling & logging
│   └── functions.php      # Utility functions
│
├── admin/                 # Admin panel
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login page
│   ├── authenticate.php   # Admin authentication
│   ├── manageuser.php     # User CRUD
│   ├── addforum.php       # Forum category management
│   ├── addmonster.php     # Monster/animal management
│   └── [other admin files]
│
├── forums/                # Forum system
│   ├── index.php          # Forum list
│   ├── forum.php          # Topic list for a forum
│   ├── messages.php       # Thread view
│   ├── post.php           # Create new topic
│   ├── reply.php          # Reply to topic
│   ├── edit.php           # Edit post
│   └── delete.php         # Delete post
│
├── cron/                  # Scheduled tasks
│   └── cronjob.php        # Turn regeneration (runs hourly)
│
├── migrations/            # Database migrations
│   └── 001_modernization_schema.sql
│
├── logs/                  # Application logs
│   ├── .htaccess          # Prevent web access
│   └── error.log          # PHP errors (auto-generated)
│
├── images/                # Game assets
│   ├── bg.jpg             # Background
│   ├── knight.gif         # Victory animation
│   └── [other images]
│
├── Game Files (root):
│   ├── index.php          # Main dashboard
│   ├── login.php          # Login page
│   ├── logout.php         # Logout handler
│   ├── authenticate.php   # Login processor
│   ├── register.php       # Registration form
│   ├── reguser.php        # Registration processor
│   ├── activate.php       # Email activation
│   ├── getpass.php        # Password recovery
│   ├── setpass.php        # Password change
│   ├── up_html.php        # Page header template
│   ├── down_html.php      # Page footer template
│   ├── style.css          # Game styling
│   │
│   ├── attack.php         # Land attack system
│   ├── challengeplayer.php # Death match system
│   ├── slaymonster.php    # Hunting system
│   ├── buyarmy.php        # Purchase troops
│   ├── buyland.php        # Purchase land
│   ├── buyscience.php     # Purchase science/training
│   ├── revive.php         # Respawn after death
│   ├── logs.php           # Battle history
│   ├── playerlist.php     # Player list menu
│   ├── playerclose.php    # Nearby players
│   ├── top.php            # Skill leaderboard
│   ├── tophonor.php       # Honor leaderboard
│   └── topland.php        # Land leaderboard
│
├── Configuration:
│   ├── .env               # Environment variables (create from .env.example)
│   ├── .env.example       # Configuration template
│   ├── composer.json      # PHP dependencies
│   └── .gitignore         # Git ignore rules
│
└── Documentation:
    ├── README.md
    ├── MODERNIZATION_PLAN.md
    ├── PHASE1_ANALYSIS.md
    ├── PHASE2_COMPLETE.md
    └── DEVELOPER_GUIDE.md  # This file
```

---

## Database Schema

### Tables Overview

The game uses 5 main tables in a MySQL database:

#### 1. km_users (Player Data)

Primary table storing all player information.

```sql
CREATE TABLE km_users (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  playername VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,        -- Argon2ID hash
  email VARCHAR(100) NOT NULL,
  validated TINYINT(1) DEFAULT 0,        -- Email activation status
  validkey VARCHAR(64),                  -- Activation token

  -- Game Stats
  skillpts INT(11) DEFAULT 0,            -- Skill points (hunting/challenges)
  gold INT(11) DEFAULT 0,                -- Currency
  land INT(11) DEFAULT 10,               -- Territory (supports armies)
  offarmy INT(11) DEFAULT 0,             -- Offensive troops
  dffarmy INT(11) DEFAULT 0,             -- Defensive troops
  science INT(11) DEFAULT 0,             -- Training/tech level
  honor INT(11) DEFAULT 0,               -- PvP victory counter
  numturns INT(11) DEFAULT 100,          -- Energy for actions

  -- Death/Revival
  dead VARCHAR(3) DEFAULT 'no',          -- Death status
  killer VARCHAR(50),                    -- Who killed you

  -- Security/Tracking
  ip VARCHAR(45),                        -- IP address
  lastaction INT(11),                    -- Cooldown timer
  lasttime INT(11),                      -- Last action timestamp
  oldtime INT(11),                       -- Previous timestamp
  tsgone INT(11),                        -- Time since gone
  justattacked TINYINT(1) DEFAULT 0,     -- Recently attacked flag

  INDEX idx_playername (playername),
  INDEX idx_land (land),
  INDEX idx_honor (honor),
  INDEX idx_skillpts (skillpts)
) ENGINE=MyISAM;
```

**Key Fields:**
- `numturns`: Energy for actions (0-100, regenerates 10/hour)
- `land`: Each acre supports 10 troops max
- `science`: Percentage bonus to combat (e.g., 50 science = 50% boost)
- `lastaction`: Unix timestamp for cooldown enforcement

#### 2. km_admins (Admin Users)

```sql
CREATE TABLE km_admins (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,        -- Argon2ID hash
  status TINYINT(1) DEFAULT 3,          -- Admin level
  validated TINYINT(1) DEFAULT 1
) ENGINE=MyISAM;
```

#### 3. km_monsters (Huntable Animals)

```sql
CREATE TABLE km_monsters (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,             -- Animal name (e.g., "Bear")
  skill INT(11) NOT NULL,                -- Required skill to hunt
  pointsifkilled INT(11) NOT NULL,       -- Skill points reward
  goldworth INT(11) NOT NULL,            -- Gold reward
  energycost INT(11) NOT NULL,           -- Turns required
  image VARCHAR(255),                    -- Image URL
  continent VARCHAR(50)                  -- Category/region
) ENGINE=MyISAM;
```

**Example Data:**
- Mouse: 1 skill, 5 points, 20 gold, 1 turn
- Bear: 100 skill, 150 points, 500 gold, 5 turns

#### 4. km_battlerecords (Combat Logs)

```sql
CREATE TABLE km_battlerecords (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  attid INT(11) NOT NULL,                -- Attacker ID
  attname VARCHAR(50) NOT NULL,          -- Attacker name
  result VARCHAR(50) NOT NULL,           -- Win/lose description
  landlost INT(11) NOT NULL,             -- Land transferred
  victimid INT(11) NOT NULL,             -- Victim ID

  INDEX idx_attid (attid),
  INDEX idx_victimid (victimid)
) ENGINE=MyISAM;
```

#### 5. km_forums (Forum Categories)

```sql
CREATE TABLE km_forums (
  forumID INT(11) AUTO_INCREMENT PRIMARY KEY,
  forumname VARCHAR(100) NOT NULL,
  descrip TEXT,
  forumorder INT(11) DEFAULT 0,
  numtopics INT(11) DEFAULT 0,
  numposts INT(11) DEFAULT 0,
  timelastpost VARCHAR(50),
  lastposter VARCHAR(50),
  realtimelastpost INT(11)
) ENGINE=MyISAM;
```

#### 6. km_messages (Forum Posts)

```sql
CREATE TABLE km_messages (
  msgid INT(11) AUTO_INCREMENT PRIMARY KEY,
  posterid INT(11) NOT NULL,
  parentid INT(11) DEFAULT 0,            -- 0 = topic, else reply
  forumparent INT(11) NOT NULL,          -- Forum category ID
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  time VARCHAR(50),
  realtime INT(11),
  lastreplied INT(11),
  numreplies INT(11) DEFAULT 0,

  INDEX idx_forumparent (forumparent),
  INDEX idx_parentid (parentid)
) ENGINE=MyISAM;
```

---

## Core Architecture

### Request Flow

```
1. User Request
   ↓
2. bootstrap.php (includes/bootstrap.php)
   - Load configuration
   - Initialize database ($db)
   - Start session (Session class)
   - Set security headers
   - Initialize error handler
   ↓
3. Page Logic (e.g., attack.php)
   - Check authentication
   - Validate input
   - Execute database queries (PDO)
   - Calculate game logic
   - Prepare output data
   ↓
4. Template Rendering
   - up_html.php (header + navigation + stats sidebar)
   - Page content (HTML with PHP variables)
   - down_html.php (footer)
   ↓
5. Response to User
```

### Bootstrapping Process

Every page starts with:

```php
<?php
require_once 'includes/bootstrap.php';
include 'up_html.php';

// Your page logic here

include 'down_html.php';
?>
```

**What bootstrap.php does:**
1. Loads `config.php` (environment variables)
2. Includes all core classes (Database, Security, Validator, Session, ErrorHandler)
3. Initializes ErrorHandler
4. Starts secure session
5. Sets security headers
6. Provides global `$db` (PDO connection)

### Database Layer

**Singleton Pattern:**

```php
// Get database connection (already done in bootstrap)
$db = Database::getInstance()->getConnection();

// Use PDO prepared statements
$stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
$stmt->execute(['player' => $player]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
```

**Why PDO?**
- SQL injection prevention (prepared statements)
- Consistent error handling
- Better performance
- PHP 8.5 compatible

### Session Management

```php
// Check if user is logged in
if (Session::isLoggedIn()) {
    $username = Session::getUsername();
    $userId = Session::getUserId();
}

// Login user
Session::setUser($userId, $username);
$_SESSION['player'] = $username; // Legacy compatibility

// Logout
Session::destroy();
```

**Session Variables:**
- `$_SESSION['player']` - Current username (primary auth check)
- `$_SESSION['userid']` - User ID
- `$_SESSION['adminname']` - Admin username (for admin panel)

### Input Validation

```php
// Sanitize user input
$username = Validator::sanitizeString($_POST['username'] ?? '', 50);
$userId = Validator::sanitizeInt($_POST['user_id'] ?? 0);
$email = Validator::sanitizeEmail($_POST['email'] ?? '');

// Escape output (prevent XSS)
echo Validator::escapeHtml($user['playername']);
```

### Error Handling

```php
try {
    $stmt = $db->prepare("SELECT * FROM km_users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred. Please try again.");
}
```

---

## Game Mechanics

### Turn System

**Regeneration:**
- Players start with 100 turns
- Gain +10 turns per hour (cron job)
- Max capacity: 100 turns
- Actions consume turns (1-5 depending on action)

**Implementation:** `cron/cronjob.php` runs hourly:

```php
// Add 10 turns to players with ≤90 turns
UPDATE km_users SET numturns = numturns + 10 WHERE numturns <= 90

// Cap at 100 for players with >90 turns
UPDATE km_users SET numturns = 100 WHERE numturns > 90
```

### Combat System

#### 1. Land Attacks (`attack.php`)

**Requirements:**
- 3 turns
- 1 hour cooldown since last attack
- Victim must have ≥50% of your land (protection)

**Combat Formula:**

```php
// Attacker strength
$attacker_strength = $offarmy * (1 + $science/100) * (1 + $honor/1000);

// Defender strength
$defender_strength = $dffarmy * (1 + $science/100) * (1 + $honor/1000);

// Random factor
$attacker_power = rand(1, $attacker_strength);
$defender_power = rand(1, $defender_strength);

// Winner
if ($attacker_power > $defender_power) {
    // Attacker wins
} else {
    // Defender wins
}
```

**Outcomes:**

**Attacker Wins:**
- Gains 10% of victim's land
- Loses offensive army units based on strength ratio
- Victim loses defensive army units

**Attacker Loses:**
- Loses all offensive army units used
- Victim loses some defensive army units
- No land transfer

**Cooldown:** 1 hour (`lastaction` timestamp)

#### 2. Player Challenges (`challengeplayer.php`)

**Death Match Mechanics:**

**Requirements:**
- 2 turns
- Both players alive

**Combat:**

```php
$total_skill = $your_skill + $opponent_skill;
$random = rand(1, $total_skill);

if ($random <= $your_skill) {
    // You win
    $you_gain = floor($opponent_skill * 0.10);      // +10% of opponent skill
    $opponent_loses = floor($opponent_skill * 0.33); // Opponent loses 33%
    $opponent_dies = true;
    $your_honor++;
    $opponent_honor--;
} else {
    // Opponent wins (reverse)
}
```

**Stakes:**
- Winner gains 10% of loser's skill points
- Winner gains +1 honor
- Loser dies, loses 33% of skill points
- Loser loses -1 honor
- Loser must revive before playing again

#### 3. Monster Hunting (`slaymonster.php`)

**Chance-Based Combat:**

```php
$player_skill = $skillpts;
$monster_skill = $monster['skill'];
$total = $player_skill + $monster_skill;

$chance = rand(1, $total);

if ($chance <= $player_skill) {
    // Success!
    $gold += $monster['goldworth'];
    $skillpts += $monster['pointsifkilled'];
    $numturns -= $monster['energycost'];
} else {
    // Failure - lose turns only
    $numturns -= $monster['energycost'];
}
```

**Monster Progression:**
- Mouse: 1 skill required, 1 turn, small rewards
- Spider: 5 skill, 1 turn
- Rabbit: 20 skill, 2 turns
- Deer: 50 skill, 3 turns
- Hog: 75 skill, 4 turns
- Bear: 100 skill, 5 turns, large rewards

### Economy System

#### Buying Army (`buyarmy.php`)

```php
$cost_per_unit = 75;
$max_army = $land * 10; // 10 troops per acre

// Purchase offensive units
$cost = $offensive_units * 75;
$new_offarmy = $current_offarmy + $offensive_units;

// Constraints
if ($new_offarmy > $max_army) die("Not enough land!");
if ($gold < $cost) die("Not enough gold!");
if ($numturns < 1) die("Not enough turns!");

// Update
UPDATE km_users SET
    gold = gold - $cost,
    offarmy = offarmy + $offensive_units,
    numturns = numturns - 1
WHERE playername = :player
```

#### Buying Land (`buyland.php`)

```php
$cost_per_acre = 500;
$cost = $acres * 500;

// Purchase
UPDATE km_users SET
    gold = gold - $cost,
    land = land + $acres,
    numturns = numturns - 1
WHERE playername = :player
```

#### Buying Science (`buyscience.php`)

```php
$cost_per_point = 35;
$max_science = $land * 10; // Max 10x your land

// Purchase
UPDATE km_users SET
    gold = gold - $cost,
    science = science + $points,
    numturns = numturns - 1
WHERE playername = :player
```

**Science Bonus:**
- 50 science = 50% combat bonus
- 100 science = 100% combat bonus (double strength)

### Death & Revival

**Dying:**
- Set `dead = 'Yes'`
- Set `killer = $opponent_name`
- Lose 33% of skill points

**Reviving (`revive.php`):**
- Set `dead = 'no'`
- Keep remaining skill points
- Keep gold, land, armies
- No penalty for revival

### Leaderboards

**Three Rankings:**

1. **Skill Points** (`top.php`) - Total skill from hunting/challenges
2. **Honor** (`tophonor.php`) - PvP victory count
3. **Land** (`topland.php`) - Total acres owned

**Pagination:** 10 players per page

---

## Getting Started

### Prerequisites

- PHP 8.5+
- MySQL 8.0+
- Apache with mod_rewrite
- Composer (optional, for future dependencies)
- Cron job capability

### Installation

#### 1. Clone Repository

```bash
git clone <repository-url>
cd survival-war
```

#### 2. Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE survival_war CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON survival_war.* TO 'gameuser'@'localhost' IDENTIFIED BY 'password';
```

#### 3. Import Schema

```bash
mysql -u root -p survival_war < sql.sql
# OR
mysql -u root -p survival_war < install.txt
```

#### 4. Run Migration

```bash
mysql -u root -p survival_war < migrations/001_modernization_schema.sql
```

#### 5. Configure Environment

```bash
cp .env.example .env
nano .env
```

```env
DB_HOST=localhost
DB_NAME=survival_war
DB_USER=gameuser
DB_PASS=your_password

SITE_URL=http://localhost/survival-war
ADMIN_EMAIL=admin@example.com

MAIL_FROM=noreply@example.com
```

#### 6. Set Permissions

```bash
chmod 755 logs/
chmod 644 logs/.htaccess
chmod 600 .env
```

#### 7. Configure Cron Job

```bash
crontab -e
```

Add:
```cron
0 * * * * php /path/to/survival-war/cron/cronjob.php
```

#### 8. Create Admin Account

Visit: `http://localhost/survival-war/admin/register.php`

**⚠️ IMPORTANT:** Delete `admin/register.php` after creating admin!

#### 9. Test Installation

1. Visit main page: `http://localhost/survival-war/`
2. Register a test account
3. Check email for activation link
4. Login and verify dashboard loads
5. Test hunting, buying actions
6. Check `/logs/error.log` for any issues

---

## Adding New Features

### Example: Add a New Game Action

Let's add a "Training Arena" where players can train troops for gold.

#### 1. Create New File: `trainarmy.php`

```php
<?php
/**
 * Training Arena
 * Train troops to improve their effectiveness
 */

require_once 'includes/bootstrap.php';
include 'up_html.php';

// Check authentication
if (!isset($_SESSION['player'])) {
    header("Location: login.php");
    exit;
}

try {
    $player = $_SESSION['player'];

    // Get player stats
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $stmt->execute(['player' => $player]);
    $stats = $stmt->fetch();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $units_to_train = Validator::sanitizeInt($_POST['units'] ?? 0);
        $training_cost = 50; // Gold per unit
        $total_cost = $units_to_train * $training_cost;

        // Validation
        if ($units_to_train <= 0) {
            $error = "Please enter a valid number of units.";
        } elseif ($stats['numturns'] < 2) {
            $error = "Not enough turns! Need 2 turns.";
        } elseif ($stats['gold'] < $total_cost) {
            $error = "Not enough gold! Need " . $total_cost . " gold.";
        } elseif ($units_to_train > $stats['offarmy']) {
            $error = "You can't train more troops than you have!";
        } else {
            // Process training
            $stmt = $db->prepare("
                UPDATE km_users
                SET gold = gold - :cost,
                    science = science + :bonus,
                    numturns = numturns - 2
                WHERE playername = :player
            ");

            $science_gain = floor($units_to_train / 10); // 1 science per 10 units

            $stmt->execute([
                'cost' => $total_cost,
                'bonus' => $science_gain,
                'player' => $player
            ]);

            $success = "Trained {$units_to_train} troops! Gained {$science_gain} science points.";

            // Refresh stats
            $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
            $stmt->execute(['player' => $player]);
            $stats = $stmt->fetch();
        }
    }
} catch (PDOException $e) {
    error_log("Training error: " . $e->getMessage());
    $error = "An error occurred. Please try again.";
}
?>

<h2>Training Arena</h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo Validator::escapeHtml($error); ?></p>
<?php endif; ?>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo Validator::escapeHtml($success); ?></p>
<?php endif; ?>

<p>Train your troops to gain science points!</p>
<p><strong>Cost:</strong> 50 gold per unit trained</p>
<p><strong>Bonus:</strong> 1 science point per 10 units trained</p>
<p><strong>Turns:</strong> 2 turns required</p>

<form method="POST">
    <label>
        Units to Train:
        <input type="number" name="units" min="1" max="<?php echo $stats['offarmy']; ?>" required>
    </label>
    <p>Available: <?php echo Validator::escapeHtml($stats['offarmy']); ?> offensive troops</p>
    <p>Your Gold: <?php echo Validator::escapeHtml($stats['gold']); ?></p>
    <button type="submit" class="RedButton">Train Troops</button>
</form>

<?php include 'down_html.php'; ?>
```

#### 2. Add to Navigation (`up_html.php`)

```php
<li><a href='../trainarmy.php'>Training Arena</a></li>
```

#### 3. Test the Feature

1. Login to game
2. Navigate to Training Arena
3. Train some troops
4. Verify gold is deducted
5. Verify science increases
6. Verify turns decrease

### Example: Add a New Database Table

Let's add an achievement system.

#### 1. Create Migration: `migrations/002_achievements.sql`

```sql
-- Achievements Table
CREATE TABLE IF NOT EXISTS km_achievements (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    requirement_type VARCHAR(50) NOT NULL,  -- 'skill', 'honor', 'land', etc.
    requirement_value INT(11) NOT NULL,
    icon_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_requirement_type (requirement_type)
) ENGINE=MyISAM;

-- Player Achievements (many-to-many)
CREATE TABLE IF NOT EXISTS km_player_achievements (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    player_id INT(11) NOT NULL,
    achievement_id INT(11) NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_player_id (player_id),
    INDEX idx_achievement_id (achievement_id),
    UNIQUE KEY unique_player_achievement (player_id, achievement_id)
) ENGINE=MyISAM;

-- Sample Achievements
INSERT INTO km_achievements (name, description, requirement_type, requirement_value) VALUES
('First Blood', 'Win your first land attack', 'land_attack_wins', 1),
('Rookie Hunter', 'Hunt 10 animals', 'monsters_killed', 10),
('Landowner', 'Own 100 acres of land', 'land', 100),
('Honorable', 'Reach 10 honor', 'honor', 10),
('Master Hunter', 'Kill a Bear', 'kill_bear', 1);
```

#### 2. Run Migration

```bash
mysql -u root -p survival_war < migrations/002_achievements.sql
```

#### 3. Create Achievement Checker Function

Add to `includes/functions.php`:

```php
/**
 * Check and award achievements to a player
 *
 * @param PDO $db Database connection
 * @param int $playerId Player ID
 * @param string $type Achievement type to check
 * @param int $currentValue Current value to check against
 */
function checkAchievements($db, $playerId, $type, $currentValue) {
    try {
        // Get unearned achievements of this type
        $stmt = $db->prepare("
            SELECT a.* FROM km_achievements a
            LEFT JOIN km_player_achievements pa
                ON a.id = pa.achievement_id AND pa.player_id = :player_id
            WHERE a.requirement_type = :type
                AND a.requirement_value <= :value
                AND pa.id IS NULL
        ");

        $stmt->execute([
            'player_id' => $playerId,
            'type' => $type,
            'value' => $currentValue
        ]);

        $earned = [];
        while ($achievement = $stmt->fetch()) {
            // Award achievement
            $awardStmt = $db->prepare("
                INSERT INTO km_player_achievements (player_id, achievement_id)
                VALUES (:player_id, :achievement_id)
            ");

            $awardStmt->execute([
                'player_id' => $playerId,
                'achievement_id' => $achievement['id']
            ]);

            $earned[] = $achievement['name'];
        }

        return $earned;
    } catch (PDOException $e) {
        error_log("Achievement check error: " . $e->getMessage());
        return [];
    }
}
```

#### 4. Integrate into Game Actions

In `attack.php` after a win:

```php
// Check for achievements
$newAchievements = checkAchievements($db, $stats['id'], 'land_attack_wins', 1);
if (!empty($newAchievements)) {
    echo "<p style='color: gold;'>🏆 Achievement Unlocked: " .
         implode(', ', $newAchievements) . "</p>";
}
```

#### 5. Create Achievements Page: `achievements.php`

```php
<?php
require_once 'includes/bootstrap.php';
include 'up_html.php';

if (!isset($_SESSION['player'])) {
    header("Location: login.php");
    exit;
}

$stmt = $db->prepare("SELECT id FROM km_users WHERE playername = :player");
$stmt->execute(['player' => $_SESSION['player']]);
$player = $stmt->fetch();

// Get earned achievements
$stmt = $db->prepare("
    SELECT a.*, pa.earned_at
    FROM km_achievements a
    INNER JOIN km_player_achievements pa ON a.id = pa.achievement_id
    WHERE pa.player_id = :player_id
    ORDER BY pa.earned_at DESC
");
$stmt->execute(['player_id' => $player['id']]);
$earned = $stmt->fetchAll();

// Get available achievements
$stmt = $db->prepare("
    SELECT a.* FROM km_achievements a
    LEFT JOIN km_player_achievements pa
        ON a.id = pa.achievement_id AND pa.player_id = :player_id
    WHERE pa.id IS NULL
    ORDER BY a.requirement_value ASC
");
$stmt->execute(['player_id' => $player['id']]);
$available = $stmt->fetchAll();
?>

<h2>Achievements</h2>

<h3>Earned (<?php echo count($earned); ?>)</h3>
<?php foreach ($earned as $ach): ?>
    <div class="achievement earned">
        <strong><?php echo Validator::escapeHtml($ach['name']); ?></strong>
        <p><?php echo Validator::escapeHtml($ach['description']); ?></p>
        <small>Earned: <?php echo date('Y-m-d H:i', strtotime($ach['earned_at'])); ?></small>
    </div>
<?php endforeach; ?>

<h3>Available (<?php echo count($available); ?>)</h3>
<?php foreach ($available as $ach): ?>
    <div class="achievement locked">
        <strong><?php echo Validator::escapeHtml($ach['name']); ?></strong>
        <p><?php echo Validator::escapeHtml($ach['description']); ?></p>
        <small>Requirement: <?php echo $ach['requirement_value']; ?>
               <?php echo $ach['requirement_type']; ?></small>
    </div>
<?php endforeach; ?>

<?php include 'down_html.php'; ?>
```

---

## Code Patterns

### Standard Page Structure

```php
<?php
/**
 * Page Title
 * Description of what this page does
 */

// Bootstrap
require_once 'includes/bootstrap.php';

// Header
include 'up_html.php';

// Authentication check
if (!isset($_SESSION['player'])) {
    header("Location: login.php");
    exit;
}

// Main logic
try {
    $player = $_SESSION['player'];

    // Get player data
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
    $stmt->execute(['player' => $player]);
    $stats = $stmt->fetch();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize input
        $input = Validator::sanitizeString($_POST['field'] ?? '');

        // Validate
        if (empty($input)) {
            $error = "Field is required.";
        } else {
            // Process
            $stmt = $db->prepare("UPDATE km_users SET field = :value WHERE playername = :player");
            $stmt->execute(['value' => $input, 'player' => $player]);

            $success = "Action completed!";
        }
    }
} catch (PDOException $e) {
    error_log("Error in " . __FILE__ . ": " . $e->getMessage());
    $error = "An error occurred.";
}
?>

<!-- HTML Content -->
<h2>Page Title</h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo Validator::escapeHtml($error); ?></p>
<?php endif; ?>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo Validator::escapeHtml($success); ?></p>
<?php endif; ?>

<!-- Page content here -->

<?php include 'down_html.php'; ?>
```

### Database Query Patterns

**SELECT Single Row:**

```php
$stmt = $db->prepare("SELECT * FROM km_users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}
```

**SELECT Multiple Rows:**

```php
$stmt = $db->prepare("SELECT * FROM km_users WHERE land > :min ORDER BY land DESC LIMIT 10");
$stmt->execute(['min' => 100]);

while ($user = $stmt->fetch()) {
    echo $user['playername'] . ": " . $user['land'] . " acres<br>";
}
```

**INSERT:**

```php
$stmt = $db->prepare("
    INSERT INTO km_users (playername, password, email, gold, land)
    VALUES (:player, :pass, :email, :gold, :land)
");

$stmt->execute([
    'player' => $username,
    'pass' => $hashedPassword,
    'email' => $email,
    'gold' => 1000,
    'land' => 10
]);

$newUserId = $db->lastInsertId();
```

**UPDATE:**

```php
$stmt = $db->prepare("
    UPDATE km_users
    SET gold = gold - :cost,
        land = land + :acres,
        numturns = numturns - 1
    WHERE playername = :player
");

$stmt->execute([
    'cost' => $cost,
    'acres' => $acres,
    'player' => $player
]);

$rowsAffected = $stmt->rowCount();
```

**DELETE:**

```php
$stmt = $db->prepare("DELETE FROM km_battlerecords WHERE attid = :player_id");
$stmt->execute(['player_id' => $playerId]);
```

### Validation Patterns

**Sanitize Input:**

```php
// String (with max length)
$username = Validator::sanitizeString($_POST['username'] ?? '', 50);

// Integer
$amount = Validator::sanitizeInt($_POST['amount'] ?? 0);

// Email
$email = Validator::sanitizeEmail($_POST['email'] ?? '');

// Array
$data = Validator::sanitizeArray($_POST['data'] ?? []);
```

**Validate Data:**

```php
// Email
if (!Validator::validateEmail($email)) {
    $error = "Invalid email address.";
}

// Username
if (!Validator::validateUsername($username, 3, 32)) {
    $error = "Username must be 3-32 characters (alphanumeric, underscore, hyphen).";
}

// Password
if (!Validator::validatePassword($password, 8)) {
    $error = "Password must be at least 8 characters.";
}
```

**Escape Output:**

```php
// Always escape user-generated content
echo Validator::escapeHtml($user['playername']);
echo Validator::escapeHtml($forumPost['message']);

// In attributes too
<input type="text" value="<?php echo Validator::escapeHtml($user['email']); ?>">
```

### Combat Calculation Pattern

```php
/**
 * Calculate combat outcome
 *
 * @param array $attacker Attacker stats
 * @param array $defender Defender stats
 * @return array ['winner' => 'attacker'|'defender', 'attacker_losses' => int, 'defender_losses' => int]
 */
function calculateCombat($attacker, $defender) {
    // Base strength
    $att_strength = $attacker['offarmy'];
    $def_strength = $defender['dffarmy'];

    // Science bonus (percentage)
    $att_strength *= (1 + $attacker['science'] / 100);
    $def_strength *= (1 + $defender['science'] / 100);

    // Honor bonus (small)
    $att_strength *= (1 + $attacker['honor'] / 1000);
    $def_strength *= (1 + $defender['honor'] / 1000);

    // Random factor
    $att_power = rand(1, (int)$att_strength);
    $def_power = rand(1, (int)$def_strength);

    // Determine winner
    if ($att_power > $def_power) {
        $winner = 'attacker';
        $ratio = $def_power / $att_power;
        $att_losses = floor($attacker['offarmy'] * $ratio * 0.3);
        $def_losses = floor($defender['dffarmy'] * 0.5);
    } else {
        $winner = 'defender';
        $ratio = $att_power / $def_power;
        $att_losses = $attacker['offarmy']; // Total loss
        $def_losses = floor($defender['dffarmy'] * $ratio * 0.3);
    }

    return [
        'winner' => $winner,
        'attacker_losses' => $att_losses,
        'defender_losses' => $def_losses
    ];
}
```

---

## API Reference

### Database Class

```php
// Get instance
$dbInstance = Database::getInstance();

// Get PDO connection
$pdo = $dbInstance->getConnection();
// OR (in pages with bootstrap)
$db; // Global variable
```

### Security Class

```php
// Generate CSRF token
$token = Security::generateCSRFToken();

// Verify CSRF token
if (Security::verifyCSRFToken($_POST['csrf_token'])) {
    // Valid
}

// Get CSRF input field HTML
echo Security::getCSRFInput();
// Outputs: <input type="hidden" name="csrf_token" value="...">

// Set security headers (done automatically in bootstrap)
Security::setSecurityHeaders();
```

### Validator Class

```php
// Sanitize string
$clean = Validator::sanitizeString($input, $maxLength = 255);

// Sanitize integer
$int = Validator::sanitizeInt($input, $min = null, $max = null);

// Sanitize email
$email = Validator::sanitizeEmail($input);

// Validate email
$isValid = Validator::validateEmail($email);

// Validate username
$isValid = Validator::validateUsername($username, $minLength = 3, $maxLength = 32);

// Validate password
$isValid = Validator::validatePassword($password, $minLength = 8);

// Escape HTML (prevent XSS)
$safe = Validator::escapeHtml($input);

// Sanitize array
$cleanArray = Validator::sanitizeArray($array);
```

### Session Class

```php
// Start session (done automatically in bootstrap)
Session::start();

// Destroy session
Session::destroy();

// Check if user logged in
if (Session::isLoggedIn()) { }

// Check if admin logged in
if (Session::isAdminLoggedIn()) { }

// Get user ID
$userId = Session::getUserId();

// Get username
$username = Session::getUsername();

// Get admin name
$adminName = Session::getAdminName();

// Set user session (on login)
Session::setUser($userId, $username);

// Set admin session (on admin login)
Session::setAdmin($adminName);

// Clear user session (on logout)
Session::clearUser();

// Clear admin session (on admin logout)
Session::clearAdmin();
```

### ErrorHandler Class

```php
// Initialize (done automatically in bootstrap)
ErrorHandler::init();

// Errors are logged to: logs/error.log
// Exceptions are caught and logged automatically
```

### Configuration Constants

Available after including `bootstrap.php`:

```php
// Database
DB_HOST      // Database host
DB_NAME      // Database name
DB_USER      // Database username
DB_PASS      // Database password

// Site
SITE_URL     // Full site URL
SITE_NAME    // Site name
ADMIN_EMAIL  // Admin email

// Game
MAX_TURNS           // Maximum turns (100)
TURNS_PER_HOUR     // Turn regeneration rate (10)
ATTACK_COOLDOWN    // Cooldown in seconds (3600)
MAX_ARMY_PER_LAND  // Army capacity per acre (10)
ARMY_COST          // Cost per troop (75)

// Security
SESSION_LIFETIME    // Session timeout (7200)
PASSWORD_MIN_LENGTH // Minimum password length (8)

// Email
MAIL_FROM      // From email address
MAIL_FROM_NAME // From name

// Development
DEBUG_MODE     // Debug mode boolean
DISPLAY_ERRORS // Display errors boolean
LOG_ERRORS     // Log errors boolean
```

---

## Best Practices

### Security Checklist

- ✅ Always use prepared statements for database queries
- ✅ Always sanitize user input with `Validator` methods
- ✅ Always escape output with `Validator::escapeHtml()`
- ✅ Always check authentication before protected pages
- ✅ Always validate data before processing
- ✅ Always use try-catch for database operations
- ✅ Never trust user input
- ✅ Never display system errors to users
- ✅ Never commit .env file to git
- ✅ Never use MD5 for new passwords (use `password_hash()`)

### Performance Tips

- Use indices on frequently queried fields
- Limit SELECT queries (don't always use `SELECT *`)
- Use pagination for large result sets
- Cache frequently accessed data
- Optimize combat calculations

### Code Quality

- Document functions with PHPDoc comments
- Use meaningful variable names
- Keep functions focused (single responsibility)
- Follow existing code style
- Test thoroughly before deploying
- Log errors, don't show them to users

### Database Tips

- Always use transactions for related updates
- Use appropriate data types
- Add indices for WHERE/JOIN clauses
- Clean up old battle records periodically
- Backup database regularly

---

## Troubleshooting

### Common Issues

**"Could not connect to database"**
- Check .env file has correct credentials
- Verify MySQL is running
- Check database exists

**"Class 'Database' not found"**
- Ensure bootstrap.php is included
- Check includes/ directory exists
- Verify file paths are correct

**"Session already started"**
- Remove manual `session_start()` calls
- Bootstrap handles session automatically

**"Undefined variable: $db"**
- Ensure bootstrap.php is included before use
- Check for typos in variable name

**Turns not regenerating**
- Check cron job is running (`crontab -l`)
- Verify cron/cronjob.php path is correct
- Check logs/error.log for cron errors

**Passwords not working**
- Run database migration (password field must be VARCHAR(255))
- Check error logs for authentication issues
- Verify password_hash is available (PHP 5.5+)

### Debug Mode

Enable in `.env`:

```env
DEBUG_MODE=true
```

This will:
- Display errors on screen
- Show detailed error messages
- Log all database queries

**⚠️ Never enable in production!**

---

## Contributing

### Adding Features

1. Create feature branch
2. Follow existing code patterns
3. Test thoroughly
4. Document in code comments
5. Update this guide if needed
6. Submit pull request

### Reporting Bugs

Include:
- What you were doing
- What happened
- What you expected
- Error logs (logs/error.log)
- Browser and PHP version

---

## Resources

### Documentation Files

- `MODERNIZATION_PLAN.md` - Modernization strategy
- `PHASE1_ANALYSIS.md` - Detailed code analysis
- `PHASE2_COMPLETE.md` - Modernization completion report
- `migrations/` - Database schema changes

### External Resources

- [PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [PHP 8.5 Migration Guide](https://www.php.net/manual/en/migration85.php)
- [MySQL 8.0 Reference](https://dev.mysql.com/doc/refman/8.0/en/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/) - Security best practices

---

## License

See project LICENSE file.

---

## Support

For issues, questions, or contributions:
- Check error logs: `logs/error.log`
- Review documentation in `/docs`
- Check database structure in `sql.sql`
- Contact: rutger.demaeyer@gmail.com

---

**Happy coding! 🎮**
