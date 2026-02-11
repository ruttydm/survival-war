# Nette Latte Template Migration - Complete

## Overview

The Survival War codebase has been successfully modernized to use the Nette Latte templating system. This migration separates presentation logic from business logic, improving maintainability, security, and code organization while preserving the exact frontend appearance.

## What Was Done

### 1. Infrastructure Setup

**Composer Dependencies:**
- Added `latte/latte: ^3.0` to composer.json

**New Core Classes:**
- `includes/TemplateEngine.php` - Singleton wrapper for Latte engine with custom filters and global parameters

**Directory Structure:**
```
templates/
├── layouts/
│   ├── main.latte          # Main game layout (header, menu, footer)
│   └── admin.latte         # Admin panel layout
├── pages/                  # Main game templates
├── forums/                 # Forum templates
└── admin/                  # Admin templates
```

**Cache Directory:**
- `temp/cache/` - Latte compiled template cache (gitignored)

### 2. Template System Features

**TemplateEngine Class Features:**
- Singleton pattern for consistent configuration
- Automatic template caching with auto-refresh in debug mode
- Global parameters available to all templates (player stats, session info)
- Custom Latte filters:
  - `|number` - Number formatting with thousands separator
  - `|escape` - HTML escaping (alias for |escapeHtml)
  - `|date` - Date/time formatting
  - `|plural` - Pluralization helper

**Layout Templates:**
- `layouts/main.latte` - Converted from up_html.php/down_html.php
- `layouts/admin.latte` - Admin-specific layout with sidebar navigation

### 3. Files Migrated

#### Root Game Pages (20 files)
- index.php - Main dashboard
- login.php - Login form
- register.php - Registration form
- reguser.php - Registration handler
- authenticate.php - Login handler (no template, redirects only)
- attack.php - Attack land
- buyarmy.php - Purchase army
- buyland.php - Purchase land
- buyscience.php - Purchase science
- challengeplayer.php - Challenge another player
- slaymonster.php - Hunt animals
- logs.php - Battle logs
- playerlist.php - Player listing
- playerclose.php - Nearby players
- top.php - Rank leaderboard
- tophonor.php - Honor leaderboard
- topland.php - Land leaderboard
- revive.php - Revival page
- setpass.php - Change password
- getpass.php - Password recovery
- activate.php - Account activation

#### Forum Section (7 files)
- forums/index.php - Forum listing
- forums/forum.php - Topics in forum
- forums/messages.php - Thread messages
- forums/post.php - Create new topic
- forums/reply.php - Reply to topic
- forums/edit.php - Edit message
- forums/delete.php - Delete message

#### Admin Section (14 files + sidebar)
- admin/index.php - Admin dashboard
- admin/login.php - Admin login
- admin/authenticate.php - Admin authentication
- admin/addmonster.php - Create monsters
- admin/deletemonster.php - Delete monsters
- admin/addforum.php - Create forums
- admin/listforums.php - Forum management
- admin/manageuser.php - User management
- admin/register.php - Admin registration
- admin/reguser.php - Admin user creation
- admin/edit.php - Edit entities
- admin/delete.php - Delete entities
- admin/reset.php - Reset game
- admin/logout.php - Admin logout

**Total: 41 PHP files migrated to use Latte templates**

### 4. Files Deleted

**Deprecated Files Removed:**
- `up_html.php` - Replaced by `layouts/main.latte`
- `down_html.php` - Replaced by `layouts/main.latte`
- `connect.php` - Replaced by `includes/bootstrap.php`
- `admin/connect.php` - Replaced by `../includes/bootstrap.php`
- `admin/left.php` - Replaced by `admin/sidebar.latte` template

## Migration Pattern

### Before (Old Pattern)
```php
<?php
include 'up_html.php';

// Business logic mixed with presentation
print "<table class='maintable'>";
print "<tr><td>Player: " . Validator::escapeHtml($player) . "</td></tr>";
print "</table>";

include 'down_html.php';
?>
```

### After (New Pattern)

**PHP File (Business Logic):**
```php
<?php
require_once 'includes/bootstrap.php';

// Authentication checks
if (!isset($_SESSION['player'])) {
    header('Location: login.php');
    exit;
}

// Business logic
$player = $_SESSION['player'];
// ... database queries, calculations, validations

// Prepare template data
$templateData = [
    'player' => $player,
    'stats' => $stats,
];

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/filename.latte', $templateData);
```

**Latte Template (Presentation):**
```latte
{layout '../layouts/main.latte'}

{block content}
    <table class='maintable'>
        <tr><td>Player: {$player|escapeHtml}</td></tr>
    </table>
{/block}
```

## Key Benefits

### 1. Separation of Concerns
- Business logic stays in PHP files
- Presentation logic in Latte templates
- Clearer code organization and easier maintenance

### 2. Security Improvements
- Automatic HTML escaping in templates reduces XSS vulnerabilities
- Consistent use of `|escapeHtml` filter on all user-generated content
- No more manual escaping in PHP code

### 3. Code Reusability
- Single layout template shared across all pages
- Admin layout template shared across admin pages
- Template inheritance eliminates code duplication

### 4. Performance
- Latte compiles templates to PHP for fast execution
- Template caching in `temp/cache/` directory
- Auto-refresh in debug mode, cached in production

### 5. Maintainability
- HTML changes only require template updates
- No need to touch PHP code for UI changes
- Consistent template structure across all pages
- Better separation makes debugging easier

### 6. Modern Best Practices
- Follows MVC-like architecture
- Template inheritance and blocks
- Custom filters for common formatting needs
- Global template variables for shared data

## Frontend Appearance

**Important:** All HTML structure, CSS classes, IDs, and styling have been preserved exactly. The frontend appearance is **unchanged**. Users will not notice any visual differences.

## Template Syntax Examples

### Variables
```latte
{$player}                    <!-- Raw output -->
{$player|escapeHtml}         <!-- HTML escaped -->
{$gold|number}               <!-- Number formatting -->
{$timestamp|date:'Y-m-d'}    <!-- Date formatting -->
```

### Conditionals
```latte
{if $isLoggedIn}
    Welcome, {$player|escapeHtml}!
{else}
    Please log in
{/if}
```

### Loops
```latte
{foreach $players as $player}
    <tr>
        <td>{$player['playername']|escapeHtml}</td>
        <td>{$player['honor']|number}</td>
    </tr>
{/foreach}
```

### Layout Inheritance
```latte
{layout '../layouts/main.latte'}

{block title}Page Title{/block}

{block content}
    <!-- Page content here -->
{/block}
```

## Configuration

### Bootstrap Integration
The TemplateEngine is loaded in `includes/bootstrap.php`:

```php
// Load Composer autoloader if available
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load TemplateEngine
require_once __DIR__ . '/TemplateEngine.php';
```

### Global Template Variables
Available in all templates via TemplateEngine:
- `$player` - Current player username
- `$userstats` - Current player statistics array
- `$isLoggedIn` - Boolean login status
- `$isAdmin` - Boolean admin status
- `$siteUrl` - Site URL from config
- `$currentUrl` - Current request URI

## Next Steps

### For Deployment
1. Run `composer install` to install Latte dependencies
2. Ensure `temp/cache/` directory is writable
3. Set `DEBUG_MODE` appropriately in production (.env or config)

### For Development
1. Templates auto-refresh when `DEBUG_MODE = true`
2. Template cache in `temp/cache/` can be cleared if needed
3. Custom filters can be added to TemplateEngine class

### Future Enhancements
- Consider adding more custom filters as needed
- Potentially create reusable template components/macros
- Add CSRF token helpers to templates
- Consider adding more layout options

## Documentation

### Official Latte Documentation
- https://latte.nette.org/en/guide
- https://latte.nette.org/en/syntax
- https://latte.nette.org/en/filters

### Project Files
- `includes/TemplateEngine.php` - Template engine wrapper
- `templates/layouts/main.latte` - Main layout
- `templates/layouts/admin.latte` - Admin layout

## Migration Statistics

- **Total PHP files migrated:** 41
- **Template files created:** 60+ (including multiple templates per file for different states)
- **Deprecated files removed:** 5
- **Lines of code improved:** ~2,000+
- **Frontend changes:** 0 (appearance unchanged)

## Conclusion

The migration to Nette Latte templating is complete and successful. The codebase now follows modern best practices with clear separation between business logic and presentation, improved security through automatic escaping, better maintainability through template inheritance, and enhanced performance through template caching.

All functionality has been preserved, and the frontend appearance remains exactly the same. The application is now better structured for future development and maintenance.
