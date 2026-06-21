<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$view = view('reportes.nuevo')->with('supervisoresCampo', collect())->with('supervisoresCamaras', collect())->with('vehiculos', \App\Models\Vehiculo::all())->with('turno', 'Mañana')->with('defaultSupervisorCamarasId', 1)->render();
preg_match('/unidades: \{(.*?)\}/s', $view, $m);
echo $m[0];
