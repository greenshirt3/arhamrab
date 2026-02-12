
<?php
// ======== Arham Digital Hub - Config ========
// !! IMPORTANT: change these for RabHost !!

// DB
define('DB_HOST', 'localhost');
define('DB_NAME', 'arham_erp');     // create in phpMyAdmin
define('DB_USER', 'arham_user');    // your db user
define('DB_PASS', 'Strong_DB_Pass'); // your db pass

// App
define('APP_NAME', 'Arham Digital Hub');
define('APP_TIMEZONE', 'Asia/Karachi'); date_default_timezone_set(APP_TIMEZONE);

// Security
define('APP_SECRET', 'change-this-32-char-secret');  // used for CSRF/webhook
define('SESSION_NAME', 'arham_session');

// Printing (logical printer names you’ll bind via QZ Tray on each PC)
define('PRN_THERMAL', 'THERMAL');   // route tokens/receipts
define('PRN_LASER',   'PANTUM');    // invoices/forms
define('PRN_EPSON',   'EPSON_L8050'); // photos

// Webhook from HBL device SMS forwarder (POST JSON -> transactions.php?action=webhook)
define('WEBHOOK_SHARED_SECRET', 'ArhamSecure123'); // must match phone app forwarder

// Feature flags
define('FEATURE_QZ', true);   // set false if you don’t want QZ Tray yet

// ======== End config ========
