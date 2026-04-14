<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Check cookie first, then session, then default
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

        // Add to response cookies if not present
        if (! $request->cookie('language')) {
            Cookie::queue('language', $locale, 60 * 24 * 365); // 1 year
        }

        return $next($request);
    }
}
