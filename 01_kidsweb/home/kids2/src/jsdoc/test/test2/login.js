<!--
//: ----------------------------------------------------------------------------
//: ファイル概要：
//:               共通関数
//: 備考        ：
//:               
//:
//: 作成日      ：YYYY/MM/MM
//: 作成者      ：** **
//: 修正履歴    ：
//: ----------------------------------------------------------------------------


//@*****************************************************************************
//  概要   ：PLATFORM CHECK
//******************************************************************************
function Checks(test1
				test2,
				test3)
{

	if (!((navigator.platform.indexOf('Win') > -1) &&
				(navigator.appName.indexOf('Microsoft') > -1) &&
				(navigator.appVersion.indexOf('MSIE 6') > -1))) 
	{
		location.href = '/error/confirm.html';
	}

	return false;

}


//@*****************************************************************************
//  概要   ：ログインチェック
//******************************************************************************
function LoginCheck() {

	document.frmLogin.action = "/login/login.php";
	document.frmLogin.submit();

	return false;
}


//@*****************************************************************************
//  概要   ：メニューへのリンク
//******************************************************************************
function GoMENU()
{

	location.href = '/menu/index.html';

	return false;

}

//-->