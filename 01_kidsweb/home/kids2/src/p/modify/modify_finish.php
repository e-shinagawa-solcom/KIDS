<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  修正完了
 *
 *       処理概要
 *         ・修正した商品情報を商品修正処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

include 'conf.inc';
require LIB_FILE;
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

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 入力者コードを取得
$lngUserCode = $objAuth->UserCode;

// 300 商品管理
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 306 商品管理（商品修正）
if (!fncCheckAuthority(DEF_FUNCTION_P6, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 部門
$aryData["lngInchargeGroupCode"] = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $aryData["lngInchargeGroupCode"] . ":str", 'bytGroupDisplayFlag=true', $objDB);
// 担当者
$aryData["lngInchargeUserCode"] = fncGetMasterValue("m_user", "struserdisplaycode", "lngusercode", $aryData["lngInchargeUserCode"] . ":str", '', $objDB);
// 開発担当者
$aryData["lngDevelopUserCode"] = fncGetMasterValue("m_user", "struserdisplaycode", "lngusercode", $aryData["lngDevelopUserCode"] . ":str", '', $objDB);
// 顧客
$aryData["lngCustomerCompanyCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCustomerCompanyCode"] . ":str", '', $objDB);
// 顧客担当者
$aryData["lngCustomerUserCode"] = fncGetMasterValue("m_user", "struserdisplaycode", "lngusercode", $aryData["lngCustomerUserCode"] . ":str", '', $objDB);
// 生産工場
$aryData["lngFactoryCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngFactoryCode"] . ":str", '', $objDB);
// アッセンブリ工場
$aryData["lngAssemblyFactoryCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngAssemblyFactoryCode"] . ":str", '', $objDB);
// 納品場所
$aryData["lngDeliveryPlaceCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngDeliveryPlaceCode"] . ":str", '', $objDB);
// 顧客担当者
if (strcmp($aryData["strCustomerUserCode"], "") != 0) {
    $aryData["strCustomerUserCode"] = fncGetMasterValue("m_user", "struserdisplaycode", "lngusercode", $aryData["strCustomerUserCode"] . ":str", '', $objDB);
}

// キー文字列を小文字に変換
$aryData = array_change_key_case($aryData, CASE_LOWER);

// リビジョン番号の設定
//リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ製品に対してロック状態にする
$strLockQuery = "SELECT lngRevisionNo FROM m_Product WHERE strproductcode = '" . $aryData["strproductcode"] . "' FOR UPDATE";
// ロッククエリーの実行
list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
$lngMaxRevision = 0;
if ($lngLockResultNum) {
    for ($i = 0; $i < $lngLockResultNum; $i++) {
        $objRevision = $objDB->fetchObject($lngLockResultID, $i);
        if ($lngMaxRevision < $objRevision->lngrevisionno) {
            $lngMaxRevision = $objRevision->lngrevisionno;
        }
    }
    $lngrevisionno = $lngMaxRevision + 1;
} else {
    $lngrevisionno = $lngMaxRevision;
}

$objDB->freeResult($lngLockResultID);

$aryInsert = array();
// 製品登録
$aryInsert[] = "INSERT INTO M_PRODUCT";
$aryInsert[] = "(lngproductno, ";
$aryInsert[] = "strproductcode, ";
$aryInsert[] = "strproductname, ";
$aryInsert[] = "strproductenglishname, ";
$aryInsert[] = "strgoodscode, ";
$aryInsert[] = "strgoodsname, ";
$aryInsert[] = "lnginchargegroupcode, ";
$aryInsert[] = "lnginchargeusercode, ";
$aryInsert[] = "lngdevelopusercode, ";
$aryInsert[] = "lnginputusercode, ";
$aryInsert[] = "lngcustomercompanycode, ";
// $aryInsert[] = "lngcustomergroupcode, ";
$aryInsert[] = "lngcustomerusercode, ";
$aryInsert[] = "strcustomerusername, ";
$aryInsert[] = "lngpackingunitcode, ";
$aryInsert[] = "lngproductunitcode, ";
$aryInsert[] = "lngboxquantity, ";
$aryInsert[] = "lngcartonquantity, ";
$aryInsert[] = "lngproductionquantity, ";
$aryInsert[] = "lngproductionunitcode, ";
$aryInsert[] = "lngfirstdeliveryquantity, ";
$aryInsert[] = "lngfirstdeliveryunitcode, ";
$aryInsert[] = "lngfactorycode, ";
$aryInsert[] = "lngassemblyfactorycode, ";
$aryInsert[] = "lngdeliveryplacecode, ";
$aryInsert[] = "dtmdeliverylimitdate, ";
$aryInsert[] = "curproductprice, ";
$aryInsert[] = "curretailprice, ";
$aryInsert[] = "lngtargetagecode, ";
$aryInsert[] = "lngroyalty, ";
$aryInsert[] = "lngcertificateclasscode, ";
$aryInsert[] = "lngcopyrightcode, ";
$aryInsert[] = "strcopyrightdisplaystamp, ";
$aryInsert[] = "strcopyrightdisplayprint, ";
$aryInsert[] = "lngproductformcode, ";
$aryInsert[] = "strproductcomposition, ";
$aryInsert[] = "strassemblycontents, ";
$aryInsert[] = "strspecificationdetails, ";
$aryInsert[] = "strnote, ";
$aryInsert[] = "bytinvalidflag, ";
$aryInsert[] = "dtminsertdate, ";
$aryInsert[] = "dtmupdatedate, ";
$aryInsert[] = "strcopyrightnote, ";
$aryInsert[] = "lngcategorycode, ";
$aryInsert[] = "lngrevisionno, ";
$aryInsert[] = "strrevisecode ";
$aryInsert[] = ")VALUES(";
$aryInsert[] = "'" . $aryData["lngProductNo"] . "',";
$aryInsert[] = "'" . $aryData["strproductcode"] . "',";
$aryInsert[] = "'" . mb_convert_encoding($aryData["strproductname"], "euc-jp", "UTF-8") . "',";
$aryInsert[] = "'" . $aryData["strproductenglishname"] . "',";
$aryInsert[] = "'" . $aryData["strgoodscode"] . "',";
$aryInsert[] = "'" . $aryData["strgoodsname"] . "',";
$aryInsert[] = $aryData["lnginchargegroupcode"] . ",";
$aryInsert[] = $aryData["lnginchargeusercode"] . ",";
$aryInsert[] = $aryData["lngdevelopusercode"] . ",";
$aryInsert[] = $lngUserCode . ",";
$aryInsert[] = $aryData["lngcustomercompanycode"] . ",";
// $aryInsert[] = $aryData["lngcustomergroupcode"] . ",";
$aryInsert[] = $aryData["lngcustomerusercode"] . ",";
$aryInsert[] = "'" . mb_convert_encoding($aryData["strcustomerusername"], "euc-jp", "UTF-8") . "',";
$aryInsert[] = ($aryData["lngpackingunitcode"] == "" ? "null" : $aryData["lngpackingunitcode"]) . ",";
$aryInsert[] = ($aryData["lngproductunitcode"] == "" ? "null" : $aryData["lngproductunitcode"]) . ",";
$aryInsert[] = ($aryData["lngboxquantity"] == "" ? "null" : $aryData["lngboxquantity"]) . ",";
$aryInsert[] = $aryData["lngcartonquantity"] . ",";
$aryInsert[] = $aryData["lngproductionquantity"] . ",";
$aryInsert[] = $aryData["lngproductionunitcode"] . ",";
$aryInsert[] = $aryData["lngfirstdeliveryquantity"] . ",";
$aryInsert[] = $aryData["lngfirstdeliveryunitcode"] . ",";
$aryInsert[] = ($aryData["lngfactorycode"] == "" ? "null" : $aryData["lngfactorycode"]) . ",";
$aryInsert[] = ($aryData["lngassemblyfactorycode"] == "" ? "null" : $aryData["lngassemblyfactorycode"]) . ",";
$aryInsert[] = ($aryData["lngdeliveryplacecode"] == "" ? "null" : $aryData["lngdeliveryplacecode"]) . ",";
$aryInsert[] = "To_timestamp('" . $aryData["dtmdeliverylimitdate"] . "','YYYY-MM-DD'),";
$aryInsert[] = ($aryData["curproductprice"] == "" ? "null" : "to_number('" . $aryData["curproductprice"] . "','9999999999.9999')") . ", ";
$aryInsert[] = ($aryData["curretailprice"] == "" ? "null" : "to_number('" . $aryData["curretailprice"] . "','9999999999.9999')") . ", ";
$aryInsert[] = ($aryData["lngtargetagecode"] == "" ? "null" : $aryData["lngtargetagecode"]) . ",";
$aryInsert[] = ($aryData["lngroyalty"] == "" ? "null" : $aryData["lngroyalty"]) . ",";
$aryInsert[] = ($aryData["lngcertificateclasscode"] == "" ? "null" : $aryData["lngcertificateclasscode"]) . ",";
$aryInsert[] = ($aryData["lngcopyrightcode"] == "" ? "null" : $aryData["lngcopyrightcode"]) . ",";
$aryInsert[] = "'" . mb_convert_encoding($aryData["strcopyrightdisplaystamp"], "euc-jp", "UTF-8") . "',";
$aryInsert[] = "'" . $aryData["strcopyrightdisplayprint"] . "',";
$aryInsert[] = $aryData["lngproductformcode"] . ",";
$aryInsert[] = "'" . $aryData["strproductcomposition"] . "',";
$aryInsert[] = "'" . mb_convert_encoding($aryData["strassemblycontents"], "euc-jp", "UTF-8") . "',";
$aryInsert[] = "'" . mb_convert_encoding(stripslashes($aryData["strspecificationdetails"]), "euc-jp", "UTF-8") . "',";
$aryInsert[] = "'" . mb_convert_encoding($aryData["strnote"], "euc-jp", "UTF-8") . "',";
$aryInsert[] = "FALSE,";
$aryInsert[] = "now(),";
$aryInsert[] = "now(),";
$aryInsert[] = "'" . mb_convert_encoding($aryData["strcopyrightnote"], "euc-jp", "UTF-8") . "',";
$aryInsert[] = $aryData["lngcategorycode"] . ",";
$aryInsert[] = $lngrevisionno . ",";
$aryInsert[] = "'" . $aryData["strrevisecode"] . "')";
$strQuery = implode("\n", $aryInsert);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

$objDB->freeResult($lngResultID);

// 商品企画登録
// 商品企画番号の取得
$sequence_t_goodsplan = fncGetSequence('t_goodsplan.lnggoodsplancode', $objDB);
// 作成日の取得
$strCreationQuery = "SELECT dtmCreationDate from t_GoodsPlan WHERE lngProductNo = " . $aryData["lngproductno"] . " and lngRevisionNo = 0 ";

// 検索クエリーの実行
list($lngCreationResultID, $lngCreationResultNum) = fncQuery($strCreationQuery, $objDB);
if ($lngCreationResultNum) {
    $objCreationResult = $objDB->fetchObject($lngCreationResultID, 0);
    if ($objCreationResult->dtmcreationdate != "") {
        $dtmInsertDate = $objCreationResult->dtmcreationdate;
    }
} else {
    $dtmInsertDate = "now()";
}
$objDB->freeResult($lngCreationResultID);

/////   リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ製品に対してロック状態にする
// リビジョン番号値を同じ製品に対してロック状態にする
$strLockQuery = "SELECT lngRevisionNo FROM t_GoodsPlan WHERE lngProductNo = " . $aryData["lngproductno"] . " FOR UPDATE";
// ロッククエリーの実行
list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
$lngMaxRevision = 0;
if ($lngLockResultNum) {
    for ($i = 0; $i < $lngLockResultNum; $i++) {
        $objRevision = $objDB->fetchObject($lngLockResultID, $i);
        if ($lngMaxRevision < $objRevision->lngrevisionno) {
            $lngMaxRevision = $objRevision->lngrevisionno;
        }
    }
}
$revisionNo = $lngMaxRevision + 1;
$objDB->freeResult($lngLockResultID);

$aryUpdate_Goods = array();
$aryUpdate_Goods[] = "INSERT INTO t_goodsplan (";
$aryUpdate_Goods[] = "lnggoodsplancode, ";
$aryUpdate_Goods[] = "lngrevisionno, ";
$aryUpdate_Goods[] = "lngproductno, ";
$aryUpdate_Goods[] = "dtmcreationdate, ";
$aryUpdate_Goods[] = "dtmrevisiondate, ";
$aryUpdate_Goods[] = "lnggoodsplanprogresscode,";
$aryUpdate_Goods[] = "lnginputusercode ";
$aryUpdate_Goods[] = ") values (";
$aryUpdate_Goods[] = $sequence_t_goodsplan . ","; //グッズプランコード
$aryUpdate_Goods[] = $revisionNo . ","; //リビジョン番号
$aryUpdate_Goods[] = $sequence_m_product . ","; //プロダクト
$aryUpdate_Goods[] = "'" . $dtmInsertDate . "', ";
$aryUpdate_Goods[] = "now(), "; //更新日
$aryUpdate_Goods[] = $aryData["lnggoodsplanprogresscode"] . ", ";
$aryUpdate_Goods[] = $lngUserCode;
$aryUpdate_Goods[] = ")";
$strUpdate_Goods = implode("\n", $aryUpdate_Goods);

if (!$lngResultID = $objDB->execute($strUpdate_Goods)) {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}

$objDB->transactionCommit();

$objDB->close();

if ($aryData["dtminsertdate"]) {
    $aryData["dtNowDate"] = substr($aryData["dtminsertdate"], 0, 10);
}
echo $aryData["strSessionID"];
// 帳票出力対応
// 権限を持ってない場合もプレビューボタンを表示しない
if (fncCheckAuthority(DEF_FUNCTION_LO1, $objAuth)) {
    $aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strsessionid"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $sequence_m_product . "&bytCopyFlag=TRUE";

    $aryData["listview"] = 'style="visibility: visible"';
} else {
    $aryData["listview"] = 'style="visibility: hidden"';
}

mb_convert_variables('EUC-JP', 'UTF-8', $aryData);
// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("p/modify/p_finish_modify.html");
// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

return true;
