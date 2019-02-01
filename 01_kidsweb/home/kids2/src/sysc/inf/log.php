<?
/** 
*	�����ƥ���� ����������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ƥ�����ԥ���������
// index.php -> strSessionID           -> log.php
//
// �����ƥ�����ԥ���������(PAGE�ѹ�)
// log.php -> strSessionID           -> log.php
// log.php -> lngPage                -> log.php
//
// �����ƥ�����ԥ��ܺٲ���
// log.php -> strSessionID             -> detail.php
// log.php -> lngSystemInformationCode -> detail.php
// log.php -> lngPage                  -> detail.php


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


$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngPage"]      = "number(0,)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����ƥ��������", TRUE, "", $objDB );
}


// �ڡ���ɽ���������
$lngViewRows = fncGetCommonFunction( "defaultnumberoflist", "m_CommonFunction", $objDB );

// �������ϹԿ�����
$lngOffsetRows = $lngViewRows * $aryData["lngPage"];


// ���Τ餻��������(����������1��¿��������ɽ���Ϸ���̤�)
$strQuery = "SELECT * FROM m_SystemInformation ORDER BY dtmInsertDate DESC LIMIT " . ( $lngViewRows + 1 ) . " OFFSET $lngOffsetRows";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// ɽ������򥻥å�
// �ڡ���ɽ�������Ķ���Ƥ�����硢1�����
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

// �ڡ�����ư�ܥ���ɽ������
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


// HTML����
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
