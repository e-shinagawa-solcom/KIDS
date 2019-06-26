

// English ⇔ 日本語切り替えボタン押下時
$('.control-block__button-language').on('click', function(){

    // COOKIEから言語コードを取得
    var langCode = $.cookie('lngLanguageCode');

    // 切り替え対象要素のキャッシュ
    var title = $('.base-header__title-label');
    var funid = $("#funId").val();

    // 言語コードが "0:英語" の場合
    if (langCode == 0) {

        // タイトル画像設定
        if (funid == 'lcinfo') {
            title.text("L/C 情報");
        } else if (funid == 'lcedit') {
            title.text("L/C 編集");
        } else if (funid == 'lcset') {
            title.text("L/C 設定");
        }

        // 言語コードを1(日本語)に設定
        $.removeCookie('lngLanguageCode', {path: '/'});
        $.cookie('lngLanguageCode', 1, {path: '/'});
        // 言語切り替えボタンを日本語に変更
        $(this).text("JAPANESE");
    }
    // それ以外の場合(問答無用で日本語扱い)
    else {
        // タイトル画像設定
        if (funid == 'lcinfo') {
            title.text("L/C INFORMATION");
        } else if (funid == 'lcedit') {
            title.text("L/C EDIT");
        } else if (funid == 'lcset') {
            title.text("L/C SETTING");
        }

        // 言語コードを0(英語)に設定
        $.removeCookie('lngLanguageCode', {path: '/'});
        $.cookie('lngLanguageCode', 0, {path: '/'});
        // 言語切り替えボタンを英語に変更
        $(this).text("ENGLISH");
    }

    if($('.navigate-box').length){
        $('.navigate-box').hide();
    }
});

// 読み込み時に切り替え実行
// そのまま実行すると英日スイッチしてしまうので
// langCodeを逆に設定してからkickする
(function(langCode){

    // 言語コード反転
    langCode = (langCode == 1) ? 0 : 1;
    // COOKIEに設定
    $.removeCookie('lngLanguageCode', {path: '/'});
    $.cookie('lngLanguageCode', langCode, {path: '/'});

    // 言語切り替えボタン clickイベント発行
    $('.control-block__button-language').trigger('click');

})($.cookie('lngLanguageCode'));
