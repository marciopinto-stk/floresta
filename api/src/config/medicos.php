<?php

return [
    'productivity' => [
        'max_upload_kb' => env('MEDICOS_PRODUCTIVITY_MAX_UPLOAD_KB', 2048),
        'delimiter' => ';',
        'required_headers' => [
            'AccessionNumber',
            'DataLaudo',
            'ProfissionalIdS2'
        ],
    ],
];
