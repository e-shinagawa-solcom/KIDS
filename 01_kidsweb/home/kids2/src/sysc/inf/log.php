<?
/** 
*	システム管理 ログ閲覧画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// システム管理者ログ閲覧画面
// index.php -> strSessionID           -> log.php
//
// システム管理者ログ閲覧画面(PAGE変更)
// log.php -> strSessionID           -> log.php
// log.php -> lngPage                -> log.php
//
// システム管理者ログ詳細画面
// log.php -> strSessionID             -> detail.php
// log.php -> lngSystemInformationCode -> detail.php
// log.php -> lngPage                  -> detail.php


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
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngPage"]      = "number(0,)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "システム管理失敗", TRUE, "", $objDB );
}


// ページ表示件数取得
$lngViewRows = fncGetCommonFunction( "defaultnumberoflist", "m_CommonFunction", $objDB );

// 取得開始行数算出
$lngOffsetRows = $lngViewRows * $aryData["lngPage"];


// お知らせ記事取得(取得件数より1件多く取得、表示は件数通り)
$strQuery = "SELECT * FROM m_SystemInformation ORDER BY dtmInsertDate DESC LIMIT " . ( $lngViewRows + 1 ) . " OFFSET $lngOffsetRows";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// 表示件数をセット
// ページ表示件数を超えていた場合、1を引く
$lngResultOutputRows = $lngResultNum;
if ( $lngResultNum > $lngViewRows )
{
	$lngResultOutputRows -= 1;
}
for ( $i = 0; $i < $lngResultOutputRows; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryParts["RESULT"] .= "<tr class=\"Segs\"><td align=\"center\">" . ( $aryData["lngPage"] * $lngViewRows + $i + 1 ) . "</td><td>" . fncHTMLSpecialChars( $objResult->strsysteminformationtitle ) . "</td><td align=\"center\">" . $objResult->dtminsertdate . "</td><td align=\"center\"><a href=detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngSystemInformationCode=" . $objResult->lngsysteminformationcode . "&lngPage=" . $aryData["lngPage"] . "><img onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);fncAlphaOff( this );\" src=\"/img/" . LAYOUT_CODE . "/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>\n";
}

// ページ移動ボタン表示処理
$aryParts["REVIEW"] = "";
$aryParts["NEXT"]   = "";
$aryParts["strSessionID"] =& $aryData["strSessionID"];
if ( $aryData["lngPage"] > 0 )
{
	$aryParts["REVIEW"] = "window.location='log.php?strSessionID=" . $aryData["strSessionID"] . "&lngPage=" . ( $aryData["lngPage"] - 1 ) . "';";
}
if ( $lngViewRows < $lngResultNum )
{
	$aryParts["NEXT"] = "window.location='log.php?strSessionID=" . $aryData["strSessionID"] . "&lngPage=" . ( $aryData["lngPage"] + 1 ) . "';";
}


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/inf/log.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
<!--
<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=euc-jp">
</head>
<body>
<table border>
<? echo $aryParts["RESULT"]; ?>
</table>
<a<? echo $aryParts["REVIEW"]; ?>>REVIEW</a>
|
<a<? echo $aryParts["NEXT"]; ?>>NEXT</a>
</body>
</html>
-->
<?

return TRUE;
?>
