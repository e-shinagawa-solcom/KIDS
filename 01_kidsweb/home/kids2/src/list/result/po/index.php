<?
	/** 
	*	Ģɼ���� ȯ��� ������̲���
	*
	*	@package   KIDS
	*	@license   http://www.wiseknot.co.jp/ 
	*	@copyright Copyright &copy; 2003, Wiseknot 
	*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
	*	@access    public
	*	@version   1.00
	*
	*/
	// ������̲���( * �ϻ���Ģɼ�Υե�����̾ )
	// *.php -> strSessionID       -> index.php

	// �������̤�
	// index.php -> strSessionID       -> index.php
	// index.php -> lngReportCode      -> index.php

	// �����ɤ߹���
	include_once('conf.inc');

	require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "list/cmn/lib_lo.php");
	require (LIB_DEBUGFILE);

	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to = UtilSearchForm::extractArrayByTo($_REQUEST);
	$searchValue = $_REQUEST;
	
	$isSearch=array_keys($isSearch);
	$aryData['SearchColumn']=$isSearch;
	foreach($from as $key=> $item){
		$aryData[$key.'From']=$item;
	}
	foreach($to as $key=> $item){
		$aryData[$key.'To']=$item;
	}
	foreach($searchValue as $key=> $item){
		$aryData[$key]=$item;
	}


	// ���������ܼ���
	if (is_array($aryData["SearchColumn"]) && $lngArrayLength = count ( $aryData["SearchColumn"] ) )
	{
		$aryColumn = $aryData["SearchColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		unset ( $aryData["SearchColumn"] );
		unset ( $aryColumn );
	}


	// ʸ��������å�
	$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}


	// ȯ��Ģɼ����
	// ���ԡ��ե������������������
	$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_ORDER;

	// ȯ����������������
	$aryQuery[] = "SELECT distinct";
	$aryQuery[] = "	o.strOrderCode || '-' || strReviseCode	AS strOrderNo";
	$aryQuery[] = "	,u1.strUserDisplayCode	AS strInputUserDisplayCode";
	$aryQuery[] = "	,u1.strUserDisplayName	AS strInputUserDisplayName";
	$aryQuery[] = "	,c.strCompanyDisplayCode";
	$aryQuery[] = "	,c.strCompanyDisplayName";
	$aryQuery[] = "	,g.strGroupDisplayCode AS strInChargeGroupDisplayCode";
	$aryQuery[] = "	,g.strGroupDisplayName AS strInChargeGroupDisplayName";
	$aryQuery[] = "	,u2.strUserDisplayCode";
	$aryQuery[] = "	,u2.strUserDisplayName";
	$aryQuery[] = "	,o.lngOrderNo	AS strReportKeyCode";
	$aryQuery[] = "FROM";
	$aryQuery[] = "	m_Order o";
	$aryQuery[] = "		left join t_orderdetail tod";
	$aryQuery[] = "		on tod.lngorderno = o.lngorderno";
	$aryQuery[] = "			left join m_product mp";
	$aryQuery[] = "			on mp.strproductcode = tod.strproductcode";
	$aryQuery[] = "	,m_User u1";
	$aryQuery[] = "	,m_User u2";
	$aryQuery[] = "	,m_Group g";
	$aryQuery[] = "	,m_Company c";

	// ȯ���Ϣ���
	// ��ӥ����ʥ�С������� ���� ��Х��������ɤ����� ���� ��ӥ����ʥ�С��Ǿ��ͤ�0�ʾ�
	$aryQuery[] = "WHERE";
	$aryQuery[] = "	o.lngRevisionNo = ( ";
	$aryQuery[] = "		SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )";
	$aryQuery[] = "		AND o.strReviseCode = ( ";
	$aryQuery[] = "			SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )";
	$aryQuery[] = "			AND 0 <= ( ";
	$aryQuery[] = "		SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";


	/////////////////////////////////////////////////////////////////
	// �������
	/////////////////////////////////////////////////////////////////
	// ��������
	if ( $aryData["dtmInsertDateConditions"] )
	{
		if ( $aryData["dtmInsertDateFrom"] )
		{
			$aryQuery[] = " AND date_trunc('day', o.dtmInsertDate ) >= '" . $aryData["dtmInsertDateFrom"] . "'";
		}
		if ( $aryData["dtmInsertDateTO"] )
		{
			$aryQuery[] = " AND date_trunc('day', o.dtmInsertDate ) <= '" . $aryData["dtmInsertDateTo"] . "'";
		}
	}
	// �׾���
	if ( $aryData["dtmOrderAppDateConditions"] )
	{
		if ( $aryData["dtmOrderAppDateFrom"] )
		{
			$aryQuery[] = " AND date_trunc('day', o.dtmAppropriationDate ) >= '" . $aryData["dtmOrderAppDateFrom"] . "'";
		}
		if ( $aryData["dtmOrderAppDateTo"] )
		{
			$aryQuery[] = " AND date_trunc('day', o.dtmAppropriationDate ) <= '" . $aryData["dtmOrderAppDateTo"] . "'";
		}
	}
	// ȯ��Σ�.
	if ( $aryData["strOrderCodeConditions"] )
	{
		if ( $aryData["strOrderCodeFrom"] != NULL )
		{
			$aryQuery[] = " AND o.strOrderCode >= '" . $aryData["strOrderCodeFrom"] . "'";
		}
		if ( $aryData["strOrderCodeTo"] != NULL )
		{
			$aryQuery[] = " AND o.strOrderCode <= '" . $aryData["strOrderCodeTo"] . "'";
		}
	}
	// ���ʥ�����
	if ( $aryData["strProductCodeConditions"] )
	{
		if ( $aryData["strProductCodeFrom"] )
		{
			$aryQuery[] = " AND tod.strProductCode >= '" . $aryData["strProductCodeFrom"] . "'";
		}
		if ( $aryData["strProductCodeTo"] )
		{
			$aryQuery[] = " AND tod.strProductCode <= '" . $aryData["strProductCodeTo"] . "'";
		}
	}
	// ���ϼ�
	if ( $aryData["lngInputUserCodeConditions"] && $aryData["strInputUserDisplayCode"] )
	{
		$aryQuery[] = " AND u1.strUserDisplayCode = '" . $aryData["strInputUserDisplayCode"] . "'";
	}
	// ������
	if ( $aryData["lngCustomerCodeConditions"] && $aryData["strCustomerDisplayCode"] )
	{
		$aryQuery[] = " AND to_number ( c.strCompanyDisplayCode, '9999999') = '" . $aryData["strCustomerDisplayCode"] . "'";
	}
	// ����
	if ( $aryData["lngInChargeGroupCodeConditions"] && $aryData["strInChargeGroupDisplayCode"] )
	{
	//	$aryQuery[] = " AND g.strGroupDisplayCode = '" . $aryData["strInChargeGroupDisplayCode"] . "'";
		$aryQuery[] = " AND mp.lngInchargeGroupCode = (select lngGroupCode from m_group where strGroupDisplayCode = '".$aryData["strInChargeGroupDisplayCode"]."')";
	}
	// ô����
	if ( $aryData["lngInChargeUserCodeConditions"] && $aryData["strInChargeUserDisplayCode"] )
	{
	//	$aryQuery[] = " AND u2.strUserDisplayCode = '" . $aryData["strInChargeUserDisplayCode"] . "'";
		$aryQuery[] = " AND mp.lngInchargeUserCode = (select lngUserCode from m_user where strUserDisplayCode = '".$aryData["strInChargeUserDisplayCode"]."')";
	}


	// ����ե���Ϣ���
	// ��ǽ�����ɤ�ȯ�� ���� ���֥����ɤ���ǧ ���� ̵���ե饰���� ���� ���֥����ɺ���
	/*
	$aryQuery[] = " AND m.lngFunctionCode = " . DEF_FUNCTION_PO1;
	$aryQuery[] = " AND t.lngWorkflowStatusCode = " . DEF_STATUS_APPROVE;
	$aryQuery[] = " AND m.bytInvalidFlag = FALSE";
	$aryQuery[] = " AND t.lngWorkflowSubCode =";
	$aryQuery[] = "(";
	$aryQuery[] = "  SELECT MAX ( t2.lngWorkflowSubCode )";
	$aryQuery[] = "  FROM t_Workflow t2";
	$aryQuery[] = "  WHERE t.lngWorkflowCode = t2.lngWorkflowCode";
	$aryQuery[] = ")";
	*/
	// A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
	// B:��ȯ��׾��֤Υǡ���
	// C:����ե���¸�ߤ��ʤ�(¨ǧ�ڰƷ�)
	// D:�־�ǧ�׾��֤ˤ���Ʒ�
	// A OR ( B AND ( C OR D ) )
	$aryQuery[] = " AND (";

	// A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
	$aryQuery[] = "  o.lngOrderStatusCode > " . DEF_ORDER_ORDER;

	$aryQuery[] = "  OR";
	$aryQuery[] = "  (";

	// B:��ȯ��׾��֤Υǡ���
	$aryQuery[] = "    o.lngOrderStatusCode = " . DEF_ORDER_ORDER;
	$aryQuery[] = "     AND";
	$aryQuery[] = "    (";

	// C:����ե���¸�ߤ��ʤ�(¨ǧ�ڰƷ�)
	$aryQuery[] = "      0 = ";
	$aryQuery[] = "      (";
	$aryQuery[] = "        SELECT COUNT ( mw.lngWorkflowCode ) ";
	$aryQuery[] = "        FROM m_Workflow mw ";
	$aryQuery[] = "        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = o.lngOrderNo";
	$aryQuery[] = "         AND mw.lngFunctionCode = " . DEF_FUNCTION_PO1;
	$aryQuery[] = "      )";

	// D:�־�ǧ�׾��֤ˤ���Ʒ�
	$aryQuery[] = "      OR " . DEF_STATUS_APPROVE . " = ";
	$aryQuery[] = "      (";
	$aryQuery[] = "        SELECT tw.lngWorkflowStatusCode";
	$aryQuery[] = "        FROM m_Workflow mw2, t_Workflow tw";
	$aryQuery[] = "        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = o.lngOrderNo";
	$aryQuery[] = "         AND mw2.lngFunctionCode = " . DEF_FUNCTION_PO1;
	$aryQuery[] = "         AND tw.lngWorkflowSubCode =";
	$aryQuery[] = "        (";
	$aryQuery[] = "          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode";
	$aryQuery[] = "        )";
	$aryQuery[] = "         AND mw2.lngWorkflowCode = tw.lngWorkflowCode";
	$aryQuery[] = "      )";
	$aryQuery[] = "    )";
	$aryQuery[] = "  )";
	$aryQuery[] = ")";


	//$aryQuery[] = " AND m.lngWorkflowCode = t.lngWorkflowCode";
	//$aryQuery[] = " AND to_number ( m.strWorkflowKeyCode, '9999999') = o.lngOrderNo ";
	$aryQuery[] = " AND o.lngInputUserCode = u1.lngUserCode";
	$aryQuery[] = " AND mp.lngInchargeGroupCode = g.lngGroupCode";
	$aryQuery[] = " AND mp.lngInchargeUserCode  = u2.lngUserCode";
	$aryQuery[] = " AND o.lngCustomerCompanyCode = c.lngCompanyCode ";
	$aryQuery[] = "ORDER BY strOrderNo DESC";

	// �ʥ�С��򥭡��Ȥ���Ϣ�������Ģɼ�����ɤ����
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strCopyQuery, $objDB );

	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
	}

	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
	}


	// Ģɼ�ǡ�������������¹ԡ��ơ��֥�����
	$strQuery = implode( "\n", $aryQuery );
//fncDebug('list.txt', $strQuery, __FILE__, __LINE__);


	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		$aryParts["strResult"] .= "<tr class=\"Segs\">\n";

		$aryParts["strResult"] .= "<td>" . $objResult->strorderno . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strcompanydisplaycode . ":" . $objResult->strcompanydisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->struserdisplaycode . ":" . $objResult->struserdisplayname . "</td>\n";

		$aryParts["strResult"] .= "<td align=center>";

		// ���ԡ��ե�����ѥ���¸�ߤ��Ƥ����硢���ԡ�Ģɼ���ϥܥ���ɽ��
		if ( $aryReportCode[$objResult->strreportkeycode] != NULL )
		{
			// ���ԡ�Ģɼ���ϥܥ���ɽ��
			$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
		}

		$aryParts["strResult"] .= "</td>\n<td align=center>";

		// ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
		// Ģɼ���ϥܥ���ɽ��
		if ( $aryReportCode[$objResult->strreportkeycode] == NULL || fncCheckAuthority( DEF_FUNCTION_LO4, $objAuth ) )
		{
			// Ģɼ���ϥܥ���ɽ��
			$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
		}

		$aryParts["strResult"] .= "</td></tr>\n";

		unset ( $strCopyCheckboxObject );
	}

	$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>ȯ�� No.</td>
						<td id=\"Column1\" nowrap>���ϼ�</td>
						<td id=\"Column2\" nowrap>������</td>
						<td id=\"Column3\" nowrap>����</td>
						<td id=\"Column4\" nowrap>ô����</td>
						<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
						<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
	";

	$aryParts["strListType"] = "po";
	$aryParts["HIDDEN"] = getArrayTable( $aryData, "HIDDEN" );


	$objDB->close();

	$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// HTML����
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/parts.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;

?>
