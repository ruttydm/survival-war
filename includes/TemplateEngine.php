<?php

use Latte\Engine;

/**
 * Template Engine wrapper for Latte
 * Provides a simple interface for rendering templates with caching
 */
class TemplateEngine
{
    private static ?TemplateEngine $instance = null;
    private Engine $latte;
    private string $templateDir;
    private string $tempDir;

    private function __construct()
    {
        $this->latte = new Engine();

        // Set up directories
        $this->templateDir = dirname(__DIR__) . '/templates';
        $this->tempDir = dirname(__DIR__) . '/temp/cache';

        // Create temp directory if it doesn't exist
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        // Configure Latte
        $this->latte->setTempDirectory($this->tempDir);
        $this->latte->setAutoRefresh(DEBUG_MODE ?? true);

        // Add custom filters
        $this->addCustomFilters();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): TemplateEngine
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render a template with data
     *
     * @param string $template Template name (relative to templates directory)
     * @param array $params Data to pass to template
     * @return string Rendered HTML
     */
    public function render(string $template, array $params = []): string
    {
        $templatePath = $this->templateDir . '/' . $template;

        if (!file_exists($templatePath)) {
            throw new RuntimeException("Template not found: {$template}");
        }

        // Add global parameters
        $params = array_merge($this->getGlobalParams(), $params);

        return $this->latte->renderToString($templatePath, $params);
    }

    /**
     * Render a template and output directly
     *
     * @param string $template Template name (relative to templates directory)
     * @param array $params Data to pass to template
     */
    public function display(string $template, array $params = []): void
    {
        echo $this->render($template, $params);
    }

    /**
     * Get global parameters available to all templates
     */
    private function getGlobalParams(): array
    {
        $db = Database::getInstance();
        $player = $_SESSION['player'] ?? null;
        $userstats = null;

        if ($player) {
            try {
                $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
                $stmt->execute(['player' => $player]);
                $userstats = $stmt->fetch();
            } catch (PDOException $e) {
                error_log("Error fetching user stats: " . $e->getMessage());
            }
        }

        return [
            'player' => $player,
            'userstats' => $userstats,
            'isLoggedIn' => isset($_SESSION['player']),
            'isAdmin' => isset($_SESSION['adminname']),
            'siteUrl' => SITE_URL ?? '',
            'currentUrl' => $_SERVER['REQUEST_URI'] ?? '',
        ];
    }

    /**
     * Add custom Latte filters
     */
    private function addCustomFilters(): void
    {
        // Add number formatting filter
        $this->latte->addFilter('number', function ($number) {
            return number_format($number, 0, ',', '.');
        });

        // Add escape filter (already available as |escapeHtml but we can alias it)
        $this->latte->addFilter('escape', function ($text) {
            return Validator::escapeHtml($text);
        });

        // Add date formatting filter
        $this->latte->addFilter('date', function ($timestamp, $format = 'Y-m-d H:i:s') {
            if ($timestamp instanceof DateTime) {
                return $timestamp->format($format);
            }
            return date($format, is_numeric($timestamp) ? $timestamp : strtotime($timestamp));
        });

        // Add pluralization filter
        $this->latte->addFilter('plural', function ($count, $singular, $plural) {
            return $count == 1 ? $singular : $plural;
        });
    }

    /**
     * Add a custom filter
     */
    public function addFilter(string $name, callable $callback): void
    {
        $this->latte->addFilter($name, $callback);
    }

    /**
     * Get the Latte engine instance for advanced usage
     */
    public function getEngine(): Engine
    {
        return $this->latte;
    }
}
