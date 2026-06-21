<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
        public function index()
    {
        $config = \App\Models\Setting::where('key', 'shift_configuration')->first();
        $settings = $config ? json_decode($config->value, true) : [];
        
        return view('configuracion.index', compact('settings'));
    }

        public function update(Request $request)
    {
        $config = \App\Models\Setting::where('key', 'shift_configuration')->first();
        if ($config) {
            $settings = $request->settings;
            
            // Procesar cada turno para convertir el string de notificaciones en array con validación
            foreach (['DIA', 'TARDE', 'NOCHE'] as $turno) {
                if (isset($settings[$turno]['notifications'])) {
                    // Separar por comas y limpiar espacios
                    $times = array_map('trim', explode(',', $settings[$turno]['notifications']));
                    
                    // Validar formato HH:MM (24 horas)
                    $validTimes = array_filter($times, function($time) {
                        return preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $time);
                    });

                    // Ordenar cronológicamente para que las alertas salgan en orden
                    sort($validTimes);
                    
                    $settings[$turno]['notifications'] = array_values($validTimes);
                }
            }

            $config->update(['value' => json_encode($settings)]);
        }

        return redirect()->back()->with('status', 'Configuración actualizada correctamente.');
    }
}
