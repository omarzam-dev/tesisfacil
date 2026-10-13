<?php

namespace App\Services\Apa;

/**
 * Lista de referencias según ISO 690.
 * Ejemplos de lo que se espera:
 *   PÉREZ, Juan. Título del libro. Lima: Editorial, 2020. ISBN 978-...
 *   GARCÍA, Ana. Título del artículo [en línea]. Revista, 2021, vol. 3, n.º 2, pp. 10-20.
 *       [consulta: 12 de octubre de 2026]. Disponible en: https://...
 */
class IsoReferenceValidator
{
    public function validar(array $parrafos, array $resultadoCitas, string $edicion): array
    {
        $reglas = config("apa_rules.{$edicion}");
        $numerico = ($reglas['sistema_citas'] ?? 'autor_fecha') === 'numerico';
        $citas = $resultadoCitas['citas'] ?? [];

        $entradas = TextoReferencias::entradas($parrafos);

        $checks = [];
        $checks[] = $this->validarEncabezado($parrafos, $entradas);

        if (! empty($entradas)) {
            if ($numerico) {
                $checks = array_merge($checks, $this->validarListaNumerica($entradas, $citas));
            } else {
                $checks[] = $this->validarOrdenAlfabetico($entradas);
                $checks = array_merge($checks, $this->cruzarAutorFecha($citas, $entradas));
            }

            $checks = array_merge($checks, $this->validarFormatoEntradas($entradas));
        }

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

    protected function validarEncabezado(array $parrafos, array $entradas): array
    {
        $i = TextoReferencias::indiceEncabezado($parrafos);

        if ($i === null) {
            return $this->check(
                'Sección de referencias',
                'error',
                'No se encontró la sección de referencias. Debe tener un título propio: "Referencias", "Referencias bibliográficas" o "Bibliografía", según tu universidad.'
            );
        }

        if (empty($entradas)) {
            return $this->check('Sección de referencias', 'error', "Se encontró el título \"{$parrafos[$i]}\", pero no hay referencias debajo.");
        }

        return $this->check('Sección de referencias', 'ok', "Sección \"{$parrafos[$i]}\" con " . count($entradas) . ' referencias.');
    }

    /* ---------------------------------------------------------------
     | Autor-fecha
     --------------------------------------------------------------- */

    protected function validarOrdenAlfabetico(array $entradas): array
    {
        if (count($entradas) < 2) {
            return $this->check('Orden alfabético', 'ok', 'No hay suficientes referencias para revisar el orden.');
        }

        $apellidos = array_map(fn ($e) => TextoReferencias::normalizar(TextoReferencias::primerAutor($e)), $entradas);

        for ($i = 1; $i < count($apellidos); $i++) {
            if (strcmp($apellidos[$i - 1], $apellidos[$i]) > 0) {
                return $this->check(
                    'Orden alfabético',
                    'error',
                    'En el sistema autor-fecha las referencias van en orden alfabético por el apellido del primer autor. Revisa desde: "'
                        . TextoReferencias::recortar($entradas[$i], 60) . '".'
                );
            }
        }

        return $this->check('Orden alfabético', 'ok', 'Las referencias están en orden alfabético.');
    }

    protected function cruzarAutorFecha(array $citas, array $entradas): array
    {
        if (empty($citas)) {
            return [$this->check('Citas vs. referencias', 'advertencia', 'No hay citas detectadas en el texto para cruzar con la lista de referencias.')];
        }

        // apellido normalizado => lista de [entrada, años que aparecen en ella]
        $indice = [];

        foreach ($entradas as $entrada) {
            preg_match_all('/\b(1[5-9]\d{2}|20\d{2})[a-z]?\b/u', $entrada, $anios);
            $sinFecha = (bool) preg_match('/s\.\s?f\./iu', $entrada);

            $indice[TextoReferencias::normalizar(TextoReferencias::primerAutor($entrada))][] = [
                'entrada' => $entrada,
                'anios' => $anios[1],
                'sin_fecha' => $sinFecha,
            ];
        }

        $citasSinReferencia = [];
        $entradasCitadas = [];

        foreach ($citas as $cita) {
            $encontrada = false;

            foreach ($indice[$cita['firma_autor']] ?? [] as $ref) {
                $anio = substr($cita['anio'], 0, 4);
                $coincide = str_starts_with($cita['anio'], 's.')
                    ? $ref['sin_fecha']
                    : in_array($anio, $ref['anios'], true);

                if ($coincide) {
                    $encontrada = true;
                    $entradasCitadas[$ref['entrada']] = true;
                }
            }

            if (! $encontrada) {
                $citasSinReferencia[$cita['texto_original']] = true;
            }
        }

        $referenciasSinCita = array_filter($entradas, fn ($e) => ! isset($entradasCitadas[$e]));

        $checks = [];

        $checks[] = empty($citasSinReferencia)
            ? $this->check('Citas sin referencia', 'ok', 'Todas las citas del texto tienen su referencia.')
            : $this->check(
                'Citas sin referencia',
                'error',
                'Estas citas no tienen una referencia con el mismo apellido y año: ' . implode(', ', array_slice(array_keys($citasSinReferencia), 0, 8))
            );

        $checks[] = empty($referenciasSinCita)
            ? $this->check('Referencias sin citar', 'ok', 'Todas las referencias están citadas en el texto.')
            : $this->check(
                'Referencias sin citar',
                'advertencia',
                'Estas referencias no parecen estar citadas en el texto: '
                    . implode(' | ', array_map(fn ($e) => TextoReferencias::recortar($e), array_slice(array_values($referenciasSinCita), 0, 6)))
            );

        return $checks;
    }

    /* ---------------------------------------------------------------
     | Numérico
     --------------------------------------------------------------- */

    protected function validarListaNumerica(array $entradas, array $citas): array
    {
        $checks = [];
        $total = count($entradas);
        $numeros = array_map(fn ($e) => TextoReferencias::numeroInicial($e), $entradas);
        $numeradas = count(array_filter($numeros, fn ($n) => $n !== null));

        if ($numeradas === 0) {
            $checks[] = $this->check(
                'Numeración de la lista',
                'advertencia',
                'No se ve un número escrito delante de cada referencia. Si usaste la numeración automática de Word está bien; verifica que el orden de la lista sea el mismo en que citas las fuentes.'
            );
        } else {
            $esperado = range(1, $total);
            $checks[] = $numeros === $esperado
                ? $this->check('Numeración de la lista', 'ok', "Referencias numeradas del 1 al {$total} sin saltos.")
                : $this->check('Numeración de la lista', 'error', "La lista debe numerarse 1, 2, 3... sin saltos ni repetidos, en el orden de cita. Hay {$total} referencias y la numeración no es consecutiva.");
        }

        if (empty($citas)) {
            return $checks;
        }

        $citados = [];

        foreach ($citas as $cita) {
            foreach ($cita['numeros'] as $n) {
                $citados[$n] = $cita['texto_original'];
            }
        }

        $sinReferencia = array_filter(array_keys($citados), fn ($n) => $n > $total);
        sort($sinReferencia);

        $checks[] = empty($sinReferencia)
            ? $this->check('Citas sin referencia', 'ok', 'Todos los números citados existen en la lista de referencias.')
            : $this->check(
                'Citas sin referencia',
                'error',
                "Se cita el número " . implode(', ', array_slice($sinReferencia, 0, 8)) . " pero la lista solo tiene {$total} referencias."
            );

        $sinCitar = array_values(array_filter(range(1, $total), fn ($n) => ! isset($citados[$n])));

        $checks[] = empty($sinCitar)
            ? $this->check('Referencias sin citar', 'ok', 'Todas las referencias se citan en el texto.')
            : $this->check(
                'Referencias sin citar',
                'advertencia',
                'Estas referencias no se citan en el texto: ' . implode(', ', array_map(fn ($n) => "[{$n}]", array_slice($sinCitar, 0, 10))) . '.'
            );

        return $checks;
    }

    /* ---------------------------------------------------------------
     | Forma de cada referencia (ambos sistemas)
     --------------------------------------------------------------- */

    protected function validarFormatoEntradas(array $entradas): array
    {
        $apellidoMinuscula = [];
        $estiloApa = [];
        $sinDisponible = [];
        $sinConsulta = [];
        $sinEnLinea = [];
        $doiMal = [];

        foreach ($entradas as $entrada) {
            $texto = TextoReferencias::sinNumero($entrada);
            $autor = TextoReferencias::primerAutor($entrada);
            $corta = TextoReferencias::recortar($texto, 60);

            // Apellido de persona (1 o 2 palabras) que no está en MAYÚSCULAS
            if (str_contains($texto, ',') && count(preg_split('/\s+/u', $autor)) <= 2 && $autor !== mb_strtoupper($autor, 'UTF-8')) {
                $apellidoMinuscula[] = $corta;
            }

            // "Pérez, J. (2020)." es la forma APA, no ISO
            if (preg_match('/^[^()]{2,150}\(\d{4}[a-z]?\)\./u', $texto)) {
                $estiloApa[] = $corta;
            }

            $tieneUrl = (bool) preg_match('/https?:\/\/|www\./iu', $texto);
            $soloDoi = $tieneUrl && ! preg_match('/https?:\/\/(?!(dx\.)?doi\.org)|www\./iu', $texto);

            if ($tieneUrl && ! $soloDoi) {
                if (! preg_match('/disponible\s+en|available\s+(from|at)/iu', $texto)) {
                    $sinDisponible[] = $corta;
                }
                if (! preg_match('/\[\s*(fecha\s+de\s+)?consult|\[\s*accessed|\[\s*cit/iu', $texto)) {
                    $sinConsulta[] = $corta;
                }
                if (! preg_match('/\[\s*(en\s+l[ií]nea|online)\s*\]/iu', $texto)) {
                    $sinEnLinea[] = $corta;
                }
            }

            if (preg_match('/doi/iu', $texto)
                && ! preg_match('/doi:?\s*10\.\d{4,9}\/\S+|https?:\/\/(dx\.)?doi\.org\/10\.\d{4,9}\/\S+/iu', $texto)) {
                $doiMal[] = $corta;
            }
        }

        $checks = [];

        $checks[] = $this->resultadoLista(
            'Apellidos en mayúsculas',
            $apellidoMinuscula,
            'advertencia',
            'Los apellidos están en MAYÚSCULAS.',
            'ISO 690 escribe el apellido del autor en MAYÚSCULAS (PÉREZ, Juan). Revisa: '
        );

        $checks[] = $this->resultadoLista(
            'Forma ISO (no APA)',
            $estiloApa,
            'error',
            'Ninguna referencia usa la forma APA con el año entre paréntesis.',
            'Estas referencias tienen la forma APA, con el año entre paréntesis después del autor. En ISO 690 el año va sin paréntesis (en autor-fecha: PÉREZ, Juan, 2020.; en numérico: después de la editorial). Revisa: '
        );

        $checks[] = $this->resultadoLista(
            'Fuentes de internet: "Disponible en"',
            $sinDisponible,
            'error',
            'Las fuentes de internet indican "Disponible en:".',
            'En ISO 690 la dirección web va precedida de "Disponible en:" (no "Recuperado de"). Revisa: '
        );

        $checks[] = $this->resultadoLista(
            'Fuentes de internet: fecha de consulta',
            $sinConsulta,
            'advertencia',
            'Las fuentes de internet tienen fecha de consulta.',
            'Falta la fecha de consulta entre corchetes, por ejemplo [consulta: 12 de octubre de 2026]. Revisa: '
        );

        $checks[] = $this->resultadoLista(
            'Fuentes de internet: [en línea]',
            $sinEnLinea,
            'advertencia',
            'Las fuentes de internet indican [en línea].',
            'ISO 690 indica el soporte de los documentos electrónicos con [en línea] después del título. Revisa: '
        );

        $checks[] = $this->resultadoLista(
            'Formato de DOI',
            $doiMal,
            'advertencia',
            'Los DOI están bien escritos (DOI: 10.xxxx/... o https://doi.org/10.xxxx/...).',
            'Escribe el DOI como "DOI: 10.xxxx/..." o "https://doi.org/10.xxxx/...". Revisa: '
        );

        return $checks;
    }

    protected function resultadoLista(string $nombre, array $lista, string $nivel, string $mensajeOk, string $prefijo): array
    {
        if (empty($lista)) {
            return $this->check($nombre, 'ok', $mensajeOk);
        }

        $extra = count($lista) > 4 ? ' (y ' . (count($lista) - 4) . ' más)' : '';

        return $this->check($nombre, $nivel, $prefijo . implode(' | ', array_slice($lista, 0, 4)) . $extra);
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
