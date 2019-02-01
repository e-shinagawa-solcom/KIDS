<?
/** 
*	システム管理 ログインセッション閲覧画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// システム管理者ログインセッション閲覧画面
// index.php   -> strSessionID -> session.php
//
// システム管理者ログ画面へ
// session.php -> strSessionID -> index.php


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
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"] = "null:numenglish(32,32)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "システム管理失敗", TRUE, "", $objDB );
}


$arySuccessfulFlag = Array ( "t" => "成功", "f" => "失敗・ログアウト" );

// タイムアウト時間取得
$lngTimeOut = fncGetCommonFunction( "timeout", "m_CommonFunction", $objDB );

// セッション表示件数取得
$lngSessionLimit = fncGetCommonFunction( "sessionlimit", "m_CommonFunction", $objDB );


// ログインセッション取得
$strQuery = "SELECT * FROM t_LoginSession WHERE dtmLoginTime < ( now() - ( interval '$lngTimeOut min' ) ) ORDER BY dtmLoginTime DESC LIMIT " . $lngSessionLimit;

// ログインユーザー表示
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// レコード表示
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryResult[$i] = "<tr class=\"Segs\"><td align=\"center\">" . ( $i + 1 ) . "</td><td align=\"center\">" . $objResult->strsessionid . "</td><td align=\"center\">" . $objResult->lngusercode . "</td><td align=\"center\">" . $objResult->strloginuserid . "</td><td align=\"center\">" . $objResult->strloginpassword . "</td><td align=\"center\">" . $objResult->dtmlogintime . "</td><td align=\"center\">" . $arySuccessfulFlag[$objResult->bytsuccessfulflag] . "</td></tr>\n";
}

// 結果テーブル生成
$aryParts["strRecordTable"] = join ( "", $aryResult );

$aryParts["strSessionID"] =& $aryData["strSessionID"];
$aryParts["LOGIN"] =& $aryResult[0];
$aryParts["LOG"]   =& $aryResult[1];

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/session/log.tmpl" );
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
<h2>Active User</h2>
<table border>
<tr bgcolor="#99CCFF"><th>NO</th><td>SessionID</td><td>UserCode</td><td>UserID</td><td>Password</td><td>LoginTime</td><td>SuccessfulFlag</td></tr>
<? echo $aryParts["LOGIN"]; ?>
</table>
<h2>Log</h2>
<table border>
<tr bgcolor="#99CCFF"><th>NO</th><td>SessionID</td><td>UserCode</td><td>UserID</td><td>Password</td><td>LoginTime</td><td>SuccessfulFlag</td></tr>
<? echo $aryParts["LOG"]; ?>
</table>
<a href="/sysc/index.php?strSessionID=<? echo $aryParts["strSessionID"]; ?>">BACK</a>
</body>
</html>
-->
<?

return TRUE;
?>
