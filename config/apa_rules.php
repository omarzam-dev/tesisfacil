<?php

/*
| Normas que revisa TesisFácil.
| - tipo "apa": la norma define formato de página + citas + referencias.
| - tipo "iso": ISO 690 solo define citas y referencias. El formato de página
|   (márgenes, fuente, interlineado) lo pone cada universidad: se revisa con un
|   perfil de formato creado en Admin > Perfiles de formato.
*/

return [

    'APA7' => [
        'nombre' => 'APA 7ma edición',
        'tipo' => 'apa',
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

    'APA6' => [
        'nombre' => 'APA 6ta edición',
        'tipo' => 'apa',
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

    'ISO690A' => [
        'nombre' => 'ISO 690 autor-fecha',
        'descripcion' => 'Citas como (Pérez, 2020). Referencias en orden alfabético.',
        'tipo' => 'iso',
        'sistema_citas' => 'autor_fecha',
        'requiere_running_head' => false,
        // Con más de 3 autores, ISO 690 permite citar solo el primero + "et al."
        'et_al_desde' => 4,
    ],

    'ISO690N' => [
        'nombre' => 'ISO 690 numérico',
        'descripcion' => 'Citas como [1] o (1). Referencias numeradas en el orden en que se citan.',
        'tipo' => 'iso',
        'sistema_citas' => 'numerico',
        'requiere_running_head' => false,
    ],

];
