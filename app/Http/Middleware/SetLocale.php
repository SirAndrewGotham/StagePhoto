<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Get locale from cookie, then session, then default
        $locale = $request->cookie('language');

        if (! $locale) {
            $locale = session('locale');
        }

        if (! $locale) {
            $locale = config('app.locale', 'ru');
        }

        // Validate locale
        $supportedLocales = ['ru', 'en', 'eo'];
        if (! in_array($locale, $supportedLocales)) {
            $locale = 'ru';
        }

        // Set the application locale
        app()->setLocale($locale);

        // Store in session for future requests
        session(['locale' => $locale]);

        // Also set in view for debugging
        view()->share('currentLocale', $locale);

        return $next($request);
    }
}
