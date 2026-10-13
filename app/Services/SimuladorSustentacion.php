<?php

namespace App\Services;

use App\Models\SimulacionSustentacion;

/**
 * Simulador de sustentación: la IA actúa como jurado.
 * 1) Genera 5 preguntas a partir del resumen de la tesis.
 * 2) Evalúa cada respuesta del tesista (nota 0-20, comentario y consejo).
 * La IA nunca inventa datos de la tesis ni redacta la respuesta por el tesista.
 */
class SimuladorSustentacion
{
    public function __construct(private AsistenteIa $ia)
    {
    }

    public function generarPreguntas(SimulacionSustentacion $s): array
    {
        $tono = $s->tono === 'exigente'
            ? 'Eres un jurado EXIGENTE: preguntas incisivas que buscan debilidades, pides precisión y justificación.'
            : 'Eres un jurado COMPRENSIVO: preguntas claras y directas, como en una sustentación tranquila, pero sin dejar de evaluar.';

        $sistema = <<<TXT
Eres miembro del jurado de una sustentación de tesis en una universidad peruana. {$tono}

Con el resumen de la tesis, formula exactamente 5 preguntas, en este orden:
1. Problema y justificación (por qué es relevante, qué vacío cubre).
2. Objetivos e hipótesis (coherencia entre ellos y con el problema).
3. Metodología (diseño, población y muestra, instrumentos, validez y confiabilidad).
4. Resultados (interpretación, prueba estadística o análisis usado, qué significan).
5. Conclusiones, limitaciones o aportes.

Reglas:
- Trata al tesista de "usted", como en una sustentación real.
- Cada pregunta debe ser concreta y referirse a SU tesis, no genérica.
- Una sola pregunta por ítem (no encadenes varias preguntas en una).
- No inventes datos que no estén en el resumen; si falta información, pregunta por ella.
TXT;

        return $this->ia->consultarConFormato(
            config('ia.modelos.avanzado'),
            $sistema,
            "RESUMEN DE LA TESIS:\n\n" . $s->contexto(),
            [
                'name'         => 'registrar_preguntas',
                'description'  => 'Registra las 5 preguntas del jurado.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'preguntas' => [
                            'type'     => 'array',
                            'minItems' => 5,
                            'maxItems' => 5,
                            'items'    => [
                                'type'       => 'object',
                                'properties' => [
                                    'pregunta'  => ['type' => 'string'],
                                    'que_evalua'=> ['type' => 'string', 'description' => 'Qué aspecto evalúa, en pocas palabras. Ej.: "Metodología: tamaño de muestra".'],
                                ],
                                'required' => ['pregunta', 'que_evalua'],
                            ],
                        ],
                    ],
                    'required' => ['preguntas'],
                ],
            ],
            1500
        );
    }

    public function evaluarRespuesta(SimulacionSustentacion $s, int $indice, string $respuesta): array
    {
        $pregunta = $s->preguntas[$indice] ?? [];
        $exigencia = $s->tono === 'exigente' ? 'Califica con rigor.' : 'Califica con justicia, valorando el esfuerzo pero sin regalar nota.';

        $sistema = <<<TXT
Eres el mismo jurado de la sustentación de tesis (universidad peruana). Evalúas la respuesta del tesista a tu pregunta. {$exigencia}

Reglas:
- Nota en escala vigesimal de 0 a 20.
- "comentario": lo que diría el jurado en voz alta, tratando de "usted", 2 o 3 oraciones.
- "que_falto": puntos concretos que una buena respuesta debió mencionar (máximo 4).
- "consejo": cómo estructurar mejor la respuesta. NO redactes la respuesta por el tesista ni inventes datos de su tesis.
- Si la respuesta está vacía, es evasiva o no tiene relación con la pregunta, la nota debe ser baja y dilo con respeto.
TXT;

        $mensaje = "RESUMEN DE LA TESIS:\n\n" . $s->contexto()
            . "\n\nPREGUNTA DEL JURADO (" . ($pregunta['que_evalua'] ?? '') . "):\n" . ($pregunta['pregunta'] ?? '')
            . "\n\nRESPUESTA DEL TESISTA:\n" . $respuesta;

        return $this->ia->consultarConFormato(
            config('ia.modelos.avanzado'),
            $sistema,
            $mensaje,
            [
                'name'         => 'registrar_evaluacion',
                'description'  => 'Registra la evaluación de la respuesta.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'nota'       => ['type' => 'integer', 'minimum' => 0, 'maximum' => 20],
                        'comentario' => ['type' => 'string'],
                        'que_falto'  => ['type' => 'array', 'items' => ['type' => 'string']],
                        'consejo'    => ['type' => 'string'],
                    ],
                    'required' => ['nota', 'comentario', 'que_falto', 'consejo'],
                ],
            ],
            1000
        );
    }
}
