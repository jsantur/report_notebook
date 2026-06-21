<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestLocalCameras extends Command
{
    protected $signature = 'camaras:escanear';
    protected $description = 'Escanea el CSV de cámaras y muestra el estado detallado';

    public function handle()
    {
        $this->info('📡 Iniciando escaneo de cámaras desde el CSV...');
        $this->newLine();

        $csvPath = storage_path('app/cameras.csv');

        if (!File::exists($csvPath)) {
            $this->error('❌ Archivo cameras.csv no encontrado en: ' . $csvPath);
            return 1;
        }

        $file = fopen($csvPath, 'r');
        fgetcsv($file); // Omitir cabecera

        $camaras = [];
        $excluidas = [];
        $inactivas = [];
        $totalAnalizados = 0;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if (empty($row) || count($row) < 3) continue;
            
            $nombre = trim(str_replace("\t", '', $row[0]));
            $ip = trim(str_replace("\t", '', $row[1]));
            $puerto = intval(trim(str_replace("\t", '', $row[2])));
            
            if (empty($ip) || $ip === 'Device Address') continue;
            
            $totalAnalizados++;

            // Primero chequeamos exclusión por nombre
            $esExcluido = (stripos($nombre, 'LPR') !== false) || (stripos($nombre, 'Control de Acceso') !== false);
            
            if ($esExcluido) {
                $excluidas[] = [
                    'nombre' => $nombre,
                    'ip' => $ip,
                    'puerto' => $puerto,
                    'motivo' => 'Contiene LPR o Control de Acceso en el nombre'
                ];
                continue;
            }

            // Ahora chequeamos conexión por socket
            $socket = @fsockopen($ip, $puerto, $errno, $errstr, 1.0);
            $estaOnline = ($socket !== false);
            
            if ($socket) {
                fclose($socket);
            }
            
            if (!$estaOnline) {
                $inactivas[] = [
                    'nombre' => $nombre,
                    'ip' => $ip,
                    'puerto' => $puerto,
                    'motivo' => 'No responde al escaneo (OFFLINE)'
                ];
                continue;
            }

            // Si llega hasta aquí, la cámara está ONLINE y cumple los filtros
            $camaras[] = [
                'nombre' => $nombre,
                'ip' => $ip,
                'puerto' => $puerto,
                'online' => true
            ];
        }
        
        fclose($file);

        // NO ORDENAMOS ALFABÉTICAMENTE - MANTENEMOS EL ORDEN DEL CSV
        // Solo aseguramos índices limpios para consistencia
        $camaras = array_values($camaras);

        // 1. Mostrar cámaras excluidas
        if (count($excluidas) > 0) {
            $this->warn('⚠️  Cámaras EXCLUIDAS del listado:');
            $this->table(
                ['Nombre', 'IP', 'Puerto', 'Motivo de exclusión'],
                array_map(function($cam) {
                    return [$cam['nombre'], $cam['ip'], $cam['puerto'], $cam['motivo']];
                }, $excluidas)
            );
            $this->newLine();
        }

        // 2. Mostrar cámaras inactivas
        if (count($inactivas) > 0) {
            $this->error('🔴 Cámaras INACTIVAS (OFFLINE) - NO incluidas:');
            $this->table(
                ['Nombre', 'IP', 'Puerto', 'Motivo'],
                array_map(function($cam) {
                    return [$cam['nombre'], $cam['ip'], $cam['puerto'], $cam['motivo']];
                }, $inactivas)
            );
            $this->newLine();
        }

        // 3. Mostrar cámaras activas (que finalmente se incluyen)
        if (count($camaras) > 0) {
            $this->info('✅ Cámaras ACTIVAS (ONLINE) - INCLUIDAS en el listado:');
            $this->table(
                ['Nombre de Cámara / Poste', 'Dirección IP', 'Puerto', 'Estado'],
                array_map(function($cam) {
                    return [
                        $cam['nombre'],
                        $cam['ip'],
                        $cam['puerto'],
                        $cam['online'] ? '🟢 ONLINE' : '🔴 OFFLINE'
                    ];
                }, $camaras)
            );
        } else {
            $this->warn('⚠️  No hay cámaras activas para mostrar.');
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════');
        $this->info('📊 RESUMEN FINAL DEL ESCANEO:');
        $this->line('   - Total dispositivos en CSV: ' . $totalAnalizados);
        $this->line('   - Excluidas por nombre: ' . count($excluidas));
        $this->line('   - Inactivas (OFFLINE): ' . count($inactivas));
        $this->info('   - ✅ ACTIVAS para el sistema: ' . count($camaras));
        $this->info('═══════════════════════════════════════════════');

        return 0;
    }
}
