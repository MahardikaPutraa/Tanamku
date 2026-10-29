<?php

// Forward Vercel requests to Laravel
// Ensure serverless writable directories in /tmp
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// In Vercel serverless, explicitly flag HTTPS
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

require __DIR__ . '/../public/index.php';

