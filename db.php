<?php
// db.php — secure, reusable PDO singleton with dotenv + helpers
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

class DB {
    private static ?PDO $pdo = null;
    private static bool $inited = false;

    // Logs directory OUTSIDE webroot
    private const LOG_DIR  = __DIR__ . '/../env_logs';
    private const LOG_FILE = __DIR__ . '/../env_logs/error.log';
    private const MAX_LOG_SIZE = 5_000_000; // 5 MB

    // init dotenv + logging + error policy
    public static function init(): void {
        if (self::$inited) return;

        // Load .env
        if (!file_exists(__DIR__ . '/.env')) {
            throw new RuntimeException('.env file not found in project root.');
        }
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // Ensure logs dir exists
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0775, true);
        }

        // Log rotation (prevent huge files)
        if (file_exists(self::LOG_FILE) && filesize(self::LOG_FILE) > self::MAX_LOG_SIZE) {
            rename(self::LOG_FILE, self::LOG_FILE . '.' . date('Ymd_His') . '.bak');
        }

        // Error policy
        $env = $_ENV['APP_ENV'] ?? 'production';
        error_reporting(E_ALL);
        ini_set('display_errors', $env === 'development' ? '1' : '0');

        // Catch all uncaught exceptions
        set_exception_handler(function ($e) use ($env) {
            $msg = $e instanceof Throwable ? $e->getMessage() : (string)$e;
            self::log("Unhandled Exception: " . $msg);

            if ($env === 'development') {
                echo "<h2 style='color:red;'>⚠️ Unhandled Exception</h2>";
                echo "<pre>" . htmlspecialchars((string)$e) . "</pre>";
            } else {
                echo "<h2 style='color:red;'>⚠️ Something went wrong</h2>";
                echo "<p>Please check server error logs.</p>";
            }
        });

        // Catch PHP warnings/notices
        set_error_handler(function ($severity, $message, $file, $line) use ($env) {
            $logMessage = "PHP Error [$severity] $message in $file on line $line";
            self::log($logMessage);

            if ($env === 'development') {
                echo "<pre style='color:red;'>$logMessage</pre>";
            }
            return true; // prevent default PHP error output
        });

        self::$inited = true;
    }

    // Get singleton PDO
    public static function conn(): PDO {
        if (self::$pdo !== null) return self::$pdo;

        self::init();

        // Required ENV vars
        $required = ['DB_HOST','DB_PORT','DB_NAME','DB_USER','DB_PASS','DB_CHARSET'];
        foreach ($required as $k) {
            if (empty($_ENV[$k])) {
                self::log("Missing env var: $k");
                throw new RuntimeException("Database configuration missing: $k");
            }
        }

        $host    = $_ENV['DB_HOST'];
        $port    = (int) ($_ENV['DB_PORT'] ?? 3306);
        $dbname  = $_ENV['DB_NAME'];
        $user    = $_ENV['DB_USER'];
        $pass    = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            self::log('DB Connection failed: ' . $e->getMessage());

            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                throw new RuntimeException('DB Connection failed: ' . $e->getMessage());
            }
            throw new RuntimeException('Database connection failed. Please check logs.');
        }

        return self::$pdo;
    }

    // Safe Logger
    public static function log(string $message): void {
        $ts = date('Y-m-d H:i:s');
        $line = "[$ts] $message" . PHP_EOL;

        if (!is_dir(self::LOG_DIR)) {
            @mkdir(self::LOG_DIR, 0775, true);
        }
        error_log($line, 3, self::LOG_FILE);
    }

    // Run prepared query
    public static function query(string $sql, array $params = []): PDOStatement {
        $pdo = self::conn();
        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $param = is_string($key) && strpos($key, ':') !== 0 ? ':' . $key : $key;

            $type = PDO::PARAM_STR;
            if (is_int($value))   $type = PDO::PARAM_INT;
            if (is_bool($value))  $type = PDO::PARAM_BOOL;
            if (is_null($value))  $type = PDO::PARAM_NULL;

            if (is_int($key)) {
                $stmt->bindValue($key + 1, $value, $type);
            } else {
                $stmt->bindValue($param, $value, $type);
            }
        }

        $stmt->execute();
        return $stmt;
    }

    // Helpers
    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetch(string $sql, array $params = []): ?array {
        $row = self::query($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function fetchColumn(string $sql, array $params = []): mixed {
        return self::query($sql, $params)->fetchColumn();
    }

    public static function execute(string $sql, array $params = []): int {
        return self::query($sql, $params)->rowCount();
    }

    // For IN() clauses
    public static function inPlaceholders(array $values): array {
        $placeholders = [];
        $bindings = [];
        foreach (array_values($values) as $i => $v) {
            $ph = ':in_' . $i;
            $placeholders[] = $ph;
            $bindings[$ph] = $v;
        }
        return [implode(',', $placeholders), $bindings];
    }

    private function __construct() {}
    private function __clone() {}
}
