<?
/** 
*	�����ƥ���� �������������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ƥ���������괰λ����
// index.php -> strSessionID              -> action.php
// index.php -> strSystemInformationTitle -> action.php
// index.php -> strSystemInformationBody  -> action.php

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
$aryData = $_POST;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]              = "null:numenglish(32,32)";
$aryCheck["strSystemInformationTitle"] = "null:length(1,100)";
$aryCheck["strSystemInformationBody"]  = "null:length(1,1000)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );

// ���Կ������å�(3�԰ʾ�ϥ��顼)
if ( preg_match ( "/\n/", $aryData["strSystemInformationBody"] ) > 2 )
{
	$aryCheckResult["strSystemInformationBody"] = TRUE;
}

// ʸ���󥨥顼�����å�
if ( join ( "", $aryCheckResult ) != "" )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<form action=inf.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
	exit;
}

// ������CR���
$aryData["strSystemInformationBody"] = preg_replace ( "/(<.+?>|\r)/", "", $aryData["strSystemInformationBody"] );


// ���������¹�
$lngSeq = fncGetSequence( "m_SystemInformation.lngSystemInformationCode", $objDB );
$strQuery = "INSERT INTO m_SystemInformation VALUES ( $lngSeq, '" . $aryData["strSystemInformationTitle"] . "', '" . preg_replace ( "/\n/", "<br>", $aryData["strSystemInformationBody"] ) . "', now() )";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );



// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/inf/finish.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
