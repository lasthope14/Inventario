<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', 'AIzaSyARvq9AXlzFcPXeTIgnTXFCuDdAIK43a5w');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent";
    }

    public function generateQuery($userInput)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->endpoint . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $this->buildPrompt($userInput)
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 2048,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $this->parseResponse($response->json());
            }

            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Gemini Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function buildPrompt($userInput)
    {
        return "Actúa como un experto en bases de datos de sistemas de inventario. 
    Analiza esta solicitud del usuario: \"{$userInput}\"
    
    Estructura de la base de datos:
    - inventarios (id, codigo_unico, categoria_id, nombre, marca, modelo, ubicacion_id, estado)
    - categorias (id, nombre, prefijo)
    - ubicaciones (id, nombre)
    - inventario_ubicaciones (id, inventario_id, ubicacion_id, cantidad, estado)
    
    Estados posibles en inventario_ubicaciones: disponible, en uso, en mantenimiento, dado de baja, robado
    
    Para realizar las búsquedas:
    1. Usa inventario_ubicaciones para obtener cantidades y estados actuales
    2. Une con ubicaciones para saber dónde están los items
    3. Une con categorias para filtrar por tipo de item
    4. Incluye siempre los campos más relevantes: codigo_unico, nombre, marca, modelo
    5. Suma las cantidades cuando sea necesario agrupar
    
    IMPORTANTE:
    - Usa LEFT JOIN para las uniones entre tablas
    - Usa alias cortos: i para inventarios, iu para inventario_ubicaciones, u para ubicaciones, c para categorias
    - Si hay búsqueda por texto, usa LOWER() y LIKE
    - Agrupa por inventario_id cuando sumes cantidades
    - Filtra por estado usando inventario_ubicaciones.estado
    
    Responde solo en este formato JSON:
    {
        \"sql\": \"LA CONSULTA SQL AQUÍ\",
        \"explanation\": \"EXPLICACIÓN AMIGABLE AQUÍ\",
        \"suggestion\": \"SUGERENCIA DE REFINAMIENTO AQUÍ (opcional)\"
    }";
    }

    protected function parseResponse($result)
    {
        try {
            if (!empty($result['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $result['candidates'][0]['content']['parts'][0]['text'];
                // Extraer el JSON de la respuesta
                if (preg_match('/{.*}/s', $text, $matches)) {
                    $parsed = json_decode($matches[0], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $parsed;
                    }
                }
            }
            Log::error('Invalid Gemini Response Format', [
                'response' => $result
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error parsing Gemini response', [
                'error' => $e->getMessage(),
                'response' => $result
            ]);
            return null;
        }
    }
}