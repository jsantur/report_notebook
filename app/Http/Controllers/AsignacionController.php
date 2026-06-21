<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function updateCodes(\App\Http\Requests\UpdateAsignacionCodesRequest $request)
    {
        $validated = $request->validated();

        $reporteId = $validated['reporte_id'] ?? null;
        if (!$reporteId && !empty($validated['asignaciones'])) {
            $firstAsig = Asignacion::find($validated['asignaciones'][0]['id']);
            if ($firstAsig) {
                $reporteId = $firstAsig->reporte_id;
            }
        }

        if ($reporteId) {
            $reporte = \App\Models\Reporte::find($reporteId);
            // La validación de permisos fue removida a petición del usuario para permitir
            // que supervisores de otros turnos puedan ingresar los códigos PO del turno anterior.
        }

        foreach ($validated['asignaciones'] as $asigData) {
            Asignacion::where('id', $asigData['id'])->update([
                'cod_po' => $asigData['cod_po']
            ]);
        }

        if ($request->filled('reporte_id') && $request->filled('distribucion_personal_campo')) {
            \App\Models\Reporte::where('id', $request->reporte_id)->update([
                'distribucion_personal_campo' => $request->distribucion_personal_campo
            ]);
        }

        return response()->json(['success' => true]);
    }
}
