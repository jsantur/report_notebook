<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HikvisionCameraController extends Controller
{
    // URL de tu túnel ngrok (LA CAMBIARÁS CADA VEZ QUE REINICIES NGROK, O USA UN DOMINIO PERSONALIZADO)
    // Ejemplo: "https://abc123.ngrok-free.app"
    private $ngrokUrl;

    public function __construct()
    {
        $this->ngrokUrl = env('NGROK_URL', '');
    }

    public function getStatus()
    {
        // Si es LOCAL: Chequeamos las cámaras directamente (como antes)
        if (app()->environment('local')) {
            return $this->checkCamerasLocally();
        }

        // Si es PRODUCCIÓN (Fly.io): Llamamos a la máquina LOCAL via ngrok
        try {
            // Si no tienes configurada la URL de ngrok, usa el fallback
            if (empty($this->ngrokUrl)) {
                return $this->fallbackToAllCameras();
            }

            // Hacemos una petición a tu endpoint LOCAL via ngrok
            $response = Http::timeout(10)->get($this->ngrokUrl . '/api/hikcentral/status');
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return $this->fallbackToAllCameras();
            }
        } catch (\Exception $e) {
            // Si hay error (ngrok apagado, etc.), devolvemos todas las cámaras como activas
            return $this->fallbackToAllCameras();
        }
    }

    private function checkCamerasLocally()
    {
        $filePath = storage_path('app/cameras.csv');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Archivo de cámaras no encontrado.'], 404);
        }

        // Leer todas las filas del CSV
        $csvData = [];
        $file = fopen($filePath, 'r');
        $csvData[] = fgetcsv($file); // Cabecera

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
                // Agregar la fila con el estado OFFLINE (o mantener el estado existente)
                $csvData[] = array_merge([$alias, $ip, $port], isset($row[3]) ? [$row[3]] : ['OFFLINE']);
                continue;
            }

            // Chequeamos el socket para ver si está activa
            $socket = @fsockopen($ip, $port, $errno, $errstr, 1.0);
            $estado = 'OFFLINE';
            if ($socket) {
                fclose($socket);
                $estado = 'ONLINE';
                $activeCameras[] = [
                    'nombre' => $alias,
                    'ip' => $ip,
                    'puerto' => $port,
                    'estado' => 'ONLINE'
                ];
            }

            // Agregar la fila con el estado actualizado
            $csvData[] = [$alias, $ip, $port, $estado];
        }
        fclose($file);

        // Escribir el CSV actualizado
        $file = fopen($filePath, 'w');
        foreach ($csvData as $dataRow) {
            fputcsv($file, $dataRow);
        }
        fclose($file);

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

    private function fallbackToAllCameras()
    {
        $filePath = storage_path('app/cameras.csv');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Archivo de cámaras no encontrado.'], 404);
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Omitir la cabecera del CSV

        $todasLasCamaras = [];
        $totalAnalizados = 0;
        $totalOmitidos = 0;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (empty($row) || count($row) < 3) continue;

            $alias = trim(str_replace("\t", "", $row[0]));
            $ip = trim(str_replace("\t", "", $row[1]));
            $port = intval(trim(str_replace("\t", "", $row[2])));

            if (empty($ip) || $ip === 'Device Address') continue;

            $totalAnalizados++;

            $esOmitido = (stripos($alias, 'LPR') !== false) || (stripos($alias, 'Control de Acceso') !== false);
            if ($esOmitido) {
                $totalOmitidos++;
                continue;
            }

            // Si el CSV ya tiene estado, lo usamos; si no, usamos ONLINE por defecto
            $estado = isset($row[3]) ? $row[3] : 'ONLINE';

            $todasLasCamaras[] = [
                'nombre' => $alias,
                'ip' => $ip,
                'puerto' => $port,
                'estado' => $estado
            ];
        }
        fclose($file);

        return response()->json([
            'resumen' => [
                'total_csv' => $totalAnalizados,
                'omitidos' => $totalOmitidos,
                'activos_validos' => count($todasLasCamaras),
                'inactivos' => 0
            ],
            'cameras' => $todasLasCamaras
        ]);
    }

    // Método legacy mantenido para compatibilidad
    public function getDashboardStats()
    {
        return $this->getStatus();
    }
}
