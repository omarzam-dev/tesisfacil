<?php

namespace App\Services\Apa;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

class ApaDocxParser
{
    /**
     * Lee el .docx y devuelve las métricas crudas necesarias para
     * compararlas contra las reglas de config/apa_rules.php.
     */
    public function extraer(string $rutaAbsoluta): array
    {
        $phpWord = IOFactory::load($rutaAbsoluta, 'Word2007');

        $sections = $phpWord->getSections();
        $primeraSeccion = $sections[0] ?? null;

        return [
            'margenes_cm' => $this->extraerMargenes($primeraSeccion),
            'interlineado' => $this->extraerInterlineadoPromedio($sections),
            'fuente_predominante' => $this->extraerFuentePredominante($sections, $phpWord),
            'tiene_encabezado' => $this->tieneEncabezado($primeraSeccion),
            'texto_encabezado' => $this->textoEncabezado($primeraSeccion),
            'total_secciones' => count($sections),
        ];
    }

    protected function extraerMargenes($seccion): ?array
    {
        if (! $seccion) {
            return null;
        }

        $style = $seccion->getStyle();

        // PhpWord guarda los márgenes en twips (1440 twips = 1 pulgada = 2.54 cm)
        $twipsACm = fn ($twips) => round(($twips / 1440) * 2.54, 2);

        return [
            'superior' => $twipsACm($style->getMarginTop()),
            'inferior' => $twipsACm($style->getMarginBottom()),
            'izquierdo' => $twipsACm($style->getMarginLeft()),
            'derecho' => $twipsACm($style->getMarginRight()),
        ];
    }

    protected function extraerInterlineadoPromedio(array $sections): ?float
    {
        $valores = [];

        foreach ($sections as $seccion) {
            foreach ($seccion->getElements() as $elemento) {
                if (! method_exists($elemento, 'getParagraphStyle')) {
                    continue;
                }

                $estiloParrafo = $elemento->getParagraphStyle();

                if (! $estiloParrafo || ! method_exists($estiloParrafo, 'getSpacing')) {
                    continue;
                }

                $spacing = $estiloParrafo->getLineHeight();

                if ($spacing) {
                    $valores[] = (float) $spacing;
                }
            }
        }

        if (empty($valores)) {
            return null;
        }

        return round(array_sum($valores) / count($valores), 2);
    }

    protected function extraerFuentePredominante(array $sections, $phpWord): ?array
    {
        $conteo = [];

        foreach ($sections as $seccion) {
            $this->recolectarFuentes($seccion->getElements(), $conteo);
        }

        if (empty($conteo)) {
            // Fallback: fuente por defecto del documento (styles.xml)
            $nombre = $phpWord->getDefaultFontName();
            $tamano = $phpWord->getDefaultFontSize();

            return $nombre ? ['nombre' => $nombre, 'tamano' => $tamano] : null;
        }

        arsort($conteo);
        $clave = array_key_first($conteo);
        [$nombre, $tamano] = explode('|', $clave);

        return ['nombre' => $nombre, 'tamano' => (float) $tamano];
    }

    protected function recolectarFuentes(array $elementos, array &$conteo): void
    {
        foreach ($elementos as $elemento) {
            if ($elemento instanceof Text) {
                $estilo = $elemento->getFontStyle();

                if (is_object($estilo) && $estilo->getName()) {
                    $clave = $estilo->getName() . '|' . ($estilo->getSize() ?? '');
                    $conteo[$clave] = ($conteo[$clave] ?? 0) + 1;
                }
            }

            if ($elemento instanceof TextRun) {
                $this->recolectarFuentes($elemento->getElements(), $conteo);
            }
        }
    }

    protected function tieneEncabezado($seccion): bool
    {
        if (! $seccion || ! method_exists($seccion, 'hasDifferentFirstPage')) {
            return false;
        }

        $headers = method_exists($seccion, 'getHeaders') ? $seccion->getHeaders() : [];

        return ! empty($headers);
    }

    protected function textoEncabezado($seccion): ?string
    {
        if (! $seccion || ! method_exists($seccion, 'getHeaders')) {
            return null;
        }

        $headers = $seccion->getHeaders();

        if (empty($headers)) {
            return null;
        }

        $texto = '';

        foreach (reset($headers)->getElements() as $elemento) {
            if ($elemento instanceof Text) {
                $texto .= $elemento->getText() . ' ';
            } elseif ($elemento instanceof TextRun) {
                foreach ($elemento->getElements() as $sub) {
                    if ($sub instanceof Text) {
                        $texto .= $sub->getText() . ' ';
                    }
                }
            }
        }

        return trim($texto) ?: null;
    }
}
