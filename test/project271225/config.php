<?php
$json = file_get_contents('arham_data.json');
$data = json_decode($json, true);
$info = $data['settings'];
?>