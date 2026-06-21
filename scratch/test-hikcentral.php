<?php

// Inicializar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Crear instancia del controlador
use App\Http\Controllers\HikvisionCameraController;
$controller = new HikvisionCameraController();

// Llamar al método y mostrar la respuesta
$response = $controller->getDashboardStats();
echo json_encode($response->getData(true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n";
