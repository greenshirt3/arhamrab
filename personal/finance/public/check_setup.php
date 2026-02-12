<?php
/**
 * Arham Finance - System Health & Logic Check
 * Run this in VS Code terminal: php public/check_setup.php
 */

header('Content-Type: text/plain');
echo "--- ARHAM FINANCE SYSTEM CHECK ---\n";

// 1. Test Autoloader & Bootstrap
echo "1. Testing Bootstrap: ";
if (file_exists(__DIR__ . '/../api/bootstrap.php')) {
    require __DIR__ . '/../api/bootstrap.php';
    echo "OK\n";
} else {
    die("FAILED - bootstrap.php not found!\n");
}

// 2. Test Database Connection
echo "2. Testing Database: ";
try {
    $pdo = \Lib\DB::pdo();
    echo "OK (Connected to " . DB_NAME . ")\n";
} catch (Exception $e) {
    echo "FAILED - " . $e->getMessage() . "\n";
}

// 3. Test Controller Loading
echo "3. Testing Controllers: ";
$controllers = ['HealthController', 'AuthController', 'InvoiceController', 'SyncController'];
$missing = [];
foreach ($controllers as $c) {
    $className = "Controllers\\$c";
    if (!class_exists($className)) {
        $missing[] = $c;
    }
}
if (empty($missing)) {
    echo "OK (All core controllers found)\n";
} else {
    echo "FAILED - Missing: " . implode(', ', $missing) . "\n";
}

// 4. Test Accounting Logic (Sync)
echo "4. Testing Sync Data: ";
try {
    $sync = new \Controllers\SyncController();
    $data = $sync->bootstrap();
    $accountCount = count($data['accounts'] ?? []);
    if ($accountCount > 0) {
        echo "OK ($accountCount accounts found in DB)\n";
    } else {
        echo "WARNING - Database tables are empty. Did you run schema.sql?\n";
    }
} catch (Exception $e) {
    echo "FAILED - " . $e->getMessage() . "\n";
}

// 5. Test Directory Permissions
echo "5. Testing Upload Folder: ";
if (is_writable(UPLOAD_DIR)) {
    echo "OK (Writable at " . UPLOAD_DIR . ")\n";
} else {
    echo "FAILED - Cannot write to uploads folder. Check permissions.\n";
}

echo "----------------------------------\n";
echo "Check Complete.\n";