<?php
/**
*	�ᥤ���˥塼ɽ��������
*
*	�ᥤ���˥塼���̤�ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/
*	@copyright Copyright &copy; 2003, Wiseknot
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
*	@access    public
*	@version   1.00
*
*	��������
*	�ᥤ���˥塼���̤�ɽ������
*
*/

// �ᥤ���˥塼����
// index.php -> strSessionID    -> index.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require( SRC_ROOT. "menu/cmn/lib_submenu.php" );


$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData["strSessionID"]    = $_GET["strSessionID"];
setcookie("lngLanguageCode", 1,0,"/");

// ʸ��������å�
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ�ʥᥤ���˥塼���̡�
if ( !fncCheckAuthority( DEF_FUNCTION_MENU0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", FALSE, "", $objDB );
}

// ���ʴ�����˥塼
if ( fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
{
	$aryData["Pnavi_visibility"] = "visible";
}
else
{
	$aryData["Pnavi_visibility"] = "hidden";
}
// ���������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
{
	$aryData["SOnavi_visibility"] = "visible";
}
else
{
	$aryData["SOnavi_visibility"] = "hidden";
}
// ȯ�������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
{
	$aryData["POnavi_visibility"] = "visible";
}
else
{
	$aryData["POnavi_visibility"] = "hidden";
}
// ��������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
{
	$aryData["SCnavi_visibility"] = "visible";
}
else
{
	$aryData["SCnavi_visibility"] = "hidden";
}
// ����������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
{
	$aryData["PCnavi_visibility"] = "visible";
}
else
{
	$aryData["PCnavi_visibility"] = "hidden";
}
// ����ե�������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_WF0, $objAuth ) )
{
	$aryData["WFnavi_visibility"] = "visible";
}
else
{
	$aryData["WFnavi_visibility"] = "hidden";
}
// Ģɼ���ϥ�˥塼
if ( fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	$aryData["LOnavi_visibility"] = "visible";
}
else
{
	$aryData["LOnavi_visibility"] = "hidden";
}
// �ǡ����������ݡ��ȥ�˥塼
if ( fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
{
	$aryData["DEnavi_visibility"] = "visible";
}
else
{
	$aryData["DEnavi_visibility"] = "hidden";
}


// ���åץ��ɥ�˥塼
if ( fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
	$aryData["UPLOADnavi_visibility"] = "visible";
}
else
{
	$aryData["UPLOADnavi_visibility"] = "hidden";
}


// �桼����������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_UC0, $objAuth ) )
{
	$aryData["UCnavi_visibility"] = "";
}
else
{
	$aryData["UCnavi_visibility"] = "none";
}
// �ޥ�����������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	$aryData["Mnavi_visibility"] = "";
}
else
{
	$aryData["Mnavi_visibility"] = "none";
}

// �����ƥ������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_SYS0, $objAuth ) )
{
	$aryData["SYSnavi_visibility"] = "";
}
else
{
	$aryData["SYSnavi_visibility"] = "none";
}

// ���������˥塼
if ( fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
	$aryData["DATACLOSEDnavi_visibility"] = "";
}
else
{
	$aryData["DATACLOSEDnavi_visibility"] = "none";
}

// ���Ѥ�긶���׻���˥塼
if ( fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	$aryData["Enavi_visibility"] = "visible";
}
else
{
	$aryData["Enavi_visibility"] = "hidden";
}


// �ⷿ����
if ( fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth))
{
	$aryData["MMnavi_visibility"] = "visible";
}
else
{
	$aryData["MMnavi_visibility"] = "hidden";
}

// �ⷿĢɼ����
if ( fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth))
{
	$aryData["MRnavi_visibility"] = "visible";
}
else
{
	$aryData["MRnavi_visibility"] = "hidden";
}
// L/C����
if ( fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth))
{
	$aryData["LCnavi_visibility"] = "visible";
}
else
{
	$aryData["LCnavi_visibility"] = "hidden";
}

	// ���֥�˥塼����
	$aryData = fncSetSubMenu( $aryData, $objAuth, $objDB );



$aryData["strSessionID"] = $objAuth->SessionID;

// ���ߤΤ��Τ餻��������
$strQuery = "SELECT strSystemInformationTitle, strSystemInformationBody FROM m_SystemInformation ORDER BY dtmInsertDate DESC LIMIT 1";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryData["strMessageTitle"] = $objResult->strsysteminformationtitle;
	$aryData["strMessageBody"]  = $objResult->strsysteminformationbody;
}




$aryData["strSystemVersion"] = DEF_SYSTEM_VERSION;

// �إ���б�
$aryData["lngFunctionCode"] = DEF_FUNCTION_MENU0;

// HTML����
echo fncGetReplacedHtml( "menu/parts.tmpl", $aryData, $objAuth );


$objDB->close();


return TRUE;
?>
