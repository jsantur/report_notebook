<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$vehiculos = \App\Models\Vehiculo::all();
foreach($vehiculos as $v) {
    echo $v->tipo_patrullaje . " => " . $v->nro_unidad . " (" . bin2hex($v->nro_unidad) . ")\n";
}
