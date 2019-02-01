<?
/** 
*	システム管理 管理者設定画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// システム管理者設定完了画面
// index.php -> strSessionID              -> action.php
// index.php -> strSystemInformationTitle -> action.php
// index.php -> strSystemInformationBody  -> action.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
//require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_POST;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]              = "null:numenglish(32,32)";
$aryCheck["strSystemInformationTitle"] = "null:length(1,100)";
$aryCheck["strSystemInformationBody"]  = "null:length(1,1000)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );

// 改行数チェック(3行以上はエラー)
if ( preg_match ( "/\n/", $aryData["strSystemInformationBody"] ) > 2 )
{
	$aryCheckResult["strSystemInformationBody"] = TRUE;
}

// 文字列エラーチェック
if ( join ( "", $aryCheckResult ) != "" )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<form action=inf.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
	exit;
}

// タグ、CR削除
$aryData["strSystemInformationBody"] = preg_replace ( "/(<.+?>|\r)/", "", $aryData["strSystemInformationBody"] );


// 更新処理実行
$lngSeq = fncGetSequence( "m_SystemInformation.lngSystemInformationCode", $objDB );
$strQuery = "INSERT INTO m_SystemInformation VALUES ( $lngSeq, '" . $aryData["strSystemInformationTitle"] . "', '" . preg_replace ( "/\n/", "<br>", $aryData["strSystemInformationBody"] ) . "', now() )";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );



// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/inf/finish.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
