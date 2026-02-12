<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if(!$input) {
    echo json_encode(['status'=>'error']);
    exit;
}

$ref = 'ORD-' . time();
$file = '../orders/' . $ref . '.json';

// Create orders folder if not exists
if(!is_dir('../orders')) mkdir('../orders');

if(file_put_contents($file, json_encode($input))) {
    echo json_encode(['status'=>'success', 'ref'=>$ref]);
} else {
    echo json_encode(['status'=>'error']);
}
?>