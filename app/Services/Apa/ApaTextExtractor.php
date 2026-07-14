<?php

namespace App\Services\Apa;

use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\IOFactory;

class ApaTextExtractor
{
    /**
     * Devuelve la lista de párrafos del documento como texto plano,
     * en orden. Cada entrada es un string (el párrafo puede tener
     * varios TextRun/Text adentro, se concatenan).
     */
    public function extraerParrafos(string $rutaAbsoluta): array
    {
        $phpWord = IOFactory::load($rutaAbsoluta, 'Word2007');
        $parrafos = [];

        foreach ($phpWord->getSections() as $seccion) {
            foreach ($seccion->getElements() as $elemento) {
                $texto = $this->textoDeElemento($elemento);

                if (trim($texto) !== '') {
                    $parrafos[] = trim(preg_replace('/\s+/', ' ', $texto));
                }
            }
        }

        return $parrafos;
    }

    public function extraerTextoCompleto(string $rutaAbsoluta): string
    {
        return implode("\n", $this->extraerParrafos($rutaAbsoluta));
    }

    protected function textoDeElemento($elemento): string
    {
        if ($elemento instanceof Text) {
            return $elemento->getText();
        }

        if ($elemento instanceof TextRun) {
            $texto = '';

            foreach ($elemento->getElements() as $sub) {
                $texto .= $this->textoDeElemento($sub);
            }

            return $texto;
        }

        return '';
    }
}
