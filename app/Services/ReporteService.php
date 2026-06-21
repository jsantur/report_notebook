<?php

namespace App\Services;

use App\Models\Reporte;
use App\Models\Asignacion;
use App\Models\AsignacionTemp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReporteService
{
    /**
     * Guarda un reporte nuevo y procesa sus asignaciones en una transacción.
     *
     * @param array $data Los datos validados del request.
     * @param string $turno El turno calculado en el backend.
     * @return Reporte
     */
    public function createReporte(array $data, string $turno): Reporte
    {
        $data['turno'] = $turno;

        $userId = Auth::id();
        $targetUserId = $userId;
        
        if (Auth::user()->role === 'admin' && session()->has('admin_monitoring_user_id')) {
            $targetUserId = session('admin_monitoring_user_id');
        }
        $data['user_id'] = $targetUserId;

        return DB::transaction(function () use ($data, $targetUserId) {
            $reporte = Reporte::create($data);

            // 1. PRIORIDAD: Verificar si hay unidades en borrador (asignaciones_temp)
            $unidadesBorrador = AsignacionTemp::where('user_id', $targetUserId)
                ->where('tipo', 'vehicular')
                ->get();

            if ($unidadesBorrador->isNotEmpty()) {
                // 2. Si hay borrador, mover unidades a asignaciones reales
                foreach ($unidadesBorrador as $temp) {
                    Asignacion::create([
                        'reporte_id' => $reporte->id,
                        'unidad_id' => $temp->unidad_id,
                        'tipo' => 'vehicular',
                        'subtipo' => $temp->subtipo,
                        'placa' => $temp->placa,
                        'sector' => $temp->sector,
                        'turnos' => $temp->turnos,
                        'jurisdiccion' => $temp->jurisdiccion,
                        'km' => $temp->km,
                        'ap' => $temp->ap,
                        'po' => $temp->po,
                        'cod_po' => $temp->cod_po,
                    ]);
                }
                // 3. Limpiar borrador después de mover
                AsignacionTemp::where('user_id', $targetUserId)->delete();
            } else {
                // 4. Si no hay borrador, procesar desde el formulario (fallback)
                if (!empty($data['distribucion_personal_campo'])) {
                    $personalCampo = is_string($data['distribucion_personal_campo']) ? json_decode($data['distribucion_personal_campo'], true) : ($data['distribucion_personal_campo'] ?? []);
                    
                    if (is_array($personalCampo)) {
                        foreach ($personalCampo as $item) {
                            if (isset($item['tipo_patrullaje']) && $item['tipo_patrullaje'] === 'Vehicular') {
                                Asignacion::create([
                                    'reporte_id' => $reporte->id,
                                    'unidad_id' => $item['unidad'] ?? null,
                                    'tipo' => 'vehicular',
                                    'subtipo' => $item['subtipo_vehiculo'] ?? null,
                                    'placa' => $item['matricula'] ?? null,
                                    'km' => 0,
                                    'ap' => 0,
                                    'po' => 0,
                                ]);
                            }
                        }
                    }
                }
            }

            return $reporte;
        });
    }
}
