<?php
// GLOBAL SETTINGS
define('APP_NAME', 'City International Smart Hospital');
define('HOSPITAL_PHONE', '+92 300 1234567');
define('HOSPITAL_ADDRESS', 'Medical Road, Jalalpur Jattan, Pakistan');
define('CURRENCY', 'PKR');

// PATH CONSTANTS (Fixes subfolder issues)
define('DIR_BASE', __DIR__);
define('DIR_DATA', DIR_BASE . '/data/');

// FILE PATHS
define('FILE_USERS', DIR_DATA . 'users.json');
define('FILE_PATIENTS', DIR_DATA . 'patients.json');
define('FILE_VISITS', DIR_DATA . 'visits.json');
define('FILE_INVENTORY', DIR_DATA . 'inventory.json');
define('FILE_LAB', DIR_DATA . 'lab_queue.json');

// TIMEZONE
date_default_timezone_set('Asia/Karachi');

// AUTO-INITIALIZE DATA FOLDER
if (!file_exists(DIR_DATA)) {
    mkdir(DIR_DATA, 0777, true);
}

// AUTO-CREATE DEFAULT ADMIN USER IF MISSING
if (!file_exists(FILE_USERS)) {
    $default_users = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'name' => 'System Administrator',
            'dept' => 'Management'
        ],
        [
            'id' => 2,
            'username' => 'doctor',
            'password' => password_hash('doc123', PASSWORD_DEFAULT),
            'role' => 'doctor',
            'name' => 'Dr. Ali Raza',
            'dept' => 'Cardiology'
        ]
    ];
    file_put_contents(FILE_USERS, json_encode($default_users, JSON_PRETTY_PRINT));
}

// START SESSION GLOBAL
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>