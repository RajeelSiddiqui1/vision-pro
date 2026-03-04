<?php
/**
 * Email Configuration
 * Uses environment variables from .env file
 */

require_once __DIR__ . '/env.php';

return [
    // SMTP Settings
    'host' => env('MAIL_HOST', 'smtp.gmail.com'),
    'port' => (int) env('MAIL_PORT', 587),
    'username' => env('MAIL_USERNAME', 'Visionpro.lcd@gmail.com'),
    'password' => env('MAIL_PASSWORD', ''),
    
    // Security
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    
    // From Address
    'from_email' => env('MAIL_FROM_ADDRESS', 'Visionpro.lcd@gmail.com'),
    'from_name' => env('MAIL_FROM_NAME', 'VisionPro'),
    
    // Reply-To
    'reply_to' => env('MAIL_FROM_ADDRESS', 'Visionpro.lcd@gmail.com'),
    'reply_name' => env('MAIL_FROM_NAME', 'VisionPro Support'),
    
    // Debug mode (0 = off, 1 = errors only, 2 = verbose)
    'debug' => (int) env('APP_DEBUG', 0) ? 2 : 0,
];


