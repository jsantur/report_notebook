<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Reporte;
use App\Models\Serenazgo;
use App\Models\Asignacion;
use App\Models\AsignacionTemp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;
use App\Services\ReporteService;

class ReporteController extends Controller
{
    protected $reporteService;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * Display a listing of reports.
     */
    public function index()
    {
        $reportes = Reporte::with(['supervisorCampo', 'supervisorCamaras', 'asignaciones', 'user'])
            ->latest('fecha')
            ->latest('hora')
            ->paginate(20);

        $users = [];
        if (Auth::check() && Auth::user()->role === 'admin') {
            $users = \App\Models\User::where('activo', true)->orderBy('name')->get();
        }

        return view('reportes.buscar', compact('reportes', 'users'));
    }

    /**
     * Calcula el turno actual basado en la hora.
     */
    private function getTurno()
    {
        $hora = Carbon::now()->hour;

        if ($hora >= 6 && $hora < 14) {
            return 'Mañana';
        } elseif ($hora >= 14 && $hora < 22) {
            return 'Tarde';
        } else {
            return 'Noche';
        }
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        // Filtrado estricto por perfiles solicitados y solo personal activo
        $supervisoresCampo = Serenazgo::whereRaw('LOWER(perfil_trabajo) = ?', [strtolower('Supervisor Encargado')])
            ->where('activo', true)
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres')
            ->get();

        $supervisoresCamaras = Serenazgo::whereRaw('LOWER(perfil_trabajo) = ?', [strtolower('Supervisor de Cámaras')])
            ->where('activo', true)
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres')
            ->get();

        $defaultSupervisorCamaras = $supervisoresCamaras->first(function ($sup) {
            return stripos($sup->nombres, 'ALEXIS') !== false;
        });
        $defaultSupervisorCamarasId = $defaultSupervisorCamaras ? $defaultSupervisorCamaras->id : '';

        $fechaActual = Carbon::now()->toDateString();
        $horaActual = Carbon::now()->toTimeString();
        $turno = $this->getTurno();
        $vehiculos = Vehiculo::where('activo', true)->get();
        $camaras = \App\Models\Camara::where('activa', true)->orderBy('nombre')->get();

        $adminMonitoringUserId = session('admin_monitoring_user_id', null);
        $adminMonitoringMode = session('admin_monitoring_mode', null);
        $adminMonitoringDraftId = session('admin_monitoring_draft_id', null);

        return view('reportes.nuevo', compact('supervisoresCampo', 'supervisoresCamaras', 'fechaActual', 'horaActual', 'turno', 'vehiculos', 'camaras', 'defaultSupervisorCamarasId', 'adminMonitoringUserId', 'adminMonitoringMode', 'adminMonitoringDraftId'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(\App\Http\Requests\StoreReporteRequest $request)
    {
        \Log::info('ReporteController@store request received', [
            'operadores_camaras' => $request->input('operadores_camaras'),
            'personal_campo' => $request->input('personal_campo'),
            'reporte_personal_patrullando' => $request->input('reporte_personal_patrullando'),
            'visualizaciones_resaltantes' => $request->input('visualizaciones_resaltantes'),
        ]);
        
        $validated = $request->validated();

        $validated['supervisor_camaras_id'] = $request->input('supervisores_camaras')[0];
        
        // Mapear los campos JSON a las columnas del modelo Reporte
        $validated['distribucion_personal_camaras'] = $request->input('operadores_camaras');
        $validated['distribucion_personal_campo']   = $request->input('personal_campo');
        $validated['reporte_personal_patrullando']  = $request->input('reporte_personal_patrullando');
        $validated['visualizaciones_resaltantes']   = $request->input('visualizaciones_resaltantes');

        $reporte = $this->reporteService->createReporte($validated, $this->getTurno());
        
        \Log::info('Reporte guardado en BD', ['reporte_id' => $reporte->id, 'reporte_personal_patrullando' => $reporte->reporte_personal_patrullando]);

        return redirect()->route('dashboard')->with('status', 'Reporte guardado exitosamente.');
    }

    /**
     * Generar reporte WhatsApp - solo texto, imagen se genera en frontend con html2canvas
     */
    public function generarReporteWhatsApp(Request $request)
    {
        $validated = $request->validate([
            'fuente' => 'required|in:SIPCOP-M,Wialon',
            'nota' => 'nullable|string|max:500',
            'unidades' => 'required|array',
            'unidades.*.nombre' => 'required|string',
            'unidades.*.placa' => 'nullable|string',
            'unidades.*.tipo' => 'nullable|string',
            'unidades.*.km' => 'nullable|numeric',
            'unidades.*.ap' => 'nullable|numeric',
            'unidades.*.po' => 'nullable|integer',
            'unidades.*.turnos' => 'nullable|array',
        ]);

        $turno = $this->getTurno();
        $hora = Carbon::now()->format('H:i');
        $fecha = Carbon::now()->isoFormat('LL'); // MIÉRCOLES, 29 DE ABRIL DE 2026
        $fuente = $validated['fuente'];
        $nota = $validated['nota'] ?? 'Sin novedades resaltantes.';
        
        // El turno en backend es "Mañana", en BD/frontend es "DIA"
        $turnoStr = strtoupper($turno === 'Mañana' ? 'DIA' : $turno);

        // Filtrar solo las unidades que están operativas en el turno actual
        $unidades = collect($validated['unidades'])->filter(function ($u) use ($turnoStr) {
            $turnos = $u['turnos'] ?? [];
            return in_array($turnoStr, array_map('strtoupper', $turnos));
        });

        // Contar tipos
        $pickups = $unidades->filter(fn($u) => str_contains(strtoupper($u['tipo'] ?? ''), 'CAMIONETA'))->count();
        $autos = $unidades->filter(fn($u) => str_contains(strtoupper($u['tipo'] ?? ''), 'AUTO'))->count();
        $motos = $unidades->count() - $pickups - $autos;

        // Generar texto para WhatsApp
        $texto = "📊 REPORTE DE TURNO - " . strtoupper($turno) . "\n";
        $texto .= "🕒 $hora | 📅 " . Carbon::now()->format('Y-m-d') . "\n\n";
        
        $texto .= "⚙️🚗 Unidades en operación:\n";
        if ($pickups > 0) $texto .= "🔹 🚙 Camionetas [$pickups]\n";
        if ($autos > 0) $texto .= "🔹 🚘 Autos Sedán [$autos]\n";
        if ($motos > 0) $texto .= "🔹 🏍️ Motos [$motos]\n";
        
        $texto .= "\n📡 Fuente: $fuente\n";
        $texto .= "📝 Novedad:\n$nota\n\n";
        
        $texto .= "👤 Registro: " . strtoupper(Auth::user()->name ?? 'SISTEMA');

        return response()->json([
            'success' => true,
            'texto' => $texto,
            'resumen' => [
                'turno' => $turno,
                'hora' => $hora,
                'fecha' => strtoupper($fecha),
                'pickups' => $pickups,
                'autos' => $autos,
                'motos' => $motos,
                'total' => $unidades->count(),
                'fuente' => $fuente,
                'nota' => $nota,
                'usuario' => strtoupper(Auth::user()->name ?? 'SISTEMA')
            ]
        ]);
    }

    /**
     * Remove the specified report from storage.
     */
        public function destroy(Reporte $reporte)
    {
        try {
            DB::transaction(function () use ($reporte) {
                // Eliminar asignaciones relacionadas
                $reporte->asignaciones()->delete();
                // Eliminar el reporte
                $reporte->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Reporte eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar vista de impresión PDF para un reporte.
     */
    public function generarPDF(Reporte $reporte)
    {
        $reporte->load(['supervisorCampo', 'supervisorCamaras', 'asignaciones']);
        
        // Decodificar JSONs para la vista
        $distribucionCamaras = is_string($reporte->distribucion_personal_camaras) ? json_decode($reporte->distribucion_personal_camaras, true) : ($reporte->distribucion_personal_camaras ?? []);
        $distribucionCampo = is_string($reporte->distribucion_personal_campo) ? json_decode($reporte->distribucion_personal_campo, true) : ($reporte->distribucion_personal_campo ?? []);
        $halconReportes = is_string($reporte->reporte_personal_patrullando) ? json_decode($reporte->reporte_personal_patrullando, true) : ($reporte->reporte_personal_patrullando ?? []);
        $visualizacionesIA = is_string($reporte->visualizaciones_resaltantes) ? json_decode($reporte->visualizaciones_resaltantes, true) : ($reporte->visualizaciones_resaltantes ?? []);
        
        return view('reportes.pdf', compact(
            'reporte', 
            'distribucionCamaras', 
            'distribucionCampo', 
            'halconReportes', 
            'visualizacionesIA'
        ));
    }

    /**
     * Generar reporte Excel con múltiples hojas.
     */
    public function generarExcel(Reporte $reporte)
    {
        $fileName = 'Reporte_Turno_' . $reporte->turno . '_' . $reporte->fecha . '.xlsx';
        return Excel::download(new ReporteExport($reporte), $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Reasignar el usuario responsable de un reporte.
     */
    public function reasignarResponsable(Request $request, Reporte $reporte)
    {
        if ($request->user()->cannot('reasignar', $reporte)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para delegar este reporte.'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $reporte->user_id = $request->input('user_id');
        $reporte->save();

        // Cargar la relación para retornar los datos actualizados
        $reporte->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Responsable del reporte reasignado correctamente.',
            'user' => $reporte->user
        ]);
    }
    public function saveUnidadesReportes(Request $request, $id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->unidades_reportes = $request->reportes;
        $reporte->save();
        
        // Actualizar el borrador colaborativo (tabla reporte_drafts) dentro del JSON 'data'
        $draft = \App\Models\ReporteDraft::where('user_id', auth()->id())
            ->where('fecha', $reporte->fecha)
            ->where('turno', $reporte->turno)
            ->first();
            
        if ($draft) {
            $data = $draft->data ?? [];
            $data['unidades_reportes'] = $request->reportes;
            $data['last_modified_by'] = auth()->user()->role;
            $draft->data = $data;
            $draft->save();
        }
        
        return response()->json(['success' => true]);
    }
}
