<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZSystemLicenseService
{
    protected string $cacheKey;

    protected string $graceCacheKey;

    public function __construct()
    {
        $licenseKey = config('zsystem.license_key');

        $this->cacheKey = 'zsystem_license_' . md5((string) $licenseKey);

        $this->graceCacheKey = $this->cacheKey . '_grace';
    }

    /**
     * Valida la licencia de esta instalación contra ZSystem Platform.
     * Devuelve el array 'data' de la respuesta si es válida, o null si no lo es.
     */
    public function validate(): ?array
    {
        $licenseKey = config('zsystem.license_key');

        if (empty($licenseKey)) {
            Log::error('ZSystemLicenseService: ZSYSTEM_LICENSE_KEY no configurada en .env');

            return null;
        }

        return Cache::remember(
            $this->cacheKey,
            now()->addMinutes((int) config('zsystem.cache_minutes')),
            fn () => $this->fetchFromPlatform($licenseKey)
        );
    }

    /**
     * Llama a la API de ZSystem Platform. Si la plataforma no responde,
     * cae en modo de gracia usando la última validación válida conocida.
     */
    protected function fetchFromPlatform(string $licenseKey): ?array
    {
        try {
            $response = Http::timeout((int) config('zsystem.timeout'))
                ->post(
                    rtrim(config('zsystem.platform_url'), '/') . '/api/v1/license/validate',
                    ['license_key' => $licenseKey]
                );

            $body = $response->json();

            if ($response->successful() && ($body['success'] ?? false)) {
                // Guarda copia de respaldo para modo de gracia
                Cache::put(
                    $this->graceCacheKey,
                    $body['data'],
                    now()->addDays((int) config('zsystem.grace_days'))
                );

                return $body['data'];
            }

            Log::warning('ZSystemLicenseService: licencia rechazada por la plataforma', [
                'message' => $body['message'] ?? 'sin mensaje',
            ]);

            return null;

        } catch (\Throwable $e) {
            Log::warning('ZSystemLicenseService: no se pudo contactar a ZSystem Platform, evaluando modo de gracia', [
                'error' => $e->getMessage(),
            ]);

            return $this->fallbackToGrace();
        }
    }

    /**
     * Si la plataforma está caída, usa la última validación válida cacheada
     * (dentro del periodo de gracia) para no tumbar el sistema del cliente.
     */
    protected function fallbackToGrace(): ?array
    {
        $graceData = Cache::get($this->graceCacheKey);

        if ($graceData) {
            Log::info('ZSystemLicenseService: usando licencia en modo de gracia');
        }

        return $graceData;
    }

    /**
     * Fuerza una revalidación contra la plataforma, ignorando la caché normal.
     */
    public function forceRevalidate(): ?array
    {
        Cache::forget($this->cacheKey);

        return $this->validate();
    }
}
