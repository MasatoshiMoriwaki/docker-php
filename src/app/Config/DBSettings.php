<?php

namespace App\Config;

use App\Models as Models;

class DBSettings
{
    public const DB_DRIVER = 'mysql';
    public const DB_HOST = 'mysql';
    public const DB_CHARSET = 'utf8mb4';

    public const DB_NAME = 'junkissa_club_db';
    public const DB_USER = 'root';
    public const DB_PASS = 'example_password';

    public const DB_OPTIONS =
        [
            [\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION],
            [\PDO::ATTR_EMULATE_PREPARES, false]
        ];

    public const MODELS_TABLE =
    [
        [
            'key' => 'junkissa',
            'table_name' => 'junkissas',
            'model' => Models\Junkissa::class
        ],
        [
            'key' => 'prefecture',
            'table_name' => '_mst_prefectures',
            'model' => Models\Mst\Prefecture::class
        ],
        [
            'key' => 'area',
            'table_name' => '_mst_areas',
            'model' => Models\Mst\Area::class
        ]
    ];
}