<?php
/**
 * Email Configuration File
 * Centralized configuration for email settings
 */

return [
    'contact' => [
        'to' => 'info@goldenictsolutions.com',
        'subject' => 'New Contact Form Submission - Golden ICT Solutions'
    ],
    
    'newsletter' => [
        'to' => 'info@goldenictsolutions.com',
        'subject_prefix' => 'Newsletter Subscription: '
    ],
    
    'smtp' => [
        'enabled' => true,
        'host' => 'smtp.zoho.com',
        'username' => 'info@goldenictsolutions.com',
        'password' => getenv('SMTP_PASSWORD') ?: 'YOUR_ZOHO_APP_PASSWORD',
        'port' => 465,
        'encryption' => 'ssl',
        'from_name' => 'Golden ICT Solutions',
        'from_email' => 'info@goldenictsolutions.com'
    ],
    
    'validation' => [
        'max_message_length' => 5000,
        'required_fields' => ['name', 'email', 'message']
    ],
    
    'security' => [
        'rate_limit' => 5,
        'rate_limit_window' => 3600,
        'honeypot_enabled' => true
    ]
];
