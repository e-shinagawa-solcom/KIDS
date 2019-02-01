<?
/** 
*	�����ƥ���� ���ܺٲ���
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ƥ�����ԥ��ܺٲ���
// log.php -> strSessionID             -> detail.php
// log.php -> lngSystemInformationCode -> detail.php
// log.php -> lngPage                  -> detail.php
//
// �����ƥ�����ԥ���������(PAGE�ѹ�)��
// detail.php -> strSessionID          -> log.php
// detail.php -> lngPage               -> log.php


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
$aryData["lngPage"] = 0;
$aryData = $_GET;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]             = "null:numenglish(32,32)";
$aryCheck["lngPage"]                  = "number(0,)";
$aryCheck["lngSystemInformationCode"] = "null:number(0,)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����ƥ��������", TRUE, "", $objDB );
}


// ���Τ餻��������(����������1��¿��������ɽ���Ϸ���̤�)
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
$aryParts["lngPage"]      =& $aryData["lngPage"];


// HTML����
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
<meta http-equiv="content-type" content="text/html; charset=euc-jp">
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
