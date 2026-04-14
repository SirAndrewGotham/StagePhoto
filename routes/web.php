<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

// Language switching route
Route::get('/lang/{locale}', function ($locale) {
    $supportedLocales = ['ru', 'en', 'eo'];

    if (in_array($locale, $supportedLocales)) {
        // Set cookie
        cookie()->queue('language', $locale, 60 * 24 * 365);

        // Set session
        session(['locale' => $locale]);

        // Set application locale
        app()->setLocale($locale);
    }

    // Redirect back to previous page
    return redirect()->back();
})->name('lang.switch');

Route::livewire('/', 'frontend.pages.⚡home')->name('home');
Route::livewire('/album/{album:slug}', 'frontend.pages.⚡album-show')->name('album.show');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
