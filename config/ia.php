<?php

/*
|--------------------------------------------------------------------------
| Asistente de IA de TesisFácil
|--------------------------------------------------------------------------
| La clave va SOLO en el .env (ANTHROPIC_API_KEY). Nunca en el código.
| Los precios sirven para calcular cuánto gasta cada uso (USD por millón de tokens).
*/

return [

    'api_key' => env('ANTHROPIC_API_KEY'),

    'modelos' => [
        // Revisiones rápidas y baratas (matriz, capítulos)
        'rapido'   => env('IA_MODELO_RAPIDO', 'claude-haiku-5-5'),
        // Tareas donde la calidad pesa más (simulador de sustentación)
        'avanzado' => env('IA_MODELO_AVANZADO', 'claude-sonnet-5-5'),
    ],

    'precios' => [
        'claude-haiku-5-5'  => ['entrada' => 0.10, 'salida' => 0.50],
        'claude-sonnet-5-5' => ['entrada' => 2.00, 'salida' => 10.00],
    ],

    // Usos de IA al mes durante la prueba gratuita
    'trial_usos' => 3,

];
