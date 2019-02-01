<?php

// ----------------------------------------------------------------------------
/**
*       �������  ��Ͽ
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  Kuwagata
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



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc' );                     // ����ե�����
	require( LIB_FILE );                       // ���饹�饤�֥��ե�����
	require( SRC_ROOT . "po/cmn/lib_po.php" ); // ȯ������ؿ��ե�����
	require( SRC_ROOT . "so/cmn/lib_so.php" ); // ��������ؿ��ե�����
	require( LIB_DEBUGFILE );

	//var_dump($_POST);
	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();   // DB���֥�������
	$objAuth = new clsAuth(); // ǧ�ڽ������֥�������


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]         = $_REQUEST["strSessionID"];          // ���å����ID
	$aryData["lngLanguageCode"]      = $_COOKIE["lngLanguageCode"];        // ���쥳����


	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// �� ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ����ʸ����
	$aryCheck["strSessionID"] = "null:numenglish( 32,32 )";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å����
	$objAuth     = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;


	// 400 ȯ�����
	if( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
		fncOutputError( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 401 ȯ������ʼ�����Ͽ��
	if( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
	{
		fncOutputError( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}





	// ���ٹԤ�������ͤ����
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each( $_POST );

		if( $strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}



	// displayCode��code���Ѵ�����
	// fncChangeData�ǿ������������
	$aryNewData = fncChangeData( $aryData, $objDB );


	// ���ٹԤ��ͤ����
	for( $i=0; $i<count($_POST[aryPoDitail]); $i++ )
	{
		while( list( $strKeys, $strValues ) = each( $_POST[aryPoDitail][$i] ) )
		{
			$aryNewData["aryPoDitail"][$i][$strKeys] = ( $strValues == "" ) ? "null" : $strValues ;
		}
	}



	// �����⡼�ɤμ���
	$strProcMode = $aryNewData["strProcMode"]; // �����⡼��


	// ����ե���å�����������ξ��
	$aryNewData["strWorkflowMessage"] = ( $aryNewData["strWorkflowMessage"] == "null" ) ? "" : $aryNewData["strWorkflowMessage"];

//fncDebug( 'lib_so.txt', $aryNewData, __FILE__, __LINE__);

	//-------------------------------------------------------------------------
	// �� DB -> SELECT : m_productprice
	//-------------------------------------------------------------------------
	// Ʊ���ֻ������ܡסֻ������ʡפξ���Ʊ��ñ�������뤫
	// m_productPrice��Ʊ���ͤ����뤫���ߤä���������˹��ֹ�򵭲�
	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$lngmonetaryunitcode = "";

		$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

		$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
		$strProductCode      = "";
		$strProductCode      = fncGetMasterValue( "m_product", "strproductcode", "lngproductno", $aryNewData["aryPoDitail"][$i]["strProductCode"]. ":str", '', $objDB );

		$arySelect   = array();
		$arySelect[] = "SELECT ";
		$arySelect[] = "lngproductpricecode ";
		$arySelect[] = "FROM ";
		$arySelect[] = "m_productprice ";
		$arySelect[] = "WHERE ";
		$arySelect[] = "lngproductno = $strProductCode AND ";
		$arySelect[] = "lngsalesclasscode = ".$aryNewData["aryPoDitail"][$i]["lngSalesClassCode"]." AND ";
		$arySelect[] = "lngmonetaryunitcode = $lngmonetaryunitcode AND ";
		$arySelect[] = "curproductprice = '".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."'";

		$strSelect = implode("\n", $arySelect );


		// ���ID�����
		$objDB->freeResult( $lngResultID );


		// ������¹�
		$lngResultID = $objDB->execute( $strSelect );


		// ������¹������ξ��
		if( $lngResultID )
		{
			// Ʊ�����ʲ��ʤ����Ĥ���ʤ���硢�⤷����ñ�̷׾夬����ñ�̷׾�ξ��Τ߹��ֹ�򵭲�����
			if( pg_num_rows( $lngResultID ) == 0 and $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" )
			{
				$aryM_ProductPrice[] = $i; //���ֹ�򵭲�
			}
		}
	}
	//---------------------------------------------------------------



	// �̲ߤ򥳡��ɤ��Ѵ�
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );

	// ���ϼԥ����ɤ����
	$lngUserCode = $objAuth->UserCode;

	// ���ͤ����
	$strDetailNote = ( $aryNewData["strNote"] == "null" ) ? "null" : "'".$aryNewData["strNote"]."'";



	//-------------------------------------------------------------------------
	// �� m_Receive �Υ��������ֹ�����
	//-------------------------------------------------------------------------
	$lngReceiveNo = fncGetSequence( 'm_Receive.lngReceiveNo', $objDB );

	//-------------------------------------------------------------------------
	// �� �ȥ�󥶥�����󳫻�
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();





	//-------------------------------------------------------------------------
	// �� DB -> INSERT : m_Receive
	//-------------------------------------------------------------------------
	// �����ֹ�μ���
	$strReceiveCode = $aryNewData["strReceiveCode"];


	//---------------------------------------------------------------
	// �����⡼�ɤ�����Ͽ�פξ��
	//---------------------------------------------------------------
	if( $strProcMode == "regist" )
	{
		// ��ӥ�����ֹ������
		$lngRevisionNo = 0;

		// ��Х����ֹ�ν����
		$strReviseCode  = "00";

		// �����ֹ�μ���
		$strReceiveCode = fncGetDateSequence( date('Y', strtotime( $aryNewData["dtmOrderAppDate"] ) ), date('m',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_receive.strreceivecode", $objDB );

		// d ʸ���ղ�
		$strReceiveCode = "d" . $strReceiveCode;
		$aryNewData["strReceiveCode"] = $strReceiveCode;

		// ������֥����ɤμ���
		$lngReceiveStatusCode = ( $aryNewData["lngWorkflowOrderCode"] == 0 ) ? 2 : 1;
	}
	//---------------------------------------------------------------
	// �����⡼�ɤ��ֽ����פξ��
	//---------------------------------------------------------------
	else
	{
		//-------------------------------------------------
		// �ǿ���Х����ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
		//-------------------------------------------------
		$strCheckQuery = "SELECT lngReceiveNo, lngReceiveStatusCode FROM m_Receive r WHERE r.strReceiveCode = '" . $aryNewData["strReceiveCode"] . "'";
		$strCheckQuery .= " AND r.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND r.lngRevisionNo = ( "
			. "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode )\n";
		$strCheckQuery .= " AND r.strReviseCode = ( "
			. "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode )\n";

		// �����å������꡼�μ¹�
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngReceiveStatusCode = $objResult->lngreceivestatuscode;

			if ( $lngReceiveStatusCode == DEF_ORDER_APPLICATE )
			{
				fncOutputError( 409, DEF_WARNING, "", TRUE, "../so/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngCheckResultID );



		//-------------------------------------------------------------------------
		// �ƾ�ǧ�Τ��ᡢ���ơ�������ֿ�����פ��ѹ�
		//-------------------------------------------------------------------------
		if( $aryNewData["lngWorkflowOrderCode"] == 0 )
		{
			$lngReceiveStatusCode = DEF_RECEIVE_ORDER;

			// �������å�
			$arySql = array();
			$arySql[] = "select count(*) as count";
			$arySql[] = "from";
			$arySql[] = "	m_sales ms";
			$arySql[] = "		left join t_salesdetail tsd on tsd.lngsalesno = ms.lngsalesno";
			$arySql[] = "where";
			$arySql[] = "tsd.lngreceiveno in ";
			$arySql[] = "(";
			$arySql[] = "	select ms1.lngreceiveno";
			$arySql[] = "	from";
			$arySql[] = "		m_receive ms1";
			$arySql[] = "	where";
			$arySql[] = "		ms1.strreceivecode = '" . $aryNewData["strReceiveCode"] . "'";
			$arySql[] = ")";
			$arySql[] = "and ms.bytinvalidflag = false";
			$arySql[] = "AND ms.lngRevisionNo = (";
			$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.bytInvalidFlag = false and s1.strSalesCode = ms.strSalesCode)";
			$arySql[] = "	AND 0 <= (";
			$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.bytInvalidFlag = false and s2.strSalesCode = ms.strSalesCode )";

			$strQuery = implode("\n", $arySql);
			// �ģ��䤤��碌
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum == 1 )
			{
				$objResult	= $objDB->fetchObject( $lngResultID, 0 );
				// ��İʾ塢���ǡ���������С���Ǽ����פȤ���
				if( 1 <= (int)$objResult->count)
				{
					$lngReceiveStatusCode = DEF_RECEIVE_DELIVER;
				}
			}

		}
		// ������
		else
		{
			$lngReceiveStatusCode = DEF_RECEIVE_APPLICATE;
		}


		//-------------------------------------------------------------------------
		// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
		//-------------------------------------------------------------------------
		$lngReceiveStatusCode = fncCheckNullStatus( $lngReceiveStatusCode );

		//-------------------------------------------------------------------------
		// ���֥����ɤ���0�פξ�硢��1�פ������
		//-------------------------------------------------------------------------
		$lngReceiveStatusCode = fncCheckZeroStatus( $lngReceiveStatusCode );


		// Ʊ������No����Ѥ���
//		$lngReceiveNo = $aryData['lngReceiveNo'];


		// �����ξ��Ʊ���������Ф��ƥ�ӥ�����ֹ�κ����ͤ��������
		// ��ӥ�����ֹ�򸽺ߤκ����ͤ�Ȥ�褦�˽�������
		// ���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ��������Ф��ƥ�å����֤ˤ����MAX����ؿ����Ѥ���ȡ�FOR UPDATE�������ʤ���
		$strLockQuery = "SELECT lngRevisionNo FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "' FOR UPDATE";

		// ��å������꡼�¹�
		list( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

		// �����ӥ�����ֹ�μ���
		$lngMaxRevision = 0;
		for( $i = 0; $i < $lngLockResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngLockResultID, $i );
			if ( $lngMaxRevision < $objResult->lngrevisionno )
			{
				$lngMaxRevision = $objResult->lngrevisionno;
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngLockResultID );

		// ��ӥ�����ֹ�򥤥󥯥����
		$lngRevisionNo = $lngMaxRevision + 1;

		// ��Х����ֹ�򥤥󥯥����
		$strReviseCode = sprintf( "%02d", $lngMaxRevision + 1 );

	}



	//---------------------------------------------------------------
	// �� ����������å�
	//---------------------------------------------------------------
	// �ܵҼ����ֹ�Υ����å�
	$aryNewData["strCustomerReceiveCode"] = trim( $aryNewData["strCustomerReceiveCode"] );

	// �ܵҼ����ֹ椬����Ǥ�̵�����
	if( $aryNewData["strCustomerReceiveCode"] != "null" )
	{
		// ������֤���� ( ���������ܵҼ����ֹ椬��10:������פξ��ϡ���1:������פ��Ѵ� )
		$lngReceiveStatusCode = ( $lngReceiveStatusCode == 10 ) ? 1 : $lngReceiveStatusCode;

		// �ܵҼ����ֹ�����
		$strCustomerReceiveCode = $aryNewData["strCustomerReceiveCode"];
	}
	// ����ξ�硢������
	else
	{
		// ������֤����
		$lngReceiveStatusCode = 10;

		// �ܵҼ����ֹ�����
		$strCustomerReceiveCode = "";
	}



	// ��Х����ֹ�μ���
	$strReviseCode = ( $aryNewData["strReviseCode"] == "null" ) ? "00" : $strReviseCode;

	// �ܵҤ򥳡����Ѵ�
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );




	$aryQuery   = array();
	$aryQuery[] = "INSERT INTO m_receive( ";
	$aryQuery[] = "lngreceiveno, ";												// 1:�����ֹ�
	$aryQuery[] = "lngrevisionno, ";											// 2:��ӥ�����ֹ�
	$aryQuery[] = "strreceivecode, ";											// 3:��������
	$aryQuery[] = "strrevisecode, ";											// 4:��Х��������� 
	$aryQuery[] = "dtmappropriationdate, ";										// 5:�׾���
	$aryQuery[] = "lngcustomercompanycode, ";									// 6:��ҥ����� 
	//$aryQuery[] = "lnggroupcode, ";												// 7:���롼�ץ�����
	//$aryQuery[] = "lngusercode, ";												// 8:�桼����������
	$aryQuery[] = "lngreceivestatuscode, ";										// 9:������֥�����
	$aryQuery[] = "lngmonetaryunitcode, ";										// 10:�̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode, ";										// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";										// 12:�����졼��
	$aryQuery[] = "curtotalprice, ";											// 13:��׶��
	$aryQuery[] = "strnote, ";													// 14:����
	$aryQuery[] = "lnginputusercode, ";											// 15:���ϼԥ����� 
	$aryQuery[] = "bytinvalidflag, ";											// 16:̵���ե饰
	$aryQuery[] = "dtminsertdate, ";											// 17:��Ͽ��
	$aryQuery[] = "strcustomerreceivecode ";									// �ܵҼ����ֹ�
	$aryQuery[] = " ) values ( ";
	$aryQuery[] = "$lngReceiveNo, ";											// 1:�����ֹ�
	$aryQuery[] = "$lngRevisionNo, ";											// 2:��ӥ�����ֹ�
	$aryQuery[] = "'".$strReceiveCode."', ";									// 3:��������
	$aryQuery[] = "'$strReviseCode', ";											// 4:��Х��������� 
	$aryQuery[] = "'". $aryNewData["dtmOrderAppDate"]."',";						// 5:�׾���
	$aryQuery[] = $aryNewData["lngCustomerCode"].", ";							// 6:��ҥ����� 
	//$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";						// 7:���롼�ץ�����
	//$aryQuery[] = $aryNewData["lngInChargeUserCode"].", ";						// 8:�桼����������
	$aryQuery[] = "$lngReceiveStatusCode, ";									// 9:������֥�����
	$aryQuery[] = "$lngMonetaryUnitCode, ";										// 10:�̲�ñ�̥�����
	$aryQuery[] = $aryNewData["lngMonetaryRateCode"].", ";						// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "'".$aryNewData["curConversionRate"]."', ";					// 12:�����졼��
	$aryQuery[] = "'".$aryNewData["curAllTotalPrice"]."', ";					// 13:��׶��
	$aryQuery[] = "$strDetailNote, ";											// 14:����
	$aryQuery[] = "$lngUserCode, ";												// 15:���ϼԥ����� 
	$aryQuery[] = "false, ";													// 16:̵���ե饰
	$aryQuery[] = "now(), ";													// 17:��Ͽ��
	$aryQuery[] = "'" . $strCustomerReceiveCode . "' ";							// �ܵҼ����ֹ�
	$aryQuery[] = " ) ";


	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );


	// ���ID�����
	$objDB->freeResult( $lngResultID );


	// ������¹�
	$lngResultID = $objDB->execute( $strQuery );


	// ������¹Լ��Ԥξ��
	if ( !$lngResultID )
	{
		echo "m_receive : ERROR<br>";
		//fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� DB -> INSERT : t_receivedetail
	//-------------------------------------------------------------------------
	// ���ٹ��ֹ椬�����ʹԤ��н�
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

	// ���Ǥ����ٹ��ɲä��б����뤿��˽Ťʤ�Τʤ��ֹ����Ѥ���
	if( $strProcMode != "regist" )
	{
		$lngMaxDetailNo = $lngMaxDetailNo + 100;
	}


	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		// ����
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" )  ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

		// �����ǥ�����
		if($aryNewData["aryPoDitail"][$i]["lngTaxCode"] != "null")
		{
			$lngTaxCode = $aryNewData["aryPoDitail"][$i]["lngTaxCode"];
		}
		else
		{
			$lngTaxCode = "null";
		}
		

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

		// ������ʬ������
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2 ;

		// �ǳ�
		$curTaxPrice = ( $aryNewData["aryPoDitail"][$i]["curTaxPrice"] == "null" ) ? "null" : "'".$aryNewData["aryPoDitail"][$i]["curTaxPrice"]."'";


		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO t_receivedetail ( ";
		$aryQuery[] = "lngreceiveno, ";													// 1:�����ֹ�
		$aryQuery[] = "lngreceivedetailno, ";											// 2:���������ֹ�
		$aryQuery[] = "lngrevisionno, ";												// 3:��ӥ�����ֹ�
		$aryQuery[] = "strproductcode, ";												// 4:���ʥ�����
		$aryQuery[] = "lngsalesclasscode, ";											// 5:����ʬ������
		$aryQuery[] = "dtmdeliverydate, ";												// 6:Ǽ����
		$aryQuery[] = "lngconversionclasscode, ";										// 7:������ʬ������
		$aryQuery[] = "curproductprice, ";												// 8:���ʲ���
		$aryQuery[] = "lngproductquantity, ";											// 9:���ʿ���
		$aryQuery[] = "lngproductunitcode, ";											// 10:����ñ�̥�����
		$aryQuery[] = "lngtaxclasscode, ";												// 11:�����Ƕ�ʬ������
		$aryQuery[] = "lngtaxcode, ";													// 12:�����ǥ�����
		$aryQuery[] = "curtaxprice, ";													// 13:�����Ƕ��
		$aryQuery[] = "cursubtotalprice, ";												// 14:���׶��
		$aryQuery[] = "strnote,";														// 15:����
		$aryQuery[] = "lngSortKey";														// 16:ɽ���ѥ����ȥ���
		$aryQuery[] = " ) values ( ";
		$aryQuery[] = "'$lngReceiveNo', ";												// 1:�����ֹ�
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ", ";		// 2:���������ֹ�
		$aryQuery[] = "$lngRevisionNo, ";												// 3:��ӥ�����ֹ�
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";		// 4:���ʥ�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngSalesClassCode"].", ";			// 5:����ʬ������
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";		// 6:Ǽ����
		$aryQuery[] = "$lngConversionClassCode, ";										// 7:������ʬ������ 
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."', ";		// 8:���ʲ���
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";			// 9:���ʿ���
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";		// 10:����ñ�̥�����
		$aryQuery[] = "null, ";															// 11:�����Ƕ�ʬ������lngTaxClassCode
		$aryQuery[] = "null, ";															// 12:�����ǥ�����lngTaxCode
		$aryQuery[] = "null, ";															// 13:�����Ƕ��curTaxPrice
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["curTotalPrice"].", ";				// 14:���׶��curSubtotalPrice
		$aryQuery[] = $strDetailNote . ", ";											// 15:����strNote
		$aryQuery[] = $lngSortKey . " ";												// 16:ɽ���ѥ����ȥ���
		$aryQuery[] = ") ";
		
		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// ���ID�����
		$objDB->freeResult( $lngResultID );


		// ������¹�
		$lngResultID = $objDB->execute( $strQuery );


		// ������¹Լ��Ԥξ��
		if ( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();

			return true;
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� DB -> INSERT : m_productprice
	//-------------------------------------------------------------------------
	if( is_array( $aryM_ProductPrice ) )
	{
		for( $i = 0; $i < count( $aryM_ProductPrice ); $i++ )
		{
			// m_order�Υ������󥹤����
			$sequence_m_productprice = fncGetSequence( 'm_ProductPrice.lngProductPriceCode', $objDB );

			list( $strKeys, $strValues ) = each( $aryM_ProductPrice );

			$aryQuery = array();
			$aryQuery[] = "INSERT INTO m_productprice (";
			$aryQuery[] = "lngproductpricecode, ";												// 1:���ʲ��ʥ�����
			$aryQuery[] = "lngproductno,";														// 2:�����ֹ�
			$aryQuery[] = "lngsalesclasscode,";													// 3:����ʬ������
			$aryQuery[] = "lngmonetaryunitcode,";												// 4:�̲�ñ�̥�����
			$aryQuery[] = "curproductprice ";													// 5:���ʲ���
			$aryQuery[] = ") VALUES (";
			$aryQuery[] = "$sequence_m_productprice, ";											// 1:���ʲ��ʥ�����
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["strProductCode"].",";			// 2:�����ֹ�	
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["lngSalesClassCode"].",";		// 3:����ʬ������
			$aryQuery[] = "$lngmonetaryunitcode,";												// 4:�̲�ñ�̥�����
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["curProductPrice"];			// 5:���ʲ���
			$aryQuery[] = ")";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );


			// ���ID�����
			$objDB->freeResult( $lngResultID );


			// ������¹�
			$lngResultID = $objDB->execute( $strQuery );


			// ������¹Լ��Ԥξ��
			if ( !$lngResultID )
			{
				fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
				$objDB->close();
				return true;
			}
		}
	}
	//-------------------------------------------------------------------------





	//-------------------------------------------------------------------------
	// �� ��ǧ����
	//
	//   ��ǧ�롼��
	//     ��0 : ��ǧ�롼�Ȥʤ�
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryNewData["lngWorkflowOrderCode"];	// ��ǧ�롼��

	//$strWFName   = "���� [No:" . $strReceiveCode . "-" . $strReviseCode . "]";
	$strWFName   = "���� [No:" . $aryNewData["strCustomerReceiveCode"] . "]";

	$lngSequence = $lngReceiveNo;
	$strDefFnc   = DEF_FUNCTION_SO1;

	$strProductCode       = $aryNewData["aryPoDitail"][0]["strProductCode"];
	$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );


	// ��ǧ�롼�Ȥ����򤵤줿���
	if( $lngWorkflowOrderCode != 0 && $lngReceiveStatusCode != 10 )
	{
		//---------------------------------------------------------------
		// DB -> INSERT : m_workflow
		//---------------------------------------------------------------
		// m_workflow �Υ������󥹤����
		$lngworkflowcode = fncGetSequence( 'm_Workflow.lngworkflowcode', $objDB );
		$strworkflowname = $strWFName;

		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO m_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// 1  : ����ե�������
		$aryQuery[] = "lngworkflowordercode, ";						// 2  : ����ե����������
		$aryQuery[] = "strworkflowname, ";							// 3  : ����ե�̾��
		$aryQuery[] = "lngfunctioncode, ";							// 4  : ��ǽ������
		$aryQuery[] = "strworkflowkeycode, ";						// 5  : ����ե����������� 
		$aryQuery[] = "dtmstartdate, ";								// 6  : �Ʒ�ȯ����
		$aryQuery[] = "dtmenddate, ";								// 7  : �Ʒｪλ��
		$aryQuery[] = "lngapplicantusercode, ";						// 8  : �Ʒ����ԥ�����
		$aryQuery[] = "lnginputusercode, ";							// 9  : �Ʒ����ϼԥ�����
		$aryQuery[] = "bytinvalidflag, ";							// 10 : ̵���ե饰
		$aryQuery[] = "strnote";									// 11 : ����

		$aryQuery[] = " ) values (";
		$aryQuery[] = "$lngworkflowcode, ";							// 1  : ����ե�������
		$aryQuery[] = ( $lngWorkflowOrderCode != "" ) ? $lngWorkflowOrderCode . ", " : "null, "; // 2  : ����ե����������
		$aryQuery[] = "'$strworkflowname', ";						// 3  : ����ե�̾��
		$aryQuery[] = $strDefFnc . ", ";							// 4  : ��ǽ������
		$aryQuery[] = $lngSequence . ", ";							// 5  : ����ե����������� 
		$aryQuery[] = "now(), ";									// 6  : �Ʒ�ȯ����
		$aryQuery[] = "null, ";										// 7  : �Ʒｪλ��
		$aryQuery[] = $lngApplicantUserCode . ", ";					// 8  : �Ʒ����ԥ�����
		$aryQuery[] = "$lngUserCode, ";								// 9  : �Ʒ����ϼԥ�����
		$aryQuery[] = "false, ";									// 10 : ̵���ե饰
		$aryQuery[] = "''";											// 11 : ����
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// ������¹�
		$lngResultID = $objDB->execute( $strQuery );


		// ������¹Լ��Ԥξ��
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		// ͭ���������μ���
		$lngLimitDate = fncGetMasterValue( "m_workfloworder" ,"lngworkflowordercode", "lnglimitdays", $lngWorkflowOrderCode ,"lngworkfloworderno = 1", $objDB );

		//echo "��������$lngLimitDate<br>";



		//---------------------------------------------------------------
		// DB -> INSERT : t_workflow
		//---------------------------------------------------------------
		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO t_workflow (";
		$aryQuery[] = "lngworkflowcode, ";								// ����ե�������
		$aryQuery[] = "lngworkflowsubcode, ";							// ����ե����֥�����
		$aryQuery[] = "lngworkfloworderno, ";							// ����ե�����ֹ�
		$aryQuery[] = "lngworkflowstatuscode, ";						// ����ե����֥�����
		$aryQuery[] = "strnote, ";										// ����
		$aryQuery[] = "dtminsertdate, ";								// ��Ͽ��
		$aryQuery[] = "dtmlimitdate ";									// ������

		$aryQuery[] = ") values (";
		$aryQuery[] = "$lngworkflowcode, ";								// ����ե�������
		$aryQuery[] = DEF_T_WORKFLOW_SUBCODE.", ";						// ����ե����֥�����
		$aryQuery[] = DEF_T_WORKFLOW_ORDERNO.", ";						// ����ե�����ֹ�
		$aryQuery[] = DEF_T_WORKFLOW_STATUS.", ";						// ����ե����֥�����
		$aryQuery[] = "'" . $aryNewData["strWorkflowMessage"] . "',";	// 11:����
		$aryQuery[] = "now(), ";										// ��Ͽ��
		$aryQuery[] = "now() + (interval '$lngLimitDate day' )";		// ������
		$aryQuery[] = ")";

		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );


		// ������¹�
		$lngResultID = $objDB->execute( $strQuery );


		// ������¹Լ��Ԥξ��
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_workfloworder, m_user, m_authoritygroup
		//---------------------------------------------------------------
		// ��ǧ�Ԥ˥᡼�������
		$arySelect = array();
		$arySelect[] = "SELECT u.strmailaddress, ";									// �᡼�륢�ɥ쥹
		$arySelect[] = "u.bytMailTransmitFlag, ";									// �᡼���ۿ����ĥե饰
		$arySelect[] = "w.strworkflowordername, ";									// ����ե�̾
		$arySelect[] = "u.struserdisplayname ";										// ��ǧ��
		$arySelect[] = "FROM m_workfloworder w, m_user u, m_authoritygroup a ";
		$arySelect[]= "WHERE w.lngworkflowordercode = ";
		$arySelect[] = $lngWorkflowOrderCode." AND ";
		$arySelect[] = "u.lngusercode = w.lnginchargecode AND ";
		$arySelect[] = "u.lngauthoritygroupcode = a.lngauthoritygroupcode ";
		$arySelect[] = "ORDER BY a.lngauthoritylevel DESC";

		$strSelect = "";
		$strSelect = implode("\n", $arySelect );

		// echo "$strSelect";


		// ������¹�
		$lngResultID = $objDB->execute( $strSelect );


		// ������¹������ξ��
		if( $lngResultID )
		{
			$aryResult[] = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_User
		//---------------------------------------------------------------
		// ���ϼԥ᡼�륢�ɥ쥹�μ���
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;

		list( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );

		// ������¹������ξ��
		if( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag = $objResult->bytmailtransmitflag;
			$strInputUserMailAddress      = $objResult->strmailaddress;
		}
		// ������¹Լ��Ԥξ��
		else
		{
			fncOutputError( 9051, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "po/regist/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// ���ID�����
		$objDB->freeResult( $lngUserMailResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// �᡼������
		//---------------------------------------------------------------
		// �᡼��ʸ�̤�ɬ�פʥǡ��������� $aryMailData �˳�Ǽ
		$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];	// ��ǧ�ԥ᡼�륢�ɥ쥹

/*
fncDebug( 'lib_so.txt',  $aryResult[0]["bytmailtransmitflag"], __FILE__, __LINE__);
fncDebug( 'lib_so.txt',  $aryMailData["strmailaddress"], __FILE__, __LINE__);
fncDebug( 'lib_so.txt',  $strInputUserMailAddress, __FILE__, __LINE__);
*/

		// �᡼���ۿ����ĥե饰�� TRUE �����ꤵ��Ƥ��ʤ���礫�ġ�
		// ���ϼԡʿ����ԡˤΥ᡼�륢�ɥ쥹�����ꤵ��Ƥ��ʤ����ϡ��᡼���������ʤ�
		if( $aryResult[0]["bytmailtransmitflag"] == "t" and $aryMailData["strmailaddress"] != "" and $strInputUserMailAddress != "" )
		{
			$aryMailData                       = array();
			//$strMailAddress                    = $aryResult[0]["strmailaddress"];			// ��ǧ�ԥ᡼�륢�ɥ쥹
			$aryMailData["strmailaddress"]     = $aryResult[0]["strmailaddress"];			// ��ǧ�ԥ᡼�륢�ɥ쥹
			$aryMailData["strWorkflowName"]    = $strworkflowname;							// �Ʒ�̾
			//$aryMailData["strUserDisplayName"] = $aryResult[0]["struserdisplayname"];		// ��ǧ�����
			$aryMailData["strUserDisplayName"] = $objAuth->UserDisplayName;					// ���ϼԡʿ����ԡ�ɽ��̾
			$aryMailData["strURL"]             = LOGIN_URL;									// URL

			// ��ǧ���̾�Υ�å�������᡼�����������Ȥ�������
			$aryMailData["strNote"] = $aryNewData["strWorkflowMessage"];


			// �᡼���å���������
			list( $strSubject, $strTemplate ) = fncGetMailMessage( 807, $aryMailData, $objDB );

			// �����ԥ᡼�륢�ɥ쥹����
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

			// �᡼������
			fncSendMail( $aryMailData["strmailaddress"], $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}

		// Ģɼ����ɽ������
		$aryData["PreviewVisible"] = "hidden";
		//---------------------------------------------------------------
	}



	//-------------------------------------------------------------------------
	// �� ¨��ǧ�ξ��
	//-------------------------------------------------------------------------
	// �ץ�ӥ塼�ܥ����ɽ������
	else
	{
		// Ģɼ�����б�
		// ���¤���äƤʤ����ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
		if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $lngSequence . "&bytCopyFlag=TRUE";

			// Ģɼ����ɽ������
			$aryData["PreviewVisible"] = "visible";
		}
		else
		{
			// Ģɼ����ɽ������
			$aryData["PreviewVisible"] = "hidden";
		}
	}





	//-------------------------------------------------------------------------
	// �� �ȥ�󥶥������λ
	//-------------------------------------------------------------------------
	$objDB->transactionCommit();





	//-------------------------------------------------------------------------
	// �� ����
	//-------------------------------------------------------------------------
	$aryData["strPreviewButton"] = "<br><a href=\"index.php?strSessionID=".$aryData["strSessionID"]."\">���</a>";

	// �ܵҼ����ֹ�
	$aryData["lngCRC"]  = $strCustomerReceiveCode;

	// �����ֹ�
	$aryData["lngPONo"] = $aryNewData["strReceiveCode"] . " - $strReviseCode";

	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/so/regist/index.php?strSessionID=";

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	echo $objTemplate->strTemplate;



	return true;

?>




