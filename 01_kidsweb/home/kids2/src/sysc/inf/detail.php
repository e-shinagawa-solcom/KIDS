<?
/** 
*	システム管理 ログ詳細画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// システム管理者ログ詳細画面
// log.php -> strSessionID             -> detail.php
// log.php -> lngSystemInformationCode -> detail.php
// log.php -> lngPage                  -> detail.php
//
// システム管理者ログ閲覧画面(PAGE変更)へ
// detail.php -> strSessionID          -> log.php
// detail.php -> lngPage               -> log.php


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
$aryData["lngPage"] = 0;
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]             = "null:numenglish(32,32)";
$aryCheck["lngPage"]                  = "number(0,)";
$aryCheck["lngSystemInformationCode"] = "null:number(0,)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "システム管理失敗", TRUE, "", $objDB );
}


// お知らせ記事取得(取得件数より1件多く取得、表示は件数通り)
$strQuery = "SELECT * FROM m_SystemInformation WHERE lngSystemInformationCode = " . $aryData["lngSystemInformationCode"];

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum == 1 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryParts["strSystemInformationTitle"] = fncHTMLSpecialChars( $objResult->strsysteminformationtitle );
	$aryParts["dtmInsertDate"]             = $objResult->dtminsertdate;
	$aryParts["strSystemInformationBody"]  = $objResult->strsysteminformationbody;
}
$aryParts["strSessionID"] =& $aryData["strSessionID"];
if ($aryData["lngPage"] == '') {
	$aryParts["lngPage"] = 0;
} else {
	$aryParts["lngPage"]      =& $aryData["lngPage"];
}
// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/inf/detail.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
<!--
<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<p>TITLE:<? echo $aryParts["strSystemInformationTitle"]; ?></p>
<p>BODY:<? echo $aryParts["strSystemInformationBody"]; ?></p>
<p>DATE:<? echo $aryParts["dtmInsertDate"]; ?></p>
<p><a href="log.php?strSessionID=<? echo $aryParts["strSessionID"]; ?>&lngPage=<? echo $aryParts["lngPage"]; ?>">BACK</a></p>
</body>
</html>
-->
<?

return TRUE;
?>
