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

	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");

	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	if( strcmp( $_GET["strSessionID"],"" ) != 0 )
	{
		$aryData["strSessionID"] = $_GET["strSessionID"];
		$aryData["lngOrderNo"]   = 1;
	}
	else
	{
		$aryData["strSessionID"] = $_POST["strSessionID"];
		$aryData["lngOrderNo"]   = $_POST["lngOrderNo"];
	}
	$aryData["lngLanguageCode"]	= $_COOKIE["lngLanguageCode"];

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
	$objDB->open("", "", "", "");

	// 文字列チェック
	$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	
	// 明細検索
	if($_POST["strMode"] == "search-detail"){

		$aryReceiveDetail = fncGetReceiveDetail($_POST["condition"], $objDB);
		$strHtml = fncGetReceiveDetailHtml($aryReceiveDetail);

		echo $strHtml;

		return true;
	}

	// 権限チェック
	$lngUserCode = $objAuth->UserCode;
	/*
	
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
		$aryUpdate["lngorderstatuscode"]   = 2;
		for($i = 0; $i < count($_POST["aryDetail"]); $i++){
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
		
		
		$objDB->transactionBegin();
		// 発注マスタ更新
		if(!fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
		// 発注明細更新
		if(!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
		// 発注書マスタ更新
		//if(!fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB)){ return false; }
		$aryResult = fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB);
		//echo implode(",", $aryResult);
		// TODO:あとでコミットに変更する
		// $objDB->transactionRollback();
		$objDB->transactionCommit();

		// 更新後発注書データ取得
		$aryPurcharseOrder = fncGetPurchaseOrder($aryResult, $objDB);
		if(!$aryPurcharseOrder){
			fncOutputError ( 9051, DEF_ERROR, "発注書の取得に失敗しました。", TRUE, "", $objDB );
			return FALSE;
		}
		
		$strHtml = fncCreatePurchaseOrderHtml($aryPurcharseOrder);
		$aryData["aryPurchaseOrder"] = $strHtml;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		
		header("Content-type: text/plain; charset=EUC-JP");
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryData );
		// $objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		return true;
	}

	// ヘッダ・フッダ部
	$aryOrderHeader = fncGetOrder($aryData["lngOrderNo"], $objDB);

	// $aryData["strOrderCode"]          = $aryOrderHeader[0]["strordercode"];
	// $aryData["strReviseCode"]         = str_pad($aryOrderHeader[0]["lngrevisionno"],2,"0",STR_PAD_LEFT);
	$aryData["dtmExpirationDate"]     = str_replace("-", "/", $aryOrderHeader[0]["dtmexpirationdate"]);
	$aryData["strProductCode"]        = $aryOrderHeader[0]["strproductcode"];
	$aryData["strNote"]               = $aryOrderHeader[0]["strnote"];
	// $aryData["lngCustomerCode"]       = $aryOrderHeader[0]["strcompanydisplaycode"];
	// $aryData["strCustomerName"]       = $aryOrderHeader[0]["strcompanydisplayname"];
	$aryData["strGroupDisplayCode"]   = $aryOrderHeader[0]["strgroupdisplaycode"];
	$aryData["strGroupDisplayName"]   = $aryOrderHeader[0]["strgroupdisplayname"];
	$aryData["strProductName"]        = $aryOrderHeader[0]["strproductname"];
	$aryData["strProductEnglishName"] = $aryOrderHeader[0]["strproductenglishname"];
	$aryData["lngCountryCode"]        = $aryOrderHeader[0]["lngcountrycode"];
	$aryData["lngLocationCode"]       = $aryOrderHeader[0]["strcompanydisplaycode2"];
	$aryData["strLocationName"]       = $aryOrderHeader[0]["strcompanydisplayname2"];
	$aryData["lngRevisionNo"]         = $aryOrderHeader[0]["lngrevisionno"];
	
	// 明細
	// $aryDetail = [];
	// for($i = 0; $i < count($aryOrderHeader); $i++){
	// 	$aryDetail[] = fncGetOrderDetail($aryOrderHeader[$i], $objDB);
	// }
	$aryDetail = fncGetOrderDetail($aryData["lngOrderNo"], $objDB);

	// 支払条件プルダウン
	$strPulldownPaycondition = fncPulldownMenu(0, 0, "", $objDB);
	$aryData["optPayCondition"] = $strPulldownPaycondition;
	// 通貨プルダウン
	$strPulldownMonetaryUnit = fncPulldownMenu(1, $aryOrderHeader["lngmonetaryunitcode"], "", $objDB);
	$aryData["optMonetaryUnit"] = $strPulldownMonetaryUnit;
	// 運搬方法プルダウン
	$strPulldownDeliveryMethod = fncPulldownMenu(2, null, "", $objDB);

	$aryData["strOrderDetail"] = fncGetOrderDetailHtml($aryDetail, $strPulldownDeliveryMethod);




	if(false){
	if($_POST["strMode"] == "check")
	{

		// 明細行を除く
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each ( $_POST );
			if($strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}
		
		
		// headerの項目チェック
		list ( $aryData, $bytErrorFlag )  = fncCheckData_po( $aryData,"header", $objDB );
		$errorCount = ( $bytErrorFlag != "") ? 1 : 0;

		// 2004/03/15 watanabe update start
		// 商品コードに付随する商品が存在するか
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// 製品コード００００の製品対応
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );
			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 303, "", "", FALSE, "", $objDB );
			}
		}
		// watanabe end
		
		
		// 明細行のチェック
		if(  count( $_POST["aryPoDitail"] ) > 0 )
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_po( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}
			
			// 明細行のエラー関数
			$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );
			
			if( $strDetailErrorMessage != "" )
			{
				$aryDetailErrorMessage[] = $strDetailErrorMessage;
			}
			
		}
		else
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}

		// 2004.03.30 suzukaze update start
		// 明細行のデータに対して、製品コードが違うデータが存在しないかどうかのチェック
		$bytCheck = fncCheckOrderDetailProductCode ( $_POST["aryPoDitail"], $objDB );
		if ( $bytCheck == 99 )
		{
			$aryDetailErrorMessage[] = fncOutputError( 506, "", "", FALSE, "", $objDB );
		}
		// 2004.03.30 suzukaze update end

		// エラーがあった場合 ==============================================================================
		if( $errorCount != 0 || is_array( $aryDetailErrorMessage ))
		{
			
			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}
			
			// 明細行に値が入っている場合は通貨をdisabledにする
			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
			}

			//特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
			
			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 支払条件
			$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// 仕入科目
			$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, $aryData["strStockSubjectCode"], '', $objDB );
			// 運搬方法
			$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, $aryData["lngCarrierCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, $aryData["lngProductUnitCode"], '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, $aryData["lngPackingUnitCode"], '', $objDB );
			
			$aryData["strMode"]			= "check";			// モード（次の動作）check→insert
			$aryData["strActionUrl"]	= "index2.php";		// formのaction



			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 承認ルートの生成
			// 「マネージャー」以上の場合
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"]);
			}




			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" , $objDB );
			}
			
			// submit関数
			$aryData["lngRegistConfirm"] = 0;
			$aryData["strMode"] = "check";
			
			// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
			// 2004.04.08 suzukaze update end

			// 2004.04.19 suzukaze update start
			$aryData["strPageCondition"] = "regist";
			// 2004.04.19 suzukaze update end


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


			$objDB->close();
			$objDB->freeResult( $lngResultID );

			echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);

			return true;
			
		}
		else
		{
			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 承認ルートの生成
			// 「マネージャー」以上の場合
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );
			}


			// 上とまったく同じ(許してください・・・）
			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 支払条件
			$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// 仕入科目
			$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, $aryData["strStockSubjectCode"], '', $objDB );
			// 運搬方法
			$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, $aryData["lngCarrierCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, $aryData["lngProductUnitCode"], '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, $aryData["lngPackingUnitCode"], '', $objDB );


			// 確認画面表示 =======================================
			$aryData["strBodyOnload"] = "";
			$aryData["strMode"] = "check";
			$aryData["strProcMode"] = "regist";
			
			// submit関数
			$aryData["lngRegistConfirm"] = 1;
			
			// 明細行をhidden値に変換する
			//$aryData["strHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" ,$objDB );

			$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" , $objDB );

			
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

			// テンプレート読み込み
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData, $objAuth );
			return true;
			
		}
	
	}

	// 最初の画面
	// プルダウンメニューの生成
	// 通貨
	$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, "\\", '', $objDB );
	// レートタイプ
	$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, 0, '', $objDB );
	// 支払条件
	$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, 0, '', $objDB );
	// 仕入科目
	$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, 0, '', $objDB );
	// 運搬方法
	$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, 0, '', $objDB );
	// 製品単位
	$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
	// 荷姿単位
	$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 承認ルートの生成
	// 「マネージャー」以上の場合
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
	}
	else
	{
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB ,"" );
	}
	}



	$aryData["strMode"] = "update";				// モード（次の動作）check→renew
	$aryData["strActionUrl"] = "index2.php";		// formのaction
	
	//echo "value :".$aryData["lngWorkflowOrderCode"]."<br>";
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
*/
	echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);
	
	
	return true;

?>