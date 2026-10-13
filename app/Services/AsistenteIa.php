<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Habla con la API de Claude y devuelve una respuesta estructurada.
 * Se usa una "herramienta" (tool) obligatoria para que la IA responda
 * siempre con el mismo formato JSON, sin texto suelto que haya que adivinar.
 */
class AsistenteIa
{
    private const URL = 'https://api.anthropic.com/v1/messages';

    /**
     * @return array{datos: array, modelo: string, tokens_entrada: int, tokens_salida: int, costo_usd: float}
     */
    public function consultarConFormato(
        string $modelo,
        string $sistema,
        string $mensaje,
        array $herramienta,
        int $maxTokens = 3000
    ): array {
        $clave = config('ia.api_key');

        if (! $clave) {
            throw new RuntimeException('El asistente de IA no está configurado todavía (falta ANTHROPIC_API_KEY en el .env).');
        }

        $respuesta = Http::withHeaders([
                'x-api-key'         => $clave,
                'anthropic-version' => '2023-06-01',
            ])
            ->acceptJson()
            ->timeout(90)
            ->post(self::URL, [
                'model'       => $modelo,
                'max_tokens'  => $maxTokens,
                'system'      => $sistema,
                'tools'       => [$herramienta],
                'tool_choice' => ['type' => 'tool', 'name' => $herramienta['name']],
                'messages'    => [
                    ['role' => 'user', 'content' => $mensaje],
                ],
            ]);

        if ($respuesta->failed()) {
            $detalle = $respuesta->json('error.message') ?? 'sin detalle';
            throw new RuntimeException("La IA no respondió correctamente (código {$respuesta->status()}: {$detalle}).");
        }

        $bloque = collect($respuesta->json('content', []))->firstWhere('type', 'tool_use');

        if (! $bloque || ! is_array($bloque['input'] ?? null)) {
            throw new RuntimeException('La IA respondió en un formato inesperado. Intenta de nuevo.');
        }

        $entrada = (int) $respuesta->json('usage.input_tokens', 0);
        $salida  = (int) $respuesta->json('usage.output_tokens', 0);

        return [
            'datos'          => $bloque['input'],
            'modelo'         => $modelo,
            'tokens_entrada' => $entrada,
            'tokens_salida'  => $salida,
            'costo_usd'      => $this->costo($modelo, $entrada, $salida),
        ];
    }

    /** Costo en dólares según los precios de config/ia.php. */
    public function costo(string $modelo, int $entrada, int $salida): float
    {
        $precio = config('ia.precios')[$modelo] ?? null;

        if (! $precio) {
            return 0.0;
        }

        return round(($entrada * $precio['entrada'] + $salida * $precio['salida']) / 1_000_000, 6);
    }
}
