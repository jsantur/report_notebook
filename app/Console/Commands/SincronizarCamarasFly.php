<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SincronizarCamarasFly extends Command
{
    protected $signature = 'camaras:sincronizar-fly';
    protected $description = 'Chequea el estado de las cámaras localmente y sincroniza el archivo de estado con Fly.io';

    public function handle()
    {
        $this->info('📡 Iniciando sincronización de cámaras con Fly.io...');

        // 1. Primero, generamos el archivo de estado localmente (esto lo hace el controlador)
        $this->info('🔍 Chequeando estado de cámaras en red local...');
        $controller = new \App\Http\Controllers\HikvisionCameraController();
        $controller->getStatus(); // Esto genera cameras-status.json

        // 2. Verificamos que el archivo exista
        $estadoPath = storage_path('app/cameras-status.json');
        if (!file_exists($estadoPath)) {
            $this->error('❌ No se pudo generar el archivo de estado de cámaras.');
            return 1;
        }

        // 3. Sincronizamos el archivo con Fly.io usando fly sftp
        $this->info('📤 Subiendo archivo de estado a Fly.io...');
        
        // Primero, aseguramos que el directorio exista en Fly.io
        $this->info('   → Asegurando directorio en Fly.io...');
        $resultDir = shell_exec('fly ssh console -C "mkdir -p /var/www/html/storage/app"');
        $this->info($resultDir);

        // Luego, subimos el archivo
        $this->info('   → Subiendo cameras-status.json...');
        $resultPut = shell_exec('fly sftp put storage/app/cameras-status.json /var/www/html/storage/app/cameras-status.json');
        
        if ($resultPut === null) {
            $this->error('❌ No se pudo subir el archivo a Fly.io. Asegúrate de que el CLI de Fly esté autenticado.');
            $this->info('💡 Intenta ejecutar "fly auth login" primero.');
            return 1;
        }

        $this->info($resultPut);
        $this->info('✅ ¡Sincronización completada exitosamente!');
        $this->info('   El estado de las cámaras se actualizó en Fly.io.');

        return 0;
    }
}
