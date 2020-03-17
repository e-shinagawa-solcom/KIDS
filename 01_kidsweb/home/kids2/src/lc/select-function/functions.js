

//@-------------------------------------------------------------------------------------------------------------------
/**
* ファイル概要 : LC関連処理
*
*
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/
//--------------------------------------------------------------------------------------------------------------------

//共通変数
var session_id;

//---------------------------------------------------
// 画面初期処理
//---------------------------------------------------
function lcInit( json_obj )
{
	var phpData = JSON.parse(json_obj);
	session_id = phpData.session_id;

	//userAuthの2桁目が1の場合は「L/C設定変更」が利用可能
	var auth = phpData.login_user_auth.substr(1, 1);
	if(auth != "1")
	{
		$("#lcsetBtn").prop("disabled", true);
	}

	//同一権限のユーザーがログイン済みの場合のコンファーム
	if(phpData.logined_flg == true){
		if( res = confirm("下記のログイン情報はログアウトしていません。\r\n" + phpData.lcInfoDate.lcgetdate + " " + phpData.lcInfoDate.lgusrname  + "強制ログアウトを実行しますか。") )
		{
			$.ajax({
				url:'../lcModel/lcinfo_ajax.php',
				type:'POST',
				data:{
					'method': 'logoutState',
					'lgno': phpData.login_state.login_obj.lgno
				}
			})
			// Ajaxリクエストが成功した時発動
			.done( (data) => {
			})
			// Ajaxリクエストが失敗した時発動
			.fail( (data) => {
			})
			// Ajaxリクエストが成功・失敗どちらでも発動
			.always( (data) => {

			});
		}
	}
}

/**
 * L/C情報取得初期処理
 * @param {} sessionId 
 */
function initLcinfo(sessionId)
{
	$.ajax({
		url:'../info/init.php',
		type:'POST',
		data:{
			'strSessionID': sessionId
		}
	})
	.done(function(data) {
		// Ajaxリクエストが成功
		var data = JSON.parse(data);
		var strURL = "";
		if (data.lcgetdate != "" && data.lcgetdate != null) {
			if( res = confirm("既にL/C情報の取得を行っています。最新の情報を取得しますか。")) 
			{
				strURL="/lc/info/index.php?strSessionID=" + data.strSessionID + "&aclcinitFlg=true"
			} else {
				strURL="/lc/info/index.php?strSessionID=" + data.strSessionID + "&aclcinitFlg=false"			
			}
		}
		else
		{
			strURL="/lc/info/index.php?strSessionID=" + data.strSessionID + "&aclcinitFlg=true"
		}

		window.open(strURL, 'LC INFO', 'width='+ screen.availWidth + ', height=' + (screen.availHeight - 50) + ', resizable=yes, scrollbars=yes, menubar=no');   
	})
	.fail(function() {
		alert("fail");
		// Ajaxリクエストが失敗
	});
}

function initLcset(sessionId)
{
	var strURL = "/lc/set/index.php?strSessionID=" + sessionId
	window.open(strURL, 'LC INFO', 'width=1000, height=650, resizable=yes, scrollbars=yes, menubar=no');   
}
