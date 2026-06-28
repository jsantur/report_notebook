<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SincronizarCamarasFly extends Command
{
    protected $signature = 'camaras:sincronizar-fly';
    protected $description = 'Chequea el estado de las cámaras localmente y sincroniza el CSV completo con Fly.io';

    public function handle()
    {
        $this->info('📡 Iniciando sincronización de cámaras con Fly.io...');

        // 1. Primero, actualizamos el CSV local con el estado real de las cámaras
        $this->info('🔍 Chequeando estado de cámaras en red local y actualizando CSV...');
        $controller = new \App\Http\Controllers\HikvisionCameraController();
        $response = $controller->getStatus(); // Esto actualiza cameras.csv
        
        // Verificamos que el CSV exista
        $csvPath = storage_path('app/cameras.csv');
        if (!file_exists($csvPath)) {
            $this->error('❌ No se encontró el archivo cameras.csv.');
            return 1;
        }

        $this->info('✅ CSV local actualizado.');

        // 2. Sincronizamos el CSV con Fly.io usando fly sftp
        $this->info('📤 Subiendo CSV a Fly.io...');
        
        // Primero, aseguramos que el directorio exista en Fly.io
        $this->info('   → Asegurando directorio en Fly.io...');
        $resultDir = shell_exec('fly ssh console -C "mkdir -p /var/www/html/storage/app"');
        if ($resultDir) {
            $this->info($resultDir);
        }

        // Luego, subimos el CSV
        $this->info('   → Subiendo cameras.csv...');
        $resultPut = shell_exec('fly sftp put storage/app/cameras.csv /var/www/html/storage/app/cameras.csv');
        
        if ($resultPut === null) {
            $this->error('❌ No se pudo subir el archivo a Fly.io. Asegúrate de que el CLI de Fly esté autenticado.');
            $this->info('💡 Intenta ejecutar "fly auth login" primero.');
            return 1;
        }

        $this->info($resultPut);
        $this->info('✅ ¡Sincronización completada exitosamente!');
        $this->info('   El CSV con el estado de las cámaras se actualizó en Fly.io.');
        $this->info('   Ahora Fly.io verá el mismo estado que tu máquina local.');

        return 0;
    }
}
