<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  修正確認
 *
 *       処理概要
 *         ・修正した商品情報を登録確認画面に表示する処理
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

// 300 商品管理
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 306 商品管理（商品修正）
if (!fncCheckAuthority(DEF_FUNCTION_P6, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 変更があった項目のみ$aryUpdateに格納
$lngproductno = $aryData["lngProductNo"];

$aryQuery[] = "SELECT ";
$aryQuery[] = "lngproductno, ";
$aryQuery[] = "strProductCode, "; //2:製品コード
$aryQuery[] = "strProductName, "; //3:製品名称
$aryQuery[] = "strProductEnglishName, "; //4:製品名称(英語)
$aryQuery[] = "lngInChargeGroupCode, "; //5:部門
$aryQuery[] = "lngInChargeUserCode, "; //6:担当者
$aryQuery[] = "lnginputusercode, "; //7:入力者
//8:顧客識別コード(表示のみ)
$aryQuery[] = "strGoodsCode, "; //9:商品コード
$aryQuery[] = "strGoodsName, "; //10:商品名称
$aryQuery[] = "lngCustomerCompanyCode as lngCompanyCode, "; //11:顧客
$aryQuery[] = "lngCustomerUserCode as strCustomerUserCode, "; //13:顧客担当者コード
$aryQuery[] = "strCustomerUserName, "; //14:顧客担当者()
$aryQuery[] = "lngPackingUnitCode, "; //15:荷姿単位(int2)
$aryQuery[] = "lngProductUnitCode, "; //16:製品単位(int2)
$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, "; //17:内箱（袋）入数(int4)
$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, "; //18:カートン入数(int4)
$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, "; //19:生産予定数()
$aryQuery[] = "lngProductionUnitCode, "; //20:生産予定数の単位()
$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, "; //21:初回納品数(int4)
$aryQuery[] = "lngFirstDeliveryUnitCode, "; //22:初回納品数の単位()
$aryQuery[] = "lngFactoryCode, "; //23:生産工場()
$aryQuery[] = "lngAssemblyFactoryCode, "; //24:アッセンブリ工場()
$aryQuery[] = "lngDeliveryPlaceCode, "; //25:納品場所(int2)
$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, "; //26:納品期限日()
$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, "; //27:卸値()
$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,"; //28:売値()
$aryQuery[] = "lngTargetAgeCode, "; //29:対象年齢()
$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,"; //30:ロイヤルティー()
$aryQuery[] = "lngCertificateClassCode, "; //31:証紙()
$aryQuery[] = "lngCopyrightCode, "; //32:版権元()
$aryQuery[] = "strCopyrightDisplayStamp, "; //33:版権表示(刻印)
$aryQuery[] = "strCopyrightDisplayPrint, "; //34:版権表示(印刷物)
$aryQuery[] = "lngProductFormCode, "; //35:商品形態()
$aryQuery[] = "strProductComposition, "; //36:製品構成()
$aryQuery[] = "strAssemblyContents, "; //37:アッセンブリ内容()
$aryQuery[] = "strSpecificationDetails, "; //38:仕様詳細()
$aryQuery[] = "strNote, "; //39:備考
$aryQuery[] = "strCopyrightNote, "; //40:版権元備考
$aryQuery[] = "lngCategoryCode, "; // カテゴリーコード
$aryQuery[] = "lngrevisionno, "; // リビジョン番号
$aryQuery[] = "strrevisecode"; // リビジョン番号
$aryQuery[] = "FROM m_product ";
$aryQuery[] = "WHERE bytinvalidflag = false ";
$aryQuery[] = "AND lngproductno = '$lngproductno'";
$strQuery = implode("\n", $aryQuery);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    }
} else {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}


$objDB->freeResult($lngResultID);

// 企画進行状況 ====================================================
$lngproductno = $aryResult["lngproductno"];
$aryQuery = array();
$aryQuery[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
$aryQuery[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate ";
$aryQuery[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
$aryQuery[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
$aryQuery[] = "$lngproductno )";
$strQuery= implode("\n", $aryQuery);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryGoodsPlanResult = $objDB->fetchArray($lngResultID, 0);
    }
} else {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}

$aryNewResult = array();
$aryData["lnggoodsplanprogresscode"] = $aryGoodsPlanResult["lnggoodsplanprogresscode"];	
//改訂番号
$aryData["lngrevisionno"]				= $aryGoodsPlanResult["lngrevisionno"];
//改訂日時
$aryData["dtmrevisiondate"]			= $aryGoodsPlanResult["dtmrevisiondate"];


// クワガタ社内サーバーでは、POSTされたデータのダブルクォートに￥マークが付いてしまうため、これを削除する
$aryData["strSpecificationDetails"] = StripSlashes($aryData["strSpecificationDetails"]);
// http:// 又は https:// のホストが含まれている場合、削除する
$aryData["strSpecificationDetails"] = preg_replace("/(http:\/\/?[^\/]+)|(https:\/\/?[^\/]+)/i", "", $aryData["strSpecificationDetails"]);
// 仕様詳細表示用
$aryData["strSpecificationDetails_DIS"] = nl2br($aryData["strSpecificationDetails"]);
$aryData["strSpecificationDetails"] = preg_replace('/\x0D\x0A|\x0A|\x0D/', "\x0A", $aryData["strSpecificationDetails"]);
// カテゴリー
$aryNewResult["lngCategoryCode_DIS"] = fncGetMasterValue("m_Category", "lngCategoryCode", "strCategoryName", $_POST["lngCategoryCode"], '', $objDB);

// 荷姿単価
$aryNewResult["lngPackingUnitCode_DIS"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngPackingUnitCode"], '', $objDB);

// 製品単位
$aryNewResult["lngProductUnitCode_DIS"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngProductUnitCode"], '', $objDB);

// 商品形態
$aryNewResult["lngProductFormCode_DIS"] = fncGetMasterValue("m_ProductForm", "lngProductFormCode", "strProductFormName", $_POST["lngProductFormCode"], '', $objDB);

// 対象年齢
$aryNewResult["lngTargetAgeCode_DIS"] = fncGetMasterValue("m_targetage", "lngTargetAgeCode", "strTargetAgeName", $_POST["lngTargetAgeCode"], '', $objDB);

// 証紙
$aryNewResult["lngCertificateClassCode_DIS"] = fncGetMasterValue("m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $_POST["lngCertificateClassCode"], '', $objDB);
// 版権元
$aryNewResult["lngCopyrightCode_DIS"] = fncGetMasterValue("m_copyright", "lngcopyrightcode", "strcopyrightname", $_POST["lngCopyrightCode"], '', $objDB);

// 生産予定数
$aryNewResult["lngProductionUnitCode_DIS"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngProductionUnitCode"], '', $objDB);

// 初回納品数
$aryNewResult["lngFirstDeliveryUnitCode_DIS"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngFirstDeliveryUnitCode"], '', $objDB);

// 企画進行状況
$aryNewResult["lngGoodsPlanProgressCode_DIS"] = fncGetMasterValue("m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lnggoodsplanprogresscode"], '', $objDB);

$aryData["lngFactoryCode_DISCODE"] = ($aryData["lngFactoryCode"] != "") ? "[" . $aryData["lngFactoryCode"] . "]" : "";
$aryData["lngAssemblyFactoryCode_DISCODE"] = ($aryData["lngAssemblyFactoryCode"] != "") ? "[" . $aryData["lngAssemblyFactoryCode"] . "]" : "";
$aryData["lngDeliveryPlaceCode_DISCODE"] = ($aryData["lngDeliveryPlaceCode"] != "") ? "[" . $aryData["lngDeliveryPlaceCode"] . "]" : "";

// 仕様詳細HIDDEN用（HIDDENに埋め込むために余分なタグなどを取り除く）
if (strcmp($aryData["strSpecificationDetails"], "") != 0) {
    $aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
    $aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
}

$objDB->close();

$lngUserCode = $objAuth->UserCode;
$strUserName = $objAuth->UserDisplayName;

// 作成日
$aryData["dtminsertdate"] = date('Y/m/d', time());
// 入力コード
$aryData["lnginputusercode"] = $lngUserCode;
// 入力者名称
$aryData["strinputusername"] = $strUserName;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("p/modify/p_confirm_modify.html");
mb_convert_variables('EUC-JP', 'UTF-8', $aryData);
// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$tableDetail = $doc->getElementById("tbl_product_info");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

// 明細情報の出力
$num = 0;
foreach ($aryData as $key => $value) {
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    // key
    $td = $doc->createElement("td", $key);
    $trBody->appendChild($td);
    // value
    if ($key == "lngBoxQuantity" or $key == "lngCartonQuantity" or $key == "lngProductionQuantity"
    or $key == "lngFirstDeliveryQuantity" or $key == "curProductPrice" or $key == "curRetailPrice") {
        $value = str_replace("," , "", $value);
    }
    $td = $doc->createElement("td", toUTF8($value));
    $trBody->appendChild($td);

    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDetail->appendChild($trBody);
}

$objDB->close();

// HTML出力
echo $doc->saveHTML();

// function toUTF8($str)
// {
//     return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
// }

// function money_format($lngmonetaryunitcode, $strmonetaryunitsign, $price)
// {
//     if ($lngmonetaryunitcode == 1) {
//         return "&yen;" . " " . number_format($price, 4);
//     } else {
//         return toUTF8($strmonetaryunitsign . " " . number_format($price, 4));
//     }
// }
