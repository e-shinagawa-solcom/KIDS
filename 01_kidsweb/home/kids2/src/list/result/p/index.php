<?
	/** 
	*	Ģɼ���� ���ʴ��� ������̲���
	*
	*	@package   KIDS
	*	@license   http://www.wiseknot.co.jp/ 
	*	@copyright Copyright &copy; 2003, Wiseknot 
	*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
	*	@access    public
	*	@version   1.00
	*
	*	��������
	*	2004.05.21	���ʲ����񸡺���̰����ˤ����ʥ����ɤȤ���ɽ�����Ƥ������Ƥ������ֹ�Ǥ��ä��Х��ν���
	*
	*/
	// ������̲���( * �ϻ���Ģɼ�Υե�����̾ )
	// *.php -> strSessionID       -> index.php

	// �������̤�
	// index.php -> strSessionID       -> frameset.php
	// index.php -> lngReportClassCode -> frameset.php
	// index.php -> strReportKeyCode   -> frameset.php
	// index.php -> lngReportCode      -> frameset.php

	// �����ɤ߹���
	include_once('conf.inc');

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
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// ���������ܼ���
	if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
	{
		$aryColumn = $aryData["SearchColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		unset ( $aryData["SearchColumn"] );
		unset ( $aryColumn );
	}

	//echo getArrayTable( $aryData, "TABLE" );
	//exit;

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


	// ���ʴ������
	// ���ԡ��ե������������������
	$strCopyQuery = "SELECT gp.lngProductNo AS strReportKeyCode, r.lngReportCode FROM t_GoodsPlan gp, t_Report r WHERE r.lngReportClassCode = " . DEF_REPORT_PRODUCT . " AND lngRevisionNo = ( SELECT MAX ( gp2.lngRevisionNo ) FROM t_GoodsPlan gp2 WHERE gp.lngProductNo = gp2.lngProductNo ) AND to_number ( r.strReportKeyCode, '9999999') = gp.lngGoodsPlanCode";

	// ���ʴ����������������
	// 2004.05.21 suzukaze update start
	$aryQuery[] = "SELECT p.strProductCode, p.strProductName, p.lngproductstatuscode,";
	// 2004.05.21 suzukaze update start

	$aryQuery[] = " u1.strUserDisplayCode AS strInputUserDisplayCode,";
	$aryQuery[] = " u1.strUserDisplayName AS strInputUserDisplayName,";
	$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
	$aryQuery[] = " g.strGroupDisplayName AS strInChargeGroupDisplayName,";
	$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
	$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
	$aryQuery[] = " gp.lngProductNo AS strReportKeyCode ";

	// 2004.05.21 suzukaze update start
	$aryQuery[] = "FROM m_Product p";
	$aryQuery[] = " LEFT JOIN m_User u1 ON p.lngInputUserCode = u1.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_User u2 ON p.lngInChargeUserCode = u2.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Group g ON p.lngInChargeGroupCode = g.lngGroupCode";
	$aryQuery[] = ", t_GoodsPlan gp ";
	// 2004.05.21 suzukaze update end

	$aryQuery[] = "WHERE gp.lngRevisionNo =";
	$aryQuery[] = "(";
	$aryQuery[] = "  SELECT MAX ( gp2.lngRevisionNo )";
	$aryQuery[] = "  FROM t_GoodsPlan gp2";
	$aryQuery[] = "  WHERE gp.lngProductNo = gp2.lngProductNo";
	$aryQuery[] = ")";

	/////////////////////////////////////////////////////////////////
	// �������
	/////////////////////////////////////////////////////////////////
	// ��������
	if ( $aryData["dtmInsertDateConditions"] )
	{
		if ( $aryData["dtmInsertDateFrom"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmInsertDate ) >= '" . $aryData["dtmInsertDateFrom"] . "'";
		}
		if ( $aryData["dtmInsertDateTo"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmInsertDate ) <= '" . $aryData["dtmInsertDateTo"] . "'";
		}
	}
	// ���ʹԾ���
	if ( $aryData["lngGoodsPlanProgressCodeConditions"] && $aryData["lngGoodsPlanProgressCode"] )
	{
		$aryQuery[] = " AND gp.lngGoodsPlanProgressCode = " . $aryData["lngGoodsPlanProgressCode"];
	}
	// ��������
	if ( $aryData["dtmRevisionDateConditions"] )
	{
		if ( $aryData["dtmRevisionDateFrom"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate ) >= '" . $aryData["dtmRevisionDateFrom"] . "'";
		}
		if ( $aryData["dtmRevisionDateTo"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate ) <= '" . $aryData["dtmRevisionDateTo"] . "'";
		}
	}
	// ���ʥ�����
	if ( $aryData["strProductCodeConditions"] )
	{
		if ( $aryData["strProductCodeFrom"] )
		{
			$aryQuery[] = " AND p.strProductCode >= '" . $aryData["strProductCodeFrom"] . "'";
		}
		if ( $aryData["strProductCodeTo"] )
		{
			$aryQuery[] = " AND p.strProductCode <= '" . $aryData["strProductCodeTo"] . "'";
		}
	}
	// ����̾
	if ( $aryData["strProductNameConditions"] && $aryData["strProductName"] )
	{
		$aryQuery[] = " AND p.strProductName LIKE '%" . $aryData["strProductName"] . "%'";
	}
	// ����̾(�Ѹ�)
	if ( $aryData["strProductEnglishNameConditions"] && $aryData["strProductEnglishName"] )
	{
		$aryQuery[] = " AND p.strProductEnglishName LIKE '%" . $aryData["strProductEnglishName"] . "%'";
	}
	// ���ϼԥ�����
	if ( $aryData["lngInputUserCodeConditions"] && $aryData["strInputUserDisplayCode"] )
	{
		$aryQuery[] = " AND u1.strUserDisplayCode = '" . $aryData["strInputUserDisplayCode"] . "'";
	}
	// ���祳����
	if ( $aryData["lngInChargeGroupCodeConditions"] && $aryData["strInChargeGroupDisplayCode"] )
	{
		$aryQuery[] = " AND g.strGroupDisplayCode = '" . $aryData["strInChargeGroupDisplayCode"] . "'";
	}
	// ô���ԥ�����
	if ( $aryData["lngInChargeUserCodeConditions"] && $aryData["strInChargeUserDisplayCode"] )
	{
		$aryQuery[] = " AND u2.strUserDisplayCode = '" . $aryData["strInChargeUserDisplayCode"] . "'";
	}

	$aryQuery[] = " AND p.lngProductNo = gp.lngProductNo";

//Add by kou	
	$aryQuery[] = " AND p.bytinvalidflag = false";
	
//end	
	// 2004.05.21 suzukaze update start
	// $aryQuery[] = " AND p.lngInputUserCode = u1.lngUserCode";
	// $aryQuery[] = " AND p.lngInChargeUserCode = u2.lngUserCode";
	// $aryQuery[] = " AND p.lngInChargeGroupCode = g.lngGroupCode ";
	// 2004.05.21 suzukaze update start


	$aryQuery[] = "AND p.lngproductstatuscode != " . DEF_PRODUCT_APPLICATE;

	$aryQuery[] = " ORDER BY p.strProductCode ASC";

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
	$strQuery = join ( "\n", $aryQuery );
//fncDebug( 'lib_list_p.txt', $strQuery, __FILE__, __LINE__);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		$aryParts["strResult"] .= "<tr class=\"Segs\">\n";

	// 2004.05.21 suzukaze update start
	//	$aryParts["strResult"] .= "<td>" . $objResult->strreportkeycode . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "</td>\n";
	// 2004.05.21 suzukaze update end

		$aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";

		$aryParts["strResult"] .= "<td align=center>";

		// ���ԡ��ե�����ѥ���¸�ߤ��Ƥ����硢���ԡ�Ģɼ���ϥܥ���ɽ��
		if ( $aryReportCode[$objResult->strreportkeycode] != NULL )
		{
			// ���ԡ�Ģɼ���ϥܥ���ɽ��
			$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
		}

		$aryParts["strResult"] .= "</td>\n<td align=center>";

		// ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
		// Ģɼ���ϥܥ���ɽ��
		if ( $aryReportCode[$objResult->strreportkeycode] == NULL || fncCheckAuthority( DEF_FUNCTION_LO3, $objAuth ) )
		{
			// Ģɼ���ϥܥ���ɽ��
			$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&strActionList=p' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
		}

		$aryParts["strResult"] .= "</td></tr>\n";

		unset ( $strCopyCheckboxObject );
	}


	// �����ɽ��
	$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>���ʥ�����</td>
						<td id=\"Column1\" nowrap>����̾��</td>
						<td id=\"Column2\" nowrap>���ϼ�</td>
						<td id=\"Column3\" nowrap>����</td>
						<td id=\"Column4\" nowrap>ô����</td>
						<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
						<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
	";

	$aryParts["strListType"] = "p";
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
