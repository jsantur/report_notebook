<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$apiKey = env('GEMINI_API_KEY');
if (!$apiKey) {
    echo "❌ GEMINI_API_KEY not set in .env\n";
    exit(1);
}

echo "🔍 Checking available models...\n";
echo "API Key: " . substr($apiKey, 0, 8) . "..." . substr($apiKey, -4) . "\n\n";

$response = \Illuminate\Support\Facades\Http::get(
    'https://generativelanguage.googleapis.com/v1/models',
    ['key' => $apiKey]
);

if ($response->successful()) {
    $data = $response->json();
    echo "✅ Models retrieved successfully!\n\n";
    if (isset($data['models'])) {
        foreach ($data['models'] as $model) {
            echo "📌 " . $model['name'] . "\n";
            if (isset($model['displayName'])) {
                echo "   Display: " . $model['displayName'] . "\n";
            }
            echo "\n";
        }
    }
} else {
    echo "❌ Error: " . $response->status() . "\n";
    echo $response->body() . "\n";
}
