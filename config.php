<?php

return [
    'routes' => [
        'static' => [
            'admin/orders/fulfillment/printful' => 'lightningsdk\\checkout_printful\\Pages\\Fulfillment',
        ]
    ],
    'modules' => [
        'checkout' => [
            'fulfillment_handlers' => [
                'printful' => 'lightningsdk\\checkout_printful\\Connector\\Checkout',
            ]
        ]
    ]
];
