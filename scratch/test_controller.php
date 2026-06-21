<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\AIController;
use Illuminate\Http\Request;

echo "Probando Gemini en AIController directamente...\n";

$controller = new AIController();
$request = new Request(['text' => 'un sujeto que estaba orinando en la via publica']);

try {
    $response = $controller->correctText($request);
    $data = json_decode($response->getContent(), true);

    echo "Texto Corregido: " . ($data['corrected_text'] ?? 'ERROR') . "\n";
    echo "IA Usada: " . ($data['is_ai_corrected'] ? 'SI' : 'NO (Fallback)') . "\n";
    if (isset($data['debug_error'])) {
        echo "Error Detalle: " . $data['debug_error'] . "\n";
    }
} catch (\Exception $e) {
    echo "Error Fatal: " . $e->getMessage() . "\n";
}
