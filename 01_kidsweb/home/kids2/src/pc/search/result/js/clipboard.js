// クリップボード除外クラス
var excludeClassName = ".exclude-in-clip-board-target";
// 対象テーブル
var table = $('table#result');
// ヘッダ項目
var headers = table.find('th:not(' + excludeClassName + ')');
// テーブルレコード
var records = table.find('tbody > tr');
// 区切り文字
var delimiter = '\t';
var brReplacement = ',';

// br置換後区切り文字の末尾削除用パターン
eval('var patternLastBrReplacement = /' + brReplacement +'+$/');

// コピーボタン
$('img.copy').on({
    'click': function(){
        // クリップボードに反映させる文字列
        var contents = "";

        // ヘッダ行の取得
        headers.each(function(){
            contents += $(this).children('div').get(0).innerHTML + delimiter;
        });
        // 改行
        contents += '\r\n';

        // データ行の取得
        records.each(function(){
            var cells = $(this).find('td:not(' + excludeClassName + ')');

            cells.each(function(){
                contents += this.innerHTML;
                contents += delimiter;
            });
            // 改行
            contents += '\r\n'
        });

        // クリップボードに値を反映
        if (window.clipboardData.setData('Text', contents)){
            alert("クリップボードに検索結果をコピーしました。");
        }
        else {
            alert("クリップボードへのコピーに失敗しました。");
        }
    }
});
