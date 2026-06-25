<?php

return [
    'store' => [
        'lat' => env('STORE_LAT', -8.1191),
        'lng' => env('STORE_LNG', -79.0330),
        'address' => 'Los Cedros 154, Víctor Larco Herrera 13009',
    ],
    'rates' => [
        ['max_distance' => 3, 'price' => 5.00],
        ['max_distance' => 7, 'price' => 8.00],
        ['max_distance' => 12, 'price' => 12.00],
        ['max_distance' => 20, 'price' => 18.00],
        ['max_distance' => PHP_INT_MAX, 'price' => 25.00],
    ],
    'bounds' => [
        'lat_min' => -8.50,
        'lat_max' => -7.50,
        'lng_min' => -79.50,
        'lng_max' => -78.00,
    ],
];
