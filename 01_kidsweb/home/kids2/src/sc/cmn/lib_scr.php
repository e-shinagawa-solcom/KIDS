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
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngrevisionno"]);
        $strHtml .= "<td class='forEdit detailRevisionNo'>" . $strDisplayValue . "</td>";
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
        
        $strHtml .= "</tr>";
    }
    return $strHtml;
}




// ����Ǽ�ʽ����Ͽ�ᥤ��ؿ�
function fncRegisterSalesAndSlip($aryHeader, $aryDetail, $objDB, $objAuth)
{
    // ��������
    $dtmNowDate = date( 'Y/m/d', time() );  

    // ��Ͽ�������٤ο�
    $totalItemCount = count($aryDetail);

    // TODO:�ܵҥ����ɤ�ɳ�Ť�Ģɼ1�ڡ���������κ������ٿ����������
    $maxItemPerPage = 999;

    // ����ڡ������η׻�
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);

    for ( $page = 1; $page <= $maxPageCount; $page++ ){

        // ���ߤΥڡ�������1�ڡ�������������ٿ�������Ͽ�������٤Υ���ǥå����κǾ��ͤȺ����ͤ����
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

        // TODO:������ɳ�Ť�Ǽ����ɼ�����ɤ�ȯ��
        $strSlipCode = "";

        // Ǽ����ɼ�ֹ�򥷡����󥹤��ȯ��
        $lngSlipNo = fncGetSequence( 'm_slip.lngslipno', $objDB );

        // ���ޥ�����Ͽ
        if (!fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $aryHeader , $aryDetail, $objDB, $objAuth)){
            // ����
            return false;
        }

        // ���������Ͽ
        if (!fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo, $aryHeader , $aryDetail, $objDB, $objAuth)){
            // ����
            return false;
        }

        // Ǽ����ɼ�ޥ�����Ͽ
        if (!fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $aryHeader , $aryDetail, $objDB, $objAuth)){
            // ����
            return false;
        }
    
        // Ǽ����ɼ������Ͽ
        if (!fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo, $aryHeader, $aryDetail, $objDB, $objAuth)){
            // ����
            return false;
        }

    }

    // ����
    return true;
}

// ���ޥ�����Ͽ
function fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $aryHeader , $aryDetail, $objDB, $objAuth)
{
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
    $aryInsert[] = "  :lngsalesno ";                     //1:����ֹ�
    $aryInsert[] = "  , :lngrevisionno ";                //2:��ӥ�����ֹ�
    $aryInsert[] = "  , :strsalescode ";                 //3:��女����
    $aryInsert[] = "  , :dtmappropriationdate ";         //4:�׾���
    $aryInsert[] = "  , :lngcustomercompanycode ";       //5:�ܵҥ�����
    $aryInsert[] = "  , :lnggroupcode ";                 //6:���롼�ץ�����
    $aryInsert[] = "  , :lngusercode ";                  //7:�桼��������
    $aryInsert[] = "  , :lngsalesstatuscode ";           //8:�����֥�����
    $aryInsert[] = "  , :lngmonetaryunitcode ";          //9:�̲�ñ�̥�����
    $aryInsert[] = "  , :lngmonetaryratecode ";          //10:�̲ߥ졼�ȥ�����
    $aryInsert[] = "  , :curconversionrate ";            //11:�����졼��
    $aryInsert[] = "  , :strslipcode ";                  //12:Ǽ�ʽ�NO
    $aryInsert[] = "  , :lnginvoiceno ";                 //13:������ֹ�
    $aryInsert[] = "  , :curtotalprice ";                //14:��׶��
    $aryInsert[] = "  , :strnote ";                      //15:����
    $aryInsert[] = "  , :lnginputusercode ";             //16:���ϼԥ�����
    $aryInsert[] = "  , :bytinvalidflag ";               //17:̵���ե饰
    $aryInsert[] = "  , :dtminsertdate ";                //18:��Ͽ��
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // TODO:ʸ�����ִ��ˤ�뵿���ѥ�᡼���Х����
    $strQuery = str_replace(":lngsalesno", $value, $strQuery);                     //1:����ֹ�
    $strQuery = str_replace(":lngrevisionno", $value, $strQuery);                  //2:��ӥ�����ֹ�
    $strQuery = str_replace(":strsalescode", $value, $strQuery);                   //3:��女����
    $strQuery = str_replace(":dtmappropriationdate", $value, $strQuery);           //4:�׾���
    $strQuery = str_replace(":lngcustomercompanycode", $value, $strQuery);         //5:�ܵҥ�����
    $strQuery = str_replace(":lnggroupcode", $value, $strQuery);                   //6:���롼�ץ�����
    $strQuery = str_replace(":lngusercode", $value, $strQuery);                    //7:�桼��������
    $strQuery = str_replace(":lngsalesstatuscode", $value, $strQuery);             //8:�����֥�����
    $strQuery = str_replace(":lngmonetaryunitcode", $value, $strQuery);            //9:�̲�ñ�̥�����
    $strQuery = str_replace(":lngmonetaryratecode", $value, $strQuery);            //10:�̲ߥ졼�ȥ�����
    $strQuery = str_replace(":curconversionrate", $value, $strQuery);              //11:�����졼��
    $strQuery = str_replace(":strslipcode", $value, $strQuery);                    //12:Ǽ�ʽ�NO
    $strQuery = str_replace(":lnginvoiceno", $value, $strQuery);                   //13:������ֹ�
    $strQuery = str_replace(":curtotalprice", $value, $strQuery);                  //14:��׶��
    $strQuery = str_replace(":strnote", $value, $strQuery);                        //15:����
    $strQuery = str_replace(":lnginputusercode", $value, $strQuery);               //16:���ϼԥ�����
    $strQuery = str_replace(":bytinvalidflag", $value, $strQuery);                 //17:̵���ե饰
    $strQuery = str_replace(":dtminsertdate", $value, $strQuery);                  //18:��Ͽ��


    // ��Ͽ�¹�
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// ����
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// ����
	return true;
}


// ���������Ͽ
function fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo, $aryHeader , $aryDetail, $objDB, $objAuth)
{
    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        // ��Ͽ���������
        $aryInsert = [];
        $aryInsert[] ="INSERT  ";
        $aryInsert[] ="INTO t_salesdetail(  ";
        $aryInsert[] ="  lngsalesno ";                    //1:����ֹ�
        $aryInsert[] ="  , lngsalesdetailno ";            //2:��������ֹ�
        $aryInsert[] ="  , lngrevisionno ";               //3:��ӥ�����ֹ�
        $aryInsert[] ="  , strproductcode ";              //4:���ʥ�����
        $aryInsert[] ="  , strrevisecode ";               //5:���Υ�����
        $aryInsert[] ="  , lngsalesclasscode ";           //6:����ʬ������
        $aryInsert[] ="  , lngconversionclasscode ";      //7:������ʬ������
        $aryInsert[] ="  , lngquantity ";                 //8:����
        $aryInsert[] ="  , curproductprice ";             //9:���ʲ���
        $aryInsert[] ="  , lngproductquantity ";          //10:���ʿ���
        $aryInsert[] ="  , lngproductunitcode ";          //11:����ñ�̥�����
        $aryInsert[] ="  , lngtaxclasscode ";             //12:�����Ƕ�ʬ������
        $aryInsert[] ="  , lngtaxcode ";                  //13:������Ψ������
        $aryInsert[] ="  , curtaxprice ";                 //14:�����Ƕ��
        $aryInsert[] ="  , cursubtotalprice ";            //15:���׶��
        $aryInsert[] ="  , strnote ";                     //16:����
        $aryInsert[] ="  , lngsortkey ";                  //17:ɽ���ѥ����ȥ���
        $aryInsert[] ="  , lngreceiveno ";                //18:�����ֹ�
        $aryInsert[] ="  , lngreceivedetailno ";          //19:���������ֹ�
        $aryInsert[] ="  , lngreceiverevisionno ";        //20:�����ӥ�����ֹ�
        $aryInsert[] =")  ";                              
        $aryInsert[] ="VALUES (  ";                       
        $aryInsert[] ="  :lngsalesno ";                   //1:����ֹ�
        $aryInsert[] ="  , :lngsalesdetailno ";           //2:��������ֹ�
        $aryInsert[] ="  , :lngrevisionno ";              //3:��ӥ�����ֹ�
        $aryInsert[] ="  , :strproductcode ";             //4:���ʥ�����
        $aryInsert[] ="  , :strrevisecode ";              //5:���Υ�����
        $aryInsert[] ="  , :lngsalesclasscode ";          //6:����ʬ������
        $aryInsert[] ="  , :lngconversionclasscode ";     //7:������ʬ������
        $aryInsert[] ="  , :lngquantity ";                //8:����
        $aryInsert[] ="  , :curproductprice ";            //9:���ʲ���
        $aryInsert[] ="  , :lngproductquantity ";         //10:���ʿ���
        $aryInsert[] ="  , :lngproductunitcode ";         //11:����ñ�̥�����
        $aryInsert[] ="  , :lngtaxclasscode ";            //12:�����Ƕ�ʬ������
        $aryInsert[] ="  , :lngtaxcode ";                 //13:������Ψ������
        $aryInsert[] ="  , :curtaxprice ";                //14:�����Ƕ��
        $aryInsert[] ="  , :cursubtotalprice ";           //15:���׶��
        $aryInsert[] ="  , :strnote ";                    //16:����
        $aryInsert[] ="  , :lngsortkey ";                 //17:ɽ���ѥ����ȥ���
        $aryInsert[] ="  , :lngreceiveno ";               //18:�����ֹ�
        $aryInsert[] ="  , :lngreceivedetailno ";         //19:���������ֹ�
        $aryInsert[] ="  , :lngreceiverevisionno ";       //20:�����ӥ�����ֹ�
        $aryInsert[] =") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // TODO:ʸ�����ִ��ˤ�뵿���ѥ�᡼���Х����
        $strQuery = str_replace(":lngsalesno", $value, $strQuery);                    //1:����ֹ�
        $strQuery = str_replace(":lngsalesdetailno", $value, $strQuery);              //2:��������ֹ�
        $strQuery = str_replace(":lngrevisionno", $value, $strQuery);                 //3:��ӥ�����ֹ�
        $strQuery = str_replace(":strproductcode", $value, $strQuery);                //4:���ʥ�����
        $strQuery = str_replace(":strrevisecode", $value, $strQuery);                 //5:���Υ�����
        $strQuery = str_replace(":lngsalesclasscode", $value, $strQuery);             //6:����ʬ������
        $strQuery = str_replace(":lngconversionclasscode", $value, $strQuery);        //7:������ʬ������
        $strQuery = str_replace(":lngquantity", $value, $strQuery);                   //8:����
        $strQuery = str_replace(":curproductprice", $value, $strQuery);               //9:���ʲ���
        $strQuery = str_replace(":lngproductquantity", $value, $strQuery);            //10:���ʿ���
        $strQuery = str_replace(":lngproductunitcode", $value, $strQuery);            //11:����ñ�̥�����
        $strQuery = str_replace(":lngtaxclasscode", $value, $strQuery);               //12:�����Ƕ�ʬ������
        $strQuery = str_replace(":lngtaxcode", $value, $strQuery);                    //13:������Ψ������
        $strQuery = str_replace(":curtaxprice", $value, $strQuery);                   //14:�����Ƕ��
        $strQuery = str_replace(":cursubtotalprice", $value, $strQuery);              //15:���׶��
        $strQuery = str_replace(":strnote", $value, $strQuery);                       //16:����
        $strQuery = str_replace(":lngsortkey", $value, $strQuery);                    //17:ɽ���ѥ����ȥ���
        $strQuery = str_replace(":lngreceiveno", $value, $strQuery);                  //18:�����ֹ�
        $strQuery = str_replace(":lngreceivedetailno", $value, $strQuery);            //19:���������ֹ�
        $strQuery = str_replace(":lngreceiverevisionno", $value, $strQuery);          //20:�����ӥ�����ֹ�
        
        // ��Ͽ�¹�
        if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
        {
            // ����
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

	// ����
	return true;
}

// Ǽ����ɼ�ޥ�����Ͽ
function fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $aryHeader , $aryDetail, $objDB, $objAuth)
{
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
    $aryInsert[] ="  :lngslipno ";                       //1:Ǽ����ɼ�ֹ�
    $aryInsert[] ="  , :lngrevisionno ";                 //2:��ӥ�����ֹ�
    $aryInsert[] ="  , :strslipcode ";                   //3:Ǽ����ɼ������
    $aryInsert[] ="  , :lngsalesno ";                    //4:����ֹ�
    $aryInsert[] ="  , :strcustomercode ";               //5:�ܵҥ�����
    $aryInsert[] ="  , :strcustomercompanyname ";        //6:�ܵҼ�̾
    $aryInsert[] ="  , :strcustomername ";               //7:�ܵ�̾
    $aryInsert[] ="  , :strcustomeraddress1 ";           //8:�ܵҽ���1
    $aryInsert[] ="  , :strcustomeraddress2 ";           //9:�ܵҽ���2
    $aryInsert[] ="  , :strcustomeraddress3 ";           //10:�ܵҽ���3
    $aryInsert[] ="  , :strcustomeraddress4 ";           //11:�ܵҽ���4
    $aryInsert[] ="  , :strcustomerphoneno ";            //12:�ܵ������ֹ�
    $aryInsert[] ="  , :strcustomerfaxno ";              //13:�ܵ�FAX�ֹ�
    $aryInsert[] ="  , :strcustomerusername ";           //14:�ܵ�ô����̾
    $aryInsert[] ="  , :strshippercode ";                //15:�����襳���ɡʽвټԡ�
    $aryInsert[] ="  , :dtmdeliverydate ";               //16:Ǽ����
    $aryInsert[] ="  , :lngdeliveryplacecode ";          //17:Ǽ�ʾ�ꥳ����
    $aryInsert[] ="  , :strdeliveryplacename ";          //18:Ǽ�ʾ��̾
    $aryInsert[] ="  , :strdeliveryplaceusername ";      //19:Ǽ�ʾ��ô����̾
    $aryInsert[] ="  , :lngpaymentmethodcode ";          //20:��ʧ��ˡ������
    $aryInsert[] ="  , :dtmpaymentlimit ";               //21:��ʧ����
    $aryInsert[] ="  , :lngtaxclasscode ";               //22:���Ƕ�ʬ������
    $aryInsert[] ="  , :strtaxclassname ";               //23:���Ƕ�ʬ
    $aryInsert[] ="  , :curtax ";                        //24:������Ψ
    $aryInsert[] ="  , :strusercode ";                   //25:ô���ԥ�����
    $aryInsert[] ="  , :strusername ";                   //26:ô����̾
    $aryInsert[] ="  , :curtotalprice ";                 //27:��׶��
    $aryInsert[] ="  , :lngmonetaryunitcode ";           //28:�̲�ñ�̥�����
    $aryInsert[] ="  , :strmonetaryunitsign ";           //29:�̲�ñ��
    $aryInsert[] ="  , :dtminsertdate ";                 //30:������
    $aryInsert[] ="  , :strinsertusercode ";             //31:���ϼԥ�����
    $aryInsert[] ="  , :strinsertusername ";             //32:���ϼ�̾
    $aryInsert[] ="  , :strnote ";                       //33:����
    $aryInsert[] ="  , :lngprintcount ";                 //34:�������
    $aryInsert[] ="  , :bytinvalidflag ";                //35:̵���ե饰
    $aryInsert[] =") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // TODO:ʸ�����ִ��ˤ�뵿���ѥ�᡼���Х����
    $strQuery = str_replace(":lngslipno", $value, $strQuery);                        //1:Ǽ����ɼ�ֹ�
    $strQuery = str_replace(":lngrevisionno", $value, $strQuery);                    //2:��ӥ�����ֹ�
    $strQuery = str_replace(":strslipcode", $value, $strQuery);                      //3:Ǽ����ɼ������
    $strQuery = str_replace(":lngsalesno", $value, $strQuery);                       //4:����ֹ�
    $strQuery = str_replace(":strcustomercode", $value, $strQuery);                  //5:�ܵҥ�����
    $strQuery = str_replace(":strcustomercompanyname", $value, $strQuery);           //6:�ܵҼ�̾
    $strQuery = str_replace(":strcustomername", $value, $strQuery);                  //7:�ܵ�̾
    $strQuery = str_replace(":strcustomeraddress1", $value, $strQuery);              //8:�ܵҽ���1
    $strQuery = str_replace(":strcustomeraddress2", $value, $strQuery);              //9:�ܵҽ���2
    $strQuery = str_replace(":strcustomeraddress3", $value, $strQuery);              //10:�ܵҽ���3
    $strQuery = str_replace(":strcustomeraddress4", $value, $strQuery);              //11:�ܵҽ���4
    $strQuery = str_replace(":strcustomerphoneno", $value, $strQuery);               //12:�ܵ������ֹ�
    $strQuery = str_replace(":strcustomerfaxno", $value, $strQuery);                 //13:�ܵ�FAX�ֹ�
    $strQuery = str_replace(":strcustomerusername", $value, $strQuery);              //14:�ܵ�ô����̾
    $strQuery = str_replace(":strshippercode", $value, $strQuery);                   //15:�����襳���ɡʽвټԡ�
    $strQuery = str_replace(":dtmdeliverydate", $value, $strQuery);                  //16:Ǽ����
    $strQuery = str_replace(":lngdeliveryplacecode", $value, $strQuery);             //17:Ǽ�ʾ�ꥳ����
    $strQuery = str_replace(":strdeliveryplacename", $value, $strQuery);             //18:Ǽ�ʾ��̾
    $strQuery = str_replace(":strdeliveryplaceusername", $value, $strQuery);         //19:Ǽ�ʾ��ô����̾
    $strQuery = str_replace(":lngpaymentmethodcode", $value, $strQuery);             //20:��ʧ��ˡ������
    $strQuery = str_replace(":dtmpaymentlimit", $value, $strQuery);                  //21:��ʧ����
    $strQuery = str_replace(":lngtaxclasscode", $value, $strQuery);                  //22:���Ƕ�ʬ������
    $strQuery = str_replace(":strtaxclassname", $value, $strQuery);                  //23:���Ƕ�ʬ
    $strQuery = str_replace(":curtax", $value, $strQuery);                           //24:������Ψ
    $strQuery = str_replace(":strusercode", $value, $strQuery);                      //25:ô���ԥ�����
    $strQuery = str_replace(":strusername", $value, $strQuery);                      //26:ô����̾
    $strQuery = str_replace(":curtotalprice", $value, $strQuery);                    //27:��׶��
    $strQuery = str_replace(":lngmonetaryunitcode", $value, $strQuery);              //28:�̲�ñ�̥�����
    $strQuery = str_replace(":strmonetaryunitsign", $value, $strQuery);              //29:�̲�ñ��
    $strQuery = str_replace(":dtminsertdate", $value, $strQuery);                    //30:������
    $strQuery = str_replace(":strinsertusercode", $value, $strQuery);                //31:���ϼԥ�����
    $strQuery = str_replace(":strinsertusername", $value, $strQuery);                //32:���ϼ�̾
    $strQuery = str_replace(":strnote", $value, $strQuery);                          //33:����
    $strQuery = str_replace(":lngprintcount", $value, $strQuery);                    //34:�������
    $strQuery = str_replace(":bytinvalidflag", $value, $strQuery);                   //35:̵���ե饰
        

    // ��Ͽ�¹�
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// ����
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// ����
	return true;
}

// Ǽ����ɼ������Ͽ
function fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo, $aryHeader, $aryDetail, $objDB, $objAuth)
{
    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        // ��Ͽ���������
        $aryInsert = [];
        $aryInsert[] ="INSERT  ";
        $aryInsert[] ="INTO t_slipdetail(  ";
        $aryInsert[] ="  lngslipno ";                      //1:Ǽ����ɼ�ֹ�
        $aryInsert[] ="  , lngslipdetailno ";              //2:Ǽ����ɼ�����ֹ�
        $aryInsert[] ="  , lngrevisionno ";                //3:��ӥ�����ֹ�
        $aryInsert[] ="  , strcustomersalescode ";         //4:�ܵҼ����ֹ�
        $aryInsert[] ="  , lngsalesclasscode ";            //5:����ʬ������
        $aryInsert[] ="  , strsalesclassname ";            //6:����ʬ̾
        $aryInsert[] ="  , strgoodscode ";                 //7:�ܵ�����
        $aryInsert[] ="  , strproductcode ";               //8:���ʥ�����
        $aryInsert[] ="  , strrevisecode ";                //9:���Υ�����
        $aryInsert[] ="  , strproductname ";               //10:����̾
        $aryInsert[] ="  , strproductenglishname ";        //11:����̾�ʱѸ��
        $aryInsert[] ="  , curproductprice ";              //12:ñ��
        $aryInsert[] ="  , lngquantity ";                  //13:����
        $aryInsert[] ="  , lngproductquantity ";           //14:����
        $aryInsert[] ="  , lngproductunitcode ";           //15:����ñ�̥�����
        $aryInsert[] ="  , strproductunitname ";           //16:����ñ��̾
        $aryInsert[] ="  , cursubtotalprice ";             //17:����
        $aryInsert[] ="  , strnote ";                      //18:��������
        $aryInsert[] ="  , lngreceiveno ";                 //19:�����ֹ�
        $aryInsert[] ="  , lngreceivedetailno ";           //20:���������ֹ�
        $aryInsert[] ="  , lngreceiverevisionno ";         //21:�����ӥ�����ֹ�
        $aryInsert[] ="  , lngsortkey ";                   //22:ɽ���ѥ����ȥ���
        $aryInsert[] =")  ";                               
        $aryInsert[] ="VALUES (  ";                        
        $aryInsert[] ="  :lngslipno ";                     //1:Ǽ����ɼ�ֹ�
        $aryInsert[] ="  , :lngslipdetailno ";             //2:Ǽ����ɼ�����ֹ�
        $aryInsert[] ="  , :lngrevisionno ";               //3:��ӥ�����ֹ�
        $aryInsert[] ="  , :strcustomersalescode ";        //4:�ܵҼ����ֹ�
        $aryInsert[] ="  , :lngsalesclasscode ";           //5:����ʬ������
        $aryInsert[] ="  , :strsalesclassname ";           //6:����ʬ̾
        $aryInsert[] ="  , :strgoodscode ";                //7:�ܵ�����
        $aryInsert[] ="  , :strproductcode ";              //8:���ʥ�����
        $aryInsert[] ="  , :strrevisecode ";               //9:���Υ�����
        $aryInsert[] ="  , :strproductname ";              //10:����̾
        $aryInsert[] ="  , :strproductenglishname ";       //11:����̾�ʱѸ��
        $aryInsert[] ="  , :curproductprice ";             //12:ñ��
        $aryInsert[] ="  , :lngquantity ";                 //13:����
        $aryInsert[] ="  , :lngproductquantity ";          //14:����
        $aryInsert[] ="  , :lngproductunitcode ";          //15:����ñ�̥�����
        $aryInsert[] ="  , :strproductunitname ";          //16:����ñ��̾
        $aryInsert[] ="  , :cursubtotalprice ";            //17:����
        $aryInsert[] ="  , :strnote ";                     //18:��������
        $aryInsert[] ="  , :lngreceiveno ";                //19:�����ֹ�
        $aryInsert[] ="  , :lngreceivedetailno ";          //20:���������ֹ�
        $aryInsert[] ="  , :lngreceiverevisionno ";        //21:�����ӥ�����ֹ�
        $aryInsert[] ="  , :lngsortkey ";                  //22:ɽ���ѥ����ȥ���
        $aryInsert[] =") ";

        // ʸ�����ִ��ˤ�뵿���ѥ�᡼���Х����
        $strQuery = str_replace(":lngslipno", $value, $strQuery);                  //1:Ǽ����ɼ�ֹ�
        $strQuery = str_replace(":lngslipdetailno", $value, $strQuery);            //2:Ǽ����ɼ�����ֹ�
        $strQuery = str_replace(":lngrevisionno", $value, $strQuery);              //3:��ӥ�����ֹ�
        $strQuery = str_replace(":strcustomersalescode", $value, $strQuery);       //4:�ܵҼ����ֹ�
        $strQuery = str_replace(":lngsalesclasscode", $value, $strQuery);          //5:����ʬ������
        $strQuery = str_replace(":strsalesclassname", $value, $strQuery);          //6:����ʬ̾
        $strQuery = str_replace(":strgoodscode", $value, $strQuery);               //7:�ܵ�����
        $strQuery = str_replace(":strproductcode", $value, $strQuery);             //8:���ʥ�����
        $strQuery = str_replace(":strrevisecode", $value, $strQuery);              //9:���Υ�����
        $strQuery = str_replace(":strproductname", $value, $strQuery);             //10:����̾
        $strQuery = str_replace(":strproductenglishname", $value, $strQuery);      //11:����̾�ʱѸ��
        $strQuery = str_replace(":curproductprice", $value, $strQuery);            //12:ñ��
        $strQuery = str_replace(":lngquantity", $value, $strQuery);                //13:����
        $strQuery = str_replace(":lngproductquantity", $value, $strQuery);         //14:����
        $strQuery = str_replace(":lngproductunitcode", $value, $strQuery);         //15:����ñ�̥�����
        $strQuery = str_replace(":strproductunitname", $value, $strQuery);         //16:����ñ��̾
        $strQuery = str_replace(":cursubtotalprice", $value, $strQuery);           //17:����
        $strQuery = str_replace(":strnote", $value, $strQuery);                    //18:��������
        $strQuery = str_replace(":lngreceiveno", $value, $strQuery);               //19:�����ֹ�
        $strQuery = str_replace(":lngreceivedetailno", $value, $strQuery);         //20:���������ֹ�
        $strQuery = str_replace(":lngreceiverevisionno", $value, $strQuery);       //21:�����ӥ�����ֹ�
        $strQuery = str_replace(":lngsortkey", $value, $strQuery);                 //22:ɽ���ѥ����ȥ���
                

        // ��Ͽ�¹�
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);
        if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
        {
            // ����
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

	// ����
	return true;
}
// --------------------------------
// �ѥ�᡼���Х�����ѥإ�Ѵؿ�
// --------------------------------
// ľ���ִ�
function bindDirect($parameterName, $bindValue, $strQuery)
{
    return str_replace($parameterName, $bindValue, $strQuery);
}
// ���󥰥륯�����ȤǰϤ�Ǥ����ִ�
function bindWithQuote($parameterName, $bindValue, $strQuery)
{
    return str_replace($parameterName, "'".$bindValue."'", $strQuery);
}


function fncSample()
{
    // m_sales �Υ��������ֹ�����
    //$sequence_m_sales = fncGetSequence( 'm_sales.lngSalesNo', $objDB );


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
