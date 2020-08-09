<?php

namespace App\Service;

use App\Models\ImageFileInfo;
use App\Entities\ImageFileInfoEntity;
use App\Http\Forms\ImageFileInfoForm;

class ImageFileInfoService
{
    private static $_save_file_name = array();
    private static $_msgs = array();
    private static $_file_cont = 0;
    private static $dst_w = 0;
    private static $dst_h = 0;

    /**
     * 画像ファイル情報取得処理
     */
    public static function getImageFileInfos($key_type, $key_value)
    {
        $stored_rows = self::getStoredImageFileInfos($key_type, $key_value);
        // 保存済みのseqリスト
        $stored_seq_array = array_column($stored_rows, 'seq');
        // 返却用のエンティティリスト
        $image_file_infos = $stored_rows;

        for ($i = 0; $i <= JUNKISSA_IMAGE_MAX_SEQ; $i++) {
            if (!in_array($i, $stored_seq_array)) {
                $new_row = new ImageFileInfoEntity();
                $new_row->seq = $i;
                array_push($image_file_infos, $new_row);
            }
        }

        // seqでソート
        foreach ($image_file_infos as $row) {
            $seq_array[] = $row->seq;
        }
        array_multisort($seq_array, SORT_ASC, SORT_NUMERIC, $image_file_infos);

        return $image_file_infos;
    }

    /**
     * 画像ファイル情報レコード取得
     */
    public static function getStoredImageFileInfos($key_type, $key_value)
    {
        // 保存済みのファイル情報を取得
        $stored_rows = ImageFileInfo::getByKeyValue($key_type, (int)$key_value);
        // ファイルパス設定
        array_map('self::setFilePath', $stored_rows);

        return $stored_rows;
    }

    /**
     * 画像ファイルパス設定
     * @param ImageFileInfoEntity $image_file_info
     */
    private static function setFilePath($image_file_info)
    {
        switch ($image_file_info->key_type) {
            case IMAGE_KEY_TYPE_JUNKISSA :
                $image_file_info->file_name = IMAGE_FILE_PATH_JUNKISSA . $image_file_info->file_name;
                break;
            case IMAGE_KEY_TYPE_USER :
                $image_file_info->file_name = IMAGE_FILE_PATH_USER . $image_file_info->file_name;
                break;
            default:
                break;
        }
    }

    /**
     * 画像ファイルアップロード処理
     */
    public static function uploadImageFiles($key_type, $key_value, $params = array(), $is_check_only = false)
    {
        switch ($key_type) {
            case IMAGE_KEY_TYPE_JUNKISSA:
                if (self::saveJunkissaImages($key_type, $key_value, $params) !== true) {
                    return self::$_msgs;
                } else {
                    return true;
                }

            case IMAGE_KEY_TYPE_USER:
                if (self::saveUserImage($key_type, $key_value, $is_check_only) !== true) {
                    return self::$_msgs;
                } else {
                    return true;
                }

            default:
                return false;
        }
    }

    /**
     * 純喫茶画像ファイル保存処理
     */
    private static function saveJunkissaImages($key_type, $key_value, $params = array())
    {
        // 入力項目のバリデーション
        if (($validateError = ImageFileInfoForm::validate($params)) !== true) {
            // 入力不備あり
            self::$_msgs = $validateError;
            // バリデーションがエラーでも後続のファイルチェックを行うため処理続行する
        }

        // ファイルチェック
        if (!self::checkUplodeImageFiles($key_type, JUNKISSA_IMAGE_MAX_SEQ)) return false;

        // ファイルがPOSTされている場合
        if (self::$_file_cont > 0) {
            // ファイルアップロード
            if (!self::saveImageFiles(IMAGE_KEY_TYPE_JUNKISSA, $key_value, JUNKISSA_IMAGE_MAX_SEQ)) {
                self::$_msgs += array(ERR_TYPE_FILE_UPLOAD);
                return false;
            }
        }
            // 画像ファイル情報テーブルへ保存(DB)
            if (!self::upsertImageFileInfoData($key_type, $key_value, $params, JUNKISSA_IMAGE_MAX_SEQ)) {
                self::$_msgs += array(ERR_TYPE_DATA_COMMIT);
                return false;
            }

        if (count(self::$_msgs) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * ユーザ画像(アイコン)ファイル保存処理
     */
    private static function saveUserImage($key_type, $key_value, $is_check_only)
    {
        // ファイルチェック
        if (!self::checkUplodeImageFiles($key_type) || $is_check_only) return false;

        // ファイルがPOSTされている場合
        if (self::$_file_cont > 0) {
            // ファイルアップロード
            if (!self::saveImageFiles(IMAGE_KEY_TYPE_USER, $key_value)) {
                self::$_msgs += array(ERR_TYPE_FILE_UPLOAD);
                return false;
            }

            // 画像ファイル情報テーブルへ保存(DB)
            if (!self::upsertImageFileInfoData($key_type, $key_value)) {
                self::$_msgs += array(ERR_TYPE_DATA_COMMIT);
                return false;

            };
        }

        if (count(self::$_msgs) > 0) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * ファイルチェック
     */
    private static function checkUplodeImageFiles($key_type, $max_file_count = 0)
    {
        $files = $_FILES['images'];
        $name = 'bin';
        self::setImageSizeBasis($key_type);

        for ($i = 0; $i <= $max_file_count; $i++) {

            if (isset($files['error'][$i])) {

                // エラー情報取得
                $file_error_info = $files['error'][$i][$name];
                if (!isset($file_error_info) || !is_int($file_error_info)) {
                    self::$_msgs += array('image_' . $i => 'パラメータが不正です');
                    continue;
                }
                // エラーチェック
                switch ($file_error_info) {
                    case UPLOAD_ERR_OK:         // OK
                        self::$_file_cont++;
                        break;
                    case UPLOAD_ERR_NO_FILE:    // ファイル未選択
                        continue 2;
                    case UPLOAD_ERR_INI_SIZE:   // php.ini定義の最大サイズ超過
                    case UPLOAD_ERR_FORM_SIZE:  // フォーム定義の最大サイズ超過
                        self::$_msgs += array('image_' . $i => 'ファイルサイズが大きすぎます');
                    default:
                        self::$_msgs += array('image_' . $i => 'ファイル送信時にエラーが発生しました');
                }

                // サイズ情報取得
                $file_size_info = @getimagesize($files['tmp_name'][$i][$name]);
                // MIMEタイプチェック
                if (!$file_size_info) {
                    self::$_msgs += array('image_' . $i => '有効な画像ファイルを指定してください');
                    continue;
                } else {
                    if (!in_array($file_size_info[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                        self::$_msgs += array('image_' . $i => '画像ファイルはGIF, JPEG, PNG形式を指定してください');
                        continue;
                    }
                }
                // サイズチェック
                if ($file_size_info[0] < self::$dst_w ||  $file_size_info[1] < self::$dst_h) {
                    self::$_msgs += array('image_' . $i => sprintf('%s✕%sピクセル以上の画像を選択してください', self::$dst_w, self::$dst_h));
                    continue;
                }

            } else {
                self::$_msgs += array('message' => 'パラメータが不正です');
                break;
            }
        }

        if (count(self::$_msgs) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * ファイルアップロード
     */
    private static function saveImageFiles($key_type, $key_value, $max_file_count = 0)
    {
        $files = $_FILES['images'];
        $name = 'bin';
        $image_file_path = '';
        switch ($key_type) {
            case IMAGE_KEY_TYPE_JUNKISSA:
                $image_file_path = IMAGE_FILE_PATH_JUNKISSA;
                break;
            case IMAGE_KEY_TYPE_USER:
                $image_file_path = IMAGE_FILE_PATH_USER;
                break;
            default:
                break;
        }

        for ($i = 0; $i <= $max_file_count; $i++) {

            // ファイル選択チェック
            if ($files['error'][$i][$name] !== UPLOAD_ERR_OK) continue;

            // ファイル情報取得
            $tmp_name_info = $files['tmp_name'][$i][$name];
            $file_size_info = @getimagesize($tmp_name_info);

            // 処理する関数名をMIMEタイプから決定する
            // 画像ファイル用リソース生成用関数
            $create_resource_method = str_replace('/', 'createfrom', $file_size_info['mime']);
            // 画像ファイル出力用関数
            $output_image_file_method = str_replace('/', '', $file_size_info['mime']);

            // オリジナル画像のサイズ取得
            list($org_w, $org_h) = self::getOriginalSize($tmp_name_info, $file_size_info);
            // リサイズ
            list($dst_w, $dst_h) = self::calcResizeImageParam($org_w, $org_h);

            // 元画像リソースを生成する
            $img_src = @$create_resource_method($tmp_name_info);
            if (!$img_src) {
                return false;
            }

            // 回転を補正
            $img_src = self::imageOrientation($img_src, $tmp_name_info);

            // サムネイル画像リソースを生成する
            $dst = imagecreatetruecolor($dst_w, $dst_h);

            // pngファイルの透過部分を保持する
            // アルファブレンディングを無効
            imagealphablending($dst, false);
            // アルファフラグを設定
            imagesavealpha($dst, true);

            // サムネイルを生成する
            imagecopyresampled($dst, $img_src, 0, 0, 0, 0, $dst_w, $dst_h, $org_w, $org_h);

            // ファイル名
            $save_file_name = sprintf('%s_%s_%s_%s%s',
                                        $key_value,
                                        $i,
                                        date('Ymd_His'),
                                        sha1(uniqid(mt_rand(), true)),
                                        image_type_to_extension($file_size_info[2])
                                    );
            // ファイル保存実行
            $result = $output_image_file_method($dst,
                                                sprintf('%s%s',
                                                            __DIR__ . '/../../public' . $image_file_path,
                                                            $save_file_name
                                                        )
                                                );

            if ($result) {
                self::$_save_file_name += array($i => $save_file_name);
            } else {
                return false;
            }

            // リソース解放
            if (isset($img_src) && is_resource($img_src)) {
                imagedestroy($img_src);
            }
            if (isset($dst) && is_resource($dst)) {
                imagedestroy($dst);
            }
        }

        return true;
    }

    /**
     * 画像ファイル情報テーブルへ保存
     */
    private static function upsertImageFileInfoData($key_type, $key_value, $params = array(), $max_file_count = 0)
    {
        $stored_rows = ImageFileInfo::getByKeyValue($key_type, (int)$key_value);
        $stored_seq_array = array_column($stored_rows, 'seq');
        // upsert用のエンティティリスト
        $upsert_rows = array();
        for ($i = 0; $i <= $max_file_count; $i++) {

            $input_cap = isset($params[$i]) && $params[$i]['caption'] !== '' ? $params[$i]['caption'] : null;
            $upload_file_name = isset(self::$_save_file_name[$i]) ? self::$_save_file_name[$i] : null;

            if (in_array($i, $stored_seq_array)) {
                // 既存のファイル情報レコードあり

                if (is_null($upload_file_name)) {
                    // 画像アップなし
                    $upd_row = $stored_rows[array_search($i, array_column($stored_rows, 'seq'))];

                    if ($upd_row->caption !== $input_cap) {
                        // キャプションが変更されている場合: update
                        $upd_row->caption = $input_cap;
                        array_push($upsert_rows, $upd_row);
                    }
                } else {
                    // 画像アップあり
                    $new_row = self::createNewRow($key_type, $key_value, $i, $upload_file_name, $input_cap);
                    array_push($upsert_rows, $new_row);

                }
            } else {
                // 既存のファイル情報レコードなし

                if (!is_null($upload_file_name)) {
                    // 画像アップあり
                    $new_row = self::createNewRow($key_type, $key_value, $i, $upload_file_name, $input_cap);
                    array_push($upsert_rows, $new_row);
                }
            }
        }
        // DB保存処理
        $result_upsert = ImageFileInfo::upsert($upsert_rows);
        if (!$result_upsert) {
            // self::$_msgs += array('message' => 'ファイル保存時にエラーが発生しました(データベースエラー)');
            return false;
        } else {
            return true;
        };
    }

    /**
     * 新規DB登録のエンティティ作成
     */
    private static function createNewRow($key_type, $key_value, $seq, $upload_file_name, $input_cap)
    {
        $new_row = new ImageFileInfoEntity();
        $new_row->key_type = $key_type;
        $new_row->key_value = $key_value;
        $new_row->seq = $seq;
        $new_row->file_name = $upload_file_name;
        $new_row->caption = $input_cap;
        return $new_row;
    }

    /**
     * サムネイル画像のサイズ設定
     */
    private static function setImageSizeBasis($key_type)
    {
        switch ($key_type) {
            case IMAGE_KEY_TYPE_JUNKISSA:
                self::$dst_w = JUNKISSA_IMAGE_MIN_WIDTH;
                self::$dst_h = JUNKISSA_IMAGE_MIN_HIGHT;
                break;
            case IMAGE_KEY_TYPE_USER:
                self::$dst_w = USER_IMAGE_MIN_WIDTH;
                self::$dst_h = USER_IMAGE_MIN_HIGHT;
                break;
            default:
                break;
        }
    }

    /**
     * 画像のリサイズ
     */
    private static function calcResizeImageParam($org_w, $org_h)
    {
        $dst_aspect = round(self::$dst_w / self::$dst_h, 3);
        $org_aspect = round($org_w / $org_h, 3);
        // リサイズ画像の大きさ
        $dst_w = 0;
        $dst_h = 0;

        if ($org_aspect > $dst_aspect) {
            // 規定のサイズより横長の場合 : 幅基準
            $dst_w = self::$dst_w;
            $dst_h = ceil($org_h * (self::$dst_w / $org_w));
        } else {
            // 規定のサイズより縦長 or 正方形 : 高さ基準

            $dst_h = self::$dst_h;
            $dst_w = ceil($org_w * (self::$dst_h / $org_h));
        }

        return array($dst_w, $dst_h);
    }

    /**
     * 画像のトリム
     */
    private static function calcTrimImageParam($org_w, $org_h)
    {
        // 前提 : サイズチェック クリア
        // $org_w >= self::$dst_w && $org_h >= self::$dst_h
        $dst_aspect = round(self::$dst_w / self::$dst_h, 3);
        $org_aspect = round($org_w / $org_h, 3);
        // 元画像から切り取るサイズ
        $src_w = 0;
        $src_h = 0;
        // 切り取り開始位置
        $src_x = 0;
        $src_y = 0;
        if ($org_aspect > $dst_aspect) {
            // 高さ基準
            $src_h = $org_h;
            $src_w = ceil($org_h * (self::$dst_w / self::$dst_h));
            $src_x = ceil(($org_w - $src_w) * 0.5);
        } else {
            // 幅基準
            $src_w = $org_w;
            $src_h = ceil($org_w * (self::$dst_h / self::$dst_w));
            $src_y = ceil(($org_h - $src_h) * 0.5);
        }
        return array($src_x, $src_y, $src_w, $src_h);
    }

    /**
     * 回転パラメータ取得
     */
    private static function getOrientationParam($tmp_name_info)
    {
        $exif = @exif_read_data($tmp_name_info, 0, true);
        $orientation = null;
        if ($exif && isset($exif['IFD0']['Orientation'])) {
            $orientation = $exif['IFD0']['Orientation'];
        }
        return $orientation;
    }

    /**
     * 縦横の入れ替え
     */
    private static function switchWH($orientation, $w, $h)
    {
        switch ($orientation) {
            case 5:
            case 6:
            case 7:
            case 8:
                list($w, $h) = array($h, $w);
                break;
            default:
                break;
        }
        return array($w, $h);
    }

    /**
     * オリジナル画像のサイズ取得
     */
    private static function getOriginalSize($tmp_name_info, $file_size_info)
    {
        $orientation = self::getOrientationParam($tmp_name_info);
        return self::switchWH($orientation, $file_size_info[0], $file_size_info[1]);

    }

    /**
     * 画像の回転
     */
    private static function imageOrientation($image, $tmp_name_info)
    {
        // 回転角度
        $degrees = 0;

        switch(self::getOrientationParam($tmp_name_info)) {
            case 1:		// 回転なし（↑）
                break;
            case 8:		// 右に90度（→）
                $degrees = 90;
                break;
            case 3:		// 180度回転（↓）
                $degrees = 180;
                break;
            case 6:		// 右に270度回転（←）
                $degrees = 270;
                break;
            case 2:		// 反転（↑）
                $mode = IMG_FLIP_HORIZONTAL;
                break;
            case 7:		// 反転して右90度（→）
                $degrees = 90;
                $mode = IMG_FLIP_HORIZONTAL;
                break;
            case 4:		// 反転して180度なんだけど縦反転と同じ（↓）
                $mode = IMG_FLIP_VERTICAL;
                break;
            case 5:		// 反転して270度（←）
                $degrees = 270;
                $mode = IMG_FLIP_HORIZONTAL;
                break;
            default:
                break;
        }

        // 反転(2,7,4,5)
        if (isset($mode)) {
            $image = imageflip($image, $mode);
        }
        // 回転(8,3,6,7,5)
        if ($degrees > 0) {
            $image = imagerotate($image, $degrees, 0);
        }

        return $image;
    }
}