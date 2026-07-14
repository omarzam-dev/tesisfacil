<?php

namespace App\Services\Apa;

class ApaReferenceValidator
{
    /**
     * $parrafos: lista de párrafos en orden (de ApaTextExtractor::extraerParrafos).
     * $citasDetectadas: lista de citas ya extraídas por ApaCitationValidator
     * (para hacer el cruce cita <-> referencia).
     */
    public function validar(array $parrafos, array $citasDetectadas, string $edicion): array
    {
        $reglas = config("apa_rules.{$edicion}");

        $entradas = $this->extraerEntradasReferencias($parrafos, $reglas);

        $checks = [];

        $checks[] = $this->validarEncabezado($parrafos, $reglas);
        $checks[] = $this->validarOrdenAlfabetico($entradas);
        $checks[] = $this->validarFormatoDoi($entradas, $reglas);
        $checks = array_merge($checks, $this->cruzarCitasYReferencias($citasDetectadas, $entradas));

        $errores = collect($checks)->where('nivel', 'error')->count();
        $advertencias = collect($checks)->where('nivel', 'advertencia')->count();

        return [
            'edicion' => $edicion,
            'total_referencias_detectadas' => count($entradas),
            'checks' => $checks,
            'resumen' => [
                'errores' => $errores,
                'advertencias' => $advertencias,
                'aprobado' => $errores === 0,
            ],
        ];
    }

    /**
     * Encuentra el párrafo "Referencias" (o "Bibliografía") y toma todo lo
     * que viene después como entradas individuales. Asume que la lista de
     * referencias es la última sección del documento -- si hay apéndices
     * después, quedarán incluidos por error; limitación conocida del MVP.
     */
    protected function extraerEntradasReferencias(array $parrafos, array $reglas): array
    {
        $indiceEncabezado = null;

        foreach ($parrafos as $i => $texto) {
            if (preg_match('/^\s*(referencias|bibliograf[ií]a)\s*$/iu', $texto)) {
                $indiceEncabezado = $i;
                break;
            }
        }

        if ($indiceEncabezado === null) {
            return [];
        }

        $resto = array_slice($parrafos, $indiceEncabezado + 1);

        // Filtra párrafos vacíos o demasiado cortos para ser una referencia real
        return array_values(array_filter($resto, fn ($p) => strlen($p) > 15));
    }

    protected function validarEncabezado(array $parrafos, array $reglas): array
    {
        foreach ($parrafos as $texto) {
            if (preg_match('/^\s*bibliograf[ií]a\s*$/iu', $texto)) {
                return $this->check(
                    'Encabezado de referencias',
                    'error',
                    "El documento usa \"Bibliografía\" como título de sección; APA exige el título \"{$reglas['encabezado_esperado']}\"."
                );
            }

            if (preg_match('/^\s*referencias\s*$/iu', $texto)) {
                return $this->check('Encabezado de referencias', 'ok', 'Título "Referencias" correcto.');
            }
        }

        return $this->check(
            'Encabezado de referencias',
            'error',
            'No se encontró una sección de "Referencias" en el documento.'
        );
    }

    protected function validarOrdenAlfabetico(array $entradas): array
    {
        if (count($entradas) < 2) {
            return $this->check('Orden alfabético', 'ok', 'No hay suficientes referencias para validar el orden.');
        }

        $apellidos = array_map(fn ($e) => $this->apellidoInicial($e), $entradas);
        $ordenados = $apellidos;
        sort($ordenados, SORT_STRING | SORT_FLAG_CASE);

        if ($apellidos === $ordenados) {
            return $this->check('Orden alfabético', 'ok', 'Las referencias están en orden alfabético.');
        }

        return $this->check(
            'Orden alfabético',
            'error',
            'Las referencias no están en orden alfabético por apellido del primer autor.'
        );
    }

    protected function apellidoInicial(string $entrada): string
    {
        $primero = explode(',', $entrada)[0] ?? $entrada;

        return strtolower(trim($primero));
    }

    protected function validarFormatoDoi(array $entradas, array $reglas): array
    {
        $conDoi = array_filter($entradas, fn ($e) => stripos($e, 'doi') !== false || str_contains($e, 'doi.org'));

        if (empty($conDoi)) {
            return $this->check('Formato de DOI', 'ok', 'No se detectaron DOIs en las referencias (o no aplica).');
        }

        $malFormateados = [];

        foreach ($conDoi as $entrada) {
            if (! preg_match($reglas['formato_doi_regex'], $entrada)) {
                $malFormateados[] = substr($entrada, 0, 60) . '...';
            }
        }

        if (empty($malFormateados)) {
            return $this->check('Formato de DOI', 'ok', "Los DOI siguen el formato esperado ({$reglas['formato_doi_esperado']}).");
        }

        return $this->check(
            'Formato de DOI',
            'advertencia',
            "Algunos DOI no siguen el formato \"{$reglas['formato_doi_esperado']}\" esperado en esta edición: " . implode(' | ', $malFormateados)
        );
    }

    /**
     * El diferencial: cruza las citas detectadas en el cuerpo del texto
     * contra las entradas de la lista de referencias, usando el apellido
     * del primer autor + año como clave de coincidencia aproximada.
     */
    protected function cruzarCitasYReferencias(array $citas, array $entradas): array
    {
        if (empty($entradas)) {
            return []; // ya se reportó como error en validarEncabezado
        }

        $firmasReferencias = [];

        foreach ($entradas as $entrada) {
            $apellido = $this->apellidoInicial($entrada);

            if (preg_match('/\((\d{4})/', $entrada, $m)) {
                $firmasReferencias["{$apellido}|{$m[1]}"] = $entrada;
            }
        }

        $firmasCitadas = [];
        $citasSinReferencia = [];

        foreach ($citas as $cita) {
            $clave = "{$cita['firma_autor']}|{$cita['anio']}";
            $firmasCitadas[$clave] = true;

            if (! isset($firmasReferencias[$clave])) {
                $citasSinReferencia[$cita['texto_original']] = true;
            }
        }

        $referenciasSinCita = [];

        foreach ($firmasReferencias as $clave => $entrada) {
            if (! isset($firmasCitadas[$clave])) {
                $referenciasSinCita[] = substr($entrada, 0, 70) . '...';
            }
        }

        $checks = [];

        if (empty($citas)) {
            $checks[] = $this->check(
                'Citas vs. referencias',
                'advertencia',
                'No se detectaron citas en el texto para cruzar contra la lista de referencias.'
            );

            return $checks;
        }

        if (empty($citasSinReferencia)) {
            $checks[] = $this->check(
                'Citas sin referencia',
                'ok',
                'Todas las citas detectadas en el texto tienen una referencia correspondiente.'
            );
        } else {
            $lista = implode(', ', array_slice(array_keys($citasSinReferencia), 0, 8));
            $checks[] = $this->check(
                'Citas sin referencia',
                'error',
                'Estas citas no tienen una entrada correspondiente en la lista de referencias: ' . $lista
            );
        }

        if (empty($referenciasSinCita)) {
            $checks[] = $this->check(
                'Referencias sin citar',
                'ok',
                'Todas las referencias están citadas en algún punto del texto.'
            );
        } else {
            $checks[] = $this->check(
                'Referencias sin citar',
                'advertencia',
                'Estas referencias no parecen estar citadas en el texto: ' . implode(' | ', $referenciasSinCita)
            );
        }

        return $checks;
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
