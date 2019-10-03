(function () {
    // フォーム
    var workForm = $('form');
    // クリアボタン
    var btnClear = $('img.clear');
    // 登録ボタン
    var btnSearch = $('img.search');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // クリアボタン
    btnClear.on('click', function () {
        // テキスト入力箇所をリセット
        workForm.find('input[type="text"], textarea').val('');
        workForm.find('select').val('');
        var checks = workForm.find('input[type="checkbox"]');
        // for(var i = 0;i < checks.length;i++){
        // 	checks[i].checked = false;
        // }
		workForm.find('input[type="checkbox"][name="IsDisplay_lngUserCodeVisible"]').each(function(index){
            $(this).attr('checked', true);
            // alert($(this).attr('name'));
        });
        // var target = $('input[type="checkbox"][name="IsDisplay_lngUserCodeVisible"]');
        // alert(target);
        // target.checked = false;
        // // target.attr('checked', true);
        // restoreCookieDispayItems1(workForm);
    });
// 検索画面にて、表示項目のチェック状態を保存されたCOOKIEから復元する
function restoreCookieDispayItems1(form){
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
            workForm.find('input[type="checkbox"][name="' + name + '"]').each(function(index){
                switch (value) {
                    case "true":
                    $(this).attr('checked', true);
                        break;
                    case "false":
                    alert($(this).attr('name'));
                    $(this).attr('checked', false);
                        break;
                    default:
                        break;
                }
            });
        });
    }
}
    // 検索ボタン押下時の処理
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            var windowName = 'searchResult';
            window.open("", windowName,"width=1011px, height=700px, scrollbars=yes, resizable=yes"); 
            workForm.attr('action', '/uc/result/index.php?strSessionID=' + $.cookie('strSessionID'));
            workForm.attr('method', 'post');
            workForm.attr('target', windowName);
            workForm.submit();
            // バリデーションのキック
            // workForm.find(':submit').click();
        }
        else {
            // バリデーションのキック
            workForm.find(':submit').click();
        }
    });

    // 会社コード変更イベント
    $('select[name="lngCompanyCode"]').on('change', function () {
        // リクエスト送信
        $.ajax({
            url: '/cmn/getmasterdata.php?lngProcessID=15&strFormValue[0]=' + $(this).val(),
            type: 'post',
        })
            .done(function (response) {

                $('select[name="lngGroupCode"] option').remove();
                $option = $('<option>')
                    .val('0')
                    .text('');
                $('select[name="lngGroupCode"]').append($option);
                var rows = response.split('\n');
                var cols = rows[1].split('\t');
                for (i = 1, len = rows.length; i < len; i++) {
                    var cols = rows[i].split('\t');
                    $option = $('<option>')
                        .val(cols[0])
                        .text(cols[1]);
                    $('select[name="lngGroupCode"]').append($option);
                }
            })
            .fail(function (response) {
                alert("fail");
            })
    });
})();
