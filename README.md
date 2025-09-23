# php-secure-db-helper  

A **secure and reusable database helper for PHP projects**.  
It uses **PDO**, **Dotenv**, and a custom **logging + error handling** system to make database connections safe, simple, and consistent.  

---

## ✨ Features  

- ✅ PDO Singleton connection (no repeated connections)  
- ✅ `.env` configuration support (no hardcoded credentials)  
- ✅ Error handling (different for development and production)  
- ✅ Error logging with rotation (`env_logs/error.log`)  
- ✅ Helper functions for queries:  
  - `DB::fetchAll()` → Get multiple rows  
  - `DB::fetch()` → Get one row  
  - `DB::fetchColumn()` → Get one column  
  - `DB::execute()` → Run insert/update/delete  
- ✅ Safe prepared statements with type binding  
- ✅ Support for dynamic `IN()` placeholders  

---

## 📋 Requirements  

- PHP **8.0+**  
- MySQL database  
- Composer (for dependencies)  

---

## ⚙️ Installation  

1. Clone the repository:  
   ```bash
   git clone https://github.com/your-username/php-secure-db-helper.git
   cd php-secure-db-helper

2. Install dependencies:
 ```bash
  composer install
 ```

3. Create a .env file in the project root:
```bash
APP_ENV=development
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=my_database
DB_USER=root
DB_PASS=secret
DB_CHARSET=utf8mb4
```

4. Create the logs folder (if not exists):
```
mkdir env_logs
touch env_logs/error.log
```


## Project File Structure

```php
your-project/
│
├── .env                # Stores DB credentials & app environment
├── db.php              # Main PDO Singleton class (your project code)
├── test.php           # Example entry point (use db.php here)
├── vendor/             # Composer dependencies (autoload + dotenv)
│   └── autoload.php
│
├── env_logs/           # Log directory (kept OUTSIDE public webroot)
│   └── error.log       # Error & exception logs (auto-generated)
│
└── README.md           # Project documentation (this file)
```



##  🔎 Behind the Scenes – secure-pdo-connector

-  Load .env file with Dotenv
-  This loads your database credentials (DB_HOST, DB_USER, etc.) from the .env file.
- It keeps sensitive data out of your code and makes it easy to change settings.
```php
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```
## Ensure logs folder exists
```php
if (!is_dir(self::LOG_DIR)) {
    mkdir(self::LOG_DIR, 0775, true);
}
```

## Log rotation (prevent huge files)
- If error.log becomes too large, it’s renamed with a timestamp.
- Keeps logs clean and prevents server from filling up.

```php
if (file_exists(self::LOG_FILE) && filesize(self::LOG_FILE) > self::MAX_LOG_SIZE) {
    rename(self::LOG_FILE, self::LOG_FILE . '.' . date('Ymd_His') . '.bak');
}
```

## Error policy (development vs production)
- In development → errors show on screen.
- In production → errors are hidden (but logged).

```php
$env = $_ENV['APP_ENV'] ?? 'production';
error_reporting(E_ALL);
ini_set('display_errors', $env === 'development' ? '1' : '0');
```

## Catch uncaught exceptions
- Any error your code doesn’t handle goes straight into error.log.
- Developers see details only in development mode.

```php
set_exception_handler(function ($e) use ($env) {
    self::log("Unhandled Exception: " . $e->getMessage());
    if ($env === 'development') {
        echo "<pre>" . htmlspecialchars((string)$e) . "</pre>";
    } else {
        echo "Something went wrong. Check logs.";
    }
});

```

## Catch PHP warnings/notices
- Even PHP warnings and notices are safely logged.

```php
set_error_handler(function ($severity, $message, $file, $line) {
    self::log("PHP Error [$severity] $message in $file on line $line");
});
```

## Singleton PDO Connection
- Database connection is created once and reused everywhere.
- Safer + faster than opening new connections.
```php
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
self::$pdo = new PDO($dsn, $user, $pass, $options);
```

## Validate required env variables
- Ensures all important database settings are provided before connecting.
```php
$required = ['DB_HOST','DB_PORT','DB_NAME','DB_USER','DB_PASS','DB_CHARSET'];
foreach ($required as $k) {
    if (empty($_ENV[$k])) {
        throw new RuntimeException("Database configuration missing: $k");
    }
}
```

## Safe Logger
- Every error or custom message goes into env_logs/error.log.
```php
public static function log(string $message): void {
    $ts = date('Y-m-d H:i:s');
    error_log("[$ts] $message" . PHP_EOL, 3, self::LOG_FILE);
}

```

## Safe Query Execution
- Queries always use prepared statements with bound parameters.
- Prevents SQL Injection.

```php
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
```

## Helper methods for developers
- fetchAll → get all rows
- fetch → get one row
- fetchColumn → get single value
- execute → run insert/update/delete

```php
DB::fetchAll("SELECT * FROM users");
DB::fetch("SELECT * FROM users WHERE id = :id", ['id' => 1]);
DB::fetchColumn("SELECT COUNT(*) FROM users");
DB::execute("DELETE FROM users WHERE id = :id", ['id' => 1]);
```

## IN() clause helper
-  Helps safely handle WHERE id IN (...) queries without manual string building.

```php
list($placeholders, $bindings) = DB::inPlaceholders([1,2,3]);
$sql = "SELECT * FROM users WHERE id IN ($placeholders)";
```

# Where You Can Use This Project
- Any Core PHP Project – Easily connect to MySQL without repeating connection code.
- Small to Medium Web Apps – Blogs, CMS, dashboards, or admin panels that need secure DB access.
- Learning Projects – Beginners can learn PDO, .env usage, and error handling in a clean way.
- Production Apps – Built-in error logging and rotation make it safe for live servers.
- Reusable DB Layer – Can be copied into any project as a ready-made “Database Helper.”
- Testing Environments – Switch between development and production modes with just one variable.

  ## 👉 In short:
This project is not a full framework, but a secure base layer to handle database connections, errors, and logging in PHP. It saves time and ensures best practices.
