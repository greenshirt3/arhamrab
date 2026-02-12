<?php
// public_html/family/api/sync.php
require 'config.php';
$chatFile = $DATA_DIR . 'chat_general.json';

if (file_exists($chatFile)) {
    echo file_get_contents($chatFile);
} else {
    echo "[]";
}
?>