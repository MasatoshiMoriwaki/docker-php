<div>
    <div class="">
        <a href="<?= $base_url ?>/junkissa/<?= e($junkissa->id) ?>/edit">編集する</a>
    </div>
</div>

<div class="list-item">
    <table>
        <tbody>
            <tr>
                <th>店名</th>
                <td><?= e($junkissa->name) ?></td>
            </tr>
            <tr>
                <th>都道府県</th>
                <td><?= ($junkissa->prefecture()) ? e($junkissa->prefecture->name) : "" ?></td>
            </tr>
            <tr>
                <th>住所1</th>
                <td><?= e($junkissa->address_1) ?></td>
            </tr>
            <tr>
                <th>住所2</th>
                <td><?= e($junkissa->address_2) ?></td>
            </tr>
            <tr>
                <th>住所3</th>
                <td><?= e($junkissa->address_3) ?></td>
            </tr>
            <tr>
                <th>アクセス</th>
                <td><?= e($junkissa->access_info) ?></td>
            </tr>
            <tr>
                <th>TEL</th>
                <td><?= e($junkissa->phone_number) ?></td>
            </tr>
            <tr>
                <th>ホームページ</th>
                <td><?= e($junkissa->web_page) ?></td>
            </tr>
            <tr>
                <th>定休日</th>
                <td><?= e($junkissa->regular_holiday) ?></td>
            </tr>
            <tr>
                <th>営業時間</th>
                <td><?= e($junkissa->business_hours) ?></td>
            </tr>
            <tr>
                <th>メニュー情報</th>
                <td><?= e($junkissa->menu_info) ?></td>
            </tr>
            <tr>
                <th>補足情報</th>
                <td><?= e($junkissa->remark) ?></td>
            </tr>

            <tr>
                <th>経営形態</th>
                <td>
                    <? if($junkissa->type_of_management === (int)'1') { ?>
                        個人経営
                    <? } else if($junkissa->type_of_management === (int)'2') { ?>
                        チェーン
                    <? }?>
                </td>
            </tr>

            <tr>
                <th>創業年</th>
                <td><?= !empty($y = $junkissa->year_of_establishment) ? e($y . '年') : '' ?></td>
            </tr>

            <tr>
                <th>自家焙煎</th>
                <td>
                    <?= binaryToLabel($junkissa->has_home_roasting)?>
                </td>
            </tr>

            <tr>
                <th>ペーパードリップ</th>
                <td>
                    <?= binaryToLabel($junkissa->has_paper_drip)?>
                </td>
            </tr>

            <tr>
                <th>ネルドリップ</th>
                <td>
                    <?= binaryToLabel($junkissa->has_nel_drip)?>
                </td>
            </tr>

            <tr>
                <th>サイフォン</th>
                <td>
                    <?= binaryToLabel($junkissa->has_siphon)?>
                </td>
            </tr>

            <tr>
                <th>フレンチプレス</th>
                <td>
                    <?= binaryToLabel($junkissa->has_french_press)?>
                </td>
            </tr>

            <tr>
                <th>エアロプレス</th>
                <td>
                    <?= binaryToLabel($junkissa->has_aero_press)?>
                </td>
            </tr>

            <tr>
                <th>エスプレッソ</th>
                <td>
                    <?= binaryToLabel($junkissa->has_espresso)?>
                </td>
            </tr>

            <tr>
                <th>水出し</th>
                <td>
                    <?= binaryToLabel($junkissa->has_cold_brew)?>
                </td>
            </tr>

            <tr>
                <th>軽食</th>
                <td>
                    <?= binaryToLabel($junkissa->has_light_meal)?>
                </td>
            </tr>

            <tr>
                <th>モーニングセット</th>
                <td>
                    <?= binaryToLabel($junkissa->has_morning_set)?>
                </td>
            </tr>

            <tr>
                <th>デザート</th>
                <td>
                    <?= binaryToLabel($junkissa->has_dessert)?>
                </td>
            </tr>

            <tr>
                <th>アルコール</th>
                <td>
                    <?= binaryToLabel($junkissa->has_alcohol)?>
                </td>
            </tr>

            <tr>
                <th>喫煙席</th>
                <td>
                    <?= binaryToLabel($junkissa->has_smoking_seat)?>
                </td>
            </tr>

            <tr>
                <th>漫画</th>
                <td>
                    <?= binaryToLabel($junkissa->has_comic)?>
                </td>
            </tr>

            <tr>
                <th>クラシック・名曲</th>
                <td>
                    <?= binaryToLabel($junkissa->has_classical_music)?>
                </td>
            </tr>

            <tr>
                <th>ゲーム台</th>
                <td>
                    <?= binaryToLabel($junkissa->has_game_machine)?>
                </td>
            </tr>

            <tr>
                <th>カラオケ</th>
                <td>
                    <?= binaryToLabel($junkissa->has_karaoke)?>
                </td>
            </tr>


            <tr>
                <th>登録日時</th>
                <td><?= e($junkissa->created_at) ?></td>
            </tr>
            <tr>
                <th>更新日時</th>
                <td><?= e($junkissa->updated_at) ?></td>
            </tr>
        </tbody>
    </table>

    <?= $images ? createJunkissaImagesView($junkissa, $images) : ""?>

</div>
