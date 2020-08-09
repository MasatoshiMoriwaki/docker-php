
    CREATE TABLE IF NOT EXISTS `image_file_infos` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `key_type` int NOT NULL COMMENT 'キータイプ(1:junkissa, 2:user)',
        `key_value` int NOT NULL COMMENT 'キーバリュー',
        `seq` int NOT NULL DEFAULT 1 COMMENT 'シーケンス',
        `file_name` varchar(255) NOT NULL COMMENT 'ファイル名',
        `caption` varchar(40) COMMENT 'キャプション',
        `active` int NOT NULL DEFAULT '1' COMMENT 'アクティブ',
        `created_by` int NULL COMMENT '登録ユーザid',
        `created_at` timestamp NULL COMMENT '登録日時',
        `updated_by` int NULL COMMENT '更新ユーザid',
        `updated_at` timestamp NULL COMMENT '更新日時',
        PRIMARY KEY (`id`)
    )
    default character set utf8mb4
    collate 'utf8mb4_unicode_ci';