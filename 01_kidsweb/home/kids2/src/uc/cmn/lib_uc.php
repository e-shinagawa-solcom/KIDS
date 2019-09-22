<?
/** 
*	�桼���������ѥ饤�֥��
*
*	�桼���������Ѵؿ��饤�֥��
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/



/**
* �桼��������
*
*	�桼�����ǡ����ɤ߹��ߡ��������ܺپ������������ؿ�
*
*	@param  String $lngUserCode �桼����������
*	@param  Array  $aryData     FORM�ǡ���
*	@param  Object $objDB       DB���֥�������
*	@access public
*/
function getUserQuery( $lngUserCode, $aryData, $objDB )
{
	$bytInvalidFlag         = $aryData['bytInvalidFlag'];
	$lngUserCode            = $aryData['lngUserCode'];
	$strUserID              = $aryData['strUserID'];
	$strMailAddress         = $aryData['strMailAddress'];
	$bytMailTransmitFlag    = $aryData['bytMailTransmitFlag'];
	$bytUserDisplayFlag     = $aryData['bytUserDisplayFlag'];
	$strUserDisplayCode     = $aryData['strUserDisplayCode'];
	$strUserDisplayName     = $aryData['strUserDisplayName'];
	$strUserFullName        = $aryData['strUserFullName'];
	$lngCompanyCode         = $aryData['lngCompanyCode'];
	$lngGroupCode           = $aryData['lngGroupCode'];
	$lngAuthorityGroupCode  = $aryData['lngAuthorityGroupCode'];
	$lngAccessIPAddressCode = $aryData['lngAccessIPAddressCode'];
	$strNote                = $aryData['strNote'];
	$lngFunctionCode        = $aryData['lngFunctionCode'];
	$strSort                = $aryData['strSort'];

	// �����Ȥ��륫�����о��ֹ�����
	$arySortColumn = array ( 1 => "u.bytInvalidFlag",
	                         2 => "u.lngUserCode",
	                         3 => "u.strUserID",
	                         4 => "u.strMailAddress",
	                         5 => "u.bytMailTransmitFlag",
	                         6 => "u.bytUserDisplayFlag",
	                         7 => "u.strUserDisplayCode",
	                         8 => "u.strUserDisplayName",
	                         9 => "u.strUserFullName",
	                        10 => "u.lngCompanyCode",
	                        11 => "g.lngGroupCode",
	                        12 => "u.lngAuthorityGroupCode",
	                        13 => "u.lngAccessIPAddressCode",
	                        14 => "u.strNote" );

	//////////////////////////////////////////////////////////////////////////
	// ��������
	//////////////////////////////////////////////////////////////////////////
	$strQuery = "SELECT\n" .
                " u.bytInvalidFlag, u.lngUserCode," .
                " trim( trailing from u.strUserID ) AS strUserID,\n" .
                " u.strMailAddress, u.bytMailTransmitFlag,\n" .
                " u.bytUserDisplayFlag, u.strUserDisplayCode,\n" .
                " u.strUserDisplayName, u.strUserFullName,\n" .
                " c.lngCompanyCode, c.strCompanyDisplayCode,\n" .
                " c.strCompanyName, g.lngGroupCode, g.strGroupDisplayCode,\n" .
                " g.strGroupName, gr.bytDefaultFlag,\n" .
                " ag.lngAuthorityGroupCode, ag.strAuthorityGroupName,\n" .
                " ip.lngAccessIPAddressCode, ip.strAccessIPAddress,\n" .
                " u.strNote, g.strGroupDisplayColor, u.strUserImageFileName\n";

	$strQuery .= "FROM m_User u, m_Company c, m_Group g, m_GroupRelation gr, m_AuthorityGroup ag, m_AccessIPAddress ip \n" .
                 "WHERE";

	//////////////////////////////////////////////////////////////////////////
	// ���
	//////////////////////////////////////////////////////////////////////////
	// ����            ��Ｐ 
	// ����            ��Ｐ       B
	// �ܺ١�����(����)��Ｐ A
	// �ܺ١�����(����)��Ｐ A and B
	//////////////////////////////////////////////////////////////////////////
	// A:���ꤷ���桼����������
	// B:�Ƹ������

	// A:���ꤷ���桼����������
	if ( $aryData["lngUserCodeConditions"] && $lngUserCode != "" )
	{
		$strQuery .= " AND u.lngUserCode = $lngUserCode \n";
	}

	// B:�Ƹ������
	// ���������
	if ( $aryData["bytInvalidFlagConditions"] && $bytInvalidFlag )
	{
		$strQuery .= " AND u.bytInvalidFlag = $bytInvalidFlag \n";
	}

	// �桼����ID
	if ( $aryData["strUserIDConditions"] && $strUserID != "" )
	{
		$strQuery .= " AND u.strUserID LIKE '%$strUserID%' \n";
	}

	// �᡼�륢�ɥ쥹
	if ( $aryData["strMailAddressConditions"] && $strMailAddress != "" )
	{
		$strQuery .= " AND u.strMailAddress LIKE '%$strMailAddress%' \n";
	}

	// �᡼���ۿ�����
	if ( $aryData["bytMailTransmitFlagConditions"] && $bytMailTransmitFlag )
	{
		$strQuery .= " AND u.bytMailTransmitFlag = $bytMailTransmitFlag \n";
	}

	// ɽ���桼�����ե饰
	if ( $aryData["bytUserDisplayFlagConditions"] && $bytUserDisplayFlag )
	{
		$strQuery .= " AND u.bytUserDisplayFlag = $bytUserDisplayFlag \n";
	}

	// ɽ���桼����������
	if ( $aryData["strUserDisplayCodeConditions"] && $strUserDisplayCode != "" )
	{
		$strQuery .= " AND u.strUserDisplayCode LIKE '%$strUserDisplayCode%' \n";
	}

	// ɽ���桼����̾
	if ( $aryData["strUserDisplayNameConditions"] && $strUserDisplayName != "" )
	{
		$strQuery .= " AND u.strUserDisplayName LIKE '%$strUserDisplayName%' \n";
	}

	// �ե�͡���
	if ( $aryData["strUserFullNameConditions"] && $strUserFullName != "" )
	{
		$strQuery .= " AND u.strUserFullName LIKE '%$strUserFullName%' \n";
	}

	// ��ȥ�����
	if ( $aryData["lngCompanyCodeConditions"] && $lngCompanyCode != "" )
	{
		$strQuery .= " AND c.lngCompanyCode = $lngCompanyCode \n";
	}

	// ���롼�ץ�����
	if ( $aryData["lngGroupCodeConditions"] && $lngGroupCode != "" )
	{
		$strQuery .= " AND g.lngGroupCode = $lngGroupCode \n";
	}
	elseif ( $lngFunctionCode == DEF_FUNCTION_UC3 )
	{
		$strQuery .= " AND gr.bytDefaultFlag = TRUE \n";
	}

	// ���¥��롼�ץ�����
	if ( $aryData["lngAuthorityGroupCodeConditions"] && $lngAuthorityGroupCode != "" )
	{
		$strQuery .= " AND ag.lngAuthorityGroupCode = $lngAuthorityGroupCode \n";
	}

	// ��������IP���ɥ쥹
	if ( $aryData["lngAccessIPAddressCodeConditions"] && $lngAccessIPAddressCode != "" )
	{
		$strQuery .= " AND ip.lngAccessIPAddressCode = $lngAccessIPAddressCode \n";
	}

	//////////////////////////////////////////////////////////////////////////
	// ɳ�դ�
	//////////////////////////////////////////////////////////////////////////
	// m_User u, m_Company c, m_Group g, m_GroupRelation gr
	// m_AuthorityGroup ag, m_AccessIPAddress ip
	$strQuery .= " AND u.lngCompanyCode = c.lngCompanyCode\n" .
                 " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode\n" .
                 " AND u.lngAccessIPAddressCode = ip.lngAccessIPAddressCode\n" .
                 " AND u.lngUserCode = gr.lngUserCode\n" .
                 " AND g.lngGroupCode = gr.lngGroupCode\n";

	//////////////////////////////////////////////////////////////////////////
	// �����Ƚ���
	//////////////////////////////////////////////////////////////////////////
	// $strSort ��¤ "sort_[�о��ֹ�]_[�߽硦����]"
	// $strSort �����о��ֹ桢�߽硦��������
	list ( $sort, $column, $DESC ) = explode ( "_", $strSort );
	if ( $column )
	{
		$strQuery .= "ORDER BY $arySortColumn[$column] $DESC, u.lngUserCode ASC\n";
	}

	// �桼�����ܺ١��桼���������ξ�硢�ǥե���ȥ��롼�פˤƥ�����
	elseif ( $lngFunctionCode == DEF_FUNCTION_UC4 || $lngFunctionCode == DEF_FUNCTION_UC5 )
	{
		$strQuery .= "ORDER BY gr.bytDefaultFlag ASC\n";
	}

	else
	{
		$strQuery .= "ORDER BY u.lngUserCode ASC\n";
	}
	$strQuery = preg_replace ( "/WHERE AND/", "WHERE", $strQuery );

// echo $strQuery;
	//////////////////////////////////////////////////////////////////////////
	// ������¹�
	//////////////////////////////////////////////////////////////////////////
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	//$lngResultNum = pg_Num_Rows( $lngResultID );
	$lngResultNum = pg_Num_Rows( $lngResultID );
	if ( !$lngResultNum )
	{
		$strErrorMessage = fncOutputError( 1107, DEF_WARNING, "", FALSE, "/wf/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		$strErrorMessage = "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f818\"><tr bgcolor=\"#FFFFFF\"><th>" . $strErrorMessage . "</th></tr></table>";
	}

	return array ( $lngResultID, $lngResultNum, $strErrorMessage );
}



/**
* GET�ǡ�������URL�����ؿ�
*
*	@param  Array  $aryData GET�ǡ���
*	@return String          URL(**.php?�������ʹߤ�ʸ����)
*	@access public
*/
function fncGetURL( $aryData )
{
	$url = "strSessionID=" .$aryData["strSessionID"] .
           "&lngFunctionCode=" .$aryData["lngFunctionCode"] .
           "&lngUserCode=" .$aryData["lngUserCode"] .
           "&bytInvalidFlag=" .$aryData["bytInvalidFlag"] .
           "&strUserID=" .$aryData["strUserID"] .
           "&strMailAddress=" .$aryData["strMailAddress"] .
           "&bytMailTransmitFlag=" .$aryData["bytMailTransmitFlag"] .
           "&strUserDisplayCode=" .$aryData["strUserDisplayCode"] .
           "&strUserDisplayName=" .$aryData["strUserDisplayName"] .
           "&strUserFullName=" .$aryData["strUserFullName"] .
           "&lngCompanyCode=" .$aryData["lngCompanyCode"] .
           "&lngGroupCode=" .$aryData["lngGroupCode"] .
           "&lngAuthorityGroupCode=" .$aryData["lngAuthorityGroupCode"] .
           "&lngAccessIPAddressCode=" .$aryData["lngAccessIPAddressCode"] .
           "&strNote=" .$aryData["strNote"];

	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC3 )
	{
		$url .= "&detailVisible=" .$aryData["detailVisible"] .
                "&bytInvalidFlagVisible=" .$aryData["bytInvalidFlagVisible"] .
                "&lngUserCodeVisible=" .$aryData["lngUserCodeVisible"] .
                "&strUserIDVisible=" .$aryData["strUserIDVisible"] .
                "&strMailAddressVisible=" .$aryData["strMailAddressVisible"] .
                "&bytMailTransmitFlagVisible=" .$aryData["bytMailTransmitFlagVisible"] .
                "&bytUserDisplayFlagVisible=" .$aryData["bytUserDisplayFlagVisible"] .
                "&strUserDisplayCodeVisible=" .$aryData["strUserDisplayCodeVisible"] .
                "&strUserDisplayNameVisible=" .$aryData["strUserDisplayNameVisible"] .
                "&strUserFullNameVisible=" .$aryData["strUserFullNameVisible"] .
                "&lngCompanyCodeVisible=" .$aryData["lngCompanyCodeVisible"] .
                "&lngGroupCodeVisible=" .$aryData["lngGroupCodeVisible"] .
                "&lngAuthorityGroupCodeVisible=" .$aryData["lngAuthorityGroupCodeVisible"] .
                "&lngAccessIPAddressCodeVisible=" .$aryData["lngAccessIPAddressCodeVisible"] .
                "&strNoteVisible=" .$aryData["strNoteVisible"] .
                "&updateVisible=" .$aryData["updateVisible"] .

                "&bytInvalidFlagConditions=" .$aryData["bytInvalidFlagConditions"] .
                "&lngUserCodeConditions=" .$aryData["lngUserCodeConditions"] .
                "&strUserIDConditions=" .$aryData["strUserIDConditions"] .
                "&strMailAddressConditions=" .$aryData["strMailAddressConditions"] .
                "&bytMailTransmitFlagConditions=" .$aryData["bytMailTransmitFlagConditions"] .
                "&bytUserDisplayFlagConditions=" .$aryData["bytUserDisplayFlagConditions"] .
                "&strUserDisplayCodeConditions=" .$aryData["strUserDisplayCodeConditions"] .
                "&strUserDisplayNameConditions=" .$aryData["strUserDisplayNameConditions"] .
                "&strUserFullNameConditions=" .$aryData["strUserFullNameConditions"] .
                "&lngCompanyCodeConditions=" .$aryData["lngCompanyCodeConditions"] .
                "&lngGroupCodeConditions=" .$aryData["lngGroupCodeConditions"] .
                "&lngAuthorityGroupCodeConditions=" .$aryData["lngAuthorityGroupCodeConditions"] .
                "&lngAccessIPAddressCodeConditions=" .$aryData["lngAccessIPAddressCodeConditions"] .
                "&strNoteConditions=" .$aryData["strNoteConditions"];
	}
	return $url;
}



function checkUniqueUser( $lngUserCode, $strUserID, $lngCompanyCode, $strUserDisplayCode, $lngUserCodeOriginal, $strUserIDOriginal, $lngCompanyCodeOriginal, $strUserDisplayCodeOriginal, $mode, $objDB )
{
	$aryError["lngUserCode"]        = "visibility:hidden;";
	$aryError["strUserID"]          = "visibility:hidden;";
	$aryError["strUserDisplayCode"] = "visibility:hidden;";

	// �����ǤϤʤ� �ޤ��� �桼���������ɤ��Ѥ�ä�
	if ( $mode != "UPDATE" || $lngUserCode != $lngUserCodeOriginal )
	{
		// �桼���������ɤν�ʣ�����å�
		$strQuery  = "SELECT lngUserCode FROM m_User " .
	                 "WHERE lngUserCode = $lngUserCode";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$bytErrorFlag = 1;
			$aryError["lngUserCode"]   = "visibility:visible;";
			$aryMessage["lngUserCode"] = "�桼��������ʣ���Ƥ��ޤ���";
			$objDB->freeResult( $lngResultID );
		}
	}

	// �����ǤϤʤ� �ޤ��� �桼����ID���Ѥ�ä�
	if ( $mode != "UPDATE" || $strUserID != $strUserIDOriginal )
	{
		// �桼����ID�ν�ʣ�����å�
		$strQuery  = "SELECT lngUserCode FROM m_User " .
	                 "WHERE strUserID = '$strUserID'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$bytErrorFlag = 1;
			$aryError["strUserID"]   = "visibility:visible;";
			$aryMessage["strUserID"] = "�桼��������ʣ���Ƥ��ޤ���";
			$objDB->freeResult( $lngResultID );
		}
	}

	// �����ǤϤʤ� �ޤ��� �桼����ɽ�������ɤ��Ѥ�ä�
	if ( $mode != "UPDATE" || $strUserDisplayCode != $strUserDisplayCodeOriginal || $lngCompanyCode != $lngCompanyCodeOriginal )
	{
		// ��°���������Ʊ��ɽ�������ɤμ�(��ʬ�ʳ�)��������票�顼
		$strQuery = "SELECT lngUserCode FROM m_User " .
	                "WHERE strUserDisplayCode = '$strUserDisplayCode'" .
	                " AND lngCompanyCode = $lngCompanyCode\n" .
	                " AND lngUserCode != $lngUserCode";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$bytErrorFlag = 1;
			$aryError["strUserDisplayCode"]   = "visibility:visible;";
			$aryMessage["strUserDisplayCode"] = "�桼��������ʣ���Ƥ��ޤ���";
			$objDB->freeResult( $lngResultID );
		}
	}

	return array ( $bytErrorFlag, $aryError, $aryMessage );
}



return TRUE;
?>
