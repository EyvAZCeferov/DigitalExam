<?php

return [
    'supportedLocales' => [
        'az'          => ['name' => 'AZE',    'script' => 'Latn', 'native' => 'azərbaycanca', 'regional' => 'az_AZ'],
        'en'          => ['name' => 'EN',                'script' => 'Latn', 'native' => 'English', 'regional' => 'en_GB'],
        // 'ru'          => ['name' => 'Russian',                'script' => 'Cyrl', 'native' => 'русский', 'regional' => 'ru_RU'],
    ],
    'useAcceptLanguageHeader' => true,
    'hideDefaultLocaleInURL' => false,
    'localesOrder' => [
        'az',
        // 'ru',
        'en'
    ],
    'localesMapping' => [
        
    ],
    'utf8suffix' => env('LARAVELLOCALIZATION_UTF8SUFFIX', '.UTF-8'),
    'urlsIgnored' => ['/skipped'],
    'httpMethodsIgnored' => ['POST', 'PUT', 'PATCH', 'DELETE'],
    'defaultLocale' => 'az',
];
