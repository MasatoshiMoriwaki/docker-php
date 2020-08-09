<?php

namespace App\Http\Controllers\Junkissa;

use App\Http\Controllers\AppBase\AppBaseController;
use App\Models\Junkissa;
// use App\Models\ImageFileInfo;
use App\Models\Mst\Prefecture;
use App\Entities\JunkissaEntity;
use App\Http\Forms\JunkissaForm;
use App\Http\Forms\ImageFileInfoForm;
use App\Service\ImageFileInfoService;

class JunkissaController extends AppBaseController
{
    protected $auth_actions = array('edit', 'save', 'editImages', 'saveImages');

    public function index($param)
    {
        $junkissa = Junkissa::getById($param['junkissa_id']);
        if (empty($junkissa)) {
            $this->forward404();
        }
        // 画像
        $images = ImageFileInfoService::getStoredImageFileInfos(IMAGE_KEY_TYPE_JUNKISSA, $junkissa->id);
        // 保存→リダイレクト時にメッセージ表示
        $this->getSessionMsg('info_msg');

        return $this->render('junkissa/index', [
            'junkissa'  => $junkissa,
            'images'   => $images
        ]);
    }

    public function edit($param = null)
    {
        if($this->isNewData($param) === false) {
            // 更新
            $junkissa = Junkissa::getById($param['junkissa_id']);
            // junkissa_idをセッションにセット
            $this->session->set('junkissa_id', $junkissa->id);
        } else {
            // 新規登録
            $junkissa = new JunkissaEntity();
        }

        // 画面表示
        return $this->viewEdit($junkissa);
    }

    public function save($param)
    {
        // CSRFトークンチェック
        if (!$this->checkCsrfToken()) {
             // リダイレクト
             return $this->redirect('/');
        }

        if($this->isNewData($param) === false) {
            // junkissa_idをチェック
            $sess_junkissa_id = $this->session->get('junkissa_id');
            // junkissa_id URLとセッションのjunkissa_idが一致しない場合
            if (is_null($sess_junkissa_id) || $sess_junkissa_id !== $param['junkissa_id']) {
                // リダイレクト
                return $this->redirect('/');
            }

            // 更新データを取得
            $junkissa = Junkissa::getById($param['junkissa_id']);
            if (is_null($junkissa)) {
                // リダイレクト
                return $this->redirect('/');
            }
        } else {
            // 新規登録
            $junkissa = new JunkissaEntity();
        }

        // 入力項目を取得
        $input_values = $this->getInputValues();
        // エンティティにセット
        $inputed_junkissa = $this->setFormDataToEntity($junkissa, $input_values);

        // バリデーション
        if (($validateError = JunkissaForm::validate($input_values)) !== true) {
            // 入力不備あり
            $this->setInputErrMessage();
            return $this->viewEdit($inputed_junkissa, $validateError);
        }

        // 保存処理
        $junkissa = Junkissa::save($inputed_junkissa);

        if ($junkissa === false ) {
            //  model内のエラー
            $this->setDataCommitErrMessage();
            return $this->viewEdit($inputed_junkissa);
        }

        $this->setSessionMsg('info_msg', '純喫茶情報を保存しました。ありがとうございます！');

        $this->redirect('/junkissa/' . $junkissa->id);
    }

    public function editImages($param = null)
    {
        // 更新
        $junkissa = Junkissa::getById($param['junkissa_id']);
        // junkissa_idをセッションにセット
        $this->session->set('junkissa_id', $junkissa->id);

        $images = ImageFileInfoService::getImageFileInfos(IMAGE_KEY_TYPE_JUNKISSA, $junkissa->id);

        return $this->render('junkissa/edit', [
            'is_edit_images'    => true,
            'junkissa'          => $junkissa,
            'images'            => $images
        ]);
    }

    public function saveImages($param = null)
    {

        $stored_images = ImageFileInfoService::getImageFileInfos(IMAGE_KEY_TYPE_JUNKISSA, $param['junkissa_id']);

        // junkissa_idをチェック
        $sess_junkissa_id = $this->session->get('junkissa_id');
        // junkissa_id URLとセッションのjunkissa_idが一致しない場合
        if (is_null($sess_junkissa_id) || $sess_junkissa_id !== $param['junkissa_id']) {
            // リダイレクト
            return $this->redirect('/');
        }

        // 更新データを取得
        $junkissa = Junkissa::getById($param['junkissa_id']);
        $stored_images = ImageFileInfoService::getImageFileInfos(IMAGE_KEY_TYPE_JUNKISSA, $param['junkissa_id']);

        // 入力項目を取得
        $input_values = $this->getInputImageFileValues();
        // エンティティにセット
        $inputed_images = [];
        foreach ($stored_images as $i => $image) {
            $inputed_images[] = $this->setFormDataToEntity($image, $input_values[$i]);
        }
        // 画像保存
        if (($errors = ImageFileInfoService::uploadImageFiles(IMAGE_KEY_TYPE_JUNKISSA, $junkissa->id, $input_values)) !== true) {
            // エラーメッセージ設定
            $this->setErrMessage($errors);
            return $this->viewImagesEdit($junkissa, $inputed_images, $errors);
        }


        $this->setSessionMsg('info_msg', '純喫茶の画像を保存しました。ありがとうございます！');

        $this->redirect('/junkissa/' . $param['junkissa_id']);
    }

    private function viewEdit($junkissa, $errors = array())
    {
        return $this->render('junkissa/edit', [
            'junkissa'      => $junkissa,
            'prefectures'   => $this->getSelectList(Prefecture::class), // セレクトボックス
            'errors'        => $errors,
            'csrf_token'    => $this->generateCsrfToken()
        ]);
    }


    private function viewImagesEdit($junkissa, $images, $errors = array())
    {
        return $this->render('junkissa/edit', [
            'is_edit_images'    => true,
            'junkissa'          => $junkissa,
            'images'            => $images,
            'errors'            => $errors,
        ]);
    }
    private function isNewData($param)
    {
        $junkissa_id = (array_key_exists('junkissa_id', $param)) ?  $param['junkissa_id'] : null;
        if (!$junkissa_id) {
            return true;
        }
        return false;
    }

    private function getInputValues()
    {
        // POSTされた項目を取得
        $post_items= $this->request->getPostParam('user_edit');
        if (empty($post_items['type_of_management'])) {
            $post_items['type_of_management'] = 0;
        }
        $editable_items = JunkissaForm::editableItems();
        return $this->getFormData($post_items, $editable_items);
    }

    private function getInputImageFileValues()
    {
        // POSTされた項目を取得
        $post_items= $this->request->getPostParam('images');
        $editable_items = ImageFileInfoForm::editableItems();
        return $this->getArrayFormData($post_items, $editable_items);
    }
}