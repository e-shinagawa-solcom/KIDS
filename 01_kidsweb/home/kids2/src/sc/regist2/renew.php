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
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
	require (SRC_ROOT."sc/cmn/lib_scd1.php");

	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	// セッションID
	if ($_POST["strSessionID"]){
		$aryData["strSessionID"] = $_POST["strSessionID"];
	}else{
		$aryData["strSessionID"] = $_REQUEST["strSessionID"];   
	}
	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// 処理モード
	$strMode    = $_POST["strMode"];

	// 修正対象に紐づく情報
	$lngSlipNo = $_GET["lngSlipNo"];
	$lngSalesNo = $_GET["lngSalesNo"];
	$strCustomerCode = $_GET["strCustomerCode"];

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;

	// 600 売上管理
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 601 売上管理（売上登録）
	if( fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// 610 売上管理（行追加・行削除）
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}

	// --------------------------------
	//    修正可能かどうかのチェック
	// --------------------------------
	// 顧客の国が日本で、かつ納品書ヘッダに紐づく請求書明細が存在する場合は修正不可
	if (fncJapaneseInvoiceExists($strCustomerCode, $lngSalesNo, $objDB)){
		MoveToErrorPage("請求書発行済みのため、修正できません");
	}

	// 納品書明細に紐づく受注ステータスが「締済み」の場合は修正不可
	if (fncReceiveStatusIsClosed($lngSlipNo, $objDB))
	{
		MoveToErrorPage("締済みのため、修正できません");
	}

	//-------------------------------------------------------------------------
	// 【ajax】顧客に紐づく国コードを取得
	//-------------------------------------------------------------------------
	if ($strMode == "get-lngcountrycode"){
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
	if ($strMode == "get-closedday"){
		// 顧客コード
		$strCompanyDisplayCode = $_POST["strcompanydisplaycode"];
		// TODO:締め日取得
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
	if($strMode == "search-detail"){
		// DBから明細を検索
		$aryReceiveDetail = fncGetReceiveDetail($_POST["condition"], $objDB);
		// 明細選択エリアに出力するHTMLの作成
		$strHtml = fncGetReceiveDetailHtml($aryReceiveDetail);
		// データ返却
		echo $strHtml;
		// DB切断
		$objDB->close();
		// 処理終了
		return true;
	}

	//-------------------------------------------------------------------------
	// 【ajax】納品日変更時の消費税率選択項目再設定
	//-------------------------------------------------------------------------
	if($strMode == "change-deliverydate"){
		// 変更後の納品日に対応する消費税率の選択項目を取得
		$optTaxRate = fncGetTaxRatePullDown($_POST["dtmDeliveryDate"], $objDB);
		// データ返却
		echo $optTaxRate;
		// DB切断
		$objDB->close();
		// 処理終了
		return true;
	}

	//-------------------------------------------------------------------------
	// フォーム初期値設定
	//-------------------------------------------------------------------------
	// ヘッダ・フッダ部

	// 納品日
	$nowDate = new DateTime();
	$aryData["dtmDeliveryDate"] = $nowDate->format('Y/m/d');

	// 支払期限
	$oneMonthLater = $nowDate->modify('+1 month');
	$aryData["dtmPaymentDueDate"] = $oneMonthLater->format('Y/m/d');

	// 支払方法プルダウン
	$optPaymentMethod .= fncGetPulldown("m_paymentmethod","lngpaymentmethodcode","strpaymentmethodname", "", "", $objDB);
	$aryData["optPaymentMethod"] = $optPaymentMethod;

	// 消費税区分プルダウン
	$optTaxClass .= fncGetPulldown("m_taxclass","lngtaxclasscode","strtaxclassname", "", "", $objDB);
	$aryData["optTaxClass"] = $optTaxClass;

	// 消費税率プルダウン
	$optTaxRate = fncGetTaxRatePullDown($aryData["dtmDeliveryDate"], $objDB);
	$aryData["optTaxRate"] = $optTaxRate;

	// 消費税額
	$aryData["strTaxAmount"] = "0";

	// 合計金額
	$aryData["strTotalAmount"] = "0";

	// 納品書修正画面表示（テンプレートは売上（納品書）登録画面と共通）
	echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);

	// DB切断
	$objDB->close();

	// 処理終了
	return true;

// エラー画面への遷移
function MoveToErrorPage($strMessage){
	
	// 言語コード：日本語
	$aryHtml["lngLanguageCode"] = 1;

	// エラーメッセージの設定
	$aryHtml["strErrorMessage"] = $strMessage;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );
	
	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	exit;
}

?>