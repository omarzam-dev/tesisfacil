<?php

namespace App\Services\Apa;

class ApaCitationValidator
{
    /**
     * Escanea el texto completo buscando citas en formato paréntesis y
     * narrativo, valida conectores y regla de "et al." según la edición,
     * y devuelve tanto los hallazgos como la lista de citas detectadas
     * (usada después para el cruce contra referencias).
     */
    public function validar(string $texto, string $edicion): array
    {
        $reglas = config("apa_rules.{$edicion}");

        $citasParentesis = $this->extraerCitasParentesis($texto);
        $citasNarrativas = $this->extraerCitasNarrativas($texto);

        $todasLasCitas = array_merge($citasParentesis, $citasNarrativas);

        $checks = [];
        $vistas = []; // firma de autor => true, para saber si es "primera cita"

        foreach ($todasLasCitas as $cita) {
            $checks[] = $this->validarCita($cita, $reglas, $vistas);
            $vistas[$cita['firma_autor']] = true;
        }

        $errores = collect($checks)->where('nivel', 'error')->count();
        $advertencias = collect($checks)->where('nivel', 'advertencia')->count();

        return [
            'edicion' => $edicion,
            'total_citas_detectadas' => count($todasLasCitas),
            'checks' => $checks,
            'citas' => $todasLasCitas, // para el cruce con referencias
            'resumen' => [
                'errores' => $errores,
                'advertencias' => $advertencias,
                'aprobado' => $errores === 0,
            ],
        ];
    }

    protected function extraerCitasParentesis(string $texto): array
    {
        // (Apellido, 2020) | (Apellido & Apellido2, 2020) | (Apellido et al., 2020)
        $patron = '/\(([A-ZÁÉÍÓÚÑ][\p{L}\.\-]+(?:,?\s*(?:&|et al\.?)\s*[A-ZÁÉÍÓÚÑ]?[\p{L}\.\-]*)?),\s*(\d{4}[a-z]?)\)/u';

        preg_match_all($patron, $texto, $matches, PREG_SET_ORDER);

        $citas = [];

        foreach ($matches as $m) {
            $citas[] = [
                'tipo' => 'parentesis',
                'texto_original' => $m[0],
                'autores_texto' => trim($m[1]),
                'anio' => $m[2],
                'firma_autor' => $this->firmaAutor($m[1]),
                'num_autores_aprox' => $this->contarAutoresAprox($m[1]),
                'tiene_et_al' => stripos($m[1], 'et al') !== false,
            ];
        }

        return $citas;
    }

    protected function extraerCitasNarrativas(string $texto): array
    {
        // Apellido (2020) | Apellido y Apellido2 (2020) | Apellido et al. (2020)
        $patron = '/([A-ZÁÉÍÓÚÑ][\p{L}\.\-]+(?:\s*(?:y|et al\.?)\s*[A-ZÁÉÍÓÚÑ]?[\p{L}\.\-]*)?)\s*\((\d{4}[a-z]?)\)/u';

        preg_match_all($patron, $texto, $matches, PREG_SET_ORDER);

        $citas = [];

        foreach ($matches as $m) {
            $citas[] = [
                'tipo' => 'narrativa',
                'texto_original' => $m[0],
                'autores_texto' => trim($m[1]),
                'anio' => $m[2],
                'firma_autor' => $this->firmaAutor($m[1]),
                'num_autores_aprox' => $this->contarAutoresAprox($m[1]),
                'tiene_et_al' => stripos($m[1], 'et al') !== false,
            ];
        }

        return $citas;
    }

    protected function firmaAutor(string $autoresTexto): string
    {
        // Toma el primer apellido como firma para agrupar "misma cita" en distintas apariciones
        $primero = preg_split('/[,&]|\sy\s|\set al/u', $autoresTexto)[0] ?? $autoresTexto;

        return strtolower(trim($primero));
    }

    protected function contarAutoresAprox(string $autoresTexto): int
    {
        if (stripos($autoresTexto, 'et al') !== false) {
            return 3; // et al. implica 3 o más, no sabemos el número exacto
        }

        $conectores = preg_match_all('/&|\sy\s/u', $autoresTexto);

        return $conectores + 1;
    }

    protected function validarCita(array $cita, array $reglas, array $vistas): array
    {
        $esPrimeraCita = ! isset($vistas[$cita['firma_autor']]);
        $conectorEsperado = $cita['tipo'] === 'parentesis'
            ? $reglas['conector_parentesis']
            : $reglas['conector_narrativo'];

        // Regla de "et al." según edición y si es primera o repetida
        $deberiaUsarEtAl = $esPrimeraCita
            ? $cita['num_autores_aprox'] >= $reglas['et_al_desde_primera_cita']
            : $cita['num_autores_aprox'] > 1; // en citas repetidas, 2+ autores no-únicos suelen abreviarse

        if ($cita['num_autores_aprox'] >= 3 && $esPrimeraCita && ! $cita['tiene_et_al'] && $reglas['et_al_desde_primera_cita'] <= $cita['num_autores_aprox']) {
            return $this->resultado(
                $cita,
                'error',
                "Con {$cita['num_autores_aprox']}+ autores, esta edición exige \"et al.\" desde la primera cita: \"{$cita['texto_original']}\"."
            );
        }

        if ($cita['num_autores_aprox'] === 2 && ! $cita['tiene_et_al']) {
            $tieneConectorEsperado = str_contains($cita['autores_texto'], $conectorEsperado);

            if (! $tieneConectorEsperado) {
                return $this->resultado(
                    $cita,
                    'advertencia',
                    "Verifica el conector entre 2 autores en \"{$cita['texto_original']}\" — se espera \"{$conectorEsperado}\" en citas de tipo {$cita['tipo']}."
                );
            }
        }

        return $this->resultado($cita, 'ok', "Cita correcta: \"{$cita['texto_original']}\".");
    }

    protected function resultado(array $cita, string $nivel, string $mensaje): array
    {
        return [
            'cita' => $cita['texto_original'],
            'nivel' => $nivel,
            'mensaje' => $mensaje,
        ];
    }
}
