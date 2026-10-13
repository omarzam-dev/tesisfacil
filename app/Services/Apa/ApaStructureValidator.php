<?php

namespace App\Services\Apa;

/**
 * Revisa el formato de página: márgenes, interlineado, fuente y encabezado.
 *
 * $reglas puede ser:
 * - el código de una norma APA ("APA7", "APA6"): usa config/apa_rules.php
 * - un arreglo de reglas ya armado (por ejemplo, el de un perfil de formato
 *   de universidad, ver PerfilFormato::reglas()).
 */
class ApaStructureValidator
{
    public function validar(array $metricas, string|array $reglas): array
    {
        $edicion = is_string($reglas) ? $reglas : ($reglas['codigo'] ?? 'perfil');
        $reglas = $this->normalizarReglas($reglas);

        $resultados = [];

        $resultados[] = $this->validarMargenes($metricas['margenes_cm'] ?? null, $reglas);
        $resultados[] = $this->validarInterlineado($metricas['interlineado'] ?? null, $reglas);
        $resultados[] = $this->validarFuente($metricas['fuente_predominante'] ?? null, $reglas);
        $resultados[] = $this->validarEncabezado($metricas, $reglas);

        $errores = collect($resultados)->where('nivel', 'error')->count();
        $advertencias = collect($resultados)->where('nivel', 'advertencia')->count();

        return [
            'edicion' => $edicion,
            'formato_segun' => $reglas['nombre'],
            'checks' => $resultados,
            'resumen' => [
                'errores' => $errores,
                'advertencias' => $advertencias,
                'aprobado' => $errores === 0,
            ],
        ];
    }

    /**
     * Para normas que no definen formato de página (ISO 690) cuando el
     * tesista no eligió el perfil de su universidad.
     */
    public function sinFormatoDefinido(string $edicion, string $nombreNorma): array
    {
        return [
            'edicion' => $edicion,
            'formato_segun' => null,
            'checks' => [
                $this->check(
                    'Formato de página',
                    'advertencia',
                    "{$nombreNorma} solo norma las citas y las referencias; los márgenes, la fuente y el interlineado los fija la guía de tu universidad. Elige el perfil de tu universidad al crear la revisión para que también se revisen."
                ),
            ],
            'resumen' => [
                'errores' => 0,
                'advertencias' => 1,
                'aprobado' => true,
            ],
        ];
    }

    protected function normalizarReglas(string|array $reglas): array
    {
        if (is_string($reglas)) {
            $reglas = config("apa_rules.{$reglas}");
        }

        // Las normas APA traen un solo margen para los 4 lados
        if (! isset($reglas['margenes']) && isset($reglas['margen_cm'])) {
            $m = $reglas['margen_cm'];
            $reglas['margenes'] = ['superior' => $m, 'inferior' => $m, 'izquierdo' => $m, 'derecho' => $m];
        }

        return $reglas + [
            'nombre' => 'la norma',
            'margen_tolerancia_cm' => 0.15,
            'interlineado_tolerancia' => 0.15,
            'requiere_running_head' => false,
            'fuentes_permitidas' => [],
        ];
    }

    protected function validarMargenes(?array $margenes, array $reglas): array
    {
        if (! $margenes) {
            return $this->check('Márgenes', 'error', 'No se pudieron leer los márgenes del documento.');
        }

        $esperados = $reglas['margenes'];
        $tolerancia = $reglas['margen_tolerancia_cm'];

        $fueraDeRango = [];

        foreach ($margenes as $lado => $valor) {
            $esperado = (float) ($esperados[$lado] ?? 0);

            if (abs($valor - $esperado) > $tolerancia) {
                $fueraDeRango[] = "{$lado}: {$valor} cm (debe ser {$this->cm($esperado)} cm)";
            }
        }

        if (empty($fueraDeRango)) {
            return $this->check('Márgenes', 'ok', 'Márgenes correctos según ' . $reglas['nombre'] . ': ' . $this->resumenMargenes($esperados) . '.');
        }

        return $this->check(
            'Márgenes',
            'error',
            'Márgenes fuera de rango según ' . $reglas['nombre'] . ': ' . implode(', ', $fueraDeRango) . '.'
        );
    }

    protected function validarInterlineado(?float $interlineado, array $reglas): array
    {
        $esperado = (float) $reglas['interlineado'];
        $texto = $this->nombreInterlineado($esperado);

        if ($interlineado === null) {
            return $this->check(
                'Interlineado',
                'advertencia',
                "No se pudo verificar automáticamente (pasa cuando el interlineado está definido en la plantilla de Word y no en cada párrafo). Revísalo manualmente: debe ser {$texto}."
            );
        }

        if (abs($interlineado - $esperado) <= $reglas['interlineado_tolerancia']) {
            return $this->check('Interlineado', 'ok', "Interlineado correcto ({$texto}).");
        }

        return $this->check(
            'Interlineado',
            'error',
            "El interlineado detectado ({$interlineado}) no corresponde al requerido: {$texto}."
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
                return $this->check('Fuente', 'ok', "{$fuente['nombre']} {$this->pt($fuente['tamano'])} pt es válida según {$reglas['nombre']}.");
            }
        }

        $permitidas = collect($reglas['fuentes_permitidas'])
            ->map(fn ($f) => "{$f['nombre']} {$this->pt($f['tamano'])} pt")
            ->implode(', ');

        return $this->check(
            'Fuente',
            'error',
            "La fuente detectada ({$fuente['nombre']} {$this->pt($fuente['tamano'])} pt) no está permitida según {$reglas['nombre']}. Válidas: {$permitidas}."
        );
    }

    protected function validarEncabezado(array $metricas, array $reglas): array
    {
        $tieneEncabezado = $metricas['tiene_encabezado'] ?? false;

        if (! $reglas['requiere_running_head']) {
            return $this->check(
                'Encabezado (running head)',
                'ok',
                'No es obligatorio en trabajos de estudiante con esta norma.'
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
                'Hay encabezado, pero no se encontró el texto "Running head:". Revísalo manualmente.'
            );
        }

        return $this->check('Encabezado (running head)', 'ok', 'Running head detectado correctamente.');
    }

    protected function nombreInterlineado(float $valor): string
    {
        return match (true) {
            abs($valor - 1.0) < 0.01 => 'sencillo (1,0)',
            abs($valor - 1.15) < 0.01 => '1,15',
            abs($valor - 1.5) < 0.01 => '1,5 líneas',
            abs($valor - 2.0) < 0.01 => 'doble (2,0)',
            default => str_replace('.', ',', (string) $valor),
        };
    }

    protected function resumenMargenes(array $m): string
    {
        $valores = array_map(fn ($v) => (float) $v, $m);

        if (count(array_unique($valores)) === 1) {
            return $this->cm(reset($valores)) . ' cm en los 4 lados';
        }

        return "superior {$this->cm($m['superior'])}, inferior {$this->cm($m['inferior'])}, izquierdo {$this->cm($m['izquierdo'])}, derecho {$this->cm($m['derecho'])} cm";
    }

    protected function cm($v): string
    {
        return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
    }

    protected function pt($v): string
    {
        return rtrim(rtrim(number_format((float) $v, 1, '.', ''), '0'), '.');
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
