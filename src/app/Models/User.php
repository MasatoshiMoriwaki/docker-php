<?php

namespace App\Models;

use Framework\DB\Model;
use App\Entities\UserEntity;

class User extends Model
{
    public static $_table_name = 'users';

    // 更新対象外カラム
    protected static $_columns_non_target_insert = [];

    public static function getByEmail($email)
    {
        self::instance();
        $result = self::where('email', '=', $email);
        return empty($result[0]) ? null : $result[0];
    }

    public static function checkVerificationCodeIsUnique($code)
    {
        self::instance();
        $sql = "select count(*) from " . self::getTableName() . " where new_email_verification_code = ?";
        $stmt = self::execute($sql, array($code));
        return $stmt->fetchColumn() > 0 ? false : true;
    }

    public static function saveNewEmailInfo($user)
    {
        self::instance();

        try {
            self::connect();
            // トランザクション開始
            self::$_pdo->beginTransaction();

                try {
                    $sql = self::updateNewEmailInfoSql();
                    $stmt = self::$_pdo->prepare($sql);
                    $stmt->bindValue(':new_email', $user->new_email, \PDO::PARAM_STR);
                    $stmt->bindValue(':new_email_verification_code', $user->new_email_verification_code, \PDO::PARAM_STR);
                    $stmt->bindValue(':id', $user->id, \PDO::PARAM_STR);
                    $stmt->execute();

                }  catch (\PDOException $e) {

                    // ロールバック
                    self::$_pdo->rollBack();

                    throw $e;
                }
            // コミット
            self::$_pdo->commit();
        }  catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    public static function activateNewEmail($verification_code, $expiration_minites)
    {
        self::instance();

        try {
            self::connect();
            // トランザクション開始
            self::$_pdo->beginTransaction();

                try {
                    $sql = self::updateActivateNewEmailSql();
                    $stmt = self::$_pdo->prepare($sql);
                    $stmt->bindValue(':new_email_verification_code', $verification_code, \PDO::PARAM_STR);
                    $stmt->bindValue(':exp_min', $expiration_minites, \PDO::PARAM_INT);
                    $stmt->execute();

                }  catch (\PDOException $e) {

                    // ロールバック
                    self::$_pdo->rollBack();

                    throw $e;
                }
            // updateする行数が複数ある場合
            if ($stmt->rowCount() > 1) {
                // ロールバック
                self::$_pdo->rollBack();
                return false;
            }
            // update対象がない場合
            if ($stmt->rowCount() == 0) {
                return false;
            }
            // コミット
            self::$_pdo->commit();
        }  catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    private static function updateNewEmailInfoSql()
    {
        $sql = <<<EOF
                update users
                    set new_email = :new_email,
                    new_email_verification_code = :new_email_verification_code,
                    new_email_registered_at = now()
                where id = :id and active = 1;
        EOF;

        return $sql;
    }

    private static function updateActivateNewEmailSql()
    {
        $sql = <<<EOF
                update users
                    set email = new_email,
                    new_email = null,
                    new_email_verification_code = null,
                    new_email_registered_at = null
                where
                    new_email_verification_code = :new_email_verification_code
                and new_email_registered_at + INTERVAL :exp_min MINUTE >= now();
        EOF;

        return $sql;
    }
}
