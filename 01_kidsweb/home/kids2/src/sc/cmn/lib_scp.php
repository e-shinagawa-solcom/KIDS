<?
// ----------------------------------------------------------------------------
/**
*       ������  ʬǼ�ѥ����å��ؿ���
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         ��ʬǼ�����å�
*
*       ��������
*       2013.05.31����������Ψ�������������ʴ��������Ψ�����Ǥ��ʤ���硢�ǿ����֤���Ψ��������� ��
*
*/
// ----------------------------------------------------------------------------



/**
* ����μ���ǡ����˴ؤ��ơ����μ���ǡ����������֤��Ŀ������ؿ�
*
*	������������ۤ�ꤽ�μ���No����ꤷ�Ƥ�����夹�٤Ƥ���
*	����Ĥ��������
*
*	@param  Integer 	$lngReceiveNo 	�����ֹ�
*	@param  Integer 	$lngSalesNo 	�оݳ��Ȥ��ʤ����No����彤��������
*	@param	Integer		$lngCalcCode	ü������������
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*
*	��������
*	2004.04.16	��Ŀ������ݤ�ü�������������к���Ԥ��褦���ѹ�
*/
function fncGetSalesRemains ( $lngReceiveNo, $lngSalesNo, $lngCalcCode, $objDB )
{

	// �����ֹ椬¸�ߤ��ʤ���礽�Τޤ޽�λ
	if ( $lngReceiveNo == "" or $lngReceiveNo == 0 )
	{
		return 0;
	}

	// �ǿ��μ���Υǡ������������
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo				as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode			as strReceiveCode";
	$arySql[] = "	,r.strCustomerReceiveCode	as strCustomerReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode		as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode		as lngMonetaryUnitCode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strReceiveCode = (";
	$arySql[] = "		SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $lngReceiveNo . " ) ";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0 ";
	$arySql[] = "	AND r.lngRevisionNo = ( ";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = ( ";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) )";
	$arySql[] = "	AND 0 <= ( ";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";
	$strQuery = implode("\n", $arySql);

//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult					= $objDB->fetchObject( $lngResultID, 0 );
		$lngNewReceiveNo 			= $objResult->lngreceiveno;
//		$strNewReceiveCode			= $objResult->strreceivecode;
		$strNewCutomerReceiveCode 	= $objResult->strcustomerreceivecode;
		$lngNewReceiveStatusCode 	= $objResult->lngreceivestatuscode;
		$ReceivelngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// ����No�ϻ��ꤷ�Ƥ��뤬����ͭ���ʺǿ�����¸�ߤ��ʤ����Ϥ��Τޤ޽�λ
		return 0;
	}
	$objDB->freeResult( $lngResultID );

	// ��������̲�ñ�̥����ɤ������оݷ��������
	if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$ReceivelngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$ReceivelngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}

	// �ǿ���������پ�����������
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	 rd.lngReceiveDetailNo	as lngOrderDetailNo";
	$arySql[] = "	,rd.lngReceiveDetailNo	as lngReceiveDetailNo";
	$arySql[] = "	,rd.strProductCode		as strProductCode";
	$arySql[] = "	,rd.lngSalesClassCode	as lngSalesClassCode";
	$arySql[] = "	,rd.dtmDeliveryDate		as dtmDeliveryDate";
	$arySql[] = "	,rd.lngConversionClassCode	as lngConversionClassCode";
	$arySql[] = "	,rd.curProductPrice		as curProductPrice";
	$arySql[] = "	,rd.lngProductQuantity	as lngProductQuantity";
	$arySql[] = "	,rd.lngProductUnitCode	as lngProductUnitCode";
	$arySql[] = "	,rd.lngTaxClassCode		as lngTaxClassCode";
	$arySql[] = "	,rd.lngTaxCode			as lngTaxCode";
	$arySql[] = "	,rd.curTaxPrice			as curTaxPrice";
	$arySql[] = "	,rd.curSubTotalPrice	as curSubTotalPrice";
	$arySql[] = "	,rd.strNote				as strDetailNote";
	$arySql[] = "	,p.lngCartonQuantity	as lngCartonQuantity";
	$arySql[] = "FROM";
	$arySql[] = "	t_ReceiveDetail rd";
	$arySql[] = "	,m_Product p";
	$arySql[] = "WHERE";
	$arySql[] = "	rd.lngReceiveNo = " . $lngNewReceiveNo;
	$arySql[] = "	AND rd.strProductCode = p.strProductCode";
	$arySql[] = "ORDER BY lngSortKey ASC";
	$strQuery = implode("\n", $arySql);

//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryReceiveDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
		return 1;
	}
	$objDB->freeResult( $lngResultID );



	// Ʊ���ָܵҼ����ֹ�פ���ꤷ�Ƥ���ǿ����򸡺�
	$arySql = array();
	$arySql[] = "SELECT distinct";
	$arySql[] = "	s.lngSalesNo as lngSalesNo";
	$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
	$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";

	$arySql[] = "	,case when (1<(select count(lngreceiveno) from m_receive where strreceivecode = r.strreceivecode)) then";
	$arySql[] = "			(";
	$arySql[] = "				select";
	$arySql[] = "					max(mr.lngreceiveno)";
	$arySql[] = "				from";
	$arySql[] = "					m_receive mr";
	$arySql[] = "				where";
	$arySql[] = "					mr.strreceivecode = r.strreceivecode";
	$arySql[] = "					and mr.bytInvalidFlag = false";
	$arySql[] = "					and mr.lngRevisionNo >= 0 ";
	$arySql[] = "					and mr.lngRevisionNo = (";
	$arySql[] = "						select max(mr2.lngRevisionNo) from m_receive mr2 where mr2.strreceivecode = mr.strreceivecode )";
	$arySql[] = "						and 0 <= (";
	$arySql[] = "							select min(mr3.lngRevisionNo) from m_receive mr3 where mr3.bytinvalidflag = false and mr3.strreceivecode = mr.strreceivecode";
	$arySql[] = "						)";

	$arySql[] = "			)";
	$arySql[] = "		else";
	$arySql[] = "		tsd.lngreceiveno";
	$arySql[] = "	end";

	$arySql[] = "FROM";
	$arySql[] = "	m_Sales s";
	$arySql[] = "	left join t_salesdetail tsd";
	$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
	$arySql[] = "	,m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strCustomerReceiveCode = '" . $strNewCutomerReceiveCode . "'";
	$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
	$arySql[] = "	AND s.bytInvalidFlag = FALSE";
	$arySql[] = "	AND s.lngRevisionNo >= 0";
	$arySql[] = "	AND s.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
	$arySql[] = "		AND 0 <= (";
	$arySql[] = "			SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
	$arySql[] = "		)";
	// ������ $lngSalesNo �����ꤵ��Ƥ����礽������ֹ�Υǡ������оݳ��Ȥ���
	if ( $lngSalesNo != "" )
	{
		$arySql[] = "AND s.lngSalesNo <> " . $lngSalesNo;
	}
	$strQuery = implode("\n", $arySql);
//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);


	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ���ǡ�����¸�ߤ�����
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$arySalesResult[] = $objDB->fetchArray( $lngResultID, $i );

			// ���پ�����������
			$arySql = array();
			$arySql[] = "SELECT";
			$arySql[] = "	sd.lngSalesDetailNo as lngOrderDetailNo";
			$arySql[] = "	,sd.strProductCode as strProductCode";
			$arySql[] = "	,sd.lngSalesClassCode as lngSalesClassCode";
			$arySql[] = "	,sd.dtmDeliveryDate as dtmDeliveryDate";
			$arySql[] = "	,sd.lngConversionClassCode as lngConversionClassCode";
			$arySql[] = "	,sd.curProductPrice as curProductPrice";
			$arySql[] = "	,sd.lngProductQuantity as lngProductQuantity";
			$arySql[] = "	,sd.lngProductUnitCode as lngProductUnitCode";
			$arySql[] = "	,sd.lngTaxClassCode as lngTaxClassCode";
			$arySql[] = "	,sd.lngTaxCode as lngTaxCode";
			$arySql[] = "	,sd.curTaxPrice as curTaxPrice";
			$arySql[] = "	,sd.curSubTotalPrice as curSubTotalPrice";
			$arySql[] = "	,sd.strNote as strDetailNote";
//			$arySql[] = "	,sd.lngreceiveno";
			$arySql[] = "	,".$arySalesResult[$i]["lngreceiveno"]." as lngreceiveno";	// �� lngreceiveno ���顢�ǿ��� lngreceiveno ��
			$arySql[] = "	,sd.lngreceivedetailno";
			$arySql[] = "	,p.lngCartonQuantity as lngCartonQuantity";
			$arySql[] = "FROM";
			$arySql[] = "	t_SalesDetail sd";
			$arySql[] = "	,m_Product p";
			$arySql[] = "WHERE";
			$arySql[] = "	sd.lngSalesNo   = " . $arySalesResult[$i]["lngsalesno"];
//			$arySql[] = "	and sd.lngreceiveno = " . $arySalesResult[$i]["lngreceiveno"];
// �� ���ʬǼ�塢���������б��ʲ���
			
			$arySql[] = "	and sd.lngreceiveno in";
			$arySql[] = "		(";
			$arySql[] = "			select";
			$arySql[] = "				mr.lngreceiveno";
			$arySql[] = "			from";
			$arySql[] = "				m_receive mr";
			$arySql[] = "			where";
			$arySql[] = "				mr.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = ".$arySalesResult[$i]["lngreceiveno"].")";
			$arySql[] = "				and mr.bytInvalidFlag = false";
			$arySql[] = "				AND mr.lngRevisionNo >= 0 ";
			$arySql[] = "		)";
			
// ��
			$arySql[] = "	AND sd.strProductCode = p.strProductCode";

			$strSalesDetailQuery = implode("\n",$arySql);
//fncDebug('lib_scp.txt', $strSalesDetailQuery, __FILE__, __LINE__);

			list ( $lngSalesDetailResultID, $lngSalesDetailResultNum ) = fncQuery( $strSalesDetailQuery, $objDB );

			if ( $lngSalesDetailResultNum )
			{
				for ( $j = 0; $j < $lngSalesDetailResultNum; $j++ )
				{
					$arySalesDetailResult[$i][] = $objDB->fetchArray( $lngSalesDetailResultID, $j );

/*
					// ���Υ�Х������줿 lngreceiveno �����
					if( !empty($arySalesDetailResult[$i][$j]["lngreceiveno"] ) )
					{
						$arySql = array();
						$arySql[] = "select";
						$arySql[] = "	mr.lngreceiveno";
						$arySql[] = "from";
						$arySql[] = "	m_receive mr";
						$arySql[] = "where";
						$arySql[] = "	mr.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = ".$arySalesDetailResult[$i][$j]["lngreceiveno"].")";
						$arySql[] = "	and mr.bytInvalidFlag = false";
						$arySql[] = "	and mr.lngRevisionNo >= 0 ";

						$strReceiveQuery = implode("\n",$arySql);
						list( $lngReceiveResultId, $lngReceiveResultNum ) = fncQuery( $strReceiveQuery, $objDB );
						if ( $lngReceiveResultNum )
						{
							$aryBuff = array();
							for ( $k = 0; $k < $lngReceiveResultNum; $k++ )
							{
								 $aryBuff[$k] = $objDB->fetchArray( $lngReceiveResultId, $k );
								$arySalesDetailResult[$i][$j]["aryReceiveNo"][] = $aryBuff[$k]["lngreceiveno"];
							}
//fncDebug('lib_scp.txt', $arySalesDetailResult[$i][$j]["aryReceiveNo"], __FILE__, __LINE__);
						}
					}
*/
				}
			}
			$objDB->freeResult( $lngSalesDetailResultID );
		}
	}
	else
	{
		// ��夬¸�ߤ��ʤ�����ˤĤ��ƤϤ��Τޤ޼���ĤȤ�������
		for ( $i = 0; $i < count($aryReceiveDetailResult); $i++ )
		{
			$aryRemainsDetail[$i]["lngorderdetailno"] 		= $aryReceiveDetailResult[$i]["lngorderdetailno"];			// ���ٹ��ֹ�
			$aryRemainsDetail[$i]["strproductcode"] 		= $aryReceiveDetailResult[$i]["strproductcode"];			// ���ʥ�����
			$aryRemainsDetail[$i]["lngsalesclasscode"] 		= $aryReceiveDetailResult[$i]["lngsalesclasscode"];			// ����ʬ������
			$aryRemainsDetail[$i]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryReceiveDetailResult[$i]["dtmdeliverydate"]);								// Ǽ�����ʼ���ʸ�����ִ���
			$aryRemainsDetail[$i]["lngconversionclasscode"] = $aryReceiveDetailResult[$i]["lngconversionclasscode"];	// ������ʬ������
			$aryRemainsDetail[$i]["curproductprice"]		= $aryReceiveDetailResult[$i]["curproductprice"];			// ����ñ���ʲٻѿ��̡�
			$aryRemainsDetail[$i]["lngproductquantity"]		= $aryReceiveDetailResult[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
			$aryRemainsDetail[$i]["lngproductunitcode"]		= $aryReceiveDetailResult[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
//���ܱ߰ʳ����̲ߤ��Ƕ������Ǥˤ���
			if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// �̲ߤ��ߤξ����Ƕ�ʬ���Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// �����Ƕ�ʬ�����ɡʳ��ǡ�
			}
			else
			{
				// �̲ߤ��߰ʳ��ξ����Ƕ�ʬ������Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;										// �����Ƕ�ʬ�����ɡ�����ǡ�
			}
//			$aryRemainsDetail[$i]["lngtaxclasscode"]		= $aryReceiveDetailResult[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
			$aryRemainsDetail[$i]["lngtaxcode"]				= $aryReceiveDetailResult[$i]["lngtaxcode"];				// �����ǥ�����
			$aryRemainsDetail[$i]["curtaxprice"]			= $aryReceiveDetailResult[$i]["curtaxprice"];				// �����Ƕ��
			$aryRemainsDetail[$i]["cursubtotalprice"]		= $aryReceiveDetailResult[$i]["cursubtotalprice"];			// ��ȴ���
			$aryRemainsDetail[$i]["strdetailnote"]			= $aryReceiveDetailResult[$i]["strdetailnote"];				// ����
			$aryRemainsDetail[$i]["lngcartonquantity"]		= $aryReceiveDetailResult[$i]["lngcartonquantity"];			// �����ȥ�����
			$aryRemainsDetail[$i]["lngreceiveno"] 			= $lngNewReceiveNo;											// �����ֹ�
			$aryRemainsDetail[$i]["lngreceivedetailno"] 	= $aryReceiveDetailResult[$i]["lngreceivedetailno"];;		// ���������ֹ�
		}
		$objDB->freeResult( $lngResultID );

//fncDebug('lib_scp.txt', $strSalesDetailQuery, __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);

		return $aryRemainsDetail;
	}
//fncDebug('lib_scp.txt', $arySalesDetailResult, __FILE__, __LINE__);

	$objDB->freeResult( $lngResultID );

	$lngCnt = 0;		// �Ĥ����Ĥ��ä��Կ�������

	// ���ȸ������������˼����������ˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
	for ( $i = 0; $i < count($aryReceiveDetailResult); $i++ )
	{
		
		$lngOrderDetailNo 		= $aryReceiveDetailResult[$i]["lngorderdetailno"];			// ���ٹ��ֹ�
		$strProductCode 		= $aryReceiveDetailResult[$i]["strproductcode"];			// ���ʥ�����
		$lngSalesClassCode 		= $aryReceiveDetailResult[$i]["lngsalesclasscode"];			// ����ʬ������
		$lngConversionClassCode = $aryReceiveDetailResult[$i]["lngconversionclasscode"];	// ������ʬ������
		$curProductPrice		= $aryReceiveDetailResult[$i]["curproductprice"];			// ����ñ���ʲٻ�ñ����
		$lngProductQuantity		= $aryReceiveDetailResult[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
		$lngProductUnitCode		= $aryReceiveDetailResult[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
		$curSubTotalPrice		= $aryReceiveDetailResult[$i]["cursubtotalprice"];			// ��ȴ���
		$lngCartonQuantity		= $aryReceiveDetailResult[$i]["lngcartonquantity"];			// �����ȥ�����
		$lngReceiveDetailNo 	= $aryReceiveDetailResult[$i]["lngreceivedetailno"];		// ���ٹ��ֹ�


		// ������ʬ���ٻ�ñ�̷׾�ξ�硢����ñ���ط׻�
		if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
		{
			// 0 ����к�
			if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
			{
				// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
				$lngCartonQuantity = 1;
			}

			// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
			$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

			// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
			$curProductPrice = $curProductPrice / $lngCartonQuantity;

			// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
			// ��ȴ��ۤ�׻�����
			// ��ȴ��ۤ����ʿ��� * ���ʲ���
			$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
			// ü��������Ԥ�
			$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $ReceivelngDigitNumber );

			// ñ�̤�����ñ��
			$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

			// ������ʬ�����ɤ�����ñ�̤˽���
			$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
		}

		$bytEndFlag = 0;
		$lngSalesProductQuantity = 0;
		$curSalesSubTotalPrice = 0;


		for ( $j = 0; $j < count($arySalesResult); $j++ )
		{
			$SaleslngMonetaryUnitCode = $arySalesResult[$j]["lngmonetaryunitcode"];

			// ���������̲�ñ�̥����ɤ������оݷ��������
			if ( $SaleslngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				$SaleslngDigitNumber = 0;		// ���ܱߤξ��ϣ���
			}
			else
			{
				$SaleslngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
			}

			for ( $k = 0; $k < count($arySalesDetailResult[$j]); $k++ )
			{
//fncDebug('lib_scp.txt', $lngOrderDetailNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceivedetailno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $strProductCode 	.'='.$arySalesDetailResult[$j][$k]["strproductcode"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $lngNewReceiveNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceiveno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $ReceivelngMonetaryUnitCode 	.'='.$SaleslngMonetaryUnitCode, __FILE__, __LINE__);

				// �������ٹ��ֹ���Ф���������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
				if ( $lngOrderDetailNo		== $arySalesDetailResult[$j][$k]["lngreceivedetailno"]
					and $strProductCode		== $arySalesDetailResult[$j][$k]["strproductcode"]
					and $lngNewReceiveNo	== $arySalesDetailResult[$j][$k]["lngreceiveno"]
//					and in_array($arySalesDetailResult[$j][$k]["lngreceiveno"], $arySalesDetailResult[$j][$k]["aryReceiveNo"])
					and $ReceivelngMonetaryUnitCode == $SaleslngMonetaryUnitCode )
				{

					
					$strDetailNote		= $arySalesDetailResult[$j][$k]["strdetailnote"];
					
					
					
					// ������ʬ���ٻѷ׾�Ǥ��ä����ϡ�����ñ�̷׾���ѹ�����
					if ( $arySalesDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
					{
						// 0 ����к�
						if ( $arySalesDetailResult[$j][$k]["lngcartonquantity"] == 0 or $arySalesDetailResult[$j][$k]["lngcartonquantity"] == "" )
						{
							// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
							$arySalesDetailResult[$j][$k]["lngcartonquantity"] = 1;
						}

						// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
						$arySalesDetailResult[$j][$k]["lngproductquantity"] 
							= $arySalesDetailResult[$j][$k]["lngproductquantity"] * $arySalesDetailResult[$j][$k]["lngcartonquantity"];

						// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
						$arySalesDetailResult[$j][$k]["curproductprice"] 
							= $arySalesDetailResult[$j][$k]["curproductprice"] / $arySalesDetailResult[$j][$k]["lngcartonquantity"];

						// ��ȴ��ۤ����ʿ��� * ���ʲ���
						$arySalesDetailResult[$j][$k]["cursubtotalprice"] 
							= $arySalesDetailResult[$j][$k]["lngproductquantity"] * $arySalesDetailResult[$j][$k]["curproductprice"];

						// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
						// ü��������Ԥ�
						$arySalesDetailResult[$j][$k]["cursubtotalprice"] 
							= fncCalcDigit( $arySalesDetailResult[$j][$k]["cursubtotalprice"], $lngCalcCode, $SaleslngDigitNumber );

						// ñ�̤�����ñ��
						$arySalesDetailResult[$j][$k]["lngproductunitcode"] = DEF_PRODUCTUNIT_PCS;

						// ������ʬ�����ɤ�����ñ�̤˽���
						$arySalesDetailResult[$j][$k]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;
					}

//fncDebug('lib_scp.txt', $lngNewReceiveNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceiveno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $lngOrderDetailNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceivedetailno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt',$lngProductQuantity .'='. $arySalesDetailResult[$j][$k]["lngproductquantity"] , __FILE__, __LINE__);
//fncDebug('lib_scp.txt',$curSubTotalPrice .'='. $arySalesDetailResult[$j]["cursubtotalprice"] , __FILE__, __LINE__);

					// �������
					if ( $lngProductQuantity >= $arySalesDetailResult[$j][$k]["lngproductquantity"] )
					{
						$lngSalesProductQuantity += $arySalesDetailResult[$j][$k]["lngproductquantity"];
						// ʣ����夫��ι绻�Ǥο������
						if ( $lngProductQuantity < $lngSalesProductQuantity )
						{
							$bytEndFlag = 99;
							break;
						}
					}
					else
					{
						$bytEndFlag = 99;
						break;
					}
					
					// ��ȴ������
					if ( $curSubTotalPrice >= $arySalesDetailResult[$j]["cursubtotalprice"] )
					{
						$curSalesSubTotalPrice += $arySalesDetailResult[$j]["cursubtotalprice"];
						// ʣ����夫��ι绻�Ǥ���ȴ������
						if ( $curSubTotalPrice < $curSalesSubTotalPrice )
						{
							$bytEndFlag = 99;
							break;
						}
					}
					else
					{
						$bytEndFlag = 99;
						break;
					}

					// Ʊ�����ٹԤξ��󤬼�������Ǹ��Ĥ��ä��ݤˤϡ�Ǽ����פȤʤ뤿��ʲ�����
					$bytEndFlag = 1;
				}
			}
			// ������٤˼������٤�Ʊ���Ƥ����Ĥ��ä����ϡ�for ʸȴ��
			if ( $bytEndFlag == 99 )
			{
				break;
			}
		}


		// �������ٹ����������ٹԤ����Ĥ��ä����֤򵭲�
		$aryStatus[] = $bytEndFlag;
		// ������Ф�����夬���Ĥ���ʤ��ä����
		if ( $bytEndFlag == 0 )
		{
			$aryRemainsDetail[$lngCnt]["lngorderdetailno"] 		= $lngOrderDetailNo;										// ���ٹ��ֹ�
			$aryRemainsDetail[$lngCnt]["strproductcode"] 		= $strProductCode;											// ���ʥ�����
			$aryRemainsDetail[$lngCnt]["lngsalesclasscode"] 	= $lngSalesClassCode;										// ����ʬ������
			$aryRemainsDetail[$lngCnt]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryReceiveDetailResult[$i]["dtmdeliverydate"]);									// Ǽ�����ʼ���ʸ�����ִ���
			$aryRemainsDetail[$lngCnt]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// ������ʬ������
			$aryRemainsDetail[$lngCnt]["curproductprice"] 		= $curProductPrice;											// ����ñ��
			$aryRemainsDetail[$lngCnt]["lngproductquantity"] 	= $lngProductQuantity;										// ���ʿ���
			$aryRemainsDetail[$lngCnt]["lngproductunitcode"] 	= $lngProductUnitCode;										// ����ñ��
												// �ʤ����ǤϲٻѤǤ��äƤ⤽�Τޤޤ��ͤ����ꤵ��Ƥ����ΤȤ����
//			$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]		= $aryReceiveDetailResult[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
//���ܱ߰ʳ����̲ߤ��Ƕ������Ǥˤ���
			if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// �̲ߤ��ߤξ����Ƕ�ʬ���Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// �����Ƕ�ʬ�����ɡʳ��ǡ�
			}
			else
			{
				// �̲ߤ��߰ʳ��ξ����Ƕ�ʬ������Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;	
			}
			$aryRemainsDetail[$lngCnt]["lngtaxcode"]			= $aryReceiveDetailResult[$i]["lngtaxcode"];				// �����ǥ�����
			$aryRemainsDetail[$lngCnt]["curtaxprice"]			= $aryReceiveDetailResult[$i]["curtaxprice"];				// �����Ƕ��
			$aryRemainsDetail[$lngCnt]["cursubtotalprice"] 		= $curSubTotalPrice;										// ��ȴ���
			$aryRemainsDetail[$lngCnt]["strdetailnote"]			= $aryReceiveDetailResult[$i]["strdetailnote"];				// ����
			$aryRemainsDetail[$lngCnt]["lngcartonquantity"] 	= $lngCartonQuantity;										// �����ȥ�����
			$aryRemainsDetail[$lngCnt]["lngreceiveno"] 			= $lngNewReceiveNo;											// �����ֹ�
			$aryRemainsDetail[$lngCnt]["lngreceivedetailno"] 	= $lngReceiveDetailNo;										// ���������ֹ�
			
			$lngCnt++;	// �Ĥ����Ĥ��ä��Կ������󥿤򥫥���ȥ��å�
		}
		// ������Ф�����夬¸�ߤ����ޤ���Ǽ���֤ˤʤ����
		else if ( $bytEndFlag == 1 )
		{
			$aryRemainsDetail[$lngCnt]["lngorderdetailno"] 		= $lngOrderDetailNo;										// ���ٹ��ֹ�
			$aryRemainsDetail[$lngCnt]["strproductcode"] 		= $strProductCode;											// ���ʥ�����
			$aryRemainsDetail[$lngCnt]["lngsalesclasscode"] 	= $lngSalesClassCode;										// ����ʬ������
			$aryRemainsDetail[$lngCnt]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryReceiveDetailResult[$i]["dtmdeliverydate"]);									// Ǽ�����ʼ���ʸ�����ִ���
			$aryRemainsDetail[$lngCnt]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// ������ʬ������
			$aryRemainsDetail[$lngCnt]["curproductprice"] 		= $curProductPrice;											// ����ñ��
			// ���ʿ��̤ϼ�����̡��ݡ���������
			$aryRemainsDetail[$lngCnt]["lngproductquantity"] 	= $lngProductQuantity - $lngSalesProductQuantity;			// ���ʿ���
			$aryRemainsDetail[$lngCnt]["lngproductunitcode"] 	= $lngProductUnitCode;										// ����ñ��
												// �ʤ����ǤϲٻѤǤ��äƤ⤽�Τޤޤ��ͤ����ꤵ��Ƥ����ΤȤ����
			$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]		= $aryReceiveDetailResult[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
//���ܱ߰ʳ����̲ߤ��Ƕ������Ǥˤ���
			if ( $SaleslngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// �̲ߤ��ߤξ����Ƕ�ʬ���Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// �����Ƕ�ʬ�����ɡʳ��ǡ�
			}
			else
			{
				// �̲ߤ��߰ʳ��ξ����Ƕ�ʬ������Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;	
			}
			$aryRemainsDetail[$lngCnt]["lngtaxcode"]				= $aryReceiveDetailResult[$i]["lngtaxcode"];			// �����ǥ�����
			$aryRemainsDetail[$lngCnt]["curtaxprice"]			= $aryReceiveDetailResult[$i]["curtaxprice"];				// �����Ƕ��
				// ��ȴ����ۤϼ����ۡ��������
			$aryRemainsDetail[$lngCnt]["cursubtotalprice"] 		= $curSubTotalPrice - $lngSalesSubTotalPrice;				// ��ȴ���
//			$aryRemainsDetail[$lngCnt]["strdetailnote"]			= $aryReceiveDetailResult[$i]["strdetailnote"];				// ����
//			$aryRemainsDetail[$lngCnt]["strdetailnote"]			= $strDetailNote;											// ����
			$aryRemainsDetail[$lngCnt]["lngcartonquantity"] 	= $lngCartonQuantity;										// �����ȥ�����
			$aryRemainsDetail[$lngCnt]["lngreceiveno"] 			= $lngNewReceiveNo;											// �����ֹ�
			$aryRemainsDetail[$lngCnt]["lngreceivedetailno"] 	= $lngReceiveDetailNo;										// ���������ֹ�

			$lngCnt++;	// �Ĥ����Ĥ��ä��Կ������󥿤򥫥���ȥ��å�
		}
	}

//fncDebug('lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);
	return $aryRemainsDetail;
}



/**
* ����μ���Ŀ��ǡ���������ˤƻ��ꤵ��Ƥ����׾�ñ�̤˹�碌�����
*
*	����ĥǡ���������κݤ˻��ꤵ�줿�׾�ñ�̤˽����������
*	����Ĥ��������
*
*	@param	array 		$arySalesRemains 		����ġ���夬¸�ߤ��ʤ����Ǥ⡢���Ƥμ���ǡ�����
*	@param	array 		$aryReceiveDetail 		��������
*	@param	Integer		$lngMonetaryUnitCode	��������̲�ñ�̥�����
*	@param	Integer		$lngCalcCode			ü������������
*	@param	Date		$dtmAppropriationDate	�����׾���
*	@param	Object		$objDB					DB���֥�������
*	@return	Boolean 	$arySalesRemains_New	�¹�����
*						1						�¹Լ��� �����������
*	@access public
*/
function fncSetConversionSalesRemains ( $arySalesRemains, $aryReceiveDetail, $lngMonetaryUnitCode, $lngCalcCode, $dtmAppropriationDate, $objDB )
{
//fncDebug('lib_scp.txt', ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>\n", __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $arySalesRemains, __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $aryReceiveDetail, __FILE__, __LINE__);



	if ( !is_array($aryReceiveDetail) )
	{
		return 1;
	}

	// ������ٹԥǡ����Ȥ���ɽ��������ǡ�������������ѿ��ν����
	$arySalesRemains_New = array();


	// ��������̲�ñ�̥����ɤ������оݷ��������
	if ( $lngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}

	// ���ǡ�����¸�ߤ����硢�ѿ�������Ȥʤ�١������ο���ʬ��
	// ����Ǥ�̵����硢���ǡ�����¸�ߤ��ʤ�
	if ( !is_array($arySalesRemains) )
	{

		for( $i = 0; $i < count($aryReceiveDetail); $i++ )
		{
			// ����Ĥξ���������ü��������Ԥ�
			$arySalesRemains_New[$i]["lngorderdetailno"] 		= $aryReceiveDetail[$i]["lngorderdetailno"];		// ���ٹ��ֹ�
			$arySalesRemains_New[$i]["strproductcode"]			= $aryReceiveDetail[$i]["strproductcode"];			// ���ʥ�����
			$arySalesRemains_New[$i]["lngsalesclasscode"]		= $aryReceiveDetail[$i]["lngsalesclasscode"];		// ����ʬ������
			$arySalesRemains_New[$i]["dtmdeliverydate"] 		= str_replace( "-", "/", $aryReceiveDetail[$i]["dtmdeliverydate"]);	// Ǽ�����ʼ���ʸ�����ִ���
			$arySalesRemains_New[$i]["lngconversionclasscode"]	= $aryReceiveDetail[$i]["lngconversionclasscode"];	// ������ʬ������
			$arySalesRemains_New[$i]["curproductprice"]			= $aryReceiveDetail[$i]["curproductprice"];			// ����ñ���ʲٻ�ñ����
			$arySalesRemains_New[$i]["lngproductquantity"]		= $aryReceiveDetail[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
			$arySalesRemains_New[$i]["lngproductunitcode"]		= $aryReceiveDetail[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
			$arySalesRemains_New[$i]["lngtaxclasscode"]			= $aryReceiveDetail[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
			$arySalesRemains_New[$i]["lngtaxcode"]				= $aryReceiveDetail[$i]["lngtaxcode"];				// �����ǥ�����
			$arySalesRemains_New[$i]["curtaxprice"]				= $aryReceiveDetail[$i]["curtaxprice"];				// �����Ƕ��
			
			// ��ȴ��ۤˤĤ��Ƥ�ü��������������ü��������Ԥ�
			$curSubTotalPrice = $aryReceiveDetail[$i]["lngproductquantity"] * $aryReceiveDetail[$i]["curproductprice"];
			$arySalesRemains_New[$i]["cursubtotalprice"]		= fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );	// ��ȴ���
			$arySalesRemains_New[$i]["strdetailnote"]			= $aryReceiveDetail[$i]["strdetailnote"];			// ����
			$arySalesRemains_New[$i]["lngcartonquantity"]		= $aryReceiveDetail[$i]["lngcartonquantity"];		// �����ȥ�����
			$arySalesRemains_New[$i]["lngreceiveno"] 			= $lngReceiveNo;									// �����ֹ�
			$arySalesRemains_New[$i]["lngreceivedetailno"] 		= $aryReceiveDetail[$i]["lngreceivedetailno"];		// ���������ֹ�
		}

	}
	// ���ǡ�����¸�ߤ�����
	else
	{


//fncDebug('lib_scp.txt', $arySalesRemains, __FILE__, __LINE__);


		// ���ȸ������������˼�����������ĤˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
		for ( $i = 0; $i < count($arySalesRemains); $i++ )
		{
			// ����Ĥ����پ�����ѿ�������
			$lngOrderDetailNo 		= $arySalesRemains[$i]["lngorderdetailno"];			// ���ٹ��ֹ�
			$strProductCode 		= $arySalesRemains[$i]["strproductcode"];			// ���ʥ�����
			$lngSalesClassCode 		= $arySalesRemains[$i]["lngsalesclasscode"];		// ����ʬ������
			$lngConversionClassCode = $arySalesRemains[$i]["lngconversionclasscode"];	// ������ʬ������
			$curProductPrice		= $arySalesRemains[$i]["curproductprice"];			// ����ñ���ʲٻ�ñ����
			$lngProductQuantity		= $arySalesRemains[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
			$lngProductUnitCode		= $arySalesRemains[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
			$curSubTotalPrice		= $arySalesRemains[$i]["cursubtotalprice"];			// ��ȴ���
			$lngCartonQuantity		= $arySalesRemains[$i]["lngcartonquantity"];		// �����ȥ�����
			$lngReceiveNo 			= $arySalesRemains[$i]["lngreceiveno"];				// �����ֹ�
			$lngTaxClassCode		= $arySalesRemains[$i]["lngtaxclasscode"];			// �Ƕ�ʬ������
			$lngReceiveDetailNo 	= $arySalesRemains[$i]["lngreceivedetailno"];		// ���������ֹ�

			if ( $lngTaxClassCode == "" )
			{
				$lngTaxClassCode = 0;
			}
			$curTaxPrice			= $arySalesRemains[$i]["curtaxprice"];				// �ǳ�
			if ( $curTaxPrice == "" )
			{
				$curTaxPrice = 0;
			}

//fncDebug('lib_scp.txt', "++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++", __FILE__, __LINE__);
			for( $j = 0; $j < count($aryReceiveDetail); $j++ )
			{

//fncDebug('lib_scp.txt', $aryReceiveDetail, __FILE__, __LINE__);


			// ���ٹԤ��Ф���Ʊ�����Ƥ����٤�����ĤΥǡ����˸��Ĥ��ä����
			if ( $aryReceiveDetail[$j]["lngorderdetailno"] == $lngReceiveDetailNo 
				and $aryReceiveDetail[$j]["strproductcode"] == $strProductCode 
				and $aryReceiveDetail[$j]["lngreceiveno"] == $lngReceiveNo 
			)
			{

//fncDebug('lib_scp.txt', "Ʊ��ǡ�����\n".$lngOrderDetailNo."\n".$strProductCode."\n".$lngReceiveNo."\n", __FILE__, __LINE__);

				// ����Ĥη׾�ñ�̤ȼ���η׾�ñ�̤��㤦���ޤ�������Ĥη׾�ñ�̤�����ñ�̷׾�Ǥ���
				if ( $aryReceiveDetail[$j]["lngconversionclasscode"] != $lngConversionClassCode 
					and $lngConversionClassCode == DEF_CONVERSION_SEIHIN )
				{
						// 0 ����к�
					if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
					{
						// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
						$lngCartonQuantity = 1;
					}

					// ����η׾�ñ�̤Ǥ���ٻ�ñ�̷׾���ͤ���
					// �ٻѿ��̤����ʿ��� / �����ȥ�����
					$NisugatalngProductQuantity = $lngProductQuantity / $lngCartonQuantity;
					// �⤷���������ٻѿ��̤���������ޤ��������ñ�̿��̤Τޤޤǽ�������
					if ( $NisugatalngProductQuantity - floor($NisugatalngProductQuantity) > 0 )
					{
						// ���κݤ�����ñ�̤ˤĤ��Ƥϥǥե���Ȥ� pcs �����ꤹ��
						$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

						// ������ʬ�ˤĤ��Ƥ�����ñ�̷׾�Ȥ���
						$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
					}
					else
					// ���������ٻѿ��̤���������ޤޤʤ����ϲٻѤ��Ѵ���������
					{
						$lngProductQuantity = $NisugatalngProductQuantity;

						// �ٻѲ��ʤ�����ñ�� * �����ȥ�����
						$curProductPrice = $curProductPrice * $lngCartonQuantity;

						// ��ȴ��ۤϲٻ�ñ�� * �ٻѿ���
						$curSubTotalPrice = $lngProductQuantity * $curProductPrice;

						// ���κݤ�����ñ�̤ˤĤ��Ƥϥǥե���Ȥ� c/t �����ꤹ��
						$lngProductUnitCode = DEF_PRODUCTUNIT_CTN;

						// ������ʬ�ˤĤ��Ƥϲٻ�ñ�̷׾�Ȥ���
						$lngConversionClassCode = DEF_CONVERSION_NISUGATA;
					}
				}
				// ����Ĥη׾�ñ�̤ȼ���η׾�ñ�̤��㤦���ޤ�������Ĥη׾�ñ�̤ϲٻ�ñ�̷׾�Ǥ���
				else if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
				{
					// �ٻѤǤ錄�äƤ���ݤˤϤ������׾夵��Ƥ��ʤ��Τ�Ʊ���ʤΤǷ׻����ʤ�

					// ���κݤ�����ñ�̤ˤĤ��Ƥϥǥե���Ȥ� c/t �����ꤹ��
					$lngProductUnitCode = DEF_PRODUCTUNIT_CTN;

					// ������ʬ�ˤĤ��Ƥϲٻ�ñ�̷׾�Ȥ���
					$lngConversionClassCode = DEF_CONVERSION_NISUGATA;
				}
				
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
				// ��ȴ��ۤ�׻�����
				// ��ȴ��ۤϿ��� * ñ��
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
				$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );

// �Ƕ�ʬ�����꤬���Ǥξ���ǳۤ�������ͤ���ȴ��ۤȤ���
				if ( $lngTaxClassCode == DEF_TAXCLASS_UCHIZEI )
				{
					// �ǳۤ��ͤ��ޤޤ�Ƥ��ʤ����
					if ( $curTaxPrice == 0 )
					{
						// �׾�����ꤽ�λ�����Ψ���Ȥ��
						$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
							. "FROM m_tax "
							. "WHERE dtmapplystartdate <= '" . $dtmAppropriationDate . "' "
							. "AND dtmapplyenddate >= '" . $dtmAppropriationDate . "' "
							. "GROUP BY lngtaxcode, curtax "
							. "ORDER BY 3 ";

						// ��Ψ�ʤɤμ��������꡼�μ¹�
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

						if ( $lngResultNum == 1 )
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$curTax = $objResult->curtax;
						}
						else
						{
							$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
								. "FROM m_tax "
								. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
								. "GROUP BY lngtaxcode, curtax ";
							// ��Ψ�ʤɤμ��������꡼�μ¹�
							list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

							if ( $lngResultNum == 1 )
							{
								$objResult = $objDB->fetchObject( $lngResultID, 0 );
								$curTax = $objResult->curtax;
							}
							else
							{
								fncOutputError ( 9051, DEF_ERROR, "�����Ǿ���μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
							}
						}
						$objDB->freeResult( $lngResultID );

						$curTaxPrice = $curSubTotalPrice * $curTax;
						// ü��������Ԥ�
						$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );
					}
					// ��ȴ��ۤ�ñ���߿��̡��ǳ�
					$curSubTotalPrice = $curSubTotalPrice - $curTotalPrice;
				}
				// for ʸȴ��
				//break;
			}
			}
			
			// �Ѵ����줿�ͤ����ꤹ��
			$arySalesRemains_New[$i]["lngorderdetailno"] 		= $lngOrderDetailNo;								// ���ٹ��ֹ�
			$arySalesRemains_New[$i]["strproductcode"]			= $strProductCode;									// ���ʥ�����
			$arySalesRemains_New[$i]["lngsalesclasscode"]		= $lngSalesClassCode;								// ����ʬ������
			$arySalesRemains_New[$i]["dtmdeliverydate"] 		
					= str_replace( "-", "/", $arySalesRemains[$i]["dtmdeliverydate"]);								// Ǽ�����ʼ���ʸ�����ִ���
			$arySalesRemains_New[$i]["lngconversionclasscode"]	= $lngConversionClassCode;							// ������ʬ������
			$arySalesRemains_New[$i]["curproductprice"]			= $curProductPrice;									// ����ñ���ʲٻ�ñ����
			$arySalesRemains_New[$i]["lngproductquantity"]		= $lngProductQuantity;								// ���ʿ��̡ʲٻѿ��̡�
			$arySalesRemains_New[$i]["lngproductunitcode"]		= $lngProductUnitCode;								// ����ñ�̡ʲٻ�ñ�̡�
			$arySalesRemains_New[$i]["lngtaxclasscode"]			= $arySalesRemains[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
			$arySalesRemains_New[$i]["lngtaxcode"]				= $arySalesRemains[$i]["lngtaxcode"];				// �����ǥ�����
			$arySalesRemains_New[$i]["curtaxprice"]				= $arySalesRemains[$i]["curtaxprice"];				// �����Ƕ��
			$arySalesRemains_New[$i]["cursubtotalprice"]		= $curSubTotalPrice;								// ��ȴ���
			$arySalesRemains_New[$i]["strdetailnote"]			= $arySalesRemains[$i]["strdetailnote"];			// ����
			$arySalesRemains_New[$i]["lngcartonquantity"]		= $lngCartonQuantity;								// �����ȥ�����
			$arySalesRemains_New[$i]["lngreceiveno"] 			= $lngReceiveNo;									// �����ֹ�
			$arySalesRemains_New[$i]["lngreceivedetailno"] 		= $lngReceiveDetailNo;								// ���������ֹ�

//fncDebug('lib_scp.txt', $arySalesRemains_New[$i], __FILE__, __LINE__);
		}
	}


	return $arySalesRemains_New;
}



/**
* ����μ���ǡ����˴ؤ��ơ����μ���ǡ����������֤��Ŀ������ؿ�
*
*	������������ۤ�ꤽ�μ���No����ꤷ�Ƥ�����夹�٤Ƥ���
*	����Ĥ��������
*
*	@param  Integer 	$lngReceiveNo 				�����ֹ�
*	@param	Array		$arySalesDetail				�����Ͽ�ˤ����ꤵ�줿���پ���
*	@param	Integer		$lngReceiveMonetaryUnitCode	��������̲�ñ�̥�����
*	@param	Integer		$lngSalesMonetaryUnitCode	�������̲�ñ�̥�����
*	$param	Integer		$lngSalesNo					�оݳ��Ȥ������No������彤�������ѡ�
*	@param	Integer		$lngCalcCode				ü������������
*	@param  Object		$objDB						DB���֥�������
*	@return Boolean 	0							�¹�����
*						1							�¹Լ��� �����������
*						50							�¹����������٤����Ƥ˼���Ĥ�Ķ�������Ϥʤ�
*						99							����İʾ�˻��ꤵ��Ƥ���
*	@access public
*
*	��������
*	2004.04.16	fncGetSalesRemains �ؿ��ΰ����ѹ���ȼ������
*	2004.04.19	arySalesDetail �����Key����̾��ʸ������ʸ���˽���
*	2004.04.20	����Ĥ��᤿�塢����Ĥ�ñ�̷׾夬�ٻѤǤ��ä�������Ӥ����������ʤ�Х��ν���
*/
function fncGetStatusSalesRemains ( $lngReceiveNo, $arySalesDetail, $lngReceiveMonetaryUnitCode, $lngSalesMonetaryUnitCode, 
									$lngSalesNo, $lngCalcCode, $objDB )
{
	
	// ����Ĥ����ؿ��θƤӽФ�
	$aryRemainsDetail = fncGetSalesRemains ( $lngReceiveNo, $lngSalesNo, $lngCalcCode, $objDB );
fncDebug('lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);

	// �ؿ���̤��
	if ( $aryRemainsDetail == 1 )
	{
		// �۾ｪλ
		return 1;
	}
	else if ( $aryRemainsDetail == 0 )
	{
		return 0;
	}

	// ��������̲�ñ�̥����ɤ������оݷ��������
	if ( $lngReceiveMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}

	// ����Ĥ�¸�ߤ����
	// ������ꤵ�줿����Ĵ����������Ĥ�����å���������İʾ����ʸ���Ƥ��ʤ����ɤ�����Ĵ������
	for ( $i = 0; $i < count($aryRemainsDetail); $i++ )
	{
		$lngOrderDetailNo 		= $aryRemainsDetail[$i]["lngorderdetailno"];			// ���ٹ��ֹ�

		$strProductCode 		= $aryRemainsDetail[$i]["strproductcode"];				// ���ʥ�����
		$lngSalesClassCode 		= $aryRemainsDetail[$i]["lngsalesclasscode"];			// ����ʬ������
		$lngConversionClassCode = $aryRemainsDetail[$i]["lngconversionclasscode"];		// ������ʬ������
		$curProductPrice		= $aryRemainsDetail[$i]["curproductprice"];				// ����ñ���ʲٻ�ñ����
		$lngProductQuantity		= $aryRemainsDetail[$i]["lngproductquantity"];			// ���ʿ��̡ʲٻѿ��̡�
		$lngProductUnitCode		= $aryRemainsDetail[$i]["lngproductunitcode"];			// ����ñ�̡ʲٻ�ñ�̡�
		$curSubTotalPrice		= $aryRemainsDetail[$i]["cursubtotalprice"];			// ��ȴ���
		$lngCartonQuantity		= $aryRemainsDetail[$i]["lngcartonquantity"];			// �����ȥ�����
		$lngReceiveNo			= $aryRemainsDetail[$i]["lngreceiveno"];				// �����ֹ�
		$lngReceiveDetailNo		= $aryRemainsDetail[$i]["lngreceivedetailno"];			// ���������ֹ�
		

		// ������ʬ���ٻѷ׾�Ǥ��ä����ϡ�����ñ�̷׾���ѹ�����
		if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
		{
			// 0 ����к�
			if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
			{
				// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
				$lngCartonQuantity = 1;
			}

			// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
			$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

			// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
			$curProductPrice = $curProductPrice / $lngCartonQuantity;

			// ��ȴ��ۤ����ʿ��� * ���ʲ���
			$curSubTotalPrice = $lngGoodsQuantity * $curProductPrice;

			// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
			$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );

			// ñ�̤�����ñ��
			$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

			// ������ʬ�����ɤ�����ñ�̤˽���
			$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
		}

		for ( $j = 0; $j < count($arySalesDetail); $j++ )
		{
			// ��������ٹ��ֹ���Ф���������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
			if ( $lngOrderDetailNo	== $arySalesDetail[$j]["lngReceiveDetailNo"]	// $lngOrderDetailNo	== $arySalesDetail[$j]["lngOrderDetailNo"]
				and $strProductCode	== $arySalesDetail[$j]["strProductCode"]
				and $lngReceiveNo	== $arySalesDetail[$j]["lngReceiveNo"]
				and $lngReceiveMonetaryUnitCode == $lngSalesMonetaryUnitCode )
			{
				// ������ʬ���ٻѷ׾�Ǥ��ä����ϡ�����ñ�̷׾���ѹ�����
				if ( $arySalesDetail[$j]["lngConversionClassCode"] != "gs" )
				{
					// �����������ˤϥ����ȥ�������ξ������äƤ��ʤ��Τ�
					// �����Ǥ����ʥ����ɤ�Ʊ���Ȥ������Ȥ������ĤΥ����ȥ����������Ѥ���
					// 0 ����к�
					if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
					{
						// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
						$lngCartonQuantity = 1;
					}

// 2004.04.19 suzukaze update start
					// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
					$arySalesDetail[$j]["lngGoodsQuantity"] 
						= $arySalesDetail[$j]["lngGoodsQuantity"] * $lngCartonQuantity;

					// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
					$arySalesDetail[$j]["curProductPrice"] 
						= $arySalesDetail[$j]["curProductPrice"] / $lngCartonQuantity;

					// ��ȴ��ۤ����ʿ��� * ���ʲ���
					$arySalesDetail[$j]["curSubTotalPrice"] 
						= $arySalesDetail[$j]["lngGoodsQuantity"] * $arySalesDetail[$j]["curProductPrice"];

// 2004.04.16 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
					$arySalesDetail[$j]["curSubTotalPrice"] 
						= fncCalcDigit( $arySalesDetail[$j]["curSubTotalPrice"], $lngCalcCode, $lngDigitNumber );
// 2004.04.16 suzukaze update end

					// ñ�̤�����ñ��
					$arySalesDetail[$j]["lngProductUnitCode"] = DEF_PRODUCTUNIT_PCS;

					// ������ʬ�����ɤ�����ñ�̤˽���
					$arySalesDetail[$j]["lngConversionClassCode"] = DEF_CONVERSION_SEIHIN;
				}

				// �������
				if ( $lngProductQuantity < $arySalesDetail[$j]["lngGoodsQuantity"] )
				{
					// ���̤�����Ŀ��ʾ�
					return 99;
				}
// 2004.04.19 suzukaze update end

				// ��ȴ������
				if ( $curSubTotalPrice < $arySalesDetailResult[$j]["curSubTotalPrice"] )
				{
					// ��ȴ��ۤ�����İʾ�
					return 99;
				}

				// ����Ĥ�Ʊ�����پ��󤬸��Ĥ��ä����ϼ��ιԤ����
				break;
			}
		}
	}
	return 50;	// �¹���������������˼���Ĥ�ۤ������Ϥʤ�
}



/**
* ��������ǡ�������Ͽ�˴ؤ��ơ��������ǡ�������Ͽ���뤳�ȤǤξ����ѹ��ؿ�
*
*	���ξ��֤���Ǽ�ʺѡפξ�硢����No����ꤷ�Ƥ�����硢ʬǼ�Ǥ��ä����ʤ�
*	�ƾ��֤��Ȥˤ������˴ؤ���ǡ����ξ��֤��ѹ�����
*
*	@param  Integer 	$lngReceiveNo 	��夬���Ȥ��Ƥ������No
*	@param	Integer		$lngCalcCode	ü������������
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*
*	��������
*	2004.04.19	ü�����������ɤ��ɲ�
*/
function fncSalesSetStatus ( $lngReceiveNo, $lngCalcCode, $objDB )
{
	// �����ֹ椬¸�ߤ��ʤ���礽�Τޤ޽�λ
	if ( $lngReceiveNo == "" or $lngReceiveNo == 0 )
	{
		return 1;
	}

	// �ǿ��μ���Υǡ������������
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo		as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode	as strReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode	as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode	as lngMonetaryUnitCode";
	$arySql[] = "	,r.strcustomerreceivecode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strReceiveCode = (";
	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $lngReceiveNo;
	$arySql[] = "	)";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0";
	$arySql[] = "	AND r.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = (";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
	$arySql[] = "	)";
	$arySql[] = "	AND 0 <= (";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
	$arySql[] = "	)";
	$strQuery = implode("\n", $arySql);

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );


//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);


	if ( $lngResultNum == 1 )
	{
		$objResult			= $objDB->fetchObject( $lngResultID, 0 );
		$lngNewReceiveNo	= $objResult->lngreceiveno;
		$strNewReceiveCode	= $objResult->strreceivecode;
//		$strNewReceiveCode	= $objResult->strcustomerreceivecode;
		$lngNewReceiveStatusCode	= $objResult->lngreceivestatuscode;
		$ReceivelngMonetaryUnitCode	= $objResult->lngmonetaryunitcode;
	}
	else
	{
		// ����No�ϻ��ꤷ�Ƥ��뤬����ͭ���ʺǿ�����¸�ߤ��ʤ����Ϥ��Τޤ޽�λ
		return 1;
	}
	$objDB->freeResult( $lngResultID );

	// ��������̲�ñ�̥����ɤ������оݷ��������
	if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}

	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	rd.lngReceiveDetailNo";
	$arySql[] = "	,rd.strProductCode		as strProductCode";
	$arySql[] = "	,rd.lngSalesClassCode	as lngSalesClassCode";
	$arySql[] = "	,rd.lngConversionClassCode	as lngConversionClassCode";
	$arySql[] = "	,rd.curProductPrice		as curProductPrice";
	$arySql[] = "	,rd.lngProductQuantity	as lngProductQuantity";
	$arySql[] = "	,rd.lngProductUnitCode	as lngProductUnitCode";
	$arySql[] = "	,rd.curSubTotalPrice	as curSubTotalPrice";
	$arySql[] = "	,p.lngCartonQuantity	as lngCartonQuantity";
	$arySql[] = "FROM";
	$arySql[] = "	t_ReceiveDetail rd";
	$arySql[] = "	,m_Product p";
	$arySql[] = "WHERE";
	$arySql[] = "	rd.lngReceiveNo = " . $lngNewReceiveNo;
	$arySql[] = "	AND rd.strProductCode = p.strProductCode";
	$arySql[] = "ORDER BY lngSortKey ASC";
	
	// �ǿ���������پ�����������
	$strQuery = implode("\n", $arySql);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryReceiveDetail[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
		return 2;
	}
	$objDB->freeResult( $lngResultID );

	// Ʊ���ּ���No�פ���ꤷ�Ƥ���ǿ����򸡺�
	$arySql = array();
	$arySql[] = "SELECT distinct";
	$arySql[] = "	s.lngSalesNo as lngSalesNo";
	$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
	$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";
//	$arySql[] = "	,tsd.lngreceiveno";
	$arySql[] = "FROM";
	$arySql[] = "	m_Sales s";
	$arySql[] = "	left join t_salesdetail tsd";
	$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
	$arySql[] = "	,m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strReceiveCode = '" . $strNewReceiveCode . "'";
//	$arySql[] = "	r.strcustomerReceiveCode = '" . $strNewReceiveCode . "'";
	$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
	$arySql[] = "	AND s.bytInvalidFlag = FALSE";
	$arySql[] = "	AND s.lngRevisionNo >= 0";
	$arySql[] = "	AND s.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
	$arySql[] = "		AND 0 <= (";
	$arySql[] = "		SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
	$arySql[] = "		)";
	$strQuery = implode("\n", $arySql);
//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);
//exit;

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		$arySales = array();
		$arySalesDetail = array();
		// ���ǡ�����¸�ߤ�����
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$arySales[] = $objDB->fetchArray( $lngResultID, $i );

			$arySql = array();
			$arySql[] = "SELECT";
			$arySql[] = "	sd.lngreceiveno";
			$arySql[] = "	,sd.lngreceivedetailno";
			$arySql[] = "	,sd.strProductCode		as strProductCode";
			$arySql[] = "	,sd.lngSalesClassCode	as lngSalesClassCode";
			$arySql[] = "	,sd.lngConversionClassCode as lngConversionClassCode";
			$arySql[] = "	,sd.curProductPrice		as curProductPrice";
			$arySql[] = "	,sd.lngProductQuantity	as lngProductQuantity";
			$arySql[] = "	,sd.lngProductUnitCode	as lngProductUnitCode";
			$arySql[] = "	,sd.curSubTotalPrice	as curSubTotalPrice";
			$arySql[] = "	,p.lngCartonQuantity	as lngCartonQuantity";
			$arySql[] = "FROM";
			$arySql[] = "	t_SalesDetail sd";
			$arySql[] = "	,m_Product p";
			$arySql[] = "WHERE";
			$arySql[] = "	sd.lngSalesNo = " . $arySales[$i]["lngsalesno"];
			$arySql[] = "	AND sd.strProductCode = p.strProductCode";
			$arySql[] = "ORDER BY lngSortKey ASC";
			
			// ���پ�����������
			$strSalesDetailQuery = implode("\n", $arySql);
//fncDebug('lib_scp.txt', $strSalesDetailQuery, __FILE__, __LINE__);
			list ( $lngSalesDetailResultID, $lngSalesDetailResultNum ) = fncQuery( $strSalesDetailQuery, $objDB );

			if ( $lngSalesDetailResultNum )
			{
				for ( $j = 0; $j < $lngSalesDetailResultNum; $j++ )
				{
					$arySalesDetail[$i][] = $objDB->fetchArray( $lngSalesDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngSalesDetailResultID );
		}

//fncDebug('lib_scp.txt', $arySalesDetail, __FILE__, __LINE__);

		// ���ȸ������������˼����������ˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
		for ( $i = 0; $i < count($aryReceiveDetail); $i++ )
		{
			// ���ȸ���������ٹ��ֹ������������������ٹ��ֹ�ˤҤ�Ť�����夬�ä����ߤ���뤿��
//			$lngTSDReceiveNo 		= $aryReceiveDetail[$i]["lngreceiveno"];			// �����ֹ�
			$lngReceiveDetailNo 	= $aryReceiveDetail[$i]["lngreceivedetailno"];		// ���ٹ��ֹ�

			$strProductCode 		= $aryReceiveDetail[$i]["strproductcode"];			// ���ʥ�����
			$lngSalesClassCode 		= $aryReceiveDetail[$i]["lngsalesclasscode"];			// ����ʬ������
			$lngConversionClassCode = $aryReceiveDetail[$i]["lngconversionclasscode"];	// ������ʬ������
			$curProductPrice		= $aryReceiveDetail[$i]["curproductprice"];			// ����ñ���ʲٻ�ñ����
			$lngProductQuantity		= $aryReceiveDetail[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
			$lngProductUnitCode		= $aryReceiveDetail[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
			$curSubTotalPrice		= $aryReceiveDetail[$i]["cursubtotalprice"];		// ��ȴ���
			$lngCartonQuantity		= $aryReceiveDetail[$i]["lngcartonquantity"];		// �����ȥ�����

			// ������ʬ���ٻ�ñ�̷׾�ξ�硢����ñ���ط׻�
			if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
			{
				// 0 ����к�
				if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
				{
					// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
					$lngCartonQuantity = 1;
				}

				// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
				$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

				// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
				$curProductPrice = $curProductPrice / $lngCartonQuantity;

				// ��ȴ��ۤ�����ñ�� * ���ʿ���
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;

				// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
				// ��ȴ��ۤ�׻�����
				// ��ȴ��ۤϿ��� * ñ��
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
				$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );
			}

			$bytEndFlag = 0;
			$lngSalesProductQuantity = 0;
			$curSalesSubTotalPrice = 0;
			
			for ( $j = 0; $j < count($arySales); $j++ )
			{
				$SaleslngMonetaryUnitCode = $arySales[$j]["lngmonetaryunitcode"];

				// �������̲�ñ�̥����ɤ������оݷ��������
				if ( $SaleslngMonetaryUnitCode == DEF_MONETARY_YEN )
				{
					$SaleslngDigitNumber = 0;		// ���ܱߤξ��ϣ���
				}
				else
				{
					$SaleslngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
				}


				for ( $k = 0; $k < count($arySalesDetail[$j]); $k++ )
				{

					// �������ٹ��ֹ���Ф���������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
					// ����˲ä����̲ߤ�Ʊ�����
					if ( $lngReceiveDetailNo == $arySalesDetail[$j][$k]["lngreceivedetailno"]
						and $strProductCode == $arySalesDetail[$j][$k]["strproductcode"] 
						and $ReceivelngMonetaryUnitCode == $SaleslngMonetaryUnitCode )
					{
//fncDebug('lib_scp.txt', $strProductCode ."=".$lngReceiveDetailNo, __FILE__, __LINE__);

						// ������ʬ���ٻ�ñ�̷׾�ξ�硢����ñ���ط׻�
						if ( $arySalesDetail[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
						{
							// 0 ����к�
							if ( $arySalesDetail[$j][$k]["lngcartonquantity"] == 0 or $arySalesDetail[$j][$k]["lngcartonquantity"] == "" )
							{
								// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
								$arySalesDetail[$j][$k]["lngcartonquantity"] = 1;
							}

							// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
							$arySalesDetail[$j][$k]["lngproductquantity"] 
								= $arySalesDetail[$j][$k]["lngproductquantity"] * $arySalesDetail[$j][$k]["lngcartonquantity"];

							// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
							$arySalesDetail[$j][$k]["curproductprice"] 
								= $arySalesDetail[$j][$k]["curproductprice"] / $arySalesDetail[$j][$k]["lngcartonquantity"];

							// ��ȴ��ۤϲٻ�ñ�� * �ٻѿ���
							$arySalesDetail[$j][$k]["cursubtotalprice"] 
								= $arySalesDetail[$j][$k]["lngproductquantity"] * $arySalesDetail[$j][$k]["curproductprice"];

							// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
							// ü��������Ԥ�
							$arySalesDetail[$j][$k]["cursubtotalprice"] 
								= fncCalcDigit( $arySalesDetail[$j][$k]["cursubtotalprice"], $lngCalcCode, $SaleslngDigitNumber );
						}

						// �������
						if ( $lngProductQuantity > $arySalesDetail[$j][$k]["lngproductquantity"] )
						{
							$lngSalesProductQuantity += $arySalesDetail[$j][$k]["lngproductquantity"];
							// ʣ����夫��ι绻�Ǥο������
							if ( $lngProductQuantity <= $lngSalesProductQuantity )
							{
								$bytEndFlag = 99;
								break;
							}
						}
						else
						{
							$bytEndFlag = 99;
							break;
						}
						
						// ��ȴ������
						if ( $curSubTotalPrice > $arySalesDetail[$j]["cursubtotalprice"] )
						{
							$curSalesSubTotalPrice += $arySalesDetail[$j]["cursubtotalprice"];
							// ʣ����夫��ι绻�Ǥ���ȴ������
							if ( $curSubTotalPrice <= $curSalesSubTotalPrice )
							{
								$bytEndFlag = 99;
								break;
							}
						}
						else
						{
							$bytEndFlag = 99;
							break;
						}

						// Ʊ�����ٹԤξ��󤬼�������Ǹ��Ĥ��ä��ݤˤϡ�Ǽ����פȤʤ뤿��ʲ�����
						$bytEndFlag = 1;
					}
				}
				// ������٤˼������٤�Ʊ���Ƥ����Ĥ��ä����ϡ�for ʸȴ��
				if ( $bytEndFlag == 99 )
				{
					break;
				}
			}
			// �������ٹ����������ٹԤ����Ĥ��ä����֤򵭲�
			$aryStatus[] = $bytEndFlag;
		}
		
		// ���٥����å���$aryStatus�����٤��Ȥξ��֡ˤˤ��������ΤȤ��Ƥξ��֤�Ƚ��
		$flagZERO = 0;
		$flagALL  = 0;
		for ( $i = 0; $i < count($aryStatus); $i++ )
		{
			if ( $aryStatus[$i] == 0 )
			{
				$flagZERO++;
			}
			if ( $aryStatus[$i] == 99 )
			{
				$flagALL++;
			}
		}
//exit;

		// �������٤��Ф��ư�����夬ȯ�����Ƥ��ʤ���硢�ޤ��ϴ�Ǽ�ǤϤʤ����
		// ��flagZERO���������ٿ����Ф��ƥ�������ξ��ºݤϽ�����֤Ǥ��뤬�����ˤ�
		//   ����No�����ꤵ��Ƥ���ΤǤ����Ǥξ��֤ϡ�Ǽ����פȤ����
		if ( $flagALL != count($aryStatus) )
		{
			// ��廲�ȼ���ξ��֤ξ��֤��Ǽ����פȤ���
		
			// �����оݼ���ǡ������å�����
			$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $lngNewReceiveNo . " AND bytInvalidFlag = FALSE FOR UPDATE";

			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// ��Ǽ����׾��֤ؤι�������
			$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_DELIVER . " WHERE lngReceiveNo = " . $lngNewReceiveNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// Ʊ������NO����ꤷ�Ƥ������ξ��֤��Ф��Ƥ��Ǽ����פȤ���
			for ( $i = 0; $i < count($arySales); $i++ )
			{
				// �����о����ǡ������å�����
				$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
					. "WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";

				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// ��Ǽ����׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_DELIVER 
					. " WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			
			return 0;
		}
		else
		// �оݼ���ϴ�Ǽ���֤Ǥ��ä���
		{
			// ��廲�ȼ���ξ��֤ξ��֤��Ǽ�ʺѡפȤ���
		
			// �����оݼ���ǡ������å�����
			$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $lngNewReceiveNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// ��Ǽ�ʺѡ׾��֤ؤι�������
			$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_END . " WHERE lngReceiveNo = " . $lngNewReceiveNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// Ʊ������NO����ꤷ�Ƥ������ξ��֤��Ф��Ƥ��Ǽ�ʺѡפȤ���
			for ( $i = 0; $i < count($arySales); $i++ )
			{
				// �����о����ǡ������å�����
				$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
					. "WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// ��Ǽ�ʺѡ׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_END 
					. " WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			return 0;
		}
	}
	else
	{
		// ���ǡ�����¸�ߤ��ʤ����
		// ���λ��ȸ��ǿ�����ξ��֤�ּ���פ��᤹
		
		// �����оݼ���ǡ������å�����
		$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $lngNewReceiveNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if ( !$lngLockResultNum )
		{
			fncOutputError ( 9051, DEF_ERROR, "̵�����������顼", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngLockResultID );

		// �ּ���׾��֤ؤι�������
		$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_ORDER . " WHERE lngReceiveNo = " . $lngNewReceiveNo;

		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		$objDB->freeResult( $lngUpdateResultID );

		return 0;
	}

	$objDB->freeResult( $lngResultID );

	return 0;
}

?>