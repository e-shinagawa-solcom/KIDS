<?
/**
 *    帳票出力 商品企画書 印刷プレビュー画面(FARAMESET)
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// 検索結果画面より
// index.php -> strSessionID       -> frameset.php
// index.php -> lngReportClassCode -> frameset.php
// index.php -> strReportKeyCode   -> frameset.php
// index.php -> lngReportCode      -> frameset.php

// 発注詳細から
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php
// frameset.php -> bytCopyFlag      -> listoutput.php

// プレビュー画面へ
// frameset.php -> strSessionID       -> action.php
// frameset.php -> strReportKeyCode   -> action.php
// frameset.php -> lngReportCode      -> action.php

// 印刷ボタン出力画面へ
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
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<!-- jQuery -->
<script src="/cmn/jquery/jquery-3.1.0.js"></script>
<!-- jQuery Cookie -->
<script src="/cmn/jquery/jquery-cookie-1.4.1.js"></script>
<!-- jQuery UI -->
<script src="/cmn/jquery/ui/jquery-ui-1.12.0.js"></script>
<script src="/list/result/cmn/frameset.js"></script>
</head>


<frameset rows="40,1,*" frameborder="0" border="0" framespacing="0">
	<frame id="button" src="button.php?strSessionID=<?echo $aryData["strSessionID"]; ?>&lngReportClassCode=<?echo $aryData["lngReportClassCode"]; ?>&lngSlipKindCode=<?echo $aryData["lngSlipKindCode"]; ?>&strReportKeyCode=<?echo $aryData["strReportKeyCode"]; ?>&lngReportCode=<?echo $aryData["lngReportCode"]; ?>" name="button" scrolling="no" noresize>
	<frame src="/list/printset/borders.html" scrolling="no" noresize>
	<frame src="<?echo $aryListOutputMenu[$aryData["lngReportClassCode"]]["file"]; ?>/listoutput.php?strSessionID=<?echo $aryData["strSessionID"]; ?>&strReportKeyCode=<?echo $aryData["strReportKeyCode"]; ?>&lngReportCode=<?echo $aryData["lngReportCode"]; ?>&bytCopyFlag=<?echo $aryData["bytCopyFlag"]; ?>" name="list" noresize>
</frameset>


</html>