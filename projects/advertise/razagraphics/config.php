<?php
$json_data = file_get_contents('raza_data.json');
$data = json_decode($json_data, true);
$info = $data['settings'];
?>