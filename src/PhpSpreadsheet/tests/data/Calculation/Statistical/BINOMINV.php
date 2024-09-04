<?php

declare(strict_types=1);

return [
    [
        32,
        100, 0.3, 0.7,
    ],
    [
        4,
        6, 0.5, 0.75,
    ],
    [
        7,
        12, 0.5, 0.75,
    ],
    [
        46,
        100, 0.5, 0.2,
    ],
    [
        50,
        100, 0.5, 0.5,
    ],
    [
        56,
        100, 0.5, 0.9,
    ],
    [
        '#VALUE!',
        'NaN', 0.5, 0.9,
    ],
    [
        '#VALUE!',
        100, 'NaN', 0.9,
    ],
    [
        '#VALUE!',
        100, 0.5, 'NaN',
    ],
    [
        '#NUM!',
        -1, 0.5, 0.9,
    ],
    [
        '#NUM!',
        100, -0.5, 0.9,
    ],
    [
        '#NUM!',
        100, 1.5, 0.9,
    ],
    [
        '#NUM!',
        100, 0.5, -0.9,
    ],
    [
        '#NUM!',
        100, 0.5, 1.9,
    ],
];
