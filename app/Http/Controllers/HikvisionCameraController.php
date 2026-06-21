<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HikvisionCameraController extends Controller
{
    private function getEstadoCamaras()
    {
        $filePath = storage_path('app/cameras.csv');
        $estadoPath = storage_path('app/cameras-status.json');

        if (!file_exists($filePath)) {
            return ['error' => 'Archivo de cámaras no encontrado.'];
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Omitir la cabecera del CSV

        $todasLasCamaras = [];
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

            $todasLasCamaras[] = [
                'nombre' => $alias,
                'ip' => $ip,
                'puerto' => $port
            ];
        }
        fclose($file);

        // 1. Si es LOCAL: Chequeamos sockets y guardamos el estado en JSON
        if (app()->environment('local')) {
            $camarasActivas = [];
            foreach ($todasLasCamaras as $camara) {
                $socket = @fsockopen($camara['ip'], $camara['puerto'], $errno, $errstr, 1.0);
                if ($socket) {
                    fclose($socket);
                    $camarasActivas[] = $camara;
                }
            }

            // Guardamos el estado actualizado en el archivo JSON
            file_put_contents($estadoPath, json_encode([
                'resumen' => [
                    'total_csv' => $totalAnalizados,
                    'omitidos' => $totalOmitidos,
                    'activos_validos' => count($camarasActivas),
                    'inactivos' => ($totalAnalizados - $totalOmitidos - count($camarasActivas))
                ],
                'cameras' => $camarasActivas
            ]));
        }

        // 2. Si el archivo de estado existe, lo leemos (tanto LOCAL como PRODUCCIÓN)
        if (file_exists($estadoPath)) {
            $data = json_decode(file_get_contents($estadoPath), true);
            return $data;
        }

        // 3. Si no hay archivo de estado, devolvemos todas las cámaras como activas (fallback)
        return [
            'resumen' => [
                'total_csv' => $totalAnalizados,
                'omitidos' => $totalOmitidos,
                'activos_validos' => count($todasLasCamaras),
                'inactivos' => 0
            ],
            'cameras' => $todasLasCamaras
        ];
    }

    public function getStatus()
    {
        $data = $this->getEstadoCamaras();
        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], 404);
        }

        // Añadimos el estado ONLINE a todas las cámaras del listado
        foreach ($data['cameras'] as &$camara) {
            $camara['estado'] = 'ONLINE';
        }

        return response()->json($data);
    }

    // Método legacy mantenido para compatibilidad
    public function getDashboardStats()
    {
        return $this->getStatus();
    }
}
