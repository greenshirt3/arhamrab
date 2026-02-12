<?php
spl_autoload_register(function ($class) {
    $prefixes = array(
        'Lib\\'         => __DIR__ . '/lib/',
        'Controllers\\' => __DIR__ . '/controllers/'
    );
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) { continue; }
        $relative = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) { 
            require $file; 
            return;
        }
    }
});
require __DIR__ . '/config.php';