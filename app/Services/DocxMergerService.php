<?php

namespace App\Services;

use ZipArchive;

class DocxMergerService
{
    /**
     * Combina varios .docx en uno solo, en el orden dado, con un título
     * de capítulo antes de cada uno y salto de página entre capítulos.
     *
     * $capitulos: array de ['nombre' => string, 'ruta_absoluta' => string]
     * Devuelve ['ruta' => string, 'omitidos' => array<string>]
     *
     * Limitación conocida: preserva texto y formato en línea (negrita,
     * tamaño, alineación), pero NO combina imágenes ni numeración de listas
     * de los capítulos 2 en adelante -- viven en relaciones internas
     * separadas de cada .docx que este método no fusiona.
     */
    public function combinar(array $capitulos, string $rutaSalida): array
    {
        $validos = array_values(array_filter(
            $capitulos,
            fn ($c) => is_file($c['ruta_absoluta'])
        ));

        if (empty($validos)) {
            throw new \RuntimeException('Ningún capítulo tiene un archivo .docx válido para combinar.');
        }

        $omitidos = [];
        $base = array_shift($validos);

        copy($base['ruta_absoluta'], $rutaSalida);

        $zip = new ZipArchive();
        if ($zip->open($rutaSalida) !== true) {
            throw new \RuntimeException('No se pudo abrir el documento base para combinar.');
        }

        $baseXml = $zip->getFromName('word/document.xml');

        if ($baseXml === false) {
            $zip->close();
            throw new \RuntimeException('El documento base no tiene una estructura .docx válida.');
        }

        // Punto de inserción: justo antes de <w:sectPr> (propiedades de
        // sección/página), que debe quedar al final del documento combinado.
        $sectPrPos = strrpos($baseXml, '<w:sectPr');

        if ($sectPrPos === false) {
            $zip->close();
            throw new \RuntimeException('No se pudo ubicar la estructura del documento base.');
        }

        // Encabezado del primer capítulo, insertado al inicio del cuerpo
        preg_match('/<w:body[^>]*>/', $baseXml, $matchBody, PREG_OFFSET_CAPTURE);

        if (empty($matchBody)) {
            $zip->close();
            throw new \RuntimeException('No se pudo ubicar el cuerpo del documento base.');
        }

        $bodyTagEnd = $matchBody[0][1] + strlen($matchBody[0][0]);

        $contenidoCapitulo1 = substr($baseXml, $bodyTagEnd, $sectPrPos - $bodyTagEnd);

        $fragmentos = $this->encabezadoCapitulo($base['nombre'], false) . $contenidoCapitulo1;

        foreach ($validos as $capitulo) {
            try {
                $cuerpo = $this->extraerCuerpo($capitulo['ruta_absoluta']);
                $fragmentos .= $this->encabezadoCapitulo($capitulo['nombre'], true)
                    . $cuerpo;
            } catch (\Throwable $e) {
                $omitidos[] = $capitulo['nombre'];
            }
        }

        $nuevoXml = substr($baseXml, 0, $bodyTagEnd)
            . $fragmentos
            . substr($baseXml, $sectPrPos);

        $zip->addFromString('word/document.xml', $nuevoXml);
        $zip->close();

        return ['ruta' => $rutaSalida, 'omitidos' => $omitidos];
    }

    protected function extraerCuerpo(string $rutaDocx): string
    {
        $zip = new ZipArchive();

        if ($zip->open($rutaDocx) !== true) {
            throw new \RuntimeException("No se pudo abrir {$rutaDocx}");
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new \RuntimeException("Documento .docx inválido: {$rutaDocx}");
        }

        preg_match('/<w:body[^>]*>(.*)<w:sectPr/s', $xml, $m);

        if (empty($m[1])) {
            throw new \RuntimeException("No se pudo extraer el contenido de {$rutaDocx}");
        }

        return $m[1];
    }

    protected function encabezadoCapitulo(string $nombre, bool $saltoPagina = true): string
    {
        $nombreEscapado = htmlspecialchars($nombre, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $breakXml = $saltoPagina ? '<w:pageBreakBefore/>' : '';

        return '<w:p><w:pPr>' . $breakXml . '<w:jc w:val="center"/><w:spacing w:before="240" w:after="240"/></w:pPr>'
            . '<w:r><w:rPr><w:b/><w:sz w:val="32"/></w:rPr><w:t xml:space="preserve">'
            . $nombreEscapado
            . '</w:t></w:r></w:p>';
    }
}
