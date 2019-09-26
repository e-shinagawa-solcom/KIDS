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

// ������Ψ�ץ�������������ܺ���
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
    $arySelect[] = "  rd.lngrevisionno as lngreceiverevisionno,";  //��ӥ�����ֹ��������Ͽ�ѡ�
    $arySelect[] = "  rd.strnote,";                                //���͡�������Ͽ�ѡ�
    $arySelect[] = "  r.lngmonetaryunitcode,";                     //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
    $arySelect[] = "  r.lngmonetaryratecode,";                     //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
    $arySelect[] = "  mu.strmonetaryunitsign,";                    //�̲�ñ�̵����������Ͽ�ѡ�
    $arySelect[] = "  sc.bytdetailunifiedflg";                     //��������ե饰��������Ͽ�ѡ�
    $arySelect[] = " FROM";
    $arySelect[] = "  t_receivedetail rd ";
    $arySelect[] = "    INNER JOIN m_receive r ON rd.lngreceiveno=r.lngreceiveno AND rd.lngrevisionno = r.lngrevisionno";
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

function fncGetReceiveDetailHtml($aryDetail){
    $strHtml = "";
    for($i=0; $i < count($aryDetail); $i++){
        $strDisplayValue = "";
        $strHtml .= "<tr>";

        //��������å��ܥå���
        $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit'></td>";
        //NO.
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngsortkey"]);
        $strHtml .= "<td class='detailSortKey'>" . $strDisplayValue . "</td>";
        //�ܵ�ȯ���ֹ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //�����ֹ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //�ܵ�����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //���ʥ�����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductcode"]);
        $strDisplayValue .= "_";
        $strDisplayValue .= htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //����̾
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //����̾�ʱѸ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //�Ķ�����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //����ʬ
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //Ǽ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngunitquantity"]);
        $strHtml .= "<td class='detailUnitQuantity'>" . $strDisplayValue . "</td>";
        //ñ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //ñ��
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //����
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //��ȴ���
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //�����ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiveno"]);
        $strHtml .= "<td class='forEdit detailReceiveNo'>" . $strDisplayValue . "</td>";
        //���������ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceivedetailno"]);
        $strHtml .= "<td class='forEdit detailReceiveDetailNo'>" . $strDisplayValue . "</td>";
        //��ӥ�����ֹ��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiverevisionno"]);
        $strHtml .= "<td class='forEdit detailReceiveRevisionNo'>" . $strDisplayValue . "</td>";
        //���Υ����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='forEdit detailReviseCode'>" . $strDisplayValue . "</td>";
        //����ʬ�����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngsalesclasscode"]);
        $strHtml .= "<td class='forEdit detailSalesClassCode'>" . $strDisplayValue . "</td>";
        //����ñ�̥����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class='forEdit detailProductUnitCode'>" . $strDisplayValue . "</td>";
        //���͡�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strnote"]);
        $strHtml .= "<td class='forEdit detailNote'>" . $strDisplayValue . "</td>";
        //�̲�ñ�̥����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitCode'>" . $strDisplayValue . "</td>";
        //�̲ߥ졼�ȥ����ɡ�������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryratecode"]);
        $strHtml .= "<td class='forEdit detailMonetaryRateCode'>" . $strDisplayValue . "</td>";
        //�̲�ñ�̵����������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strmonetaryunitsign"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitSign'>" . $strDisplayValue . "</td>";
        //��������ե饰��������Ͽ�ѡ�
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["bytdetailunifiedflg"]);
        $strHtml .= "<td class='forEdit detailUnifiedFlg'>" . $strDisplayValue . "</td>";
        
        $strHtml .= "</tr>";
    }
    return $strHtml;
}

// Ǽ����ɼ�ޥ����������������
function fncGetSlipInsertDate($strSlipCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  to_char(dtminsertdate, 'yyyy/mm/dd hh24:mm:ss') as dtminsertdate"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode = '". $strSlipCode ."'"
    ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "Ǽ����ɼ�κ������μ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult[0]["dtminsertdate"];
}

function fncNotReceivedDetailExists($aryDetail, $objDB)
{
    for ( $i = 0; $i < count($aryDetail); $i++ )
    {
        $d = $aryDetail[$i];

        $lngReceiveNo = $d["lngreceiveno"];
        $lngRevisionNo = $d["lngreceiverevisionno"];

        $strQuery = ""
        . "SELECT"
        . "  lngreceivestatuscode"
        . " FROM"
        . "  m_receive"
        . " WHERE"
        . "  lngreceivestatuscode <> 2"
        . "  AND lngreceiveno = " . $lngReceiveNo
        . "  AND lngrevisionno = " . $lngRevisionNo
        ;

        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
        if ( $lngResultID ) {
            // ������֥����ɤ�2�ʳ������٤�¸�ߤ���ʤ�true���֤��Ƹ����Ǥ��ڤ�
            if ($lngResultNum > 0) { return true; }
        } else {
            fncOutputError ( 9501, DEF_FATAL, "������֥����ɤμ����˼���", TRUE, "", $objDB );
        }
        $objDB->freeResult( $lngResultID );
    }

    // ������֥����ɤ�2�ʳ������٤�¸�ߤ��ʤ�
    return false;

}

// ���٤�ɳ�Ť�����ޥ����μ�����֥����ɤ򹹿�
function fncUpdateReceiveMaster($aryDetail, $objDB)
{
    for ( $i = 0; $i < count($aryDetail); $i++ )
    {
        $d = $aryDetail[$i];

        $lngReceiveNo = $d["lngreceiveno"];
        $lngRevisionNo = $d["lngreceiverevisionno"];

        $strQuery = ""
        . "UPDATE"
        . "  m_receive"
        . " SET"
        . "  lngreceivestatuscode = 4"
        . " WHERE"
        . "  lngreceiveno = " . $lngReceiveNo
        . "  AND lngrevisionno = " . $lngRevisionNo
        ;

        // �����¹�
        if ( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "����ޥ����������ԡ�", TRUE, "", $objDB );
            // ����
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

    // ����
    return true;
}

// ɽ���Ѳ�ҥ����ɤ����ҥ����ɤ��������
function fncGetNumericCompanyCode($strCompanyDisplayCode, $objDB)
{
    $lngCompanyCode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $strCompanyDisplayCode.":str", '', $objDB );
    return $lngCompanyCode;
}

// ɽ���Ѳ�ҥ����ɤ���񥳡��ɤ��������
function fncGetCountryCode($strCompanyDisplayCode, $objDB)
{
    $lngCountryCode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcountrycode", "$strCompanyDisplayCode:str", '', $objDB);
    return $lngCountryCode;
}

// ɽ���Ѳ�ҥ����ɤ������������������
function fncGetClosedDay($strCompanyDisplayCode, $objDB)
{
    $strQuery = ""
    . "SELECT"
    . "  cd.lngclosedday"
    . " FROM"
    . "  m_company c "
    . "    INNER JOIN m_closedday cd "
    . "    on c.lngcloseddaycode = cd.lngcloseddaycode"
    . " WHERE"
    . "  c.strcompanydisplaycode = " . withQuote($strCompanyDisplayCode)
    ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "�������μ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult[0]["lngclosedday"];    
}


// ɽ���ѥ桼���������ɤ���桼���������ɤ��������
function fncGetNumericUserCode($strUserDisplayCode, $objDB)
{
    $lngUserCode = fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $strUserDisplayCode.":str", '', $objDB );
    return $lngUserCode;
}



// ��ҥ����ɤ�ɳ�Ť�Ģɼ��ɼ���̤����
function fncGetSlipKindByCompanyCode($lngCompanyCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  c.lngcompanycode,"
        . "  c.strcompanydisplaycode,"
        . "  c.strcompanydisplayname,"
        . "  sk.lngslipkindcode,"
        . "  sk.strslipkindname,"
        . "  sk.lngmaxline"
        . " FROM m_slipkindrelation skr"
        . "   LEFT JOIN m_slipkind sk ON skr.lngslipkindcode = sk.lngslipkindcode"
        . "   LEFT JOIN m_company c ON skr.lngcompanycode = c.lngcompanycode"
        . " WHERE c.lngcompanycode = ".$lngCompanyCode
        ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "Ģɼ��ɼ���̤μ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult[0];    
}

// ��ҥ����ɤ�ɳ�Ť���Ҿ�������
function fncGetCompanyInfoByCompanyCode($lngCompanyCode, $objDB)
{
    $strQuery = ""
        . "SELECT "
        . "  c.lngcompanycode,"
        . "  c.strcompanydisplaycode, "
        . "  c.strcompanydisplayname,"
        . "  c.straddress1,"
        . "  c.straddress2,"
        . "  c.straddress3,"
        . "  c.straddress4,"
        . "  c.strtel1,"
        . "  c.strfax1,"
        . "  sc.strstockcompanycode,"
        . "  cp.strprintcompanyname,"
        . "  c.strcompanyname,"
        . "  c.bytorganizationfront,"
        . "  o.lngorganizationcode,"
        . "  o.strorganizationname"
        . " FROM m_company c"
        . "  LEFT JOIN m_stockcompanycode sc ON c.lngcompanycode = sc.lngcompanyno"
        . "  LEFT JOIN m_companyprintname cp ON c.lngcompanycode = cp.lngcompanycode"
        . "  LEFT JOIN m_organization o ON c.lngorganizationcode = o.lngorganizationcode"
        . " WHERE c.lngcompanycode = ".$lngCompanyCode
    ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "��Ҿ���μ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult[0];    
}

// �ܵҼ�̾�����
function fncGetCustomerCompanyName($lngCountryCode, $aryCompanyInfo)
{
    if (strlen($aryCompanyInfo["strprintcompanyname"]) != 0)
    {
        return $aryCompanyInfo["strprintcompanyname"];
    }

    // Ģɼ�Ѳ��̾����
    if ($lngCountryCode != 81)
    {
        return $aryCompanyInfo["strcompanyname"];
    }
    else if ($aryCompanyInfo["bytorganizationfront"] == TRUE)
    {
        return $aryCompanyInfo["strorganizationname"] . $aryCompanyInfo["strcompanyname"];
    }
    else 
    {
        return $aryCompanyInfo["strcompanyname"] . $aryCompanyInfo["strorganizationname"];
    }
}

// �ܵ�̾�����
function fncGetCustomerName($aryCompanyInfo)
{
    if (strlen($aryCompanyInfo["strprintcompanyname"]) != 0)
    {
        return $aryCompanyInfo["strprintcompanyname"];
    }
    else{
        return null;
    }
}

// �桼���������ɤ�ɳ�Ť��桼������������
function fncGetUserInfoByUserCode($lngUserCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  u.lngusercode,"
        . "  u.struserdisplaycode,"
        . "  u.struserdisplayname,"
        . "  gr.lnggroupcode"
        . " FROM m_user u"
        . "  LEFT JOIN (select * from m_grouprelation WHERE bytdefaultflag=TRUE) gr ON u.lngusercode = gr.lngusercode "
        . " WHERE u.lngusercode=".$lngUserCode
    ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "�桼��������μ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult[0];    
}

// ����ǡ�����ɳ�Ť������졼�Ȥ����
function fncGetConversionRateByReceiveData($lngReceiveNo, $lngReceiveRevisionNo, $dtmAppropriationDate, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  r.lngreceiveno,"
        . "  r.lngmonetaryunitcode,"
        . "  r.lngmonetaryratecode,"
        . "  mr.curconversionrate,"
        . "  mr.dtmapplystartdate,"
        . "  mr.dtmapplyenddate"
        . " FROM m_receive r"
        . "  LEFT JOIN (select distinct * from m_monetaryrate "
        . "             where dtmapplystartdate<='".$dtmAppropriationDate."' and '".$dtmAppropriationDate."'<=dtmapplyenddate) mr "
        . "   ON r.lngmonetaryunitcode = mr.lngmonetaryunitcode AND r.lngmonetaryratecode = mr.lngmonetaryratecode"
        . " WHERE r.lngreceiveno=".$lngReceiveNo." AND r.lngrevisionno = ".$lngReceiveRevisionNo
    ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "�����졼�Ȥμ����˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult[0];    
}

// Ǽ�ʽ�NO��ȯ��
function fncPublishSlipCode($dtmPublishDate, $objDB)
{
    $strYYYYMM = substr($dtmPublishDate, 0, 4) . substr($dtmPublishDate, 5, 2);

    $strQuery = ""
        . "SELECT"
        . "  MAX(strslipcode) as yyyymmnn,"
        . "  SUBSTR(MAX(strslipcode),7,8) as nn"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode IS NOT NULL "
        . "  AND LENGTH(strslipcode) = 8"
        . "  AND strslipcode LIKE '".$strYYYYMM."__'"
    ;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "Ǽ�ʽ�NO��ȯ�Ԥ˼���", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    // Ǽ�ʽ�NO������
    if ($lngResultNum != 0){
        $lngNumber = intval($aryResult[0]["nn"]);
        $lngNumber += 1;
    }else{
        // ����1���ܤ� nn='01' �ǳ���
        $lngNumber = 1;
    }

    $strNN = sprintf("%02d", $lngNumber);
    $strPublishdSlipCode = $strYYYYMM . $strNN;

    return $strPublishdSlipCode;    
}

// �����ǳۤη׻�
function fncCalcTaxPrice($curPrice, $lngTaxClassCode, $lngTaxRate)
{
    $curTaxPrice = 0;

    if ($lngTaxClassCode == "1")
    {
        // 1:�����
        $curTaxPrice = 0;
    }
    else if ($lngTaxClassCode == "2")
    {
        // 2:����
        $curTaxPrice = floor($curPrice * $lngTaxRate);
    }
    else if ($lngTaxClassCode == "3")
    {
        // 3:����
        $curTaxPrice = floor( ($curPrice / (1+$lngTaxRate)) * $lngTaxRate );
    }

    return $curTaxPrice;
}

// --------------------------------
// 
// ����Ǽ�ʽ����Ͽ�ᥤ��ؿ�
// 
// --------------------------------
function fncRegisterSalesAndSlip($aryHeader, $aryDetail, $objDB, $objAuth)
{
    // ����ͤν����
    $aryRegisterResult = array();
    $aryRegisterResult["result"] = false;
    $aryRegisterResult["strSlipCode"] = array();

    // ��������
    $dtmNowDate = date( 'Y/m/d', time() );
    // �׾���
    $dtmAppropriationDate = $dtmNowDate;
    // �ܵҤβ�ҥ����ɤ����
    $lngCustomerCompanyCode = fncGetNumericCompanyCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҤβ�ҥ����ɤ�ɳ�Ť���Ҿ�������
    $aryCustomerCompany = fncGetCompanyInfoByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �����졼�Ȥμ���
    $aryConversionRate = fncGetConversionRateByReceiveData($aryDetail[0]["lngreceiveno"], $aryDetail[0]["lngreceiverevisionno"], $dtmAppropriationDate, $objDB);
    
    // ��ɼ�Ԥ�ɳ�Ť��桼������������
    $lngDrafterUserCode = fncGetNumericUserCode($aryHeader["strdrafteruserdisplaycode"], $objDB);
    $aryDrafter = fncGetUserInfoByUserCode($lngDrafterUserCode, $objDB);

    // �ܵҤι񥳡��ɤ����
    $lngCustomerCountryCode = fncGetCountryCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҼ�̾�μ���
    $strCustomerCompanyName = fncGetCustomerCompanyName($lngCustomerCountryCode, $aryCustomerCompany);
    // �ܵ�̾�μ���
    $strCustomerName = fncGetCustomerName($aryCustomerCompany);
    // Ǽ����β�ҥ����ɤμ���
    $lngDeliveryPlaceCode = fncGetNumericCompanyCode($aryHeader["strdeliveryplacecompanydisplaycode"], $objDB);

    // �ܵҤβ�ҥ����ɤ�ɳ�Ť�Ǽ����ɼ���̤����
    $aryReport = fncGetSlipKindByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �ܵҤ�ɳ�Ť�Ģɼ1�ڡ���������κ������ٿ����������
    $maxItemPerPage = intval($aryReport["lngmaxline"]);
    // ��Ͽ���������٤ο�
    $totalItemCount = count($aryDetail);
    // ����ڡ������η׻�
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);

    // �ڡ���ñ�̤ǤΥǡ�����Ͽ
    for ( $page = 1; $page <= $maxPageCount; $page++ ){

        // ���ߤΥڡ�������1�ڡ�������������ٿ����顢
        // ��Ͽ�������٤Υ���ǥå����κǾ��ͤȺ����ͤ����
        $itemMinIndex = ($page-1) * $maxItemPerPage ;
        $itemMaxIndex = $page * $maxItemPerPage - 1;
        if ($itemMaxIndex > $totalItemCount - 1){
            $itemMaxIndex = $totalItemCount - 1;
        }

        // ����ֹ�򥷡����󥹤��ȯ��
        $lngSalesNo = fncGetSequence( 'm_sales.lngSalesNo', $objDB );
        
        // ������Ͽ������ӥ�����ֹ��0����
        $lngRevisionNo = 0;

        // �����ɳ�Ť���女���ɤ�ȯ��
        $strSalesCode = fncGetDateSequence( date( 'Y', strtotime( $dtmNowDate ) ), 
                                            date( 'm',strtotime( $dtmNowDate ) ), "m_sales.lngSalesNo", $objDB );

        // ������ɳ�Ť�Ǽ����ɼ�����ɤ�ȯ��
        $strSlipCode = fncPublishSlipCode($dtmNowDate, $objDB);
        $aryRegisterResult["strSlipCode"][] = $strSlipCode;

        // Ǽ����ɼ�ֹ�򥷡����󥹤��ȯ��
        $lngSlipNo = fncGetSequence( 'm_Slip.lngSlipNo', $objDB );

        // ���ޥ�����Ͽ
        if (!fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $dtmAppropriationDate, $aryConversionRate, $aryCustomerCompany, $aryDrafter,
                $aryHeader , $aryDetail, $objDB, $objAuth))
        {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // ���������Ͽ
        if (!fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo,
                $aryHeader , $aryDetail, $objDB, $objAuth))
        {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // Ǽ����ɼ�ޥ�����Ͽ
        if (!fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $strCustomerCompanyName, $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
                $aryHeader , $aryDetail, $objDB, $objAuth))
        {
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }
    
        // Ǽ����ɼ������Ͽ
        if (!fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo,
                $aryHeader, $aryDetail, $objDB, $objAuth)){
            // ����
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

    }

    // ����
    $aryRegisterResult["result"] = true;
    return $aryRegisterResult;
}

// --------------------------------
// �ѥ�᡼���Х�����ѥإ�Ѵؿ�
// --------------------------------
// ���󥰥륯�����ȤǰϤ�
function withQuote($source)
{
    return "'" . $source . "'";
}


// ���ޥ�����Ͽ
function fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $dtmAppropriationDate, $aryConversionRate, $aryCustomerCompany, $aryDrafter,
     $aryHeader , $aryDetail, $objDB, $objAuth)
{
    // �����졼�Ȥ�����
    if (strlen($aryConversionRate["curconversionrate"]) == 0){
        $curConversionRate = "Null";
    }else{
        $curConversionRate = $aryConversionRate["curconversionrate"];
    }

    // ��Ͽ�ǡ����Υ��å�
    $v_lngsalesno = $lngSalesNo;                                        //1:����ֹ�
    $v_lngrevisionno = $lngRevisionNo;                                  //2:��ӥ�����ֹ�
    $v_strsalescode = withQuote($strSalesCode);                         //3:��女����
    $v_dtmappropriationdate = withQuote($dtmAppropriationDate);         //4:�׾���
    $v_lngcustomercompanycode = $aryCustomerCompany["lngcompanycode"];  //5:�ܵҥ�����
    $v_lnggroupcode = $aryDrafter["lnggroupcode"];                      //6:���롼�ץ�����
    $v_lngusercode = $aryDrafter["lngusercode"];                        //7:�桼��������
    $v_lngsalesstatuscode = "4";                                        //8:�����֥�����
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"];      //9:�̲�ñ�̥�����
    $v_lngmonetaryratecode = $aryDetail[0]["lngmonetaryratecode"];      //10:�̲ߥ졼�ȥ�����
    $v_curconversionrate = $curConversionRate;                          //11:�����졼��
    $v_strslipcode = withQuote($strSlipCode);                           //12:Ǽ�ʽ�NO
    $v_lnginvoiceno = "Null";                                           //13:������ֹ�
    $v_curtotalprice = $aryHeader["curtotalprice"];                     //14:��׶��
    $v_strnote = withQuote($aryHeader["strnote"]);                      //15:����
    $v_lnginputusercode = $objAuth->UserCode;                           //16:���ϼԥ�����
    $v_bytinvalidflag = "FALSE";                                        //17:̵���ե饰
    $v_dtminsertdate = "now()";                                         //18:��Ͽ��

    // ��Ͽ���������
    $aryInsert = [];
    $aryInsert[] = "INSERT  ";
    $aryInsert[] = "INTO m_sales(  ";
    $aryInsert[] = "  lngsalesno ";                      //1:����ֹ�
    $aryInsert[] = "  , lngrevisionno ";                 //2:��ӥ�����ֹ�
    $aryInsert[] = "  , strsalescode ";                  //3:��女����
    $aryInsert[] = "  , dtmappropriationdate ";          //4:�׾���
    $aryInsert[] = "  , lngcustomercompanycode ";        //5:�ܵҥ�����
    $aryInsert[] = "  , lnggroupcode ";                  //6:���롼�ץ�����
    $aryInsert[] = "  , lngusercode ";                   //7:�桼��������
    $aryInsert[] = "  , lngsalesstatuscode ";            //8:�����֥�����
    $aryInsert[] = "  , lngmonetaryunitcode ";           //9:�̲�ñ�̥�����
    $aryInsert[] = "  , lngmonetaryratecode ";           //10:�̲ߥ졼�ȥ�����
    $aryInsert[] = "  , curconversionrate ";             //11:�����졼��
    $aryInsert[] = "  , strslipcode ";                   //12:Ǽ�ʽ�NO
    $aryInsert[] = "  , lnginvoiceno ";                  //13:������ֹ�
    $aryInsert[] = "  , curtotalprice ";                 //14:��׶��
    $aryInsert[] = "  , strnote ";                       //15:����
    $aryInsert[] = "  , lnginputusercode ";              //16:���ϼԥ�����
    $aryInsert[] = "  , bytinvalidflag ";                //17:̵���ե饰
    $aryInsert[] = "  , dtminsertdate ";                 //18:��Ͽ��
    $aryInsert[] = ")  ";                                
    $aryInsert[] = "VALUES (  ";                         
    $aryInsert[] = "  " . $v_lngsalesno;                 //1:����ֹ�
    $aryInsert[] = " ," . $v_lngrevisionno;              //2:��ӥ�����ֹ�
    $aryInsert[] = " ," . $v_strsalescode;               //3:��女����
    $aryInsert[] = " ," . $v_dtmappropriationdate;       //4:�׾���
    $aryInsert[] = " ," . $v_lngcustomercompanycode;     //5:�ܵҥ�����
    $aryInsert[] = " ," . $v_lnggroupcode;               //6:���롼�ץ�����
    $aryInsert[] = " ," . $v_lngusercode;                //7:�桼��������
    $aryInsert[] = " ," . $v_lngsalesstatuscode;         //8:�����֥�����
    $aryInsert[] = " ," . $v_lngmonetaryunitcode;        //9:�̲�ñ�̥�����
    $aryInsert[] = " ," . $v_lngmonetaryratecode;        //10:�̲ߥ졼�ȥ�����
    $aryInsert[] = " ," . $v_curconversionrate;          //11:�����졼��
    $aryInsert[] = " ," . $v_strslipcode;                //12:Ǽ�ʽ�NO
    $aryInsert[] = " ," . $v_lnginvoiceno;               //13:������ֹ�
    $aryInsert[] = " ," . $v_curtotalprice;              //14:��׶��
    $aryInsert[] = " ," . $v_strnote;                    //15:����
    $aryInsert[] = " ," . $v_lnginputusercode;           //16:���ϼԥ�����
    $aryInsert[] = " ," . $v_bytinvalidflag;             //17:̵���ե饰
    $aryInsert[] = " ," . $v_dtminsertdate;              //18:��Ͽ��
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // ��Ͽ�¹�
    if ( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "���ޥ�����Ͽ���ԡ�", TRUE, "", $objDB );
        // ����
        return false;
    }
    $objDB->freeResult( $lngResultID );

	// ����
	return true;
}


// ���������Ͽ
function fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo,
     $aryHeader , $aryDetail, $objDB, $objAuth)
{
    // ������Ψ
    $lngTaxRate = floatval($aryHeader["lngtaxrate"]);
    // �����Ƕ�ʬ
    $lngTaxClassCode = $aryHeader["lngtaxclasscode"];

    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        $d = $aryDetail[$i];

        // ����ñ�̤Ǥξ����Ƕ�ۤη׻�
        $curTaxPrice = fncCalcTaxPrice($d["cursubtotalprice"], $lngTaxClassCode, $lngTaxRate);

        // ��Ͽ�ǡ����Υ��å�
        $v_lngsalesno = $lngSalesNo;                            //1:����ֹ�
        $v_lngsalesdetailno = $d["rownumber"];                  //2:��������ֹ�
        $v_lngrevisionno = $lngRevisionNo;                      //3:��ӥ�����ֹ�
        $v_strproductcode = withQuote($d["strproductcode"]);    //4:���ʥ�����
        $v_strrevisecode = withQuote($d["strrevisecode"]);      //5:���Υ�����
        $v_lngsalesclasscode = $d["lngsalesclasscode"];         //6:����ʬ������
        $v_lngconversionclasscode = "Null";                     //7:������ʬ������
        $v_lngquantity = $d["lngunitquantity"];                 //8:����
        $v_curproductprice = $d["curproductprice"];             //9:���ʲ���
        $v_lngproductquantity = $d["lngproductquantity"];       //10:���ʿ���
        $v_lngproductunitcode = $d["lngproductunitcode"];       //11:����ñ�̥�����
        $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"];     //12:�����Ƕ�ʬ������
        $v_lngtaxcode = $aryHeader["lngtaxcode"];               //13:������Ψ������
        $v_curtaxprice = $curTaxPrice;                          //14:�����Ƕ��
        $v_cursubtotalprice = $d["cursubtotalprice"];           //15:���׶��
        $v_strnote = withQuote(fncToEucjp($d["strnote"]));                  //16:����
        $v_lngsortkey = $d["rownumber"];                        //17:ɽ���ѥ����ȥ���
        $v_lngreceiveno = $d["lngreceiveno"];                   //18:�����ֹ�
        $v_lngreceivedetailno = $d["lngreceivedetailno"];       //19:���������ֹ�
        $v_lngreceiverevisionno = $d["lngreceiverevisionno"];   //20:�����ӥ�����ֹ�
        
        // ��Ͽ���������
        $aryInsert = [];
        $aryInsert[] ="INSERT  ";
        $aryInsert[] ="INTO t_salesdetail(  ";
        $aryInsert[] ="  lngsalesno ";                      //1:����ֹ�
        $aryInsert[] ="  , lngsalesdetailno ";              //2:��������ֹ�
        $aryInsert[] ="  , lngrevisionno ";                 //3:��ӥ�����ֹ�
        $aryInsert[] ="  , strproductcode ";                //4:���ʥ�����
        $aryInsert[] ="  , strrevisecode ";                 //5:���Υ�����
        $aryInsert[] ="  , lngsalesclasscode ";             //6:����ʬ������
        $aryInsert[] ="  , lngconversionclasscode ";        //7:������ʬ������
        $aryInsert[] ="  , lngquantity ";                   //8:����
        $aryInsert[] ="  , curproductprice ";               //9:���ʲ���
        $aryInsert[] ="  , lngproductquantity ";            //10:���ʿ���
        $aryInsert[] ="  , lngproductunitcode ";            //11:����ñ�̥�����
        $aryInsert[] ="  , lngtaxclasscode ";               //12:�����Ƕ�ʬ������
        $aryInsert[] ="  , lngtaxcode ";                    //13:������Ψ������
        $aryInsert[] ="  , curtaxprice ";                   //14:�����Ƕ��
        $aryInsert[] ="  , cursubtotalprice ";              //15:���׶��
        $aryInsert[] ="  , strnote ";                       //16:����
        $aryInsert[] ="  , lngsortkey ";                    //17:ɽ���ѥ����ȥ���
        $aryInsert[] ="  , lngreceiveno ";                  //18:�����ֹ�
        $aryInsert[] ="  , lngreceivedetailno ";            //19:���������ֹ�
        $aryInsert[] ="  , lngreceiverevisionno ";          //20:�����ӥ�����ֹ�
        $aryInsert[] =")  ";                              
        $aryInsert[] ="VALUES (  ";                       
        $aryInsert[] = "  " . $v_lngsalesno;                //1:����ֹ�
        $aryInsert[] = " ," . $v_lngsalesdetailno;          //2:��������ֹ�
        $aryInsert[] = " ," . $v_lngrevisionno;             //3:��ӥ�����ֹ�
        $aryInsert[] = " ," . $v_strproductcode;            //4:���ʥ�����
        $aryInsert[] = " ," . $v_strrevisecode;             //5:���Υ�����
        $aryInsert[] = " ," . $v_lngsalesclasscode;         //6:����ʬ������
        $aryInsert[] = " ," . $v_lngconversionclasscode;    //7:������ʬ������
        $aryInsert[] = " ," . $v_lngquantity;               //8:����
        $aryInsert[] = " ," . $v_curproductprice;           //9:���ʲ���
        $aryInsert[] = " ," . $v_lngproductquantity;        //10:���ʿ���
        $aryInsert[] = " ," . $v_lngproductunitcode;        //11:����ñ�̥�����
        $aryInsert[] = " ," . $v_lngtaxclasscode;           //12:�����Ƕ�ʬ������
        $aryInsert[] = " ," . $v_lngtaxcode;                //13:������Ψ������
        $aryInsert[] = " ," . $v_curtaxprice;               //14:�����Ƕ��
        $aryInsert[] = " ," . $v_cursubtotalprice;          //15:���׶��
        $aryInsert[] = " ," . $v_strnote;                   //16:����
        $aryInsert[] = " ," . $v_lngsortkey;                //17:ɽ���ѥ����ȥ���
        $aryInsert[] = " ," . $v_lngreceiveno;              //18:�����ֹ�
        $aryInsert[] = " ," . $v_lngreceivedetailno;        //19:���������ֹ�
        $aryInsert[] = " ," . $v_lngreceiverevisionno;      //20:�����ӥ�����ֹ�
        $aryInsert[] =") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);
        
        // ��Ͽ�¹�
        if ( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "���������Ͽ���ԡ�", TRUE, "", $objDB );
            // ����
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

	// ����
	return true;
}

function nullIfEmpty($source)
{
    if (strlen($source) == 0){
        return "Null";
    } else {
        return $source;
    }
}

// Ǽ����ɼ�ޥ�����Ͽ
function fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $strCustomerCompanyName, $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
     $aryHeader , $aryDetail, $objDB, $objAuth)
{
    // �����襳���ɤμ����ʶ��ξ�������Ū��Null�򥻥åȡ�
    if (strlen($aryCustomerCompany["strstockcompanycode"]) != 0){
        $strShipperCode = withQuote($aryCustomerCompany["strstockcompanycode"]);
    }else{
        $strShipperCode = "Null";
    }

    if (strlen($lngDeliveryPlaceCode) == 0)
    {
        $lngDeliveryPlaceCode = "Null";
    }

    if (strlen($aryHeader["dtmpaymentlimit"])!=0){
        $dtmPaymentLimit = withQuote($aryHeader["dtmpaymentlimit"]);
    }else{
        $dtmPaymentLimit = "Null";
    }

    // ��Ͽ�ǡ����Υ��å�
    $v_lngslipno = $lngSlipNo;                                                 //1:Ǽ����ɼ�ֹ�
    $v_lngrevisionno = $lngRevisionNo;                                         //2:��ӥ�����ֹ�
    $v_strslipcode = withQuote($strSlipCode);                                             //3:Ǽ����ɼ������
    $v_lngsalesno = $lngSalesNo;                                               //4:����ֹ�
    $v_strcustomercode = $aryCustomerCompany["lngcompanycode"];                //5:�ܵҥ�����
    $v_strcustomercompanyname = withQuote($strCustomerCompanyName);                       //6:�ܵҼ�̾
    $v_strcustomername = withQuote($strCustomerName);                                     //7:�ܵ�̾
    $v_strcustomeraddress1 = withQuote($aryCustomerCompany["straddress1"]);               //8:�ܵҽ���1
    $v_strcustomeraddress2 = withQuote($aryCustomerCompany["straddress2"]);               //9:�ܵҽ���2
    $v_strcustomeraddress3 = withQuote($aryCustomerCompany["straddress3"]);               //10:�ܵҽ���3
    $v_strcustomeraddress4 = withQuote($aryCustomerCompany["straddress4"]);               //11:�ܵҽ���4
    $v_strcustomerphoneno = withQuote($aryCustomerCompany["strtel1"]);                    //12:�ܵ������ֹ�
    $v_strcustomerfaxno = withQuote($aryCustomerCompany["strfax1"]);                      //13:�ܵ�FAX�ֹ�
    $v_strcustomerusername = withQuote($aryHeader["strcustomerusername"]);                //14:�ܵ�ô����̾
    $v_strshippercode = $strShipperCode;                                       //15:�����襳���ɡʽвټԡ�
    $v_dtmdeliverydate = withQuote($aryHeader["dtmdeliverydate"]);             //16:Ǽ����
    $v_lngdeliveryplacecode = nullIfEmpty($lngDeliveryPlaceCode);                           //17:Ǽ�ʾ�ꥳ����
    $v_strdeliveryplacename = withQuote($aryHeader["strdeliveryplacename"]);           //18:Ǽ�ʾ��̾
    $v_strdeliveryplaceusername = withQuote($aryHeader["strdeliveryplaceusername"]);   //19:Ǽ�ʾ��ô����̾
    $v_lngpaymentmethodcode = $aryHeader["lngpaymentmethodcode"];             //20:��ʧ��ˡ������
    $v_dtmpaymentlimit = $dtmPaymentLimit;                                    //21:��ʧ����
    $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"];                        //22:���Ƕ�ʬ������
    $v_strtaxclassname = withQuote($aryHeader["strtaxclassname"]);             //23:���Ƕ�ʬ
    $v_curtax = $aryHeader["lngtaxrate"];                                      //24:������Ψ
    $v_strusercode = withQuote( $aryHeader["strdrafteruserdisplaycode"]);      //25:ô���ԥ�����
    $v_strusername = withQuote($aryHeader["strdrafteruserdisplayname"]);       //26:ô����̾
    $v_curtotalprice = $aryHeader["curtotalprice"];                            //27:��׶��
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"];             //28:�̲�ñ�̥�����
    $v_strmonetaryunitsign = withQuote($aryDetail[0]["strmonetaryunitsign"]);  //29:�̲�ñ��
    $v_dtminsertdate = "now()";                                                //30:������
    $v_strinsertusercode = withQuote($objAuth->UserCode);                      //31:���ϼԥ�����
    $v_strinsertusername = withQuote($objAuth->UserDisplayName);               //32:���ϼ�̾
    $v_strnote = withQuote($aryHeader["strNote"]);                             //33:����
    $v_lngprintcount = 0;                                                      //34:�������
    $v_bytinvalidflag = "FALSE";                                               //35:̵���ե饰

    
    // ��Ͽ���������
    $aryInsert = [];
    $aryInsert[] ="INSERT  ";
    $aryInsert[] ="INTO m_slip(  ";
    $aryInsert[] ="  lngslipno ";                        //1:Ǽ����ɼ�ֹ�
    $aryInsert[] ="  , lngrevisionno ";                  //2:��ӥ�����ֹ�
    $aryInsert[] ="  , strslipcode ";                    //3:Ǽ����ɼ������
    $aryInsert[] ="  , lngsalesno ";                     //4:����ֹ�
    $aryInsert[] ="  , strcustomercode ";                //5:�ܵҥ�����
    $aryInsert[] ="  , strcustomercompanyname ";         //6:�ܵҼ�̾
    $aryInsert[] ="  , strcustomername ";                //7:�ܵ�̾
    $aryInsert[] ="  , strcustomeraddress1 ";            //8:�ܵҽ���1
    $aryInsert[] ="  , strcustomeraddress2 ";            //9:�ܵҽ���2
    $aryInsert[] ="  , strcustomeraddress3 ";            //10:�ܵҽ���3
    $aryInsert[] ="  , strcustomeraddress4 ";            //11:�ܵҽ���4
    $aryInsert[] ="  , strcustomerphoneno ";             //12:�ܵ������ֹ�
    $aryInsert[] ="  , strcustomerfaxno ";               //13:�ܵ�FAX�ֹ�
    $aryInsert[] ="  , strcustomerusername ";            //14:�ܵ�ô����̾
    $aryInsert[] ="  , strshippercode ";                 //15:�����襳���ɡʽвټԡ�
    $aryInsert[] ="  , dtmdeliverydate ";                //16:Ǽ����
    $aryInsert[] ="  , lngdeliveryplacecode ";           //17:Ǽ�ʾ�ꥳ����
    $aryInsert[] ="  , strdeliveryplacename ";           //18:Ǽ�ʾ��̾
    $aryInsert[] ="  , strdeliveryplaceusername ";       //19:Ǽ�ʾ��ô����̾
    $aryInsert[] ="  , lngpaymentmethodcode ";           //20:��ʧ��ˡ������
    $aryInsert[] ="  , dtmpaymentlimit ";                //21:��ʧ����
    $aryInsert[] ="  , lngtaxclasscode ";                //22:���Ƕ�ʬ������
    $aryInsert[] ="  , strtaxclassname ";                //23:���Ƕ�ʬ
    $aryInsert[] ="  , curtax ";                         //24:������Ψ
    $aryInsert[] ="  , strusercode ";                    //25:ô���ԥ�����
    $aryInsert[] ="  , strusername ";                    //26:ô����̾
    $aryInsert[] ="  , curtotalprice ";                  //27:��׶��
    $aryInsert[] ="  , lngmonetaryunitcode ";            //28:�̲�ñ�̥�����
    $aryInsert[] ="  , strmonetaryunitsign ";            //29:�̲�ñ��
    $aryInsert[] ="  , dtminsertdate ";                  //30:������
    $aryInsert[] ="  , strinsertusercode ";              //31:���ϼԥ�����
    $aryInsert[] ="  , strinsertusername ";              //32:���ϼ�̾
    $aryInsert[] ="  , strnote ";                        //33:����
    $aryInsert[] ="  , lngprintcount ";                  //34:�������
    $aryInsert[] ="  , bytinvalidflag ";                 //35:̵���ե饰
    $aryInsert[] =")  ";                                 
    $aryInsert[] ="VALUES (  ";                          
    $aryInsert[] = "  " . $v_lngslipno;                    //1:Ǽ����ɼ�ֹ�
    $aryInsert[] = " ," . $v_lngrevisionno;                //2:��ӥ�����ֹ�
    $aryInsert[] = " ," . $v_strslipcode;                  //3:Ǽ����ɼ������
    $aryInsert[] = " ," . $v_lngsalesno;                   //4:����ֹ�
    $aryInsert[] = " ," . $v_strcustomercode;              //5:�ܵҥ�����
    $aryInsert[] = " ," . $v_strcustomercompanyname;       //6:�ܵҼ�̾
    $aryInsert[] = " ," . $v_strcustomername;              //7:�ܵ�̾
    $aryInsert[] = " ," . $v_strcustomeraddress1;          //8:�ܵҽ���1
    $aryInsert[] = " ," . $v_strcustomeraddress2;          //9:�ܵҽ���2
    $aryInsert[] = " ," . $v_strcustomeraddress3;          //10:�ܵҽ���3
    $aryInsert[] = " ," . $v_strcustomeraddress4;          //11:�ܵҽ���4
    $aryInsert[] = " ," . $v_strcustomerphoneno;           //12:�ܵ������ֹ�
    $aryInsert[] = " ," . $v_strcustomerfaxno;             //13:�ܵ�FAX�ֹ�
    $aryInsert[] = " ," . $v_strcustomerusername;          //14:�ܵ�ô����̾
    $aryInsert[] = " ," . $v_strshippercode;               //15:�����襳���ɡʽвټԡ�
    $aryInsert[] = " ," . $v_dtmdeliverydate;              //16:Ǽ����
    $aryInsert[] = " ," . $v_lngdeliveryplacecode;         //17:Ǽ�ʾ�ꥳ����
    $aryInsert[] = " ," . $v_strdeliveryplacename;         //18:Ǽ�ʾ��̾
    $aryInsert[] = " ," . $v_strdeliveryplaceusername;     //19:Ǽ�ʾ��ô����̾
    $aryInsert[] = " ," . $v_lngpaymentmethodcode;         //20:��ʧ��ˡ������
    $aryInsert[] = " ," . $v_dtmpaymentlimit;              //21:��ʧ����
    $aryInsert[] = " ," . $v_lngtaxclasscode;              //22:���Ƕ�ʬ������
    $aryInsert[] = " ," . $v_strtaxclassname;              //23:���Ƕ�ʬ
    $aryInsert[] = " ," . $v_curtax;                       //24:������Ψ
    $aryInsert[] = " ," . $v_strusercode;                  //25:ô���ԥ�����
    $aryInsert[] = " ," . $v_strusername;                  //26:ô����̾
    $aryInsert[] = " ," . $v_curtotalprice;                //27:��׶��
    $aryInsert[] = " ," . $v_lngmonetaryunitcode;          //28:�̲�ñ�̥�����
    $aryInsert[] = " ," . $v_strmonetaryunitsign;          //29:�̲�ñ��
    $aryInsert[] = " ," . $v_dtminsertdate;                //30:������
    $aryInsert[] = " ," . $v_strinsertusercode;            //31:���ϼԥ�����
    $aryInsert[] = " ," . $v_strinsertusername;            //32:���ϼ�̾
    $aryInsert[] = " ," . $v_strnote;                      //33:����
    $aryInsert[] = " ," . $v_lngprintcount;                //34:�������
    $aryInsert[] = " ," . $v_bytinvalidflag;               //35:̵���ե饰
    $aryInsert[] =") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // ��Ͽ�¹�
    if ( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "Ǽ����ɼ�ޥ�����Ͽ���ԡ�", TRUE, "", $objDB );
        // ����
        return false;
    }
    $objDB->freeResult( $lngResultID );

	// ����
	return true;
}

// Ǽ����ɼ������Ͽ
function fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo,
    $aryHeader, $aryDetail, $objDB, $objAuth)
{
    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        $d = $aryDetail[$i];

        // ��Ͽ�ǡ����Υ��å�
        $v_lngslipno = $lngSlipNo;                                          //1:Ǽ����ɼ�ֹ�
        $v_lngslipdetailno = $d["rownumber"];                               //2:Ǽ����ɼ�����ֹ�
        $v_lngrevisionno = $lngRevisionNo;                                  //3:��ӥ�����ֹ�
        $v_strcustomersalescode = withQuote($d["strcustomerreceivecode"]);  //4:�ܵҼ����ֹ�
        $v_lngsalesclasscode = $d["lngsalesclasscode"];                     //5:����ʬ������
        $v_strsalesclassname = withQuote($d["strsalesclassname"]);          //6:����ʬ̾
        $v_strgoodscode = withQuote($d["strgoodscode"]);                    //7:�ܵ�����
        $v_strproductcode = withQuote($d["strproductcode"]);                //8:���ʥ�����
        $v_strrevisecode = withQuote($d["strrevisecode"]);                  //9:���Υ�����
        $v_strproductname = withQuote($d["strproductname"]);                //10:����̾
        $v_strproductenglishname = withQuote($d["strproductenglishname"]);  //11:����̾�ʱѸ��
        $v_curproductprice = $d["curproductprice"];                         //12:ñ��
        $v_lngquantity = $d["lngunitquantity"];                             //13:����
        $v_lngproductquantity = $d["lngproductquantity"];                   //14:����
        $v_lngproductunitcode = $d["lngproductunitcode"];                   //15:����ñ�̥�����
        $v_strproductunitname = withQuote($d["strproductunitname"]);        //16:����ñ��̾
        $v_cursubtotalprice = $d["cursubtotalprice"];                       //17:����
        $v_strnote = withQuote($d["strnote"]);                              //18:��������
        $v_lngreceiveno = $d["lngreceiveno"];                               //19:�����ֹ�
        $v_lngreceivedetailno = $d["lngreceivedetailno"];                   //20:���������ֹ�
        $v_lngreceiverevisionno = $d["lngreceiverevisionno"];               //21:�����ӥ�����ֹ�
        $v_lngsortkey = $d["rownumber"];                                    //22:ɽ���ѥ����ȥ���

        // ��Ͽ���������
        $aryInsert = [];
        $aryInsert[] ="INSERT  ";
        $aryInsert[] ="INTO t_slipdetail(  ";
        $aryInsert[] ="  lngslipno ";                            //1:Ǽ����ɼ�ֹ�
        $aryInsert[] ="  , lngslipdetailno ";                    //2:Ǽ����ɼ�����ֹ�
        $aryInsert[] ="  , lngrevisionno ";                      //3:��ӥ�����ֹ�
        $aryInsert[] ="  , strcustomersalescode ";               //4:�ܵҼ����ֹ�
        $aryInsert[] ="  , lngsalesclasscode ";                  //5:����ʬ������
        $aryInsert[] ="  , strsalesclassname ";                  //6:����ʬ̾
        $aryInsert[] ="  , strgoodscode ";                       //7:�ܵ�����
        $aryInsert[] ="  , strproductcode ";                     //8:���ʥ�����
        $aryInsert[] ="  , strrevisecode ";                      //9:���Υ�����
        $aryInsert[] ="  , strproductname ";                     //10:����̾
        $aryInsert[] ="  , strproductenglishname ";              //11:����̾�ʱѸ��
        $aryInsert[] ="  , curproductprice ";                    //12:ñ��
        $aryInsert[] ="  , lngquantity ";                        //13:����
        $aryInsert[] ="  , lngproductquantity ";                 //14:����
        $aryInsert[] ="  , lngproductunitcode ";                 //15:����ñ�̥�����
        $aryInsert[] ="  , strproductunitname ";                 //16:����ñ��̾
        $aryInsert[] ="  , cursubtotalprice ";                   //17:����
        $aryInsert[] ="  , strnote ";                            //18:��������
        $aryInsert[] ="  , lngreceiveno ";                       //19:�����ֹ�
        $aryInsert[] ="  , lngreceivedetailno ";                 //20:���������ֹ�
        $aryInsert[] ="  , lngreceiverevisionno ";               //21:�����ӥ�����ֹ�
        $aryInsert[] ="  , lngsortkey ";                         //22:ɽ���ѥ����ȥ���
        $aryInsert[] =")  ";                               
        $aryInsert[] ="VALUES (  ";                        
        $aryInsert[] = "  " . $v_lngslipno;                      //1:Ǽ����ɼ�ֹ�
        $aryInsert[] = " ," . $v_lngslipdetailno;                //2:Ǽ����ɼ�����ֹ�
        $aryInsert[] = " ," . $v_lngrevisionno;                  //3:��ӥ�����ֹ�
        $aryInsert[] = " ," . $v_strcustomersalescode;           //4:�ܵҼ����ֹ�
        $aryInsert[] = " ," . $v_lngsalesclasscode;              //5:����ʬ������
        $aryInsert[] = " ," . $v_strsalesclassname;              //6:����ʬ̾
        $aryInsert[] = " ," . $v_strgoodscode;                   //7:�ܵ�����
        $aryInsert[] = " ," . $v_strproductcode;                 //8:���ʥ�����
        $aryInsert[] = " ," . $v_strrevisecode;                  //9:���Υ�����
        $aryInsert[] = " ," . $v_strproductname;                 //10:����̾
        $aryInsert[] = " ," . $v_strproductenglishname;          //11:����̾�ʱѸ��
        $aryInsert[] = " ," . $v_curproductprice;                //12:ñ��
        $aryInsert[] = " ," . $v_lngquantity;                    //13:����
        $aryInsert[] = " ," . $v_lngproductquantity;             //14:����
        $aryInsert[] = " ," . $v_lngproductunitcode;             //15:����ñ�̥�����
        $aryInsert[] = " ," . $v_strproductunitname;             //16:����ñ��̾
        $aryInsert[] = " ," . $v_cursubtotalprice;               //17:����
        $aryInsert[] = " ," . $v_strnote;                        //18:��������
        $aryInsert[] = " ," . $v_lngreceiveno;                   //19:�����ֹ�
        $aryInsert[] = " ," . $v_lngreceivedetailno;             //20:���������ֹ�
        $aryInsert[] = " ," . $v_lngreceiverevisionno;           //21:�����ӥ�����ֹ�
        $aryInsert[] = " ," . $v_lngsortkey;                     //22:ɽ���ѥ����ȥ���
        $aryInsert[] =") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // ��Ͽ�¹�
        if ( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "Ǽ����ɼ������Ͽ��Ͽ���ԡ�", TRUE, "", $objDB );
            // ����
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

	// ����
	return true;
}

// Ģɼ���̤��б�����Ģɼ�ƥ�ץ졼�ȥե�����̾�μ���
function fncGetReportTemplateFileName($lngSlipKindCode)
{

    if ($lngSlipKindCode == "1")
    {
        //1:���ꡦ����
        return "Ǽ�ʽ�temple_B��_Ϣ�����.xlsx";
    }
    else if ($lngSlipKindCode == "2")
    {
        //2:����
        return "Ǽ�ʽ�temple_����_Ϣ�����.xlsx";
    }
    else if ($lngSlipKindCode == "3")
    {
        //3:DEBIT NOTE
        return "DEBIT NOTE.xlsx";
    }
    else 
    {
        throw new Exception("Ģɼ�ƥ�ץ졼�Ȥ�����Ǥ��ޤ���lngSlipKindCode=".$lngSlipKindCode);
    }
}

// ʸ�������� EUC-JP -> UTF-8 �Ѵ��ѥإ�Ѵؿ�
function fncToUtf8($eucjpText)
{
    return mb_convert_encoding($eucjpText, 'UTF-8','EUC-JP' );
}

// ʸ�������� UTF-8 -> EUC-JP �Ѵ��ѥإ�Ѵؿ�
function fncToEucjp($utf8Text)
{
    return mb_convert_encoding($utf8Text, 'EUC-JP', 'UTF-8' );
}

// ���ꥢ�ɥ쥹�Υ�����ͤ򥻥åȤ���إ�Ѵؿ�
function setCellValue($xlWorkSheet, $address, $value)
{
    $value = fncToUtf8($value);
    $xlWorkSheet->GetCell($address)->SetValue($value);
}

// �Ԥ��Ѥ��ʤ��饻����ͤ򥻥åȤ���إ�Ѵؿ�
function setCellDetailValue($xlWorkSheet, $columnAddress, $rowNumber, $value)
{
    $address = $columnAddress . $rowNumber;
    $value = fncToUtf8($value);
    $xlWorkSheet->GetCell($address)->SetValue($value);
}

// �ƥ��ȥ����ɡʺ���ġ�
function fncGeneratePreviewTestCode($aryHeader, $aryDetail, $objDB)
{
    ini_set('default_charset', 'UTF-8');
    fncCalled($aryHeader);
    $file = mb_convert_encoding(REPORT_TMPDIR.'\Ǽ�ʽ�temple_B��_Ϣ�����.xlsx', 'UTF-8','EUC-JP' );
    $sheetname = mb_convert_encoding('B������', 'UTF-8','EUC-JP' );
    $cellValue = mb_convert_encoding('���̤��ͤ򥻥å�', 'UTF-8','EUC-JP' );
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

    $ws = $spreadsheet->GetSheetByName($sheetname);
    $ws->GetCell('C3')->SetValue($cellValue);

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
    
    $outStyle = $writer->generateStyles(true);
    $outSheetData = $writer->generateSheetData();

    $aryPreview["PreviewStyle"] = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
    $aryPreview["PreviewData"] = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');

    return $aryPreview;
}

function fncConvertArrayHeaderToEucjp($aryHeader)
{
    $aryHeader["strdrafteruserdisplaycode"] = fncToEucjp($aryHeader["strdrafteruserdisplaycode"]);
    $aryHeader["strdrafteruserdisplayname"] = fncToEucjp($aryHeader["strdrafteruserdisplayname"]);
    $aryHeader["strcompanydisplaycode"] = fncToEucjp($aryHeader["strcompanydisplaycode"]);
    $aryHeader["strcompanydisplayname"] = fncToEucjp($aryHeader["strcompanydisplayname"]);
    $aryHeader["strcustomerusername"] = fncToEucjp($aryHeader["strcustomerusername"]);
    $aryHeader["dtmdeliverydate"] = fncToEucjp($aryHeader["dtmdeliverydate"]);
    $aryHeader["strdeliveryplacecompanydisplaycode"] = fncToEucjp($aryHeader["strdeliveryplacecompanydisplaycode"]);
    $aryHeader["strdeliveryplacename"] = fncToEucjp($aryHeader["strdeliveryplacename"]);
    $aryHeader["strdeliveryplaceusername"] = fncToEucjp($aryHeader["strdeliveryplaceusername"]);
    $aryHeader["strnote"] = fncToEucjp($aryHeader["strnote"]);
    $aryHeader["lngtaxclasscode"] = fncToEucjp($aryHeader["lngtaxclasscode"]);
    $aryHeader["strtaxclassname"] = fncToEucjp($aryHeader["strtaxclassname"]);
    $aryHeader["lngtaxcode"] = fncToEucjp($aryHeader["lngtaxcode"]);
    $aryHeader["lngtaxrate"] = fncToEucjp($aryHeader["lngtaxrate"]);
    $aryHeader["strtaxamount"] = fncToEucjp($aryHeader["strtaxamount"]);
    $aryHeader["dtmpaymentlimit"] = fncToEucjp($aryHeader["dtmpaymentlimit"]);
    $aryHeader["lngpaymentmethodcode"] = fncToEucjp($aryHeader["lngpaymentmethodcode"]);
    $aryHeader["curtotalprice"] = fncToEucjp($aryHeader["curtotalprice"]);
    
    return $aryHeader;
}

function fncConvertArrayDetailToEucjp($aryDetail)
{
    for ( $i = 0; $i < count($aryDetail); $i++ )
    {
        $d = &$aryDetail[$i];

        $d["rownumber"] = fncToEucjp($d["rownumber"]);
        $d["strcustomerreceivecode"] = fncToEucjp($d["strcustomerreceivecode"]);
        $d["strreceivecode"] = fncToEucjp($d["strreceivecode"]);
        $d["strgoodscode"] = fncToEucjp($d["strgoodscode"]);
        $d["strproductcode"] = fncToEucjp($d["strproductcode"]);
        $d["strproductname"] = fncToEucjp($d["strproductname"]);
        $d["strproductenglishname"] = fncToEucjp($d["strproductenglishname"]);
        $d["strsalesdeptname"] = fncToEucjp($d["strsalesdeptname"]);
        $d["strsalesclassname"] = fncToEucjp($d["strsalesclassname"]);
        $d["dtmdeliverydate"] = fncToEucjp($d["dtmdeliverydate"]);
        $d["lngunitquantity"] = fncToEucjp($d["lngunitquantity"]);
        $d["curproductprice"] = fncToEucjp($d["curproductprice"]);
        $d["strproductunitname"] = fncToEucjp($d["strproductunitname"]);
        $d["lngproductquantity"] = fncToEucjp($d["lngproductquantity"]);
        $d["cursubtotalprice"] = fncToEucjp($d["cursubtotalprice"]);
        $d["lngreceiveno"] = fncToEucjp($d["lngreceiveno"]);
        $d["lngreceivedetailno"] = fncToEucjp($d["lngreceivedetailno"]);
        $d["lngreceiverevisionno"] = fncToEucjp($d["lngreceiverevisionno"]);
        $d["strrevisecode"] = fncToEucjp($d["strrevisecode"]);
        $d["lngsalesclasscode"] = fncToEucjp($d["lngsalesclasscode"]);
        $d["lngproductunitcode"] = fncToEucjp($d["lngproductunitcode"]);
        $d["strnote"] = fncToEucjp($d["strnote"]);
        $d["lngmonetaryunitcode"] = fncToEucjp($d["lngmonetaryunitcode"]);
        $d["lngmonetaryratecode"] = fncToEucjp($d["lngmonetaryratecode"]);
        $d["strmonetaryunitsign"] = fncToEucjp($d["strmonetaryunitsign"]);
    }

    return $aryDetail;
}

// �ץ�ӥ塼HTML����������
function fncGenerateReportPreview($aryHeader, $aryDetail, $objDB)
{
    // --------------------------------------------
    //  �ǡ�������
    // --------------------------------------------
    // �ܵҤβ�ҥ����ɤ����
    $lngCustomerCompanyCode = fncGetNumericCompanyCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҤβ�ҥ����ɤ�ɳ�Ť�Ǽ����ɼ���̤����
    $aryReport = fncGetSlipKindByCompanyCode($lngCustomerCompanyCode, $objDB);
    
    // Ģɼ���̤μ���
    $lngSlipKindCode = $aryReport["lngslipkindcode"];
    // Ģɼ���̤��б�����Ģɼ�ƥ�ץ졼�ȥե�����̾�μ���
    $templatFileName = fncGetReportTemplateFileName($lngSlipKindCode);
    // �ܵҤ�ɳ�Ť�Ģɼ1�ڡ���������κ������ٿ����������
    $maxItemPerPage = intval($aryReport["lngmaxline"]);
    // ��Ͽ���������٤ο�
    $totalItemCount = count($aryDetail);
    // ����ڡ������η׻�
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);

    // �ܵҤβ�ҥ����ɤ�ɳ�Ť���Ҿ�������
    $aryCustomerCompany = fncGetCompanyInfoByCompanyCode($lngCustomerCompanyCode, $objDB);
    // �ܵҤι񥳡��ɤ����
    $lngCustomerCountryCode = fncGetCountryCode($aryHeader["strcompanydisplaycode"], $objDB);
    // �ܵҼ�̾�μ���
    $strCustomerCompanyName = fncGetCustomerCompanyName($lngCustomerCountryCode, $aryCustomerCompany);
    // �ܵ�̾�μ���
    $strCustomerName = fncGetCustomerName($aryCustomerCompany);
    // Ǽ����β�ҥ����ɤμ���
    $lngDeliveryPlaceCode = fncGetNumericCompanyCode($aryHeader["strdeliveryplacecompanydisplaycode"], $objDB);

    // --------------------------------------------
    //  ���ץ�åɥ����Ƚ����
    // --------------------------------------------
    // ���ܸ��б�
    ini_set('default_charset', 'UTF-8');
    // Ģɼ�ƥ�ץ졼�ȤΥե�ѥ�
    $spreadSheetFilePath = fncToUtf8(REPORT_TMPDIR . $templatFileName);
    // �ǡ��������ꤹ�륷����̾
    $dataSheetName = fncToUtf8("�ǡ���������");

    // --------------------------------------------
    //  �ץ�ӥ塼HTML����
    // --------------------------------------------
    // �ץ�ӥ塼��CSS
    $previewStyle = "";
    // �ץ�ӥ塼HTML
    $previewData = "";

    // �ڡ���ñ�̤Ǥ�HTML����
    for ( $page = 1; $page <= $maxPageCount; $page++ ){

        // �μ¤˽�������뤿��1�ڡ�����˥��ץ�åɥ����Ȥ��ɤ߹��ߤʤ���
        $xlSpreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadSheetFilePath);
        $xlWorkSheet = $xlSpreadSheet->GetSheetByName($dataSheetName);
        $xlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Html($xlSpreadSheet);

        if (strlen($previewStyle) == 0)
        {
            // CSS�����Τ�1�Ĥ���Ф褤
            $previewStyle = $xlWriter->generateStyles(true);
        }

        // ���ߤΥڡ�������1�ڡ�������������ٿ�����
        // ���Ϥ������٤Υ���ǥå����κǾ��ͤȺ����ͤ����
        $itemMinIndex = ($page-1) * $maxItemPerPage ;
        $itemMaxIndex = $page * $maxItemPerPage - 1;
        if ($itemMaxIndex > $totalItemCount - 1){
            $itemMaxIndex = $totalItemCount - 1;
        }

        // 1�ڡ���ʬ�Υץ�ӥ塼HTML����
        $pageHtml = fncGeneratePreviewPageHtml(
            $xlWorkSheet, $xlWriter,
            $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName, 
            $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
            $aryHeader, $aryDetail);

        // ���Τ��ɲ�
        $previewData .= $pageHtml;
       
    }

    // �Ǹ��UTF-8����EUC-JP���Ѵ�������̤򥻥å�
    $aryPreview = array();
    $aryPreview["PreviewStyle"] = fncToEucjp($previewStyle);
    $aryPreview["PreviewData"] = fncToEucjp($previewData);

    return $aryPreview;

}

// Ǽ�ʽ�ǡ�������Ģɼ�ץ�ӥ塼HTML����
function fncGeneratePreviewPageHtml(
    $xlWorkSheet, $xlWriter,
    $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName, 
    $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
    $aryHeader, $aryDetail)
{
    // ------------------------------------------
    //   �ޥ����ǡ����Υ��å�
    // ------------------------------------------
    // �ͤ�����
    $v_lngslipno = "";                                                      //1:Ǽ����ɼ�ֹ�
    $v_lngrevisionno = "";                                                  //2:��ӥ�����ֹ�
    $v_strslipcode = "";                                                    //3:Ǽ����ɼ������
    $v_lngsalesno = "";                                                     //4:����ֹ�
    $v_strcustomercode = $aryCustomerCompany["lngcompanycode"];             //5:�ܵҥ�����
    $v_strcustomercompanyname = $strCustomerCompanyName;                    //6:�ܵҼ�̾
    $v_strcustomername = $strCustomerName;                                  //7:�ܵ�̾
    $v_strcustomeraddress1 = $aryCustomerCompany["straddress1"];            //8:�ܵҽ���1
    $v_strcustomeraddress2 = $aryCustomerCompany["straddress2"];            //9:�ܵҽ���2
    $v_strcustomeraddress3 = $aryCustomerCompany["straddress3"];            //10:�ܵҽ���3
    $v_strcustomeraddress4 = $aryCustomerCompany["straddress4"];            //11:�ܵҽ���4
    $v_strcustomerphoneno = $aryCustomerCompany["strtel1"];                 //12:�ܵ������ֹ�
    $v_strcustomerfaxno = $aryCustomerCompany["strfax1"];                   //13:�ܵ�FAX�ֹ�
    $v_strcustomerusername = $aryHeader["strcustomerusername"];             //14:�ܵ�ô����̾
    $v_dtmdeliverydate = $aryHeader["dtmdeliverydate"];                     //15:Ǽ����
    $v_lngdeliveryplacecode = $lngDeliveryPlaceCode;                        //16:Ǽ�ʾ�ꥳ����
    $v_strdeliveryplacename = $aryHeader["strdeliveryplacename"];           //17:Ǽ�ʾ��̾
    $v_strdeliveryplaceusername = $aryHeader["strdeliveryplaceusername"];   //18:Ǽ�ʾ��ô����̾
    $v_strusercode =  $aryHeader["strdrafteruserdisplaycode"];              //19:ô���ԥ�����
    $v_strusername = $aryHeader["strdrafteruserdisplayname"];               //20:ô����̾
    $v_curtotalprice = $aryHeader["curtotalprice"];                         //21:��׶��
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"];          //22:�̲�ñ�̥�����
    $v_strmonetaryunitsign = $aryDetail[0]["strmonetaryunitsign"];          //23:�̲�ñ��
    $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"];                     //24:���Ƕ�ʬ������
    $v_strtaxclassname = $aryHeader["strtaxclassname"];                     //25:���Ƕ�ʬ
    $v_curtax = $aryHeader["lngtaxrate"];                                   //26:������Ψ
    $v_lngpaymentmethodcode = $aryHeader["lngpaymentmethodcode"];           //27:��ʧ��ˡ������
    $v_dtmpaymentlimit = $aryHeader["dtmpaymentlimit"];                     //28:��ʧ����
    $v_dtminsertdate = "";                                                  //29:������
    $v_strnote = $aryHeader["strNote"];                                     //30:����
    $v_strshippercode = $aryCustomerCompany["strstockcompanycode"];         //31:�����襳���ɡʽвټԡ�

    // ������ͤ򥻥å�
    setCellValue($xlWorkSheet, "B3", $v_lngslipno);                         //1:Ǽ����ɼ�ֹ�
    setCellValue($xlWorkSheet, "C3", $v_lngrevisionno);                     //2:��ӥ�����ֹ�
    setCellValue($xlWorkSheet, "D3", $v_strslipcode);                       //3:Ǽ����ɼ������
    setCellValue($xlWorkSheet, "E3", $v_lngsalesno);                        //4:����ֹ�
    setCellValue($xlWorkSheet, "F3", $v_strcustomercode);                   //5:�ܵҥ�����
    setCellValue($xlWorkSheet, "G3", $v_strcustomercompanyname);            //6:�ܵҼ�̾
    setCellValue($xlWorkSheet, "H3", $v_strcustomername);                   //7:�ܵ�̾
    setCellValue($xlWorkSheet, "I3", $v_strcustomeraddress1);               //8:�ܵҽ���1
    setCellValue($xlWorkSheet, "J3", $v_strcustomeraddress2);               //9:�ܵҽ���2
    setCellValue($xlWorkSheet, "K3", $v_strcustomeraddress3);               //10:�ܵҽ���3
    setCellValue($xlWorkSheet, "L3", $v_strcustomeraddress4);               //11:�ܵҽ���4
    setCellValue($xlWorkSheet, "M3", $v_strcustomerphoneno);                //12:�ܵ������ֹ�
    setCellValue($xlWorkSheet, "N3", $v_strcustomerfaxno);                  //13:�ܵ�FAX�ֹ�
    setCellValue($xlWorkSheet, "O3", $v_strcustomerusername);               //14:�ܵ�ô����̾
    setCellValue($xlWorkSheet, "P3", $v_dtmdeliverydate);                   //15:Ǽ����
    setCellValue($xlWorkSheet, "Q3", $v_lngdeliveryplacecode);              //16:Ǽ�ʾ�ꥳ����
    setCellValue($xlWorkSheet, "R3", $v_strdeliveryplacename);              //17:Ǽ�ʾ��̾
    setCellValue($xlWorkSheet, "S3", $v_strdeliveryplaceusername);          //18:Ǽ�ʾ��ô����̾
    setCellValue($xlWorkSheet, "T3", $v_strusercode);                       //19:ô���ԥ�����
    setCellValue($xlWorkSheet, "U3", $v_strusername);                       //20:ô����̾
    setCellValue($xlWorkSheet, "V3", $v_curtotalprice);                     //21:��׶��
    setCellValue($xlWorkSheet, "W3", $v_lngmonetaryunitcode);               //22:�̲�ñ�̥�����
    setCellValue($xlWorkSheet, "X3", $v_strmonetaryunitsign);               //23:�̲�ñ��
    setCellValue($xlWorkSheet, "Y3", $v_lngtaxclasscode);                   //24:���Ƕ�ʬ������
    setCellValue($xlWorkSheet, "Z3", $v_strtaxclassname);                   //25:���Ƕ�ʬ
    setCellValue($xlWorkSheet, "AA3", $v_curtax);                           //26:������Ψ
    setCellValue($xlWorkSheet, "AB3", $v_lngpaymentmethodcode);             //27:��ʧ��ˡ������
    setCellValue($xlWorkSheet, "AC3", $v_dtmpaymentlimit);                  //28:��ʧ����
    setCellValue($xlWorkSheet, "AD3", $v_dtminsertdate);                    //29:������
    setCellValue($xlWorkSheet, "AE3", $v_strnote);                          //30:����
    setCellValue($xlWorkSheet, "AF3", $v_strshippercode);                   //31:�����襳���ɡʽвټԡ�
    
    // ------------------------------------------
    //   ���٥ǡ����Υ��å�
    // ------------------------------------------
    // ���٥ǡ����򥻥åȤ��볫�Ϲ�
    $startRowIndex = 6;
    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        $d = $aryDetail[$i];
        
        // �ͤ�����
        $v_lngslipno = "";                                                         //1:Ǽ����ɼ�ֹ�
        $v_lngslipdetailno = $d["rownumber"];                                      //2:Ǽ����ɼ�����ֹ�
        $v_lngrevisionno = "";                                                     //3:��ӥ�����ֹ�
        $v_strcustomersalescode = $d["strcustomerreceivecode"];                    //4:�ܵҼ����ֹ�
        $v_lngsalesclasscode = $d["lngsalesclasscode"];                            //5:����ʬ������
        $v_strsalesclassname = $d["strsalesclassname"];                            //6:����ʬ̾
        $v_strgoodscode = $d["strgoodscode"];                                      //7:�ܵ�����
        $v_strproductcode = $d["strproductcode"];                                  //8:���ʥ�����
        $v_strrevisecode = $d["strrevisecode"];                                    //9:���Υ�����
        $v_strproductname = $d["strproductname"];                                  //10:����̾
        $v_strproductenglishname = $d["strproductenglishname"];                    //11:����̾�ʱѸ��
        $v_curproductprice = $d["curproductprice"];                                //12:ñ��
        $v_lngquantity = $d["lngunitquantity"];                                    //13:����
        $v_lngproductquantity = $d["lngproductquantity"];                          //14:����
        $v_lngproductunitcode = $d["lngproductunitcode"];                          //15:����ñ�̥�����
        $v_strproductunitname = $d["strproductunitname"];                          //16:����ñ��̾
        $v_cursubtotalprice = $d["cursubtotalprice"];                              //17:����
        $v_strnote = $d["strnote"];                                                //18:��������
        
        // ������ͤ򥻥å�
        $r = $startRowIndex + ($i - $itemMinIndex);
        setCellDetailValue($xlWorkSheet, "B", $r, $v_lngslipno);                   //1:Ǽ����ɼ�ֹ�
        setCellDetailValue($xlWorkSheet, "C", $r, $v_lngslipdetailno);             //2:Ǽ����ɼ�����ֹ�
        setCellDetailValue($xlWorkSheet, "D", $r, $v_lngrevisionno);               //3:��ӥ�����ֹ�
        setCellDetailValue($xlWorkSheet, "E", $r, $v_strcustomersalescode);        //4:�ܵҼ����ֹ�
        setCellDetailValue($xlWorkSheet, "F", $r, $v_lngsalesclasscode);           //5:����ʬ������
        setCellDetailValue($xlWorkSheet, "G", $r, $v_strsalesclassname);           //6:����ʬ̾
        setCellDetailValue($xlWorkSheet, "H", $r, $v_strgoodscode);                //7:�ܵ�����
        setCellDetailValue($xlWorkSheet, "I", $r, $v_strproductcode);              //8:���ʥ�����
        setCellDetailValue($xlWorkSheet, "J", $r, $v_strrevisecode);               //9:���Υ�����
        setCellDetailValue($xlWorkSheet, "K", $r, $v_strproductname);              //10:����̾
        setCellDetailValue($xlWorkSheet, "L", $r, $v_strproductenglishname);       //11:����̾�ʱѸ��
        setCellDetailValue($xlWorkSheet, "M", $r, $v_curproductprice);             //12:ñ��
        setCellDetailValue($xlWorkSheet, "N", $r, $v_lngquantity);                 //13:����
        setCellDetailValue($xlWorkSheet, "O", $r, $v_lngproductquantity);          //14:����
        setCellDetailValue($xlWorkSheet, "P", $r, $v_lngproductunitcode);          //15:����ñ�̥�����
        setCellDetailValue($xlWorkSheet, "Q", $r, $v_strproductunitname);          //16:����ñ��̾
        setCellDetailValue($xlWorkSheet, "R", $r, $v_cursubtotalprice);            //17:����
        setCellDetailValue($xlWorkSheet, "S", $r, $v_strnote);                     //18:��������
        
    }

    $pageHtml = $xlWriter->generateSheetData();

    return $pageHtml;

}

?>
