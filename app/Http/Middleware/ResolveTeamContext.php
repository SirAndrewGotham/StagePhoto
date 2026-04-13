<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTeamContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! request()->route('team')) {
            $user = auth()->user();
            if (! $user->currentTeam && $user->teams()->exists()) {
                $user->currentTeam()->associate($user->personalTeam());
                $user->save();
            }
        }

        return $next($request);
    }
}
