<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token non fornito'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(config('app.key'), 'HS256'));
            // Puoi iniettare l'utente trovato nel payload della richiesta
            $request->merge(['user_id' => $decoded->sub]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token non valido o scaduto'], 401);
        }


        return $next($request);
    }
}
