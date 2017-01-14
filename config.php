<?php

return [
    'routes' => [
        'static' => [
            'admin/orders/fulfillment/theprintful' => 'Modules\\ThePrintful\\Pages\\Fulfillment',
        ]
    ],
    'modules' => [
        'checkout' => [
            'fulfillment_handlers' => [
                'printful' => 'Modules\\ThePrintful\\Connector\\Checkout',
            ]
        ]
    ]
];
