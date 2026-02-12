<?php
namespace Controllers;
use Lib\Response; 
use Lib\JWT;

class AuthController { 
    /**
     * Authenticates user against hardcoded owner credentials [cite: 1411, 1413]
     */
    public function login() { 
        // Read the JSON body from the Android request 
        $body = json_decode(file_get_contents('php://input'), true); 
        $email = $body['email'] ?? ''; 
        $password = $body['password'] ?? ''; 
        
        // Manual credentials check [cite: 1413]
        if ($email === 'owner@arham.local' && $password === 'password') { 
            // Generate JWT Token valid for 24 hours (86400 seconds) [cite: 1413, 1414]
            $token = JWT::encode(array(
                'sub' => 1,
                'email' => $email,
                'exp' => time() + 86400
            ), JWT_SECRET); 
            
            return array('token' => $token); 
        } 
        
        // Return 401 if login fails [cite: 1414]
        Response::json(array('error' => 'Invalid credentials'), 401); 
        exit; 
    } 

    /**
     * Middleware to verify JWT on every protected request [cite: 1415]
     */
    public static function checkAuth() { 
        $h = $_SERVER['HTTP_AUTHORIZATION'] ?? ''; 
        
        // Check for Bearer token in the header [cite: 1416]
        if (stripos($h, 'Bearer ') !== 0) { 
            Response::json(array('error' => 'Unauthorized'), 401); 
            exit; 
        } 
        
        $jwt = substr($h, 7); 
        $payload = \Lib\JWT::decode($jwt, JWT_SECRET); 
        
        // Verify token validity and expiration [cite: 1418]
        if (!$payload || ($payload['exp'] ?? 0) < time()) { 
            Response::json(array('error' => 'Unauthorized'), 401); 
            exit; 
        } 
        
        // Store user ID for use in other controllers [cite: 1419, 1420]
        $_SERVER['user_id'] = $payload['sub'] ?? 0; 
    } 
}