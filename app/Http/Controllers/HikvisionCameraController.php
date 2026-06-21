<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HikvisionCameraController extends Controller
{
    public function getStatus()
    {
        $filePath = storage_path('app/cameras.csv');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Archivo de cámaras no encontrado.'], 404);
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Omitir la cabecera del CSV

        $activeCameras = [];
        $totalAnalizados = 0;
        $totalOmitidos = 0;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (empty($row) || count($row) < 3) continue;

            // Limpieza de tabulaciones nativas del CSV de Hikvision
            $alias = trim(str_replace("\t", "", $row[0]));
            $ip = trim(str_replace("\t", "", $row[1]));
            $port = intval(trim(str_replace("\t", "", $row[2])));

            if (empty($ip) || $ip === 'Device Address') continue;

            $totalAnalizados++;

            // Filtro estricto de exclusión para LPR y Control de Acceso
            $esOmitido = (stripos($alias, 'LPR') !== false) || (stripos($alias, 'Control de Acceso') !== false);
            if ($esOmitido) {
                $totalOmitidos++;
                continue;
            }

            // Escaneo veloz por socket local (1 segundo máx)
            $socket = @fsockopen($ip, $port, $errno, $errstr, 1.0);

            if ($socket) {
                fclose($socket);
                
                $activeCameras[] = [
                    'nombre' => $alias,
                    'ip' => $ip,
                    'puerto' => $port,
                    'estado' => 'ONLINE'
                ];
            }
        }
        fclose($file);

        // NO ORDENAMOS ALFABÉTICAMENTE - MANTENEMOS EL ORDEN DEL CSV
        // Solo aseguramos índices limpios para array JSON válido
        $activeCameras = array_values($activeCameras);

        return response()->json([
            'resumen' => [
                'total_csv' => $totalAnalizados,
                'omitidos' => $totalOmitidos,
                'activos_validos' => count($activeCameras),
                'inactivos' => ($totalAnalizados - $totalOmitidos - count($activeCameras))
            ],
            'cameras' => $activeCameras
        ]);
    }

    // Método legacy mantenido para compatibilidad
    public function getDashboardStats()
    {
        return $this->getStatus();
    }
}
