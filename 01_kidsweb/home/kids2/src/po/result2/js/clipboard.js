// コピーボタン
$('img.copy').on({
    'click': function () {
        // クリップボードに値を反映
        if (window.getSelection) {
            var selection = getSelection();
            selection.removeAllRanges();
            var range = document.createRange();
            range.selectNodeContents(document.getElementById("result"));
            selection.addRange(range);
            document.execCommand('copy');
            selection.removeAllRanges();
            alert('クリップボードにコピーしました。');
        } else {
            alert("クリップボードへのコピーに失敗しました。");
        }
    }
});
