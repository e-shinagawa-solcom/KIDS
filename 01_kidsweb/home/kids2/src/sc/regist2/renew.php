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

	// --------------------
	// 修正対象に紐づく情報
	// --------------------
	// 納品伝票番号
	$lngSlipNo = $_GET["lngSlipNo"];
	// 納品伝票コード
	$strSlipCode = $_GET["strSlipCode"];
	// 納品書のリビジョン番号
	$lngRevisionNo = $_GET["lngRevisionNo"];
	// 売上番号
	$lngSalesNo = $_GET["lngSalesNo"];
	// 売上コード
	$strSalesCode = $_GET["strSalesCode"];
	// 顧客コード
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
	if($strMode == "search-detail"){
		// DBから明細を検索
		$aryReceiveDetail = fncGetReceiveDetail($_POST["condition"], $objDB);
		// 明細選択エリアに出力するHTMLの作成
		$withCheckBox = true;
		$strHtml = fncGetReceiveDetailHtml($aryReceiveDetail, $withCheckBox);
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
		$optTaxRate = fncGetTaxRatePullDown($_POST["dtmDeliveryDate"], "", $objDB);
		// データ返却
		echo $optTaxRate;
		// DB切断
		$objDB->close();
		// 処理終了
		return true;
	}

	//-------------------------------------------------------------------------
	// 修正対象データのロック取得
	//-------------------------------------------------------------------------
	// 他にロックしている人がいないか確認
	$lockUserName = fncGetExclusiveLockUser(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
	if (strlen($lockUserName) > 0)
	{
		MoveToErrorPage("ユーザー".$lockUserName."が修正中です。");
	}

	// 修正対象データのロックを取る
	$locked = fncTakeExclusiveLock(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
	if (!$locked)
	{
		MoveToErrorPage("納品書データのロックに失敗しました。");
	}

	//-------------------------------------------------------------------------
	// 修正対象データ取得
	//-------------------------------------------------------------------------
	// 納品伝票番号に紐づくヘッダ・フッタ部のデータ読み込み
	$aryHeader = fncGetHeaderBySlipNo($lngSlipNo, $lngRevisionNo, $objDB);

	// 納品伝票番号に紐づく明細部のキーを取得する
	$aryDetailKey = fncGetDetailKeyBySlipNo($lngSlipNo, $objDB);
	
	// 明細部のキーに紐づく受注明細情報を取得する
	$aryDetail = array();
	for ( $i = 0; $i < count($aryDetailKey); $i++ ){

		$aryCondition = array();
		$aryCondition["lngReceiveNo"] = $aryDetailKey[$i]["lngreceiveno"];
		$aryCondition["lngReceiveDetailNo"] = $aryDetailKey[$i]["lngreceivedetailno"];
		$aryCondition["lngReceiveRevisionNo"] = $aryDetailKey[$i]["lngreceiverevisionno"];
		
		// キーに紐づく明細を1件ずつ取得して全体の配列にマージ
		$arySubDetail = fncGetReceiveDetail($aryCondition, $objDB);
		$aryDetail = array_merge($aryDetail, $arySubDetail);
	}

	// 明細部のHTMLを生成
	$withCheckBox = false;
	$strDetailHtml = fncGetReceiveDetailHtml($aryDetail, $withCheckBox);
	
	//-------------------------------------------------------------------------
	// フォーム初期値設定
	//-------------------------------------------------------------------------
	// -------------------------
	//  修正対象データに紐づく値
	// -------------------------
	// 納品伝票番号（この値がセットされていたら修正とみなす）
	$aryData["lngSlipNo"] = $lngSlipNo;
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
	$aryData["strEditTableBody"] = $strDetailHtml;

	// -------------------------
	//  ヘッダ・フッダ部
	// -------------------------
	// 起票者
	$aryData["lngInsertUserCode"] = $aryHeader["struserdisplaycode"];
	$aryData["strInsertUserName"] = $aryHeader["struserdisplayname"];

	// 顧客
	$aryData["lngCustomerCode"] = $aryHeader["strcustomercode"];
	$aryData["strCustomerName"] = $aryHeader["strcustomerdisplayname"];

	// 顧客側担当者
	$aryData["strCustomerUserName"] = $aryHeader["strcustomerusername"];

	// 納品日
	$aryData["dtmDeliveryDate"] = $aryHeader["dtmdeliverydate"];

	// 支払方法プルダウン
	$lngDefaultPaymentMethodCode = $aryHeader["lngpaymentmethodcode"];
	$optPaymentMethod .= fncGetPulldown("m_paymentmethod","lngpaymentmethodcode","strpaymentmethodname", $lngDefaultPaymentMethodCode, "", $objDB);
	$aryData["optPaymentMethod"] = $optPaymentMethod;

	// 支払期限
	$aryData["dtmPaymentDueDate"] = $aryHeader["dtmpaymentlimit"];

	// 納品先
	$aryData["lngDeliveryPlaceCode"] = $aryHeader["strcompanydisplaycode"];
	$aryData["strDeliveryPlaceName"] = $aryHeader["strdeliveryplacename"];

	// 納品先担当者
	$aryData["strDeliveryPlaceUserName"] = $aryHeader["strdeliveryplaceusername"];

	// 備考
	$aryData["strNote"] = $aryHeader["strnote"];

	// 消費税区分プルダウン
	$lngDefaultTaxClassCode = $aryHeader["lngtaxclasscode"];
	$optTaxClass .= fncGetPulldown("m_taxclass","lngtaxclasscode","strtaxclassname", $lngDefaultTaxClassCode , "", $objDB);
	$aryData["optTaxClass"] = $optTaxClass;

	// 消費税率プルダウン
	if($aryData["dtmDeliveryDate"]){
		$curDefaultTax = $aryHeader["curtax"];
		$optTaxRate = fncGetTaxRatePullDown($aryData["dtmDeliveryDate"], $curDefaultTax, $objDB);
		$aryData["optTaxRate"] = $optTaxRate;
	}

	// 消費税額（※ここでは0をセットしておき、画面表示時にjavascriptで関数を呼び出して計算する）
	$aryData["strTaxAmount"] = "0";

	// 合計金額
	$aryData["strTotalAmount"] = $aryHeader["curtotalprice"];

	//-------------------------------------------------------------------------
	// 画面表示
	//-------------------------------------------------------------------------
	// ajax POST先をこのファイルにする
	$aryData["ajaxPostTarget"] = "renew.php";

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