# Survival War Modernization Plan

## Overview
This plan outlines the modernization of Survival War to PHP 8.5 and current MySQL standards while preserving the original game's vibe, layout, and functionality.

**Goals:**
- ✅ PHP 8.5 compatibility
- ✅ Modern PDO database layer
- ✅ Enhanced security (password_hash, CSRF, prepared statements)
- ✅ Maintain original look, feel, and gameplay
- ✅ Zero breaking changes to user experience

---

## Phase 1: Setup & Analysis

### 1.1 Development Environment
- [ ] Set up PHP 8.5 development environment
- [ ] Install MySQL 8.0+
- [ ] Configure Apache/Nginx with proper error reporting
- [ ] Set up version control branching strategy
- [ ] Create `composer.json` for dependency management

### 1.2 Code Analysis
- [ ] Document all database queries (50+ PHP files)
- [ ] List all mysql_* function calls to replace
- [ ] Map session variable usage across files
- [ ] Identify security vulnerabilities
- [ ] Create backup of original codebase

### 1.3 Testing Baseline
- [ ] Set up test database with sample data
- [ ] Document current functionality (screenshots/videos)
- [ ] Create test cases for each game feature
- [ ] List all user flows to validate

**Deliverables:** Development environment, baseline documentation, test plan

---

## Phase 2: Database Layer Modernization

### 2.1 Create PDO Database Class
**File:** `includes/Database.php` (new)

```php
<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
```

### 2.2 Replace mysql_* Functions
**Pattern to follow for each file:**

**BEFORE (connect.php):**
```php
$link = mysql_connect($hostname, $username, $password);
mysql_select_db($database);
```

**AFTER (includes/config.php + usage):**
```php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'survival_war');
define('DB_USER', 'username');
define('DB_PASS', 'password');

// Usage in files
require_once 'includes/Database.php';
$db = Database::getInstance()->getConnection();
```

### 2.3 Convert Queries to Prepared Statements

**Files to update (priority order):**
1. `authenticate.php` - Login queries
2. `reguser.php` - User registration
3. `index.php` - Dashboard data
4. `attack.php` - Combat mechanics
5. `challengeplayer.php` - Death matches
6. `slaymonster.php` - Monster hunting
7. `buyarmy.php`, `buyland.php`, `buyscience.php` - Purchases
8. `logs.php` - Battle records
9. `top.php`, `tophonor.php`, `topland.php` - Leaderboards
10. `playerlist.php`, `playerclose.php` - Player listings
11. All forum files (forums/*.php)
12. All admin files (admin/*.php)
13. `cron/cronjob.php` - Turn regeneration

**Conversion pattern:**

**BEFORE:**
```php
$username = mysql_real_escape_string($username);
$query = "SELECT * FROM km_users WHERE playername='$username'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
```

**AFTER:**
```php
$stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :username");
$stmt->execute(['username' => $username]);
$row = $stmt->fetch();
```

### 2.4 Remove Magic Quotes Handling
- [ ] Remove all `get_magic_quotes_gpc()` checks
- [ ] Remove stripslashes() calls related to magic quotes
- [ ] Update `connect.php` to remove legacy code

**Deliverables:** PDO database class, all queries converted, tested connections

---

## Phase 3: Security Enhancements

### 3.1 Password Security

**Update Registration (reguser.php, admin/reguser.php):**
```php
// BEFORE
$password = md5($password);

// AFTER
$password = password_hash($password, PASSWORD_ARGON2ID);
```

**Update Authentication (authenticate.php, admin/authenticate.php):**
```php
// BEFORE
$password = md5($password);
// Query: WHERE password='$password'

// AFTER
// Query: Get user by username only
$stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Check if rehash needed
    if (password_needs_rehash($user['password'], PASSWORD_ARGON2ID)) {
        $newHash = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $db->prepare("UPDATE km_users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $newHash, 'id' => $user['id']]);
    }
    // Login success
}
```

**Create Password Migration Script:**
- [ ] Create `scripts/migrate_passwords.php`
- [ ] Force password reset on first login OR
- [ ] Add password rehashing on successful login

### 3.2 CSRF Protection

**Create CSRF Token Handler (includes/Security.php):**
```php
<?php
class Security {
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function getCSRFInput() {
        return '<input type="hidden" name="csrf_token" value="' .
               htmlspecialchars(self::generateCSRFToken()) . '">';
    }
}
```

**Update Forms:**
- [ ] Add CSRF token to all POST forms
- [ ] Update up_html.php to initialize Security class
- [ ] Validate tokens in all form handlers

**Files requiring CSRF protection:**
- All forms in attack.php, challengeplayer.php, slaymonster.php
- Buy actions: buyarmy.php, buyland.php, buyscience.php
- Forum posts: forums/post.php, forums/reply.php, forums/edit.php
- Login/register: login.php, register.php
- Admin actions: admin/*.php

### 3.3 Input Validation & Sanitization

**Create Validation Class (includes/Validator.php):**
```php
<?php
class Validator {
    public static function sanitizeString($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeInt($input) {
        return filter_var($input, FILTER_VALIDATE_INT);
    }

    public static function sanitizeEmail($input) {
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
```

**Apply to all user inputs:**
- [ ] Replace strip_tags() with comprehensive sanitization
- [ ] Validate numeric inputs (player IDs, amounts)
- [ ] Validate email addresses properly
- [ ] Add max length checks

### 3.4 XSS Protection
- [ ] Update all echo/print statements to use htmlspecialchars()
- [ ] Review forum message display (forums/forum.php)
- [ ] Add Content-Security-Policy headers

### 3.5 SQL Injection Prevention
- [ ] Verify all queries use prepared statements
- [ ] Remove any string concatenation in queries
- [ ] Test with SQL injection payloads

**Deliverables:** Password hashing, CSRF protection, input validation, security testing

---

## Phase 4: PHP 8.5 Compatibility

### 4.1 Session Handling Updates
**Update all session_start() calls:**
```php
// BEFORE
session_start();

// AFTER
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true
    ]);
}
```

### 4.2 Error Handling
**Replace deprecated error handling:**
- [ ] Remove @ error suppression operators
- [ ] Use try-catch blocks for database operations
- [ ] Implement proper error logging

### 4.3 Type Safety (Optional but recommended)
- [ ] Add declare(strict_types=1) to new files
- [ ] Add type hints to new functions/methods
- [ ] Add return type declarations

### 4.4 Deprecated Function Replacements
- [ ] Replace each() with foreach
- [ ] Replace split() with explode() or preg_split()
- [ ] Check for any other deprecated functions

### 4.5 Null Safety
- [ ] Add null checks for array access
- [ ] Use null coalescing operator (??) where appropriate
- [ ] Handle undefined array keys

**Example updates:**
```php
// BEFORE
$username = $_POST['username'];

// AFTER
$username = $_POST['username'] ?? '';
```

**Deliverables:** PHP 8.5 compatible codebase, no deprecation warnings

---

## Phase 5: Configuration Management

### 5.1 Centralized Configuration

**Create config structure:**
```
includes/
├── config.php          (main config, loads .env)
├── Database.php        (PDO singleton)
├── Security.php        (CSRF, session)
├── Validator.php       (input validation)
└── functions.php       (move from root)
```

**Create .env file:**
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=survival_war
DB_USER=your_username
DB_PASS=your_password

# Site Configuration
SITE_URL=http://localhost/survival-war
SITE_NAME="Survival War"
ADMIN_EMAIL=admin@example.com

# Game Configuration
MAX_TURNS=100
TURNS_PER_HOUR=10
ATTACK_COOLDOWN=3600

# Security
SESSION_LIFETIME=7200
```

**Create config.php:**
```php
<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        define(trim($name), trim($value));
    }
}

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'survival_war');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Site Configuration
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost');
define('SITE_NAME', getenv('SITE_NAME') ?: 'Survival War');

// Game Constants
define('MAX_TURNS', 100);
define('TURNS_PER_HOUR', 10);
define('ATTACK_COOLDOWN', 3600);
```

### 5.2 Update All Files
- [ ] Replace connect.php includes with config.php
- [ ] Remove duplicate connection code
- [ ] Update hardcoded paths (reguser.php line 5)
- [ ] Centralize email configuration

### 5.3 Git Security
- [ ] Add .env to .gitignore
- [ ] Create .env.example with dummy values
- [ ] Remove sql.sql with real credentials from repo

**Deliverables:** Centralized config, environment variables, secure credential management

---

## Phase 6: Error Handling & Logging

### 6.1 Error Handler Class

**Create includes/ErrorHandler.php:**
```php
<?php
class ErrorHandler {
    private static $logFile = __DIR__ . '/../logs/error.log';

    public static function init() {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatal']);
    }

    public static function handleError($errno, $errstr, $errfile, $errline) {
        $message = date('[Y-m-d H:i:s] ') . "Error [$errno]: $errstr in $errfile:$errline\n";
        error_log($message, 3, self::$logFile);

        if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            self::displayErrorPage();
        }
    }

    public static function handleException($exception) {
        $message = date('[Y-m-d H:i:s] ') . "Exception: " . $exception->getMessage() .
                   " in " . $exception->getFile() . ":" . $exception->getLine() . "\n";
        error_log($message, 3, self::$logFile);
        self::displayErrorPage();
    }

    public static function handleFatal() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private static function displayErrorPage() {
        echo "<h1>Game Error</h1><p>An error occurred. Please try again later.</p>";
        exit;
    }
}
```

### 6.2 Wrap Database Operations
**Pattern for all database queries:**
```php
try {
    $stmt = $db->prepare("SELECT * FROM km_users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Database error in " . __FILE__ . ": " . $e->getMessage());
    die("An error occurred. Please try again.");
}
```

### 6.3 Create Logs Directory
- [ ] Create logs/ directory
- [ ] Add .htaccess to block web access
- [ ] Add logs/*.log to .gitignore

### 6.4 Initialize in Bootstrap
**Update up_html.php:**
```php
require_once 'includes/config.php';
require_once 'includes/ErrorHandler.php';
ErrorHandler::init();
```

**Deliverables:** Error handling system, logging infrastructure, graceful error display

---

## Phase 7: Session Security Hardening

### 7.1 Session Configuration

**Update includes/config.php:**
```php
// Session Security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // If using HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 7200);
```

### 7.2 Session Management Class

**Create includes/Session.php:**
```php
<?php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 0,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
                'use_strict_mode' => true
            ]);

            // Regenerate session ID periodically
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }

    public static function isLoggedIn() {
        return isset($_SESSION['myusername']) && isset($_SESSION['userid']);
    }

    public static function getUserId() {
        return $_SESSION['userid'] ?? null;
    }
}
```

### 7.3 Add Security Headers

**Update up_html.php:**
```php
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
// Content-Security-Policy if needed
```

### 7.4 Update Login/Logout
- [ ] Use Session class in authenticate.php
- [ ] Use Session class in logout.php
- [ ] Add session regeneration after login
- [ ] Clear all session data on logout

**Deliverables:** Hardened session management, security headers, session hijacking prevention

---

## Phase 8: Testing & Validation

### 8.1 Functional Testing

**Test all game features:**
- [ ] User registration with email validation
- [ ] User login/logout
- [ ] Password recovery
- [ ] Dashboard displays correct stats
- [ ] Land attacks work correctly
- [ ] Player challenges work correctly
- [ ] Monster hunting works correctly
- [ ] Buying army units
- [ ] Buying land
- [ ] Buying science points
- [ ] Battle logs display
- [ ] Player rankings (skill, honor, land)
- [ ] Forum posting
- [ ] Forum replies
- [ ] Forum editing
- [ ] Admin login
- [ ] Admin user management
- [ ] Admin monster management
- [ ] Cron job turn regeneration

### 8.2 Security Testing

**Test for vulnerabilities:**
- [ ] SQL injection attempts (test all forms)
- [ ] XSS attempts (forum posts, usernames)
- [ ] CSRF attacks (replay requests)
- [ ] Session hijacking
- [ ] Brute force login attempts
- [ ] Direct file access (.htaccess working)
- [ ] Password strength

### 8.3 Performance Testing
- [ ] Load test with multiple concurrent users
- [ ] Database query optimization
- [ ] Memory usage monitoring
- [ ] Page load time benchmarks

### 8.4 Browser Compatibility
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile browsers

### 8.5 Error Scenarios
- [ ] Database connection failure
- [ ] Invalid user input
- [ ] Expired sessions
- [ ] Missing files
- [ ] Corrupted data

**Deliverables:** Test results, bug list, performance metrics

---

## Phase 9: Documentation & Deployment

### 9.1 Update Documentation

**Create/Update files:**
- [ ] README.md (modernized installation)
- [ ] INSTALL.md (step-by-step guide)
- [ ] SECURITY.md (security considerations)
- [ ] CHANGELOG.md (list of changes)
- [ ] MIGRATION.md (upgrade guide from old version)

### 9.2 Installation Scripts

**Create install/setup.php:**
```php
<?php
// Interactive setup script
// - Test PHP version (8.5+)
// - Test database connection
// - Create tables from schema
// - Set up admin account
// - Generate secure .env file
// - Set proper file permissions
// - Test cron job access
```

### 9.3 Database Migration

**Create migrations/001_update_schema.sql:**
```sql
-- Update password column length for modern hashes
ALTER TABLE km_users MODIFY password VARCHAR(255);
ALTER TABLE km_admins MODIFY password VARCHAR(255);

-- Add indices for performance
CREATE INDEX idx_playername ON km_users(playername);
CREATE INDEX idx_lastaction ON km_users(lastaction);
CREATE INDEX idx_land ON km_users(land);
CREATE INDEX idx_honor ON km_users(honor);
CREATE INDEX idx_skillpts ON km_users(skillpts);

-- Add new security fields
ALTER TABLE km_users ADD COLUMN password_reset_token VARCHAR(64) NULL;
ALTER TABLE km_users ADD COLUMN password_reset_expires INT NULL;
ALTER TABLE km_users ADD COLUMN failed_login_attempts INT DEFAULT 0;
ALTER TABLE km_users ADD COLUMN locked_until INT NULL;
```

### 9.4 Deployment Checklist

**Pre-deployment:**
- [ ] Backup current database
- [ ] Backup current files
- [ ] Test on staging environment
- [ ] Review all security settings
- [ ] Check error logging works

**Deployment:**
- [ ] Upload new files
- [ ] Update .env configuration
- [ ] Run database migrations
- [ ] Set file permissions (logs/, includes/)
- [ ] Test cron job
- [ ] Verify .htaccess rules

**Post-deployment:**
- [ ] Monitor error logs
- [ ] Test all critical paths
- [ ] Check performance
- [ ] Verify email sending
- [ ] Force user password resets (if needed)

### 9.5 Developer Documentation

**Create DEVELOPMENT.md:**
- File structure explanation
- How to add new features
- Database access patterns
- Security best practices
- Testing procedures

**Deliverables:** Complete documentation, deployment scripts, migration tools

---

## Implementation Timeline Estimate

**Phase 1:** 1-2 days
**Phase 2:** 3-5 days (50+ files to update)
**Phase 3:** 3-4 days
**Phase 4:** 2-3 days
**Phase 5:** 1-2 days
**Phase 6:** 1-2 days
**Phase 7:** 1-2 days
**Phase 8:** 2-3 days
**Phase 9:** 1-2 days

**Total:** 15-25 days of focused development

---

## File Change Summary

### New Files to Create
- includes/config.php
- includes/Database.php
- includes/Security.php
- includes/Validator.php
- includes/Session.php
- includes/ErrorHandler.php
- .env (not in repo)
- .env.example
- composer.json
- logs/.htaccess
- migrations/001_update_schema.sql
- scripts/migrate_passwords.php
- install/setup.php
- MODERNIZATION_PLAN.md (this file)
- INSTALL.md
- SECURITY.md
- CHANGELOG.md
- MIGRATION.md
- DEVELOPMENT.md

### Files to Modify (50+)
- All root PHP files (20+)
- All forum files (7)
- All admin files (15+)
- cron/cronjob.php
- style.css (minor, if needed)
- .gitignore

### Files to Remove/Archive
- connect.php (replaced by includes/config.php)
- admin/connect.php (use same config)
- sql.sql (archive, remove credentials)
- install.txt (replaced by INSTALL.md)

---

## Maintenance Considerations

### Ongoing Security
- Regular security audits
- Dependency updates
- Log monitoring
- Backup procedures
- Rate limiting for brute force
- CAPTCHA for registration (optional)

### Future Enhancements (Post-Modernization)
- Responsive CSS for mobile
- API endpoints for potential mobile app
- WebSocket for real-time updates
- Enhanced admin dashboard
- Player statistics/graphs
- In-game messaging system
- Alliance/guild system

---

## Risk Mitigation

### High Risk Areas
1. **Password migration** - Users must reset if not handled carefully
2. **Session handling** - Could log out all users
3. **Database queries** - Syntax errors could break features
4. **Cron job** - Turn regeneration is critical

### Mitigation Strategies
- Comprehensive testing at each phase
- Staging environment matches production
- Rollback plan (database dumps, file backups)
- Gradual rollout (test with small user group)
- Password rehashing on login (transparent to users)

---

## Success Criteria

✅ All game features work identically to original
✅ Runs on PHP 8.5 without errors/warnings
✅ All queries use PDO prepared statements
✅ Passwords use password_hash/password_verify
✅ CSRF protection on all forms
✅ No SQL injection vulnerabilities
✅ No XSS vulnerabilities
✅ Sessions are secure and hardened
✅ Error handling doesn't expose sensitive info
✅ Original visual design preserved
✅ All tests pass
✅ Documentation is complete and accurate

---

## Next Steps

1. **Review this plan** with stakeholders
2. **Set up development environment**
3. **Begin Phase 1** - Setup & Analysis
4. **Implement phases sequentially** with testing between each
5. **Deploy to staging** after Phase 8
6. **Production deployment** after final validation

---

*This plan is comprehensive and can be adjusted based on priorities, resources, and timeline constraints. Each phase builds on the previous one, ensuring a stable, secure, and modern codebase while respecting the original game's character.*
