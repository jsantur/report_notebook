<?php

namespace App\Services;

use App\Models\Reporte;
use App\Models\Asignacion;
use App\Models\AsignacionTemp;
use Illuminate\Support\Facades\DB;
use App\Models\ReporteDraft;

class KilometrajeService
{
    /**
     * Guarda los kilometrajes en AsignacionTemp y en Asignacion si corresponde.
     * Luego sincroniza con el borrador maestro.
     */
    public function saveKilometrajes(int $userId, array $reportes): string
    {
        $unidadesBorrador = [];
        $unidadesReporte = [];

        foreach ($reportes as $item) {
            if (isset($item['is_draft']) && $item['is_draft']) {
                $unidadesBorrador[] = $item;
            } else {
                $unidadesReporte[] = $item;
            }
        }

        DB::transaction(function () use ($userId, $unidadesBorrador, $unidadesReporte) {
            foreach ($unidadesBorrador as $item) {
                AsignacionTemp::where('id', $item['id'])
                    ->where('user_id', $userId)
                    ->update([
                        'km' => $item['km'],
                        'ap' => $item['ap'],
                        'po' => $item['po'],
                        'turnos' => $item['turnos'] ?? null,
                        'jurisdiccion' => $item['jurisdiccion'] ?? null,
                    ]);
            }

            if (count($unidadesReporte) > 0) {
                // BUG FIX: Reporte::latest()->first() -> Reporte::where('user_id', $userId)->latest()->first()
                $reporte = Reporte::where('user_id', $userId)->latest()->first();
                
                if ($reporte) {
                    foreach ($unidadesReporte as $item) {
                        Asignacion::where('id', $item['id'])
                            ->where('reporte_id', $reporte->id)
                            ->where('tipo', 'vehicular')
                            ->update([
                                'km' => $item['km'],
                                'ap' => $item['ap'],
                                'po' => $item['po'],
                            ]);
                    }
                }
            }
        });

        ReporteDraft::syncKilometrajes($userId);

        if (count($unidadesBorrador) > 0 && count($unidadesReporte) > 0) {
            return 'Kilometrajes guardados en borrador y reporte';
        } elseif (count($unidadesBorrador) > 0) {
            return 'Kilometrajes guardados en borrador (sincronizará con reporte al guardar)';
        }

        return 'Kilometrajes guardados correctamente';
    }
}
