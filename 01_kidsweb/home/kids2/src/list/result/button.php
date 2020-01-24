<?
/**
 *    帳票出力 商品企画書 印刷ボタン出力画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// 印刷プレビュー画面(FARAMESET)より
// frameset.php -> strSessionID       -> action.php
// frameset.php -> strReportClassCode -> action.php
// frameset.php -> strReportKeyCode   -> action.php
// frameset.php -> lngReportCode      -> action.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// データ取得
//////////////////////////////////////////////////////////////////////////
$aryData = $_GET;

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_PO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

if ($aryListOutputMenu[$aryData["lngReportClassCode"]]["file"] == "slip") {
	$function = "fncDownload('slip/action.php?strSessionID=" . $aryData["strSessionID"]
	 . "&strReportKeyCode=" . $aryData["strReportKeyCode"]
	 . "&lngReportCode=" . $aryData["lngReportCode"] ."')";
	 $url = "#";
} else {
	$function = "fncPrintFrame( parent.list )";
	$url = $aryListOutputMenu[$aryData["lngReportClassCode"]]["file"] . "/action.php?strSessionID=" . $aryData["strSessionID"]
	. "&strReportKeyCode=" . $aryData["strReportKeyCode"]
	. "&lngReportCode=" . $aryData["lngReportCode"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="ja">
	<head>
	<title>K.I.D.S.</title>
	<meta http-equiv="content-type" content="text/html; charset=euc-jp">

	<script type="text/javascript" language="javascript" src="/cmn/functions.js"></script>
	<script type="text/javascript" language="javascript" src="/list/printset/functions.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="/list/printset/layout.css">
	<!-- <script src="/cmn/jquery/jquery-3.1.0.js"></script>
	<script src="/cmn/jquery/jquery-cookie-1.4.1.js"></script>
	<script src="/cmn/jquery/ui/jquery-ui-1.12.0.js"></script> -->
	<script src="/list/result/slip/functions.js"></script>
	<script src="/list/result/inv/functions.js"></script>
</head>
<body id="Backs" oncontextmenu="return false;">
<div>
<div align="right" style="width: 50%; display: inline-block;">

	<!-- <a id="print" href="<?echo $aryListOutputMenu[$aryData["lngReportClassCode"]]["file"]; ?>/action.php?strSessionID=<?echo $aryData["strSessionID"]; ?>&strReportKeyCode=<?echo $aryData["strReportKeyCode"]; ?>&lngReportCode=<?echo $aryData["lngReportCode"]; ?>" onclick="<?echo $function; ?>"> -->
	<a id="print" href="<?echo $url; ?>" onclick="<?echo $function; ?>">
	<img onmouseover="fncPrintButton( 'on' , this );" onmouseout="fncPrintButton( 'off' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/list/print_off_bt.gif" width="72" height="20" border="0" alt="PRINT"></a>
</div>
<div align="right" style="width: 45%;font-size: 9pt;display: none" id="rePrint">
	<input id="rePrintChk" type="checkbox">再印刷を備考に印字する
</div>
</div>

</body>
</html>