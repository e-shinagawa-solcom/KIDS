<?
/** 
*	�桼�������� �ܺپ���ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID           -> ditail.php
// index.php -> lngFunctionCode        -> ditail.php
// index.php -> bytInvalidFlag         -> ditail.php
// index.php -> lngUserCode            -> ditail.php
// index.php -> strUserID              -> ditail.php
// index.php -> strMailAddress         -> ditail.php
// index.php -> bytMailtransmitFlag    -> ditail.php
// index.php -> strUserDisplayCode     -> ditail.php
// index.php -> strUserDisplayName     -> ditail.php
// index.php -> strUserFullName        -> ditail.php
// index.php -> lngCompanyCode         -> ditail.php
// index.php -> lngGroupCode           -> ditail.php
// index.php -> lngAuthorityGroupCode  -> ditail.php
// index.php -> lngAccessIPAddressCode -> ditail.php
// index.php -> strNote                -> ditail.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GET�ǡ�������
$aryData = $_GET;


$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC4 . "," . DEF_FUNCTION_UC4 . ")";
$aryCheck["bytInvalidFlag"]         = "english(4,5)";
$aryCheck["lngUserCode"]            = "null:number(0,32767)";
$aryCheck["strUserID"]              = "numenglish(0,32767)";
$aryCheck["strMailAddress"]         = "mail";
$aryCheck["strUserDisplayCode"]     = "numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "length(0,120)";
$aryCheck["strUserFullName"]        = "length(0,120)";
$aryCheck["lngCompanyCode"]         = "number(0,32767)";
$aryCheck["lngGroupCode"]           = "number(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "number(0,32767)";
$aryCheck["strNote"]                = "length(0,1000)";

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_UC4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
$strURL = fncGetURL( $aryData );

// �桼���������ɤˤ���︡����ͭ����
$aryData["lngUserCodeConditions"] = 1;
// ���롼�׾��̵����
$aryData["lngGroupCodeConditions"] = 0;


$aryInvalidFlag      = Array ("t" => "�Ե���", "f" => "����" );
$aryMailTransmitFlag = Array ("t" => "����",   "f" => "�Ե���" );
$aryUserDisplayFlag  = Array ("t" => "ɽ��",   "f" => "��ɽ��" );


// �桼��������
// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
$objResult = $objDB->fetchObject( $lngResultID, 0 );

$partsData["bytInvalidFlag"]        = $objResult->bytinvalidflag;
$partsData["lngUserCode"]           = $objResult->lngusercode;
$partsData["strUserID"]             = $objResult->struserid;
$partsData["bytMailTransmitFlag"]   = $objResult->bytmailtransmitflag;
$partsData["strMailAddress"]        = $objResult->strmailaddress;
$partsData["strUserDisplayCode"]    = $objResult->struserdisplaycode;
$partsData["strUserDisplayName"]    = $objResult->struserdisplayname;
$partsData["bytUserDisplayFlag"]    = $objResult->bytuserdisplayflag;
$partsData["strUserFullName"]       = $objResult->struserfullname;
$partsData["strCompanyDisplayCode"] = $objResult->strcompanydisplaycode;
$partsData["strCompanyName"]        = $objResult->strcompanyname;
$partsData["strAuthorityGroupName"] = $objResult->strauthoritygroupname;
$partsData["strAccessIPAddress"]    = $objResult->straccessipaddress;
$partsData["strUserImageFileName"]  = $objResult->struserimagefilename;
$partsData["strNote"]               = $objResult->strnote;

// ��°���롼��ɽ��ʸ��������
$partsData["aryGroup"] = "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupname . "<br>\n";


// ��������ĥե饰��ɽ���Ѵ�
$partsData["bytInvalidFlag"] = $aryInvalidFlag[$partsData["bytInvalidFlag"]];

// �᡼���ۿ����ĥե饰��ɽ���Ѵ�
$partsData["bytMailTransmitFlag"] = $aryMailTransmitFlag[$partsData["bytMailTransmitFlag"]];

// �桼����ɽ���ե饰��ɽ���Ѵ�
$partsData["bytUserDisplayFlag"] = $aryUserDisplayFlag[$partsData["bytUserDisplayFlag"]];

// �桼����ɽ��������ɽ���Ѵ�
if ( $partsData["strUserImageFileName"] )
{
	$partsData["strUserImageFileName"] = USER_IMAGE_URL . $partsData["strUserImageFileName"];
}
else
{
	$partsData["strUserImageFileName"] = USER_IMAGE_DEFAULT_URL;
}

// ��°���롼��ʣ��ɽ������
for ( $i = 1; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	// ��°���롼��ɽ��ʸ��������
	$partsData["aryGroup"] .= "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupname . "<br>\n";
}

$partsData["strMode"] = "detail";

$objDB->freeResult( $lngResultID );

// �ѡ��ĥƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
//$objTemplate->getTemplate( "uc/result/detail.tmpl" );
$objTemplate->getTemplate( "uc/regist/confirm.tmpl" );
$objTemplate->replace( $partsData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;



$objDB->close();


return TRUE;
?>
