<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestHikvisionConnection extends Command
{
    protected $signature = 'app:test-hikvision-connection';
    protected $description = 'Muestra solo cámaras activas omitiendo LPR y Control de Acceso';

    public function handle()
    {
        $this->info("📡 Iniciando escaneo de cámaras Activas (Omitiendo LPR y Control de Acceso)...");
        
        $filePath = storage_path('app/cameras.csv');

        if (!file_exists($filePath)) {
            $this->error("❌ No se encontró el archivo en: storage/app/cameras.csv");
            return 1;
        }

        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file); // Omitir cabecera

        $tableRows = [];
        $totalAnalizados = 0;
        $totalActivosFiltrados = 0;
        $totalOmitidos = 0;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (empty($row) || count($row) < 3) continue;

            // Limpiar tabulaciones de la exportación de HikCentral
            $alias = trim(str_replace("\t", "", $row[0]));
            $ip = trim(str_replace("\t", "", $row[1]));
            $port = intval(trim(str_replace("\t", "", $row[2])));

            if (empty($ip) || $ip === 'Device Address') continue;

            $totalAnalizados++;

            // Verificar si el nombre contiene "LPR" o "Control de Acceso"
            $esOmitido = (stripos($alias, 'LPR') !== false) || (stripos($alias, 'Control de Acceso') !== false);

            if ($esOmitido) {
                $totalOmitidos++;
                continue; // Saltar al siguiente dispositivo sin evaluar su conexión
            }

            // Medición de latencia y conexión por socket rápido
            $startTime = microtime(true);
            $socket = @fsockopen($ip, $port, $errno, $errstr, 1.0); // 1 segundo de timeout es suficiente en LAN
            $responseTime = round((microtime(true) - $startTime) * 1000);

            if ($socket) {
                fclose($socket);
                $totalActivosFiltrados++;

                // Solo guardamos en la tabla los que están ONLINE
                $tableRows[] = [
                    $alias,
                    $ip,
                    $port,
                    '🟢 ONLINE',
                    $responseTime . ' ms'
                ];
            }
        }

        fclose($file);

        // Imprimir la tabla limpia solo con las seleccionadas
        $this->table(
            ['Nombre de Cámara / Poste', 'Dirección IP', 'Puerto', 'Estado', 'Latencia'],
            $tableRows
        );

        $this->line("\n--- RESUMEN DEL ESCANEO FILTRADO ---");
        $this->info("Total dispositivos en el CSV: $totalAnalizados");
        $this->comment("Dispositivos Excluidos (LPR / Control de Acceso): $totalOmitidos");
        $this->warn("Total de Dispositivos Activos Contabilizados: $totalActivosFiltrados");
        
        return 0;
    }
}