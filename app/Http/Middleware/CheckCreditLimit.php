<?php
namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCreditLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = $request->user()?->customer;

        if ($customer && ($customer->current_balance + $request->total_amount) > $customer->credit_limit) {
            return response()->json([
                'message' => 'Credit limit exceeded.'
            ], 403);
        }

        return $next($request);
    }
}

