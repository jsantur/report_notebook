<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CamarasAutoSync extends Command
{
    protected $signature = 'camaras:auto-sync';
    protected $description = 'Escanea cámaras localmente, detecta cambios y sincroniza automáticamente con Fly.io (con reintentos)';

    // Rutas de archivos
    private $csvPath;
    private $lastSyncCsvPath;
    private $logPath;

    public function __construct()
    {
        parent::__construct();
        $this->csvPath = storage_path('app/cameras.csv');
        $this->lastSyncCsvPath = storage_path('app/cameras_last_sync.csv');
        $this->logPath = storage_path('logs/camaras_auto_sync.log');
    }

    public function handle()
    {
        $this->log('========== INICIANDO SINCRONIZACIÓN AUTOMÁTICA ==========');

        try {
            // 1. Escanear cámaras y actualizar CSV local
            $this->log('Paso 1: Escaneando cámaras...');
            $controller = new \App\Http\Controllers\HikvisionCameraController();
            $controller->getStatus(); // Actualiza el CSV

            if (!file_exists($this->csvPath)) {
                $this->error('❌ No se encontró el CSV local.');
                $this->log('ERROR: CSV no encontrado.');
                return 1;
            }
            $this->log('✅ CSV local actualizado.');

            // 2. Detectar si hubo cambios desde la última sincronización
            $cambiosDetectados = $this->detectarCambios();
            if (!$cambiosDetectados) {
                $this->info('ℹ️ No hay cambios desde la última sincronización.');
                $this->log('Sin cambios. Finalizando.');
                return 0;
            }

            $this->log('⚠️ Cambios detectados! Iniciando sincronización...');

            // 3. Sincronizar con Fly.io (con reintentos)
            $sincronizado = $this->sincronizarConReintentos(3); // 3 reintentos máximos
            if (!$sincronizado) {
                $this->error('❌ Falló la sincronización después de varios reintentos.');
                $this->log('ERROR: Falló la sincronización.');
                return 1;
            }

            // 4. Guardar la versión sincronizada para comparar la próxima vez
            copy($this->csvPath, $this->lastSyncCsvPath);
            $this->log('✅ Versión sincronizada guardada.');

            $this->info('🎉 ¡Sincronización completada exitosamente!');
            $this->log('========== SINCRONIZACIÓN EXITOSA ==========');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error fatal: ' . $e->getMessage());
            $this->log('ERROR FATAL: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Compara el CSV actual con la última versión sincronizada para detectar cambios
     */
    private function detectarCambios(): bool
    {
        // Si no hay versión anterior, hay cambios
        if (!file_exists($this->lastSyncCsvPath)) {
            $this->log('Primera sincronización (no hay versión anterior).');
            return true;
        }

        // Comparar hashes MD5 para detectar cambios rápidamente
        $hashActual = md5_file($this->csvPath);
        $hashAnterior = md5_file($this->lastSyncCsvPath);

        $this->log("Hash actual: $hashActual | Hash anterior: $hashAnterior");

        return $hashActual !== $hashAnterior;
    }

    /**
     * Sincroniza con Fly.io con reintentos exponenciales
     */
    private function sincronizarConReintentos(int $maxIntentos): bool
    {
        $intentos = 0;
        $espera = 5; // Esperar 5 segundos entre reintentos (aumenta cada vez)

        while ($intentos < $maxIntentos) {
            $intentos++;
            $this->log("Intento $intentos de $maxIntentos...");

            try {
                // 1. Asegurar directorio en Fly.io
                shell_exec('fly ssh console -C "mkdir -p /var/www/html/storage/app"');

                // 2. Subir el CSV
                $resultado = shell_exec('fly sftp put ' . escapeshellarg($this->csvPath) . ' /var/www/html/storage/app/cameras.csv');

                if ($resultado !== null) {
                    $this->log("✅ Intento $intentos exitoso!");
                    return true;
                }

                $this->log("⚠️ Intento $intentos fallido. Reintentando en $espera segundos...");
                sleep($espera);
                $espera *= 2; // Espera exponencial: 5, 10, 20...

            } catch (\Exception $e) {
                $this->log("⚠️ Error en intento $intentos: " . $e->getMessage());
                sleep($espera);
                $espera *= 2;
            }
        }

        return false;
    }

    /**
     * Registra mensajes en el log
     */
    private function log(string $mensaje): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $linea = "[$timestamp] $mensaje" . PHP_EOL;
        file_put_contents($this->logPath, $linea, FILE_APPEND);
        $this->info($mensaje);
    }
}
