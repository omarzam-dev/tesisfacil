<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->hasAccess()) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
