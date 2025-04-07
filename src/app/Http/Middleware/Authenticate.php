<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Verifica se tem token
            if (!$token = JWTAuth::parseToken()) {
                return response()->json(['error' => 'Token não fornecido'], 401);
            }

            // Tenta autenticar
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], 401);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido ou expirado'], 401);
        }

        return $next($request);
    }
}
