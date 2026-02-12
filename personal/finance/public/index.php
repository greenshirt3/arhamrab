<?php
/**
 * Arham Finance - Main Entry Point & Router
 * Location: backend/public/index.php
 */

// 1. Load the system
require __DIR__ . '/../api/bootstrap.php';
use Lib\Response;

// 2. Capture Request Data
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// 3. Normalize Path
// This removes the query string (e.g., ?id=1) and cleans leading/trailing slashes
$path = parse_url($requestUri, PHP_URL_PATH);
$path = '/' . trim($path, '/');

/**
 * Router Function
 * Maps the URL path to a Controller and Action
 */
function route($method, $path) {
    // Exact match for Login
    if ($method === 'POST' && $path === 'api/auth/login') {
        return ['Controllers\AuthController', 'login'];
    }
    
    // We add a leading slash check for flexibility
    $normalizedPath = ltrim($path, '/');

    switch (true) {
        case $method === 'GET'  && $normalizedPath === 'api/health':           return ['Controllers\HealthController', 'ok'];
        case $method === 'POST' && $normalizedPath === 'api/auth/login':       return ['Controllers\AuthController', 'login'];
        case $method === 'GET'  && $normalizedPath === 'api/sync/bootstrap':    return ['Controllers\SyncController', 'bootstrap'];
        case $method === 'GET'  && $normalizedPath === 'api/sync/changes':      return ['Controllers\SyncController', 'changes'];
        case $method === 'POST' && $normalizedPath === 'api/sync/push':         return ['Controllers\SyncController', 'push'];
        case $method === 'GET'  && $normalizedPath === 'api/invoices':          return ['Controllers\InvoiceController', 'list'];
        case $method === 'POST' && $normalizedPath === 'api/invoices':          return ['Controllers\InvoiceController', 'create'];
        case $method === 'GET'  && $normalizedPath === 'api/bills':             return ['Controllers\BillController', 'list'];
        case $method === 'POST' && $normalizedPath === 'api/bills':             return ['Controllers\BillController', 'create'];
        case $method === 'GET'  && $normalizedPath === 'api/reports/pnl':        return ['Controllers\ReportsController', 'pnl'];
        case $method === 'POST' && $normalizedPath === 'api/attachments':       return ['Controllers\AttachmentController', 'upload'];
        
        // Pattern matching for dynamic IDs
        case $method === 'POST' && preg_match('/^api\/invoices\/(\d+)\/receive$/', $normalizedPath, $m): 
            return ['Controllers\InvoiceController', 'receive', $m[1]];
    }
    return null;
}

// 4. Execute Route
$target = route($method, $path);

if (!$target) {
    // Helpful error for debugging path mismatches
    Response::json([
        'error' => 'Not Found',
        'method' => $method,
        'path_tried' => $path,
        'hint' => 'Check if your .htaccess is passing the full path correctly.'
    ], 404);
    exit;
}

// 5. Check Authentication
// Skip Auth check for login and health
$publicPaths = ['api/auth/login', 'api/health'];
if (!in_array(ltrim($path, '/'), $publicPaths)) {
    \Controllers\AuthController::checkAuth();
}

// 6. Response
list($controller, $action, $param) = array_pad($target, 3, null);
$instance = new $controller();
$result = $param ? $instance->$action($param) : $instance->$action();

Response::json($result);