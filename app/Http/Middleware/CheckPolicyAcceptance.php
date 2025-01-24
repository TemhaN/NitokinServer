<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Policy;

class CheckPolicyAcceptance
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        $latestPolicies = Policy::all();
        foreach ($latestPolicies as $policy) {
            if (!$user->policies->contains($policy->id)) {
                return response()->json([
                    'message' => 'Вы должны принять последнюю версию политики',
                    'policy' => $policy,
                ], 403);
            }
        }

        return $next($request);
    }
}