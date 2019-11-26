<?php
// ----------------------------------------------------------------------------
/**
 *       受注管理 確定画面の確定ボタン
 *
 *       処理概要
 *         ・確定対象明細行選択部で選択した行を確定する処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;

//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
include 'JSON.php';

//値の取得
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();

//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

// セッション確認
$objAuth = fncIsSession($_GET["strSessionID"], $objAuth, $objDB);

$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
$aryQuery[] = ", r.strReceiveCode";
$aryQuery[] = ", r.strcustomerreceivecode";
$aryQuery[] = ", r.strMonetaryUnitSign";
$aryQuery[] = ", rd.lngreceivedetailno as lngreceivedetailno";
$aryQuery[] = ", rd.strProductCode as strProductCode"; // 製品コード・名称
$aryQuery[] = ", p.strProductName as strProductName";
$aryQuery[] = ", r.strCompanyDisplayCode as strCustomerDisplayCode"; // 顧客コード・名称
$aryQuery[] = ", r.strCompanyDisplayName as strCustomerDisplayName";
$aryQuery[] = ", p.lngproductno as lngproductno";
$aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode"; // 売上区分
$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
$aryQuery[] = ", p.strGoodsCode as strGoodsCode"; // 顧客品番
$aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate"; // 納期
$aryQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice"; // 単価
$aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode"; // 単位
$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
$aryQuery[] = ", p.lngcartonquantity"; // カートン入数
$aryQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice"; // 税抜金額
$aryQuery[] = ", rd.strNote as strDetailNote"; // 明細備考
$aryQuery[] = ", ed.lngproductquantity as lngproductquantity"; // 製品数量
$aryQuery[] = " FROM t_ReceiveDetail rd";
$aryQuery[] = "  INNER JOIN ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      r1.*";
$aryQuery[] = "      , m_MonetaryUnit.strMonetaryUnitSign";
$aryQuery[] = "      , cust_c.strCompanyDisplayCode";
$aryQuery[] = "      , cust_c.strCompanyDisplayName ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_Receive r1 ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        select";
$aryQuery[] = "          max(lngrevisionno) lngrevisionno";
$aryQuery[] = "          , strReceiveCode ";
$aryQuery[] = "        from";
$aryQuery[] = "          m_Receive ";
$aryQuery[] = "        group by";
$aryQuery[] = "          strReceiveCode";
$aryQuery[] = "      ) r2 ";
$aryQuery[] = "        on r1.lngrevisionno = r2.lngrevisionno ";
$aryQuery[] = "        and r1.strReceiveCode = r2.strReceiveCode ";
$aryQuery[] = "      LEFT JOIN m_MonetaryUnit ";
$aryQuery[] = "        USING (lngMonetaryUnitCode) ";
$aryQuery[] = "      LEFT JOIN m_Company cust_c ";
$aryQuery[] = "        ON r1.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "    WHERE";
$aryQuery[] = "      r1.lngreceivestatuscode = " . DEF_RECEIVE_APPLICATE ." ";
if ($aryData["strReceiveCode"] != "") {
    $aryQuery[] = " AND r1.strReceiveCode = '" . $aryData["strReceiveCode"] . "' ";
}
if ($aryData["lngCustomerCode"] != "") {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $aryData["lngCustomerCode"] . "' ";
}
if ($aryData["From_dtmDeliveryDate"] != "") {
    $aryQuery[] = " AND r1.dtmDeliveryDate >= '" . $aryData["From_dtmDeliveryDate"] . "' ";
}
if ($aryData["To_dtmDeliveryDate"] != "") {
    $aryQuery[] = " AND r1.dtmDeliveryDate <= '" . $aryData["To_dtmDeliveryDate"] . "' ";
}
$aryQuery[] = " ) r USING (lngReceiveNo, lngRevisionNo)";    
$aryQuery[] = "        LEFT JOIN (";
$aryQuery[] = "            select p1.*  from m_product p1 ";
$aryQuery[] = "        	inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
$aryQuery[] = "            on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode";
$aryQuery[] = "          ) p ";
$aryQuery[] = "          ON rd.strProductCode = p.strProductCode AND rd.strrevisecode = p.strrevisecode ";
$aryQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
$aryQuery[] = " LEFT JOIN t_estimatedetail ed USING (lngestimateno, lngestimatedetailno)";
$aryQuery[] = " WHERE not exists (select strreceivecode from m_receive where lngrevisionno < 0  and strreceivecode = r.strreceivecode)";

if ($aryData["strProductCode"] != "") {
    $aryQuery[] = " and rd.strProductCode = '" . $aryData["strProductCode"] . "' ";
}
$aryQuery[] = " ORDER BY rd.lngSortKey ASC ";

$strQuery = implode("\n", $aryQuery);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
$result = array();
if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result[] = $objDB->fetchArray($lngResultID, $i);
    }
}
$objDB->freeResult($lngResultID);

$objDB->close();
$aryResult["result"] = $result;
$aryResult["count"] = $lngResultNum;
//結果出力
mb_convert_variables('UTF-8', 'EUC-JP', $aryResult);
echo $s->encodeUnsafe($aryResult);
