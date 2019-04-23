<?
/** 
*	����ե������ѥ饤�֥��
*
*	����ե������Ѵؿ��饤�֥��
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/


// ���̥����ɤ��Ф������̾���������
//$aryFunctionCode = Array ( DEF_FUNCTION_PO1 => "ȯ��", DEF_FUNCTION_E0 => "���Ѹ���" );
$aryFunctionCode = Array (	DEF_FUNCTION_P1	 =>  "����"
							,DEF_FUNCTION_SO1 => "����"
							,DEF_FUNCTION_PO1 => "ȯ��"
							,DEF_FUNCTION_SC1 => "���"
							,DEF_FUNCTION_PC1 => "����"
							,DEF_FUNCTION_E1 =>  "���Ѹ���"
						);

/**
* ����ե�����
*
*	�Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
*
*	@param  String $lngUserCode �桼����������
*	@param  Array  $aryData     FORM�ǡ���
*	@param  Object $objDB       DB���֥�������
*	@access public
*/
function getWorkflowQuery( $lngUserCode, $aryData, $objDB )
{
	$lngWorkflowCode       = $aryData['lngWorkflowCode'];
	$lngWorkflowStatusCode = $aryData['lngWorkflowStatusCode'];
	$lngApplicantUserDisplayCode  = $aryData['lngApplicantUserDisplayCode'];
	$lngInputUserDisplayCode      = $aryData['lngInputUserDisplayCode'];
	$dtmStartDateFrom      = $aryData['dtmStartDateFrom'];
	$dtmStartDateTo        = $aryData['dtmStartDateTo'];
	$dtmEndDateFrom        = $aryData['dtmEndDateFrom'];
	$dtmEndDateTo          = $aryData['dtmEndDateTo'];
	$lngInChargeCode       = $aryData['lngInChargeCode'];
	$lngFunctionCode       = $aryData['lngFunctionCode'];
	$lngSelectFunctionCode = $aryData['lngSelectFunctionCode'];
	$lngActionFunctionCode = $aryData['lngActionFunctionCode'];
	$strSort               = $aryData['strSort'];

	// �����Ȥ��륫�����о��ֹ�����
	$arySortColumn = array ( 1 => "m.dtmStartDate",
	                         2 => "m.strWorkflowName",
	                         3 => "strApplicantName",
	                         4 => "strInputName",
	                         5 => "strRecognitionName",
	                         6 => "t.dtmLimitDate",
	                         7 => "t.lngWorkflowStatusCode",
	                         8 => "m.dtmEndDate",
	                         9 => "m.lngFunctionCode" );

	//////////////////////////////////////////////////////////////////////////
	// ��������
	//////////////////////////////////////////////////////////////////////////
	if ( !$lngActionFunctionCode )
	{
		$strQuery = "SELECT\n" .
                    " t.lngWorkflowCode, t.lngWorkflowSubCode," .
                    " o.lngWorkflowOrderCode, o.lngWorkflowOrderNo, \n" .
                    " m.lngFunctionCode," .
                    " t.dtmLimitdate - now() AS lngLimitdate,\n" .
                    " to_char( t.dtmLimitdate, 'YYYY/MM/DD' ) AS dtmLimitdate,\n" .
                    " to_char( m.dtmStartDate, 'YYYY/MM/DD' ) AS dtmStartDate,\n" .
                    " to_char( m.dtmEndDate, 'YYYY/MM/DD' ) AS dtmEndDate,\n" .
                    " m.strWorkflowName, t.lngWorkflowStatusCode AS tStatusCode,\n" .
                    " u.strUserDisplayName AS strApplicantName,\n" .
                    " u2.strUserDisplayName AS strInputName,\n" .
                    " u3.strUserDisplayName AS strRecognitionName,\n" .
                    " m.lngInputUserCode, o.lngInChargeCode,\n" .
                    " m.strWorkflowKeyCode,\n" .
                    " t.lngWorkflowStatusCode \n";
	}
	elseif ( $lngActionFunctionCode == DEF_FUNCTION_WF6 )
	{
		$strQuery = "SELECT\n" .
                    " o.lngWorkflowOrderCode, o.lngWorkflowOrderNo, \n" .
                    " m.lngInputUserCode, o.lngInChargeCode, o.lngLimitDays," .
                    " t.lngWorkflowOrderNo, t.lngWorkflowSubCode, t.strNote," .
                    " m.lngFunctionCode, m.strWorkflowKeyCode," .
                    " trim(trailing from m.strWorkflowName) AS strWorkflowName," .
                    " to_char( t.dtmLimitdate, 'YYYY/MM/DD' ) AS dtmLimitdate,\n" .
                    " to_char( m.dtmStartDate, 'YYYY/MM/DD' ) AS dtmStartDate,\n" .
                    " to_char( m.dtmEndDate, 'YYYY/MM/DD' ) AS dtmEndDate,\n" .
                    " u.strUserDisplayName AS strApplicantName," .
                    " u2.strUserDisplayName AS strInputName," .
                    " u3.strUserDisplayName AS strRecognitionName," .
                    " u2.strMailAddress AS strInputMail," .
                    " u3.strMailAddress AS strRecognitionMail," .
                    " u2.bytMailtransmitFlag AS bytInputMailFlag," .
                    " u3.bytMailtransmitFlag AS bytRecognitionMailFlag," .
                    " o.lngWorkflowStatusCode AS oStatusCode," .
                    " t.lngWorkflowStatusCode AS tStatusCode\n";
	}

	$strQuery .= "FROM m_Workflow m, t_Workflow t, m_WorkflowOrder o,\n" .
                 " m_User u, m_User u2, m_User u3 \n" .
                 "WHERE";

	//////////////////////////////////////////////////////////////////////////
	// ���
	//////////////////////////////////////////////////////////////////////////
	// ����            ��Ｐ             C and D and ( E or F )
	// ����            ��Ｐ       B and C and D and ( E      or G or H )
	// �ܺ١�����(����)��Ｐ A and       C and D and ( E or F )
	// �ܺ١�����(����)��Ｐ A and B and C and D and ( E      or G or H )
	//////////////////////////////////////////////////////////////////////////
	// A:���ꤷ������ե�������
	// B:�Ƹ������
	// C:���� = $lngWorkflowStatusCode
	// D:̵���ե饰����
	// E:���ϼԤ�������桼������Ʊ��
	// F:����ե��ơ��֥�ˤ�������֤��桼�����ν��֤����礭��
	// G:����ե��ơ��֥�˴ޤޤ��
	// H:������桼������°���륰�롼�פ��ĸ��¤����Υ桼����

	// A:���ꤷ������ե�������
	if ( $lngWorkflowCode )
	{
		$strQuery .= " AND m.lngWorkflowCode = $lngWorkflowCode \n";
	}

	// B:�Ƹ������
	if ( $aryData["lngApplicantUserDisplayCodeConditions"] && $lngApplicantUserDisplayCode ) // ������
	{
		//$strQuery .= " AND m.lngApplicantUserCode = $lngApplicantUserCode \n";
		$strQuery .= " AND u.strUserDisplayCode = '$lngApplicantUserDisplayCode' \n";
	}
	if ( $aryData["lngInputUserDisplayCodeConditions"] && $lngInputUserDisplayCode ) // ���ϼ�
	{
		$strQuery .= " AND u2.strUserDisplayCode = '$lngInputUserDisplayCode' \n";
	}
	if ( $aryData["dtmStartDateConditions"] && $dtmStartDateFrom ) // ����������
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmStartDate ) >= '$dtmStartDateFrom' \n";
	}
	if ( $aryData["dtmStartDateConditions"] && $dtmStartDateTo ) // �������ޤ�
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmStartDate ) <= '$dtmStartDateTo' \n";
	}
	if ( $aryData["dtmEndDateConditions"] && $dtmEndDateFrom ) // ��λ������
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmEndDate ) >= '$dtmEndDateFrom' \n";
	}
	if ( $aryData["dtmEndDateConditions"] && $dtmEndDateTo ) // ��λ���ޤ�
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmEndDate ) <= '$dtmEndDateTo' \n";
	}
	if ( $aryData["lngInChargeCodeConditions"] && $lngInChargeCode ) // ��ǧ��
	{
		$strQuery .= " AND o.lngInChargeCode = $lngInChargeCode \n";
	}
	if ( $aryData["lngSelectFunctionCodeConditions"] && $lngSelectFunctionCode ) // ��ǽ������
	{
		$strQuery .= " AND m.lngFunctionCode = $lngSelectFunctionCode \n";
	}

	if ( $aryData["lngWorkflowStatusCodeConditions"] && $lngWorkflowStatusCode !== "" )
	{
                 // C:���� = $lngWorkflowStatusCode
//		$strQuery .= " AND t.lngWorkflowStatusCode = $lngWorkflowStatusCode\n";
		$strQuery .= " AND t.lngWorkflowStatusCode in ( $lngWorkflowStatusCode )\n";
	}
	// �ּ�áװƷ�˴ؤ��Ƹ��¥����å�
	if ( $lngFunctionCode != DEF_FUNCTION_WF3 )
	{
                 // C:���� != DEF_STATUS_CANCELL
		$strQuery .= " AND t.lngWorkflowStatusCode <> " . DEF_STATUS_CANCELL;
	}

                 // D:̵���ե饰����
	$strQuery .= " AND m.bytinvalidflag = FALSE\n" .

                 " AND\n" .
                 "(\n" .

                 // E:���ϼԤ�������桼������Ʊ��
                 "  m.lngInputUserCode = $lngUserCode\n";

	if ( $lngFunctionCode == DEF_FUNCTION_WF1 )
	{
		// F:����ե��ơ��֥�ˤ�������֤��桼�����ν��֤����礭��
		$strQuery .= "   OR t.lngWorkflowOrderNo >= \n" . 
                     "  (\n" .
                     "    SELECT o2.lngWorkflowOrderNo\n" .
                     "    FROM m_WorkflowOrder o2\n" .
                     "    WHERE o2.lngInChargeCode = $lngUserCode\n" .
                     "     AND m.lngWorkflowOrderCode = o2.lngWorkflowOrderCode\n" .
                     "  )\n";
	}
	elseif ( $lngFunctionCode == DEF_FUNCTION_WF2 || $lngFunctionCode == DEF_FUNCTION_WF3 )
	{
		// G:����ե��ơ��֥�˴ޤޤ��
		$strQuery .= "   OR m.lngWorkflowOrderCode = \n" . 
                     "  (\n" .
                     "    SELECT o2.lngWorkflowOrderCode\n" .
                     "    FROM m_WorkflowOrder o2\n" .
                     "    WHERE o2.lngInChargeCode = $lngUserCode\n" .
                     "     AND m.lngWorkflowOrderCode = o2.lngWorkflowOrderCode\n" .
                     "  )\n";

		// H:������桼������°���륰�롼�פ��ĸ��¤����Υ桼����
		$strQuery .= "   OR u.lngUserCode = \n" .
		             "  (\n" .
		             "    SELECT u5.lngUserCode \n" .
                     "    FROM m_User u5, m_AuthorityGroup ag, m_GroupRelation gr \n" .
                     "    WHERE u.lngUserCode = u5.lngUserCode\n" .
                     "     AND u5.bytinvalidflag = FALSE\n" .
                     "     AND u5.lngUserCode = gr.lngUserCode\n" .
                     "     AND u5.lngAuthorityGroupCode = ag.lngAuthorityGroupCode\n" .

                     // ���¥�٥뤬��(�㤤�ۤ������¤���)
                     "     AND ag.lngAuthorityLevel < \n" .
                     "    (\n" .
                     "      SELECT ag.lngAuthorityLevel \n" .
                     "      FROM m_User u, m_AuthorityGroup ag \n" .
                     "      WHERE u.lngUserCode = $lngUserCode\n" .
                     "       AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode\n" .
                     "    )\n" .

                     // Ʊ�����롼��
                     "     AND gr.lngGroupCode = \n" .
                     "    (\n" .
                     "      SELECT gr2.lngGroupCode \n" .
                     "      FROM m_GroupRelation gr2\n" .
                     "      WHERE gr2.lngUserCode = $lngUserCode\n" .
                     "       AND gr2.lngUserCode = u5.lngUserCode\n" .
                     "    )\n" .
                     "  )\n";
	}

	$strQuery .= ") \n" .

	//////////////////////////////////////////////////////////////////////////
	// ɳ�դ�
	//////////////////////////////////////////////////////////////////////////
	// m_Workflow m, t_Workflow t, m_WorkflowOrder o
	// m_User u, m_User u2, m_User u3
	             " AND m.lngWorkflowOrderCode = o.lngWorkflowOrderCode\n" .
                 " AND t.lngWorkflowOrderNo = o.lngWorkflowOrderNo\n" .
                 " AND m.lngApplicantUserCode = u.lngUserCode\n" .
                 " AND m.lngInputUserCode = u2.lngUserCode\n" .
                 " AND o.lngInChargeCode = u3.lngUserCode\n" .
	             " AND m.lngWorkflowCode = t.lngWorkflowCode\n" .
                 //" AND u.bytinvalidflag = FALSE\n" .
                 //" AND u2.bytinvalidflag = FALSE\n" .
                 //" AND u3.bytinvalidflag = FALSE\n";

	//////////////////////////////////////////////////////////////////////////
	// lngWorkflowSubCode �κ����ͼ���
	//////////////////////////////////////////////////////////////////////////
                 " AND t.lngWorkflowSubCode = \n" .
                 "(\n" .
                 "  SELECT MAX ( t2.lngWorkflowSubCode )\n" .
                 "  FROM t_Workflow t2\n" .
                 "  WHERE t.lngWorkflowCode = t2.lngWorkflowCode\n" .
                 "  GROUP BY t2.lngWorkflowCode\n" .
                 ")\n";

	//////////////////////////////////////////////////////////////////////////
	// �����Ƚ���
	//////////////////////////////////////////////////////////////////////////
	// $strSort ��¤ "sort_[�о��ֹ�]_[�߽硦����]"

	if ( $lngFunctionCode == DEF_FUNCTION_WF2 || $lngFunctionCode == DEF_FUNCTION_WF3 )
	{
		$arySortColumn[6] = "m.dtmEndDate";
	}

	// $strSort �����о��ֹ桢�߽硦��������
	list ( $sort, $column, $DESC ) = explode ( "_", $strSort );
	if ( $column )
	{
		$strQuery .= "ORDER BY $arySortColumn[$column] $DESC, m.lngFunctionCode, m.dtmStartDate ASC\n";
	}
	$strQuery = preg_replace ( "/WHERE AND/", "WHERE", $strQuery );


	//////////////////////////////////////////////////////////////////////////
	// ������¹�
	//////////////////////////////////////////////////////////////////////////
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	//$lngResultNum = pg_Num_Rows( $lngResultID );
	$lngResultNum = pg_Num_Rows( $lngResultID );
	if ( !$lngResultNum )
	{
		$strErrorMessage = fncOutputError( 801, DEF_WARNING, "", FALSE, "/wf/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
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
           "&lngSelectFunctionCode=" .$aryData["lngSelectFunctionCode"] .
           "&lngWorkflowStatusCode=" .$aryData["lngWorkflowStatusCode"];
	// ��������¹Ԥ��줿���ϸ��������ꤲ��
	// (���:lngSelectFunctionCode���ְ����פǤʤ��ä���)
	if ( $aryData["lngSelectFunctionCode"] != DEF_FUNCTION_WF1 )
	{
		$url .= "&lngApplicantUserDisplayCode=" .$aryData["lngApplicantUserDisplayCode"] .
                "&lngInputUserDisplayCode=" .$aryData["lngInputUserDisplayCode"] .
                "&dtmStartDateFrom=" .$aryData["dtmStartDateFrom"] .
                "&dtmStartDateTo=" .$aryData["dtmStartDateTo"] .
                "&dtmEndDateFrom=" .$aryData["dtmEndDateFrom"] .
                "&dtmEndDateTo=" .$aryData["dtmEndDateTo"] .
                "&lngInChargeCode=" .$aryData["lngInChargeCode"];
	}

	// �ڡ����ѹ��������Ƚ����ξ��ϸ���ɽ�����ܡ����������ܤ��ꤲ��
	// (���:lngWorkflowCode���ʤ��ä���)
	if ( !$aryData["lngWorkflowCode"] )
	{
		$url .= "&lngWorkflowStatusCodeVisible=" .$aryData["lngWorkflowStatusCodeVisible"] .
                "&lngApplicantUserDisplayCodeVisible=" .$aryData["lngApplicantUserDisplayCodeVisible"] .
                "&lngInputUserDisplayCodeVisible=" .$aryData["lngInputUserDisplayCodeVisible"] .
                "&dtmStartDateVisible=" .$aryData["dtmStartDateVisible"] .
                "&dtmEndDateVisible=" .$aryData["dtmEndDateVisible"] .
                "&lngInChargeCodeVisible=" .$aryData["lngInChargeCodeVisible"] .
                "&lngSelectFunctionCodeVisible=" .$aryData["lngSelectFunctionCodeVisible"] .
                "&lngWorkflowStatusCodeConditions=" .$aryData["lngWorkflowStatusCodeConditions"] .
                "&lngApplicantUserDisplayCodeConditions=" .$aryData["lngApplicantUserDisplayCodeConditions"] .
                "&lngInputUserDisplayCodeConditions=" .$aryData["lngInputUserDisplayCodeConditions"] .
                "&dtmStartDateConditions=" .$aryData["dtmStartDateConditions"] .
                "&dtmEndDateConditions=" .$aryData["dtmEndDateConditions"] .
                "&lngInChargeCodeConditions=" .$aryData["lngInChargeCodeConditions"] .
                "&lngSelectFunctionCodeConditions=" .$aryData["lngSelectFunctionCodeConditions"];
	}
	return $url;
}



/**
* �ǡ�����������ؿ�
*
*	@param  Long   $lngCode  ������
*	@param  Long   $lngSQL   �¹Ԥ���SQL������
*	@param  Object $objDB    DB���֥�������
*	@return Array  $aryData1 ����1
*	        Array  $aryData2 ����2
*	@access public
*/
function fncGetArrayData( $lngCode, $lngSQL, $objDB )
{
	// �桼���������ɤ������ե���������ɤȽ���ֹ�����
	$strQuery[0] = "SELECT lngWorkflowOrderCode, lngWorkflowOrderNo " .
	               "FROM m_WorkflowOrder " .
	               "WHERE bytWorkflowOrderDisplayFlag = TRUE" .
	               " AND lngInChargeCode = $lngCode";

	// ����ե������ɤ������ե������ֹ�ȥ᡼�륢�ɥ쥹�����
	//$strQuery[1] = "SELECT o.lngWorkflowOrderNo, u.strMailAddress " .
	//               "FROM m_WorkflowOrder o, m_Workflow m, m_User u " .
	//               "WHERE m.lngWorkflowCode = $lngCode" .
	//               " AND u.bytmailtransmitflag = TRUE" .
	//               " AND o.bytWorkflowOrderDisplayFlag = TRUE" .
	//               " AND o.lngWorkflowOrderCode = m.lngWorkflowOrderCode" .
	//               " AND o.lngInChargeCode = u.lngUserCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery[$lngSQL], $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResut = $objDB->fetchArray( $lngResultID, $i );
			$aryData1[$i] = $aryResut[0];
			$aryData2[$i] = $aryResut[1];
		}
	}

	$objDB->freeResult( $lngResultID );

	return array ( $aryData1, $aryData2 );
}

/**
* �Ʒ����Υ�󥯵��Ҥ�����
*
*	@param  Object $objDB    DB���֥�������
*	@param  Object $objResult    �ƤӽФ�����WF������̥��֥�������
*	@return Array  $strWorkflowNameLink ������̥��ʸ����
*	@access public
*	@makedate	2005/11/07
*/
function fncGetWorkflowNameLink( $objDB, $objResult, $strSessionID)
{
// �Ʒ����Υ����ץ����̾��
$aryFunctionLink = Array (	DEF_FUNCTION_P1	 =>  "/p/result/index2.php"
							,DEF_FUNCTION_SO1 => "/so/result/index2.php"
							,DEF_FUNCTION_PO1 => "/po/result/index2.php"
							,DEF_FUNCTION_SC1 => "/sc/result/index2.php"
							,DEF_FUNCTION_PC1 => "/pc/result/index2.php"
							,DEF_FUNCTION_E1 =>  "/estimate/result/detail.php"
						);

// �Ʒ����Υ��������ꤹ�륭�������ɤ��оݥ����̾��
$aryWorkflowKeyName = array( DEF_FUNCTION_P1  => "lngProductNo"
							,DEF_FUNCTION_SO1 => "lngReceiveNo"
							,DEF_FUNCTION_PO1 => "lngOrderNo"
							,DEF_FUNCTION_SC1 => "lngSalesNo"
							,DEF_FUNCTION_PC1 => "lngStockNo"
							,DEF_FUNCTION_E1 =>  "lngEstimateNo"
						);

	$strWorkflowNameLink = "";
	
	//
	// ȯ������ե��ξ��
	//
	if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
	{
		// ȯ��ˤƻ��ꤷ�Ƥ������ʥ����ɤμ�������
		$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $objResult->strworkflowkeycode;

		// �ͤ�Ȥ� =====================================
		$lngEstimateNo = "";
		list ( $lngResultProductCodeID, $lngResultProductCodeNum ) = fncQuery( $strProductCodeQuery, $objDB );
		if ( $lngResultProductCodeNum )
		{
			$objProductCodeResult = $objDB->fetchObject( $lngResultProductCodeID, 0 );
			$strProductCode = $objProductCodeResult->strproductcode;

			// ���Ѹ����ǡ�������
			$aryEstimateQuery[] = "SELECT e.lngEstimateNo ";
			$aryEstimateQuery[] = "FROM m_Estimate e";
			$aryEstimateQuery[] = "WHERE e.strProductCode = '" . $strProductCode . "'";
			$aryEstimateQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
			$aryEstimateQuery[] = " AND e.bytDecisionFlag = true ";

			list ( $lngResultEstimateID, $lngResultEstimateNum ) = fncQuery( join ( " ", $aryEstimateQuery ), $objDB );

			if ( $lngResultEstimateNum )
			{
				$objEstimateResult = $objDB->fetchObject( $lngResultEstimateID, 0 );
				$objDB->freeResult( $lngResultEstimateID );
				unset ( $lngResultEstimateID );
				unset ( $lngResultEstimateNum );

				$lngEstimateNo = $objEstimateResult->lngestimateno;
				unset ( $objEstimateResult );
			}
			unset( $aryEstimateQuery );
		}
		$objDB->freeResult( $lngResultProductCodeID );

		// ���˻�������ʥ����ɤ��Ф��Ƹ��Ѹ�������¸�ߤ����
		if ( $lngEstimateNo != "" )
		{
			// ȯ�����Ƥȸ��Ѹ��������Υ�����ɥ��򳫤�����
			$strWorkflowNameLink = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $strSessionID . "&lngOrderNo=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $strSessionID . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
		}
	}
	//
	// ���Ѹ����Υ���ե��ξ��
	//
	elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
	{
		// ���Ѹ����Υ���ե��ξ�硢���Ѹ����������ƤΥ�����ɥ��򳫤�����
		$strWorkflowNameLink = "<td class=\"Segs\"><a class=wfA href=\"".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $strSessionID . "&lngEstimateNo=" . $objResult->strworkflowkeycode . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
	}

	//
	// �嵭��ȯ��ʸ��Ѹ�����ʻ�ѡˡ����Ѹ������˳������ʤ���¾�Υ���ե��ξ��
	//
	if( empty($strWorkflowNameLink) )
	{
		$strWorkflowNameLink = "<td class=\"Segs\" onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $strSessionID . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
	}

	return $strWorkflowNameLink;
	
}

/**
* ������Ϥ��줿 lngWorkflowStatusCode �� ʸ������Ѵ�����
*
*	@param  Array  $aryStatus "lngWorkflowStatusCode"
*	@return	string	SQL���ʸ���Ȥ߹��ࡢ��礵�줿ʸ����
*	@access public
*	@makedate	2005/11/07
*/
function fncGetArrayToWorkflowStatusCode( $aryStatus )
{

	$aryQuery = array();
	$strRet   = "";
	
	// ����ե�����"lngWorkflowStatusCode"
	// �����å��ܥå����ͤ�ꡢ����򤽤Τޤ�����
	
	if( is_array( $aryStatus ) )
	{
		$aryQuery[] = "";

		// WF���֤�ʣ�����ꤵ��Ƥ����ǽ��������Τǡ�����Ŀ�ʬ�롼��
		$strBuff = "";
		for ( $j = 0; $j < count($aryStatus); $j++ )
		{
			// ������
			if ( $j <> 0 )
			{
				$strBuff .= " ,";
			}
			$strBuff .= "" . $aryStatus[$j] . "";
		}
		$aryQuery[] = $strBuff;
		
		$strRet = implode("", $aryQuery);
		return !empty($strRet) ? $strRet : '0';
	}
	elseif(empty($aryStatus))
	{
		return null;
	}
	
	return '';

}

?>
