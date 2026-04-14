<?php

declare(strict_types=1);

namespace App\Helpers;

class LanguageHelper
{
    public static function getSupportedLocales(): array
    {
        return [
            'ru' => ['name' => 'Русский', 'flag' => 'RU', 'dir' => 'ltr'],
            'en' => ['name' => 'English', 'flag' => 'EN', 'dir' => 'ltr'],
            'eo' => ['name' => 'Esperanto', 'flag' => 'EO', 'dir' => 'ltr'],
        ];
    }

    public static function isRtl($locale): bool
    {
        return in_array($locale, ['ar', 'he', 'ur']);
    }
}
