<?
/** 
*	見積原価管理 TOP画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

if ( $_GET["strSessionID"] )
{
	$aryData["strSessionID"]    = $_GET["strSessionID"];
}
else
{
	$aryData["strSessionID"]    = $_POST["strSessionID"];
}

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_UC0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

$aryData["visibility1"]			= "visible";
$aryData["visibility2"]			= "hidden";
$aryData["upload_visibility"]	= 'visible';

if ( fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	$aryData["visibility2"]      = "visible";
}

// アップロード
if ( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
	$aryData["upload_visibility"]	= 'hidden';
}





	// ユーザーコード取得
	$lngUserCode = $objAuth->UserCode;

	// 権限グループコード(ユーザー以下)チェック
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 「ユーザー」以下の場合
	if( $blnAG )
	{
		// 承認ルート存在チェック
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// 承認ルートが存在しない場合
		if( !$blnWF )
		{
			$aryData["visibility1"] = 'hidden';
		}
		else
		{
			$aryData["visibility1"] = 'visible';
		}
	}


$objDB->close();


// ヘルプリンク用機能コードをセット
$aryData["lngFunctionCode"] = DEF_FUNCTION_E0;

$aryData["lngFunctionCode1"] = DEF_FUNCTION_E1;

// HTML出力
echo fncGetReplacedHtml( "estimate/parts.tmpl", $aryData, $objAuth );
?>
