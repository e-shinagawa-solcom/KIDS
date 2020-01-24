(function(){
    // チェックボタン画像(未チェック)
    var unchecked="/img/type01/cmn/seg/off_bt.gif";
    // チェックボタン画像(チェック済み)
    var checked="/img/type01/cmn/seg/on_bt.gif"

    // チェックボックスの切り替え処理のバインド
    $('img.toggle-search').on({
        'click': function() {
            toggleSearchCheckBox();
            // チェック済みの場合
            if (this.checked){this.src = unchecked;}
            // 未チェックの場合
            else {this.src = checked;}
            // 内部フラグを反転して保持
            this.checked = !this.checked;
        }
    });

    // チェックボックスの切り替え処理のバインド
    $('img.toggle-display').on({
        'click': function() {
            toggleDisplayCheckBox();
            // チェック済みの場合
            if (this.checked){this.src = unchecked;}
            // 未チェックの場合
            else {this.src = checked;}
            // 内部フラグを反転して保持
            this.checked = !this.checked;
        }
    });

    // 検索チェックボックスのトグル
    var toggleSearchCheckBox = function(){
        $('input[type="checkbox"].is-search')
            .each(function(){
                // disabledな要素は対象外
                if (this.disabled == false){
                    this.checked = !toggleSearchCheckBox.checked;
                }
            });
        // 内部フラグを反転させて保持
        toggleSearchCheckBox.checked = !toggleSearchCheckBox.checked;
    };

    // 表示チェックボックスのトグル
    var toggleDisplayCheckBox = function(){
        $('input[type="checkbox"].is-display')
            .each(function(){
                // disabledな要素は対象外
                if (this.disabled == false){
                    this.checked = !toggleDisplayCheckBox.checked;
                }
            });
        // 内部フラグを反転させて保持
        toggleDisplayCheckBox.checked = !toggleDisplayCheckBox.checked;
    };
})();
