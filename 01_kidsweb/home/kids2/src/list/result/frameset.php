<?
/**
 *    Ģɼ���� ���ʴ��� �����ץ�ӥ塼����(FARAMESET)
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// ������̲��̤��
// index.php -> strSessionID       -> frameset.php
// index.php -> lngReportClassCode -> frameset.php
// index.php -> strReportKeyCode   -> frameset.php
// index.php -> lngReportCode      -> frameset.php

// ȯ��ܺ٤���
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php
// frameset.php -> bytCopyFlag      -> listoutput.php

// �ץ�ӥ塼���̤�
// frameset.php -> strSessionID       -> action.php
// frameset.php -> strReportKeyCode   -> action.php
// frameset.php -> lngReportCode      -> action.php

// �����ܥ�����ϲ��̤�
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
<!-- jQuery -->
<script src="/cmn/jquery/jquery-3.1.0.js"></script>
<!-- jQuery Cookie -->
<script src="/cmn/jquery/jquery-cookie-1.4.1.js"></script>
<!-- jQuery UI -->
<script src="/cmn/jquery/ui/jquery-ui-1.12.0.js"></script>
<script src="/list/result/cmn/frameset.js"></script>
</head>


<frameset rows="40,1,*" frameborder="0" border="0" framespacing="0">
	<frame id="button" src="button.php?strSessionID=<?echo $aryData["strSessionID"]; ?>&lngReportClassCode=<?echo $aryData["lngReportClassCode"]; ?>&strReportKeyCode=<?echo $aryData["strReportKeyCode"]; ?>&lngReportCode=<?echo $aryData["lngReportCode"]; ?>" name="button" scrolling="no" noresize>
	<frame src="/list/printset/borders.html" scrolling="no" noresize>
	<frame src="<?echo $aryListOutputMenu[$aryData["lngReportClassCode"]]["file"]; ?>/listoutput.php?strSessionID=<?echo $aryData["strSessionID"]; ?>&strReportKeyCode=<?echo $aryData["strReportKeyCode"]; ?>&lngReportCode=<?echo $aryData["lngReportCode"]; ?>&bytCopyFlag=<?echo $aryData["bytCopyFlag"]; ?>" name="list" noresize>
</frameset>


</html>