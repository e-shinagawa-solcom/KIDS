<?

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// データ取得
//////////////////////////////////////////////////////////////////////////
$aryData = $_GET;

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) || !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
// {
// 	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script src="/cmn/jquery/jquery-3.1.0.js"></script>
	<script src="/cmn/jquery/ui/jquery-ui-1.12.0.js"></script>
    <!-- jQuery Cookie -->
	<script src="/cmn/jquery/jquery-cookie-1.4.1.js"></script>
    <script src="/cmn/jquery/validation/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/cmn/functions.js"></script>
<script type="text/javascript" language="javascript" src="/lc/report/printset/functions.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/lc/report/printset/layout.css">

</head>
<body id="Backs" oncontextmenu="return false;">


<div align="center">
	<!-- <a href="" onclick="fncPrintFrame( parent.<? echo $aryData["printObj"]; ?>, '<? echo $aryData["nextUrl"];?>' )"><img onmouseover="fncPrintButton( 'on' , this );" onmouseout="fncPrintButton( 'off' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/list/print_off_bt.gif" width="72" height="20" border="0" alt="PRINT"></a> -->
	<a href="" onclick="fncPrintFrame( parent.<? echo $aryData["printObj"]; ?>, '<? echo $aryData["nextUrl"];?>' )"><img onmouseover="fncPrintButton( 'on' , this );" onmouseout="fncPrintButton( 'off' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/list/print_off_bt.gif" width="72" height="20" border="0" alt="PRINT"></a>

</div>


</body>
</html>