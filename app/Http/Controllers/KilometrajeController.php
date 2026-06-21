<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Reporte;
use App\Models\Asignacion;
use App\Models\AsignacionTemp;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\HasMonitoringSession;
use App\Http\Requests\StoreKilometrajeRequest;
use App\Services\KilometrajeService;

class KilometrajeController extends Controller
{
    use HasMonitoringSession;

    protected $kilometrajeService;

    public function __construct(KilometrajeService $kilometrajeService)
    {
        $this->kilometrajeService = $kilometrajeService;
    }
    public function index()
    {
        $fechaActual = Carbon::now()->toDateString();
        $horaActual = Carbon::now()->toTimeString();
        $turno = $this->getTurno();
        $userId = $this->getTargetUserId();

        // Solo mostrar unidades del borrador actual del usuario
        $unidades = AsignacionTemp::where('user_id', $userId)
            ->where('tipo', 'vehicular')
            ->get()
            ->map(function($asig) {
                return [
                    'id' => $asig->id,
                    'nombre' => $asig->unidad_id,
                    'placa' => $asig->placa,
                    'tipo' => strtoupper($asig->subtipo ?? 'HALCON'),
                    'conductor' => $asig->conductor,
                    'sector' => $asig->sector,
                    'turnos' => $asig->turnos,
                    'jurisdiccion' => $asig->jurisdiccion,
                    'km' => (float)$asig->km <= 0 ? null : (float)$asig->km,
                    'ap' => (int)$asig->ap <= 0 ? null : (int)$asig->ap,
                    'po' => (int)$asig->po <= 0 ? null : (int)$asig->po,
                    'selected' => true,
                    'is_draft' => true
                ];
            });

        $reporte = null; // No hay reporte guardado, solo borrador
        $desdeBorrador = true;

        // Totales de flota - Cacheado por 60 minutos para optimizar escalabilidad
        $totalFlotaCamionetas = Cache::remember('total_flota_camionetas', 3600, function () {
            return \App\Models\Vehiculo::where('tipo', 'CAMIONETA')->count();
        });
        
        $totalFlotaAutos = Cache::remember('total_flota_autos', 3600, function () {
            return \App\Models\Vehiculo::where('tipo', 'AUTO')->count();
        });

        return view('kilometrajes.index', compact(
            'unidades', 
            'fechaActual', 
            'horaActual', 
            'turno', 
            'reporte', 
            'desdeBorrador',
            'totalFlotaCamionetas',
            'totalFlotaAutos'
        ));
    }

    public function store(StoreKilometrajeRequest $request)
    {
        $userId = $this->getTargetUserId();
        $data = $request->validated();

        $mensaje = $this->kilometrajeService->saveKilometrajes($userId, $data['reportes']);

        return response()->json(['message' => $mensaje]);
    }

    /**
     * Obtener último registro de kilometrajes
     */
    public function last()
    {
        $userId = $this->getTargetUserId();
        $reporte = Reporte::where('user_id', $userId)->whereDate('created_at', today())->latest()->first();
        
        if (!$reporte) {
            return response()->json(['unidades' => []]);
        }
        
        $unidades = Asignacion::where('reporte_id', $reporte->id)
            ->where('tipo', 'vehicular')
            ->get()
            ->map(function($asig) {
                return [
                    'id' => $asig->id,
                    'nombre' => $asig->unidad_id,
                    'placa' => $asig->placa,
                    'km' => $asig->km,
                    'ap' => $asig->ap,
                    'po' => $asig->po,
                ];
            });
        
        return response()->json([
            'unidades' => $unidades,
            'fecha' => $reporte->fecha,
            'turno' => $reporte->turno
        ]);
    }

    private function getTurno()
    {
        $hora = Carbon::now()->hour;
        if ($hora >= 6 && $hora < 14) return 'Mañana';
        if ($hora >= 14 && $hora < 22) return 'Tarde';
        return 'Noche';
    }

    // getTargetUserId was extracted to HasMonitoringSession trait
}
