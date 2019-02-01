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

	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	if( strcmp( $_GET["strSessionID"],"" ) != 0 )
	{
		$aryData["strSessionID"]	= $_GET["strSessionID"];
	}
	else
	{
		$aryData["strSessionID"]	= $_POST["strSessionID"];
	}
	$aryData["lngLanguageCode"]	= $_COOKIE["lngLanguageCode"];
	

	
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
			$aryData["strActionUrl"]	= "index.php";		// formのaction



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

			echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryData ,$objAuth);

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
			




//			$aryData["strButton"] = "<input type=\"button\" value=\"やり直し\" onClick=\"fncPageback( 'index.php' )\">&nbsp;&nbsp;<input type=\"button\" value=\"登録\" onClick=\"fncPagenext( 'index2.php' )\">";

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
			echo fncGetReplacedHtml( "/po/regist/parts.tmpl", $aryData, $objAuth );
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



	$aryData["strMode"] = "check";				// モード（次の動作）check→renew
	$aryData["strActionUrl"] = "index.php";		// formのaction
	
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

	echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryData ,$objAuth);
	
	return true;

?>