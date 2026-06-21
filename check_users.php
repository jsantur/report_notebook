<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = App\Models\User::all();
echo "USUARIOS EN BASE DE DATOS:\n";
echo str_repeat("=", 60) . "\n";
foreach($users as $u) {
    echo "ID: {$u->id}\n";
    echo "  Username: {$u->username}\n";
    echo "  Email: {$u->email}\n";
    echo "  Name: {$u->name}\n";
    echo "  Activo: " . ($u->activo ? 'SI' : 'NO') . "\n";
    echo str_repeat("-", 40) . "\n";
}
