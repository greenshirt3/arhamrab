<?php
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) { 
        return $needle !== '' && substr($haystack, 0, strlen($needle)) === $needle; 
    }
}

// Looking for .env inside the api folder 
$envPath = __DIR__ . '/.env'; 
if (file_exists($envPath)) {
    foreach(file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#')) continue;
        $hashPos = strpos($line, '#');
        if ($hashPos !== false) { 
            $line = trim(substr($line, 0, $hashPos)); 
            if ($line === '') continue; 
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) { 
            $k = trim($parts[0]); 
            $v = trim($parts[1]); 
            if ($k !== '') { 
                $_ENV[$k] = $v; 
                putenv("$k=$v"); 
                $_SERVER[$k] = $v; 
            } 
        }
    }
}

define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'arham_finance');
define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '');

define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? 'change_this_secret');

// Path corrected to go from backend/api to backend/public/uploads [cite: 11, 18]
define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
if (!is_dir(UPLOAD_DIR)) { 
    @mkdir(UPLOAD_DIR, 0775, true); 
}