<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::all();
echo "--- REPORTE DE SEGURIDAD DE USUARIOS ---\n";
foreach($users as $u) {
    $isHashed = password_get_info($u->password)['algoName'] !== 'unknown';
    $ansHashed = password_get_info($u->security_answer)['algoName'] !== 'unknown';
    echo "Usuario: {$u->username}\n";
    echo "  - Password: " . ($isHashed ? "✅ CIFRADO" : "❌ TEXTO PLANO") . "\n";
    echo "  - Respuesta: " . ($ansHashed ? "✅ CIFRADO" : "❌ TEXTO PLANO") . "\n";
    echo "--------------------------------------\n";
}
