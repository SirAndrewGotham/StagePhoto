<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\FolioServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
//    FolioServiceProvider::class,
//    Laravel\Folio\FolioServiceProvider::class,
    FortifyServiceProvider::class,
];
