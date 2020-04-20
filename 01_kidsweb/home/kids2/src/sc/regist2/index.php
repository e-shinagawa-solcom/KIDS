<?php

// ----------------------------------------------------------------------------
/**
 *       売上（納品書）登録
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
require SRC_ROOT . "pc/cmn/lib_pc.php";
//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
include 'JSON.php';

//JSONクラスインスタンス化
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
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
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
    $isCreateNew = true;
    $aryResult = fncGetReceiveDetailHtml($aryDetail, $isCreateNew);
    
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
    $rateResult = fncGetCurConversionRate($aryData["dtmDeliveryDate"], intval($aryDetail[0]["lngmonetaryratecode"]),
    intval($aryDetail[0]["lngmonetaryunitcode"]), $objDB);
    $aryResult["curconversionrate"] = $rateResult->curconversionrate;
    // データ返却
	// echo $strHtml;
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
    $aryResult = fncGetTaxRatePullDown($_POST["dtmDeliveryDate"], "", $objDB);
    // データ返却
    // echo $optTaxRate;
    
	//結果出力
    echo $s->encodeUnsafe($aryResult);
    
    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}

//-------------------------------------------------------------------------
// フォーム初期値設定
//-------------------------------------------------------------------------
// ヘッダ・フッダ部
// 起票者
$aryData['lngInsertUserCode'] = trim($objAuth->UserID);
$aryData['strInsertUserName'] = trim($objAuth->UserFullName);

// 納品日
$nowDate = new DateTime();
$aryData["dtmDeliveryDate"] = $nowDate->format('Y/m/d');

// 支払期限
$oneMonthLater = $nowDate->modify('+1 month');
$aryData["dtmPaymentDueDate"] = $oneMonthLater->format('Y/m/d');

// 支払方法プルダウン
$optPaymentMethod .= fncGetPulldown("m_paymentmethod", "lngpaymentmethodcode", "strpaymentmethodname", "", "", $objDB);
$aryData["optPaymentMethod"] = $optPaymentMethod;

// 消費税区分プルダウン
$optTaxClass = fncGetPulldown("m_taxclass", "lngtaxclasscode", "strtaxclassname", "", "", $objDB);
$aryData["optTaxClass"] = $optTaxClass;

// 消費税率プルダウン
$optTaxRate = fncGetTaxRatePullDown($aryData["dtmDeliveryDate"], "", $objDB);
$aryData["optTaxRate"] = $optTaxRate["strHtml"];

// // レートタイププルダウン
// $optMonetaryRate .= fncGetPulldown("m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", "", "", $objDB);
// $aryData["optMonetaryRate"] = $optMonetaryRate;

// 消費税額
$aryData["strTaxAmount"] = "0";

// 合計金額
$aryData["strTotalAmount"] = "0";

//-------------------------------------------------------------------------
// 画面表示
//-------------------------------------------------------------------------
// ajax POST先をこのファイルにする
$aryData["ajaxPostTarget"] = "index.php";

// 売上（納品書）登録画面表示（テンプレートは納品書修正画面と共通）
// echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);

echo fncGetReplacedHtmlWithBase("base_sc.html", "sc/regist2/regist.html", $aryData, $objAuth);
// DB切断
$objDB->close();

// 処理終了
return true;
