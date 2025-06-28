<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        Log::info('API Activity', [
            'user_id' => optional($request->user())->id,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'status' => $response->status(),
            'payload' => $request->all()
        ]);

        return $response;
    }
}

