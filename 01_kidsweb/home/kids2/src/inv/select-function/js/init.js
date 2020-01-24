$(function () {
    // 切り替え対象要素のキャッシュ
    var title = $('.base-header__title-image');
    var label = $('.label-select-function');
    var regist = $('.function-buttons__regist');
    var search = $('.function-buttons__search');
    var total = $('.function-buttons__total');

    // タイトル画像設定
    title.attr('src', '/img/type01/inv/title_ja.gif');
    // ラベル 機能選択
    label.text('機能選択');

    // ボタン画像設定(請求書登録画面)
    regist.attr('src', '/img/type01/inv/regist_off_ja_bt.gif');
    // ボタン画像設定(請求書検索画面)
    search.attr('src', '/img/type01/inv/search_off_ja_bt.gif');
    // ボタン画像設定(請求集計画面)
    total.attr('src', '/img/type01/inv/total_off_ja_bt.gif');
});