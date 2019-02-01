<?
/** 
*	������ʬǼ�ѥ����å��ؿ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	��������
*	ʬǼ�����å�
*
*	��������
*	2004.03.29	�����å��ˤƼ����������٤ν����ɽ���ѥ����ȥ�������ѹ�
*	2004.03.30	�������ܡ��������ʤ��ѹ����줿���Ǥ⡢���ٹ��ֹ�Τߤ�Ƚ�Ǵ��Ȥ����껦����褦���ѹ�����
*	2004.03.30	ȯ��Ĥ����ݤ��Ƕ�ʬ�ʤɤ��ͤ˥ǥե�����ͤ���������褦���ѹ�
*	2004.04.12	ȯ��Ĥ����ݤ�ü��������ɤ����뤫�Ȥ����������ɲ�
*	2004.04.12	ȯ��Ŀ��η׾�ñ�����η׻��ؿ��ˤƻ�����ȯ�����Ƥ��ʤ����Ǥ�����ü��������Ԥ��褦���ѹ�
*	2004.04.16	ȯ��Ĥ������å��ˤ���ȴ��ۤ����Ǥκݤ��������ȴ����ͤˤʤäƤ��ʤ��ä��Х��ν���
*	2004.04.19	aryStockDetail �����Key����̾��ʸ������ʸ���˽��� ���� lngProductQuantity �ȤʤäƤ��������� lngGoodsQuantity ���ѹ�
*	2004.04.20	fncGetStatusStockRemains �ؿ��ˤư������ȯ��Ĥ��ٻ�ñ�̷׾���ϤäƤ����ݤ�����оȥХ�����
*/

/**
* �����ȯ��ǡ����˴ؤ��ơ�����ȯ��ǡ����λ������֤��Ŀ������ؿ�
*
*	ȯ�����ȯ���ۤ�ꤽ��ȯ��No����ꤷ�Ƥ���������٤Ƥ���
*	ȯ��Ĥ��������
*
*	@param  Integer 	$lngOrderNo 	ȯ���ֹ�
*	@param  Integer 	$lngStockNo 	�оݳ��Ȥ��ʤ�����No����������������
*	@param	Integer		$lngCalcCode	ü������������
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*
*	��������
*	2004.04.16	��Ŀ������ݤ�ü�������������к���Ԥ��褦���ѹ�
*       2013.05.31����������Ψ�������������ʴ��������Ψ�����Ǥ��ʤ���硢�ǿ����֤���Ψ��������� ��
*/
function fncGetStockRemains ( $lngOrderNo, $lngStockNo, $lngCalcCode, $objDB )
{
	// ȯ���ֹ椬¸�ߤ��ʤ���礽�Τޤ޽�λ
	if ( $lngOrderNo == "" or $lngOrderNo == 0 )
	{
		return 0;
	}

	// �ǿ���ȯ��Υǡ������������
	$strQuery = "SELECT o.lngOrderNo as lngOrderNo, o.strOrderCode as strOrderCode, "
		. "o.lngOrderStatusCode as lngOrderStatusCode, o.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Order o "
		. "WHERE o.strOrderCode = ( "
		. "SELECT o1.strOrderCode FROM m_Order o1 WHERE o1.lngOrderNo = " . $lngOrderNo . " ) "
		. "AND o.bytInvalidFlag = FALSE "
		. "AND o.lngRevisionNo >= 0 "
		. "AND o.lngRevisionNo = ( "
		. "SELECT MAX( o2.lngRevisionNo ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ) ";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
		$strNewOrderCode = $objResult->strordercode;
		$lngNewOrderStatusCode = $objResult->lngorderstatuscode;
		$OrderlngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// ȯ��No�ϻ��ꤷ�Ƥ��뤬����ͭ���ʺǿ�ȯ��¸�ߤ��ʤ����Ϥ��Τޤ޽�λ
		return 0;
	}
	$objDB->freeResult( $lngResultID );

// 2004.04.16 suzukaze update start
	// ȯ������̲�ñ�̥����ɤ������оݷ��������
	if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$OrderlngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$OrderlngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}
// 2004.04.16 suzukaze update end

	// �ǿ�ȯ������پ�����������
	$strQuery = "SELECT od.lngOrderDetailNo as lngOrderDetailNo, "			// ���ٹ��ֹ�
		. "od.strProductCode as strProductCode, "							// ���ʥ�����
		. "od.lngStockSubjectCode as lngStockSubjectCode, "					// �������ܥ�����
		. "od.lngStockItemCode as lngStockItemCode, "						// �������ʥ�����
		. "od.dtmDeliveryDate as dtmDeliveryDate, "							// Ǽ����
		. "od.lngDeliveryMethodCode as lngCarrierCode, "					// ������ˡ������
		. "od.lngConversionClassCode as lngConversionClassCode, "			// ������ʬ������
		. "od.curProductPrice as curProductPrice, "							// ���ʲ���
		. "od.lngProductQuantity as lngProductQuantity, "					// ���ʿ���
		. "od.lngProductUnitCode as lngProductUnitCode, "					// ����ñ�̥�����
		. "od.lngTaxClassCode as lngTaxClassCode, "							// �����Ƕ�ʬ������
		. "od.lngTaxCode as lngTaxCode, "									// �����ǥ�����
		. "od.curTaxPrice as curTaxPrice, "									// �����Ƕ��
		. "od.curSubTotalPrice as curSubTotalPrice, "						// ��ȴ���
		. "od.strNote as strDetailNote, "									// ����
		. "od.strMoldNo as strSerialNo, "									// �ⷿ�ֹ�
		. "p.lngCartonQuantity as lngCartonQuantity "						// ���ʤΥ����ȥ�����(�����͡�
		. "FROM t_OrderDetail od, m_Product p "
		. "WHERE od.lngOrderNo = " . $lngNewOrderNo . " AND od.strProductCode = p.strProductCode "
		. "ORDER BY lngSortKey ASC";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
		return 1;
	}
	$objDB->freeResult( $lngResultID );

	// Ʊ��ȯ��No����ꤷ�Ƥ���ǿ������򸡺�
	$strQuery = "SELECT s.lngStockNo as lngStockNo, s.lngStockStatusCode as lngStockStatusCode, "
		. "s.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Stock s, m_Order o "
		. "WHERE o.strOrderCode = '" . $strNewOrderCode . "' AND o.lngOrderNo = s.lngOrderNo "
		. "AND s.bytInvalidFlag = FALSE "
		. "AND s.lngRevisionNo >= 0 "
		. "AND s.lngRevisionNo = ( "
		. "SELECT MAX( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.strStockCode = s.strStockCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s3.bytInvalidFlag = false AND s3.strStockCode = s.strStockCode ) ";

	// ������ $lngStockNo �����ꤵ��Ƥ����礽�λ����ֹ�Υǡ������оݳ��Ȥ���
	if ( $lngStockNo != "" )
	{
		$strQuery = $strQuery 
			. "AND lngStockNo <> " . $lngStockNo . " ";
	}

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// �����ǡ�����¸�ߤ�����
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			// ���پ�����������
			$strStockDetailQuery = "SELECT sd.lngStockDetailNo as lngOrderDetailNo, "	// ���ٹ��ֹ�
				. "sd.strProductCode as strProductCode, "								// ���ʥ�����
				. "sd.lngStockSubjectCode as lngStockSubjectCode, "						// �������ܥ�����
				. "sd.lngStockItemCode as lngStockItemCode, "							// �������ʥ�����
				. "sd.dtmDeliveryDate as dtmDeliveryDate, "								// Ǽ����
				. "sd.lngDeliveryMethodCode as lngCarrierCode, "						// ������ˡ������
				. "sd.lngConversionClassCode as lngConversionClassCode, "				// ������ʬ������
				. "sd.curProductPrice as curProductPrice, "								// ���ʲ���
				. "sd.lngProductQuantity as lngProductQuantity, "						// ���ʿ���
				. "sd.lngProductUnitCode as lngProductUnitCode, "						// ����ñ�̥�����
				. "sd.lngTaxClassCode as lngTaxClassCode, "								// �����Ƕ�ʬ������
				. "sd.lngTaxCode as lngTaxCode, "										// �����ǥ�����
				. "sd.curTaxPrice as curTaxPrice, "										// �����Ƕ��
				. "sd.curSubTotalPrice as curSubTotalPrice, "							// ��ȴ���
				. "sd.strNote as strDetailNote, "										// ����
				. "sd.strMoldNo as strSerialNo, "										// �ⷿ�ֹ�
				. "p.lngCartonQuantity as lngCartonQuantity "							// ���ʤΥ����ȥ�����(�����͡�
				. "FROM t_StockDetail sd, m_Product p "
				. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND sd.strProductCode = p.strProductCode "
				. "ORDER BY lngSortKey ASC";

			list ( $lngStockDetailResultID, $lngStockDetailResultNum ) = fncQuery( $strStockDetailQuery, $objDB );

			if ( $lngStockDetailResultNum )
			{
				for ( $j = 0; $j < $lngStockDetailResultNum; $j++ )
				{
					$aryStockDetailResult[$i][] = $objDB->fetchArray( $lngStockDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngStockDetailResultID );
		}
	}
	else
	{
		// ������¸�ߤ��ʤ�ȯ��ˤĤ��ƤϤ��Τޤ�ȯ��ĤȤ�������
		for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
		{
			$aryRemainsDetail[$i]["lngorderdetailno"] 		= $aryOrderDetailResult[$i]["lngorderdetailno"];			// ���ٹ��ֹ�
			$aryRemainsDetail[$i]["strproductcode"] 		= $aryOrderDetailResult[$i]["strproductcode"];				// ���ʥ�����
			$aryRemainsDetail[$i]["lngstocksubjectcode"] 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// �������ܥ�����
			$aryRemainsDetail[$i]["lngstockitemcode"] 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// �������ʥ�����
			$aryRemainsDetail[$i]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryOrderDetailResult[$i]["dtmdeliverydate"]);									// Ǽ�����ʼ���ʸ�����ִ���
			$aryRemainsDetail[$i]["lngcarriercode"] 		= $aryOrderDetailResult[$i]["lngcarriercode"];				// ������ˡ������
			$aryRemainsDetail[$i]["lngconversionclasscode"] = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// ������ʬ������
			$aryRemainsDetail[$i]["curproductprice"]		= $aryOrderDetailResult[$i]["curproductprice"];				// ����ñ���ʲٻѿ��̡�
			$aryRemainsDetail[$i]["lngproductquantity"]		= $aryOrderDetailResult[$i]["lngproductquantity"];			// ���ʿ��̡ʲٻѿ��̡�
			$aryRemainsDetail[$i]["lngproductunitcode"]		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// ����ñ�̡ʲٻ�ñ�̡�
// 2004.03.30 suzukaze update start
			// ������ȯ�����Ƥ��ʤ����ϡ��Ǵ�Ϣ���ͤ�ȯ����̲ߤ򻲹ͤ˥ǥե�����ͤ���������
			if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// �̲ߤ��ߤξ����Ƕ�ʬ���Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// �����Ƕ�ʬ�����ɡʳ��ǡ�
			}
			else
			{
				// �̲ߤ��߰ʳ��ξ����Ƕ�ʬ������Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;										// �����Ƕ�ʬ�����ɡ�����ǡ�
			}
// 2004.03.30 suzukaze update end
			$aryRemainsDetail[$i]["lngtaxcode"]				= $aryOrderDetailResult[$i]["lngtaxcode"];					// �����ǥ�����
			$aryRemainsDetail[$i]["curtaxprice"]			= $aryOrderDetailResult[$i]["curtaxprice"];					// �����Ƕ��
			$aryRemainsDetail[$i]["cursubtotalprice"]		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// ��ȴ���
			$aryRemainsDetail[$i]["strdetailnote"]			= $aryOrderDetailResult[$i]["strdetailnote"];				// ����
			$aryRemainsDetail[$i]["strserialno"]			= $aryOrderDetailResult[$i]["strserialno"];					// �ⷿ�ֹ�
			$aryRemainsDetail[$i]["lngcartonquantity"]		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// �����ȥ�����
		}
		$objDB->freeResult( $lngResultID );
		return $aryRemainsDetail;
	}

	$objDB->freeResult( $lngResultID );

	$count = 0;		// �Ĥ����Ĥ��ä��Կ�������

	// ���ȸ�ȯ���������˼������������ˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
	for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
	{
		$lngOrderDetailNo 		= $aryOrderDetailResult[$i]["lngorderdetailno"];			// ���ٹ��ֹ�

		$strProductCode 		= $aryOrderDetailResult[$i]["strproductcode"];				// ���ʥ�����
		$lngStockSubjectCode 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// �������ܥ�����
		$lngStockItemCode 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// �������ʥ�����
		$lngConversionClassCode = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// ������ʬ������
		$curProductPrice		= $aryOrderDetailResult[$i]["curproductprice"];				// ����ñ���ʲٻ�ñ����
		$lngProductQuantity		= $aryOrderDetailResult[$i]["lngproductquantity"];			// ���ʿ��̡ʲٻѿ��̡�
		$lngProductUnitCode		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// ����ñ�̡ʲٻ�ñ�̡�
		$curSubTotalPrice		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// ��ȴ���
		$lngCartonQuantity		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// �����ȥ�����

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

// 2004.04.16 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
			// ��ȴ��ۤ�׻�����
			// ��ȴ��ۤ����ʿ��� * ���ʲ���
			$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
			// ü��������Ԥ�
			$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $OrderlngDigitNumber );
// 2004.04.16 suzukaze update end

			// ñ�̤�����ñ��
			$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

			// ������ʬ�����ɤ�����ñ�̤˽���
			$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
		}

		$bytEndFlag = 0;
		$lngStockProductQuantity = 0;
		$curStockSubTotalPrice = 0;

		for ( $j = 0; $j < count($aryStockResult); $j++ )
		{
			$StocklngMonetaryUnitCode = $aryStockResult[$j]["lngmonetaryunitcode"];

// 2004.04.16 suzukaze update start
			// ���������̲�ñ�̥����ɤ������оݷ��������
			if ( $StocklngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				$StocklngDigitNumber = 0;		// ���ܱߤξ��ϣ���
			}
			else
			{
				$StocklngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
			}
// 2004.04.16 suzukaze update end

			for ( $k = 0; $k < count($aryStockDetailResult[$j]); $k++ )
			{
// 2004.03.30 suzukaze update start
				// ȯ�����ٹ��ֹ���Ф��ƻ������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
				if ( $lngOrderDetailNo == $aryStockDetailResult[$j][$k]["lngorderdetailno"] 
					and $strProductCode == $aryStockDetailResult[$j][$k]["strproductcode"] 
//					and $lngStockSubjectCode == $aryStockDetailResult[$j][$k]["lngstocksubjectcode"] 
//					and $lngStockItemCode == $aryStockDetailResult[$j][$k]["lngstockitemcode"] 
					and $OrderlngMonetaryUnitCode == $StocklngMonetaryUnitCode )
// 2004.03.30 suzukaze update end
				{
// 2004.03.30 suzukaze update start
					// ���������Ƕ�ʬ�򵭲�����
					$lngTaxClassCode = $aryStockDetailResult[$j][$k]["lngtaxclasscode"];
// 2004.03.30 suzukaze update end

					// ������ʬ���ٻѷ׾�Ǥ��ä����ϡ�����ñ�̷׾���ѹ�����
					if ( $aryStockDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
					{
						// 0 ����к�
						if ( $aryStockDetailResult[$j][$k]["lngcartonquantity"] == 0 or $aryStockDetailResult[$j][$k]["lngcartonquantity"] == "" )
						{
							// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
							$aryStockDetailResult[$j][$k]["lngcartonquantity"] = 1;
						}

						// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
						$aryStockDetailResult[$j][$k]["lngproductquantity"] 
							= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["lngcartonquantity"];

						// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
						$aryStockDetailResult[$j][$k]["curproductprice"] 
							= $aryStockDetailResult[$j][$k]["curproductprice"] / $aryStockDetailResult[$j][$k]["lngcartonquantity"];

						// ��ȴ��ۤ����ʿ��� * ���ʲ���
						$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
							= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["curproductprice"];
// 2004.04.16 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
						// ü��������Ԥ�
						$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
							= fncCalcDigit( $aryStockDetailResult[$j][$k]["cursubtotalprice"], $lngCalcCode, $StocklngDigitNumber );
// 2004.04.16 suzukaze update end

						// ñ�̤�����ñ��
						$aryStockDetailResult[$j][$k]["lngproductunitcode"] = DEF_PRODUCTUNIT_PCS;

						// ������ʬ�����ɤ�����ñ�̤˽���
						$aryStockDetailResult[$j][$k]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;
					}

					// �������
					if ( $lngProductQuantity > $aryStockDetailResult[$j][$k]["lngproductquantity"] )
					{
						$lngStockProductQuantity += $aryStockDetailResult[$j][$k]["lngproductquantity"];
						// ʣ����������ι绻�Ǥο������
						if ( $lngProductQuantity <= $lngStockProductQuantity )
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
					if ( $curSubTotalPrice > $aryStockDetailResult[$j]["cursubtotalprice"] )
					{
						$curStockSubTotalPrice += $aryStockDetailResult[$j]["cursubtotalprice"];
						// ʣ����������ι绻�Ǥ���ȴ������
						if ( $curSubTotalPrice <= $curStockSubTotalPrice )
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

					// Ʊ�����ٹԤξ���ȯ��Ȼ����Ǹ��Ĥ��ä��ݤˤϡ�Ǽ����פȤʤ뤿��ʲ�����
					$bytEndFlag = 1;
				}
			}
			// �������٤�ȯ�����٤�Ʊ���Ƥ����Ĥ��ä����ϡ�for ʸȴ��
			if ( $bytEndFlag == 99 )
			{
				break;
			}
		}

		// ȯ�����ٹ���λ������ٹԤ����Ĥ��ä����֤򵭲�
		$aryStatus[] = $bytEndFlag;
		// ȯ����Ф�����������Ĥ���ʤ��ä����
		if ( $bytEndFlag == 0 )
		{
			$aryRemainsDetail[$count]["lngorderdetailno"] 		= $lngOrderDetailNo;										// ���ٹ��ֹ�
			$aryRemainsDetail[$count]["strproductcode"] 		= $strProductCode;											// ���ʥ�����
			$aryRemainsDetail[$count]["lngstocksubjectcode"] 	= $lngStockSubjectCode;										// �������ܥ�����
			$aryRemainsDetail[$count]["lngstockitemcode"] 		= $lngStockItemCode;										// �������ʥ�����
			$aryRemainsDetail[$count]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryOrderDetailResult[$i]["dtmdeliverydate"]);									// Ǽ�����ʼ���ʸ�����ִ���
			$aryRemainsDetail[$count]["lngcarriercode"] 		= $aryOrderDetailResult[$i]["lngcarriercode"];				// ������ˡ������
			$aryRemainsDetail[$count]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// ������ʬ������
			$aryRemainsDetail[$count]["curproductprice"] 		= $curProductPrice;											// ����ñ��
			$aryRemainsDetail[$count]["lngproductquantity"] 	= $lngProductQuantity;										// ���ʿ���
			$aryRemainsDetail[$count]["lngproductunitcode"] 	= $lngProductUnitCode;										// ����ñ��
												// �ʤ����ǤϲٻѤǤ��äƤ⤽�Τޤޤ��ͤ����ꤵ��Ƥ����ΤȤ����
// 2004.03.30 suzukaze update start
			// ������ȯ�����Ƥ��ʤ����ϡ��Ǵ�Ϣ���ͤ�ȯ����̲ߤ򻲹ͤ˥ǥե�����ͤ���������
			if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// �̲ߤ��ߤξ����Ƕ�ʬ���Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$count]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;								// �����Ƕ�ʬ�����ɡʳ��ǡ�
			}
			else
			{
				// �̲ߤ��߰ʳ��ξ����Ƕ�ʬ������Ǥ˥ǥե�������ꤹ��
				$aryRemainsDetail[$count]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;								// �����Ƕ�ʬ�����ɡ�����ǡ�
			}
// 2004.03.30 suzukaze update end
//			$aryRemainsDetail[$count]["lngtaxclasscode"]		= $aryOrderDetailResult[$i]["lngtaxclasscode"];				// �����Ƕ�ʬ������
			$aryRemainsDetail[$count]["lngtaxcode"]				= $aryOrderDetailResult[$i]["lngtaxcode"];					// �����ǥ�����
			$aryRemainsDetail[$count]["curtaxprice"]			= $aryOrderDetailResult[$i]["curtaxprice"];					// �����Ƕ��
			$aryRemainsDetail[$count]["cursubtotalprice"] 		= $curSubTotalPrice;										// ��ȴ���
			$aryRemainsDetail[$count]["strdetailnote"]			= $aryOrderDetailResult[$i]["strdetailnote"];				// ����
			$aryRemainsDetail[$count]["strserialno"]			= $aryOrderDetailResult[$i]["strserialno"];					// �ⷿ�ֹ�
			$aryRemainsDetail[$count]["lngcartonquantity"] 		= $lngCartonQuantity;										// �����ȥ�����

			$count++;	// �Ĥ����Ĥ��ä��Կ������󥿤򥫥���ȥ��å�
		}
		// ȯ����Ф��������¸�ߤ����ޤ���Ǽ���֤ˤʤ����
		else if ( $bytEndFlag == 1 )
		{
			$aryRemainsDetail[$count]["lngorderdetailno"] 		= $lngOrderDetailNo;										// ���ٹ��ֹ�
			$aryRemainsDetail[$count]["strproductcode"] 		= $strProductCode;											// ���ʥ�����
			$aryRemainsDetail[$count]["lngstocksubjectcode"] 	= $lngStockSubjectCode;										// �������ܥ�����
			$aryRemainsDetail[$count]["lngstockitemcode"] 		= $lngStockItemCode;										// �������ʥ�����
			$aryRemainsDetail[$count]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryOrderDetailResult[$i]["dtmdeliverydate"]);									// Ǽ�����ʼ���ʸ�����ִ���
			$aryRemainsDetail[$count]["lngcarriercode"] 		= $aryOrderDetailResult[$i]["lngcarriercode"];				// ������ˡ������
			$aryRemainsDetail[$count]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// ������ʬ������
			
			$aryRemainsDetail[$count]["curproductprice"] 		= $curProductPrice;											// ����ñ��
			// ���ʿ��̤�ȯ����̡��ݡ����������
			$aryRemainsDetail[$count]["lngproductquantity"] 	= $lngProductQuantity - $lngStockProductQuantity;			// ���ʿ���
			$aryRemainsDetail[$count]["lngproductunitcode"] 	= $lngProductUnitCode;										// ����ñ��
												// �ʤ����ǤϲٻѤǤ��äƤ⤽�Τޤޤ��ͤ����ꤵ��Ƥ����ΤȤ����
// 2004.03.30 suzukaze update start
			$aryRemainsDetail[$count]["lngtaxclasscode"]		= $lngTaxClassCode;											// �����Ƕ�ʬ������
// 2004.03.30 suzukaze update end
			$aryRemainsDetail[$count]["lngtaxcode"]				= $aryOrderDetailResult[$i]["lngtaxcode"];					// �����ǥ�����
			$aryRemainsDetail[$count]["curtaxprice"]			= $aryOrderDetailResult[$i]["curtaxprice"];					// �����Ƕ��
			// ��ȴ����ۤ�ȯ���ۡݻ�������
			$aryRemainsDetail[$count]["cursubtotalprice"] 		= $curSubTotalPrice - $lngStockSubTotalPrice;				// ��ȴ���
			$aryRemainsDetail[$count]["strdetailnote"]			= $aryOrderDetailResult[$i]["strdetailnote"];				// ����
			$aryRemainsDetail[$count]["strserialno"]			= $aryOrderDetailResult[$i]["strserialno"];					// �ⷿ�ֹ�
			$aryRemainsDetail[$count]["lngcartonquantity"] 		= $lngCartonQuantity;										// �����ȥ�����

			$count++;	// �Ĥ����Ĥ��ä��Կ������󥿤򥫥���ȥ��å�
		}
	}

	return $aryRemainsDetail;
}






/**
* �����ȯ��Ŀ��ǡ������ȯ��ˤƻ��ꤵ��Ƥ����׾�ñ�̤˹�碌�����
*
*	ȯ��ĥǡ������ȯ��κݤ˻��ꤵ�줿�׾�ñ�̤˽����������
*	ȯ��Ĥ��������
*
*	@param  array 		$aryStockRemains 		ȯ���
*	@param  array 		$aryOrderDetail 		ȯ������
*	@param	Integer		$lngMonetaryUnitCode	ȯ������̲�ñ�̥�����
*	@param	Integer		$lngCalcCode			ü������������
*	@param	Date		$dtmAppropriationDate	�����׾���
*	@param  Object		$objDB					DB���֥�������
*	@return Boolean 	$aryStockRemains_New	�¹�����
*						1						�¹Լ��� �����������
*	@access public
*/
function fncSetConversionStockRemains ( $aryStockRemains, $aryOrderDetail, $lngMonetaryUnitCode, $lngCalcCode, $dtmAppropriationDate, $objDB )
{
// 2004.04.12 suzukaze update start
	if ( !is_array($aryOrderDetail) )
	{
		return 1;
	}

	// ȯ������̲�ñ�̥����ɤ������оݷ��������
	if ( $lngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}
// 2004.04.12 suzukaze update end

	if ( is_array($aryStockRemains) )
	{
		// ���ȸ�ȯ���������˼�������ȯ��ĤˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
		for ( $i = 0; $i < count($aryStockRemains); $i++ )
		{
			// ȯ��Ĥ����پ�����ѿ�������
			$lngOrderDetailNo 		= $aryStockRemains[$i]["lngorderdetailno"];			// ���ٹ��ֹ�
			$strProductCode 		= $aryStockRemains[$i]["strproductcode"];			// ���ʥ�����
			$lngStockSubjectCode 	= $aryStockRemains[$i]["lngstocksubjectcode"];		// �������ܥ�����
			$lngStockItemCode 		= $aryStockRemains[$i]["lngstockitemcode"];			// �������ʥ�����
			$lngConversionClassCode = $aryStockRemains[$i]["lngconversionclasscode"];	// ������ʬ������
			$curProductPrice		= $aryStockRemains[$i]["curproductprice"];			// ����ñ���ʲٻ�ñ����
			$lngProductQuantity		= $aryStockRemains[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
			$lngProductUnitCode		= $aryStockRemains[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
			$curSubTotalPrice		= $aryStockRemains[$i]["cursubtotalprice"];			// ��ȴ���
			$lngCartonQuantity		= $aryStockRemains[$i]["lngcartonquantity"];		// �����ȥ�����
// 2004.04.16 suzukaze update start
			$lngTaxClassCode		= $aryStockRemains[$i]["lngtaxclasscode"];			// �Ƕ�ʬ������
			if ( $lngTaxClassCode == "" )
			{
				$lngTaxClassCode = 0;
			}
			$curTaxPrice			= $aryStockRemains[$i]["curtaxprice"];				// �ǳ�
			if ( $curTaxPrice == "" )
			{
				$curTaxPrice = 0;
			}
// 2004.04.16 suzukaze update end

			for ( $j = 0; $j < count($aryOrderDetail); $j++ )
			{
	// 2004.03.30 suzukaze update start
				// ���ٹԤ��Ф���Ʊ�����Ƥ����٤�ȯ��ĤΥǡ����˸��Ĥ��ä����
				if ( $aryOrderDetail[$j]["lngorderdetailno"] == $lngOrderDetailNo 
					and $aryOrderDetail[$j]["strproductcode"] == $strProductCode )
	//				and $aryOrderDetail[$j]["lngstocksubjectcode"] == $lngStockSubjectCode 
	//				and $aryOrderDetail[$j]["lngstockitemcode"] == $lngStockItemCode )
	// 2004.03.30 suzukaze update end
				{
					// ȯ��Ĥη׾�ñ�̤�ȯ��η׾�ñ�̤��㤦���ޤ���ȯ��Ĥη׾�ñ�̤�����ñ�̷׾�Ǥ���
					if ( $aryOrderDetail[$j]["lngconversionclasscode"] != $lngConversionClassCode 
						and $lngConversionClassCode == DEF_CONVERSION_SEIHIN )
					{
						// 0 ����к�
						if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
						{
							// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
							$lngCartonQuantity = 1;
						}

						// ȯ��η׾�ñ�̤Ǥ���ٻ�ñ�̷׾���ͤ���
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
					else if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
					// ȯ��Ĥη׾�ñ�̤�ȯ��η׾�ñ�̤��㤦���ޤ���ȯ��Ĥη׾�ñ�̤ϲٻ�ñ�̷׾�Ǥ���
					{
						// �ٻѤǤ錄�äƤ���ݤˤϤ��λ����׾夵��Ƥ��ʤ��Τ�Ʊ���ʤΤǷ׻����ʤ�

						// ���κݤ�����ñ�̤ˤĤ��Ƥϥǥե���Ȥ� c/t �����ꤹ��
						$lngProductUnitCode = DEF_PRODUCTUNIT_CTN;

						// ������ʬ�ˤĤ��Ƥϲٻ�ñ�̷׾�Ȥ���
						$lngConversionClassCode = DEF_CONVERSION_NISUGATA;
					}
// 2004.04.09 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
					// ��ȴ��ۤ�׻�����
					// ��ȴ��ۤϿ��� * ñ��
					$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
					$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );
// 2004.04.09 suzukaze update end
// 2004.04.16 suzukaze update start
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
								// �ǿ�����Ψ������������ 20130531 add
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
// 2004.04.16 suzukaze update end

					// for ʸȴ��
					break;
				}
			}

			// �Ѵ����줿�ͤ����ꤹ��
			$aryStockRemains_New[$i]["lngorderdetailno"] 		= $lngOrderDetailNo;								// ���ٹ��ֹ�
			$aryStockRemains_New[$i]["strproductcode"]			= $strProductCode;									// ���ʥ�����
			$aryStockRemains_New[$i]["lngstocksubjectcode"]		= $lngStockSubjectCode;								// �������ܥ�����
			$aryStockRemains_New[$i]["lngstockitemcode"]		= $lngStockItemCode;								// �������ʥ�����
			$aryStockRemains_New[$i]["dtmdeliverydate"] 		
					= str_replace( "-", "/", $aryStockRemains[$i]["dtmdeliverydate"]);								// Ǽ�����ʼ���ʸ�����ִ���
			$aryStockRemains_New[$i]["lngcarriercode"] 			= $aryStockRemains[$i]["lngcarriercode"];			// ������ˡ������
			$aryStockRemains_New[$i]["lngconversionclasscode"]	= $lngConversionClassCode;							// ������ʬ������
			$aryStockRemains_New[$i]["curproductprice"]			= $curProductPrice;									// ����ñ���ʲٻ�ñ����
			$aryStockRemains_New[$i]["lngproductquantity"]		= $lngProductQuantity;								// ���ʿ��̡ʲٻѿ��̡�
			$aryStockRemains_New[$i]["lngproductunitcode"]		= $lngProductUnitCode;								// ����ñ�̡ʲٻ�ñ�̡�
			$aryStockRemains_New[$i]["lngtaxclasscode"]			= $aryStockRemains[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
			$aryStockRemains_New[$i]["lngtaxcode"]				= $aryStockRemains[$i]["lngtaxcode"];				// �����ǥ�����
			$aryStockRemains_New[$i]["curtaxprice"]				= $aryStockRemains[$i]["curtaxprice"];				// �����Ƕ��
			$aryStockRemains_New[$i]["cursubtotalprice"]		= $curSubTotalPrice;								// ��ȴ���
			$aryStockRemains_New[$i]["strdetailnote"]			= $aryStockRemains[$i]["strdetailnote"];			// ����
			$aryStockRemains_New[$i]["strserialno"]				= $aryStockRemains[$i]["strserialno"];				// �ⷿ�ֹ�
			$aryStockRemains_New[$i]["lngcartonquantity"]		= $lngCartonQuantity;								// �����ȥ�����

		}
	}

// 2004.04.12 suzukaze update start
	// ������ȯ�����Ƥ��ʤ����
	else
	{
		// ȯ��Ĥξ���������ü��������Ԥ�
		for ( $i = 0; $i < count($aryOrderDetail); $i++ )
		{
			// �Ѵ����줿�ͤ����ꤹ��
			$aryStockRemains_New[$i]["lngorderdetailno"] 		= $aryOrderDetail[$i]["lngorderdetailno"];			// ���ٹ��ֹ�
			$aryStockRemains_New[$i]["strproductcode"]			= $aryOrderDetail[$i]["strproductcode"];			// ���ʥ�����
			$aryStockRemains_New[$i]["lngstocksubjectcode"]		= $aryOrderDetail[$i]["lngstocksubjectcode"];		// �������ܥ�����
			$aryStockRemains_New[$i]["lngstockitemcode"]		= $aryOrderDetail[$i]["lngstockitemcode"];			// �������ʥ�����
			$aryStockRemains_New[$i]["dtmdeliverydate"] 		
					= str_replace( "-", "/", $aryStockRemains[$i]["dtmdeliverydate"]);								// Ǽ�����ʼ���ʸ�����ִ���
			$aryStockRemains_New[$i]["lngcarriercode"] 			= $aryOrderDetail[$i]["lngcarriercode"];			// ������ˡ������
			$aryStockRemains_New[$i]["lngconversionclasscode"]	= $aryOrderDetail[$i]["lngconversionclasscode"];	// ������ʬ������
			$aryStockRemains_New[$i]["curproductprice"]			= $aryOrderDetail[$i]["curproductprice"];			// ����ñ���ʲٻ�ñ����
			$aryStockRemains_New[$i]["lngproductquantity"]		= $aryOrderDetail[$i]["lngproductquantity"];		// ���ʿ��̡ʲٻѿ��̡�
			$aryStockRemains_New[$i]["lngproductunitcode"]		= $aryOrderDetail[$i]["lngproductunitcode"];		// ����ñ�̡ʲٻ�ñ�̡�
			$aryStockRemains_New[$i]["lngtaxclasscode"]			= $aryOrderDetail[$i]["lngtaxclasscode"];			// �����Ƕ�ʬ������
			$aryStockRemains_New[$i]["lngtaxcode"]				= $aryOrderDetail[$i]["lngtaxcode"];				// �����ǥ�����
			$aryStockRemains_New[$i]["curtaxprice"]				= $aryOrderDetail[$i]["curtaxprice"];				// �����Ƕ��
			// ��ȴ��ۤˤĤ��Ƥ�ü��������������ü��������Ԥ�
			$curSubTotalPrice = $aryOrderDetail[$i]["lngproductquantity"] * $aryOrderDetail[$i]["curproductprice"];
			$aryStockRemains_New[$i]["cursubtotalprice"]		
					= fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );								// ��ȴ���
			$aryStockRemains_New[$i]["cursubtotalprice"]		= $aryOrderDetail[$i]["cursubtotalprice"];			// ��ȴ���
			$aryStockRemains_New[$i]["strdetailnote"]			= $aryOrderDetail[$i]["strdetailnote"];				// ����
			$aryStockRemains_New[$i]["strserialno"]				= $aryOrderDetail[$i]["strserialno"];				// �ⷿ�ֹ�
			$aryStockRemains_New[$i]["lngcartonquantity"]		= $aryOrderDetail[$i]["lngcartonquantity"];			// �����ȥ�����
		}
	}
// 2004.04.12 suzukaze update end

	return $aryStockRemains_New;
}






/**
* �����ȯ��ǡ����˴ؤ��ơ�����ȯ��ǡ����λ������֤��Ŀ������ؿ�
*
*	ȯ�����ȯ���ۤ�ꤽ��ȯ��No����ꤷ�Ƥ���������٤Ƥ���
*	ȯ��Ĥ��������
*
*	@param  Integer 	$lngOrderNo 				ȯ���ֹ�
*	@param	Array		$aryStockDetail				������Ͽ�ˤ����ꤵ�줿���پ���
*	@param	Integer		$lngOrderMonetaryUnitCode	ȯ������̲�ñ�̥�����
*	@param	Integer		$lngStockMonetaryUnitCode	���������̲�ñ�̥�����
*	$param	Integer		$lngStockNo					�оݳ��Ȥ������No���ʻ������������ѡ�
*	@param	Integer		$lngCalcCode				ü������������
*	@param  Object		$objDB						DB���֥�������
*	@return Boolean 	0							�¹�����
*						1							�¹Լ��� �����������
*						50							�¹����������٤����Ƥ�ȯ��Ĥ�Ķ�������Ϥʤ�
*						99							ȯ��İʾ�˻��ꤵ��Ƥ���
*	@access public
*
*	��������
*	2004.04.16	fncGetStockRemains �ؿ��ΰ����ѹ���ȼ������
*	2004.04.19	aryStockDetail �����Key����̾��ʸ������ʸ���˽���
*	2004.04.20	ȯ��Ĥ��᤿�塢ȯ��Ĥ�ñ�̷׾夬�ٻѤǤ��ä�������Ӥ����������ʤ�Х��ν���
*/
function fncGetStatusStockRemains ( $lngOrderNo, $aryStockDetail, $lngOrderMonetaryUnitCode, $lngStockMonetaryUnitCode, 
									$lngStockNo, $lngCalcCode, $objDB )
{
// 2004.04.16 suzukaze update start
	// ȯ��Ĥ����ؿ��θƤӽФ�
	$aryRemainsDetail = fncGetStockRemains ( $lngOrderNo, $lngStockNo, $lngCalcCode, $objDB );
// 2004.04.16 suzukaze update end

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

// 2004.04.16 suzukaze update start
	// ȯ������̲�ñ�̥����ɤ������оݷ��������
	if ( $lngOrderMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}
// 2004.04.16 suzukaze update end

	// ȯ��Ĥ�¸�ߤ����
	// ������ꤵ�줿������Ĵ������ȯ��Ĥ�����å�����ȯ��İʾ����ʸ���Ƥ��ʤ����ɤ�����Ĵ������
	for ( $i = 0; $i < count($aryRemainsDetail); $i++ )
	{
		$lngOrderDetailNo 		= $aryRemainsDetail[$i]["lngorderdetailno"];			// ���ٹ��ֹ�

		$strProductCode 		= $aryRemainsDetail[$i]["strproductcode"];				// ���ʥ�����
		$lngStockSubjectCode 	= $aryRemainsDetail[$i]["lngstocksubjectcode"];			// �������ܥ�����
		$lngStockItemCode 		= $aryRemainsDetail[$i]["lngstockitemcode"];			// �������ʥ�����
		$lngConversionClassCode = $aryRemainsDetail[$i]["lngconversionclasscode"];		// ������ʬ������
		$curProductPrice		= $aryRemainsDetail[$i]["curproductprice"];				// ����ñ���ʲٻ�ñ����
		$lngProductQuantity		= $aryRemainsDetail[$i]["lngproductquantity"];			// ���ʿ��̡ʲٻѿ��̡�
		$lngProductUnitCode		= $aryRemainsDetail[$i]["lngproductunitcode"];			// ����ñ�̡ʲٻ�ñ�̡�
		$curSubTotalPrice		= $aryRemainsDetail[$i]["cursubtotalprice"];			// ��ȴ���
		$lngCartonQuantity		= $aryRemainsDetail[$i]["lngcartonquantity"];			// �����ȥ�����

// 2004.04.20 suzukaze update start
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
// 2004.04.20 suzukaze update end

		for ( $j = 0; $j < count($aryStockDetail); $j++ )
		{
// 2004.03.30 suzukaze update start
			// ȯ������ٹ��ֹ���Ф��ƻ������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
			if ( $lngOrderDetailNo == $aryStockDetail[$j]["lngOrderDetailNo"] 
				and $strProductCode == $aryStockDetail[$j]["strProductCode"] 
//				and $lngStockSubjectCode == $aryStockDetail[$j]["strStockSubjectCode"] 
//				and $lngStockItemCode == $aryStockDetail[$j]["strStockItemCode"] 
				and $lngOrderMonetaryUnitCode == $lngStockMonetaryUnitCode )
// 2004.03.30 suzukaze update end
			{
				// ������ʬ���ٻѷ׾�Ǥ��ä����ϡ�����ñ�̷׾���ѹ�����
				if ( $aryStockDetail[$j]["lngConversionClassCode"] != "gs" )
				{
					// ����λ�������ˤϥ����ȥ�������ξ������äƤ��ʤ��Τ�
					// �����Ǥ����ʥ����ɤ�Ʊ���Ȥ������Ȥ���ȯ��ĤΥ����ȥ����������Ѥ���
					// 0 ����к�
					if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
					{
						// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
						$lngCartonQuantity = 1;
					}

// 2004.04.19 suzukaze update start
					// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
					$aryStockDetail[$j]["lngGoodsQuantity"] 
						= $aryStockDetail[$j]["lngGoodsQuantity"] * $lngCartonQuantity;

					// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
					$aryStockDetail[$j]["curProductPrice"] 
						= $aryStockDetail[$j]["curProductPrice"] / $lngCartonQuantity;

					// ��ȴ��ۤ����ʿ��� * ���ʲ���
					$aryStockDetail[$j]["curSubTotalPrice"] 
						= $aryStockDetail[$j]["lngGoodsQuantity"] * $aryStockDetail[$j]["curProductPrice"];

// 2004.04.16 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
					$aryStockDetail[$j]["curSubTotalPrice"] 
						= fncCalcDigit( $aryStockDetail[$j]["curSubTotalPrice"], $lngCalcCode, $lngDigitNumber );
// 2004.04.16 suzukaze update end

					// ñ�̤�����ñ��
					$aryStockDetail[$j]["lngProductUnitCode"] = DEF_PRODUCTUNIT_PCS;

					// ������ʬ�����ɤ�����ñ�̤˽���
					$aryStockDetail[$j]["lngConversionClassCode"] = DEF_CONVERSION_SEIHIN;
				}

				// �������
				if ( $lngProductQuantity < $aryStockDetail[$j]["lngGoodsQuantity"] )
				{
					// ���̤�ȯ��Ŀ��ʾ�
					return 99;
				}
// 2004.04.19 suzukaze update end

				// ��ȴ������
				if ( $curSubTotalPrice < $aryStockDetailResult[$j]["curSubTotalPrice"] )
				{
					// ��ȴ��ۤ�ȯ��İʾ�
					return 99;
				}

				// ȯ��Ĥ�Ʊ�����پ��󤬸��Ĥ��ä����ϼ��ιԤ����
				break;
			}
		}
	}

	return 50;	// �¹�����������λ�����ȯ��Ĥ�ۤ������Ϥʤ�
}






// 2004.03.09 suzukaze update start
/**
* ����λ����ǡ�������Ͽ�˴ؤ��ơ����λ����ǡ�������Ͽ���뤳�ȤǤξ����ѹ��ؿ�
*
*	�����ξ��֤���Ǽ�ʺѡפξ�硢ȯ��No����ꤷ�Ƥ�����硢ʬǼ�Ǥ��ä����ʤ�
*	�ƾ��֤��Ȥˤ��λ����˴ؤ���ǡ����ξ��֤��ѹ�����
*
*	@param  Integer 	$lngOrderNo 	���������Ȥ��Ƥ���ȯ��No
*	@param	Integer		$lngCalcCode	ü������������
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*
*	��������
*	2004.04.16	ü�����������ɤ��ɲ�
*/
function fncStockSetStatus ( $lngOrderNo, $lngCalcCode, $objDB )
{
	// ȯ���ֹ椬¸�ߤ��ʤ���礽�Τޤ޽�λ
	if ( $lngOrderNo == "" or $lngOrderNo == 0 )
	{
		return 1;
	}

	// �ǿ���ȯ��Υǡ������������
	$strQuery = "SELECT o.lngOrderNo as lngOrderNo, o.strOrderCode as strOrderCode, "
		. "o.lngOrderStatusCode as lngOrderStatusCode, o.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Order o "
		. "WHERE o.strOrderCode = ( "
		. "SELECT o1.strOrderCode FROM m_Order o1 WHERE o1.lngOrderNo = " . $lngOrderNo . " ) "
		. "AND o.bytInvalidFlag = FALSE "
		. "AND o.lngRevisionNo >= 0 "
		. "AND o.lngRevisionNo = ( "
		. "SELECT MAX( o2.lngRevisionNo ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ) ";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
		$strNewOrderCode = $objResult->strordercode;
		$lngNewOrderStatusCode = $objResult->lngorderstatuscode;
		$OrderlngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// ȯ��No�ϻ��ꤷ�Ƥ��뤬����ͭ���ʺǿ�ȯ��¸�ߤ��ʤ����Ϥ��Τޤ޽�λ
		return 1;
	}
	$objDB->freeResult( $lngResultID );

// 2004.04.16 suzukaze update start
	// ȯ������̲�ñ�̥����ɤ������оݷ��������
	if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}
// 2004.04.16 suzukaze update end

	// �ǿ�ȯ������پ�����������
	$strQuery = "SELECT od.lngOrderDetailNo as lngOrderDetailNo, od.strProductCode as strProductCode, "
		. "od.lngStockSubjectCode as lngStockSubjectCode, od.lngStockItemCode as lngStockItemCode, "
		. "od.lngConversionClassCode as lngConversionClassCode, od.curProductPrice as curProductPrice, "
		. "od.lngProductQuantity as lngProductQuantity, od.lngProductUnitCode as lngProductUnitCode, "
		. "od.curSubTotalPrice as curSubTotalPrice, p.lngCartonQuantity as lngCartonQuantity "
		. "FROM t_OrderDetail od, m_Product p "
		. "WHERE od.lngOrderNo = " . $lngNewOrderNo . " AND od.strProductCode = p.strProductCode "
		. "ORDER BY lngSortKey ASC";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
		return 2;
	}
	$objDB->freeResult( $lngResultID );

	// Ʊ��ȯ��No����ꤷ�Ƥ���ǿ������򸡺�
	$strQuery = "SELECT s.lngStockNo as lngStockNo, s.lngStockStatusCode as lngStockStatusCode, "
		. "s.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Stock s, m_Order o "
		. "WHERE o.strOrderCode = '" . $strNewOrderCode . "' AND o.lngOrderNo = s.lngOrderNo "
		. "AND s.bytInvalidFlag = FALSE "
		. "AND s.lngRevisionNo >= 0 "
		. "AND s.lngRevisionNo = ( "
		. "SELECT MAX( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.strStockCode = s.strStockCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s3.bytInvalidFlag = false AND s3.strStockCode = s.strStockCode ) ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// �����ǡ�����¸�ߤ�����
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			// ���پ�����������
			$strStockDetailQuery = "SELECT sd.lngStockDetailNo as lngOrderDetailNo, sd.strProductCode as strProductCode, "
				. "sd.lngStockSubjectCode as lngStockSubjectCode, sd.lngStockItemCode as lngStockItemCode, "
				. "sd.lngConversionClassCode as lngConversionClassCode, sd.curProductPrice as curProductPrice, "
				. "sd.lngProductQuantity as lngProductQuantity, sd.lngProductUnitCode as lngProductUnitCode, "
				. "sd.curSubTotalPrice as curSubTotalPrice, p.lngCartonQuantity as lngCartonQuantity "
				. "FROM t_StockDetail sd, m_Product p "
				. "WHERE sd.lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND sd.strProductCode = p.strProductCode "
				. "ORDER BY lngSortKey ASC";

			list ( $lngStockDetailResultID, $lngStockDetailResultNum ) = fncQuery( $strStockDetailQuery, $objDB );

			if ( $lngStockDetailResultNum )
			{
				for ( $j = 0; $j < $lngStockDetailResultNum; $j++ )
				{
					$aryStockDetailResult[$i][] = $objDB->fetchArray( $lngStockDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngStockDetailResultID );
		}

		// ���ȸ�ȯ���������˼������������ˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
		for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
		{
			// ���ȸ�ȯ������ٹ��ֹ������������������ٹ��ֹ�ˤҤ�Ť��ƻ������ä����ߤ���뤿��
			$lngOrderDetailNo 		= $aryOrderDetailResult[$i]["lngorderdetailno"];				// ���ٹ��ֹ�

			$strProductCode 		= $aryOrderDetailResult[$i]["strproductcode"];				// ���ʥ�����
			$lngStockSubjectCode 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// �������ܥ�����
			$lngStockItemCode 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// �������ʥ�����
			$lngConversionClassCode = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// ������ʬ������
			$curProductPrice		= $aryOrderDetailResult[$i]["curproductprice"];				// ����ñ���ʲٻ�ñ����
			$lngProductQuantity		= $aryOrderDetailResult[$i]["lngproductquantity"];			// ���ʿ��̡ʲٻѿ��̡�
			$lngProductUnitCode		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// ����ñ�̡ʲٻ�ñ�̡�
			$curSubTotalPrice		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// ��ȴ���
			$lngCartonQuantity		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// �����ȥ�����

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
// 2004.04.09 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
				// ��ȴ��ۤ�׻�����
				// ��ȴ��ۤϿ��� * ñ��
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
				$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );
// 2004.04.09 suzukaze update end
			}

			$bytEndFlag = 0;
			$lngStockProductQuantity = 0;
			$curStockSubTotalPrice = 0;
			
			for ( $j = 0; $j < count($aryStockResult); $j++ )
			{
				$StocklngMonetaryUnitCode = $aryStockResult[$j]["lngmonetaryunitcode"];
// 2004.04.16 suzukaze update start
				// ���������̲�ñ�̥����ɤ������оݷ��������
				if ( $StocklngMonetaryUnitCode == DEF_MONETARY_YEN )
				{
					$StocklngDigitNumber = 0;		// ���ܱߤξ��ϣ���
				}
				else
				{
					$StocklngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
				}
// 2004.04.16 suzukaze update end

				for ( $k = 0; $k < count($aryStockDetailResult[$j]); $k++ )
				{
// 2004.03.30 suzukaze update start
					// ȯ�����ٹ��ֹ���Ф��ƻ������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
					// ����˲ä����̲ߤ�Ʊ�����
					if ( $lngOrderDetailNo == $aryStockDetailResult[$j][$k]["lngorderdetailno"] 
						and $strProductCode == $aryStockDetailResult[$j][$k]["strproductcode"] 
//						and $lngStockSubjectCode == $aryStockDetailResult[$j][$k]["lngstocksubjectcode"] 
//						and $lngStockItemCode == $aryStockDetailResult[$j][$k]["lngstockitemcode"] 
						and $OrderlngMonetaryUnitCode == $StocklngMonetaryUnitCode )
// 2004.03.30 suzukaze update end
					{
						// ������ʬ���ٻ�ñ�̷׾�ξ�硢����ñ���ط׻�
						if ( $aryStockDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
						{
							// 0 ����к�
							if ( $aryStockDetailResult[$j][$k]["lngcartonquantity"] == 0 or $aryStockDetailResult[$j][$k]["lngcartonquantity"] == "" )
							{
								// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
								$aryStockDetailResult[$j][$k]["lngcartonquantity"] = 1;
							}

							// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
							$aryStockDetailResult[$j][$k]["lngproductquantity"] 
								= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["lngcartonquantity"];

							// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
							$aryStockDetailResult[$j][$k]["curproductprice"] 
								= $aryStockDetailResult[$j][$k]["curproductprice"] / $aryStockDetailResult[$j][$k]["lngcartonquantity"];

							// ��ȴ��ۤϲٻ�ñ�� * �ٻѿ���
							$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
								= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["curproductprice"];

// 2004.04.16 suzukaze update start
// ��ȴ����ۤ�׻�����ݤ����ꤵ�줿ü��������Ԥ�
							// ü��������Ԥ�
							$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
								= fncCalcDigit( $aryStockDetailResult[$j][$k]["cursubtotalprice"], $lngCalcCode, $StocklngDigitNumber );
// 2004.04.16 suzukaze update end

						}

						// �������
						if ( $lngProductQuantity > $aryStockDetailResult[$j][$k]["lngproductquantity"] )
						{
							$lngStockProductQuantity += $aryStockDetailResult[$j][$k]["lngproductquantity"];
							// ʣ����������ι绻�Ǥο������
							if ( $lngProductQuantity <= $lngStockProductQuantity )
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
						if ( $curSubTotalPrice > $aryStockDetailResult[$j]["cursubtotalprice"] )
						{
							$curStockSubTotalPrice += $aryStockDetailResult[$j]["cursubtotalprice"];
							// ʣ����������ι绻�Ǥ���ȴ������
							if ( $curSubTotalPrice <= $curStockSubTotalPrice )
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

						// Ʊ�����ٹԤξ���ȯ��Ȼ����Ǹ��Ĥ��ä��ݤˤϡ�Ǽ����פȤʤ뤿��ʲ�����
						$bytEndFlag = 1;
					}
				}
				// �������٤�ȯ�����٤�Ʊ���Ƥ����Ĥ��ä����ϡ�for ʸȴ��
				if ( $bytEndFlag == 99 )
				{
					break;
				}
			}
			// ȯ�����ٹ���λ������ٹԤ����Ĥ��ä����֤򵭲�
			$aryStatus[] = $bytEndFlag;
		}
		
		// ���٥����å���$aryStatus�����٤��Ȥξ��֡ˤˤ��ȯ�����ΤȤ��Ƥξ��֤�Ƚ��
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

		// ȯ�����٤��Ф��ư��������ȯ�����Ƥ��ʤ���硢�ޤ��ϴ�Ǽ�ǤϤʤ����
		// ��flagZERO��ȯ�����ٿ����Ф��ƥ�������ξ��ºݤϽ�����֤Ǥ��뤬�������ˤ�
		//   ȯ��No�����ꤵ��Ƥ���ΤǤ����Ǥξ��֤ϡ�Ǽ����פȤ����
		if ( $flagALL != count($aryStatus) )
		{
			// ��������ȯ��ξ��֤ξ��֤��Ǽ����פȤ���
		
			// �����о�ȯ��ǡ������å�����
			$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";

			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// ��Ǽ����׾��֤ؤι�������
			$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_DELIVER . " WHERE lngOrderNo = " . $lngNewOrderNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// Ʊ��ȯ��NO����ꤷ�Ƥ�������ξ��֤��Ф��Ƥ��Ǽ����פȤ���
			for ( $i = 0; $i < count($aryStockResult); $i++ )
			{
				// �����оݻ����ǡ������å�����
				$strLockQuery = "SELECT lngStockNo FROM m_Stock " 
					. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";

				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// ��Ǽ����׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_DELIVER 
					. " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			
			return 0;
		}
		else
		// �о�ȯ��ϴ�Ǽ���֤Ǥ��ä���
		{
			// ��������ȯ��ξ��֤ξ��֤��Ǽ�ʺѡפȤ���
		
			// �����о�ȯ��ǡ������å�����
			$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// ��Ǽ�ʺѡ׾��֤ؤι�������
			$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_END . " WHERE lngOrderNo = " . $lngNewOrderNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// Ʊ��ȯ��NO����ꤷ�Ƥ�������ξ��֤��Ф��Ƥ��Ǽ�ʺѡפȤ���
			for ( $i = 0; $i < count($aryStockResult); $i++ )
			{
				// �����оݻ����ǡ������å�����
				$strLockQuery = "SELECT lngStockNo FROM m_Stock " 
					. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// ��Ǽ�ʺѡ׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_END 
					. " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			return 0;
		}
	}
	else
	{
		// �����ǡ�����¸�ߤ��ʤ����
		// �����λ��ȸ��ǿ�ȯ��ξ��֤��ȯ��פ��᤹
		
		// �����о�ȯ��ǡ������å�����
		$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if ( !$lngLockResultNum )
		{
			fncOutputError ( 9051, DEF_ERROR, "̵�����������顼", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngLockResultID );

		// ��ȯ��׾��֤ؤι�������
		$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_ORDER . " WHERE lngOrderNo = " . $lngNewOrderNo;

		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		$objDB->freeResult( $lngUpdateResultID );

		return 0;
	}

	$objDB->freeResult( $lngResultID );

	return 0;
}







?>