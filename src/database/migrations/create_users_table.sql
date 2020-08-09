
    CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'ユーザ名',
    `sex` INT NULL COMMENT '性別',
    `profile` TEXT NULL COMMENT 'プロフィール',
    `web_page` TEXT NULL COMMENT 'ホームページ',
    `email` VARCHAR(255) NOT NULL COMMENT 'メールアドレス',
    `password` VARCHAR(255) NOT NULL COMMENT 'パスワード',
    `new_email` VARCHAR(255) NULL COMMENT 'メールアドレス(変更)',
    `new_email_verification_code` VARCHAR(255) NULL COMMENT 'メールアドレス変更の認証コード',
    `new_email_registered_at` TIMESTAMP NULL COMMENT '新規メールアドレス登録日時',
    `active` INT NOT NULL DEFAULT 1 COMMENT 'アクティブ',
    `last_login_at` TIMESTAMP NULL COMMENT '最終ログイン日時',
    `created_at` TIMESTAMP NULL COMMENT '登録日時',
    `updated_at` TIMESTAMP NULL COMMENT '更新日時',
    UNIQUE KEY `email` (`email`)
)
    default character set utf8mb4
    collate 'utf8mb4_unicode_ci';