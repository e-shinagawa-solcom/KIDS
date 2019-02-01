

//-------------------------------------------------------------------
// 解説 : グローバル変数[g_strEmail]から入力済みEmailAddress値の取得
//-------------------------------------------------------------------
var strEmailAddr = parent.g_strEmail;



//-------------------------------------------------------------------
// 解説 : サブミット処理関数
//-------------------------------------------------------------------
function fncRemindSubmit( objFrm , objName )
{
	// EmailAddress値の取得
	var strEmail = objName.value;

	// EmailAddressの形式が正しい場合
	if( strEmail.match(/.*@.*\..*/i) )
	{
		// グローバル変数[g_strEmail]に格納
		parent.g_strEmail = objName.value;

		// サブミット処理
		objFrm.submit();
	}
}



//-------------------------------------------------------------------
// 解説 : メッセージ書出し処理関数
//-------------------------------------------------------------------
function fncSetEmailAddress()
{
	// 変数[strEmailAddr]から値を取得
	var strEmail = strEmailAddr;

	if( typeof(strEmail) != 'undefined' )
	{
		// メッセージ書出し
		Message2.innerHTML = '<b>COMPLETED :</b><br>パスワード認証情報が [ <b>' + strEmail + '</b> ] 宛に送信されました。';
		//Message2.innerHTML = '<b>COMPLETED :</b><br>パスワード認証情報が、入力したメールアドレス宛に送信されました。';
	}
	else
	{
		// メッセージ書出し
		Message2.innerHTML = '<b>ERROR :</b><br>メールアドレスの形式が不正です。';
	}

	// 変数[strEmail]の初期化
	parent.g_strEmail = '';
}



//-------------------------------------------------------------------
// 解説 : [ENTER]キー押下時サブミット処理関数
//-------------------------------------------------------------------
window.document.onkeydown=fncEnterKeyDown;

function fncEnterKeyDown( e )
{
	if( typeof(SubmitButton) != 'undefined' )
	{
		if( window.event.keyCode == 13 ||
			window.event.keyCode == 14 )
		{

			fncAlphaOn( document.all.submitbutton );
			fncRemindSubmit( window.frmRemind , document.all.strMailAddress );

		}
	}

}