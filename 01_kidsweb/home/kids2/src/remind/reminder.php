<?php
/** 
*	�ѥ���ɥ�ޥ���������ؿ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.02.26	�桼�����ޥ����������ˡ�����Ԥ��å����֤ˤƹ�������褦���ѹ�
*
*/

/**
* �桼�����������
*
*	�᡼�륢�ɥ쥹���Ф��롢�桼��������μ���
*
*	@param  String $strMailAddress 	�᡼�륢�ɥ쥹
*	@param  Object $objDB       DB���֥�������
*	@return Array or Boolean $aryData ���� FALSE ����
*	@access public
*/
function getMailAddressToInfo( $strMailAddress, $objDB )
{
	$strQuery  = "SELECT lngUserCode, strUserID, strUserDisplayName, bytMailTransmitFlag, bytInvalidFlag";
	$strQuery .= " FROM m_User";
	$strQuery .= " WHERE strMailAddress = '" . $strMailAddress . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// �����ͤ�ƥץ�ѥƥ����˥��å�
	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$aryData["lngUserCode"] = $objResult->lngusercode;
		$aryData["strUserID"] = $objResult->struserid;
		$aryData["strUserDisplayName"] = $objResult->struserdisplayname;
		$aryData["strMailAddress"] = $strMailAddress;
		if ( $objResult->bytmailtransmitflag == 't' )
		{
			$aryData["bytMailTransmitFlag"] = 1;
		}
		else
		{
			$aryData["bytMailTransmitFlag"] = 0;
		}
		if ( $objResult->bytinvalidflag == 't' )
		{
			$aryData["bytInvalidFlag"] = 1;
		}
		else
		{
			$aryData["bytInvalidFlag"] = 0;
		}
	}
	else
	{
		return FALSE;
	}

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}
	return $aryData;
}



/**
* �ѥ���ɾ����ѹ�
*
*	�оݥ桼�����Υѥ���ɾ�����ѹ��ؿ�
*
*	@param  String $lngUserCode 	�桼����������
*	@param  String $strPassword     �ѹ��ѥ����
*	@param  Object $objDB       DB���֥�������
*	@return Boolean TRUE ���� FALSE ����
*	@access public
*/
function setNewPassword( $lngUserCode, $strPassword, $objDB )
{
// 2004.02.26 suzukaze update start
	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �桼�����ޥ�����ι����оݹԤ��å����֤ˤ���
	$strQuery = "SELECT lngUserCode FROM m_User WHERE lngUserCode = " . $lngUserCode . " FOR UPDATE ";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
 	if ( !$lngResultNum )
	{
		return FALSE;
	}
	$objDB->freeResult( $lngResultID );

	$strQuery = "UPDATE m_User set strPasswordHash = '" . md5( $strPassword ) . "'";
	$strQuery .= " WHERE lngUserCode = $lngUserCode ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	$objDB->freeResult( $lngResultID );

	// ���ߥåȽ���
	$objDB->transactionCommit();

	return TRUE;
// 2004.02.26 suzukaze update end
}




// ---------------------------------------------------------------
/**
* IP���ɥ쥹�����å���ñ���IP���ɥ쥹�ΤߤΥ����å���
*
*	�����������Ƥ���桼�����ΣɣФ����Ĥ���Ƥ���ɣФ��ɤ����Υ����å�
*
*	@param  object  $objDB        DB���֥�������
*	@param  Object  $objAuth      ǧ�ڥ��֥�������
*	@return boolean TRUE,FALSE
*	@access public
*/
// ---------------------------------------------------------------
function checkAccessIPSimple( $objDB, $objAuth )
{
	// ��������IP���ɥ쥹�ơ��֥���䤤��碌
	$strQuery = "SELECT ip.strAccessIPAddress " .
	            "FROM m_AccessIPAddress ip ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}
		return FALSE;
	}

	// ����IP����
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryAccessIP = mb_split ( ",", $objResult->straccessipaddress );

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}

	// IP�ξȹ�
	foreach ( $aryAccessIP as $strAccessIP )
	{
		$strAccessIP = mb_ereg_replace ( "\.", "\.", $strAccessIP );
		$strAccessIP = mb_ereg_replace ( "\*", ".+?", $strAccessIP );
		if ( mb_ereg ( $strAccessIP, $objAuth->AccessIP ) )
		{
			return TRUE;
		}
	}
	return FALSE;
}



// ---------------------------------------------------------------
/**
* ���å�������γ�ǧ
*
*	���å����ơ��֥����оݥ��å����ɣĤΥ����å��ؿ�
*
*	@param  string  $strSessionID ���å����ID
*	@param  object  $objDB        DB���֥�������
*	@return object  $aryData      �桼��������
*			boolean FALSE         ���å�������۾�
*	@access public
*/
// ---------------------------------------------------------------
function getSessionIDToInfo( $strSessionID, $objDB )
{
	if ( !$strSessionID )
	{
		return FALSE;
	}

	// �����󥻥å��������ơ��֥���䤤��碌
	// ���å�����ݻ��γ�ǧ��ID���ѥ���ɤμ���
	$strQuery = "SELECT strLoginUserID, strLoginPassword," .
	            " dtmLoginTime - now() + ( interval '" . REMINDER_LIMIT . " min' ) AS remaining " .
	            "FROM t_LoginSession " .
	            "WHERE strSessionID LIKE '$strSessionID'" .
	            " AND bytSuccessfulFlag = true";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	// ���å������ݻ�����桼������ID�ȥѥ���ɤ����
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	if ( preg_replace ( "-", $objResult->remaining ) )
	{
		// �����ॢ���Ƚ���
		$strQuery = "UPDATE t_LoginSession " .
		            "SET bytSuccessfulFlag = false " .
		            "WHERE strSessionID = '$strSessionID'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}
		return FALSE;
	}

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}

	// �ޥå�����ID���ѥ���ɤ��ĥ桼�����򸡺�
	$strQuery = "SELECT lngUserCode FROM m_User " .
	            "WHERE strUserID = '" . $objResult->strloginuserid . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// �����ͤ�����ͤ˥��å�
	if ( $lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$aryData["strSessionID"] = $strSessionID;
		$aryData["lngUserCode"]     = $objResult->lngusercode;
	}
	else
	{
		return FALSE;
	}
	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}

	return $aryData;
}




// ---------------------------------------------------------------
/**
* ��ޥ�������ѥ��å��������̵��������
*
*	�ѥ���ɥ�ޥ�������ѥ��å����ơ��֥��̵��������
*
*	@param  string  $strSessionID ���å����ID
*	@param  object  $objDB        DB���֥�������
*	@return boolean TRUE          ���å����̵��������
*	                FALSE         ���å����̵��������
*	@access public
*/
// ---------------------------------------------------------------
function setSessionOff( $strSessionID, $objDB )
{
	if ( !$strSessionID )
	{
		return FALSE;
	}

	// ̵��������
	$strQuery = "UPDATE t_LoginSession " .
	            "SET bytSuccessfulFlag = false " .
	            "WHERE strSessionID = '$strSessionID'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}
	return TRUE;
}



return TRUE;
?>
