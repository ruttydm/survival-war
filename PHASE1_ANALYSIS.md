# Phase 1: Setup & Analysis Report

**Date:** 2025-11-24
**Status:** Complete

---

## 1. Development Environment Setup

### ✅ Completed Tasks
- [x] Created `composer.json` for dependency management
- [x] Created directory structure: `includes/`, `logs/`, `migrations/`, `scripts/`, `install/`
- [x] Created `.env.example` template for configuration
- [x] Updated `.gitignore` for modern PHP development
- [x] Created `.htaccess` for logs directory protection

### Directory Structure Created
```
/home/user/survival-war/
├── includes/        # New: Core PHP classes (Database, Security, etc.)
├── logs/            # New: Error and application logs
│   └── .htaccess    # Prevents web access to logs
├── migrations/      # New: Database migration scripts
├── scripts/         # New: Utility scripts (password migration, etc.)
├── install/         # New: Installation scripts
├── composer.json    # Dependency management
└── .env.example     # Environment configuration template
```

---

## 2. MySQL Function Analysis

### Summary Statistics
- **Total PHP Files:** 50+
- **Files Using mysql_* Functions:** 43
- **Total mysql_* Function Calls:** 168+
- **Unique mysql_* Functions Used:** 7

### MySQL Functions by Type

#### Connection Functions
- `mysql_connect()` - 2 occurrences
  - `connect.php:10`
  - `admin/connect.php:10`

- `mysql_select_db()` - 2 occurrences
  - `connect.php:13`
  - `admin/connect.php:13`

#### Query Functions
- `mysql_query()` - 136 occurrences across 43 files
- `mysql_fetch_array()` - 54 occurrences
- `mysql_num_rows()` - 2 occurrences
  - `playerclose.php:17`
  - `playerclose.php:20`

- `mysql_result()` - 2 occurrences
  - `attack.php:35`
  - `forums/forum.php:69`

#### Security Functions
- `mysql_real_escape_string()` - 12 occurrences
  - `connect.php:17-19, 26-28` (array_map on $_GET, $_POST, $_COOKIE)
  - `admin/connect.php:17-19, 26-28` (same pattern)

#### Error Functions
- `mysql_error()` - 10 occurrences in die() statements

---

## 3. Detailed File-by-File mysql_* Usage

### Critical Core Files

#### connect.php (Root)
**Lines:** 10, 13, 17-19, 26-28
**Functions:**
- `mysql_connect()` - Database connection
- `mysql_select_db()` - Database selection
- `mysql_real_escape_string()` - Input sanitization via array_map

**Conversion Priority:** HIGHEST (affects all files)

#### admin/connect.php
**Lines:** 10, 13, 17-19, 26-28
**Functions:** Same as connect.php
**Conversion Priority:** HIGHEST (affects all admin files)

### Authentication Files

#### authenticate.php
**Lines:** 12, 13
- `mysql_query()` - Login query
- `mysql_fetch_array()` - Get user data
**Priority:** CRITICAL (security-sensitive)

#### admin/authenticate.php
**Lines:** 11, 13
- Same pattern as authenticate.php
**Priority:** CRITICAL (admin security)

#### reguser.php
**Lines:** 17, 18, 30, 31, 44
- Multiple queries for registration validation
- Player existence check
- Email duplicate check
**Priority:** HIGH (new user entry point)

#### admin/reguser.php
**Lines:** 14
- Admin registration
**Priority:** HIGH

### Game Mechanics Files

#### attack.php
**Lines:** 23, 24, 28, 29, 34, 35, 74, 126, 128, 130, 137, 139, 141
**mysql Functions:**
- 7 `mysql_query()` calls
- 2 `mysql_fetch_array()` calls
- 1 `mysql_result()` call
**Complexity:** HIGH (complex combat calculations)
**Priority:** HIGH

#### challengeplayer.php
**Lines:** 31, 32, 36, 37, 65, 67, 76, 78
**mysql Functions:**
- 6 `mysql_query()` calls
- 2 `mysql_fetch_array()` calls
**Complexity:** MEDIUM
**Priority:** HIGH

#### slaymonster.php
**Lines:** 16, 17, 21, 22, 56, 65, 81, 82
**mysql Functions:**
- 6 `mysql_query()` calls
- 3 `mysql_fetch_array()` calls (1 in while loop)
**Complexity:** MEDIUM
**Priority:** HIGH

### Purchase/Economy Files

#### buyarmy.php
**Lines:** 18, 19, 58
- Get user stats, update army
**Priority:** MEDIUM

#### buyland.php
**Lines:** 20, 21, 46
- Get user stats, update land
**Priority:** MEDIUM

#### buyscience.php
**Lines:** 30, 31, 57
- Get user stats, update science
**Priority:** MEDIUM

### Dashboard & Display Files

#### index.php (Main Dashboard)
**Lines:** 18, 19, 39
- Display user stats
- Update refresh time
**Priority:** HIGH (main entry point)

#### up_html.php (Page Header)
**Lines:** 13, 14
- Display navigation stats
**Priority:** CRITICAL (included on every page)

### Ranking/Leaderboard Files

#### top.php (Skill Rankings)
**Lines:** 12, 17, 30, 31
- 2 queries with while loops
**Priority:** MEDIUM

#### tophonor.php (Honor Rankings)
**Lines:** 9, 14, 27, 28
- 2 queries with while loops
**Priority:** MEDIUM

#### topland.php (Land Rankings)
**Lines:** 9, 14, 27, 28
- 2 queries with while loops
**Priority:** MEDIUM

#### playerclose.php (Nearby Players)
**Lines:** 13, 14, 16, 17, 19, 20, 33, 36
- Complex ranking logic with multiple queries
- Uses `mysql_num_rows()`
**Priority:** MEDIUM

### Battle & History Files

#### logs.php
**Lines:** 15, 16, 23, 29, 33
- Display battle history
- Reset functionality
**Priority:** MEDIUM

### Forum Files (7 files)

#### forums/index.php
**Lines:** 16, 17, 21, 25, 30, 31
**Priority:** MEDIUM

#### forums/forum.php
**Lines:** 16, 17, 21, 25, 42, 43, 65, 69
- Uses `mysql_result()`
**Priority:** MEDIUM

#### forums/messages.php
**Lines:** 13, 14, 19, 20, 22, 23, 51, 52
**Priority:** MEDIUM

#### forums/post.php
**Lines:** 13, 14, 42, 44
**Priority:** MEDIUM

#### forums/reply.php
**Lines:** 13, 14, 39, 41, 43
**Priority:** MEDIUM

#### forums/edit.php
**Lines:** 11, 12, 22, 32, 33
**Priority:** MEDIUM

#### forums/delete.php
**Lines:** 11, 12, 21, 22, 27, 29, 31, 36, 38, 40
**Priority:** MEDIUM

### Admin Panel Files (8 files)

#### admin/manageuser.php
**Lines:** 20, 45, 48, 58, 67
- User management
**Priority:** MEDIUM

#### admin/addforum.php
**Lines:** 29
**Priority:** LOW

#### admin/edit.php
**Lines:** 29, 38, 39
**Priority:** LOW

#### admin/listforums.php
**Lines:** 20, 21
**Priority:** LOW

#### admin/addmonster.php
**Lines:** 27, 28, 41
**Priority:** LOW

#### admin/deletemonster.php
**Lines:** 20, 35, 38
**Priority:** LOW

#### admin/delete.php
**Lines:** 21
**Priority:** LOW

#### admin/reset.php
**Lines:** 20
**Priority:** LOW

### Cron Job

#### cron/cronjob.php
**Lines:** 4, 5
- 2 `mysql_query()` UPDATE statements
- No error handling needed (protected by .htaccess)
**Priority:** HIGH (critical game mechanic)

### Other Files

#### activate.php
**Lines:** 8, 9, 24
- Email activation
**Priority:** HIGH

#### getpass.php
**Lines:** 8, 9, 22
- Password recovery
**Priority:** HIGH

#### setpass.php
**Lines:** 22
- Password change
**Priority:** HIGH

#### revive.php
**Lines:** 10
- Player revival
**Priority:** MEDIUM

---

## 4. Session Variable Analysis

### Files Using $_SESSION (31 total)

#### Session Variables Used

**Authentication Variables:**
- `$_SESSION['myusername']` - Logged in username
- `$_SESSION['userid']` - User ID
- `$_SESSION['adminname']` - Admin username

**Discovered in:**
- `authenticate.php` - Sets user session
- `admin/authenticate.php` - Sets admin session
- `up_html.php` - Checks session for navigation
- All protected pages - Verify login status

### Session Security Issues Identified

1. **No session regeneration** after login (session fixation vulnerability)
2. **No session timeout** enforcement
3. **No CSRF tokens** in forms
4. **Basic session_start()** without security options
5. **No HttpOnly or Secure flags** on session cookies
6. **No SameSite attribute** on cookies

---

## 5. Security Vulnerabilities Identified

### Critical Vulnerabilities

#### 1. Deprecated MySQL Extension
- **Severity:** CRITICAL
- **Impact:** Code will not run on PHP 7.0+
- **Files Affected:** 43 files
- **Solution:** Convert to PDO with prepared statements

#### 2. MD5 Password Hashing
- **Severity:** CRITICAL
- **Files:** `authenticate.php`, `admin/authenticate.php`, `reguser.php`
- **Issue:** MD5 is cryptographically broken
- **Solution:** Use `password_hash()` with Argon2id

#### 3. SQL Injection Risk
- **Severity:** HIGH
- **Issue:** While `mysql_real_escape_string()` is used, it's not foolproof
- **Files:** All files with mysql_query()
- **Solution:** PDO prepared statements eliminate this risk

#### 4. No CSRF Protection
- **Severity:** HIGH
- **Impact:** All POST forms vulnerable to CSRF attacks
- **Files Affected:** All forms (attack, buy, forum posts, etc.)
- **Solution:** Implement CSRF tokens

#### 5. Session Vulnerabilities
- **Severity:** HIGH
- **Issues:**
  - Session fixation possible (no regeneration)
  - No timeout enforcement
  - Insecure cookie settings
- **Solution:** Implement secure session handling

### Medium Vulnerabilities

#### 6. XSS Risks
- **Severity:** MEDIUM
- **Issue:** Forum posts may not be properly escaped
- **Files:** `forums/*.php`
- **Solution:** Use `htmlspecialchars()` consistently

#### 7. Error Information Disclosure
- **Severity:** MEDIUM
- **Issue:** `die()` statements with database errors
- **Example:** `or die(mysql_error())`
- **Solution:** Log errors, display generic messages

#### 8. No Rate Limiting
- **Severity:** MEDIUM
- **Impact:** Brute force attacks on login
- **Solution:** Implement login attempt tracking

#### 9. Hardcoded Credentials
- **Severity:** MEDIUM
- **File:** `sql.sql` - Contains sample admin passwords
- **Solution:** Remove from repository

### Low Vulnerabilities

#### 10. Magic Quotes Handling
- **Severity:** LOW
- **Issue:** Deprecated in PHP 5.4, removed in PHP 7.0
- **Files:** `connect.php`, `admin/connect.php`
- **Solution:** Remove magic quotes code

#### 11. No Input Length Validation
- **Severity:** LOW
- **Impact:** Potential buffer overflow or DOS
- **Solution:** Add max length checks

---

## 6. Query Patterns Identified

### Pattern 1: Simple Select with Fetch
```php
$query = "SELECT * FROM km_users WHERE id='$id'";
$result = mysql_query($query) or die("Error");
$row = mysql_fetch_array($result);
```
**Occurrences:** ~40
**PDO Replacement:**
```php
$stmt = $db->prepare("SELECT * FROM km_users WHERE id = :id");
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();
```

### Pattern 2: Simple Update
```php
$query = "UPDATE km_users SET gold='$gold' WHERE id='$id'";
mysql_query($query) or die("Error");
```
**Occurrences:** ~50
**PDO Replacement:**
```php
$stmt = $db->prepare("UPDATE km_users SET gold = :gold WHERE id = :id");
$stmt->execute(['gold' => $gold, 'id' => $id]);
```

### Pattern 3: While Loop Fetch
```php
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    // Process row
}
```
**Occurrences:** ~15
**PDO Replacement:**
```php
$stmt = $db->query($query);
while ($row = $stmt->fetch()) {
    // Process row
}
```

### Pattern 4: Count Rows
```php
$result = mysql_query($query);
$count = mysql_num_rows($result);
```
**Occurrences:** 2
**PDO Replacement:**
```php
$stmt = $db->query($query);
$count = $stmt->rowCount(); // or COUNT(*) in query
```

### Pattern 5: Get Single Value
```php
$result = mysql_query($query);
$value = mysql_result($result, 0);
```
**Occurrences:** 2
**PDO Replacement:**
```php
$stmt = $db->query($query);
$value = $stmt->fetchColumn();
```

---

## 7. Configuration Management

### Current Configuration Issues

1. **Hardcoded Database Credentials**
   - `connect.php` - lines 6-9
   - `admin/connect.php` - lines 6-9

2. **Hardcoded Path**
   - `reguser.php` - line 5: `$path` variable for email links

3. **Magic Numbers**
   - Turn regeneration: 10 turns per hour (hardcoded in cronjob.php)
   - Max turns: 100 (hardcoded in multiple files)
   - Army cost: 75 gold (hardcoded in buyarmy.php)
   - Attack cooldown: 3600 seconds (hardcoded in attack.php)

### Configuration to Centralize

```php
// Database
DB_HOST, DB_NAME, DB_USER, DB_PASS

// Site
SITE_URL, SITE_NAME, ADMIN_EMAIL

// Game Mechanics
MAX_TURNS = 100
TURNS_PER_HOUR = 10
ATTACK_COOLDOWN = 3600
MAX_ARMY_PER_LAND = 10
ARMY_COST = 75

// Security
SESSION_LIFETIME = 7200
PASSWORD_MIN_LENGTH = 8
```

---

## 8. Testing Baseline

### User Flows to Test

#### Authentication Flows
1. User registration → Email validation → Activation
2. User login → Dashboard display
3. User logout
4. Password recovery → Reset → Login
5. Admin registration (one-time)
6. Admin login → Admin panel

#### Game Mechanics Flows
1. View dashboard stats
2. Hunt monster → Gain gold/skill
3. Buy army units
4. Buy land
5. Buy science points
6. Attack player land → Win/Lose
7. Challenge player → Death match → Win/Lose
8. View battle logs
9. Die → Revive
10. View player rankings (skill, honor, land)

#### Forum Flows
1. View forum list
2. View forum topics
3. Create new topic
4. Reply to topic
5. Edit own post
6. Delete own post
7. View messages/replies

#### Admin Flows
1. Manage users (edit/delete)
2. Add forum category
3. Edit forum category
4. Delete forum category
5. Add monster/animal
6. Delete monster
7. Reset game

#### Cron Flow
1. Cron job runs hourly
2. All users gain +10 turns (max 100)

### Test Data Requirements

**Sample Users:**
- Regular user (active)
- Regular user (dead)
- Admin user

**Sample Game Data:**
- Multiple users with varying stats
- Battle records
- Forum posts and replies
- Monsters with different difficulties

### Success Criteria

✅ All user flows complete without errors
✅ Data integrity maintained
✅ Session handling works correctly
✅ Permissions enforced (user vs admin)
✅ Cron job executes properly
✅ No security vulnerabilities exploitable

---

## 9. Conversion Priority Matrix

### Phase 2 Conversion Order (Recommended)

**Priority 1 - Foundation (Must do first):**
1. Create `includes/Database.php` (PDO singleton)
2. Create `includes/config.php` (centralized config)
3. Convert `connect.php` and `admin/connect.php`

**Priority 2 - Authentication (Critical security):**
4. `authenticate.php`
5. `admin/authenticate.php`
6. `reguser.php`
7. `admin/reguser.php`

**Priority 3 - Core Functionality:**
8. `up_html.php` (affects all pages)
9. `index.php` (main dashboard)
10. `activate.php` (user activation)
11. `getpass.php`, `setpass.php` (password recovery)

**Priority 4 - Game Mechanics:**
12. `attack.php`
13. `challengeplayer.php`
14. `slaymonster.php`
15. `buyarmy.php`, `buyland.php`, `buyscience.php`
16. `revive.php`
17. `logs.php`

**Priority 5 - Rankings/Display:**
18. `top.php`, `tophonor.php`, `topland.php`
19. `playerclose.php`

**Priority 6 - Forums (7 files):**
20. `forums/index.php`
21. `forums/forum.php`
22. `forums/messages.php`
23. `forums/post.php`
24. `forums/reply.php`
25. `forums/edit.php`
26. `forums/delete.php`

**Priority 7 - Admin Panel (8 files):**
27. `admin/manageuser.php`
28. `admin/addforum.php`
29. `admin/edit.php`
30. `admin/listforums.php`
31. `admin/addmonster.php`
32. `admin/deletemonster.php`
33. `admin/delete.php`
34. `admin/reset.php`

**Priority 8 - Cron:**
35. `cron/cronjob.php`

---

## 10. Risk Assessment

### High Risk Items

1. **Password Migration**
   - Users currently have MD5 hashes
   - Cannot reverse to plaintext
   - **Options:**
     - Force password reset for all users
     - Transparent rehash on login (check MD5, rehash if match)
   - **Recommendation:** Transparent rehash

2. **Session Changes**
   - New session security may log out all users
   - **Mitigation:** Announce maintenance window

3. **Database Query Syntax**
   - PDO uses slightly different syntax
   - Risk of breaking functionality
   - **Mitigation:** Test each file thoroughly

### Medium Risk Items

1. **Cron Job Path**
   - .htaccess protects cron/cronjob.php
   - Must ensure still accessible by cron
   - **Mitigation:** Test cron execution

2. **File Includes**
   - Changing from `connect.php` to `includes/config.php`
   - Must update all 43 files
   - **Mitigation:** Global search/replace, test systematically

### Low Risk Items

1. **Forum Formatting**
   - XSS fixes may change display
   - **Mitigation:** Test forum posts with various content

2. **Error Messages**
   - Changing from `die()` to proper error handling
   - Users may see different messages
   - **Mitigation:** Ensure messages are clear

---

## 11. Recommendations for Phase 2

### Development Strategy

1. **Branch Strategy**
   - Work on feature branches
   - Test thoroughly before merging
   - Keep main/production branch stable

2. **Incremental Approach**
   - Convert files in priority order
   - Test after each file conversion
   - Don't batch too many files

3. **Rollback Plan**
   - Database backup before any changes
   - File backups at each phase
   - Document all changes

4. **Testing Requirements**
   - Unit tests for new classes
   - Integration tests for critical flows
   - Manual testing of all features

### Next Steps

1. ✅ Phase 1 Complete - Analysis done
2. 🔄 Begin Phase 2 - Create core classes
3. ⏳ Phase 3 - Convert authentication files
4. ⏳ Phase 4 - Convert game mechanics
5. ⏳ Phase 5+ - Complete remaining conversions

---

## Summary Statistics

- **Total Files Analyzed:** 50+
- **Files Requiring Conversion:** 43
- **mysql_* Function Calls:** 168+
- **Security Issues Found:** 11
- **Critical Vulnerabilities:** 5
- **Session-Protected Files:** 31
- **Estimated Conversion Time:** 3-5 days (Phase 2)

---

**Analysis Completed By:** Claude Code
**Date:** 2025-11-24
**Status:** ✅ Phase 1 Complete - Ready for Phase 2
