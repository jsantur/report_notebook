<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsignacionTemp;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasMonitoringSession;
use App\Http\Requests\StoreDraftUnidadesRequest;
use App\Http\Requests\UpdateKilometrajeDraftRequest;

class DraftController extends Controller
{
    use HasMonitoringSession;
    /**
     * Obtener unidades temporales del usuario actual (o del supervisor activo si es admin)
     */
    public function index()
    {
        $userId = $this->getTargetUserId();
        $unidades = AsignacionTemp::where('user_id', $userId)
            ->where('tipo', 'vehicular')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->unidad_id,
                    'placa' => $item->placa,
                    'tipo' => strtoupper($item->subtipo ?? 'VEHICULO'),
                    'conductor' => $item->conductor,
                    'sector' => $item->sector,
                    'turnos' => $item->turnos,
                    'jurisdiccion' => $item->jurisdiccion,
                    'km' => $item->km,
                    'ap' => $item->ap,
                    'po' => $item->po,
                    'selected' => true,
                    'is_draft' => true
                ];
            });

        return response()->json($unidades);
    }

    /**
     * Guardar/Actualizar unidades temporales
     */
    public function store(StoreDraftUnidadesRequest $request)
    {
        $data = $request->validated();

        $userId = $this->getTargetUserId();

        // Obtener asignaciones actuales para preservar km, ap, po
        $currentAsignaciones = AsignacionTemp::where('user_id', $userId)
            ->where('tipo', 'vehicular')
            ->get()
            ->keyBy('unidad_id');

        // Eliminar unidades temporales anteriores del usuario
        AsignacionTemp::where('user_id', $userId)
            ->where('tipo', 'vehicular')
            ->delete();

        // Crear nuevas unidades temporales
        foreach ($data['unidades'] as $unidad) {
            $km = 0;
            $ap = 0;
            $po = 0;
            $turnos = null;
            $jurisdiccion = null;
            
            if ($currentAsignaciones->has($unidad['unidad_id'])) {
                $old = $currentAsignaciones->get($unidad['unidad_id']);
                $km = $old->km;
                $ap = $old->ap;
                $po = $old->po;
                $turnos = $old->turnos;
                $jurisdiccion = $old->jurisdiccion;
            }

            AsignacionTemp::create([
                'user_id' => $userId,
                'unidad_id' => $unidad['unidad_id'],
                'tipo' => 'vehicular',
                'subtipo' => $unidad['subtipo'] ?? null,
                'placa' => $unidad['placa'] ?? null,
                'conductor' => $unidad['conductor'] ?? null,
                'sector' => $unidad['sector'] ?? null,
                'km' => $km,
                'ap' => $ap,
                'po' => $po,
                'turnos' => $turnos,
                'jurisdiccion' => $jurisdiccion,
            ]);
        }

        return response()->json([
            'message' => 'Unidades sincronizadas correctamente',
            'count' => count($data['unidades'])
        ]);
    }

    /**
     * Actualizar datos de kilometraje en borrador
     */
    public function updateKilometraje(UpdateKilometrajeDraftRequest $request)
    {
        $data = $request->validated();

        $userId = $this->getTargetUserId();

        foreach ($data['unidades'] as $item) {
            AsignacionTemp::where('id', $item['id'])
                ->where('user_id', $userId)
                ->update([
                    'km' => $item['km'] ?? 0,
                    'ap' => $item['ap'] ?? 0,
                    'po' => $item['po'] ?? 0,
                    'turnos' => $item['turnos'] ?? null,
                    'jurisdiccion' => $item['jurisdiccion'] ?? null,
                ]);
        }

        return response()->json(['message' => 'Borrador actualizado']);
    }

    /**
     * Eliminar todas las unidades temporales del usuario
     */
    public function clear()
    {
        $userId = $this->getTargetUserId();
        AsignacionTemp::where('user_id', $userId)->delete();
        return response()->json(['message' => 'Borrador limpiado']);
    }

    /**
     * Mover borrador a asignaciones reales (cuando se guarda el reporte)
     */
    public function moveToAsignaciones($reporteId)
    {
        $userId = $this->getTargetUserId();
        
        $temporales = AsignacionTemp::where('user_id', $userId)
            ->where('tipo', 'vehicular')
            ->get();

        foreach ($temporales as $temp) {
            \App\Models\Asignacion::create([
                'reporte_id' => $reporteId,
                'unidad_id' => $temp->unidad_id,
                'tipo' => 'vehicular',
                'subtipo' => $temp->subtipo,
                'placa' => $temp->placa,
                'km' => $temp->km,
                'ap' => $temp->ap,
                'po' => $temp->po,
            ]);
        }

        // Limpiar temporales después de mover
        AsignacionTemp::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Unidades movidas al reporte']);
    }

    /**
     * Guardar el borrador global del reporte en la base de datos para sincronización en tiempo real
     */
    public function saveReportDraft(Request $request)
    {
        $data = $request->validate([
            'turno' => 'required|string',
            'fecha' => 'required|date',
            'data' => 'required|array',
        ]);

        $userId = $this->getTargetUserId();

        $draft = \App\Models\ReporteDraft::updateOrCreate(
            [
                'user_id' => $userId,
                'turno' => $data['turno'],
                'fecha' => $data['fecha'],
            ],
            [
                'data' => $data['data']
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Borrador global guardado correctamente en la BD',
            'draft_id' => $draft->id,
            'user' => [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name
            ]
        ]);
    }

    /**
     * Obtener borradores activos para sincronización o visualización
     */
    public function getReportDrafts(Request $request)
    {
        $turno = $request->input('turno');
        $fecha = $request->input('fecha');
        $currentUser = Auth::user();

        $query = \App\Models\ReporteDraft::with('user');

        if ($fecha) {
            $query->where('fecha', $fecha);
        }
        if ($turno) {
            $query->where('turno', $turno);
        }

        if ($currentUser->role !== 'admin') {
            $query->where('user_id', $currentUser->id);
        }

        $drafts = $query->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'drafts' => $drafts->map(function ($d) {
                return [
                    'id' => $d->id,
                    'user_id' => $d->user_id,
                    'user_name' => $d->user ? $d->user->name : 'Desconocido',
                    'turno' => $d->turno,
                    'fecha' => $d->fecha,
                    'data' => $d->data,
                    'updated_at' => $d->updated_at->toIso8601String()
                ];
            })
        ]);
    }

    /**
     * Limpiar el borrador global
     */
    public function clearReportDraft(Request $request)
    {
        $turno = $request->input('turno');
        $fecha = $request->input('fecha');
        $userId = $this->getTargetUserId();

        $query = \App\Models\ReporteDraft::where('user_id', $userId);

        if ($fecha) {
            $query->where('fecha', $fecha);
        }
        if ($turno) {
            $query->where('turno', $turno);
        }

        $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Borrador global eliminado de la BD'
        ]);
    }

    // getTargetUserId was extracted to HasMonitoringSession trait

    /**
     * Iniciar sesión de monitoreo para el Admin
     */
    public function startMonitoringSession(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'mode' => 'nullable|string',
            'draft_id' => 'nullable|integer'
        ]);

        if (Auth::user()->role === 'admin') {
            session(['admin_monitoring_user_id' => $request->input('user_id')]);
            if ($request->has('mode')) {
                session(['admin_monitoring_mode' => $request->input('mode')]);
            }
            if ($request->has('draft_id')) {
                session(['admin_monitoring_draft_id' => $request->input('draft_id')]);
            }
            return response()->json([
                'success' => true,
                'monitoring_user_id' => session('admin_monitoring_user_id'),
                'monitoring_mode' => session('admin_monitoring_mode'),
                'monitoring_draft_id' => session('admin_monitoring_draft_id')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
    }

    /**
     * Detener sesión de monitoreo para el Admin
     */
    public function stopMonitoringSession(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            session()->forget(['admin_monitoring_user_id', 'admin_monitoring_mode', 'admin_monitoring_draft_id']);
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
    }
}
