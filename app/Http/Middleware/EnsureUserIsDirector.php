<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsDirector
{
    public function handle(Request $request, Closure $next): Response
    {
        // Seul le directeur (role=director) a accès
        if (!$request->user() || $request->user()->role !== 'director') {
            return response()->json([
                'message' => 'Accès réservé au directeur du labo.',
                'error' => 'Permissions insuffisantes.'
            ], 403);
        }

        return $next($request);
    }
}
