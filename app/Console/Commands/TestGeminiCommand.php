<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestGeminiCommand extends Command
{
    protected $signature = 'test:gemini';
    protected $description = 'Test Gemini API connection and configuration';

    public function handle()
    {
        $this->info('🔍 Testing Gemini API Configuration...\n');

        // 1. Check if API key exists
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            $this->error('❌ GEMINI_API_KEY not found in .env');
            $this->line("\n💡 To fix this:");
            $this->line("   1. Get a free API key from: https://aistudio.google.com/app/apikey");
            $this->line("   2. Add to your .env file:");
            $this->line("      GEMINI_API_KEY=your_api_key_here");
            $this->line("   3. For fly.io deployment:");
            $this->line("      fly secrets set GEMINI_API_KEY=your_api_key_here");
            return 1;
        }

        $this->info('✅ GEMINI_API_KEY is configured');
        $this->line("   Key: " . substr($apiKey, 0, 8) . '...' . substr($apiKey, -4) . "\n");

        // 2. Test API connection
        $this->info('🌐 Testing API connection...');
        
        $testText = "se visualiza a un sujeto orinando en la via publica";
        $modelName = 'gemini-2.5-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent";

        $systemPrompt = "Actúa como un oficial de guardia experto en redacción de reportes de seguridad ciudadana. Corrige el texto a lenguaje técnico y formal. REGLA: Devuelve ÚNICAMENTE el texto corregido, sin explicaciones.";
        
        $prompt = $systemPrompt . "\n\nTexto a corregir: " . $testText;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->timeout(15)
              ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1, 
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $correctedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($correctedText) {
                    $this->info('✅ API Connection Successful!\n');
                    $this->line("📝 Test Results:");
                    $this->line("   Original:  \"{$testText}\"");
                    $this->line("   Corrected: \"" . trim($correctedText) . "\"\n");
                    
                    $this->info('🎉 Gemini AI is ready to use!');
                    return 0;
                } else {
                    $this->error('❌ API returned empty response');
                    $this->line("Response: " . json_encode($result, JSON_PRETTY_PRINT));
                    return 1;
                }
            } else {
                $this->error('❌ API Request Failed');
                $this->line("Status: " . $response->status());
                $this->line("Error: " . $response->body());
                
                if ($response->status() === 400) {
                    $this->line("\n💡 API Key might be invalid or quota exceeded");
                } elseif ($response->status() === 404) {
                    $this->line("\n💡 Model not found. Check if 'gemini-1.5-flash-latest' is available in your region");
                }
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Connection Error: ' . $e->getMessage());
            $this->line("\n💡 Check your internet connection");
            return 1;
        }
    }
}
