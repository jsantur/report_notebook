<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$admin = App\Models\User::find(1);
if($admin) {
    // Guardar password plano - el modelo tiene cast 'hashed' que lo hashea automáticamente
    $admin->password = 'admin123';
    $admin->save();
    echo "Contraseña de admin reseteada a: admin123\n";
} else {
    echo "Usuario admin no encontrado\n";
}
