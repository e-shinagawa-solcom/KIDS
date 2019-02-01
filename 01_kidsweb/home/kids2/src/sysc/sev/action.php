<?
/** 
*	�����ƥ���� �����ƥ൯ư�������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ƥ൯ư���괰λ����
// index.php -> strSessionID  -> action.php
// index.php -> lngActionCode -> action.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
$aryCheck["lngActionCode"] = "null:number(" . DEF_ACTION_RESTART . "," . DEF_ACTION_STOP . ")";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����ƥ��������", TRUE, "", $objDB );
}


// �Ƶ�ư
if ( $aryData["lngActionCode"] == DEF_ACTION_RESTART )
{
	$strShell = DEF_PATH_RESTART;
	$strMessage = "�Ƶ�ư��";
}

// ���ȥå�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_STOP )
{
	$strShell = DEF_PATH_STOP;
	$strMessage = "�����";
}


// HTML����
/*
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
*/

echo $strMessage;
echo "<a href=/login.php>BACK</a>";


// ������¹�
if ( !$strShell || !$strResult = exec ( $strShell ) )
{
	fncOutputError ( 9052, DEF_WARNING, "������μ¹Ԥ˼��Ԥ��ޤ�����", TRUE, "", $objDB );
}
return TRUE;
?>
