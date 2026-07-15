<?php

use PhpOffice\PhpWord\IOFactory;

$ruta = base_path('storage/app/private/revisiones_apa/1/scMylqvbkVjtGBF9NHlm5D6FQRoNGf90H27egvJi.docx');

$phpWord = IOFactory::load($ruta, 'Word2007');
$sections = $phpWord->getSections();
$primerElemento = $sections[0]->getElements()[0] ?? null;

echo "Clase del primer elemento: " . get_class($primerElemento) . "\n";

if (method_exists($primerElemento, 'getParagraphStyle')) {
    $estilo = $primerElemento->getParagraphStyle();
    echo "Tipo de estilo: " . gettype($estilo) . " / " . (is_object($estilo) ? get_class($estilo) : $estilo) . "\n";

    if (is_object($estilo)) {
        echo "Metodos disponibles relacionados a spacing/line:\n";
        foreach (get_class_methods($estilo) as $m) {
            if (stripos($m, 'line') !== false || stripos($m, 'spac') !== false) {
                echo "  - $m\n";
            }
        }
    }
}

echo "\nMetodos de PhpWord relacionados a estilo por defecto:\n";
foreach (get_class_methods($phpWord) as $m) {
    if (stripos($m, 'default') !== false || stripos($m, 'style') !== false) {
        echo "  - $m\n";
    }
}
