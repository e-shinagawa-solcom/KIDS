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
    //  �������
    // -------------------
    $arySelect[] = " SELECT";
    $arySelect[] = "  rd.lngsortkey,";                             //No.
    $arySelect[] = "  r.strcustomerreceivecode,";                  //�ܵҼ����ֹ�
    $arySelect[] = "  r.strreceivecode,";                          //�����ֹ�
    $arySelect[] = "  p2.strgoodscode,";                           //�ܵ�����
    $arySelect[] = "  rd.strproductcode,";                         //���ʥ�����
    $arySelect[] = "  rd.strrevisecode,";                          //��Х��������ɡʺ��Υ����ɡ�
    $arySelect[] = "  p.strproductname,";                          //����̾
    $arySelect[] = "  p.strproductenglishname,";                   //����̾�ʱѸ��
    $arySelect[] = "  g.strgroupdisplayname as strsalesdeptname,"; //�Ķ������̾�Ρ�
    $arySelect[] = "  rd.lngsalesclasscode,";                      //����ʬ������
    $arySelect[] = "  sc.strsalesclassname,";                      //����ʬ��̾�Ρ�
    $arySelect[] = "  rd.dtmdeliverydate,";                        //Ǽ��
    $arySelect[] = "  rd.lngunitquantity,";                        //����
    $arySelect[] = "  rd.curproductprice,";                        //ñ��
    $arySelect[] = "  rd.lngproductunitcode,";                     //ñ�̥�����
    $arySelect[] = "  pu.strproductunitname,";                     //ñ�̡�̾�Ρ�
    $arySelect[] = "  rd.lngproductquantity,";                     //����
    $arySelect[] = "  rd.cursubtotalprice,";                       //��ȴ���
    $arySelect[] = "  rd.lngreceiveno,";                           //�����ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.lngreceivedetailno,";                     //���������ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.lngrevisionno,";                          //��ӥ�����ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.strnote,";                                //���͡�������Ͽ�ѡ�
    $arySelect[] = "  r.lngmonetaryunitcode,";                     //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
    $arySelect[] = "  r.lngmonetaryratecode,";                     //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
    $arySelect[] = "  mu.strmonetaryunitsign";                     //�̲�ñ�̵����������Ͽ�ѡ�
    $arySelect[] = " FROM";
    $arySelect[] = "  t_receivedetail rd ";
    $arySelect[] = "    LEFT JOIN m_receive r ON rd.lngreceiveno=r.lngreceiveno";
    $arySelect[] = "    LEFT JOIN m_company c ON r.lngcustomercompanycode = c.lngcompanycode";
    $arySelect[] = "    LEFT JOIN m_product p ON rd.strproductcode = p.strproductcode";
    $arySelect[] = "    LEFT JOIN m_salesclass sc ON rd.lngsalesclasscode = sc.lngsalesclasscode";
    $arySelect[] = "    LEFT JOIN m_productunit pu ON rd.lngproductunitcode = pu.lngproductunitcode";
    $arySelect[] = "    LEFT JOIN m_product p2 ON rd.strproductcode = p2.strproductcode and rd.strrevisecode = p2.strrevisecode";
    $arySelect[] = "    LEFT JOIN m_group g ON p2.lnginchargegroupcode = g.lnggroupcode";
    $arySelect[] = "    LEFT JOIN m_monetaryunit mu ON r.lngmonetaryunitcode = mu.lngmonetaryunitcode";
  
    // -------------------
    //  �����������
    // -------------------
    $aryWhere[] = " WHERE";
    $aryWhere[] = "  r.lngreceivestatuscode = 2";   //�����ơ�����=2:����

    // �ܵҡʥ����ɤǸ�����
    if ($aryCondition["strCompanyDisplayCode"]){
        $aryWhere[] = " AND c.strcompanydisplaycode = '" . $aryCondition["strCompanyDisplayCode"] . "'";
    }    

    // �ܵҼ����ֹ�
    if ($aryCondition["strCustomerReceiveCode"]){
        $aryWhere[] = " AND r.strcustomerreceivecode = '" . $aryCondition["strCustomerReceiveCode"] . "'";
    }

    // �����ֹ�
    if ($aryCondition["lngReceiveNo"]){
        $aryWhere[] = " AND r.lngreceiveno = " . $aryCondition["lngReceiveNo"];
    }

    // ���ʥ�����
    if ($aryCondition["strReceiveDetailProductCode"]){
        $aryWhere[] = " AND rd.strproductcode = '" . $aryCondition["strReceiveDetailProductCode"] ."'";
    }
    
    // �Ķ�����ʥ����ɤǸ�����
    if ($aryCondition["lngInChargeGroupCode"]){
        $aryWhere[] = " AND g.lnggroupcode = " . $aryCondition["lngInChargeGroupCode"];
    }

    // ����ʬ�ʥ����ɤǸ�����
    if ($aryCondition["lngSalesClassCode"]){
        $aryWhere[] = " AND rd.lngsalesclasscode = " . $aryCondition["lngSalesClassCode"];
    }

    // �ܵ�����
    if ($aryCondition["strGoodsCode"]){
        $aryWhere[] = " AND p2.strgoodscode = " . $aryCondition["strGoodsCode"];
    }

    // Ǽ����(FROM)
    if ( $aryCondition["From_dtmDeliveryDate"] )
    {
        $dtmSearchDate = $aryCondition["From_dtmDeliveryDate"] . " 00:00:00";
        $aryWhere[] = " AND rd.dtmdeliverydate >= '" . $dtmSearchDate . "'";
    }

    // Ǽ����(TO)
    if ( $aryCondition["To_dtmDeliveryDate"] )
    {
        $dtmSearchDate = $aryCondition["To_dtmDeliveryDate"] . " 23:59:59";
        $aryWhere[] = " AND rd.dtmdeliverydate <= '" . $dtmSearchDate . "'";
    }

    // ��������
    if ( $aryCondition["strNote"] )
    {
        $aryWhere[] = " AND rd.strNote LIKE '%" . $aryCondition["strNote"] . "%'";
    }
    
    // ���Τ�ޤ��off�ξ�硢t_receivedetail.strrevisecode='00'�Τߤ��оݡ�
    if ( $aryCondition["IsIncludingResale"] != "true")
    {
        $aryWhere[] = " AND rd.strrevisecode = '00'";
    }

    // -------------------
    //  �¤ӽ����
    // -------------------
    $aryOrder[] = " ORDER BY";
    $aryOrder[] = "  rd.lngsortkey";
    
    // -------------------
    // ���������
    // -------------------
    $strQuery = "";
    $strQuery .= implode("\n", $arySelect);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryWhere);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryOrder);

    // -------------------
    // ������¹�
    // -------------------
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    // ��̤�����˳�Ǽ
    $aryResult = [];    //��������ǽ����
    if ( 0 < $lngResultNum )
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
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngsortkey"]);
        $strHtml .= "<td class='detailSortKey'>" . $strDisplayValue . "</td>";
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
        $strDisplayValue .= "_";
        $strDisplayValue .= htmlspecialchars($aryReceiveDetail[$i]["strrevisecode"]);
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
        //����
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngunitquantity"]);
        $strHtml .= "<td class='detailUnitQuantity'>" . $strDisplayValue . "</td>";
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
        //�����ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngreceiveno"]);
        $strHtml .= "<td class='forEdit detailReceiveNo'>" . $strDisplayValue . "</td>";
        //���������ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngreceivedetailno"]);
        $strHtml .= "<td class='forEdit detailReceiveDetailNo'>" . $strDisplayValue . "</td>";
        //��ӥ�����ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngrevisionno"]);
        $strHtml .= "<td class='forEdit detailRevisionNo'>" . $strDisplayValue . "</td>";
        //���Υ����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='forEdit detailReviseCode'>" . $strDisplayValue . "</td>";
        //����ʬ�����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngsalesclasscode"]);
        $strHtml .= "<td class='forEdit detailSalesClassCode'>" . $strDisplayValue . "</td>";
        //����ñ�̥����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class='forEdit detailProductUnitCode'>" . $strDisplayValue . "</td>";
        //���͡�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strnote"]);
        $strHtml .= "<td class='forEdit detailNote'>" . $strDisplayValue . "</td>";
        //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitCode'>" . $strDisplayValue . "</td>";
        //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngmonetaryratecode"]);
        $strHtml .= "<td class='forEdit detailMonetaryRateCode'>" . $strDisplayValue . "</td>";
        //�̲�ñ�̵����������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strmonetaryunitsign"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitSign'>" . $strDisplayValue . "</td>";
        
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
