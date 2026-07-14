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

        // Citas
        'conector_parentesis' => '&',
        'conector_narrativo' => 'y',
        // APA 6: con 3-5 autores se listan todos en la PRIMERA cita, y se usa
        // "et al." en las citas subsecuentes. Con 6+ autores, "et al." desde la primera.
        'listar_todos_hasta' => 5,
        'et_al_desde_primera_cita' => 6,

        // Referencias
        'encabezado_esperado' => 'Referencias',
        'formato_doi_esperado' => 'doi:',
        'formato_doi_regex' => '/doi:\s*10\.\d{4,9}\/\S+/i',
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
        'requiere_running_head' => false,

        // Citas
        'conector_parentesis' => '&',
        'conector_narrativo' => 'y',
        // APA 7: con 3+ autores se usa "et al." desde la PRIMERA cita, siempre.
        'listar_todos_hasta' => 2,
        'et_al_desde_primera_cita' => 3,

        // Referencias
        'encabezado_esperado' => 'Referencias',
        'formato_doi_esperado' => 'https://doi.org/',
        'formato_doi_regex' => '/https?:\/\/doi\.org\/10\.\d{4,9}\/\S+/i',
    ],

];
