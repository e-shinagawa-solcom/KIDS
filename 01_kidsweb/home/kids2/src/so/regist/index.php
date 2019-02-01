<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  登録画面
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



	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	include( 'conf.inc' );                     // 設定ファイル
	require( LIB_FILE );                       // クラスライブラリファイル
	require( SRC_ROOT . "po/cmn/lib_po.php" ); // 発注管理関数ファイル
	require( SRC_ROOT . "so/cmn/lib_so.php" ); // 受注管理関数ファイル
	require( LIB_DEBUGFILE );
	//fncDebug( 'lib_so.txt', "OK", __FILE__, __LINE__);

	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();   // DBオブジェクト
	$objAuth = new clsAuth(); // 認証処理オブジェクト


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // 言語コード

	$strProcMode = $_POST["strProcMode"];   // 処理モード
	$dtmNowDate  = date( 'Y/m/d', time() ); // 現在日付





	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// ■ 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 入力文字列
	$aryCheck["strSessionID"] = "null:numenglish( 32,32 )";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション
	$objAuth     = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;

	// 400 受注管理
	if( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
		fncOutputError( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 401 受注管理（受注登録）
	if( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
	{
		fncOutputError( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}



	// 408 受注管理（商品マスタダイレクト修正）
	if( !fncCheckAuthority( DEF_FUNCTION_SO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}





	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( $strProcMode == "check" )
	{
		// 明細行を除いた値を取得
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each ( $_POST );

			if( $strKeys != "aryPoDitail" ) $aryData[$strKeys] = $strValues;
		}


		//-----------------------------------------------------------
		// ヘッダー項目チェック
		//-----------------------------------------------------------
		list( $aryData, $bytErrorFlag ) = fncCheckData_so( $aryData, "header", $objDB );


		// 商品コードに付随する商品が存在するか
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// 製品コード
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );

			// 製品コードが索引できなかった場合
			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError( 303, "", "", FALSE, "", $objDB );
			}
			// 製品コードが索引できた場合
			else
			{
				// 顧客品番索引
				$goodscode = fncGetMasterValue( "m_product", "strproductcode", "strgoodscode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );

				// 顧客受注番号が設定されており尚且つ顧客品番が設定されていない場合
				// 0を設定するとfalseになるので注意
				if($_REQUEST["strCustomerReceiveCode"] && !$goodscode)
				{
					$aryDetailErrorMessage[] = fncOutputError( 303, "", ": 顧客品番未設定の商品。", FALSE, "", $objDB );
				}
			}
		}

		// エラーカウントの取得
		$errorCount = ( $bytErrorFlag != "" ) ? 1 : 0;


		//-----------------------------------------------------------
		// 明細行のチェック
		//-----------------------------------------------------------
		if( count( $_POST["aryPoDitail"] ) > 0 )
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list( $aryDetailCheck[], $bytErrorFlag2[] )  = fncCheckData_so( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}

			// 明細行のエラー関数
			$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );

			if( $strDetailErrorMessage != "" )
			{
				$aryDetailErrorMessage[] = $strDetailErrorMessage;
			}
		}
		if( !is_array( $_POST["aryPoDitail"] ) )
		{
			$aryDetailErrorMessage[] = fncOutputError( 9001, "", "", FALSE, "", $objDB );
		}



		//-----------------------------------------------------------
		// DB -> SELECT : m_Receive
		//-----------------------------------------------------------
		// 受注番号の重複チェック
		$aryCheckQuery   = array();
		$aryCheckQuery[] = "SELECT r.strReceiveCode ";
		$aryCheckQuery[] = "FROM m_Receive r ";
		$aryCheckQuery[] = "WHERE r.strReceiveCode = '" . $aryData["strReceiveCode"] . "' ";
		$aryCheckQuery[] = "AND r.bytInvalidFlag = FALSE ";
		$aryCheckQuery[] = "AND 0 <= ( SELECT MIN( r2.lngRevisionNo ) ";
		$aryCheckQuery[] = "FROM m_Receive r2 ";
		$aryCheckQuery[] = "WHERE r2.bytInvalidFlag = false ";
		$aryCheckQuery[] = "AND r2.strReceiveCode = r.strReceiveCode ) ";

		$strCheckQuery = implode( "\n", $aryCheckQuery );


		// クエリ実行
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		// クエリ実行失敗の場合
		if( $lngCheckResultNum )
		{
			$aryDetailErrorMessage[] = fncOutputError( 405, DEF_WARNING, "", FALSE, "", $objDB );
		}

		// 結果IDを解放
		$objDB->freeResult( $lngCheckResultID );


		//-----------------------------------------------------------
		// 入力エラー
		//-----------------------------------------------------------
		if( $errorCount != 0 || is_array( $aryDetailErrorMessage ) )
		{
			// 明細行に値が入っている場合は通貨をdisabledにする
			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = "disabled";
			}

			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}


			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, $aryData["lngProductUnitCode"], '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, $aryData["lngPackingUnitCode"], '', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );

			// ヘッダ備考の特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			// 明細行 HIDDEN の生成
			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert" , $objDB );
			}
			if( is_array( $_POST["aryPoDitail"] ) )
			{
				 $aryData["MonetaryUnitCode"] = " disabled";
			}


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


			$aryData["strProcMode"]      = "check";     // モード（次の動作）check→insert
			$aryData["strActionUrl"]     = "index.php"; // formのaction
			$aryData["lngRegistConfirm"] = 0;
			$aryData["lngCalcCode"]      = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"] = "regist";

			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

			//---------------------------------------------
			// DBクローズ
			//---------------------------------------------
			$objDB->close();
			$objDB->freeResult( $lngResultID );

			//---------------------------------------------
			// 出力
			//---------------------------------------------
			echo fncGetReplacedHtml( "so/regist/parts.tmpl", $aryData ,$objAuth );

			return true;
		}
		//-----------------------------------------------------------
		// 確認画面表示
		//-----------------------------------------------------------
		else
		{
			// プルダウンメニューの生成
			// 通貨
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// レートタイプ
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// 製品単位
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, $aryData["lngProductUnitCode"], '', $objDB );
			// 荷姿単位
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, $aryData["lngPackingUnitCode"], '', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );

			// ヘッダ備考の特殊文字変換
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			// 明細行をhidden値に変換する
			$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert" , $objDB );

			if( is_array( $_POST["aryPoDitail"] ) )
			{
				 $aryData["MonetaryUnitCode"] = " disabled";
			}



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





			$aryData["strBodyOnload"]    = "";
			$aryData["strProcMode"]      = "check"; // モード（次の動作）check→insert
			$aryData["strActionURL"]     = "index.php";
			$aryData["lngRegistConfirm"] = 1;
			$aryData["lngCalcCode"]      = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"] = "regist";

			$aryData["strurl"] = "/so/confirm/index.php?strSessionID=".$aryData["strSessionID"];


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

			//---------------------------------------------
			// DBクローズ
			//---------------------------------------------
			$objDB->close();
			$objDB->freeResult( $lngResultID );

			//---------------------------------------------
			// 出力
			//---------------------------------------------
			echo fncGetReplacedHtml( "/so/regist/parts.tmpl", $aryData, $objAuth );

			return true;
		}
	}





	//-------------------------------------------------------------------------
	// ■ 初期表示 -> 初期値設定
	//-------------------------------------------------------------------------
	$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, "\\", '', $objDB );                            // 通貨
	$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, 0, '', $objDB );                               // レートタイプ
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );                               // 製品単位
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );                               // 荷姿単位
	$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );  // 売上区分



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



	$aryData["strProcMode"]           = "check";            // モード（次の動作）check→renew
	$aryData["strActionUrl"]          = "index.php";        // サブミットパス
	//$aryData["strReviseCode"]         = "00";               // リバイズ番号
	$aryData["dtmOrderAppDate"]       = $dtmNowDate;        // 現在日付
	$aryData["curConversionRate"]     = "1.000000";         // 換算レート

	$aryData["lngRegistConfirm"]      = 0;                  // 登録確認コード
	$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;  //
	$aryData["strPageCondition"]      = "regist";           // 処理状態
	$aryData["lngFunctionCode"]       = DEF_FUNCTION_SO1;   // 機能コード

	// Debug
	//$aryData["strReceiveCode"]       = '6234567890';
	//$aryData["strReviseCode"]        = '00';
	//$aryData["lngInChargeGroupCode"] = '20';
	//$aryData["lngInChargeUserCode"]  = '901';



	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

	//-------------------------------------------------------------------------
	// ■ DBクローズ
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );


	//-------------------------------------------------------------------------
	// ■ 出力
	//-------------------------------------------------------------------------
	echo fncGetReplacedHtml( "/so/regist/parts.tmpl", $aryData ,$objAuth );



	return true;

?>
