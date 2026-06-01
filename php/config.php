<?php
// Database configuration
 $db_host = 'localhost';
 $db_name = 'contact_form';
 $db_user = 'root';
 $db_password = '';
 $db_charset = 'utf8mb4';

// Email configuration
 $email_config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'gulnisarshaikh@gmail.com',
    'password' => 'YOUR_APP_PASSWORD', // Use app password for Gmail
    'encryption' => 'tls',
    'from' => [
        'address' => 'gulnisarshaikh@gmail.com',
        'name' => 'Contact Form'
    ],
    'to' => [
        'address' => 'gulnisarshaikh@gmail.com',
        'name' => 'Admin'
    ]
];

// Security settings
 $security = [
    'max_attempts' => 5,
    'attempt_window' => 600, // 10 minutes in seconds
    'spam_keywords' => [
        'casino', 'crypto', 'free money', 'earn fast', 
        'viagra', 'lottery', 'poker', 'gambling'
    ],
    'honeypot_field' => 'website'
];

// Rate limiting settings
 $rate_limiting = [
    'enabled' => true,
    'max_attempts' => 5,
    'time_window' => 600 // 10 minutes
];

// CAPTCHA settings
 $captcha = [
    'math' => [
        'enabled' => true,
        'min_value' => 1,
        'max_value' => 10
    ],
    'google' => [
        'enabled' => false,
        'site_key' => 'YOUR_SITE_KEY',
        'secret_key' => 'YOUR_SECRET_KEY'
    ]
];