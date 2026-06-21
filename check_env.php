<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "APP_ENV: " . env('APP_ENV', 'not set') . "\n";
echo "APP_URL: " . env('APP_URL', 'not set') . "\n";
echo "SESSION_SECURE_COOKIE: " . (env('SESSION_SECURE_COOKIE') ? 'true' : 'false') . "\n";
echo "ASSET_URL: " . env('ASSET_URL', 'not set') . "\n";
echo "Current URL scheme: " . (request()->secure() ? 'HTTPS' : 'HTTP') . "\n";
