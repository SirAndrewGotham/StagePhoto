<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Folio\Folio;

class FolioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //        Folio::path(resource_path('views/pages'))->middleware([
        //            '*' => [
        //                //
        //            ],
        //        ]);

        Folio::path(resource_path('views/pages/frontend'))
            ->uri('/')
            ->middleware([
                '*' => ['web'], // Apply to all Folio pages
            ]);

        Folio::path(resource_path('views/pages/admin'))
            ->uri('/admin')
            ->middleware([
                '*' => [
                    'auth',
                    'verified',

                    // ...
                ],
            ]);
    }
}
