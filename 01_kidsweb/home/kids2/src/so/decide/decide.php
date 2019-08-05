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
$data = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();

//値が存在しない場合は通常の POST で受ける
if ($data == null) {
    $data = $_POST;
}
// セッション確認
$objAuth = fncIsSession($data["strSessionID"], $objAuth, $objDB);

//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);

$strId = $data["strId"];
$aryId = explode(",", $strId);
$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
$aryQuery[] = ", r.strReceiveCode || '_' || r.strReviseCode as strReceiveCode";
$aryQuery[] = ", r.strcustomerreceivecode";
$aryQuery[] = ", r.strMonetaryUnitSign";
$aryQuery[] = ", r.lngMonetaryUnitCode";
$aryQuery[] = ", rd.lngreceivedetailno as lngreceivedetailno";
$aryQuery[] = ", rd.strProductCode as strProductCode";// 製品コード・名称
$aryQuery[] = ", p.strProductName as strProductName";
$aryQuery[] = ", r.strCompanyDisplayCode as strCompanyDisplayCode";// 顧客コード・名称
$aryQuery[] = ", r.strCompanyDisplayName as strCompanyDisplayName";
$aryQuery[] = ", p.lngproductno as lngproductno";
$aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode";// 売上区分
$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
$aryQuery[] = ", p.strGoodsCode as strGoodsCode";// 顧客品番
$aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate";// 納期
$aryQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";// 単価
$aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode";// 単位
$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
$aryQuery[] = ", p.lngcartonquantity";// カートン入数
$aryQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";// 税抜金額
$aryQuery[] = ", rd.strNote as strDetailNote";// 明細備考
$aryQuery[] = ", ed.lngproductquantity as lngproductquantity";// 製品数量
$aryQuery[] = " FROM t_ReceiveDetail rd";
$aryQuery[] = " LEFT JOIN (";
$aryQuery[] = " SELECT r1.*";
$aryQuery[] = ", m_MonetaryUnit.strMonetaryUnitSign";
$aryQuery[] = ", cust_c.strCompanyDisplayCode";
$aryQuery[] = ", cust_c.strCompanyDisplayName";
$aryQuery[] = " from m_Receive r1";
$aryQuery[] = " LEFT JOIN m_MonetaryUnit USING (lngMonetaryUnitCode) ";
$aryQuery[] = " LEFT JOIN m_Company cust_c ON r1.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = " ) r USING (lngReceiveNo)";
$aryQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
$aryQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
$aryQuery[] = " LEFT JOIN t_estimatedetail ed USING (lngestimateno, lngestimatedetailno)";
$aryQuery[] = " WHERE ";
for ($i = 0; $i < count($aryId); $i++) {
    $id = explode("_", $aryId[$i]);
    $lngReceiveNo = $id[0];
    $lngReceiveDetailNo = $id[1];
    $lngRevisionNo = $id[2];
    if ($i != 0) {
        $aryQuery[] = " OR ";        
    }
    $aryQuery[] = " (rd.lngReceiveNo = " .$lngReceiveNo . " AND rd.lngRevisionNo = " .$lngRevisionNo . " AND rd.lngReceiveDetailNo = " .$lngReceiveDetailNo . ")";
}
$aryQuery[] = " ORDER BY strReceiveCode ASC, lngreceivedetailno DESC";
$strQuery = implode( "\n", $aryQuery );
//結果配列
$result = array();
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// 検索件数がありの場合
if ($lngResultNum) {
    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result['receiveDetail'] = pg_fetch_all($lngResultID);
    }
}

$objDB->freeResult($lngResultID);

$strQuery = "SELECT lngproductunitcode, strproductunitname FROM m_productunit";
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// 検索件数がありの場合
if ($lngResultNum) {
    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result['productUnit'] = pg_fetch_all($lngResultID);
    }
}

$objDB->freeResult($lngResultID);
$objDB->close();

//結果出力
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);



/**
 * LC情報取得
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function getLcInfo($objDB, $data)
{
    $result = fncGetLcInfoData($objDB, $data);
    return $result;
}

