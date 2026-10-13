<?php

namespace App\Services;

/**
 * Revisa la coherencia de una matriz de consistencia.
 * La IA NO redacta la tesis: solo devuelve observaciones para que el tesista corrija.
 */
class RevisorMatriz
{
    public function __construct(private AsistenteIa $ia)
    {
    }

    /** Separa un texto en líneas no vacías (un elemento por línea). */
    public static function lineas(?string $texto): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $texto))
            ->map(fn ($l) => trim(preg_replace('/^\s*(\d+[\.\)\-]|[-•*])\s*/u', '', $l)))
            ->filter()
            ->values()
            ->all();
    }

    public function revisar(array $m): array
    {
        return $this->ia->consultarConFormato(
            config('ia.modelos.rapido'),
            $this->instrucciones(),
            $this->mensaje($m),
            $this->herramienta(),
            3000
        );
    }

    private function instrucciones(): string
    {
        return <<<'TXT'
Eres un asesor metodológico de tesis de universidades peruanas. Revisas la matriz de consistencia que te envía un tesista y le devuelves observaciones claras para que ÉL la corrija.

Reglas:
- No redactes la tesis ni reescribas la matriz. En "como_corregir" explica qué cambiar y por qué. Solo cuando ayude, da un ejemplo corto de estructura (una oración como máximo).
- Revisa:
  1. Correspondencia uno a uno: cada problema específico debe tener su objetivo específico y, en enfoque cuantitativo o mixto, su hipótesis específica.
  2. Que el objetivo general responda al problema general y que los específicos desagreguen el general.
  3. Que los objetivos empiecen con un verbo en infinitivo y sean alcanzables y verificables (evitar "conocer", "entender", "saber").
  4. Que las hipótesis sean afirmaciones verificables, coherentes con el nivel de la investigación (relación, efecto, influencia, diferencia) y con las variables.
  5. Que las variables o categorías aparezcan con el mismo nombre en problemas, objetivos e hipótesis.
  6. Que el problema general delimite población o unidad de estudio, lugar y tiempo.
  7. En enfoque cualitativo las hipótesis no son obligatorias: no lo marques como error.
- Severidad: "alta" rompe la coherencia de la matriz; "media" la debilita; "baja" es de forma o redacción.
- El puntaje va de 0 a 100 según la coherencia global.
- Si un campo está vacío o el texto no parece una matriz de tesis, dilo en las observaciones.
- Escribe en español, claro y directo, tratando al tesista de "tú". Frases cortas.
TXT;
    }

    private function mensaje(array $m): string
    {
        $numerar = function (string $prefijo, ?string $texto): string {
            $lineas = self::lineas($texto);
            if (! $lineas) {
                return "(vacío)";
            }
            return collect($lineas)->map(fn ($l, $i) => "{$prefijo}" . ($i + 1) . ": {$l}")->implode("\n");
        };

        return implode("\n\n", [
            'Título: ' . ($m['titulo'] ?: '(sin título)'),
            'Enfoque: ' . $m['enfoque'],
            "PROBLEMA GENERAL:\n" . $m['problema_general'],
            "PROBLEMAS ESPECÍFICOS:\n" . $numerar('PE', $m['problemas_especificos']),
            "OBJETIVO GENERAL:\n" . $m['objetivo_general'],
            "OBJETIVOS ESPECÍFICOS:\n" . $numerar('OE', $m['objetivos_especificos']),
            "HIPÓTESIS GENERAL:\n" . ($m['hipotesis_general'] ?: '(vacío)'),
            "HIPÓTESIS ESPECÍFICAS:\n" . $numerar('HE', $m['hipotesis_especificas'] ?? null),
            "VARIABLES O CATEGORÍAS:\n" . ($m['variables'] ?: '(vacío)'),
        ]);
    }

    private function herramienta(): array
    {
        return [
            'name'         => 'registrar_revision',
            'description'  => 'Registra la revisión de la matriz de consistencia.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'puntaje'    => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                    'veredicto'  => ['type' => 'string', 'enum' => ['consistente', 'requiere_ajustes', 'inconsistente']],
                    'resumen'    => ['type' => 'string', 'description' => 'Dos o tres oraciones con el diagnóstico general.'],
                    'alineacion' => [
                        'type'        => 'array',
                        'description' => 'Una fila por cada problema específico.',
                        'items'       => [
                            'type'       => 'object',
                            'properties' => [
                                'numero'       => ['type' => 'integer'],
                                'objetivo_ok'  => ['type' => 'string', 'enum' => ['si', 'no'], 'description' => 'Tiene un objetivo específico que le corresponde.'],
                                'hipotesis_ok' => ['type' => 'string', 'enum' => ['si', 'no', 'no_aplica'], 'description' => 'Tiene hipótesis que le corresponde; no_aplica si el enfoque no requiere hipótesis.'],
                                'comentario'   => ['type' => 'string'],
                            ],
                            'required' => ['numero', 'objetivo_ok', 'hipotesis_ok', 'comentario'],
                        ],
                    ],
                    'observaciones' => [
                        'type'  => 'array',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'elemento'      => ['type' => 'string', 'description' => 'Ej.: "Objetivo específico 2", "Hipótesis general".'],
                                'severidad'     => ['type' => 'string', 'enum' => ['alta', 'media', 'baja']],
                                'problema'      => ['type' => 'string'],
                                'como_corregir' => ['type' => 'string'],
                            ],
                            'required' => ['elemento', 'severidad', 'problema', 'como_corregir'],
                        ],
                    ],
                    'fortalezas' => ['type' => 'array', 'items' => ['type' => 'string']],
                ],
                'required' => ['puntaje', 'veredicto', 'resumen', 'alineacion', 'observaciones', 'fortalezas'],
            ],
        ];
    }
}
