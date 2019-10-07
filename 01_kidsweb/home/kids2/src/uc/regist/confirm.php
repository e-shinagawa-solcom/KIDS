<?
/** 
*	�桼�������� ��ǧ����
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.04.14	�桼�������ܵҤǤ��ä����ˡ�ɽ������ɽ�����ڤ��ؤ����ʤ��Х��ν���
*
*/
// edit.php -> strSessionID           -> confirm.php
// edit.php -> lngFunctionCode        -> confirm.php
// edit.php -> bytInvalidFlag         -> confirm.php
// edit.php -> lngUserCode            -> confirm.php
// edit.php -> strUserID              -> confirm.php
// edit.php -> strPassword            -> confirm.php
// edit.php -> strPasswordCheck       -> confirm.php
// edit.php -> strMailAddress         -> confirm.php
// edit.php -> bytMailTransmitFlag    -> confirm.php
// edit.php -> strUserDisplayCode     -> confirm.php
// edit.php -> strUserDisplayName     -> confirm.php
// edit.php -> strUserFullName        -> confirm.php
// edit.php -> lngAttributeCode       -> confirm.php
// edit.php -> lngCompanyCode         -> confirm.php
// edit.php -> lngGroupCode           -> confirm.php
// edit.php -> lngAuthorityGroupCode  -> confirm.php
// edit.php -> lngAccessIPAddressCode -> confirm.php
// edit.php -> strNote                -> confirm.php
// edit.php -> strUserImageFileName   -> confirm.php

// �¹Ԥ�
// confirm.php -> strSessionID           -> action.php
// confirm.php -> lngFunctionCode        -> action.php
// confirm.php -> bytInvalidFlag         -> action.php
// confirm.php -> lngUserCode            -> action.php
// confirm.php -> strUserID              -> action.php
// confirm.php -> strPassword            -> action.php
// confirm.php -> strPasswordCheck       -> action.php
// confirm.php -> strMailAddress         -> action.php
// confirm.php -> bytMailTransmitFlag    -> action.php
// confirm.php -> strUserDisplayCode     -> action.php
// confirm.php -> strUserDisplayName     -> action.php
// confirm.php -> strUserFullName        -> action.php
// confirm.php -> lngAttributeCode       -> action.php
// confirm.php -> lngCompanyCode         -> action.php
// confirm.php -> lngGroupCode           -> action.php
// confirm.php -> lngAuthorityGroupCode  -> action.php
// confirm.php -> lngAccessIPAddressCode -> action.php
// confirm.php -> strNote                -> action.php
// confirm.php -> strUserImageFileName   -> action.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_POST;


$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC5 . ")";
$aryCheck["bytInvalidFlag"]         = "english(1,7)";
$aryCheck["strUserID"]              = "null:numenglish(0,32767)";
$aryCheck["strUserDisplayCode"]     = "null:numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "null:length(0,120)";
$aryCheck["strUserFullName"]        = "null:length(0,120)";
$aryCheck["bytUserDisplayFlag"]     = "english(1,7)";
$aryCheck["bytMailTransmitFlag"]    = "english(1,7)";
$aryCheck["lngGroupCode"]           = "null:ascii(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "null:number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "null:number(-1,32767)";
$aryCheck["strNote"]                = "length(0,1000)";

// ��ҤΤߥ��顼��å��������Ѥ����ü����
$aryCheck["lngCompanyCode"]     = "null:number(0,32767,ɬ�ܹ��ܤ����Ϥ���Ƥ��ޤ���)";

$aryData["bytInvalidFlag_Error"]     = "visibility:hidden;";
$aryData["lngUserCode_Error"]        = "visibility:hidden;";
$aryData["strUserID_Error"]          = "visibility:hidden;";
$aryData["strPassword_Error"]        = "visibility:hidden;";
$aryData["strPasswordCheck_Error"]   = "visibility:hidden;";
$aryData["strMailAddress_Error"]     = "visibility:hidden;";
$aryData["strUserDisplayCode_Error"] = "visibility:hidden;";
$aryData["strUserDisplayName_Error"] = "visibility:hidden;";
$aryData["strUserFullName_Error"]    = "visibility:hidden;";
$aryData["lngCompanyCode_Error"]     = "visibility:hidden;";
$aryData["lngGroupCode_Error"]       = "visibility:hidden;";
$aryData["strNote_Error"]            = "visibility:hidden;";


// bytMailTransmitFlag �� Boolean ��
if ( $aryData["bytMailTransmitFlag"] == "t" )
{
	$aryCheck["strMailAddress"]      = "null:mail";
}
else
{
	$aryCheck["strMailAddress"]      = "mail";
}

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
//////////////////////////////////////////////////////////////////////////
// �桼��������ξ��
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 && fncCheckAuthority( DEF_FUNCTION_UC1, $objAuth ) )
{
	$aryData["lngUserCode"]           = $objAuth->UserCode;
	$aryCheck["lngUserCode"]          = "null:number(0,32767)";
	$aryData["lngUserCodeConditions"] = 1;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["strUserIDDisabled"]             = "disabled";
	$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	unset ( $aryCheck["bytInvalidFlag"] );
	unset ( $aryCheck["strUserID"] );
	unset ( $aryCheck["bytUserDisplayFlag"] );
	unset ( $aryCheck["lngAuthorityGroupCode"] );
	unset ( $aryCheck["lngAccessIPAddressCode"] );
}

//////////////////////////////////////////////////////////////////////////
// �桼������Ͽ�ξ��
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
{
	// �ܵҤǤʤ���Хѥ����ɬ��
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && $aryData["lngAttributeCode"] < 1 )
	{
		$aryCheck["strPassword"]        = "null:password(0,64)";
		$aryCheck["strPasswordCheck"]   = "null:password(0,64)";
	}
	else
	{
		$aryCheck["strPassword"]        = "password(0,64)";
		$aryCheck["strPasswordCheck"]   = "password(0,64)";
	}

	// �桼���������ɤϤʤ����ɤ�
	$aryData["lngUserCode_Error"] = "visibility:hidden;";
}

//////////////////////////////////////////////////////////////////////////
// �桼���������ξ��
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && fncCheckAuthority( DEF_FUNCTION_UC5, $objAuth ) )
{
	$aryData["lngUserCodeConditions"]  = 1;
	$aryData["lngGroupCodeConditions"] = 0;
	$aryCheck["lngUserCode"]          = "null:number(0,32767)";

}

//////////////////////////////////////////////////////////////////////////
// ����ʳ�(����ERROR)
//////////////////////////////////////////////////////////////////////////
else
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

//////////////////////////////////////////////////////////////////////////
// °�����ָܵҡפ��ä������ü����
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngAttributeCode"] > 0 )
{
	// ��������
	unset ( $aryData["bytInvalidFlag"] );
	unset ( $aryData["bytMailTransmitFlag"] );
	$aryData["lngAuthorityGroupCode"]         = 6;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["bytMailTransmitFlagDisabled"]   = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	if ( !$aryData["lngGroupCode"] )
	{
		$aryData["lngGroupCode"]              = ":0";
	}

	// ���Ͼ��������å�(�ʤ���м�ư����������)
	if ( ( !$aryData["strUserID"] || !$aryData["strUserDisplayCode"] || !$aryData["strPassword"] ) && $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 )
	{
		// ID��ư��������(̵�¥롼�פ��򤱤뤿��20����)
		for ( $i = 0; $i  < 20; $i++ )
		{
			// �桼����ID���Ϥ��ʤ��ä��鼫ư����
			if ( !$aryData["strUserID"] )
			{
				$aryData["strUserID"]          = "guest" . sprintf ( "%05d", fncGetSequence( "m_User.strUserID", $objDB ) );
			}

			// ɽ���桼�������������Ϥ��ʤ��ä��鼫ư����
			if ( !$aryData["strUserDisplayCode"] )
			{
				$aryData["strUserDisplayCode"] = sprintf ( "%03x", fncGetSequence( "m_User.strUserDisplayCode", $objDB ) );
			}

			// �ѥ�������Ϥ��ʤ��ä��鼫ư����
			if ( !$aryData["strPassword"] )
			{
				$aryData["strPassword"]        = sprintf ( "%.6s", MD5 ( $aryData["strUserID"] ) );
				$aryData["strPasswordCheck"]   = $aryData["strPassword"];
			}

			// ��ʣ�����å�
			list ( $bytErrorFlag, $a, $a ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], "", $objDB );

			if ( !$bytErrorFlag )
			{
				break;
			}
		}
	}
}

$lngErrorCount += $bytErrorFlag;

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );


//////////////////////////////////////////////////////////////////////////
// �桼������Ͽ�ξ��Υ桼���������å�
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && $aryData["lngCompanyCode"] )
{
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], "", $objDB );
	$aryData["lngUserCode_Error"]         = $aryError["lngUserCode"];
	$aryData["strUserID_Error"]           = $aryError["strUserID"];
	$aryData["strUserDisplayCode_Error"]  = $aryError["strUserDisplayCode"];
	$aryData["lngUserCode_Error_Message"] = $aryErrorMessage["lngUserCode"];
	$aryData["strUserID_Error_Message"]   = $aryErrorMessage["strUserID"];
	$aryData["strUserDisplayCode_Error_Message"] = $aryErrorMessage["strUserDisplayCode"];

	$lngErrorCount += $bytErrorFlag;
}


// �ѥ���ɰŹ沽
if ( $aryData["strPassword"] )
{
	$aryData["strPassword"]      = MD5 ( $aryData["strPassword"] );
	$aryData["strPasswordCheck"] = MD5 ( $aryData["strPasswordCheck"] );
}

//////////////////////////////////////////////////////////////////////////
// �桼�������ꡢ�桼���������ξ�硢�����Υ桼�����ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// �桼��������
	// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 1101, DEF_WARNING, "�桼���������ޤ���", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryUserData["bytInvalidFlag"]         = $objResult->bytinvalidflag;
	$aryUserData["lngUserCode"]            = $objResult->lngusercode;
	$aryUserData["strUserID"]              = $objResult->struserid;
	$aryUserData["bytMailTransmitFlag"]    = $objResult->bytmailtransmitflag;
	$aryUserData["strMailAddress"]         = $objResult->strmailaddress;
	$aryUserData["bytUserDisplayFlag"]     = $objResult->bytuserdisplayflag;
	$aryUserData["strUserDisplayCode"]     = $objResult->struserdisplaycode;
	$aryUserData["strUserDisplayName"]     = $objResult->struserdisplayname;
	$aryUserData["strUserFullName"]        = $objResult->struserfullname;
	$aryUserData["lngCompanyCode"]         = $objResult->lngcompanycode;
	$aryUserData["strCompanyName"]         = $objResult->strcompanyname;
	$aryUserData["lngGroupCode"]           = $objResult->lnggroupcode;
	$aryUserData["strGroupName"]           = $objResult->strgroupname;
	$aryUserData["lngAuthorityGroupCode"]  = $objResult->lngauthoritygroupcode;
	$aryUserData["strAuthorityGroupName"]  = $objResult->strauthoritygroupname;
	$aryUserData["lngAccessIPAddressCode"] = $objResult->lngaccessipaddresscode;
	$aryUserData["strAccessIPAddress"]     = $objResult->straccessipaddress;
	$aryUserData["strNote"]                = $objResult->strnote;
	$aryUserData["strUserImageFileName"]   = $objResult->struserimagefilename;

	$strConpanySelectWhere = "AND lngCompanyCode = " . $aryData["lngCompanyCode"];

	$objDB->freeResult( $lngResultID );

	// ��ǧ���ե饰���Ѵ�
	if ( $aryUserData["bytInvalidFlag"] == "f" )
	{
		$aryUserData["bytInvalidFlag"] = "checked";
	}
	else
	{
		$aryUserData["bytInvalidFlag"] = "";
	}

	// �桼��������ξ�硢�ѹ��ԲĹ��ܤ�������
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 )
	{
		$aryData["strUserID"]              = $aryUserData["strUserID"];
		$aryData["bytInvalidFlag"]         = $aryUserData["bytInvalidFlag"];
		$aryData["bytUserDisplayFlag"]     = $aryUserData["bytUserDisplayFlag"];
		$aryData["lngAuthorityGroupCode"]  = $aryUserData["lngAuthorityGroupCode"];
		$aryData["lngAccessIPAddressCode"] = $aryUserData["lngAccessIPAddressCode"];
	}
	
	// �桼������ʣ�����å�
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( $aryData["lngUserCode"], $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], $aryUserData["lngUserCode"], $aryUserData["strUserID"], $aryUserData["lngCompanyCode"], $aryUserData["strUserDisplayCode"], "UPDATE", $objDB );
	if ( $bytErrorFlag )
	{
		$aryData["lngUserCode_Error"]        = $aryError["lngUserCode"];
		$aryData["strUserID_Error"]          = $aryError["strUserID"];
		$aryData["strUserDisplayCode_Error"] = $aryError["strUserDisplayCode"];
		$aryData["lngUserCode_Error_Message"] = $aryErrorMessage["lngUserCode"];
		$aryData["strUserID_Error_Message"]   = $aryErrorMessage["strUserID"];
		$aryData["strUserDisplayCode_Error_Message"] = $aryErrorMessage["strUserDisplayCode"];

		$lngErrorCount += $bytErrorFlag;
	}

	// ���롼���ѹ������å�
	// ���Ϥ��줿���롼�ץ����ɤ����������
	$aryGroupCode = explode ( ":", $aryData["lngGroupCode"] );
	array_shift ( $aryGroupCode );
	$lngGroupCodeNum = count ( $aryGroupCode );

	// DB����Ͽ����Ƥ��륰�롼�ץ����ɤ����
	$strQuery = "SELECT lngGroupCode FROM m_GroupRelation WHERE lngUserCode = " . $aryUserData["lngUserCode"] . " ORDER BY lngGroupCode\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryGroupCodeOriginal[$i] = $objResult->lnggroupcode;
	}
	$lngGroupCodeOriginalNum = count ( $aryGroupCodeOriginal );

	// ��°�����������롼�ץ����ɤ����
	// ��Ȥ�Ȥν�°���롼��ʬ���롼��
	for ( $i = 0; $i < $lngGroupCodeOriginalNum; $i++ )
	{
		// �������Ϥ��줿���롼��ʬ���롼��
		for ( $j = 0; $j < $lngGroupCodeNum; $j++ )
		{
			// ���Ϥ��줿���롼�פ�����Ȥ�Ƚ�°���Ƥ������ɤ��������
			if ( $aryGroupCode[$j] == $aryGroupCodeOriginal[$i] )
			{
				$flgMatchDelete = 1;
				break 1;
			}
		}

		// ¸�ߤ��Ƥ��ʤ��ä���硢
		// ����ե������å��оݤȤ��ƥ�����WHERE������
		if ( !$flgMatchDelete )
		{
			$aryGroupCodeDelete[] = " lngWorkflowOrderGroupCode = $aryGroupCodeOriginal[$i]";
			$flgMatchArray  = 1;
		}

		// ¸�ߥե饰�����
		$flgMatchDelete = 0;
	}
	$objDB->freeResult( $lngResultID );
}


// �ѥ���ɥ����å�
if ( $aryData["strPassword"] && $aryData["strPasswordCheck"] && $aryData["strPassword"] != $aryData["strPasswordCheck"] )
{
	$lngErrorCount++;
	$aryData["strPassword_Error"]              = "visibility:visible;";
	$aryData["strPassword_Error_Message"]      = "��ǧ�ѥ���ɤ���äƤ��ޤ���";
	$aryData["strPasswordCheck_Error"]         = "visibility:visible;";
	$aryData["strPasswordCheck_Error_Message"] = "��ǧ�ѥ���ɤ���äƤ��ޤ���";
}
else
{
	$aryData["strPassword_Error"]      = "visibility:hidden;";
	$aryData["strPasswordCheck_Error"] = "visibility:hidden;";
}


//////////////////////////////////////////////////////////////////////////
// ɽ���Τ����ʸ����������Ϣ����
//////////////////////////////////////////////////////////////////////////
// �桼���������ξ�硢�ü�쥤������
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	$aryData["RENEW"] = TRUE;
}

// ��������ĥ����å��ܥå����Υ����å�
if ( $aryData["bytInvalidFlag"] == "f" )
{
	$aryData["bytInvalidFlag"] = "checked";
}
// �᡼���������ĥ����å��ܥå����Υ����å�
if ( $aryData["bytMailTransmitFlag"] == "t" )
{
	$aryData["bytMailTransmitFlag"] = "checked";
}
// �桼����ɽ�������å��ܥå����Υ����å�
if ( $aryData["bytUserDisplayFlag"] == "t" )
{
	$aryData["bytUserDisplayFlag"] = "checked";
}

// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
$lngErrorCount += $bytErrorFlag;

//////////////////////////////////////////////////////////////////////////
// ����UPLOAD
//////////////////////////////////////////////////////////////////////////
if ( $_FILES['binUserPic']['name'] != "" && preg_match ( "/image\/(" . USER_IMAGE_TYPE . ")$/i", $_FILES['binUserPic']['type'], $aryFileType ) && $_FILES['binUserPic']['size'] < IMAGE_LIMIT )
{
	$aryData["strUserPicName"]       = $aryData["strUserID"];
	$aryData["strUserImageFileName"] = MD5 ( $aryData["strUserPicName"] ) . "." . $aryFileType[1];

	if ( !move_uploaded_file( $_FILES['binUserPic']['tmp_name'], USER_IMAGE_TMPDIR . $aryData["strUserImageFileName"] ) )
	{
		$aryData["strUserImageFile_Error"]         = "visibility:visible;";
		$aryData["strUserImageFile_Error_Message"] = "�������åץ��ɤ˼��Ԥ��ޤ�����";
		$lngErrorCount++;
	}
}
//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
// ʸ��������å��˥��顼�������硢���ϲ��̤����


//���顼�����ä���
if( $lngErrorCount > 0 )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">\n";
	echo "<form action=\"/uc/regist/edit.php\" method=\"POST\">\n";
	echo getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );
	echo "</form>\n";
	echo "<script language=\"javascript\">document.forms[0].submit();</script>";
}
//���顼���ʤ��ä���
else
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">\n";
	echo "<form action=\"/uc/regist/action.php\" method=\"POST\">\n";
	echo getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );
	echo "</form>\n";
	echo "<script language=\"javascript\">document.forms[0].submit();</script>";

}

$objDB->close();


return TRUE;
?>


