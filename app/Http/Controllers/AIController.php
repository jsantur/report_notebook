<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    /**
     * Check if Gemini API is configured and accessible
     */
    public function checkStatus()
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            return response()->json([
                'configured' => false,
                'message' => 'Gemini API Key not configured',
                'help' => 'Set GEMINI_API_KEY in your .env file'
            ]);
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->when(app()->isLocal(), fn($req) => $req->withoutVerifying())
              ->timeout(10)
              ->post($url, [
                'contents' => [['parts' => [['text' => 'Hola']]]],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 10]
            ]);

            if ($response->successful()) {
                return response()->json([
                    'configured' => true,
                    'working' => true,
                    'message' => 'Gemini AI is ready',
                    'key_preview' => substr($apiKey, 0, 8) . '...'
                ]);
            } else {
                return response()->json([
                    'configured' => true,
                    'working' => false,
                    'message' => 'API Key configured but request failed',
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'configured' => true,
                'working' => false,
                'message' => 'Connection error',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function correctText(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
        ]);

        $original = $request->input('text');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'corrected_text' => $this->localCorrection($original),
                'is_ai_corrected' => false,
                'error' => 'API Key missing'
            ]);
        }

        try {
            $systemPrompt = "Actúa como un oficial de guardia experto en redacción de reportes de seguridad ciudadana y partes policiales. Tu tarea es corregir la ortografía, gramática y puntuación del texto proporcionado, transformándolo a un lenguaje técnico, formal, objetivo y profesional.
REGLA ESTRICTA: Devuelve ÚNICAMENTE el texto corregido. Sin explicaciones, saludos ni comillas.";
            
            $prompt = $systemPrompt . "\n\nTexto a corregir: " . $original;

            // Usando v1beta como en el ejemplo del usuario
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->when(app()->isLocal(), fn($req) => $req->withoutVerifying())
              ->timeout(20)
              ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.1, 
                    'maxOutputTokens' => 1200,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $corrected = $result['candidates'][0]['content']['parts'][0]['text'] ?? $original;
                $corrected = trim($corrected);
                $corrected = str_replace('"', '', $corrected);

                return response()->json([
                    'corrected_text' => $corrected,
                    'is_ai_corrected' => true
                ]);
            } else {
                $errorBody = $response->body();
                Log::error("Gemini Error ({$response->status()}): " . $errorBody);
                throw new \Exception("Gemini API Error: " . $errorBody);
            }

        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return response()->json([
                'corrected_text' => $this->localCorrection($original),
                'is_ai_corrected' => false,
                'debug_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Corrección básica local (sin IA)
     */
    private function localCorrection(string $text): string
    {
        if (empty(trim($text))) return $text;

        $replacements = [
            'oriundo' => 'se encontraba',
            'vi0' => 'vio',
            'habian' => 'había',
            'estaban' => 'se encontraban',
            'sujeto' => 'individuo',
            'tipo' => 'individuo',
            'coso' => 'objeto',
            'agarro' => 'tomó',
            'tomo' => 'tomó',
            'orinando' => 'miccionando',
            'q' => 'que',
            'x' => 'por',
            'dnde' => 'donde',
            'porq' => 'porque',
        ];

        $corrected = $text;
        foreach ($replacements as $search => $replace) {
            $pattern = '/(?<!\w)' . preg_quote($search, '/') . '(?!\w)/ui';
            $corrected = preg_replace($pattern, $replace, $corrected);
        }

        $redundancies = [
            '/miccionando\s+en\s+la\s+vía\s+pública\s+en\s+la\s+vía\s+pública/ui' => 'miccionando en la vía pública',
            '/en\s+la\s+vía\s+pública\s+en\s+la\s+vía\s+pública/ui' => 'en la vía pública',
        ];
        foreach ($redundancies as $pattern => $replace) {
            $corrected = preg_replace($pattern, $replace, $corrected);
        }

        $corrected = trim($corrected);
        if (!empty($corrected)) {
            $firstChar = mb_substr($corrected, 0, 1);
            $then = mb_substr($corrected, 1);
            $corrected = mb_strtoupper($firstChar) . $then;
        }

        if (!empty($corrected) && !preg_match('/[.!?]$/u', $corrected)) {
            $corrected .= '.';
        }

        $corrected = preg_replace('/\s+/u', ' ', $corrected);

        return $corrected;
    }
}
