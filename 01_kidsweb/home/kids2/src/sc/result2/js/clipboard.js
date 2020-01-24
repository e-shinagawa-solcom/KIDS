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

// 任意の文字列をクリップボードにコピーする
function execCopy(string){

    // 空div 生成
    var tmp = document.createElement("div");
    // 選択用のタグ生成
    var pre = document.createElement('pre');
  
    // 親要素のCSSで user-select: none だとコピーできないので書き換える
    pre.style.webkitUserSelect = 'auto';
    pre.style.userSelect = 'auto';
  
    tmp.appendChild(pre).textContent = string;
  
    // 要素を画面外へ
    var s = tmp.style;
    s.position = 'fixed';
    s.right = '200%';
  
    // body に追加
    document.body.appendChild(tmp);
    // 要素を選択
    document.getSelection().selectAllChildren(tmp);
  
    // クリップボードにコピー
    var result = document.execCommand("copy");
  
    // 要素削除
    document.body.removeChild(tmp);
  
    return result;
 };
 
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
                contents += this.innerHTML + delimiter;
            });
            // 改行
            contents += '\r\n'
        });

        // クリップボードに値を反映
        // 既存コードはIEでしか動かないため実装を変更（2019/8/31 T.Miyata）
        //if (window.clipboardData.setData('Text', contents)){
        //    alert("クリップボードに検索結果をコピーしました。");
        //}
        //else {
        //   alert("クリップボードへのコピーに失敗しました。");
        //}
        if (execCopy(contents)){
            alert("クリップボードに検索結果をコピーしました。");
        }else{
            alert("クリップボードへのコピーに失敗しました。");
        }

    }

    
});
