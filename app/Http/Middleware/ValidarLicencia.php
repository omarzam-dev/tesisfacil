<?php

namespace App\Http\Middleware;

use App\Services\ZSystemLicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarLicencia
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = (new ZSystemLicenseService())->validate();

        if (! $data) {
            return response()->view('errors.licencia', [], 403);
        }

        // Datos disponibles en toda la app vía config('zsystem.current')
        config(['zsystem.current' => $data]);

        // Límite específico de TesisFácil (definido en el plan/subscription)
        config(['zsystem.max_tesis' => $data['license']['limits']['max_tesis'] ?? null]);

        view()->share('zsystemOrganization', $data['organization']);

        return $next($request);
    }
}
