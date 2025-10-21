<?php
/**
 * Newsletter Subscription Handler - Refactored and Secure
 */

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

require_once __DIR__ . '/EmailHandler.php';
$config = require __DIR__ . '/config.php';

$handler = new EmailHandler($config);
$result = $handler->sendEmail('newsletter', $_POST);

echo $result;
