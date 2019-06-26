<?
/** 
*	�桼�������� �¹Բ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.04.14	�桼�������ܵҤǤ��ä����˥桼������ɽ������ɽ�����ڤ��ؤ����ʤ��Х��ν���
*
*/
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
//echo getArrayTable( $aryData, "TABLE" );exit;

// bytInvalidFlag �� Boolean ��
if ( $aryData["bytInvalidFlag"] == "checked" )
{
	$aryData["bytInvalidFlag"] = "FALSE";
}
else
{
	$aryData["bytInvalidFlag"] = "TRUE";
}

// bytMailTransmitFlag �� Boolean ��
if ( $aryData["bytMailTransmitFlag"] == "checked" )
{
	$aryData["bytMailTransmitFlag"] = "TRUE";
}
else
{
	$aryData["bytMailTransmitFlag"] = "FALSE";
}

// bytUserDisplayFlag �� Boolean ��
if ( $aryData["bytUserDisplayFlag"] == "checked" )
{
	$aryData["bytUserDisplayFlag"] = "TRUE";
}
else
{
	$aryData["bytUserDisplayFlag"] = "FALSE";
}



$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC5 . ")";
$aryCheck["bytInvalidFlag"]         = "null:english(4,5)";
$aryCheck["strUserID"]              = "null:numenglish(0,32767)";
$aryCheck["bytMailTransmitFlag"]    = "null:english(4,5)";
$aryCheck["bytUserDisplayFlag"]     = "null:english(4,5)";
$aryCheck["strUserDisplayCode"]     = "null:numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "null:length(0,120)";
$aryCheck["strUserFullName"]        = "null:length(0,120)";
$aryCheck["lngCompanyCode"]         = "null:number(0,32767)";
$aryCheck["lngGroupCode"]           = "null:ascii(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "null:number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "null:number(-1,32767)";
$aryCheck["strNote"]                = "length(0,1000)";

// �᡼���ۿ����ĥե饰�����äƤ�����᡼������ɬ��
if ( $aryData["bytMailTransmitFlag"] == "TRUE" )
{
	$aryCheck["strMailAddress"] = "null:mail";
}
else
{
	$aryCheck["strMailAddress"] = "mail";
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
	$aryData["lngUserCodeConditions"] = 1;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["strUserIDDisabled"]             = "disabled";
	$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
}

//////////////////////////////////////////////////////////////////////////
// �桼������Ͽ�ξ��
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
{
	$aryCheck["strPassword"]        = "null:ascii(0,32767)";
	$aryCheck["strPasswordCheck"]   = "null:ascii(0,32767)";

	if ( $aryData["strPassword"] != $aryData["strPasswordCheck"] )
	{
		fncOutputError ( 1102, DEF_WARNING, "�ѥ���ɤ��ߥ��ޥå����Ƥ��ޤ���", TRUE, "", $objDB );
	}
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayID"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayID"], "", $objDB );

	$aryData["lngUserCode_Error"]         = $aryError["lngUserCode"];
	$aryData["strUserID_Error"]           = $aryError["strUserID"];
	$aryData["strUserDisplayCode_Error"]  = $aryError["strUserDisplayCode"];
	$aryData["lngUserCode_Error_Message"] = $aryErrorMessage["lngUserCode"];
	$aryData["strUserID_Error_Message"]   = $aryErrorMessage["strUserID"];
	$aryData["strUserDisplayCode_Error_Message"] = $aryErrorMessage["strUserDisplayCode"];
}

//////////////////////////////////////////////////////////////////////////
// �桼���������ξ��
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && fncCheckAuthority( DEF_FUNCTION_UC5, $objAuth ) )
{
	$aryData["lngUserCodeConditions"] = 1;
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
	$aryData["bytInvalidFlag"]                = "TRUE";
	$aryData["bytMailTransmitFlag"]           = "FALSE";
// 2004.04.14 suzukaze update start
//	$aryData["bytUserDisplayFlag"]            = "TRUE";
// 2004.04.14 suzukaze update end
	$aryData["lngAuthorityGroupCode"]         = 6;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["bytMailTransmitFlagDisabled"]   = "disabled";
// 2004.04.14 suzukaze update start
//	$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
// 2004.04.14 suzukaze update end
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	if ( !$aryData["lngGroupCode"] )
	{
		$aryData["lngGroupCode"]              = ":0";
	}
}

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );
//exit;

$objDB->transactionBegin();

// �桼�������ꡢ�桼���������ξ�硢�����Υ桼�����ǡ�������
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// �桼��������
	// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 1107, DEF_WARNING, "", TRUE, "", $objDB );
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
	$aryUserData["strUserImageFileName"]   = $objResult->struserimagefilename;
	$aryUserData["strNote"]                = $objResult->strnote;

	// �ե饰���Ѵ�
	if ( $aryUserData["bytInvalidFlag"] == "t" )
	{
		$aryUserData["bytInvalidFlag"] = "TRUE";
	}
	else
	{
		$aryUserData["bytInvalidFlag"] = "FALSE";
	}

	if ( $aryUserData["bytMailTransmitFlag"] == "t" )
	{
		$aryUserData["bytMailTransmitFlag"] = "TRUE";
	}
	else
	{
		$aryUserData["bytMailTransmitFlag"] = "FALSE";
	}

	if ( $aryUserData["bytUserDisplayFlag"] == "t" )
	{
		$aryUserData["bytUserDisplayFlag"] = "TRUE";
	}
	else
	{
		$aryUserData["bytUserDisplayFlag"] = "FALSE";
	}

	$strConpanySelectWhere = "AND lngCompanyCode = " . $aryData["lngCompanyCode"];

	$objDB->freeResult( $lngResultID );

	// �桼��������ξ�硢�ѹ��ԲĹ��ܤ�������
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 )
	{
		$aryData["strUserID"]             = $aryUserData["strUserID"];
		$aryData["bytInvalidFlag"]        = $aryUserData["bytInvalidFlag"];
		$aryData["bytUserDisplayFlag"]    = $aryUserData["bytUserDisplayFlag"];
		$aryData["lngAuthorityGroupCode"] = $aryUserData["lngAuthorityGroupCode"];
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

	////////////////////////////////////////////////////////////////
	// ���롼���ѹ������å�
	////////////////////////////////////////////////////////////////
	// ���Ϥ��줿���롼�ץ����ɤ����������
	$aryGroupCode = explode ( ":", $aryData["lngGroupCode"] );
	array_shift ( $aryGroupCode );
	$lngGroupCodeNum = count ( $aryGroupCode );

	// DB����Ͽ����Ƥ��륰�롼�ץ����ɤ������������˥��å�
	$strQuery = "SELECT lngGroupCode FROM m_GroupRelation WHERE lngUserCode = " . $aryUserData["lngUserCode"] . " ORDER BY lngGroupCode\n";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryGroupCodeOriginal[$i] = $objResult->lnggroupcode;
	}

	$objDB->freeResult( $lngResultID );
	$lngGroupCodeOriginalNum = count ( $aryGroupCodeOriginal );

	// ��°�����������롼�ץ����ɤ������������˥��å�
	// ��Ȥ�Ȥν�°���롼��ʬ���롼��
	for ( $i = 0; $i < $lngGroupCodeOriginalNum; $i++ )
	{
		// �������Ϥ��줿���롼��ʬ���롼��
		for ( $j = 0; $j < $lngGroupCodeNum; $j++ )
		{
			// ���Ϥ��줿���롼�פ�����Ȥ�Ƚ�°���Ƥ������ɤ��������
			if ( $aryGroupCode[$j] == $aryGroupCodeOriginal[$i] )
			{
				$flgDeleteArray = 1;
				break 1;
			}
		}

		// ¸�ߤ��Ƥ��ʤ��ä���硢
		// ����ե������å��оݤȤ��ƥ�����WHERE������
		if ( !$flgDeleteArray )
		{
			$aryGroupCodeDelete[] = " lngWorkflowOrderGroupCode = $aryGroupCodeOriginal[$i]";
			$flgUpdate = 1; // �ѹ��ե饰
		}
		// ¸�ߥե饰�����
		$flgDeleteArray = 0;
	}

	// ��°��Υ�줿���롼�פ�����ե�����������¸�ߤ��Ƥ�����硢���顼
	/*
	if ( $flgUpdate )
	{
		$strQuery = "SELECT lngWorkflowOrderGroupCode FROM m_WorkflowOrder WHERE lngInChargeCode = " . $aryUserData["lngUserCode"] . " AND (" . join ( " OR", $aryGroupCodeDelete ) . ") AND bytWorkflowOrderDisplayFlag = TRUE\n";
		list ( $lngResultId, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum )
		{
			$objDB->freeResult( $lngResultID );
			fncOutputError ( 1103, DEF_WARNING, "", TRUE, "", $objDB );
		}
		else
		{
			$aryQuery[0] = "DELETE FROM m_GroupRelation WHERE lngUserCode = " . $aryUserData["lngUserCode"] . " AND (" . join ( " OR", $aryGroupCodeDelete ) . ")\n";
			$aryQuery[0] = preg_replace ( "/lngWorkflowOrderGroupCode/", "lngGroupCode", $aryQuery[0] );
		}
	}
	*/

	// �桼���������ξ�硢��������ġ����¥��롼���ѹ������å�
	// ��������Ĥ򳰤�������ե����֤˴ޤޤ�Ƥ��Ƥ����
	// �ޤ���
	// ���¥��롼�פ��ѹ���������ե����֤˴ޤޤ�Ƥ������
	// ���顼�Ȥ���
	/*
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && ( ( $aryData["bytInvalidFlag"] != $aryUserData["bytInvalidFlag"] && $aryData["bytInvalidFlag"] == "TRUE" ) || ( $aryData["lngAuthorityGroupCode"] != $aryUserData["lngAuthorityGroupCode"] && ( $aryData["lngAuthorityGroupCode"] > 2 && $aryData["lngAuthorityGroupCode"] < 6 ) ) ) )
	{
		$strQuery = "SELECT lngWorkflowOrderCode FROM m_WorkflowOrder WHERE lngInChargeCode = " . $aryData["lngUserCode"];
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$aryData["bytInvalidFlag_Error"] = "visibility:visible;";
			$objDB->freeResult( $lngResultID );
			$bytErrorFlag = TRUE;
		}
	}
	*/

	// �桼���������ξ�硢�桼����ID�ѹ������å�
	// �桼����ID���ѹ����줿��硢��������֤ˤ��ä���硢���顼
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && $aryData["strUserID"] != $aryUserData["strUserID"] )
	{
		$strQuery = "SELECT date_trunc('second', l.dtmLoginTime ) AS remaining," .
	                " c.strValue AS timeout " .
	                "FROM t_LoginSession l, m_CommonFunction c " .
	                "WHERE l.strLoginUserID = '" . $aryUserData["strUserID"] . "'" .
	                " AND l.bytSuccessfulFlag = true" .
	                " AND c.strClass = 'timeout'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			// ���å������ݻ�����桼������ID�����
			$objResult = $objDB->fetchObject( $lngResultID, 0 );

			if ( time() - strtotime ( $objResult->remaining ) > $objResult->timeout * 60 )
			{
				$aryData["strUserID_Error"] = "visibility:visible;";
				$bytErrorFlag = TRUE;
			}
			$objDB->freeResult( $lngResultID );
		}
	}


	// �ɲå��롼�פΥ���������
	for ( $i = 0; $i < $lngGroupCodeNum; $i++ )
	{
		for ( $j = 0; $j < $lngGroupCodeOriginalNum; $j++ )
		{
			if ( $aryGroupCode[$i] == $aryGroupCodeOriginal[$j] )
			{
				$flgInsertArray = 1;
				break 1;
			}
		}
		if ( !$flgInsertArray )
		{
			$lngGroupRelationCode = fncGetSequence( "m_GroupRelation.lngGroupRelationCode", $objDB );
			$aryQuery[] = "INSERT INTO m_GroupRelation VALUES ( $lngGroupRelationCode, " . $aryData["lngUserCode"] . ", $aryGroupCode[$i], FALSE )";
			$flgUpdate = 1; // �ѹ��ե饰
		}
		$flgInsertArray = 0;
	}

	// �ǥե���ȥ��롼�פ�����
	//if ( $flgUpdate )
	//{
		// ���롼�״�Ϣ�ޥ����Υ�å�
		$aryQuery[] = "SELECT * FROM m_GroupRelation WHERE lngUserCode = " . $aryData["lngUserCode"] . " FOR UPDATE";

		// �桼������°���륰�롼�פ򤹤٤�FALSE��
		$aryQuery[] = "UPDATE m_GroupRelation SET bytdefaultflag = FALSE WHERE lngUserCode = " . $aryData["lngUserCode"];
		// $aryGroupCode[0] �Υ��롼�פ�TRUE��
		$aryQuery[] = "UPDATE m_GroupRelation SET bytdefaultflag = TRUE WHERE lngUserCode = " . $aryData["lngUserCode"] . " AND lngGroupCode = $aryGroupCode[0]";
	//}

	///////////////////////////////////////////////////////////////////////
	// ���̥ѥ�᡼�����ѹ�����
	///////////////////////////////////////////////////////////////////////
	// ��������ĥե饰
	if ( $aryData["bytInvalidFlag"] != $aryUserData["bytInvalidFlag"] )
	{
		$aryUpdate[] = "bytInvalidFlag = " . $aryData["bytInvalidFlag"];
	}

	// �桼����ID
	if ( $aryData["strUserID"] != $aryUserData["strUserID"] )
	{
		$aryUpdate[] = "strUserID = '" . $aryData["strUserID"] . "'";
	}

	// �ѥ����
	if ( $aryData["strPassword"] )
	{
		$aryUpdate[] = "strPasswordHash = '" . $aryData["strPassword"] . "'";
	}

	// �᡼�륢�ɥ쥹
	if ( $aryData["strMailAddress"] != $aryUserData["strMailAddress"] )
	{
		$aryUpdate[] = "strMailAddress = '" . $aryData["strMailAddress"] . "'";
	}

	// �᡼���ۿ����ĥե饰
	if ( $aryData["bytMailTransmitFlag"] != $aryUserData["bytMailTransmitFlag"] )
	{
		$aryUpdate[] = "bytMailTransmitFlag = " . $aryData["bytMailTransmitFlag"];
	}

	// �桼����ɽ���ե饰
	if ( $aryData["bytUserDisplayFlag"] != $aryUserData["bytUserDisplayFlag"] )
	{
		$aryUpdate[] = "bytUserDisplayFlag = '" . $aryData["bytUserDisplayFlag"] . "'";
	}

	// �桼����ɽ��������
	if ( $aryData["strUserDisplayCode"] != $aryUserData["strUserDisplayCode"] )
	{
		$aryUpdate[] = "strUserDisplayCode = '" . $aryData["strUserDisplayCode"] . "'";
	}

	// �桼����ɽ��̾
	if ( $aryData["strUserDisplayName"] != $aryUserData["strUserDisplayName"] )
	{
		$aryUpdate[] = "strUserDisplayName = '" . $aryData["strUserDisplayName"] . "'";
	}

	// �桼�����ե�͡���
	if ( $aryData["strUserFullName"] != $aryUserData["strUserFullName"] )
	{
		$aryUpdate[] = "strUserFullName = '" . $aryData["strUserFullName"] . "'";
	}

	// ���̾
	if ( $aryData["lngCompanyCode"] != $aryUserData["lngCompanyCode"] )
	{
		$aryUpdate[] = "lngCompanyCode = " . $aryData["lngCompanyCode"];
	}

	// ���¥�����
	if ( $aryData["lngAuthorityGroupCode"] != $aryUserData["lngAuthorityGroupCode"] )
	{
		$aryUpdate[] = "lngAuthorityGroupCode = " . $aryData["lngAuthorityGroupCode"];
	}

	// ��������IP���ɥ쥹������
	if ( $aryData["lngAccessIPAddressCode"] != $aryUserData["lngAccessIPAddressCode"] )
	{
		$aryUpdate[] = "lngAccessIPAddressCode = " . $aryData["lngAccessIPAddressCode"];
	}

	// �桼�������᡼���ե�����
	if ( $aryData["strUserImageFileName"] && $aryData["strUserImageFileName"] != $aryUserData["strUserImageFileName"] )
	{
		$aryUpdate[] = "strUserImageFileName = '" . $aryData["strUserImageFileName"] . "'";
	}

	// ����
	if ( $aryData["strNote"] != $aryUserData["strNote"] )
	{
		$aryUpdate[] = "strNote = '" . $aryData["strNote"] . "'";
	}

	// m_User UPDATE ����������
	if ( is_array($aryUpdate) && count ( $aryUpdate ) )
	{
		// �桼�����ޥ�����å�
		$aryQuery[] = "SELECT * FROM m_User WHERE lngUserCode = " . $aryData["lngUserCode"] . " FOR UPDATE";
		$aryQuery[] = "UPDATE m_User SET " . join ( ", ", $aryUpdate ) . " WHERE lngUserCode = " . $aryData["lngUserCode"];
	}

//	echo "<h1>UPDATE</h1>";
}

// �桼������Ͽ�ξ�硢INSERT
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 )
{
	// �桼������ʣ�����å�(��Ͽ�����桼����ID�Υ����å�)
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], "", $objDB );

	if ( $bytErrorFlag )
	{
		fncOutputError ( 1101, DEF_ERROR, "��ʣ", TRUE, "", $objDB );
	}

	// �桼���������ɡ���Ͽ����������
	$aryData["lngUserCode"] = fncGetSequence( "m_User.lngUserCode", $objDB );
	$aryQuery[0] = "INSERT INTO m_User VALUES (" .
                   "  $aryData[lngUserCode]," .
                   "  $aryData[lngCompanyCode]," .
                   "  $aryData[lngAuthorityGroupCode]," .
                   " '$aryData[strUserID]'," .
                   " '$aryData[strPassword]'," .
                   " '$aryData[strUserFullName]'," .
                   "  $aryData[bytMailTransmitFlag]," .
                   " '$aryData[strMailAddress]'," .
                   "  $aryData[bytUserDisplayFlag]," .
                   " '$aryData[strUserDisplayCode]'," .
                   " '$aryData[strUserDisplayName]'," .
                   "  $aryData[bytInvalidFlag]," .
                   "  $aryData[lngAccessIPAddressCode]," .
                   " '$aryData[strUserImageFileName]'," .
                   " '$aryData[strMyPageInfo]'," .
                   " '$aryData[strNote]' )";

	$aryGroupCode = explode ( ":", $aryData["lngGroupCode"] );

	// �ǥե���ȥ��롼����Ͽ
	$lngGroupRelationCode = fncGetSequence( "m_GroupRelation.lngGroupRelationCode", $objDB );
	$aryQuery[1] = "INSERT INTO m_GroupRelation VALUES ( $lngGroupRelationCode, $aryData[lngUserCode], $aryGroupCode[1], TRUE )";

	// ����¾�Υ��롼����Ͽ
	for ( $i = 2; $i < count ( $aryGroupCode ); $i++ )
	{
		$lngGroupRelationCode = fncGetSequence( "m_GroupRelation.lngGroupRelationCode", $objDB );
		$aryQuery[$i] = "INSERT INTO m_GroupRelation VALUES ( $lngGroupRelationCode, $aryData[lngUserCode], $aryGroupCode[$i], FALSE )";
	}

//	echo "<h1>INSERT</h1>";
}


//////////////////////////////////////////////////////////////////////////
// ����UPLOAD
//////////////////////////////////////////////////////////////////////////
if ( $aryData["strUserImageFileName"] )
{
	if ( !copy ( USER_IMAGE_TMPDIR . $aryData["strUserImageFileName"], USER_IMAGE_DIR . $aryData["strUserImageFileName"] ) )
	{
		fncOutputError ( 1106, DEF_FATAL, "", TRUE, "", $objDB );
	}
	if ( !unlink ( USER_IMAGE_TMPDIR . $aryData["strUserImageFileName"] ) )
	{
		fncOutputError ( 1106, DEF_FATAL, "", TRUE, "", $objDB );
	}
}



//////////////////////////////////////////////////////////////////////////
// ������¹�(�桼�����ɲ�)
//////////////////////////////////////////////////////////////////////////
for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
//	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();

// echo "<a href=\"javascript:opener.window.location.reload();window.close();\">CLOSE</a>";
// echo "<a href=\"javascript:location='/menu/menu.php?strSessionID=$aryData[strSessionID]';\">MENU</a>";
// echo getArrayTable( $aryData, "TABLE" );


//////////////////////////////////////////////////////////////////////////
// �᡼������(�桼�����ɲ�)
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && $aryData["lngAttributeCode"] < 1 && $aryData["bytInvalidFlag"] == "FALSE" && $aryData["bytMailTransmitFlag"] == "TRUE" )
{
	list ( $strSubject, $strBody ) = fncGetMailMessage( 1102, $aryData, $objDB );
	$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );
	if ( !$aryData["strMailAddress"] || !mail ( $aryData["strMailAddress"], $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" ) )
	{
		fncOutputError ( 9053, DEF_WARNING, "�᡼���������ԡ�", TRUE, "", $objDB );
	}
//	echo "�᡼������:$aryData[strMailAddress]";
}

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////


// �桼��������ξ��
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 )
{
	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/uc/regist/edit.php?strSessionID=".$aryData["strSessionID"]."&lngFunctionCode=1101";

	echo fncGetReplacedHtml( "uc/regist/finish1.tmpl", $aryData, $objAuth );
}
// �桼������Ͽ�ξ��
elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 )
{

	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/uc/regist/edit.php?strSessionID=".$aryData["strSessionID"]."&lngFunctionCode=1102";

	echo fncGetReplacedHtml( "uc/regist/finish1.tmpl", $aryData, $objAuth );
}
// �桼���������ξ��
elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// �����������Υ��ɥ쥹���� �ʰ�̵̣�������ͽ���
	$aryData["strAction"] = "/uc/search/index.php?strSessionID=";
	$aryData["strSessionID"] = $aryData["strSessionID"];


	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "uc/regist/finish.tmpl" );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}

$objDB->close();


return TRUE;
?>
