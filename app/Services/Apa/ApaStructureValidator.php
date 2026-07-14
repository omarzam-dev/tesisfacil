<?php

namespace App\Services\Apa;

class ApaStructureValidator
{
    public function validar(array $metricas, string $edicion): array
    {
        $reglas = config("apa_rules.{$edicion}");

        $resultados = [];

        $resultados[] = $this->validarMargenes($metricas['margenes_cm'] ?? null, $reglas);
        $resultados[] = $this->validarInterlineado($metricas['interlineado'] ?? null, $reglas);
        $resultados[] = $this->validarFuente($metricas['fuente_predominante'] ?? null, $reglas);
        $resultados[] = $this->validarEncabezado($metricas, $reglas);

        $errores = collect($resultados)->where('nivel', 'error')->count();
        $advertencias = collect($resultados)->where('nivel', 'advertencia')->count();

        return [
            'edicion' => $edicion,
            'checks' => $resultados,
            'resumen' => [
                'errores' => $errores,
                'advertencias' => $advertencias,
                'aprobado' => $errores === 0,
            ],
        ];
    }

    protected function validarMargenes(?array $margenes, array $reglas): array
    {
        if (! $margenes) {
            return $this->check('Márgenes', 'error', 'No se pudieron leer los márgenes del documento.');
        }

        $esperado = $reglas['margen_cm'];
        $tolerancia = $reglas['margen_tolerancia_cm'];

        $fueraDeRango = [];

        foreach ($margenes as $lado => $valor) {
            if (abs($valor - $esperado) > $tolerancia) {
                $fueraDeRango[] = "{$lado}: {$valor}cm";
            }
        }

        if (empty($fueraDeRango)) {
            return $this->check('Márgenes', 'ok', "Todos los márgenes están en {$esperado}cm.");
        }

        return $this->check(
            'Márgenes',
            'error',
            "Márgenes fuera de rango (deben ser {$esperado}cm): " . implode(', ', $fueraDeRango)
        );
    }

    protected function validarInterlineado(?float $interlineado, array $reglas): array
    {
        if ($interlineado === null) {
            return $this->check(
                'Interlineado',
                'advertencia',
                'No se pudo verificar automáticamente (limitación conocida cuando el interlineado está definido a nivel de plantilla, no por párrafo). Revísalo manualmente: debe ser interlineado doble.'
            );
        }

        $esperado = $reglas['interlineado'];
        $tolerancia = $reglas['interlineado_tolerancia'];

        if (abs($interlineado - $esperado) <= $tolerancia) {
            return $this->check('Interlineado', 'ok', 'Interlineado doble correcto.');
        }

        return $this->check(
            'Interlineado',
            'error',
            "Interlineado detectado ({$interlineado}) no corresponde al doble espacio requerido."
        );
    }

    protected function validarFuente(?array $fuente, array $reglas): array
    {
        if (! $fuente || ! $fuente['nombre']) {
            return $this->check('Fuente', 'advertencia', 'No se pudo determinar la fuente predominante.');
        }

        foreach ($reglas['fuentes_permitidas'] as $permitida) {
            if (
                strcasecmp($fuente['nombre'], $permitida['nombre']) === 0
                && (float) $fuente['tamano'] === (float) $permitida['tamano']
            ) {
                return $this->check('Fuente', 'ok', "{$fuente['nombre']} {$fuente['tamano']}pt es válida para esta edición.");
            }
        }

        $permitidas = collect($reglas['fuentes_permitidas'])
            ->map(fn ($f) => "{$f['nombre']} {$f['tamano']}pt")
            ->implode(', ');

        return $this->check(
            'Fuente',
            'error',
            "Fuente detectada ({$fuente['nombre']} {$fuente['tamano']}pt) no está permitida. Válidas: {$permitidas}."
        );
    }

    protected function validarEncabezado(array $metricas, array $reglas): array
    {
        $tieneEncabezado = $metricas['tiene_encabezado'] ?? false;

        if (! $reglas['requiere_running_head']) {
            return $this->check(
                'Encabezado (running head)',
                'ok',
                'No es obligatorio para esta edición en trabajos de estudiante.'
            );
        }

        if (! $tieneEncabezado) {
            return $this->check(
                'Encabezado (running head)',
                'error',
                'No se detectó encabezado; APA 6 requiere "Running head:" en la portada.'
            );
        }

        $texto = $metricas['texto_encabezado'] ?? '';

        if (stripos($texto, 'running head') === false) {
            return $this->check(
                'Encabezado (running head)',
                'advertencia',
                'Hay encabezado, pero no se encontró el texto "Running head:" — revísalo manualmente.'
            );
        }

        return $this->check('Encabezado (running head)', 'ok', 'Running head detectado correctamente.');
    }

    protected function check(string $nombre, string $nivel, string $mensaje): array
    {
        return [
            'nombre' => $nombre,
            'nivel' => $nivel, // ok | advertencia | error
            'mensaje' => $mensaje,
        ];
    }
}
