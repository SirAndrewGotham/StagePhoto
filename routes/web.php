<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/debug-files', function (): array {
    $basePath = resource_path('views/components/frontend/pages/legal/');
    $files = scandir($basePath);

    $result = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $result[] = $file;
        }
    }

    return [
        'directory' => $basePath,
        'files_found' => $result,
        'current_locale' => app()->getLocale(),
        'expected_file' => '⚡terms.'.app()->getLocale().'.blade.php',
        'file_exists' => file_exists($basePath.'⚡terms.'.app()->getLocale().'.blade.php'),
    ];
});

// Language switching route
Route::get('/lang/{locale}', function ($locale) {
    $supportedLocales = ['ru', 'en', 'eo'];
    if (in_array($locale, $supportedLocales)) {
        cookie()->queue('language', $locale, 60 * 24 * 365);
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

// Auth Routes (Livewire 4 SFC)
Route::livewire('/login', 'frontend.auth.⚡login')->name('login');
Route::livewire('/register', 'frontend.auth.⚡register')->name('register');
Route::livewire('/forgot-password', 'frontend.auth.⚡forgot-password')->name('password.request');
Route::livewire('/reset-password/{token}', 'frontend.auth.⚡reset-password')->name('password.reset');

// Logout route
Route::post('/logout', function (): Redirector|RedirectResponse {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Public Routes
Route::livewire('/', 'frontend.pages.⚡home')->name('home');
Route::livewire('/photo/{photo}', 'frontend.pages.photo-show')->name('photo.show');
Route::livewire('/albums', 'frontend.pages.albums-index')->name('albums.index');
Route::livewire('/album/{album:slug}', 'frontend.pages.⚡album-show')->name('album.show');

// Protected routes for photographers
Route::middleware(['auth'])->group(function () {
    Route::livewire('/upload', 'frontend.pages.⚡photo-upload')->name('photo.upload');
    Route::livewire('/upload/multiple', 'frontend.pages.⚡multiple-photo-upload')->name('photo.upload.multiple');
    Route::livewire('/upload/zip', 'frontend.pages.⚡zip-photo-upload')->name('photo.upload.zip');
    Route::livewire('/trash', 'frontend.pages.⚡trash-manager')->name('trash.manager');
});

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';

// Legal pages - Using Livewire SFC
Route::livewire('/terms', 'frontend.pages.legal.terms')->name('terms');
Route::livewire('/privacy', 'frontend.pages.legal.privacy')->name('privacy');
Route::livewire('/guidelines', 'frontend.pages.legal.guidelines')->name('guidelines');
Route::livewire('/copyright', 'frontend.pages.legal.copyright')->name('copyright');
Route::livewire('/cookies', 'frontend.pages.legal.cookies')->name('cookies');

// Info pages
Route::livewire('/for-bands', 'frontend.pages.legal.for-bands')->name('for-bands');
Route::livewire('/for-theaters', 'frontend.pages.legal.for-theaters')->name('for-theaters');
Route::livewire('/photographer-guide', 'frontend.pages.legal.photographer-guide')->name('photographer-guide');
