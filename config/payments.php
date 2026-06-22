<?php

return [
    'yape' => [
        'phone' => env('YAPE_PHONE', '950207553'),
        'token' => env('YAPE_TOKEN', '0002010102113944xB3OUUH8sSyMnW2MTDK4Kz39zx0n6qhY78AfFg1sCEs=5204561153036045802PE5906YAPERO6004Lima6304F349'),
        'qr_url' => env('YAPE_QR_URL', 'https://api.qrserver.com/v1/create-qr-code/'),
    ],
    'plin' => [
        'phone' => env('PLIN_PHONE', '950207553'),
        'token' => env('PLIN_TOKEN', '0002015802PE26560116Plin Network P2P0032c2d68ce0d4ed46a8b3bff537a9bfbb730102115204482953036045912P2P Transfer6004Lima63040A2D'),
        'qr_url' => env('PLIN_QR_URL', 'https://api.qrserver.com/v1/create-qr-code/'),
    ],
    'bank' => [
        'bcp' => [
            'account_number' => env('BCP_ACCOUNT', '191-12345678-0-12'),
            'cci' => env('BCP_CCI', '00219100123456780129'),
        ],
        'interbank' => [
            'account_number' => env('INTERBANK_ACCOUNT', '123-4567890123'),
            'cci' => env('INTERBANK_CCI', '00312345678901234567'),
        ],
    ],
    'delivery_fee' => env('DELIVERY_FEE', 8),
];
