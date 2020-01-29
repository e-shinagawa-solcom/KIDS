<?php

// ----------------------------------------------------------------------------
/**
 *       納品書修正
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
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ライブラリファイル読込
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "sc/cmn/lib_scr.php";
require SRC_ROOT . "sc/cmn/lib_scd1.php";
require SRC_ROOT . "pc/cmn/lib_pc.php";
//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
include 'JSON.php';

$s = new Services_JSON();
$objDB = new clsDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// パラメータ取得
//-------------------------------------------------------------------------
// セッションID
if ($_POST["strSessionID"]) {
    $aryData["strSessionID"] = $_POST["strSessionID"];
} else {
    $aryData["strSessionID"] = $_REQUEST["strSessionID"];
}
setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// 処理モード
$strMode = $_POST["strMode"];

// --------------------
// 修正対象に紐づく情報
// --------------------
// 納品伝票番号
$lngSlipNo = $_GET["lngSlipNo"];
// 納品伝票コード
$strSlipCode = $_GET["strSlipCode"];
// 売上番号
$lngSalesNo = $_GET["lngSalesNo"];
// 売上コード
$strSalesCode = $_GET["strSalesCode"];
// 顧客コード
$strCustomerCode = $_GET["strCustomerCode"];

$revisionNo = $_GET["lngRevisionNo"];

//-------------------------------------------------------------------------
// DBオープン
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// 入力文字列値・セッション・権限チェック
//-------------------------------------------------------------------------
// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
$lngUserCode = $objAuth->UserCode;
$lngUserGroup = $objAuth->AuthorityGroupCode;

// 600 売上管理
if (!fncCheckAuthority(DEF_FUNCTION_SC0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// 601 売上管理（売上登録）
if (fncCheckAuthority(DEF_FUNCTION_SC1, $objAuth)) {
    $aryData["strRegistURL"] = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
}

// 610 売上管理（行追加・行削除）
if (!fncCheckAuthority(DEF_FUNCTION_SC10, $objAuth)) {
    $aryData["adddelrowview"] = 'hidden';
}


//-------------------------------------------------------------------------
// 【ajax】顧客に紐づく国コードを取得
//-------------------------------------------------------------------------
if ($strMode == "get-lngcountrycode") {
    // 顧客コード
    $strCompanyDisplayCode = $_POST["strcompanydisplaycode"];
    // 国コード取得
    $lngCountryCode = fncGetCountryCode($strCompanyDisplayCode, $objDB);
    // データ返却
    echo $lngCountryCode;
    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}

//-------------------------------------------------------------------------
// 【ajax】顧客に紐づく締め日を取得
//-------------------------------------------------------------------------
if ($strMode == "get-closedday") {
    // 顧客コード
    $strCompanyDisplayCode = $_POST["strcompanydisplaycode"];
    // 締め日取得
    $lngClosedDay = fncGetClosedDay($strCompanyDisplayCode, $objDB);
    // データ返却
    echo $lngClosedDay;
    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}

//-------------------------------------------------------------------------
// 【ajax】明細検索
//-------------------------------------------------------------------------
if ($strMode == "search-detail") {
    // 検索条件の取得
    $aryCondition = $_POST["condition"];
    // 固定検索条件の追加
    $aryCondition["lngreceivestatuscode"] = 2; //受注状態コード=2:受注
    // DBから明細を検索
    $aryDetail = fncGetReceiveDetail($aryCondition, $objDB);
    // 明細選択エリアに出力するHTMLの作成
    $withCheckBox = true;
    $aryResult = fncGetReceiveDetailHtml($aryDetail, $withCheckBox);
    
    // 通貨単位
    $aryResult["strmonetaryunitname"] = $aryDetail[0]["strmonetaryunitname"];
    $aryResult["lngmonetaryunitcode"] = $aryDetail[0]["lngmonetaryunitcode"];
    // レートタイプ
    $aryResult["lngmonetaryratecode"] = $aryDetail[0]["lngmonetaryratecode"];
    $aryResult["strmonetaryratename"] = $aryDetail[0]["strmonetaryratename"];
    // 顧客
    $aryResult["strcompanydisplaycode"] = $aryDetail[0]["strcompanydisplaycode"];
    $aryResult["strcompanydisplayname"] = $aryDetail[0]["strcompanydisplayname"];

    // 換算レートの取得
    $curconversionrate = fncGetCurConversionRate($aryData["dtmDeliveryDate"], $aryDetail[0]["lngmonetaryratecode"],
    $aryDetail[0]["lngmonetaryunitcode"], $objDB);
    $aryResult["curconversionrate"] = $curconversionrate;

	//結果出力
	echo $s->encodeUnsafe($aryResult);

    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}

//-------------------------------------------------------------------------
// 【ajax】納品日変更時の消費税率選択項目再設定
//-------------------------------------------------------------------------
if ($strMode == "change-deliverydate") {
    // 変更後の納品日に対応する消費税率の選択項目を取得
    $optTaxRate = fncGetTaxRatePullDown($_POST["dtmDeliveryDate"], "", $objDB);
    // データ返却
    echo $optTaxRate;
    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}
//-------------------------------------------------------------------------
// 修正画面を閉じる時ロックを解除
//-------------------------------------------------------------------------
if ($strMode == "releaseLock") {
/*
    // 他にロックしている人がいないか確認
    $aryLockInfo = fncGetExclusiveLockUser(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
    if ($aryLockInfo["isLock"] != 0) {
        // 修正対象データのロックを解除する
        fncReleaseExclusiveLock(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objDB);
    }
    // DB切断
    $objDB->close();
*/
    // 処理終了
    return true;
}
//-------------------------------------------------------------------------
// 修正対象データのロック取得
//-------------------------------------------------------------------------
// 他にロックしている人がいないか確認
/*
$aryLockInfo = fncGetExclusiveLockUser(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
if ($aryLockInfo["isLock"] == 1) {
    MoveToErrorPage("ユーザー" . $aryLockInfo["lockUserName"] . "が修正中です。");
} else if ($aryLockInfo["isLock"] == 0) {
    // 修正対象データのロックを取る
    if (!fncTakeExclusiveLock(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB)) {
        MoveToErrorPage("納品書データのロックに失敗しました。");
    }
}
*/
//-------------------------------------------------------------------------
// 修正対象データ取得
//-------------------------------------------------------------------------
// 納品伝票番号に紐づくヘッダ・フッタ部のデータ読み込み
$revisionNo = fncGetSlipMaxRevisionNo($lngSlipNo, $objDB);
$aryHeader = fncGetHeaderBySlipNo($lngSlipNo, $revisionNo, $objDB);
// $lngRevisionNo = $aryHeader["lngrevisionno"];
// 納品伝票番号に紐づく受注明細情報を取得する
$aryDetail = fncGetDetailBySlipNo($lngSlipNo, $revisionNo, $objDB);

// 明細部のHTMLを生成
$isCreateNew = false; //修正モード用

$aryDetailResult = fncGetReceiveDetailHtml($aryDetail, $isCreateNew);

//-------------------------------------------------------------------------
// フォーム初期値設定
//-------------------------------------------------------------------------
// -------------------------
//  修正対象データに紐づく値
// -------------------------
// 納品伝票番号（この値がセットされていたら修正とみなす）
$aryData["lngSlipNo"] = $lngSlipNo;
$aryData["lngRevisionNo"] = $revisionNo;
// 納品伝票コード
$aryData["strSlipCode"] = $strSlipCode;
// 売上番号
$aryData["lngSalesNo"] = $lngSalesNo;
// 売上コード
$aryData["strSalesCode"] = $strSalesCode;

// -------------------------
//  出力明細一覧エリア
// -------------------------
// 明細部のHTMLをセット
$aryData["strEditTableBody"] = $aryDetailResult["detail_body"];
$aryData["strEditTableNo"] = $aryDetailResult["chkbox_body"];

// -------------------------
//  ヘッダ・フッダ部
// -------------------------
// 起票者
$aryData["lngUserCode"] = $aryHeader["strdrafteruserdisplaycode"];
$aryData["strUserName"] = $aryHeader["strdrafteruserdisplayname"];

// 顧客
$aryData["lngCustomerCode"] = $aryHeader["strcompanydisplaycode"];
$aryData["strCustomerName"] = $aryHeader["strcompanydisplayname"];

// 顧客側担当者
$aryData["strCustomerUserName"] = $aryHeader["strcustomerusername"];

// 納品日
$aryData["dtmDeliveryDate"] = $aryHeader["dtmdeliverydate"];

// 支払方法プルダウン
$lngDefaultPaymentMethodCode = $aryHeader["lngpaymentmethodcode"];
$optPaymentMethod .= fncGetPulldown("m_paymentmethod", "lngpaymentmethodcode", "strpaymentmethodname", $lngDefaultPaymentMethodCode, "", $objDB);
$aryData["optPaymentMethod"] = $optPaymentMethod;

// 支払期限
$aryData["dtmPaymentLimit"] = $aryHeader["dtmpaymentlimit"];

// 納品先
$aryData["lngDeliveryPlaceCode"] = $aryHeader["strdeliveryplacecompanydisplaycode"];
$aryData["strDeliveryPlaceName"] = $aryHeader["strdeliveryplacename"];

// 納品先担当者
$aryData["strDeliveryPlaceUserName"] = $aryHeader["strdeliveryplaceusername"];

// 備考
$aryData["strNote"] = $aryHeader["strnote"];

// 消費税区分プルダウン
$lngDefaultTaxClassCode = $aryHeader["lngtaxclasscode"];
$optTaxClass .= fncGetPulldown("m_taxclass", "lngtaxclasscode", "strtaxclassname", $lngDefaultTaxClassCode, "", $objDB);
$aryData["optTaxClass"] = $optTaxClass;

// 消費税率プルダウン
if ($aryData["dtmDeliveryDate"]) {
    $curDefaultTax = $aryHeader["curtax"];
    $optTaxRate = fncGetTaxRatePullDown($aryData["dtmDeliveryDate"], $curDefaultTax, $objDB);
    $aryData["optTaxRate"] = $optTaxRate;
}

// 消費税額（※ここでは0をセットしておき、画面表示時にjavascriptで関数を呼び出して計算する）
$aryData["strTaxAmount"] = "0";

// 合計金額
$aryData["strTotalAmount"] = $aryHeader["curtotalprice"];

// 通貨単位
$aryData["strMonetaryUnitName"] = $aryHeader["strmonetaryunitname"];
// レートタイプ
$aryData["strMonetaryRateName"] = $aryHeader["strmonetaryratename"];
// 適用レート
$aryData["curConversionRate"] = $aryHeader["curconversionrate"];

//-------------------------------------------------------------------------
// 画面表示
//-------------------------------------------------------------------------
// ajax POST先をこのファイルにする
$aryData["ajaxPostTarget"] = "renew.php";

// 納品書修正画面表示
// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/regist2/renew.html" );

// テンプレート生成
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

// DB切断
$objDB->close();

// 処理終了
return true;

// エラー画面への遷移
function MoveToErrorPage($strMessage)
{

    // 言語コード：日本語
    $aryHtml["lngLanguageCode"] = 1;

    // エラーメッセージの設定
    $aryHtml["strErrorMessage"] = $strMessage;

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML出力
    echo $objTemplate->strTemplate;

    exit;
}
