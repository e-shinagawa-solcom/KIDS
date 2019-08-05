<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  確定登録確認
*
*       処理概要
*         ・登録した確定情報を確定確認画面に表示する処理
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


// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
// 権限確認
// 402 受注管理（受注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 404 受注管理（確定）
if ( !fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/so/decide/so_confirm_decide.html");
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
$tableDetail = $doc->getElementById("table_decide_detail");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

// 明細情報の出力
$num = 0;
foreach ($aryData["detailData"] as $data) {
    $num += 1;
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    // No.
    $td = $doc->createElement("td", $num);
    $trBody->appendChild($td);

    // 顧客
    $td = $doc->createElement("td", $data["strCompanyDisplayCode"]);
    $trBody->appendChild($td);

    // 受注NO.
    $td = $doc->createElement("td", $data["strReceiveCode"]);
    $trBody->appendChild($td);

    // 明細行番号
    $td = $doc->createElement("td", $data["lngReceiveDetailNo"]);
    $trBody->appendChild($td);

    // 顧客受注番号.
    $td = $doc->createElement("td", $data["strCustomerReceiveCode"]);
    $trBody->appendChild($td);

    // 製品
    $td = $doc->createElement("td", $data["strProductCode"]);
    $trBody->appendChild($td);

    // 顧客品番
    $td = $doc->createElement("td", $data["strGoodsCode"]);
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", $data["dtmDeliveryDate"]);
    $trBody->appendChild($td);

    // 売上区分
    $td = $doc->createElement("td", $data["lngSalesClassCode"]);
    $trBody->appendChild($td);
    
    // 単価
    $td = $doc->createElement("td", $data["curProductPrice"]);
    $trBody->appendChild($td);

    // 単位
    $td = $doc->createElement("td", $data["strProductUnitName"]);
    $trBody->appendChild($td);

    // 入数
    $td = $doc->createElement("td", $data["lngUnitQuantity"]);
    $trBody->appendChild($td);
    // 数量
    $td = $doc->createElement("td", $data["lngProductQuantity"]);
    $trBody->appendChild($td);

    // 小計
    $td = $doc->createElement("td", $data["curSubtotalPrice"]);
    $trBody->appendChild($td);

    // 備考
    $td = $doc->createElement("td", $data["strDetailNote"]);
    $trBody->appendChild($td);

    // 受注番号
    $td = $doc->createElement("td", $data["lngReceiveNo"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // リビジョン番号
    $td = $doc->createElement("td", $data["lngRevisionNo"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 単位コード
    $td = $doc->createElement("td", $data["lngProductUnitCode"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDetail->appendChild($trBody);

}

$objDB->close();

// HTML出力
echo $doc->saveHTML();

function toUTF8($str)
{
    return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}

?>