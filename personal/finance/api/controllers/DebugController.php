<?php
namespace Controllers;

class DebugController { 
    public function env(){ 
        return array(
            'php' => phpversion(),
            'host' => getenv('DB_HOST') ?: 'not set',
            'db' => getenv('DB_NAME') ?: 'not set'
        ); 
    } 
}