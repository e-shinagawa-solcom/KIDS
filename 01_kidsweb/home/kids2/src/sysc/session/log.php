<?
/** 
*	�����ƥ���� �����󥻥å�����������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ƥ�����ԥ����󥻥å�����������
// index.php   -> strSessionID -> session.php
//
// �����ƥ�����ԥ����̤�
// session.php -> strSessionID -> index.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
//require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"] = "null:numenglish(32,32)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����ƥ��������", TRUE, "", $objDB );
}


$arySuccessfulFlag = Array ( "t" => "����", "f" => "���ԡ���������" );

// �����ॢ���Ȼ��ּ���
$lngTimeOut = fncGetCommonFunction( "timeout", "m_CommonFunction", $objDB );

// ���å����ɽ���������
$lngSessionLimit = fncGetCommonFunction( "sessionlimit", "m_CommonFunction", $objDB );


// �����󥻥å�������
$strQuery = "SELECT * FROM t_LoginSession WHERE dtmLoginTime < ( now() - ( interval '$lngTimeOut min' ) ) ORDER BY dtmLoginTime DESC LIMIT " . $lngSessionLimit;

// ������桼����ɽ��
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// �쥳����ɽ��
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryResult[$i] = "<tr class=\"Segs\"><td align=\"center\">" . ( $i + 1 ) . "</td><td align=\"center\">" . $objResult->strsessionid . "</td><td align=\"center\">" . $objResult->lngusercode . "</td><td align=\"center\">" . $objResult->strloginuserid . "</td><td align=\"center\">" . $objResult->strloginpassword . "</td><td align=\"center\">" . $objResult->dtmlogintime . "</td><td align=\"center\">" . $arySuccessfulFlag[$objResult->bytsuccessfulflag] . "</td></tr>\n";
}

// ��̥ơ��֥�����
$aryParts["strRecordTable"] = join ( "", $aryResult );

$aryParts["strSessionID"] =& $aryData["strSessionID"];
$aryParts["LOGIN"] =& $aryResult[0];
$aryParts["LOG"]   =& $aryResult[1];

// HTML����
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
