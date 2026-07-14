<?php

return [

    'APA6' => [
        'nombre' => 'APA 6ta edición',
        'margen_cm' => 2.54,
        'margen_tolerancia_cm' => 0.15,
        'interlineado' => 2.0,
        'interlineado_tolerancia' => 0.15,
        'fuentes_permitidas' => [
            ['nombre' => 'Times New Roman', 'tamano' => 12],
        ],
        'requiere_running_head' => true,
    ],

    'APA7' => [
        'nombre' => 'APA 7ma edición',
        'margen_cm' => 2.54,
        'margen_tolerancia_cm' => 0.15,
        'interlineado' => 2.0,
        'interlineado_tolerancia' => 0.15,
        'fuentes_permitidas' => [
            ['nombre' => 'Times New Roman', 'tamano' => 12],
            ['nombre' => 'Calibri', 'tamano' => 11],
            ['nombre' => 'Arial', 'tamano' => 11],
            ['nombre' => 'Georgia', 'tamano' => 11],
        ],
        // En APA 7 el running head con la etiqueta "Running head:" ya no es
        // obligatorio en trabajos de estudiante (solo en versión profesional).
        // Se valida como advertencia, no como error, salvo que se detecte
        // encabezado ausente por completo.
        'requiere_running_head' => false,
    ],

];
