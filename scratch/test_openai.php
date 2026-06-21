<?php

require 'vendor/autoload.php';

// Cargar variables de entorno manualmente si es necesario, 
// pero en Laravel usualmente usamos el framework.
// Sin embargo, para un script rápido fuera de artisan:
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use OpenAI\Laravel\Facades\OpenAI;

echo "Probando conexión con OpenAI...\n";

try {
    $response = OpenAI::chat()->create([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'system', 'content' => 'Responde con la palabra "EXITO".'],
            ['role' => 'user', 'content' => 'Hola']
        ],
    ]);

    echo "Respuesta: " . $response->choices[0]->message->content . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
