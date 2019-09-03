(function(){
	// 表示中のタブを設定
	var tabs = $('.tabs');
	tabs.prop('displayTab', 'header');

	// ヘッダタブ
	$('.tabs__header').on({
		'click' : function() {
			tabs.prop('displayTab', 'header');
	        // ヘッダ/フッタタブ 表示/非表示切り替え
	        $('.regist-tab-header, .regist-tab-detail').toggle();
	        // 登録ボタン 表示/非表示切り替え
	        $('.form-buttons__regist').toggle();
		}
	});

	// 詳細タブ
	$('.tabs__detail').on({
		'click' : function() {
			tabs.prop('displayTab', 'detail');
	        // ヘッダ/フッタタブ表示/非表示切り替え
	        $('.regist-tab-header, .regist-tab-detail').toggle();
	        // 登録ボタン 表示/非表示切り替え
	        $('.form-buttons__regist').toggle();

	        // JQuery Validation Pluginで検知させる為イベントキック
	        $('input[name="ProductCode"]').trigger('blur');
	        $('input[name="ProductName"]').trigger('blur');
	        $('input[name="GoodsCode"]').trigger('blur');
		}
	});

	// Tabキー処理
	// ヘッダタブ先頭での shift + tab キー入力
	$('.regist-tab-header').on(
		'keydown', 'input:eq(0)', function(e){
			if(e.keyCode == 9 && e.shiftKey){
				$('.tabs__detail').click();
				// 詳細タブの終わりにフォーカスを移す
				$('.regist-tab-detail').find('input, textarea').last().focus();
				return false;
			}
		}
	);
	// ヘッダタブ終わりでのtabキー入力
	$('.regist-tab-header').on(
		'keydown', 'textarea', function(e){
			if ( $(this)[0] == $('.regist-tab-header textarea:last')[0] ) {
				if(e.keyCode == 9 && !e.shiftKey){
					$('.tabs__detail').click();
					// 詳細タブの先頭にフォーカスを移す
					$('.regist-tab-detail input:eq(0)').focus();
					return false;
				}
			}
		}
	);
	// 詳細タブ先頭での shift + tab キー入力
	$('.regist-tab-detail').on(
		'keydown', 'input:eq(0)', function(e){
			if(e.keyCode == 9　&& e.shiftKey){
				$('.tabs__header').click();
				// ヘッダタブの終わりにフォーカスを移す
				$('.regist-tab-header').find('input, textarea').last().focus();
				return false;
			}
		}
	);
	// 詳細タブ終わりでのtabキー入力
	$('.regist-tab-detail').on(
		'keydown', 'input, img', function(e) {
			// table要素にinputがある場合detail-tab最後の要素はinput、ない場合img
			if( $('.regist-tab-detail table input').length ) {
				if ( $(this)[0] == $('.regist-tab-detail input:last')[0] ) {
					if(e.keyCode == 9 && !e.shiftKey){
						$('.tabs__header').click();
						// ヘッダタブの先頭にフォーカスを移す
						$('.regist-tab-header input:eq(0)').focus();
						return false;
					}
				}
			// 金型のテーブル要素がない場合imgがdetail-tab最後の要素
			} else if ( $(this)[0] == $('.regist-tab-detail img:last')[0] ) {
				if(e.keyCode == 9 && !e.shiftKey){
					$('.tabs__header').click();
					// 詳細タブの先頭にフォーカスを移す
					$('.regist-tab-header input:eq(0)').focus();
					return false;
				}
			}
		}
	);
})();
