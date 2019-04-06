// ---------------------------------------------------
// !!グローバル!!
// ---------------------------------------------------
// 検索画面にて、表示項目のチェック状態をCOOKIEに保存する
function saveCookieDispayItems(form){
    // 保存日数(適当)
    var expires = 1095;
    // 保存対象のチェックボックス要素の取得
    var items = $(form).find('input[type="checkbox"][name^="IsDisplay_"]');
    // COOKIE keyname
    var keyname = $(form).attr('name') + '_IsDisplay';
    // 表示フラグたち
    var flags = '';
    // 区切り文字
    var delimiter = '&';
    var sepalator = ':';

    // 要素数分走査
    items.each(function(){
        flags += this.name + sepalator + this.checked + delimiter
    });

    // COOKIEへ保存
    $.cookie(keyname , flags.substr(0, flags.length-1), {
        'expires':　expires
    });
}
// 検索画面にて、表示項目のチェック状態を保存されたCOOKIEから復元する
function restoreCookieDispayItems(form){
    // 保存対象のチェックボックス要素の取得
    var items = $(form).find();
    // COOKIE keyname
    var keyname = $(form).attr('name') + '_IsDisplay';
    // 表示フラグたち
    var flags = $.cookie(keyname);
    // 区切り文字
    var delimiter = '&';
    var sepalator = ':';

    if (flags)
    {
        var conditions = flags.split(delimiter);

        $.each(conditions, function(){
            var sep = this.split(sepalator);
            var name = sep[0];
            var value = sep[1];
            var target = $(form).find('input[type="checkbox"][name^="' + name + '"]');

            switch (value) {
                case "true":
                    target.attr('checked', true);
                    break;
                case "false":
                    target.attr('checked', false);
                    break;
                default:
                    break;
            }
        });
    }
}
