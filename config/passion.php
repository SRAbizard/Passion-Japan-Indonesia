<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    | Indonesia (id) is the default. Adding a new locale here propagates to the
    | language switcher, the SetLocale middleware whitelist, and any UI list.
    */
    'locales' => [
        'id' => ['label' => 'Bahasa Indonesia', 'flag' => '🇮🇩', 'native' => 'Indonesia'],
        'en' => ['label' => 'English',           'flag' => '🇬🇧', 'native' => 'English'],
        'ja' => ['label' => '日本語',             'flag' => '🇯🇵', 'native' => '日本語'],
    ],

    'default_locale' => 'id',

    /*
    | Roles seeded by RolesAndPermissionsSeeder. The first one in the list is
    | considered the most privileged.
    */
    'roles' => ['superadmin', 'admin', 'student'],

    /*
    | First superadmin credentials, used by DatabaseSeeder for local bootstrap.
    | Override via env in any non-local environment.
    */
    'bootstrap_admin' => [
        'name'     => env('BOOTSTRAP_ADMIN_NAME', 'Super Admin'),
        'email'    => env('BOOTSTRAP_ADMIN_EMAIL', 'admin@passionjapan.id'),
        'password' => env('BOOTSTRAP_ADMIN_PASSWORD', 'password'),
    ],

    /*
    | Public-facing contact / company info — used in footer, WhatsApp float,
    | meta tags. Migrate to Setting model in Phase 3 (Website Settings CMS).
    */
    'contact' => [
        'whatsapp'  => '62882007885021',
        'phone'     => '+62 882-0078-85021',
        'email'     => 'info@passionjapan.id',
        'instagram' => 'https://instagram.com/passionjapan',
        'facebook'  => 'https://facebook.com/passionjapan',
        'tiktok'    => 'https://tiktok.com/@passionjapan',
        'offices'   => [
            ['city' => 'Yokohama', 'country' => 'Japan',     'address' => 'Yokohama, Kanagawa, Japan'],
            ['city' => 'Brebes',   'country' => 'Indonesia', 'address' => 'Brebes, Jawa Tengah, Indonesia'],
            ['city' => 'Yogyakarta','country' => 'Indonesia','address' => 'Yogyakarta, DI Yogyakarta, Indonesia'],
        ],
    ],

    /*
    | Headline stats shown on the homepage. Phase 3 will pull live counts.
    */
    'stats' => [
        'students'  => '3,300+',
        'workers'   => '1,200+',
        'companies' => '100+',
    ],
];
