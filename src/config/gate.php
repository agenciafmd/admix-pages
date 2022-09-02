<?php

return [
    [
        'name' => config('local-payments.name'),
        'policy' => '\Agenciafmd\Payments\Policies\PaymentPolicy',
        'abilities' => [
            [
                'name' => 'visualizar',
                'method' => 'view',
            ],
            [
                'name' => 'criar',
                'method' => 'create',
            ],
            [
                'name' => 'atualizar',
                'method' => 'update',
            ],
            [
                'name' => 'deletar',
                'method' => 'delete',
            ],
            [
                'name' => 'restaurar',
                'method' => 'restore',
            ],
        ],
        'sort' => 10,
    ],
];
