<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��Ͽ
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
*         ����Ͽ����
*         �����顼�����å�
*         ����Ͽ������λ�塢��Ͽ��λ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
	// require(SRC_ROOT."po/cmn/lib_po.php");
	// require(SRC_ROOT."po/cmn/lib_pop.php");
	require (SRC_ROOT."po/cmn/lib_por.php");

	//var_dump($_POST);
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"] = $_POST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	$objDB->open("", "", "", "");
	
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );



	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngInputUserCode = $objAuth->UserCode;
	
	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 500	ȯ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	// 501 ȯ�������ȯ����Ͽ��
	if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	// 508 ȯ������ʾ��ʥޥ��������쥯�Ƚ�����
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	// �����ǡ�������
	$aryUpdate["lngorderno"]           = $_POST["lngOrderNo"];
	$aryUpdate["lngrevisionno"]        = $_POST["lngRevisionNo"];
	$aryUpdate["dtmexpirationdate"]    = $_POST["dtmExpirationDate"];
	$aryUpdate["lngpayconditioncode"]  = $_POST["lngPayConditionCode"];
	$aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
	$aryUpdate["lngorderstatuscode"]   = 2;
	for($i = 0; $i < count($_POST["aryDetail"]); $i++){
		$aryUpdateDetail[$i]["lngorderdetailno"]       = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
		$aryUpdateDetail[$i]["lngsortkey"]             = $_POST["aryDetail"][$i]["lngSortKey"];
		$aryUpdateDetail[$i]["lngdeliverymethodcode"]  = $_POST["aryDetail"][$i]["lngDeliveryMethodCode"];
		$aryUpdateDetail[$i]["lngproductunitcode"]     = $_POST["aryDetail"][$i]["lngProductUnitCode"];
		$aryUpdateDetail[$i]["lngorderno"]             = $_POST["aryDetail"][$i]["lngOrderNo"];
		$aryUpdateDetail[$i]["lngrevisionno"]          = $_POST["aryDetail"][$i]["lngRevisionNo"];
		$aryUpdateDetail[$i]["lngstocksubjectcode"]    = $_POST["aryDetail"][$i]["lngStockSubjectCode"];
		$aryUpdateDetail[$i]["lngstockitemcode"]       = $_POST["aryDetail"][$i]["lngStockItemCode"];
		$aryUpdateDetail[$i]["lngmonetaryunitcode"]    = $_POST["aryDetail"][$i]["lngMonetaryUnitCode"];
		$aryUpdateDetail[$i]["lngcustomercompanycode"] = $_POST["aryDetail"][$i]["lngCustomerCompanyCode"];
	}
	
	$objDB->transactionBegin();
	// ȯ��ޥ�������
	if(!fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
	// ȯ�����ٹ���
	if(!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
	// ������ȯ��ޥ���/ȯ�����ټ���
	
	// $aryOrder = fncGetOrder($aryUpdate["lngorderno"], $objDB)[0];
	// $aryDetail = fncGetOrderDetail($aryUpdate["lngorderno"], $objDB);
	// ȯ���ޥ�������
	if(!fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB)){ return false; }

	// TODO:���Ȥǥ��ߥåȤ��ѹ�����
	$objDB->transactionRollback();
	echo fncGetReplacedHtml( "po/regist/parts2.tmpl", $aryData ,$objAuth);
	




	// ���ٹԤ����
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}
	
	//var_dump( $aryData );
	//exit();

	// displayCode��code���Ѵ�����
	// fncChangeData�ǿ������������
	$aryNewData = fncChangeData( $aryData, $objDB );
	
	
	// ���ٹԤ����������
	for($i=0; $i<count($_POST[aryPoDitail]); $i++ )
	{
		while( list( $strKeys, $strValues ) = each( $_POST[aryPoDitail][$i] ) )
		{
			if( $strKeys == "strProductCode")
			{
				$aryNewData["aryPoDitail"][$i][$strKeys] = $strValues; //fncDispalayToCode
			}
			else
			{
				$aryNewData["aryPoDitail"][$i][$strKeys] = ( $strValues == "" ) ? "null" : $strValues ;
			}
		}
	}


	// ����ե���å�����������ξ��
	$aryNewData["strWorkflowMessage"] = ( $aryNewData["strWorkflowMessage"] == "null" ) ? "" : $aryNewData["strWorkflowMessage"];



	// ���������ξ�硢�ʲ�������å�
	if ( $aryNewData["strProcMode"] == "renew")
	{
		// ��ǧ���̤�ɽ������ݤ˺ǿ���Х�����ȯ���ֿ�����פˤʤäƤ��ʤ����ɤ����γ�ǧ��Ԥ�
		$strCheckQuery = "SELECT lngOrderNo, lngOrderStatusCode FROM m_Order o WHERE o.strOrderCode = '" . $aryNewData["strOrderCode"] . "'";
		$strCheckQuery .= " AND o.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND o.lngRevisionNo = ( "
			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode )\n";
		$strCheckQuery .= " AND o.strReviseCode = ( "
			. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode )\n";

		// �����å������꡼�μ¹�
		list ( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngOrderNo         = $objResult->lngorderno;
			$lngorderstatuscode = $objResult->lngorderstatuscode;

			if ( $lngorderstatuscode == DEF_ORDER_APPLICATE )
			{
				fncOutputError ( 505, DEF_WARNING, "", TRUE, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		$objDB->freeResult( $lngCheckResultID );


		//-------------------------------------------------------------------------
		// �ƾ�ǧ�Τ��ᡢ���ơ�������ֿ�����פ��ѹ�
		//-------------------------------------------------------------------------
		if( $aryNewData["lngWorkflowOrderCode"] == 0 )
		{
			$lngorderstatuscode = DEF_ORDER_ORDER;

			// ���������å�
			$arySql = array();
			$arySql[] = "select count(*) as count";
			$arySql[] = "from";
			$arySql[] = "	m_stock ms";
			//--		left join t_stockdetail tsd on tsd.lngstockno = ms.lngstockno
			$arySql[] = "where";
			$arySql[] = "ms.lngorderno in ";
			$arySql[] = "(";
			$arySql[] = "	select mo1.lngorderno";
			$arySql[] = "	from";
			$arySql[] = "		m_order mo1";
			$arySql[] = "	where";
			$arySql[] = "		mo1.strordercode = '" . $aryNewData["strOrderCode"] . "'";
			$arySql[] = ")";
			$arySql[] = "and ms.bytinvalidflag = false";
			$arySql[] = "AND ms.lngRevisionNo = (";
			$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_stock s1 WHERE s1.bytInvalidFlag = false and s1.strStockCode = ms.strStockCode)";
			$arySql[] = "	AND 0 <= (";
			$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_stock s2 WHERE s2.bytInvalidFlag = false and s2.strStockCode = ms.strStockCode )";

			$strQuery = implode("\n", $arySql);
			// �ģ��䤤��碌
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum == 1 )
			{
				$objResult	= $objDB->fetchObject( $lngResultID, 0 );
				// ��İʾ塢�����ǡ���������С���Ǽ����פȤ���
				if( 1 <= (int)$objResult->count)
				{
					$lngorderstatuscode = DEF_ORDER_DELIVER;
				}
			}
		}

		// ������
		else
		{
			$lngorderstatuscode = DEF_ORDER_APPLICATE;
		}


		//-------------------------------------------------------------------------
		// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
		//-------------------------------------------------------------------------
		$lngorderstatuscode = fncCheckNullStatus( $lngorderstatuscode );

		//-------------------------------------------------------------------------
		// ���֥����ɤ���0�פξ�硢��1�פ������
		//-------------------------------------------------------------------------
		$lngorderstatuscode = fncCheckZeroStatus( $lngorderstatuscode );
	}


	// m_order�Υ������󥹤����
	$sequence_m_order = fncGetSequence( 'm_Order.lngOrderNo', $objDB );

	//�ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();
	// echo "�ȥ�󥶥������¹�<br>";
	
	//��Х��������ɤμ���
	// �������̤���ξ��

	if ( $aryNewData["strProcMode"] == "renew")
	{
		$strOrderCode = $aryNewData["strOrderCode"];

		// �����ξ��Ʊ��ȯ����Ф��ƥ�ӥ�����ֹ桢��Х��������ɤκ����ͤ��������
		/////   ��ӥ�����ֹ桢��Х��������ɤ򸽺ߤκ����ͤ�Ȥ�褦�˽������롡���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ��ȯ����Ф��ƥ�å����֤ˤ���

		$strLockQuery = "SELECT lngRevisionNo, strReviseCode FROM m_Order WHERE strOrderCode = '" . $strOrderCode . "' FOR UPDATE";

		// ��å������꡼�μ¹�
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

		$lngMaxRevision = 0;
		$strMaxRevise   = 0;
		if ( $lngLockResultNum )
		{
			for ( $i = 0; $i < $lngLockResultNum; $i++ )
			{
				$objResult = $objDB->fetchObject( $lngLockResultID, $i );
				if ( $lngMaxRevision < $objResult->lngrevisionno )
				{
					$lngMaxRevision = $objResult->lngrevisionno;
				}
				if ( $strMaxRevise < intval($objResult->strrevisecode) )
				{
					$strMaxRevise = intval($objResult->strrevisecode);
				}
			}
		}
		$objDB->freeResult( $lngLockResultID );
		$lngRevisionNo = $lngMaxRevision + 1;
		$strReviseCode = sprintf("%02d", $strMaxRevise + 1);
	}
	else
	{
		// ��Ͽ�ξ��
		$strReviseCode = "00";
		$lngRevisionNo = 0;
		$strOrderCode = fncGetDateSequence( date('Y', strtotime( $aryNewData["dtmOrderAppDate"] ) ), date('m',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_order.strOrderCode", $objDB );

		//�֣��פξ���ȯ������¾�Ͽ�����
		$lngorderstatuscode = ( $aryNewData["lngWorkflowOrderCode"] == 0) ? DEF_ORDER_ORDER : DEF_ORDER_APPLICATE;
	}


	// m_order HEADER����Υ��󥵡���
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];
	$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
	
	//echo "lngWorkflowOrderCode : ".$aryNewData["lngWorkflowOrderCode"]."<br>";


	$strNote = ( $aryNewData["strNote"] == "null" ) ? "null" : "'".$aryNewData["strNote"]."'";
	$lngMonetaryRateCode = ( $aryNewData["lngMonetaryRateCode"] == "null" ) ? "null" : "'".$aryNewData["lngMonetaryRateCode"]."'";
	$curConversionRate = ( $aryNewData["curConversionRate"] == "null" ) ? "null" : "'".$aryNewData["curConversionRate"]."'";




	// �����襳���ɤ����
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );

	// Ǽ�ʾ�ꥳ���ɤ����
	$aryNewData["lngLocationCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngLocationCode"] . ":str", '', $objDB );



	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_order (";
	$aryQuery[] = "lngorderno, ";													// 1:ȯ���ֹ�
	$aryQuery[] = "lngrevisionno, ";												// 2:��ӥ�����ֹ�
	$aryQuery[] = "strordercode, ";													// 3:ȯ�����ɡ�
	$aryQuery[] = "strrevisecode, ";												// 4:��Х���������
	$aryQuery[] = "dtmappropriationdate, ";											// 5:�׾���
	$aryQuery[] = "lngcustomercompanycode, ";										// 6:��ҥ�����(������)
	//$aryQuery[] = "lnggroupcode, ";													// 7:���롼�ץ����ɡ������
	//$aryQuery[] = "lngusercode, ";													// 8:�桼�������ɡ�ô���ԡ�
	$aryQuery[] = "lngorderstatuscode, ";											// 9:ȯ�����
	$aryQuery[] = "lngmonetaryunitcode, ";											// 10:�̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode, ";											// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";											// 12:�����졼��
	$aryQuery[] = "lngpayconditioncode, ";											// 13:��ʧ���
	$aryQuery[] = "curtotalprice, ";												// 14:��׶��
	$aryQuery[] = "lngdeliveryplacecode, ";											// 15:Ǽ�ʾ�ꥳ����
	$aryQuery[] = "dtmexpirationdate, ";											// 16:ȯ��ͭ��������
	$aryQuery[] = "strnote, ";														// 17:����
	$aryQuery[] = "lnginputusercode, ";												// 18:���ϼԥ�����
	$aryQuery[] = "bytinvalidflag, ";												// 19:̵���ե饰
	$aryQuery[] = "dtminsertdate ";													// 20:��Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = "$sequence_m_order, ";											// 1:ȯ���ֹ�
	$aryQuery[] = "$lngRevisionNo, ";			 									// 2:��ӥ�����ֹ�
	$aryQuery[] = "'".$strOrderCode."', ";											// 3:ȯ�����ɡ�
	$aryQuery[] = "'$strReviseCode',";												// 4:��Х���������
	$aryQuery[] = "'".$aryNewData["dtmOrderAppDate"]."', ";							// 5:�׾���
	if ( $aryNewData["lngCustomerCode"] != "" )
	{
		$aryQuery[] =  $aryNewData["lngCustomerCode"] . ", ";						// 6:��ҥ�����(������)
	}
	else
	{
		$aryQuery[] = "null, ";														// 6:��ҥ�����(������)
	}
	/*
	if ( $aryNewData["lngInChargeGroupCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";						// 7:���롼�ץ����ɡ������
	}
	else
	{
		$aryQuery[] = "null, ";														// 7:���롼�ץ����ɡ������
	}
	if ( $aryNewData["lngInChargeUserCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeUserCode"].",";						// 8:�桼�������ɡ�ô���ԡ�
	}
	else
	{
		$aryQuery[] = "null, ";														// 8:�桼�������ɡ�ô���ԡ�
	}
	*/
	if ( $lngorderstatuscode != "" )
	{
		$aryQuery[] = "$lngorderstatuscode, ";										// 9:ȯ�����
	}
	else
	{
		$aryQuery[] = "null, ";														// 9:ȯ�����
	}
	if ( $lngmonetaryunitcode != "" )
	{
		$aryQuery[] = "$lngmonetaryunitcode, ";										// 10:�̲�ñ�̥�����
	}
	else
	{
		$aryQuery[] = "null, ";														// 10:�̲�ñ�̥�����
	}
	if ( $lngMonetaryRateCode != "" )
	{
		$aryQuery[] = "$lngMonetaryRateCode, ";										// 11:�̲ߥ졼�ȥ�����
	}
	else
	{
		$aryQuery[] = "null, ";														// 11:�̲ߥ졼�ȥ�����
	}
	if ( $curConversionRate != "" )
	{
		$aryQuery[] = "$curConversionRate, ";										// 12:�����졼��
	}
	else
	{
		$aryQuery[] = "null, ";														// 12:�����졼��
	}
	if ( $aryNewData["lngPayConditionCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngPayConditionCode"] . ", ";					// 13:��ʧ���
	}
	else
	{
		$aryQuery[] = "null, ";														// 13:��ʧ���
	}
	$aryQuery[] = "'".$aryNewData["curAllTotalPrice"]."', ";						// 14:��׶��
	if ( $aryNewData["lngLocationCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngLocationCode"] . ", ";						// 15:Ǽ�ʾ�ꥳ����
	}
	else
	{
		$aryQuery[] = "null, ";														// 15:Ǽ�ʾ�ꥳ����
	}
	$aryQuery[] = "'".$aryNewData["dtmExpirationDate"]."', ";						// 16:ȯ��ͭ��������
	$aryQuery[] = "$strNote, ";														// 17:����
	$aryQuery[] = "$lngInputUserCode, ";											// 18:���ϼԥ�����
	$aryQuery[] = "false, ";														// 19:̵���ե饰
	$aryQuery[] = "now()";															// 20:��Ͽ��
	$aryQuery[] = ")";
	
	
	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );
	// echo "$strQuery<br>";
	
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
	}
	$objDB->freeResult( $lngResultID );
	
	// 2004.03.29 suzukaze update start
	////////////////////////////////////
	//// ���ٹ��ֹ椬�����ʹԤ��н� ////
	////////////////////////////////////
	$lngMaxDetailNo = 0;
	// ���ٹԤ���ǻ��ꤵ��Ƥ�������ͤ����
	for ( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "null" 
			and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
			and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "undefined" 
			and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] > $lngMaxDetailNo )
		{
			$lngMaxDetailNo = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
		}
	}
	// �����Ǥ����ٹ��ɲä��б����뤿��˽Ťʤ�Τʤ��ֹ����Ѥ���
	if ( $aryNewData["strProcMode"] == "renew" )
	{
		$lngMaxDetailNo = $lngMaxDetailNo + 100;
	}
	// 2004.03.29 suzukaze update end

	// t_orderDetail HEADER����Υ��󥵡���
	for( $i = 0 ; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		// 9:������ʬ������
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2;
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" ) ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

	// 2004.03.25 suzukaze update start
	// 2004.05.31 suzukaze update start
		// �ⷿ�ֹ���������Ƥ���������꤬�ʤ����Ƕⷿ������ä����Ͽ����˶ⷿ�ֹ�������
		if( $aryNewData["aryPoDitail"][$i]["strSerialNo"] == "null" or $aryNewData["aryPoDitail"][$i]["strSerialNo"] == "" )
		{
			$strSerialNo = "";
			// �������ܤ��������ʶⷿ�������ѡˡ��������ʤ�����Injection Mold�ˤξ��
			// �������ܤ��������ʶⷿ���ѹ�ˡ����������ʤ����ʶⷿ�ˤξ��
			if ( ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM ) 
				or ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT_ADD 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM_ADD ) )
			{
				// ���������ٹ��ֹ椬¸�ߤ�����ˡ���������Ʊ�����ٹ��ֹ�λ������ܡ��������ʡ��ⷿ�ֹ�����
				if ( $aryNewData["strProcMode"] == "renew" 
					and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
					and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "undefined" 
					and is_int($aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"]) )
				{
					$strMoldQuery = "SELECT lngStockSubjectCode, lngStockItemCode, strMoldNo FROM t_OrderDetail "
						. "WHERE lngOrderNo = " . $lngOrderNo 
						. " AND lngOrderDetailNo = " . $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
					// �����å������꡼�μ¹�
					list ( $lngMoldResultID, $lngMoldResultNum ) = fncQuery( $strMoldQuery, $objDB );

					if ( $lngMoldResultNum == 1 )
					{
						$objResult = $objDB->fetchObject( $lngMoldResultID, 0 );
						$lngStockSubjectCode = $objResult->lngstocksubjectcode;
						$lngStockItemCode = $objResult->lngstockitemcode;
						$strMoldNo = $objResult->strmoldno;
					}
					$objDB->freeResult( $lngMoldResultID );
					// Ʊ�����ٹ��ֹ�Υǡ����˶ⷿ�ֹ椬�դ��Ƥ��ʤ����Ͽ������ֹ���������
					if ( $strMoldNo != "" )
					{
						$aryNewData["aryPoDitail"][$i]["strSerialNo"] = $strMoldNo;
					}
					else
					{
						$strSerialNo = fncGetMoldNo( $aryNewData["aryPoDitail"][$i]["strProductCode"], $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"], $aryNewData["aryPoDitail"][$i]["strStockItemCode"], $objDB );
					}
				}
				else
				// ���ٹ��ֹ椬����==>�����ˤ�꿷�������٤��ɲä��줿���Ͽ����˶ⷿ�ֹ���������
				{
					$strSerialNo = fncGetMoldNo( $aryNewData["aryPoDitail"][$i]["strProductCode"], $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"], $aryNewData["aryPoDitail"][$i]["strStockItemCode"], $objDB );
				}
			}
			else
			// ���ꤵ��Ƥ���������ܡ��������ʤ��ⷿ�ֹ���ѤǤʤ����϶ⷿ�ֹ�ս�ˤ�NULL����
			{
				$strSerialNo = "";
			}
		}
		else
		{
			// �������ܤ��������ʶⷿ�������ѡˡ��������ʤ�����Injection Mold�ˤξ��
			// �������ܤ��������ʶⷿ���ѹ�ˡ����������ʤ����ʶⷿ�ˤξ��
			if ( ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM )
				or ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT_ADD 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM_ADD ) )
			{
				// ���ꤵ�줿�ⷿ�ֹ椬�������ǻ��ꤵ��Ƥ������Ϥ��Τޤޤζⷿ�ֹ�����ꤹ��
				$strSerialNo = "";
				$strSerialNo = $aryNewData["aryPoDitail"][$i]["strSerialNo"];
			}
			else
			{
				$strSerialNo = "";
			}
		}
		// SQLʸ�б�
		$strSerialNo = ( $strSerialNo != "" ) ? "'$strSerialNo'" : "null";
		// 2004.03.25 suzukaze update end
		// 2004.05.31 suzukaze update end

		// 2004.03.29 suzukaze update start
		// ���������ֹ�
		// ���ٹ��ֹ椬�ʤ����ʻ������ɲä��줿���ٹԤξ���
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}

		// �������ֹ�
		$lngSortKey = $i + 1;
		// 2004.03.29 suzukaze update end

		$aryQuery	= array();
		$aryQuery[] = "INSERT INTO t_orderdetail (";
		$aryQuery[] = "lngorderno, ";												// 1:ȯ���ֹ�
		$aryQuery[] = "lngorderdetailno, ";											// 2:ȯ�������ֹ�
		$aryQuery[] = "lngrevisionno, ";											// 3:��ӥ�����ֹ�
		$aryQuery[] = "strproductcode, ";											// 4:���ʥ�����
		$aryQuery[] = "lngstocksubjectcode, ";										// 5:�������ܥ�����
		$aryQuery[] = "lngstockitemcode, ";											// 6:�������ʥ�����
		$aryQuery[] = "dtmdeliverydate, ";											// 7:Ǽ����
		$aryQuery[] = "lngdeliverymethodcode, ";									// 8:������ˡ������
		$aryQuery[] = "lngconversionclasscode, ";									// 9:������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
		$aryQuery[] = "curproductprice, ";											// 10:���ʲ���
		$aryQuery[] = "lngproductquantity, ";										// 11:���ʿ���
		$aryQuery[] = "lngproductunitcode, ";										// 12:����ñ�̥�����
		$aryQuery[] = "lngtaxclasscode, ";											// 13:�����Ƕ�ʬ������
		$aryQuery[] = "lngtaxcode, ";												// 14:�����ǥ�����
		$aryQuery[] = "curtaxprice, ";												// 15:�����Ƕ��
		$aryQuery[] = "cursubtotalprice, ";											// 16:���׶��
		$aryQuery[] = "strnote, ";													// 17:����
		$aryQuery[] = "strmoldno, ";												// 18:�ⷿ�ֹ�
		$aryQuery[] = "lngSortKey ";												// 19:ɽ���ѥ����ȥ���
		$aryQuery[] = ") values (";
		$aryQuery[] = "$sequence_m_order, ";										// 1:ȯ���ֹ�
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ",";		// 2:ȯ�������ֹ�
		$aryQuery[] = "$lngRevisionNo,";											// 3:��ӥ�����ֹ�
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";	// 4:���ʥ�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"].", ";	// 5:�������ܥ�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockItemCode"].", ";		// 6:�������ʥ�����
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";	// 7:Ǽ����
		if ( $aryNewData["aryPoDitail"][$i]["lngCarrierCode"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngCarrierCode"].", ";	// 8:������ˡ������
		}
		else
		{
			$aryQuery[] = "null, ";													// 8:������ˡ������
		}
		if ( $lngConversionClassCode != "" )
		{
			$aryQuery[] = "$lngConversionClassCode, ";								// 9:������ʬ������
		}
		else
		{
			$aryQuery[] = "null, ";													// 9:������ʬ������
		}
		if ( $aryNewData["aryPoDitail"][$i]["curProductPrice"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["curProductPrice"].", ";	// 10:����
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		if ( $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";		// 11:����
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		if ( $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";	// 12:ñ�̥�����
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = "null,";															// 13:�����Ƕ�ʬ������
		$aryQuery[] = "null,";															// 14:�����ǥ�����
		$aryQuery[] = "null,";															// 15:�����Ƕ��
		if ( $aryNewData["aryPoDitail"][$i]["curTotalPrice"] != "" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["curTotalPrice"].", ";			// 16:��ȴ�����
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = "$strDetailNote, ";												// 17:����
		$aryQuery[] = $strSerialNo . ", ";												// 18:�ⷿ�ֹ�
		$aryQuery[] = $lngSortKey . " ";												// 19:ɽ���ѥ����ȥ���
		$aryQuery[] = ")";
		
		$strQuery = "";
		$strQuery = implode( $aryQuery );
		
		//echo "<br><br>$strQuery<br>";

		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		$objDB->freeResult( $lngResultID );
		
	}

	// 2004.04.01 suzukaze update start
	if ( !fncCheckSetProduct ( $aryNewData["aryPoDitail"], $lngmonetaryunitcode, $objDB ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	// 2004.04.01 suzukaze update end



	$strProductCode       = $aryNewData["aryPoDitail"][0]["strProductCode"];
	$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );


	// ��ǧ�롼�Ȥ����򤵤줿���־�ǧ�롼�Ȥʤ��ע���0��
	if($aryNewData["lngWorkflowOrderCode"] != 0 )
	{

		// m_workflow�Υ������󥹤����
		$lngworkflowcode = fncGetSequence( 'm_Workflow.lngworkflowcode', $objDB );
		$strworkflowname = "ȯ�� [No:$strOrderCode-$strReviseCode"."]";

		$aryQuery = array();
		$aryQuery[] = "INSERT INTO m_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// 1:����ե�������
		$aryQuery[] = "lngworkflowordercode, ";						// 2:����ե����������
		$aryQuery[] = "strworkflowname, ";							// 3:����ե�̾��
		$aryQuery[] = "lngfunctioncode, ";							// 4:��ǽ������
		$aryQuery[] = "strworkflowkeycode, ";						// 5:����ե����������� 
		$aryQuery[] = "dtmstartdate, ";								// 6:�Ʒ�ȯ����
		$aryQuery[] = "dtmenddate, ";								// 7:�Ʒｪλ��
		$aryQuery[] = "lngapplicantusercode, ";						// 8:�Ʒ����ԥ�����
		$aryQuery[] = "lnginputusercode, ";							// 9:�Ʒ����ϼԥ�����
		$aryQuery[] = "bytinvalidflag, ";							// 10:̵���ե饰
		$aryQuery[] = "strnote";									// 11:����
		$aryQuery[] = " ) values (";
		$aryQuery[] = "$lngworkflowcode, ";							// 1:����ե�������
		if ( $aryNewData["lngWorkflowOrderCode"] != "" )
		{
			$aryQuery[] = $aryNewData["lngWorkflowOrderCode"].", ";		// 2:����ե����������
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = "'$strworkflowname', ";						// 3:����ե�̾��
		$aryQuery[] = DEF_FUNCTION_PO1.", ";						// 4:��ǽ������
		$aryQuery[] = "$sequence_m_order, ";						// 5:����ե����������� 
		$aryQuery[] = "now(), ";									// 6:�Ʒ�ȯ����
		$aryQuery[] = "null, ";										// 7:�Ʒｪλ��
		$aryQuery[] = $lngApplicantUserCode . ", ";					// 8:�Ʒ����ԥ�����
		$aryQuery[] = "$lngInputUserCode, ";						// 9:�Ʒ����ϼԥ�����
		$aryQuery[] = "false, ";									// 10:̵���ե饰
		$aryQuery[] = "null";										// 11:����
		$aryQuery[] = " )";
		
		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );
		
		// echo "$strQuery<br>";
		
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		$objDB->freeResult( $lngResultID );
		
		
		$lngLimitDate = fncGetMasterValue( "m_workfloworder" ,"lngworkflowordercode", "lnglimitdays", $aryNewData["lngWorkflowOrderCode"] ,"lngworkfloworderno = 1", $objDB);
		
		
		//$lngLimitDate  = mktime ( date("H"), date("i"), date("s"), date("m"),  date("d")+$lngLimitDate,  date("Y"));
		//$lngLimitDate = date( 'Y-m-d H:i:s', $lngLimitDate );
		
		// echo "��������$lngLimitDate<br>";
		
		$aryQuery = array();
		$aryQuery[] = "INSERT INTO t_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// ����ե�������
		$aryQuery[] = "lngworkflowsubcode, ";						// ����ե����֥�����
		$aryQuery[] = "lngworkfloworderno, ";						// ����ե�����ֹ�
		$aryQuery[] = "lngworkflowstatuscode, ";					// ����ե����֥�����
		$aryQuery[] = "strnote, ";									// ����
		$aryQuery[] = "dtminsertdate, ";							// ��Ͽ��
		$aryQuery[] = "dtmlimitdate ";								// ������
		$aryQuery[] = ") values (";
		$aryQuery[] = "$lngworkflowcode, ";							// ����ե�������
		$aryQuery[] = DEF_T_WORKFLOW_SUBCODE.", ";					// ����ե����֥�����
		$aryQuery[] = DEF_T_WORKFLOW_ORDERNO.", ";					// ����ե�����ֹ�
		$aryQuery[] = DEF_T_WORKFLOW_STATUS.", ";					// ����ե����֥�����
		// 2004.03.24 suzukaze update start
		$aryQuery[] = "'" . $aryNewData["strWorkflowMessage"] . "',";	// 11:����
		// 2004.03.24 suzukaze update end
		$aryQuery[] = "now(), ";									// ��Ͽ��
		$aryQuery[] = "now() + (interval '$lngLimitDate day' )";	// ������
		$aryQuery[] = ")";
		
		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );
		
		// echo "$strQuery<br>";

		
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		$objDB->freeResult( $lngResultID );
		
		
		// ��ǧ�Ԥ˥᡼�������
		$arySelect = array();
		$arySelect[] = "SELECT u.strmailaddress, ";									// �᡼�륢�ɥ쥹
		$arySelect[] = "u.bytMailTransmitFlag, ";									// �᡼���ۿ����ĥե饰
		$arySelect[] = "w.strworkflowordername, ";									// ����ե�̾
		$arySelect[] = "u.struserdisplayname ";										// ��ǧ��
		$arySelect[] = "FROM m_workfloworder w, m_user u, m_authoritygroup a ";
		$arySelect[]= "WHERE w.lngworkflowordercode = ";
		$arySelect[] = $aryNewData["lngWorkflowOrderCode"]." AND ";
		$arySelect[] = "u.lngusercode = w.lnginchargecode AND ";
		$arySelect[] = "u.lngauthoritygroupcode = a.lngauthoritygroupcode ";
		$arySelect[] = "ORDER BY a.lngauthoritylevel DESC";

		$strSelect = "";
		$strSelect = implode("\n", $arySelect );
		
		// echo "$strSelect";
		
		if ( $lngResultID = $objDB->execute( $strSelect ) )
		{
			$aryResult[] = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		}
		$objDB->freeResult( $lngResultID );
		
		// �᡼��ʸ�̤�ɬ�פʥǡ���������$aryMailData�˳�Ǽ
		$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];				// ��ǧ�ԥ᡼�륢�ɥ쥹

		// 2004.03.23 suzukaze update start
		// ���ϼԥ᡼�륢�ɥ쥹�μ���
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;

		list ( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );
		if ( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag 	= $objResult->bytmailtransmitflag;
			$strInputUserMailAddress 		= $objResult->strmailaddress;
		}
		else
		{
			fncOutputError( 9051, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "po/regist/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngUserMailResultID );

		// �᡼���ۿ����ĥե饰����TRUE�������ꤵ��Ƥ��ʤ���硢���ϼԡʿ����ԡˤΥ᡼�륢�ɥ쥹�����ꤵ��Ƥ��ʤ����ϡ��᡼���������ʤ�
		if ( $aryResult[0]["bytmailtransmitflag"] == "t" and $aryMailData["strmailaddress"] != "" 
			and $strInputUserMailAddress != "" )
		{
			$strMailAddress = $aryResult[0]["strmailaddress"];								// ��ǧ�ԥ᡼�륢�ɥ쥹
			$aryMailData["strWorkflowName"] = $strworkflowname;								// �Ʒ�̾
			//			$aryMailData["strUserDisplayName"] = $aryResult[0]["struserdisplayname"];		// ��ǧ�����
			$aryMailData["strUserDisplayName"] = $objAuth->UserDisplayName;					// ���ϼԡʿ����ԡ�ɽ��̾
			$aryMailData["strURL"] = LOGIN_URL;												// URL
			// 2004.03.24 suzukaze update start
			// ��ǧ���̾�Υ�å�������᡼�����������Ȥ�������
			$aryMailData["strNote"] = $aryNewData["strWorkflowMessage"];
			// 2004.03.24 suzukaze update end

			// �᡼���å����������ؿ�
			list ( $strSubject, $strTemplate ) = fncGetMailMessage( 807, $aryMailData, $objDB );

			// �����ԥ᡼�륢�ɥ쥹�����ؿ�
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

			// �᡼������
			fncSendMail( $strMailAddress, $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}
		// 2004.03.23 suzukaze update end

		// Ģɼ����ɽ������
		$aryData["PreviewVisible"] = "hidden";

	}
	// ¨��ǧ�ξ��ץ�ӥ塼�ܥ����ɽ������
	else
	{
		// Ģɼ�����б�
		// ���¤���äƤʤ����ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
		if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) && $lngorderstatuscode != DEF_ORDER_APPLICATE )
		{
			$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $sequence_m_order . "&bytCopyFlag=TRUE";
			// Ģɼ����ɽ������
			$aryData["PreviewVisible"] = "visible";
		}
		else
		{
			// Ģɼ����ɽ������
			$aryData["PreviewVisible"] = "hidden";
		}
	}

	// �ȥ�󥶥������λ
	$objDB->transactionCommit();


	
	$aryData["strBodyOnload"] = "";
	
	$objDB->close();


	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/po/regist/index.php?strSessionID=";

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	
	// �ƥ�ץ졼�Ȥ�ȿ�Ǥ���ʸ����
	$aryData["lngPONo"] = "$strOrderCode - $strReviseCode";

	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->getTemplate( "po/finish/parts.tmpl" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;
			
	return true;
?>