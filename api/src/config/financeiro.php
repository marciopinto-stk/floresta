<?php
return [
    'atendimento_externo' => [
        'endpoint'  => env('FIN_ATENDIMENTO_EXTERNO_ENDPOINT'),
        'token'     => env('FIN_ATENDIMENTO_EXTERNO_TOKEN'),
        'timeout'   => (int) env('FIN_ATENDIMENTO_EXTERNO_TIMEOUT', 60),
        'chunk'     => (int) env('FIN_ATENDIMENTO_EXTERNO_CHUNK', 50),
    ],

    'enviar_invoice_oracle' => [
        'endpoint'  => env('FIN_ENVIAR_NOTA_ORACLE_ENDPOINT'),
        'token'     => env('FIN_ATENDIMENTO_EXTERNO_TOKEN'),
        'timeout'   => (int) env('FIN_ATENDIMENTO_EXTERNO_TIMEOUT', 60),
        'chunk'     => (int) env('FIN_ATENDIMENTO_EXTERNO_CHUNK', 50),
    ],
];
