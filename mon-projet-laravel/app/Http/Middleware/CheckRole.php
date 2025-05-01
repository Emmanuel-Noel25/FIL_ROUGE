<?php
// app/Http/Middleware/CheckClientRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClientRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isClient()) {
            return response()->json(['message' => 'Non autorisé. Accès réservé aux clients.'], 403);
        }

        return $next($request);
    }
}

// app/Http/Middleware/CheckProviderRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProviderRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isProvider()) {
            return response()->json(['message' => 'Non autorisé. Accès réservé aux prestataires de services.'], 403);
        }

        return $next($request);
    }
}