<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE DATOS ===\n\n";
echo "Serenos: " . \App\Models\Serenazgo::count() . "\n";
echo "Cámaras: " . \App\Models\Camara::count() . "\n";
echo "Megáfonos: " . \App\Models\Megafono::count() . "\n";
echo "Usuarios: " . \App\Models\User::count() . "\n";
echo "\n=== PRIMEROS 5 SERENOS ===\n";
$serenos = \App\Models\Serenazgo::take(5)->get();
foreach ($serenos as $s) {
    echo "- {$s->apellido_paterno} {$s->apellido_materno}, {$s->nombres}\n";
}
echo "\n=== PRIMERAS 5 CÁMARAS ===\n";
$camaras = \App\Models\Camara::take(5)->get();
foreach ($camaras as $c) {
    echo "- {$c->nombre}\n";
}
