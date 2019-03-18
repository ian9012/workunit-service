<?php

return [
    'timetrack_validator' => [
        'regex_duration' => [
            'h' => [
                'h_pattern' => '/^(\d)+h$/',
                'valid_pattern' => '/^([1-9]|1[0-2])h$/'
            ],
            'm' => [
                'm_pattern' => '/^(\d)+m$/',
                'valid_pattern' => '/^([1-9]|[1-5][0-9])m$/'
            ],
            'h_m' => [
                'h_m_pattern' => '/^(\d)+h(\d)+m$/',
                'valid_pattern' => '/^([1-9]|1[0-2])h([1-9]|[1-5][0-9])m$/'
            ],
        ]
    ]
];
