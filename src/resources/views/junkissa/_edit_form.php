<h4>
    純喫茶情報の編集
</h4>

<div class="list-item">

    <form method="POST" action="" accept-charset="UTF-8" class="" >
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <div class="btn-submit">
            <button type="submit" class="">保存する</button>
        </div>
        <? if($junkissa->id) {?>
            <div class="link">
                <a href="/junkissa/<?= e($junkissa->id)?>/images/edit" class="image-upload">画像を登録する</a>
            </div>
        <?}?>

        <table>
            <tbody>
                <tr>
                    <th>店名</th>
                    <td>
                        <?= err($errors, 'name') ?>
                        <div class="<?= isErr($errors, 'name') ?>">
                            <input type="text" name="user_edit[name]" value="<?= e($junkissa->name) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>都道府県</th>
                    <td>
                        <?= err($errors, 'prefecture_id') ?>
                        <div class="<?= isErr($errors, 'prefecture_id') ?>">
                            <select name="user_edit[prefecture_id]">
                                <option value=""></option>
                                <? foreach ( $prefectures as $prefecture_id => $prefecture_name ) { ?>
                                    <option value="<?= $prefecture_id ?>" <?= ($prefecture_id == $junkissa->prefecture_id) ? 'selected="selected"' : ''?>><?= e($prefecture_name) ?></option>
                                <? } ?>
                            </select>
                        </div>
                </tr>
                <tr>
                    <th>住所1</th>
                    <td>
                        <?= err($errors, 'address_1') ?>
                        <div class="<?= isErr($errors, 'address_1') ?>">
                            <input type="text" name="user_edit[address_1]" value="<?= e($junkissa->address_1) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>住所2</th>
                    <td>
                        <?= err($errors, 'address_2') ?>
                        <div class="<?= isErr($errors, 'address_2') ?>">
                            <input type="text" name="user_edit[address_2]" value="<?= e($junkissa->address_2) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>住所3</th>
                    <td>
                        <?= err($errors, 'address_3') ?>
                        <div class="<?= isErr($errors, 'address_3') ?>">
                            <input type="text" name="user_edit[address_3]" value="<?= e($junkissa->address_3) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>アクセス</th>
                    <td>
                        <?= err($errors, 'access_info') ?>
                        <div class="<?= isErr($errors, 'access_info') ?>">
                            <textarea name="user_edit[access_info]" cols="50" rows="5"><?= e($junkissa->access_info) ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>TEL</th>
                    <td>
                        <?= err($errors, 'phone_number') ?>
                        <div class="<?= isErr($errors, 'phone_number') ?>">
                            <input type="text" name="user_edit[phone_number]" value="<?= e($junkissa->phone_number) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>ホームページ</th>
                    <td>
                        <?= err($errors, 'web_page') ?>
                        <div class="<?= isErr($errors, 'web_page') ?>">
                            <input type="text" name="user_edit[web_page]" value="<?= e($junkissa->web_page) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>定休日</th>
                    <td>
                        <?= err($errors, 'regular_holiday') ?>
                        <div class="<?= isErr($errors, 'regular_holiday') ?>">
                            <textarea name="user_edit[regular_holiday]" cols="50" rows="5"><?= e($junkissa->regular_holiday) ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>営業時間</th>
                    <td>
                        <?= err($errors, 'business_hours') ?>
                        <div class="<?= isErr($errors, 'business_hours') ?>">
                            <textarea name="user_edit[business_hours]" cols="50" rows="5"><?= e($junkissa->business_hours) ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>メニュー情報</th>
                    <td>
                        <?= err($errors, 'menu_info') ?>
                        <div class="<?= isErr($errors, 'menu_info') ?>">
                            <textarea name="user_edit[menu_info]" cols="50" rows="5"><?= e($junkissa->menu_info) ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>補足情報</th>
                    <td>
                        <?= err($errors, 'remark') ?>
                        <div class="<?= isErr($errors, 'remark') ?>">
                            <textarea name="user_edit[remark]" cols="50" rows="5"><?= e($junkissa->remark) ?></textarea>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>経営形態</th>
                    <td>
                        <?= err($errors, 'type_of_management') ?>
                        <div class="<?= isErr($errors, 'type_of_management') ?>">
                            <select name="user_edit[type_of_management]">
                                <option value=""></option>
                                <? foreach ( \App\Constant\Junkissa::MANAGEMENT_TYPE_ARRAY() as $key => $value ) { ?>
                                    <option value="<?= $key ?>" <?= ($key == $junkissa->type_of_management) ? 'selected="selected"' : ''?>><?= e($value) ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>創業年</th>
                    <td>
                        <?= err($errors, 'year_of_establishment') ?>
                        <div class="<?= isErr($errors, 'year_of_establishment') ?>">
                            <select name="user_edit[year_of_establishment]">
                                <option value=""></option>
                                <? foreach ( \App\Constant\Junkissa::YEARS_ARRAY() as $y ) { ?>
                                    <option value="<?= $y ?>" <?= ($y == $junkissa->year_of_establishment) ? 'selected="selected"' : ''?>><?= e($y) ?></option>
                                <? } ?>
                            </select>
                            年
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>自家焙煎</th>
                    <td>
                        <?= err($errors, 'has_home_roasting') ?>
                        <div class="<?= isErr($errors, 'has_home_roasting') ?>">
                            <div class="<?= isErr($errors, 'has_home_roasting') ?>">
                                <input id="user_edit[has_home_roasting]_1" name="user_edit[has_home_roasting]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_home_roasting)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>ペーパードリップ</th>
                    <td>
                        <?= err($errors, 'has_paper_drip') ?>
                        <div class="<?= isErr($errors, 'has_paper_drip') ?>">
                            <div class="<?= isErr($errors, 'has_paper_drip') ?>">
                                <input id="user_edit[has_paper_drip]_1" name="user_edit[has_paper_drip]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_paper_drip)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>ネルドリップ</th>
                    <td>
                        <?= err($errors, 'has_nel_drip') ?>
                        <div class="<?= isErr($errors, 'has_nel_drip') ?>">
                            <div class="<?= isErr($errors, 'has_nel_drip') ?>">
                                <input id="user_edit[has_nel_drip]_1" name="user_edit[has_nel_drip]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_nel_drip)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>サイフォン</th>
                    <td>
                        <?= err($errors, 'has_siphon') ?>
                        <div class="<?= isErr($errors, 'has_siphon') ?>">
                            <div class="<?= isErr($errors, 'has_siphon') ?>">
                                <input id="user_edit[has_siphon]_1" name="user_edit[has_siphon]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_siphon)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>フレンチプレス</th>
                    <td>
                        <?= err($errors, 'has_french_press') ?>
                        <div class="<?= isErr($errors, 'has_french_press') ?>">
                            <div class="<?= isErr($errors, 'has_french_press') ?>">
                                <input id="user_edit[has_french_press]_1" name="user_edit[has_french_press]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_french_press)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>エアロプレス</th>
                    <td>
                        <?= err($errors, 'has_aero_press') ?>
                        <div class="<?= isErr($errors, 'has_aero_press') ?>">
                            <div class="<?= isErr($errors, 'has_aero_press') ?>">
                                <input id="user_edit[has_aero_press]_1" name="user_edit[has_aero_press]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_aero_press)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>エスプレッソ</th>
                    <td>
                        <?= err($errors, 'has_espresso') ?>
                        <div class="<?= isErr($errors, 'has_espresso') ?>">
                            <div class="<?= isErr($errors, 'has_espresso') ?>">
                                <input id="user_edit[has_espresso]_1" name="user_edit[has_espresso]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_espresso)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>水出し</th>
                    <td>
                        <?= err($errors, 'has_cold_brew') ?>
                        <div class="<?= isErr($errors, 'has_cold_brew') ?>">
                            <div class="<?= isErr($errors, 'has_cold_brew') ?>">
                                <input id="user_edit[has_cold_brew]_1" name="user_edit[has_cold_brew]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_cold_brew)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>軽食</th>
                    <td>
                        <?= err($errors, 'has_light_meal') ?>
                        <div class="<?= isErr($errors, 'has_light_meal') ?>">
                            <div class="<?= isErr($errors, 'has_light_meal') ?>">
                                <input id="user_edit[has_light_meal]_1" name="user_edit[has_light_meal]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_light_meal)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>モーニングセット</th>
                    <td>
                        <?= err($errors, 'has_morning_set') ?>
                        <div class="<?= isErr($errors, 'has_morning_set') ?>">
                            <div class="<?= isErr($errors, 'has_morning_set') ?>">
                                <input id="user_edit[has_morning_set]_1" name="user_edit[has_morning_set]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_morning_set)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>デザート</th>
                    <td>
                        <?= err($errors, 'has_dessert') ?>
                        <div class="<?= isErr($errors, 'has_dessert') ?>">
                            <div class="<?= isErr($errors, 'has_dessert') ?>">
                                <input id="user_edit[has_dessert]_1" name="user_edit[has_dessert]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_dessert)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>アルコール</th>
                    <td>
                        <?= err($errors, 'has_alcohol') ?>
                        <div class="<?= isErr($errors, 'has_alcohol') ?>">
                            <div class="<?= isErr($errors, 'has_alcohol') ?>">
                                <input id="user_edit[has_alcohol]_1" name="user_edit[has_alcohol]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_alcohol)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>喫煙席</th>
                    <td>
                        <?= err($errors, 'has_smoking_seat') ?>
                        <div class="<?= isErr($errors, 'has_smoking_seat') ?>">
                            <div class="<?= isErr($errors, 'has_smoking_seat') ?>">
                                <input id="user_edit[has_smoking_seat]_1" name="user_edit[has_smoking_seat]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_smoking_seat)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>漫画</th>
                    <td>
                        <?= err($errors, 'has_comic') ?>
                        <div class="<?= isErr($errors, 'has_comic') ?>">
                            <div class="<?= isErr($errors, 'has_comic') ?>">
                                <input id="user_edit[has_comic]_1" name="user_edit[has_comic]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_comic)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>クラシック・名曲</th>
                    <td>
                        <?= err($errors, 'has_classical_music') ?>
                        <div class="<?= isErr($errors, 'has_classical_music') ?>">
                            <div class="<?= isErr($errors, 'has_classical_music') ?>">
                                <input id="user_edit[has_classical_music]_1" name="user_edit[has_classical_music]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_classical_music)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>ゲーム台</th>
                    <td>
                        <?= err($errors, 'has_game_machine') ?>
                        <div class="<?= isErr($errors, 'has_game_machine') ?>">
                            <div class="<?= isErr($errors, 'has_game_machine') ?>">
                                <input id="user_edit[has_game_machine]_1" name="user_edit[has_game_machine]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_game_machine)?> >
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>カラオケ</th>
                    <td>
                        <?= err($errors, 'has_karaoke') ?>
                        <div class="<?= isErr($errors, 'has_karaoke') ?>">
                            <div class="<?= isErr($errors, 'has_karaoke') ?>">
                                <input id="user_edit[has_karaoke]_1" name="user_edit[has_karaoke]" type="checkbox" value="1" <?= checkBoxState($junkissa->has_karaoke)?> >
                            </div>
                        </div>
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
    </form>
</div>
