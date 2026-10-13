<?php

namespace App\Services\Apa;

/**
 * Citas en el texto según ISO 690, en sus dos sistemas:
 * - autor-fecha: (Pérez, 2020), (Pérez y García, 2020, p. 15), Pérez (2020)
 * - numérico: [1], [2, 5], [3-6] o (1), (2, 5)
 * Solo se revisa el cuerpo del documento (lo que está antes de las referencias).
 */
class IsoCitationValidator
{
    // Palabras con mayúscula que suelen ir antes de un año entre paréntesis sin ser autores
    protected const NO_AUTORES = [
        'en', 'el', 'la', 'los', 'las', 'del', 'de', 'segun', 'ano', 'anos', 'periodo', 'peru', 'lima',
        'censo', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre',
        'septiembre', 'octubre', 'noviembre', 'diciembre', 'tabla', 'figura', 'grafico', 'anexo', 'desde', 'hasta',
    ];

    public function validar(array $parrafos, string $edicion): array
    {
        $reglas = config("apa_rules.{$edicion}");
        $texto = implode("\n", TextoReferencias::cuerpo($parrafos));

        $numericas = $this->extraerNumericas($texto);
        $autorFecha = $this->extraerAutorFecha($texto);

        $resultado = ($reglas['sistema_citas'] ?? 'autor_fecha') === 'numerico'
            ? $this->validarNumerico($numericas, $autorFecha)
            : $this->validarAutorFecha($autorFecha, $numericas, $reglas);

        $errores = collect($resultado['checks'])->where('nivel', 'error')->count();
        $advertencias = collect($resultado['checks'])->where('nivel', 'advertencia')->count();

        return [
            'edicion' => $edicion,
            'sistema' => $reglas['sistema_citas'] ?? 'autor_fecha',
            'total_citas_detectadas' => count($resultado['citas']),
            'checks' => $resultado['checks'],
            'citas' => $resultado['citas'], // para el cruce con referencias
            'resumen' => [
                'errores' => $errores,
                'advertencias' => $advertencias,
                'aprobado' => $errores === 0,
            ],
        ];
    }

    /* ---------------------------------------------------------------
     | Sistema autor-fecha
     --------------------------------------------------------------- */

    protected function validarAutorFecha(array $citas, array $numericas, array $reglas): array
    {
        $checks = [];
        $etAlDesde = $reglas['et_al_desde'] ?? 4;

        if (empty($citas)) {
            $checks[] = $this->check(
                'Citas autor-fecha',
                'advertencia',
                'No se detectaron citas del tipo (Pérez, 2020) o Pérez (2020) en el texto. Si tu universidad usa citas numeradas [1], elige "ISO 690 numérico".'
            );
        }

        // Solo corchetes: los números entre paréntesis suelen ser enumeraciones (1), (2)
        $corchetes = array_filter($numericas['citas'], fn ($c) => $c['estilo'] === 'corchetes');

        if (count($corchetes) > 0) {
            $ejemplos = implode(', ', array_slice(array_unique(array_column($corchetes, 'texto_original')), 0, 5));
            $checks[] = $this->check(
                'Sistema de citas mezclado',
                'advertencia',
                "Además de citas autor-fecha hay citas numéricas ({$ejemplos}). ISO 690 pide usar un solo sistema en todo el documento."
            );
        }

        foreach ($citas as $cita) {
            if (str_contains($cita['autores_texto'], '&')) {
                $checks[] = $this->checkCita(
                    $cita['texto_original'],
                    'advertencia',
                    "En ISO 690 en español se une a los autores con \"y\", no con \"&\": \"{$cita['texto_original']}\"."
                );
                continue;
            }

            if (! $cita['tiene_et_al'] && $cita['num_autores'] >= $etAlDesde) {
                $checks[] = $this->checkCita(
                    $cita['texto_original'],
                    'advertencia',
                    "Con más de tres autores, ISO 690 permite citar solo al primero seguido de \"et al.\": \"{$cita['texto_original']}\"."
                );
                continue;
            }

            $checks[] = $this->checkCita($cita['texto_original'], 'ok', "Cita correcta: \"{$cita['texto_original']}\".");
        }

        return ['checks' => $checks, 'citas' => $citas];
    }

    protected function extraerAutorFecha(string $texto): array
    {
        $citas = [];
        $anio = '(\d{4}[a-z]?|s\.\s?f\.)';
        $pagina = '(?:\s*,\s*(?:p|pp)\.\s*[\d\-–]+)?';

        // Entre paréntesis, con varias citas separadas por ";": (Pérez, 2020; García y Ruiz, 2019)
        preg_match_all('/\(([^()]{3,300})\)/u', $texto, $grupos, PREG_OFFSET_CAPTURE);

        foreach ($grupos[1] as [$contenido, $offset]) {
            foreach (explode(';', $contenido) as $parte) {
                if (preg_match('/^\s*(?:(?:v[eé]ase|ver|cf\.)\s+)?([A-ZÁÉÍÓÚÑ][^\d;]{1,150}?),?\s*' . $anio . $pagina . '\s*$/u', $parte, $m)) {
                    $citas[] = $this->armarCita('parentesis', '(' . trim($parte) . ')', $m[1], $m[2], $offset);
                }
            }
        }

        // Narrativas: Pérez (2020), Pérez y García (2020), Pérez et al. (2020)
        preg_match_all(
            '/(?<![\p{L}])([A-ZÁÉÍÓÚÑ][\p{L}\-]+(?:\s+(?:y|e|&)\s+[A-ZÁÉÍÓÚÑ][\p{L}\-]+|\s+et\s+al\.?)?)\s+\(' . $anio . $pagina . '\)/u',
            $texto,
            $narrativas,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        foreach ($narrativas as $m) {
            $primeraPalabra = TextoReferencias::normalizar(preg_split('/\s+/u', $m[1][0])[0]);

            if (in_array($primeraPalabra, self::NO_AUTORES, true)) {
                continue;
            }

            $citas[] = $this->armarCita('narrativa', $m[0][0], $m[1][0], $m[2][0], $m[0][1]);
        }

        usort($citas, fn ($a, $b) => $a['posicion'] <=> $b['posicion']);

        return $citas;
    }

    protected function armarCita(string $tipo, string $original, string $autores, string $anio, int $posicion): array
    {
        $autores = trim(rtrim(trim($autores), ','));
        $tieneEtAl = (bool) preg_match('/\bet\s+al\b/iu', $autores);
        $partes = array_values(array_filter(
            array_map('trim', preg_split('/,|\s+y\s+|\s+e\s+|&|\bet\s+al\.?/u', $autores)),
            fn ($p) => $p !== ''
        ));

        return [
            'tipo' => $tipo,
            'texto_original' => preg_replace('/\s+/u', ' ', $original),
            'autores_texto' => $autores,
            'anio' => preg_replace('/\s+/u', '', $anio),
            'firma_autor' => TextoReferencias::normalizar($partes[0] ?? $autores),
            'num_autores' => $tieneEtAl ? 99 : max(1, count($partes)),
            'tiene_et_al' => $tieneEtAl,
            'posicion' => $posicion,
        ];
    }

    /* ---------------------------------------------------------------
     | Sistema numérico
     --------------------------------------------------------------- */

    protected function validarNumerico(array $numericas, array $autorFecha): array
    {
        $checks = [];
        $citas = $numericas['citas'];

        if (empty($citas)) {
            $checks[] = $this->check(
                'Citas numéricas',
                'advertencia',
                'No se detectaron citas numéricas como [1] o (1). Si usas números en superíndice, el revisor todavía no los detecta: revisa a mano que sigan el orden de aparición.'
            );

            return ['checks' => $checks, 'citas' => $citas];
        }

        $checks[] = $this->check(
            'Citas numéricas',
            'ok',
            count($citas) . ' citas numéricas detectadas con ' . ($numericas['estilo'] === 'corchetes' ? 'corchetes [1]' : 'paréntesis (1)') . '.'
        );

        if ($numericas['mezcla']) {
            $checks[] = $this->check(
                'Corchetes y paréntesis',
                'advertencia',
                'Se usan corchetes [1] y paréntesis (1) a la vez. Usa una sola forma en todo el documento. Se revisaron las citas con corchetes.'
            );
        }

        // El primer número citado debe ser 1, luego 2, luego 3...
        $mayor = 0;
        $vistos = [];
        $desorden = [];

        foreach ($citas as $cita) {
            foreach ($cita['numeros'] as $n) {
                if (isset($vistos[$n])) {
                    continue;
                }

                // Un número nuevo mayor al siguiente esperado: se saltó uno
                if ($n > $mayor + 1) {
                    $desorden[] = "{$cita['texto_original']} se cita antes que [" . ($mayor + 1) . ']';
                }

                $vistos[$n] = true;
                $mayor = max($mayor, $n);
            }
        }

        $checks[] = empty($desorden)
            ? $this->check('Orden de numeración', 'ok', 'Las fuentes se numeran en el orden en que se citan por primera vez.')
            : $this->check(
                'Orden de numeración',
                'error',
                'En el sistema numérico cada fuente recibe su número la primera vez que se cita (1, 2, 3...). Revisa: ' . implode('; ', array_slice($desorden, 0, 5)) . '.'
            );

        if (count($autorFecha) > 0) {
            $ejemplos = implode(', ', array_slice(array_unique(array_column($autorFecha, 'texto_original')), 0, 5));
            $checks[] = $this->check(
                'Sistema de citas mezclado',
                'advertencia',
                "Además de citas numéricas hay citas autor-fecha ({$ejemplos}). ISO 690 pide usar un solo sistema en todo el documento."
            );
        }

        return ['checks' => $checks, 'citas' => $citas];
    }

    protected function extraerNumericas(string $texto): array
    {
        $lista = '(\d{1,3}(?:\s*[,\-–]\s*\d{1,3})*)';

        $corchetes = $this->buscarNumericas('/\[' . $lista . '\]/u', $texto, 'corchetes');
        $parentesis = $this->buscarNumericas('/\(' . $lista . '\)/u', $texto, 'parentesis');

        // Los paréntesis con números también se usan para enumerar: si hay corchetes, mandan los corchetes
        if (count($corchetes) > 0) {
            return ['citas' => $corchetes, 'estilo' => 'corchetes', 'mezcla' => count($parentesis) > 0];
        }

        return ['citas' => $parentesis, 'estilo' => 'parentesis', 'mezcla' => false];
    }

    protected function buscarNumericas(string $patron, string $texto, string $estilo): array
    {
        preg_match_all($patron, $texto, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $citas = [];

        foreach ($matches as $m) {
            $numeros = $this->expandir($m[1][0]);

            if (empty($numeros)) {
                continue;
            }

            $citas[] = [
                'tipo' => 'numerica',
                'estilo' => $estilo,
                'texto_original' => $m[0][0],
                'numeros' => $numeros,
                'posicion' => $m[0][1],
            ];
        }

        return $citas;
    }

    /** "2, 5-7" => [2, 5, 6, 7] */
    protected function expandir(string $lista): array
    {
        $numeros = [];

        foreach (preg_split('/\s*,\s*/u', trim($lista)) as $parte) {
            if (preg_match('/^(\d+)\s*[\-–]\s*(\d+)$/u', $parte, $r)) {
                [$desde, $hasta] = [(int) $r[1], (int) $r[2]];

                if ($hasta >= $desde && $hasta - $desde <= 50) {
                    $numeros = array_merge($numeros, range($desde, $hasta));
                }
            } elseif (ctype_digit($parte)) {
                $numeros[] = (int) $parte;
            }
        }

        return array_values(array_filter($numeros, fn ($n) => $n > 0));
    }

    protected function checkCita(string $cita, string $nivel, string $mensaje): array
    {
        return [
            'cita' => $cita,
            'nivel' => $nivel,
            'mensaje' => $mensaje,
        ];
    }

    protected function check(string $nombre, string $nivel, string $mensaje): array
    {
        return [
            'nombre' => $nombre,
            'nivel' => $nivel,
            'mensaje' => $mensaje,
        ];
    }
}
