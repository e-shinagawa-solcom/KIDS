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


?>
