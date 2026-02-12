<?php
// config.php - Loads the JSON data
$json_file = 'hospital_data.json';

if (!file_exists($json_file)) {
    die("Error: Configuration file missing.");
}

$json_data = file_get_contents($json_file);
$hospital = json_decode($json_data, true);

// Shortcuts for easy access
$info = $hospital['settings'];
$theme = $hospital['theme'];
?>