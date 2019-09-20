<?php
// ----------------------------------------------------------------------------
/**
*       ����Ǽ�ʽ����Ͽ�ؿ���
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
*         ������Ǽ�ʽ����Ͽ��Ϣ�δؿ�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

function fncGetTaxRatePullDown($dtmDeliveryDate, $objDB)
{
    // DB����ǡ�������
    $strQuery = "SELECT lngtaxcode, curtax "
        . " FROM m_tax "
        . " WHERE dtmapplystartdate <= '$dtmDeliveryDate' "
        . "   AND dtmapplyenddate >= '$dtmDeliveryDate' "
        . " ORDER BY lngpriority ";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "�����Ǿ���μ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    // ������ܺ���
    $strHtml = "";
    for ( $i = 0; $i < count($aryResult); $i++)
	{
        $optionValue =  $aryResult[$i]["lngtaxcode"];
        $displayText =  $aryResult[$i]["curtax"];
        
        if ($i == 0)
        {
            // 1���ܤ�ǥե���Ȥ�����
            $strHtml .= "<OPTION VALUE=\"$optionValue\" SELECTED>$displayText</OPTION>\n";
        }
        else
        {
            $strHtml .= "<OPTION VALUE=\"$optionValue\">$displayText</OPTION>\n";
        }
    }

    return $strHtml;    
}

function fncGetReceiveDetail($aryCondition, $objDB)
{
    // -------------------
    // SELECT��,FROM��
    // -------------------
    $aryQuery[] = " SELECT";
    $aryQuery[] = "  rd.lngsortkey,";
    $aryQuery[] = "  r.strcustomerreceivecode,";
    $aryQuery[] = "  r.strreceivecode,";
    $aryQuery[] = "  p.strgoodscode,";
    $aryQuery[] = "  rd.strproductcode,";
    $aryQuery[] = "  p.strproductname,";
    $aryQuery[] = "  p.strproductenglishname,";
    $aryQuery[] = "  p.lnginchargeusercode,";
    $aryQuery[] = "  u.struserdisplayname as strsalesdeptname,"; //�Ķ������TODO:��Ǥ��Υ����꼫�ν�����
    $aryQuery[] = "  rd.lngsalesclasscode,";
    $aryQuery[] = "  sc.strsalesclassname,";
    $aryQuery[] = "  rd.dtmdeliverydate,";
    $aryQuery[] = "  rd.curproductprice,";
    $aryQuery[] = "  rd.lngproductunitcode,";
    $aryQuery[] = "  pu.strproductunitname,";
    $aryQuery[] = "  rd.lngproductquantity,";
    $aryQuery[] = "  rd.cursubtotalprice,";
    $aryQuery[] = "  rd.lngsortkey";
    $aryQuery[] = " FROM";
    $aryQuery[] = "  t_receivedetail rd ";
    $aryQuery[] = "    LEFT JOIN m_receive r ON rd.lngreceiveno=r.lngreceiveno";
    $aryQuery[] = "    LEFT JOIN m_product p ON rd.strproductcode = p.strproductcode and rd.strrevisecode = p.strrevisecode";
    $aryQuery[] = "    LEFT JOIN m_user u ON p.lnginchargeusercode = u.lngusercode";
    $aryQuery[] = "    LEFT JOIN m_salesclass sc ON rd.lngsalesclasscode = sc.lngsalesclasscode";
    $aryQuery[] = "    LEFT JOIN m_productunit pu ON rd.lngproductunitcode = pu.lngproductunitcode";
  
    // -------------------
    //  WHERE��
    // -------------------
    $aryWhere[] = " WHERE";
    $aryWhere[] = "  r.lngreceivestatuscode = 2";   //�����ơ�����=2:����

    if ($aryCondition["lngReceiveNo"]){
        $aryWhere[] = " AND r.receiveno = " . $aryCondition["lngReceiveNo"];
    }

    if ($aryCondition["strCustomerReceiveCode"]){
        $aryWhere[] = " AND r.strcustomerreceivecode = '" . $aryCondition["strCustomerReceiveCode"] . "'";
    }

    // -------------------
    //  ORDER BY��
    // -------------------
    $aryOrder[] = " ORDER BY";
    $aryOrder[] = "  rd.lngsortkey";
    
    // -------------------
    // ���������
    // -------------------
    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryWhere);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryOrder);

    // -------------------
    // ������¹�
    // -------------------
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    // ��̤�����˳�Ǽ
    unset( $aryResult );
    if ( $lngResultNum )
    {
        for ( $j = 0; $j < $lngResultNum; $j++ )
        {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $j );
        }
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult;
}

function fncGetReceiveDetailHtml($aryReceiveDetail){
    $strHtml = "";
    for($i=0; $i < count($aryReceiveDetail); $i++){
        $strDisplayValue = "";
        $strHtml .= "<tr>";

        //��������å��ܥå���
        $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit'></td>";
        //NO.
        $strHtml .= "<td class='detailSortKey'>" . ($i+1) . "</td>";
        //�ܵ�ȯ���ֹ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //�����ֹ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //�ܵ�����
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //���ʥ�����
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductcode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //����̾
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //����̾�ʱѸ��
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //�Ķ�����
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //����ʬ
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //Ǽ��
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //ñ��
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //ñ��
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //����
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //��ȴ���
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        
        $strHtml .= "</tr>";
    }
    return $strHtml;
}


function fncRegistSales ($aryNewData, $strProcMode, $objDB, $objAuth) {

    // Ʊ���ֻ������ܡסֻ������ʡפξ���Ʊ��ñ�������뤫
    // m_productPrice��Ʊ���ͤ����뤫���ߤä���������˹��ֹ�򵭲�����
    /*
	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

		$lngmonetaryunitcode = "";
		$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
		$strProductCode = "";
		$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "lngproductno", $aryNewData["aryPoDitail"][$i]["strProductCode"]. ":str", '', $objDB );

		$arySelect = array();
		$arySelect[] = "SELECT ";
		$arySelect[] = "lngproductpricecode ";
		$arySelect[] = "FROM ";
		$arySelect[] = "m_productprice ";
		$arySelect[] = "WHERE ";
		$arySelect[] = "lngproductno = $strProductCode AND ";
		$arySelect[] = "lngsalesclasscode = ".$aryNewData["aryPoDitail"][$i]["lngSalesClassCode"]." AND ";
		$arySelect[] = "lngmonetaryunitcode = $lngmonetaryunitcode AND ";
		$arySelect[] = "curproductprice = ".$aryNewData["aryPoDitail"][$i]["curProductPrice"];

		$strSelect = implode("\n", $arySelect );

		if ( $lngResultID = $objDB->execute( $strSelect ) )
		{
			// Ʊ�����ʲ��ʤ����Ĥ���ʤ���硢�⤷����ñ�̷׾夬����ñ�̷׾�ξ��Τ߹��ֹ�򵭲�����
			if( pg_num_rows( $lngResultID ) == 0 and $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" )
			{
				$aryM_ProductPrice[] = $i;		//���ֹ�򵭲�
			}
		}
		$objDB->freeResult( $lngResultID );

    }
    //������ʪ��$aryM_ProductPrice
    */

    //-------------------------------------------------------------------------
	// m_Sales �Υ��������ֹ�����
	//-------------------------------------------------------------------------
    $sequence_m_sales = fncGetSequence( 'm_sales.lngSalesNo', $objDB );
    
    // //-------------------------------------------------------------------------
	// // ����ɳ�Ť�����ǡ����μ���
	// //-------------------------------------------------------------------------
	// // �����ֹ�
	// $strReceiveCode = $aryNewData["strReceiveCode"];

	// if( $strReceiveCode != "null" )
	// {
	// 	$aryQuery = array();
	// 	$aryQuery[] = "SELECT "; 
	// 	$aryQuery[] = "r.lngReceiveNo, ";										// 1:�����ֹ�
	// 	$aryQuery[] = "r.lngReceiveStatusCode as lngSalesStatusCode ";			// 9:������֥�����
	// 	$aryQuery[] = "FROM m_Receive r ";
	// 	$aryQuery[] = "WHERE r.strReceiveCode = '". $strReceiveCode . "' ";
	// 	$aryQuery[] = "AND r.bytInvalidFlag = FALSE ";
	// 	$aryQuery[] = "AND r.lngRevisionNo >= 0 ";
	// 	$aryQuery[] = "AND r.lngRevisionNo = ( ";
	// 	$aryQuery[] = "SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode  ";
	// 	$aryQuery[] = "AND r2.strReviseCode = ( ";
	// 	$aryQuery[] = "SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) ) ";
	// 	$aryQuery[] = "AND 0 <= ( ";
	// 	$aryQuery[] = "SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";

	// 	$strQuery = "";
	// 	$strQuery = implode( "\n", $aryQuery );

	// 	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	// 	if ( $lngResultNum == 1 )
	// 	{
	// 		$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
	// 	}
	// 	// ���ꤵ�줿ȯ��¸�ߤ��ʤ����
	// 	else
	// 	{
	// 		fncOutputError ( 403, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
	// 	}
	// 	$objDB->freeResult( $lngResultID );

	// 	$lngReceiveCode = $aryReceiveResult["lngreceiveno"];
	// }
	// else
	// {
	// 	$lngReceiveCode = "null";
    // }

	//-------------------------------------------------------------------------
	// ���ޥ�������Ͽ�����ͤμ���
	//-------------------------------------------------------------------------
    // ��������
    $dtmNowDate     = date( 'Y/m/d', time() );  
    // TODO:�ܵҥ����ɤ����
    $lngCustomerCompanyCode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );
    // TODO:���롼�ץ����ɤ����
    $lngInChargeGroupCode = "";
    // TODO:�桼���������ɤ����
    $lngInChargeUserCode = "";
    // TODO:�̲�ñ�̥����ɤμ���
    $lngMonetaryUnitCode = ""; //fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
    // TODO:�̲ߥ졼�ȥ����ɤμ���
    $lngMonetaryRateCode = "";
    // TODO:�����졼�Ȥμ���
    $curConversionRate = "";
    // �����֥����ɤμ���
    $lngSalesStatusCode = DEF_SALES_ORDER;
    // TODO:��׶�ۤμ���
    $curAllTotalPrice = 0;
	// ����
    $strNote = ( $aryNewData["strNote"] != "null" ) ? "'".$aryNewData["strNote"]."'" : "null";
    // ���ϼԥ�����
    $lngInputUserCode = $objAuth->UserCode;

    if ($strProcMode == "new-record"){
    // �� �����⡼�ɤ�����Ͽ�פξ��
    
        // ��ӥ�����ֹ������
		$lngrevisionno = 0;
        
        // ���������ֹ�μ���
		$strsalsecode = fncGetDateSequence( date( 'Y', strtotime( $dtmNowDate ) ), date( 'm',strtotime( $dtmNowDate ) ), "m_sales.lngSalesNo", $objDB );
        
        // TODO:��ɼ�ֹ�μ���
        $strSlipCode = "";

    } else if ($strProcMode == "modify-record"){
    // �� �����⡼�ɤ��ֽ����פξ��
        // TODO:��¸��ӥ�����ֹ�+1�Ǻ����ͤ����
        $lngrevisionno = 999;

        // TODO:�����оݥ쥳���ɤ�ɳ�Ť�����ֹ�μ���
        $strsalsecode = "";
    
        // TODO:��ɼ�ֹ�μ���
        $strSlipCode = "";
    }

	//-------------------------------------------------------------------------
	// ���ޥ�����Ͽ������m_sales�ؤ�INSERT��
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_sales ( ";
	$aryQuery[] = "lngsalesno, ";											// 1:����ֹ�
	$aryQuery[] = "lngrevisionno, ";										// 2:��ӥ�����ֹ�
	$aryQuery[] = "strsalescode, ";											// 3:��女����(yymmxxx ǯ��Ϣ�֤ǹ������줿7����ֹ�)
	$aryQuery[] = "dtmappropriationdate, ";									// 4:�׾���
	$aryQuery[] = "lngcustomercompanycode, ";								// 5:�ܵ�
	$aryQuery[] = "lnggroupcode, ";									    	// 6:���롼�ץ�����
	$aryQuery[] = "lngusercode, ";										    // 7:�桼��������
	$aryQuery[] = "lngsalesstatuscode, ";									// 8:�����֥�����
	$aryQuery[] = "lngmonetaryunitcode, ";									// 9:�̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode, ";									// 10:�̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";									// 11:�����졼��
	$aryQuery[] = "strslipcode, ";											// 12:Ǽ�ʽ�NO 
	$aryQuery[] = "lnginvoiceno, ";											// 13:������ֹ�
	$aryQuery[] = "curtotalprice, ";										// 14:��׶��
	$aryQuery[] = "strnote, ";												// 15:����
	$aryQuery[] = "lnginputusercode, ";										// 16:���ϼԥ�����
	$aryQuery[] = "bytinvalidflag, ";										// 17:̵���ե饰
	$aryQuery[] = "dtminsertdate";											// 18:��Ͽ��
	$aryQuery[] = " ) values ( ";
	$aryQuery[] = "$sequence_m_sales,";										// 1:����ֹ�
	$aryQuery[] = "$lngrevisionno, ";										// 2:��ӥ�����ֹ�
	$aryQuery[] = "'$strsalsecode', ";										// 3:��女����
	$aryQuery[] = "'".$dtmNowDate."',";										// 4:�׾���
	$aryQuery[] = $lngCustomerCompanyCode.", ";						        // 5:�ܵҥ�����
	$aryQuery[] = $lngInChargeGroupCode.", ";								// 6:���롼�ץ�����
	$aryQuery[] = $lngInChargeUserCode.", ";								// 7:�桼��������
	$aryQuery[] = $lngSalesStatusCode . ", ";								// 8:�����֥�����
	$aryQuery[] = "$lngMonetaryUnitCode, ";									// 9:�̲�ñ�̥�����
	$aryQuery[] = $lngMonetaryRateCode.", ";					            // 10:�̲ߥ졼�ȥ�����
	$aryQuery[] = "'".$curConversionRate."', ";				                // 11:�����졼��
    $aryQuery[] = "$strSlipCode, ";											// 12:Ǽ�ʽ�NO
    $aryQuery[] = "null, ";													// 13:������ֹ�
	$aryQuery[] = "'".$curAllTotalPrice."', ";								// 14:��׶��
	$aryQuery[] = "$strNote, ";												// 15:����
	$aryQuery[] = "$lngInputUserCode, ";									// 16:���ϼԥ�����
	$aryQuery[] = "false, ";												// 17:̵���ե饰
	$aryQuery[] = "now() ";													// 18:��Ͽ��
	$aryQuery[] = ")";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$objDB->freeResult( $lngResultID );
}




?>
