<?php
require_once __DIR__ . '/db.php';

try {
    DB::conn();
    echo "<h2 style='color:green;'>✅ Database connection successful!</h2>";
} catch (Throwable $e) {
    echo "<h2 style='color:red;'>❌ " . htmlspecialchars($e->getMessage()) . "</h2>";
}
