<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DniController extends Controller
{
    /**
     * Consulta los datos de una persona por DNI a través de Perú API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consultar(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8',
        ]);

        $dni = $request->input('dni');
        $apiKey = config('app.decolecta_api_key') ?? env('DECOLECTA_API_KEY');
        $endpoint = "https://api.decolecta.com/v1/reniec/dni?numero={$dni}";

        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$apiKey}",
                ])
                ->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['first_name'])) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'nombres' => $data['first_name'],
                            'apellido_paterno' => $data['first_last_name'],
                            'apellido_materno' => $data['second_last_name'],
                            'nombre_completo' => $data['full_name'],
                        ]
                    ]);
                }
            }

            return response()->json([
                'success' => false, 
                'message' => 'No se encontraron datos para el DNI consultado o la API no respondió correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error consultando DNI {$dni}: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al conectar con el servicio de consulta.'
            ], 500);
        }
    }
}
