$(function () {
    // 切り替え対象要素のキャッシュ
    var title = $('.base-header__title-image');
    var label = $('.label-select-function');
    var regist = $('.function-buttons__regist');
    var search = $('.function-buttons__search');

    // タイトル画像設定
    title.attr('src', '/img/type01/mm/title_ja.gif');
    // ラベル 機能選択
    label.text('機能選択');

    // ボタン画像設定(金型履歴登録画面)
    regist.attr('src', '/img/type01/mm/regist_off_ja_bt.gif');
    // ボタン画像設定(金型履歴登録画面)
    search.attr('src', '/img/type01/mm/search_off_ja_bt.gif');
});