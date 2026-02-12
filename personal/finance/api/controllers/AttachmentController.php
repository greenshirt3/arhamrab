<?php
namespace Controllers;
use Lib\Response;

class AttachmentController { 
    public function upload(){ 
        if (!isset($_FILES['file'])) { 
            Response::json(array('error'=>'No file'), 400); 
            exit; 
        } 

        $name = basename($_FILES['file']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'pdf', 'zip', 'docx');

        // Security: Prevent malicious script uploads
        if (!in_array($ext, $allowed)) {
            Response::json(array('error'=>'File type not allowed'), 403);
            exit;
        }

        // Correct path for your specific RabHost subfolder setup
        $targetDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($targetDir)) { @mkdir($targetDir, 0775, true); }

        $fileName = uniqid('att_') . '_' . $name;
        $target = $targetDir . '/' . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) { 
            return array('url'=>'/uploads/' . $fileName); 
        } 
        
        Response::json(array('error'=>'Upload failed'), 500); 
        exit; 
    } 
}