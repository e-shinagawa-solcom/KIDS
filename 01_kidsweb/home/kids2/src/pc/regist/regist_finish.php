<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  仕入完了
 *
 *       処理概要
 *         ・登録した仕入情報を仕入登録処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

include 'conf.inc';
require LIB_FILE;
require LIB_EXCLUSIVEFILE;
include 'JSON.php';

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

$aryDetailData = json_decode($aryData["detailData"], true);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 入力者コードを取得
$lngUserCode = $objAuth->UserCode;
// 700 仕入管理
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 701 仕入管理（ 仕入登録）
if (fncCheckAuthority(DEF_FUNCTION_PC1, $objAuth)) {
    $aryData["strRegistURL"] = "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"];
}

$lngpurchaseorderno = $aryData["lngPurchaseOrderNo"];
$lngrevisionno = $aryData["lngpurchaserevisionno"];

$objDB->transactionBegin();

// 発注書データロック
if(!lockOrder($lngpurchaseorderno, $objDB)){
    fncOutputError(9051, DEF_ERROR, "発注書データのロックに失敗しました", true, "", $objDB);
}

// 発注書データ更新有無
if(isPurchaseOrderModified($lngpurchaseorderno, $lngrevisionno, $objDB)){
    fncOutputError(9051, DEF_ERROR, "発注書データが更新または削除されています", true, "", $objDB);
}

// 仕入番号の設定
$sequence_m_stock = fncGetSequence('m_stock.lngStockNo', $objDB);
// リビジョン番号の設定
$lngrevisionno = 0;
// 仕入コードの設定
$strstockcode = fncGetDateSequence(date('Y', strtotime($aryData["dtmStockAppDate"])), date('m', strtotime($aryData["dtmStockAppDate"])), "m_stock.strStockCode", $objDB);
// 仕入状態コードの設定
$lngStockStatusCode = DEF_STOCK_END;
// 仕入先コードを取得
$aryData["lngCustomerCompanyCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCustomerCode"] . ":str", '', $objDB);
// 納品場所コードを取得
$aryData["lngDeliveryPlaceCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngLocationCode"] . ":str", '', $objDB);
// 担当者グループを取得
$aryData["lngGroupCode"] = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $aryData["lngGroupCode"] . ":str", '', $objDB);
// 担当者コードを取得
$aryData["lngUserCode"] = fncGetMasterValue("m_user", "struserdisplaycode", "lngUserCode", $aryData["lngUserCode"] . ":str", '', $objDB);
// 仕入登録
$aryQuery = array();
$aryQuery[] = "INSERT INTO m_stock( ";
$aryQuery[] = "lngstockno, "; // 1:仕入番号
$aryQuery[] = "lngrevisionno, "; // 2:リビジョン番号
$aryQuery[] = "strstockcode, "; // 3:仕入コード / yymmxxx 年月連番で構成された7桁の番号
$aryQuery[] = "dtmappropriationdate, "; // 5:仕入日
$aryQuery[] = "lngcustomercompanycode, "; // 6:仕入先コード
$aryQuery[] = "lnggroupcode, "; // 7:部門コード
$aryQuery[] = "lngusercode, "; // 8:担当者コード
$aryQuery[] = "lngstockstatuscode, "; // 9:仕入状態コード
$aryQuery[] = "lngmonetaryunitcode, "; // 10:通貨単位コード
$aryQuery[] = "lngmonetaryratecode, "; // 11:通貨レートコード
$aryQuery[] = "curconversionrate, "; // 12:適用レート
$aryQuery[] = "lngpayconditioncode, "; // 13:支払い条件
$aryQuery[] = "strslipcode, "; // 14:伝票コード
$aryQuery[] = "curtotalprice, "; // 15:合計金額
$aryQuery[] = "lngdeliveryplacecode, "; // 16:納品場所
$aryQuery[] = "dtmexpirationdate, "; // 17:製品到着日
$aryQuery[] = "strnote, "; // 18:備考
$aryQuery[] = "lnginputusercode, "; // 19:入力者コード
$aryQuery[] = "bytinvalidflag, "; // 20:無効フラグ
$aryQuery[] = "dtminsertdate "; // 21:登録日
$aryQuery[] = " ) VALUES ( ";
$aryQuery[] = $sequence_m_stock . ", "; // 1:仕入番号
$aryQuery[] = $lngrevisionno . ","; // 2:リビジョン番号
$aryQuery[] = "'" . $strstockcode . "', "; // 3:仕入コード
$aryQuery[] = "'" . $aryData["dtmStockAppDate"] . "', "; // 5:計上日
$aryQuery[] = "'" . $aryData["lngCustomerCompanyCode"] . "', "; // 6:仕入先コード
$aryQuery[] = ($aryData["lngGroupCode"] == "" ? "null" : $aryData["lngGroupCode"]) . ", "; // 7:部門コード
$aryQuery[] = ($aryData["lngUserCode"] == "" ? "null" : $aryData["lngUserCode"]) . ", "; // 8:担当者コード

$aryQuery[] = $lngStockStatusCode . ", "; // 9:仕入状態コード
$aryQuery[] = ($aryData["lngMonetaryUnitCode"] == "" ? "null" : $aryData["lngMonetaryUnitCode"]) . ", "; // 10:通貨単位コード
$aryQuery[] = ($aryData["lngMonetaryRateCode"] == "" ? "null" : $aryData["lngMonetaryRateCode"]) . ", "; // 11:通貨レートコード
$aryQuery[] = ($aryData["curConversionRate"] == "" ? "null" : $aryData["curConversionRate"]) . ", "; // 12:適用レート
$aryQuery[] = ($aryData["lngPayConditionCode"] == "" ? "null" : $aryData["lngPayConditionCode"]) . ", "; // 13:支払い条件
$aryQuery[] = "'" . $aryData["strSlipCode"] . "', "; // 14:伝票コード
$aryQuery[] = $aryData["curTotalPrice"] . ", "; // 15:合計金額
$aryQuery[] = ($aryData["lngDeliveryPlaceCode"] == "" ? "null" : $aryData["lngDeliveryPlaceCode"]) . ", "; // 16:納品場所
$aryQuery[] = ($aryData["dtmExpirationDate"] == "" ? "null" : "'" . $aryData["dtmExpirationDate"] . "'") .", "; // 17:製品到着日
$aryQuery[] = "'" . $aryData["strNote"] . "', "; // 18:備考
$aryQuery[] = $lngUserCode . ", "; // 19:入力者コード
$aryQuery[] = "false, "; // 20:無効フラグ
$aryQuery[] = "'" . fncGetDateTimeString() . "'"; // 21:登録日
$aryQuery[] = " )";

$strQuery = implode("\n", $aryQuery);

if (!$lngResultID = $objDB->execute($strQuery)) {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
}

$objDB->freeResult($lngResultID);
// 明細登録処理
foreach ($aryDetailData as $data) {

    // 明細に紐づく発注データのステータスチェック
    if(isOrderModified($data["lngOrderNo"], DEF_ORDER_ORDER, $objDB))
    {
        fncOutputError(9051, DEF_ERROR, "明細データが確定取消されたか、仕入済みです。", true, "", $objDB);
    }
    
    // 明細行
    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "distinct od.lngorderdetailno, "; // 発注明細番号
    $aryQuery[] = "od.lngrevisionno, "; // リビジョン番号
    $aryQuery[] = "mpo.strproductcode, "; // 製品コード
    $aryQuery[] = "mpo.strrevisecode, "; // リバイズ番号
    $aryQuery[] = "pod.lngstocksubjectcode, "; // 仕入科目コード
    $aryQuery[] = "pod.lngstockitemcode, "; // 仕入部品コード
    $aryQuery[] = "To_char( pod.dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate, "; // 納品日
    $aryQuery[] = "pod.lngdeliverymethodcode as lngCarrierCode, "; // 運搬方法コード
    $aryQuery[] = "od.lngconversionclasscode, "; // 換算区分コード / 1：単位計上/ 2：荷姿単位計上
    $aryQuery[] = "pod.curproductprice, "; // 製品価格
    $aryQuery[] = "pod.lngproductquantity, "; // 製品数量
    $aryQuery[] = "pod.lngproductunitcode, "; // 製品単位コード
/*
    $aryQuery[] = "lngtaxclasscode, "; // 消費税区分コード
    $aryQuery[] = "lngtaxcode, "; // 消費税コード
    $aryQuery[] = "curtaxprice, "; // 消費税金額
*/
    $aryQuery[] = "pod.cursubtotalprice, "; // 小計金額
    $aryQuery[] = "pod.strnote, "; // 備考
    $aryQuery[] = "od.strmoldno as strserialno, "; // シリアル
    $aryQuery[] = "od.lngsortkey "; // シリアル
    $aryQuery[] = "FROM t_orderdetail od ";
    $aryQuery[] = "inner join t_purchaseorderdetail pod ";
    $aryQuery[] = "on pod.lngorderno = od.lngorderno ";
    $aryQuery[] = "and pod.lngorderdetailno = od.lngorderdetailno ";
    $aryQuery[] = "and pod.lngorderrevisionno = od.lngrevisionno ";
    $aryQuery[] = "inner join ( ";
    $aryQuery[] = "select lngpurchaseorderno, lngpurchaseorderdetailno, max(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "from t_purchaseorderdetail group by lngpurchaseorderno, lngpurchaseorderdetailno ";
    $aryQuery[] = ") pod_rev on pod_rev.lngpurchaseorderno = pod.lngpurchaseorderno and pod_rev.lngpurchaseorderdetailno = pod.lngpurchaseorderdetailno and pod_rev.lngrevisionno = pod.lngrevisionno ";
    $aryQuery[] = "inner join m_purchaseorder mpo ";
    $aryQuery[] = "on mpo.lngpurchaseorderno = pod.lngpurchaseorderno ";
    $aryQuery[] = "and mpo.lngrevisionno = pod.lngrevisionno ";
    $aryQuery[] = "and mpo.lngpurchaseorderno not in (select  lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0) ";
    $aryQuery[] = "WHERE ";
    $aryQuery[] = "od.lngorderno = " . $data["lngOrderNo"];
    $aryQuery[] = " AND od.lngrevisionno = " . $data["lngRevisionNo"];
    $aryQuery[] = " AND od.lngorderdetailno = " . $data["lngOrderDetailNo"];
    $aryQuery[] = " ORDER BY od.lngSortKey";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// echo $strQuery . "<br>";
// echo $lngResultNum . "<br>";

    if ($lngResultNum) {
        if ($lngResultNum == 1) {
            $detailDataResult = $objDB->fetchArray($lngResultID, 0);
            $objDB->freeResult($lngResultID);
            $strSerialNo = "";
//echo "strSerialNo:" . $detailDataResult["strserialno"] . "<br>";
            if ($detailDataResult["strserialno"] == null or $detailDataResult["strserialno"] == "null" or $detailDataResult["strserialno"] == "") {
                // 仕入科目が４３３（金型海外償却）、仕入部品が１（Injection Mold）の場合
                // 仕入科目が４３１（金型償却高）、　仕入部品が８（金型）の場合
                if (($detailDataResult["lngstocksubjectcode"] == DEF_MOLD_STOCK_SUBJECT
                    and $detailDataResult["lngstockitemcode"] == DEF_MOLD_STOCK_ITEM)
                    or ($detailDataResult["lngstocksubjectcode"] == DEF_MOLD_STOCK_SUBJECT_ADD
                        and $detailDataResult["lngstockitemcode"] == DEF_MOLD_STOCK_ITEM_ADD)) {
                    $strSerialNo = fncGetMoldNo($detailDataResult["strproductcode"], $detailDataResult["strrevisecode"], $detailDataResult["lngstocksubjectcode"], $detailDataResult["lngstockitemcode"], $objDB);
                }
            } else
            // 指定されている仕入科目、仕入部品が金型番号使用でない場合は金型番号箇所にはNULL指定
            {
                // 仕入科目が４３３（金型海外償却）、仕入部品が１（Injection Mold）の場合
                // 仕入科目が４３１（金型償却高）、　仕入部品が８（金型）の場合
                if (($detailDataResult["lngstocksubjectcode"] == DEF_MOLD_STOCK_SUBJECT
                    and $detailDataResult["lngstockitemcode"] == DEF_MOLD_STOCK_ITEM)
                    or ($detailDataResult["lngstocksubjectcode"] == DEF_MOLD_STOCK_SUBJECT_ADD
                        and $detailDataResult["lngstockitemcode"] == DEF_MOLD_STOCK_ITEM_ADD)) {
                    $strSerialNo = $detailDataResult["strserialno"];
                }
            }
            //-----------------------------------------------------------
            // DB -> INSERT : t_stockdetail
            //-----------------------------------------------------------
            $aryQuery = array();
            $aryQuery[] = "INSERT INTO t_stockdetail ( ";
            $aryQuery[] = "lngstockno, "; // 1:仕入番号
            $aryQuery[] = "lngstockdetailno, "; // 2:仕入明細番号
            $aryQuery[] = "lngrevisionno, "; // 3:リビジョン番号
            $aryQuery[] = "lngorderno, "; // 3:リビジョン番号            
            $aryQuery[] = "lngorderdetailno, "; // 4:発注明細番号
            $aryQuery[] = "lngorderrevisionno, "; // 4:発注明細リビジョン番号
            $aryQuery[] = "strproductcode, "; // 5:製品コード
            $aryQuery[] = "strrevisecode, "; // 6:リバイスコード
            $aryQuery[] = "lngstocksubjectcode, "; // 7:仕入科目コード
            $aryQuery[] = "lngstockitemcode, "; // 8:仕入部品コード
            $aryQuery[] = "lngdeliverymethodcode, "; // 10:運搬方法
            $aryQuery[] = "lngconversionclasscode, "; // 11:換算区分コード / 1：単位計上/ 2：荷姿単位計上
            $aryQuery[] = "curproductprice, "; // 12:製品価格
            $aryQuery[] = "lngproductquantity, "; // 13:製品数量
            $aryQuery[] = "lngproductunitcode, "; // 14:製品単位コード
            $aryQuery[] = "lngtaxclasscode, "; // 15:消費税区分コード
            $aryQuery[] = "lngtaxcode, "; // 16:消費税コード
            $aryQuery[] = "curtaxprice, "; // 17:税額
            $aryQuery[] = "cursubtotalprice, "; // 18:小計金額 / 税抜小計金額
            $aryQuery[] = "strnote, "; // 19:備考
            $aryQuery[] = "strmoldno, "; // 20:金型番号
            $aryQuery[] = "lngSortKey "; // 21:表示用ソートキー
            $aryQuery[] = " ) VALUES ( ";
            $aryQuery[] = $sequence_m_stock . ", "; // 1:仕入番号
            $aryQuery[] = $data["lngStockDetailNo"] . ", "; // 2:仕入明細番号 行ごとの明細発注は持っている
            $aryQuery[] = $lngrevisionno . ", "; // 3:リビジョン番号
            $aryQuery[] = $data["lngOrderNo"] . ", "; // 4:発注番号
            $aryQuery[] = $data["lngOrderDetailNo"] . ", "; // 4:発注明細番号
            $aryQuery[] = $data["lngRevisionNo"] . ", "; // 4:発注明細リビジョン番号
            $aryQuery[] = "'" . $detailDataResult["strproductcode"] . "', "; // 5:製品コード
            $aryQuery[] = "'" . $detailDataResult["strrevisecode"] . "', "; // 6:リバイスコード
            $aryQuery[] = $detailDataResult["lngstocksubjectcode"] . ", "; // 7:仕入科目コード
            $aryQuery[] = $detailDataResult["lngstockitemcode"] . ", "; // 8:仕入部品コード
            $aryQuery[] = $detailDataResult["lngcarriercode"] . ", "; // 10:運搬方法
            $aryQuery[] = $detailDataResult["lngconversionclasscode"] . ", "; // 11:換算区分コード / 1：単位計上/ 2：荷姿単位計上
            $aryQuery[] = $detailDataResult["curproductprice"] . ", "; // 9:製品価格
            $aryQuery[] = $detailDataResult["lngproductquantity"] . ", "; // 10:製品数量
            $aryQuery[] = $detailDataResult["lngproductunitcode"] . ", "; // 11:製品単位コード
            $aryQuery[] = $data["lngTaxClassCode"] . ", "; // 12:消費税区分コード
            $aryQuery[] = ($data["lngTaxCode"] == null ? "null" : $data["lngTaxCode"]) . ", "; // 13:消費税コード
            $aryQuery[] = $data["curTaxPrice"] . ", "; // 14:税額
            $aryQuery[] = $detailDataResult["cursubtotalprice"] . ", "; // 15:小計金額 / 税抜小計金額
            $aryQuery[] = "'" . $detailDataResult["strnote"] . "', "; // 16:備考
            $aryQuery[] = "'" . $strSerialNo . "', "; // 17:金型番号
            $aryQuery[] = ($detailDataResult["lngsortkey"] == "" ? "null" : $detailDataResult["lngsortkey"]) . " "; // 18:表示用ソートキー
            $aryQuery[] = " )";
            $strQuery = implode("\n", $aryQuery);
            if (!$lngResultID = $objDB->execute($strQuery)) {
                fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
            }

            $objDB->freeResult($lngResultID);
        }
    }

    // 発注更新
    $aryQuery = array();
    $aryQuery[] = "UPDATE m_order ";
    $aryQuery[] = "set lngorderstatuscode = " . DEF_ORDER_END . " ";
    $aryQuery[] = "where lngorderno = " . $data["lngOrderNo"] . " ";
    $aryQuery[] = "and lngrevisionno = " . $data["lngRevisionNo"] . " ";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    $objDB->freeResult($lngResultID);
}

$objDB->transactionCommit();

$objDB->close();

$aryResult["strStockCode"] = $strstockcode;
$aryResult["dtmStockAppDate"] = $aryData["dtmStockAppDate"];
$aryResult["strOrderCode"] = $aryData["strOrderCode"];
// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/pc/regist/pc_finish_regist.html" );

// テンプレート生成
$objTemplate->replace($aryResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

return true;