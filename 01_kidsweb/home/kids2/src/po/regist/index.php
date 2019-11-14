<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  登録画面
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
*         ・初期登録画面を表示
*         ・入力エラーチェック
*         ・登録ボタン押下後、登録確認画面へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."po/cmn/lib_po.php");
	require (SRC_ROOT."po/cmn/lib_pop.php");
	require (SRC_ROOT."po/cmn/lib_por.php");
	require_once (LIB_DEBUGFILE);
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	if( strcmp( $_GET["strSessionID"],"" ) != 0 )
	{
		$aryData["strSessionID"] = $_GET["strSessionID"];
		$aryData["lngOrderNo"]   = $_GET["lngOrderNo"];
	}
	else
	{
		$aryData["strSessionID"] = $_POST["strSessionID"];
		$aryData["lngOrderNo"]   = $_POST["lngOrderNo"];
	}
	$aryData["lngLanguageCode"]	= $_COOKIE["lngLanguageCode"];
//fncDebug("kids2.log", $aryData["lngOrderNo"], __FILE__, __LINE__, "a" );
	
	$objDB->open("", "", "", "");
	
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngUserCode = $objAuth->UserCode;
	
	// 500	発注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
	        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	// 501 発注管理（発注登録）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	// 508 発注管理（商品マスタダイレクト修正）
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}
	// 更新モード
	if($_POST["strMode"] == "update"){
		// 更新データ取得
		$aryUpdate["lngorderno"]           = $_POST["lngOrderNo"];
		$aryUpdate["lngrevisionno"]        = $_POST["lngRevisionNo"];
		$aryUpdate["dtmexpirationdate"]    = $_POST["dtmExpirationDate"];
		$aryUpdate["lngpayconditioncode"]  = $_POST["lngPayConditionCode"];
		$aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
		$aryUpdate["strnote"] = mb_convert_encoding($_POST["strNote"], "EUC-JP", "auto");
		$aryUpdate["lngorderstatuscode"]   = 2;
		for($i = 0; $i < count($_POST["aryDetail"]); $i++){
			$aryUpdateDetail[$i]["lngpurchaseorderdetailno"] = $i + 1;
			$aryUpdateDetail[$i]["lngorderdetailno"]       = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
			$aryUpdateDetail[$i]["lngsortkey"]             = $_POST["aryDetail"][$i]["lngSortKey"];
			$aryUpdateDetail[$i]["lngdeliverymethodcode"]  = $_POST["aryDetail"][$i]["lngDeliveryMethodCode"];
			$aryUpdateDetail[$i]["strdeliverymethodname"]  = $_POST["aryDetail"][$i]["strDeliveryMethodName"];
			$aryUpdateDetail[$i]["lngproductunitcode"]     = $_POST["aryDetail"][$i]["lngProductUnitCode"];
			$aryUpdateDetail[$i]["lngorderno"]             = $_POST["aryDetail"][$i]["lngOrderNo"];
			$aryUpdateDetail[$i]["lngrevisionno"]          = $_POST["aryDetail"][$i]["lngRevisionNo"];
			$aryUpdateDetail[$i]["lngstocksubjectcode"]    = $_POST["aryDetail"][$i]["lngStockSubjectCode"];
			$aryUpdateDetail[$i]["lngstockitemcode"]       = $_POST["aryDetail"][$i]["lngStockItemCode"];
			$aryUpdateDetail[$i]["lngmonetaryunitcode"]    = $_POST["aryDetail"][$i]["lngMonetaryUnitCode"];
			$aryUpdateDetail[$i]["lngcustomercompanycode"] = $_POST["aryDetail"][$i]["lngCustomerCompanyCode"];
			$aryUpdateDetail[$i]["curproductprice"]        = $_POST["aryDetail"][$i]["curProductPrice"];
			$aryUpdateDetail[$i]["lngproductquantity"]     = $_POST["aryDetail"][$i]["lngProductQuantity"];
			$aryUpdateDetail[$i]["cursubtotalprice"]       = $_POST["aryDetail"][$i]["curSubtotalPrice"];
			$aryUpdateDetail[$i]["dtmseliverydate"]        = $_POST["aryDetail"][$i]["dtmDeliveryDate"];
		}


		// 発注書登録確認画面へ遷移

		// 確認画面表示 =======================================
		$aryData["strBodyOnload"] = "";
		$aryData["strMode"] = "check";
		$aryData["strProcMode"] = "regist";
		
		// submit関数
		$aryData["lngRegistConfirm"] = 1;
		
		// 明細行をhidden値に変換する
		//$aryData["strHidden"] = fncDetailHidden( $_POST["aryDetail"] ,"insert" ,$objDB );
	
		//特殊文字変換
		$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
		
		//	$aryData["strButton"] = "<input type=\"button\" value=\"やり直し\" onClick=\"fncPageback( 'index.php' )\">&nbsp;&nbsp;<input type=\"button\" value=\"登録\" onClick=\"fncPagenext( 'index2.php' )\">";

		$objDB->close();
		
		$aryData["strurl"] = "/po/confirm/index.php?strSessionID=".$aryData["strSessionID"];
		$aryData["strActionURL"] = "index.php";
		
		// 2004.04.08 suzukaze update start
		$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
		// 2004.04.08 suzukaze update end

		// 2004.04.19 suzukaze update start
		$aryData["strPageCondition"] = "regist";
		// 2004.04.19 suzukaze update end


		$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード
		$aryData["aryPoDitail"] = $_POST["aryDetail"];
		// テンプレート読み込み
		$objDB->freeResult( $lngResultID );

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		header("Content-type: text/plain; charset=EUC-JP");
		//$objTemplate->getTemplate( "po/confirm/parts.tmpl" );
		// テンプレート生成
		$objTemplate->replace($aryData);
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;
		// echo fncGetReplacedHtml( "/po/regist/parts.tmpl", $aryData, $objAuth );
			
		return true;
	}
	// ヘッダ・フッダ部
	$aryOrderHeader = fncGetOrder_r($aryData["lngOrderNo"], $objDB);

	// $aryData["strOrderCode"]          = $aryOrderHeader[0]["strordercode"];
	// $aryData["strReviseCode"]         = str_pad($aryOrderHeader[0]["lngrevisionno"],2,"0",STR_PAD_LEFT);
	$aryData["dtmExpirationDate"]     = str_replace("-", "/", $aryOrderHeader[0]["dtmexpirationdate"]);
	$aryData["strProductCode"]        = $aryOrderHeader[0]["strproductcode"];
	$aryData["strNote"]               = $aryOrderHeader[0]["strnote"];
	// $aryData["lngCustomerCode"]       = $aryOrderHeader[0]["strcompanydisplaycode"];
	// $aryData["strCustomerName"]       = $aryOrderHeader[0]["strcompanydisplayname"];
	$aryData["lngInChargeGroupCode"]   = $aryOrderHeader[0]["strgroupdisplaycode"];
	$aryData["strInChargeGroupName"]   = $aryOrderHeader[0]["strgroupdisplayname"];
	$aryData["strProductName"]        = $aryOrderHeader[0]["strproductname"];
	$aryData["strProductEnglishName"] = $aryOrderHeader[0]["strproductenglishname"];
	$aryData["lngCountryCode"]        = $aryOrderHeader[0]["lngcountrycode"];
	$aryData["lngLocationCode"]       = $aryOrderHeader[0]["strcompanydisplaycode2"];
	$aryData["strLocationName"]       = $aryOrderHeader[0]["strcompanydisplayname2"];
	$aryData["lngRevisionNo"]         = $aryOrderHeader[0]["lngrevisionno"];
	
	$aryData["lngPayConditionCode"]      = fncPulldownMenu(2, 0, "", $objDB);
	// 明細
	// $aryDetail = [];
	// for($i = 0; $i < count($aryOrderHeader); $i++){
	// 	$aryDetail[] = fncGetOrderDetail($aryOrderHeader[$i], $objDB);
	// }
	$lngOrderNo = explode(",", $aryData["lngOrderNo"]);
//fncDebug("kids2.log", $lngOrderNo[0], __FILE__, __LINE__, "a" );
	//$aryDetail = fncGetOrderDetail($aryData["lngOrderNo"], $objDB);
	$aryDetail = fncGetOrderDetail($aryData["lngOrderNo"], $aryData["lngRevisionNo"], $objDB);

	// 通貨プルダウン
	$strPulldownMonetaryUnit = fncPulldownMenu(0, $aryOrderHeader["lngmonetaryunitcode"], "", $objDB);
	$aryData["optMonetaryUnit"] = $strPulldownMonetaryUnit;
	// 運搬方法プルダウン
	$strPulldownDeliveryMethod = fncPulldownMenu(6, null, "", $objDB);

	$aryData["strOrderDetail"] = fncGetOrderDetailHtml($aryDetail, $strPulldownDeliveryMethod);

	$aryData["strMode"] = "update";				// モード（次の動作）check→renew
	$aryData["strActionUrl"] = "index2.php";		// formのaction
	
	$dtmNowDate = date( 'Y/m/d', time());
	$aryData["dtmOrderAppDate"] = $dtmNowDate;
	
	// submit関数
	$aryData["lngRegistConfirm"] = 0;
	
	$aryData["curConversionRate"] = "1.000000";
	
	// 2004.04.08 suzukaze update start
	$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
	// 2004.04.08 suzukaze update end

	// 2004.04.19 suzukaze update start
	$aryData["strPageCondition"] = "regist";
	// 2004.04.19 suzukaze update end


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


	$objDB->close();
	$objDB->freeResult( $lngResultID );

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_PO1;

	$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/po/regist/parts.html");

// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
	// echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryData ,$objAuth);
	
	return true;

?>