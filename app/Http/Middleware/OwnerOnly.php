<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $employee = Auth::guard('employee')->user();

        if (! $employee || $employee->role !== 'owner') {
            abort(403, 'Access restricted to owners only.');
        }

        return $next($request);
    }
}