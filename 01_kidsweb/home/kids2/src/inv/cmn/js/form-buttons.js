(function(){
//	$('form').append($('<input />', {
//		type: 'hidden',
//		name: 'mode',
//		value: 'prev',
//	}));
//	$('form').appendTo(document.body);
    var ele = document.createElement('input');
    // データを設定
    ele.setAttribute('type', 'hidden');
    ele.setAttribute('name', 'mode');
    ele.setAttribute('value', 'prev');
    // 要素を追加
    document.Invoice.appendChild(ele);
    console.log($('input[name="mode"]'));

    // フォーム
    var registForm = Object.create($('form'));
    console.log(registForm);
    // フォーム
    var invForm = Object.create($('form[name="Invoice"]'));
    console.log(invForm);
    // クリアボタン
    var btnClear = $('#clear');
    // プレビューボタン
    var btnSearch = $('#preview-button');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        alert('submit false');
        return false;
    });

    // クリアボタン
    btnClear.on('click', function(){
        // テキスト入力箇所をリセット
        registForm.find('input[type="text"], textarea').val('');
        var checks = registForm.find('input[type="checkbox"]');
        for(var i = 0;i < checks.length;i++){
        	checks[i].checked = false;
        }
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function(){
    	var windowName = 'registPreview';
    	url = '/inv/regist/index.php?strSessionID=' + $.cookie('strSessionID');

//        var windowName = 'searchResult';
    	registForm.attr('action', url);
    	registForm.attr('method', 'post');
        registForm.attr('target', windowName);
//        registForm.append($('<input />', {
//    		type: 'hidden',
//    		name: 'mode',
//    		value: 'prev',
//    	}));
//        registForm.appendTo(document.body);

//        moldChoosenList.find('option').prop('selected', true);
        registForm.submit();

        return;
        // フォーム設定
        registForm.get(0).target = windowName;
        registForm.get(0).method = 'post';
        var baseURI = registForm.get(0).baseURI;
        alert(baseURI);

//        if((baseURI.indexOf('/search.php') > 0) && (baseURI.indexOf('/list/search/') > 0)){
//            var searchResult = open('about:blank', windowName, 'width=1011px, height=700px, resizable=yes, scrollbars=no, menubar=no');
//            GoResult_list( searchResult, workForm.get(0) , "/result/index.html" , "/result/ifrm.html" , "ResultIframe" , "YES" );
//        }
//        else if((baseURI.indexOf('/search.php') > 0) && (baseURI.indexOf('/m/search/') > 0)){
//        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
//        	var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search.php')-1)+1,baseURI.indexOf('/search.php'))
//        	workForm.get(0).action = '/m/result/' + screen + '/index.php?strSessionID=' + $.cookie('strSessionID');
//        	// サブミット
//        	workForm.submit();
//        }
//        else if((baseURI.indexOf('/search.php') > 0) && (baseURI.indexOf('/uc/search/') > 0)){
//        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
//        	workForm.get(0).action = '/uc/result/index.php?strSessionID=' + $.cookie('strSessionID');
//        	// サブミット
//        	workForm.submit();
//        }
//        else if(baseURI.indexOf('/search/index.php') > 0){
//        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
//        	var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search/index.php')-1)+1,baseURI.indexOf('/search/index.php'))
//        	workForm.get(0).action = '/' + screen + '/result/index.php?strSessionID=' + $.cookie('strSessionID');
//        	// サブミット
//        	workForm.submit();
//        }
//        else if(baseURI.indexOf('/search2/index.php') > 0){
        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
//        	var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search2/index.php')-1)+1,baseURI.indexOf('/search2/index.php'))
//        	registForm.get(0).action = '/' + screen + '/result2/index.php?strSessionID=' + $.cookie('strSessionID');
        	registForm.get(0).action = url;
        	console.log(registForm);
        	// サブミット
        	registForm.submit();
//        }

    });
})();

//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索結果表示用関数
*
* 解説 : 検索結果をダイアログを表示させるための関数。
*        配列[args]にフォーム要素の値を代入後ダイアログを表示する。
*
* 対象 : 検索用テンプレート
*
* @param [obj1]      : [オブジェクト型] . フォームのオブジェクト名
* @param [obj2]      : [文字列型]       . ダイアログ上で呼び出す親HTMLファイルのURL
* @param [strUrl]    : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルのURL(Iframe)
* @param [strID]     : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll] : [文字列型]       . Iframeでスクロールの許可・不許可
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function GoResult_list(TargetWindow, obj1 , obj2 , strUrl , strID , strScroll )
{
	var j = 0;
	var k = 0;

	args = new Array();
	args[0] = new Array();
	args[1] = new Array();
	args[2] = new Array();
	args[3] = new Array();
	args[4] = new Array();

	args[0][0] = strUrl;
	args[0][1] = strID;
	args[0][2] = strScroll;


	///// other name /////
	for ( i = 0; i < obj1.elements.length; i++ )
	{

		if( typeof(obj1.elements[i]) == 'undefined' )
		{
			continue;
		}

		if ( obj1.elements[i].type == 'checkbox' )
		{
			if ( obj1.elements[i].checked == true )
			{
				args[1][j] = obj1.elements[i].name;
				args[2][j] = obj1.elements[i].value;
				j++;
			}
			continue;
		}

		// 検索ページ[状態]項目以外
		if( obj1.elements[i].name != 'OrderStatusObject' )
		{
			args[3][k] = obj1.elements[i].name;
			args[4][k] = obj1.elements[i].value;
			k++;
		}

	}

	TargetWindow.location.href = obj2;
}