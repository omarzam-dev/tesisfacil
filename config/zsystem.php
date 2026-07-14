<?php

return [

    // URL base de ZSystem Platform (sin slash final)
    'platform_url' => env('ZSYSTEM_PLATFORM_URL', 'http://localhost/zsystem-platform/public'),

    // Licencia asignada a esta instalación (una por cliente/organización)
    'license_key' => env('ZSYSTEM_LICENSE_KEY'),

    // API Key de la organización en ZSystem Platform (header X-API-KEY requerido
    // por el endpoint /api/v1/license/validate)
    'api_key' => env('ZSYSTEM_API_KEY'),

    // Código de este producto tal como está registrado en ZSystem Platform
    'product_code' => 'TESISFACIL',

    // Minutos que se cachea una validación exitosa antes de volver a consultar
    'cache_minutes' => env('ZSYSTEM_LICENSE_CACHE_MINUTES', 60),

    // Días de gracia: si la plataforma no responde, se sigue usando la última
    // validación válida cacheada durante este periodo antes de bloquear el acceso.
    'grace_days' => env('ZSYSTEM_LICENSE_GRACE_DAYS', 7),

    // Timeout en segundos para la llamada HTTP de validación
    'timeout' => env('ZSYSTEM_LICENSE_TIMEOUT', 5),

];
