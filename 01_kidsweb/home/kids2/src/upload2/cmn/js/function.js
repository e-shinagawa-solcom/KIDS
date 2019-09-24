(function(){
    // フォーム
    var workForm = $('form');
    // Uploadボタン
    var btnUpload = $('img.upload');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // 検索ボタン押下時の処理
    btnUpload.on('click', function(){

		if( document.exc_upload.excel_file.value.length == 0 )
		{
			alert( 'ファイルを指定してください。' );
			return false;
		} else if ( !document.exc_upload.excel_file.value.toUpperCase().match(/.*\.(XLSX)$/))
		{
			alert( '拡張子がxlsxのファイルを指定してください' );
			return false;
		}
	
		var windowName = 'workSheetView';
		
        // 子ウィンドウの表示        
        if(workForm.length == 0){
	        var IFrames = $('iframe');
	        
	        for (var i = 0; i < IFrames.length;i++){
	        	if(IFrames[i].id == 'SegAIFrm') {
	        		//workForm = IFrames[i].find('form').children(0).getElementById("myid");
	        		workForm = IFrames[i].contentDocument.forms[0];
	        		workForm = $(workForm);
	        		break;
	        	}
	        }
        }
        
        // フォーム設定
        workForm.get(0).target = windowName;
        workForm.get(0).method = 'post';
        var baseURI = workForm.get(0).baseURI;
        
        if(baseURI.indexOf('/upload2/index.php') > 0) {
			var windowResult = open('about:blank', windowName, 'scrollbars=yes, width=985, height=700, resizable=0 location=0');
			var getParam = getURLGetParam('strSessionID');
			var inputSessionIDTag = '<input type="hidden" name="strSessionID" value="'+ getParam + '">';
			workForm.append(inputSessionIDTag);
        	workForm.get(0).action = '/estimate/regist/select.php';
        	// サブミット
			workForm.submit();
			// $(windowResult).load(function(){
			// 	$('#messageArea', win.document).append("<div>open直後に設定</div>");
			// });
        }
        else {
			alert('URLが不正です');
			return false;
        }

        
	});
	
	/**
	 * URLのゲットパラメータを取得する
	 *
	 * @param  name {string} パラメータのキー文字列
	 * @return  url {url} 対象のURL文字列（任意）
	 */
	function getURLGetParam(name, url) {
		if (!url) url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}

})();


function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}

function fncUploadButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = uploadJ1;
			break;

		case 'onJ':
			obj.src = uploadJ2;
			break;

		case 'downJ':
			obj.src = uploadJ3;
			break;

		default:
			break;
	}
}

// アップロードgif
var uploadJ1 = '/img/type01/upload2/upbt_off.gif';
var uploadJ2 = '/img/type01/upload2/upbt_on_off.gif';
var uploadJ3 = '/img/type01/upload2/upbt_on.gif';

