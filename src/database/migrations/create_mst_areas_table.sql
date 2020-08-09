
    CREATE TABLE IF NOT EXISTS `_mst_areas` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(20) NOT NULL COMMENT 'エリア名',
        `name_en` varchar(30) NULL,
        `active` INT NOT NULL DEFAULT 1 COMMENT 'アクティブ',
        `created_at` TIMESTAMP NULL COMMENT '登録日時',
        `updated_at` TIMESTAMP NULL COMMENT '更新日時'
    )
    default character set utf8mb4
    collate 'utf8mb4_unicode_ci';


    INSERT INTO `_mst_areas` VALUES
        (1, '北海道・東北', 'hokkaido_tohoku',      1, now(), now()),
        (2, '関東',         'kanto',                1, now(), now()),
        (3, '北陸・甲信越', 'hokuriku_kousinetsu',  1, now(), now()),
        (4, '東海',         'tokai',                1, now(), now()),
        (5, '近畿',         'kinki',                1, now(), now()),
        (6, '中国・四国',   'chugoku_shikoku',      1, now(), now()),
        (7, '九州・沖縄',   'kyusyu_okinawa',       1, now(), now());