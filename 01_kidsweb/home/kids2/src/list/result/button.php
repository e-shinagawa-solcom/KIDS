<?
/**
 *    Ģɼ���� ���ʴ��� �����ܥ�����ϲ���
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// �����ץ�ӥ塼����(FARAMESET)���
// frameset.php -> strSessionID       -> action.php
// frameset.php -> strReportClassCode -> action.php
// frameset.php -> strReportKeyCode   -> action.php
// frameset.php -> lngReportCode      -> action.php

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// �ǡ�������
//////////////////////////////////////////////////////////////////////////
$aryData = $_GET;

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_PO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
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

</head>
<body id="Backs" oncontextmenu="return false;">

<div>
	
<div align="right" style="width: 50%; display: inline-block;">
	<a href="<?echo $aryListOutputMenu[$aryData["lngReportClassCode"]]["file"]; ?>/action.php?strSessionID=<?echo $aryData["strSessionID"]; ?>&strReportKeyCode=<?echo $aryData["strReportKeyCode"]; ?>&lngReportCode=<?echo $aryData["lngReportCode"]; ?>" onclick="fncPrintFrame( parent.list )"><img onmouseover="fncPrintButton( 'on' , this );" onmouseout="fncPrintButton( 'off' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/list/print_off_bt.gif" width="72" height="20" border="0" alt="PRINT"></a>
</div>
<div align="right" style="width: 45%;font-size: 9pt;display: none" id="rePrint">
	<input name="rePrintFlag" type="checkbox">�ư��������ͤ˰�������
</div>
</div>

</body>
</html>