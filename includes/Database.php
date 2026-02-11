<?php
/**
 * Database Class - PDO Singleton
 *
 * Provides a single database connection instance throughout the application.
 * Uses PDO with prepared statements for security.
 */
class Database {
    private static $instance = null;
    private $pdo;

    /**
     * Private constructor to prevent direct instantiation
     */
    private $explorer;
    private $netteConnection;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            // Legacy PDO Connection
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
            ];

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Nette Database Initialization
            $this->initNetteDatabase($dsn);

        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Could not connect to database. Please try again later.");
        }
    }

    /**
     * Initialize Nette Database
     */
    private function initNetteDatabase($dsn) {
        // Create cache directory if it doesn't exist
        $cacheDir = __DIR__ . '/../temp/cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $storage = new Nette\Caching\Storages\FileStorage($cacheDir);
        $this->netteConnection = new Nette\Database\Connection($dsn, DB_USER, DB_PASS);
        
        // Setup Structure and Conventions
        $structure = new Nette\Database\Structure($this->netteConnection, $storage);
        $conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
        
        $this->explorer = new Nette\Database\Explorer($this->netteConnection, $structure, $conventions, $storage);
    }

    /**
     * Get singleton instance
     *
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     *
     * @return PDO
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Get Nette Explorer
     * 
     * @return Nette\Database\Explorer
     */
    public function getExplorer() {
        return $this->explorer;
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
