<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = $request->user();

        $canAccess = match ($permission) {
            'manage-users' => $user->canManageUsers(),
            'manage-products' => $user->canManageProducts(),
            'process-refunds' => $user->canProcessRefunds(),
            'manage-gateways' => $user->canManageGateways(),
            default => false,
        };

        if (!$canAccess) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}

