<?php

return [
    'cache' => [
        'key' => env('SITEMAP_CACHE_KEY', 'sitemap.xml'),
        'ttl_hours' => env('SITEMAP_CACHE_TTL_HOURS', 6),
    ],
];
