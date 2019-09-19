<?php
// ----------------------------------------------------------------------------
/**
*       売上（納品書）登録関数群
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
*       処理概要
*         ・売上（納品書）登録関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

function fncGetReceiveDetail($aryCondition, $objDB)
{
    // -------------------
    // SELECT句,FROM句
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
    $aryQuery[] = "  u.struserdisplayname as strsalesdeptname,"; //営業部署（TODO:後でこのクエリ自体修正）
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
    //  WHERE句
    // -------------------
    $aryWhere[] = " WHERE";
    $aryWhere[] = "  r.lngreceivestatuscode = 2";   //受注ステータス=2:受注

    if ($aryCondition["lngReceiveNo"]){
        $aryWhere[] = " AND r.receiveno = " . $aryCondition["lngReceiveNo"];
    }

    if ($aryCondition["strCustomerReceiveCode"]){
        $aryWhere[] = " AND r.strcustomerreceivecode = '" . $aryCondition["strCustomerReceiveCode"] . "'";
    }

    // -------------------
    //  ORDER BY句
    // -------------------
    $aryOrder[] = " ORDER BY";
    $aryOrder[] = "  rd.lngsortkey";
    
    // -------------------
    // クエリ作成
    // -------------------
    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryWhere);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryOrder);

    // -------------------
    // クエリ実行
    // -------------------
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    // 結果を配列に格納
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

        //選択チェックボックス
        $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit'></td>";
        //NO.
        $strHtml .= "<td class='detailSortKey'>" . ($i+1) . "</td>";
        //顧客発注番号
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //受注番号
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //顧客品番
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //製品コード
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductcode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //製品名
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //製品名（英語）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //営業部署
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //売上区分
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //納期
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //単価
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //単位
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //数量
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //税抜金額
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        
        $strHtml .= "</tr>";
    }
    return $strHtml;
}


?>
