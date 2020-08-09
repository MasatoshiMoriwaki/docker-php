<?php

function e($value, $doubleEncode = false)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
}

function areaCheckbox($areas, $params = array())
{
    foreach ($areas as $area) {
        $items[] = sprintf('<div class="area %s">', $area->name_en);
        $items[] = sprintf('<h3 class="">%s</h3>', $area->name);
        $items[] = sprintf('<div class="prefectures">');
        foreach ($area->prefectures() as $prefecture) {
            $items[] = sprintf('<div class="prefecture checkbox">');
            $items[] = sprintf('<input id="lbl_prefecture_%s" type="checkbox" name="prefecture[]" value="%s" %s>'
                                , $prefecture->id
                                , $prefecture->id
                                , isset($params['prefecture']) && (in_array((string)$prefecture->id, $params['prefecture'], true)) ? 'checked="checked"' : '');
            $items[] = sprintf('<label for="lbl_prefecture_%s">%s</label>', $prefecture->id, $prefecture->name);
            $items[] = sprintf('</div>');
        }
        $items[] = sprintf('</div>');
    }
    return sprintf(implode(PHP_EOL, $items));
}

function msgDisplay($messages)
{
    $msg_html = '';
    if (isset($messages)) {
        foreach ($messages as $msg) {
            $msg_html .= '<p class="">' . e($msg->msg) . '</p>';
        }
    }
    return $msg_html;
}

function err($errors, $name, $class = 'form-error')
{
    $err_msg = '';
    if(isset($errors[$name])) {
        if (is_array($errors[$name])) {
            foreach ($errors[$name] as $error) {
                $err_msg .= '<p class="' . $class . '">' . e($error) . '</p>';
            }
        } else {
            $err_msg = '<p class="' . $class . '">' . e($errors[$name]) . '</p>';
        }
    }
    return $err_msg;
}

function isErr($errors, $name ,$class = 'has-error')
{
    if(isset($errors[$name])) {
        return $class;
    }
}

function radioState($val, $chk = null)
{
    if ($val === $chk) {
        return 'checked="checked"';
    }
}

function checkBoxState($val)
{
    if ((int)$val === 1) {
        return 'checked="checked"';
    }
}

function binaryToLabel($binary)
{
    $label = '';
     if($binary === 1) {
        $label ='○';
     } else if($binary === 0) {
        $label ='-';
     }
     return $label;
}

/**
 * 現在のページ番号を取得する
 * @return int
 */
function getCurrentPage()
{
    if (!filter_input(INPUT_GET, 'page')) {
        return 1;
    }
    return (int) filter_input(INPUT_GET, 'page');
}
/**
 * ページングリンク
 */
function pagination($rec)
{
    // レコード総数がゼロのときは何も出力しない
    if (0 === count($rec)) {
        return '';
    }
    // レコード総数
    $count = $rec[0]->total_count;
    $limit = JUNKISSAS_PER_PAGE;

    // 現在のページ
    $current_page = getCurrentPage();
    // 最後尾ページ
    $last_page = (int)ceil($count / $limit);

    // ページングのリスト
    $pages = array(1);
    if ($last_page > 1) array_push($pages, $last_page);

    for ($i = $current_page - 2; $i <= $current_page + 2; $i++) {
        if (($i < 1) || ($current_page > 1 && abs($i - $current_page  ) > 1 && $current_page !== $last_page)) continue;
        if (($i > $last_page)) break ;
        if (!in_array($i, $pages)) array_push($pages, $i);
    }
    asort($pages);
    //url組み立て
    $urlparams = filter_input_array(INPUT_GET);
    $items = [];
    $items[] = sprintf('<ul class="pagenation_links">');
    foreach ($pages as $page) {
        $urlparams['page'] = $page;
        $items[] = sprintf('<li %s><a href="/search?%s">%s</a></li>'
            , ($current_page == $page) ? ' class="is-current"' : ''
            , http_build_query($urlparams)
            , $page
        );
    }
    $items[] = sprintf('</ul>');


    return sprintf('<nav class="pagination">%s</nav>', implode(PHP_EOL, $items));
}

function createJunkissaImagesView($junkissa, $images)
{
    $items[] = sprintf('<tr>');

    $items[] = sprintf('<div class="junkissa-images">');
    // $items[] = sprintf('<th>');
    $items[] = sprintf('<h2 class="headline headline--lv2"><span class="headline_string">写真ギャラリー</span></h2>');
    $items[] = sprintf('<h3 class="c-headline c-headline--lv3"><span class="c-headline_string">ユーザ投稿画像</span></h3>');
    // $items[] = sprintf('</th>');
    $items[] = sprintf('<div class="junkissa-images">');
    // $items[] = sprintf('<td>');

    foreach ($images as $image) {

        $items[] = sprintf('<div class="junkissa-gallery-item">');
        $items[] = sprintf('<div class="junkissa-image">');
        $items[] = sprintf('<img src="%s" alt="%s">'
                            , e($image->file_name)
                            , !empty($image->caption) ? e($image->caption) : e($junkissa->name) . ' 写真ギャラリー' . $image->seq);
        $items[] = sprintf('</div>');
        $items[] = sprintf('<div class="junkissa-image-caption">');
        $items[] = sprintf(e($image->caption));
        $items[] = sprintf('</div>');
        $items[] = sprintf('</div>');


    }
    // $items[] = sprintf('</td>');

    $items[] = sprintf('</div>');
    $items[] = sprintf('</div>');
    $items[] = sprintf('</tr>');
    return sprintf(implode(PHP_EOL, $items));
}

function createJunkissaImagesForm($images, $errors)
{
    $items[] = sprintf('<div class="image-forms">');

    $items[] = sprintf(err($errors, 'message'));

    for ($i = 0; $i <= JUNKISSA_IMAGE_MAX_SEQ; $i++) {

        $file = isset($images[$i]) ? $images[$i]->file_name : '';
        $caption = isset($images[$i]) ? $images[$i]->caption : '';

        $items[] = sprintf('<div class="formimageItem">');
        $items[] = sprintf('<h4 class="formImageItem_headline">%s</h4>', $i === 0 ? 'メイン画像' : $i);
        $items[] = sprintf('<div class="formimageItem_preview">');
        $items[] = sprintf('<img src="%s">', e($file));
        $items[] = sprintf(err($errors, 'image_' . $i));
        $items[] = sprintf('<input accept="image/jpeg,image/png,image/gif" class="%s" name="images[%d][bin]" type="file">', isErr($errors, 'image_' . $i), $i);
        $items[] = sprintf('<input name="images[%d][seq]" type="hidden" value="%d">', $i, $i);
        $items[] = sprintf('<div class="formImageItem_caption">');
        $items[] = sprintf('<p>キャプション</p>');
        $items[] = sprintf('<div class="formText">');
        $items[] = sprintf(err($errors, 'caption***' . $i));
        $items[] = sprintf('<input class="%s" placeholder="例：外観" name="images[%d][caption]" type="text" value="%s">', isErr($errors, 'caption***' . $i), $i, e($caption));
        $items[] = sprintf('</div>');
        $items[] = sprintf('</div>');
        $items[] = sprintf('</div>');
        $items[] = sprintf('</div>');

    }
    $items[] = sprintf('</div>');
    return sprintf(implode(PHP_EOL, $items));
}

/**
 * (デバッグ用)var_dump
 */
function vd($item, $is_callbacked = false) {

    if ($is_callbacked === false) {
        echo '<div style="background: gray; font-weight: bold; color: white;">';
        echo '<br>';
    }

    if (is_array($item)) {
        foreach ($item as $key => $value) {

            echo '<br>' . $key . ' : ';
            vd($value, true);

        }
    } elseif(is_object($item)) {
        echo get_class($item);
        vd(get_object_vars($item), true);
    } else {
        echo var_dump($item);
    }

    if ($is_callbacked === false) {
        echo '</div>';
    }
}