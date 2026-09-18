<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IntegrationToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $token = $request->bearerToken();

        $expectedToken = config(
            'services.maviko_integration.token'
        );

        if (
            !$token ||
            !$expectedToken ||
            !hash_equals($expectedToken, $token)
        ) {
            return response()->json([
                'message' => 'No autorizado.',
            ], 401);
        }

        return $next($request);
    }

}
