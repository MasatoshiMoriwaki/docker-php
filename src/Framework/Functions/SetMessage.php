<?php

namespace Framework\Functions;

trait SetMessage
{
    /**
     * エラーメッセージをセットする
     */
    protected function setErrMessage($err)
    {

        if (!isset($err) || !is_array($err)){
            return;
        }

        // データ保存エラー
        if (in_array(ERR_TYPE_DATA_COMMIT, $err)) {
            $this->setDataCommitErrMessage();
            return;
        }

        // ファイルアップロードエラー
        if (in_array(ERR_TYPE_FILE_UPLOAD, $err)) {
            $this->setFileUploadErrMessage();
            return;
        }
        // 入力エラー
        $this->setInputErrMessage();
    }

    /**
     * 入力エラー
     */
    protected function setInputErrMessage()
    {
        $this->setMessage(MSG_TYPE_ERROR, '入力エラーがあります');
    }
    /**
     * データ保存エラー
     */
    protected function setDataCommitErrMessage()
    {
        $this->setMessage(MSG_TYPE_ERROR, 'データの登録に失敗しました');
    }

    /**
     * ファイルアップロードエラー
     */
    protected function setFileUploadErrMessage()
    {
        $this->setMessage(MSG_TYPE_ERROR, 'ファイルのアップロードに失敗しました');
    }

    /**
     * メール送信エラー
     */
    protected function setFaildSendEmailErrMessage()
    {
        $this->setMessage(MSG_TYPE_ERROR, 'メール送信に失敗しました');
    }
}