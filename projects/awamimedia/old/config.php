<?php
// config.php
date_default_timezone_set('Asia/Karachi');
$dataFile = 'data.json';

// --- SECURITY ---
$USERNAME = "admin";
$PASSWORD = "admin123"; // Change this!

// --- SYSTEM SETUP ---
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, '[]');
}

function getYoutubeID($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : false;
}
?>
