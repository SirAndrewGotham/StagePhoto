<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::get('/debug-lang', fn () => [
    'cookie' => request()->cookie('language'),
    'session' => session('locale'),
    'app_locale' => app()->getLocale(),
    'all_cookies' => request()->cookies->all(),
]);

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

// Photo routes
Route::livewire('/photo/{photo}', 'frontend.pages.photo-show')->name('photo.show');

// Album routes
Route::livewire('/albums', 'frontend.pages.albums-index')->name('albums.index');
Route::livewire('/album/{album:slug}', 'frontend.pages.⚡album-show')->name('album.show');

// Home route
Route::livewire('/', 'frontend.pages.home');

// Protected routes for photographers
Route::middleware(['auth'])->group(function () {
    // Single photo upload
    Route::livewire('/upload', 'frontend.pages.photo-upload')->name('photo.upload');

    // Multiple photos upload
    Route::livewire('/upload/multiple', 'frontend.pages.multiple-photo-upload')->name('photo.upload.multiple');

    Route::livewire('/upload/zip', 'frontend.pages.zip-photo-upload')->name('photo.upload.zip');

    // Trash manager
    Route::livewire('/trash', 'frontend.trash-manager')->name('trash.manager');
});


// Protected routes for photographers
// Route::middleware(['auth'])->prefix('photographer')->name('photographer.')->group(function () {
//    Route::livewire('/upload', 'frontend.photo-upload')->name('upload');
//    Route::livewire('/albums', 'frontend.albums-index')->name('albums');
//    Route::livewire('/trash', 'frontend.trash-manager')->name('trash');
// });

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
