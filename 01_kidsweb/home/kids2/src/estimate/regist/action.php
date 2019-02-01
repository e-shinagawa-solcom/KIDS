<?
/** 
*	見積原価管理 実行画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// confirm.php -> strSessionID			-> action.php
// confirm.php -> lngFunctionCode		-> action.php
// confirm.php -> lngEstimateNo			-> action.php見積原価番号
// confirm.php -> strProductCode		-> action.php製品コード
// confirm.php -> strActionName			-> action.php実行処理名(temporary)
// confirm.php -> lngWorkflowOrderCode	-> action.php承認ルート
// confirm.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngStockItemCode]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductRate]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][strNote]				-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> action.php


	mb_http_output ( 'EUC-JP' );



	require('conf.inc');
	require( LIB_DEBUGFILE );

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	include ( LIB_ROOT . "diff/conf_diff_product.inc" );		// 製品マスタ・差分管理設定
	require ( CLS_TABLETEMP_FILE );								// Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );



	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// POSTデータ取得
	$aryData = $_POST;



	// Temp配列
	$g_aryTemp	= $aryData;


fncDebug( 'estimate_regist_action_data.txt', $aryData["aryDetail"], __FILE__, __LINE__);

	$aryDetail = $aryData["aryDetail"];
	unset ( $aryData["aryDetail"] );
	//echo getArrayTable( $aryData, "TABLE" );exit;





	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]		= "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E5 . ")";

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );
	unset ( $aryCheck );

	$aryCheck["lngWorkflowOrderCode"]	= "null:number(0,32767)";


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;


	// 権限確認
	//////////////////////////////////////////////////////////////////////////
	// 見積原価登録の場合
	//////////////////////////////////////////////////////////////////////////
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )
	{
		$aryCheck["strProductCode"] = "null:numenglish(1,6)";
	}
	//////////////////////////////////////////////////////////////////////////
	// 見積修正の場合
	//////////////////////////////////////////////////////////////////////////
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 && fncCheckAuthority( DEF_FUNCTION_E3, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(1,2147483647)";

	}
	//////////////////////////////////////////////////////////////////////////
	// 見積削除の場合
	//////////////////////////////////////////////////////////////////////////
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && fncCheckAuthority( DEF_FUNCTION_E4, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(1,2147483647)";
		unset ( $aryCheck["lngWorkflowOrderCode"] );
	}
	//////////////////////////////////////////////////////////////////////////
	// それ以外(権限ERROR)
	//////////////////////////////////////////////////////////////////////////
	else
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );
	unset ( $aryCheck );
	unset ( $aryCheckResult );


	//////////////////////////////////////////////////////////////////////
	// DB処理開始
	//////////////////////////////////////////////////////////////////////
	$objDB->transactionBegin();


	// 見積原価登録の場合、INSERT
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
	{
		// 製品コード5桁化に伴って、4桁の見積ファイルがアップロードされた際に
		// 5桁に拡張してマスタ検索を実施する
		if (strlen($aryData["strProductCode"]) == 4)
		{
			$aryData["strProductCode"] = '0'.$aryData["strProductCode"];
		}

		// 指定製品が見積されていないかどうかチェック
		list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT * FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "'", $objDB );

		// 見積情報が存在する場合
		if ( $lngResultNum > 0 )
		{
			// ファイルテンポラリ処理以外の場合
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				$objDB->freeResult( $lngResultID );
				$objDB->execute( "ROLLBACK" );
				fncOutputError ( 1501, DEF_WARNING, "既に見積原価の登録のある製品です。", TRUE, "", $objDB );
			}
			// ファイルテンポラリ処理 -> リビジョン番号設定
			else
			{
				// 見積原価番号取得
				$aryData["lngEstimateNo"]	= fncGetMasterValue( "m_estimate", "strproductcode", "lngestimateno", $aryData["strProductCode"].":str", '', $objDB );

				$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

				// ((ログインユーザーが入力したものかつ仮保存状態のもの)、
				// または、申請中以外のもの)以外は、修正不可としてエラー出力
				if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) 
						|| ( $aryEstimateData["bytDecisionFlag"] == "t" && $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) ) )
				{
					unset ( $aryEstimateData );
					unset ( $aryData );
					fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
				}

				// 現在のリビジョンナンバーを保存
				$lngRevisionNo = $aryEstimateData["lngRevisionNo"];

				// 現在の製品コードを保存
				$aryData["strProductCode"] = $aryEstimateData["strProductCode"];


				// リビジョン番号
				// 修正の場合同じ製品コードの見積原価に対してリビジョン番号の最大値を取得する
				// リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ仕入に対してロック状態にする
				$strLockQuery = "SELECT lngRevisionNo FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "' FOR UPDATE";

				// ロッククエリーの実行
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

				$lngMaxRevision = 0;

				if ( $lngLockResultNum )
				{
					for ( $i = 0; $i < $lngLockResultNum; $i++ )
					{
						$objRevision = $objDB->fetchObject( $lngLockResultID, $i );

						if ( $lngMaxRevision < $objRevision->lngrevisionno )
						{
							$lngMaxRevision = $objRevision->lngrevisionno;
						}
					}

					$lngRevisionNo = $lngMaxRevision + 1;
				}
				else
				{
					$lngRevisionNo = $lngMaxRevision;
				}

				$objDB->freeResult( $lngLockResultID );

				unset ( $aryEstimateData );
			}
		}
		// 新規
		else
		{
			// 見積原価番号取得
			$aryData["lngEstimateNo"] = fncGetSequence( "m_Estimate.lngEstimateNo", $objDB );

			// リビジョン番号設定
			$lngRevisionNo = 0;
		}

	}
	// 見積原価修正・削除の場合、
	// 現状の見積原価データ取得と削除のためのDBチェック
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 || $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
	{
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

		// ((ログインユーザーが入力したものかつ仮保存状態のもの)、
		// または、申請中以外のもの)以外は、修正不可としてエラー出力
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) 
				|| ( $aryEstimateData["bytDecisionFlag"] == "t" && $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) ) )
		{
			unset ( $aryEstimateData );
			unset ( $aryData );
			fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
		}

		// 現在のリビジョンナンバーを保存
		$lngRevisionNo = $aryEstimateData["lngRevisionNo"];

		// 現在の製品コードを保存
		$aryData["strProductCode"] = $aryEstimateData["strProductCode"];

		// 削除の場合、DBチェック
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && $aryEstimateData["bytDecisionFlag"] == "t" )
		{
			$aryQuery = Array();

			// 発注詳細に対象製品コードがあるかどうかのチェッククエリ
			$aryQuery[] = "SELECT 1 FROM t_OrderDetail WHERE strProductCode = '" . $aryData["strProductCode"]. "'";

			// 仕入マスタに対象見積原価番号があるかどうかのチェッククエリ
			//$aryQuery[] = "SELECT 1 FROM m_Stock WHERE lngEstimateNo = " . $aryData["lngEstimateNo"];
			list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " UNION ", $aryQuery ), $objDB );

			if ( $lngResultNum > 0 )
			{
	//			unset ( $aryEstimateData );
	//			unset ( $aryData );
				$objDB->freeResult( $lngResultID );
	//			fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// リビジョン番号
		// 修正の場合同じ製品コードの見積原価に対してリビジョン番号の最大値を取得する
	/////   リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ仕入に対してロック状態にする
		$strLockQuery = "SELECT lngRevisionNo FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "' FOR UPDATE";

		// ロッククエリーの実行
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

		$lngMaxRevision = 0;
		if ( $lngLockResultNum )
		{
			for ( $i = 0; $i < $lngLockResultNum; $i++ )
			{
				$objRevision = $objDB->fetchObject( $lngLockResultID, $i );
				if ( $lngMaxRevision < $objRevision->lngrevisionno )
				{
					$lngMaxRevision = $objRevision->lngrevisionno;
				}
			}
			$lngRevisionNo = $lngMaxRevision + 1;
		}
		else
		{
			$lngRevisionNo = $lngMaxRevision;
		}
		$objDB->freeResult( $lngLockResultID );

		unset ( $aryEstimateData );
	}




	/////////////////////////////////////////////////////////////
	// 成形に必要なデータの取得
	/////////////////////////////////////////////////////////////
	// 会社表示コードをキーとする会社コード連想配列を取得
	$aryCompanyCode = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "lngCompanyCode", "Array", "", $objDB );

	$aryMonetaryUnitCode = Array ( "\\" => DEF_MONETARY_YEN, "$" => DEF_MONETARY_USD, "HKD" => DEF_MONETARY_HKD );

	// 通貨レート配列生成
	$aryRate = fncGetMonetaryRate( $objDB );
	$aryRate[DEF_MONETARY_YEN] = 1;

	// 削除の場合、リビジョンナンバーを-1にセット
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
	{
		$lngRevisionNo = -1;
	}


	/////////////////////////////////////////////////////////////
	// 登録対象データ成形と取得
	/////////////////////////////////////////////////////////////
	// 製品情報取得
	$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );

	// 標準割合取得
	$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

	// 見積原価計算明細HTML出力文字列取得
	list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

	// 計算結果を見積原価配列に組み込む
	$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

	unset ( $aryEstimateDetail );
	unset ( $aryCalculated );
	unset ( $aryHiddenString );

	// 計算結果を取得
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	// 仮保存チェック
	$bytDecisionFlag = "FALSE";

	$lngEstimateStatusCode = DEF_ESTIMATE_TEMPORARY;

	if ( $aryData["strActionName"] != "temporary" )
	{
		$bytDecisionFlag = "TRUE";

		if( $aryData["lngWorkflowOrderCode"] == 0 )
		{
			$lngEstimateStatusCode = DEF_ESTIMATE_APPROVE;
		}
		else
		{
			$lngEstimateStatusCode = DEF_ESTIMATE_APPLICATE;
		}
	}





//fncDebug( 'es_temp.txt', $aryData, __FILE__, __LINE__);exit();
	/*-------------------------------------------------------------------------
		ファイルテンポラリDB処理 <- 製品マスタ情報
	-------------------------------------------------------------------------*/
	// 「削除処理」ではない場合、テンポラリデータ処理
	if( $aryData["lngFunctionCode"] != DEF_FUNCTION_E4 )
	{
		// テンポラリ処理
		if( $aryData["blnTempFlag"] )
		{
			// テンポラリ番号取得
			$lngTempNo = fncGetMasterValue( "m_estimate", "lngestimateno", "lngtempno", $aryData["lngEstimateNo"], '', $objDB );

			// テンポラリ番号が存在する場合
			if( $lngTempNo )
			{
				// テンポラリデータ取得
				$aryTempData = fncGetTempData($objDB, $lngTempNo);

				// テンポラリデータ取得失敗の場合
				if( !$aryTempData ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

				// 成功
				else $blnTempFlag	= true;
			}

			// テンポラリテーブルへ登録、登録したlngTempNoを取得
			$lngTempNo	= fncArray2Temp( $objDB, $aryTempData );


			// 以下、マネージャー以上
			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 「マネージャー」以上の場合、製品マスタ更新
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				// 製品マスタ書き換え許可フラグが有効の場合、製品マスタ更新
				if( PRODUCT_MASTER_UPDATE_FLAG )
				{
					// 製品状態取得
					$lngBuffProductStatusCode	= fncGetMasterValue( "m_product", "strproductcode", "lngproductstatuscode", $aryData["strProductCode"].":str", '', $objDB );

					// 製品マスタ状態が「承認」以外の場合、処理終了
					if( $lngBuffProductStatusCode != DEF_PRODUCT_NORMAL )
					{
						fncOutputError( 308, DEF_WARNING, "（承認されていない製品情報です。）", TRUE, "", $objDB );
					}

					// 実行
					$blnCheck	= fncTemp2ProductUpdate($objDB, $lngTempNo);

					// 失敗の場合
					if( !$blnCheck ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

					// TempNo の初期化
					$lngTempNo	= null;
				}
			}


			// 登録処理後、入力画面に戻ったときに使用
			$aryData["RENEW"]	= "&RENEW=true";	// 画面表示モード：修正 ※修正処理ではない
		}
		// ファイルテンポラリ処理
		else if( $aryData["bytTemporaryFlg"] )
		{
			while( list($index, $value) = each($aryDiffProduct["tempdb"]) )
			{
				if( !$value )
				{
					continue;
				}

				$aryTempData[$index]	= $aryData[$index];
			}

			// テンポラリテーブルへ登録、登録したlngTempNoを取得
			$lngTempNo	= fncArray2Temp( $objDB, $aryTempData );


			// 以下、マネージャー以上
			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 「マネージャー」以上の場合、製品マスタ更新
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				// 製品マスタ書き換え許可フラグが有効の場合、製品マスタ更新
				if( PRODUCT_MASTER_UPDATE_FLAG )
				{
					// 製品状態取得
					$lngBuffProductStatusCode	= fncGetMasterValue( "m_product", "strproductcode", "lngproductstatuscode", $aryData["strProductCode"].":str", '', $objDB );

					// 製品マスタ状態が「承認」以外の場合、処理終了
					if( $lngBuffProductStatusCode != DEF_PRODUCT_NORMAL )
					{
						fncOutputError( 308, DEF_WARNING, "（承認されていない製品情報です。）", TRUE, "", $objDB );
					}

					// 実行
					$blnCheck	= fncTemp2ProductUpdate($objDB, $lngTempNo);

					// 失敗の場合
					if( !$blnCheck ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

					// TempNo の初期化
					$lngTempNo	= null;
				}
			}


			// 登録処理後、入力画面に戻ったときに使用
			$aryData["RENEW"]	= "&RENEW=true";	// 画面表示モード：修正 ※修正処理ではない
		}
	}
	/*-----------------------------------------------------------------------*/








/////////////////////////////////////////////////////////////
// 見積原価関連クエリ生成開始
/////////////////////////////////////////////////////////////
	// 見積原価マスタ登録クエリ生成
	$lngTempNo	= ( is_null($lngTempNo) || empty($lngTempNo) ) ? "null" : $lngTempNo;	// テンポラリ番号
	$strRemark	= ( is_null($aryData["strRemark"]) || empty($aryData["strRemark"]) ) ? "null" : "'".$aryData["strRemark"]."'";	// コメント

	$aryEstimateQuery[] = "INSERT INTO m_Estimate VALUES ( " . $aryData["lngEstimateNo"];
	$aryEstimateQuery[] = "," . $lngRevisionNo;
	$aryEstimateQuery[] = ",'" . $aryData["strProductCode"] . "'";
	$aryEstimateQuery[] = "," . $bytDecisionFlag;
	$aryEstimateQuery[] = "," . $lngEstimateStatusCode;
	$aryEstimateQuery[] = "," . $aryEstimateData["curFixedCost"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curMemberCost"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curTargetProfit"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curManufacturingCost"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curAmountOfSales"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curProfitOnSales"];
	$aryEstimateQuery[] = "," . $objAuth->UserCode;
	$aryEstimateQuery[] = "," . "FALSE";
	$aryEstimateQuery[] = "," . "NOW()";
	$aryEstimateQuery[] = "," . $aryEstimateData["lngProductionQuantity"];

	$aryEstimateQuery[] = "," . $lngTempNo;		// テンポラリ番号
	$aryEstimateQuery[] = "," . $strRemark;		// コメント
	$aryEstimateQuery[] = ")";

	$aryQuery[] = join ( "\n", $aryEstimateQuery );
	unset ( $aryEstimateQuery );




fncDebug( 'action_aryDetail.txt', $aryDetail, __FILE__, __LINE__);
//exit();



// 登録・修正の場合、見積原価詳細・ワークフローに関するクエリ生成
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
{
	// 見積原価明細番号インクリメント
	$lngEstimateDetailNo++;

	// 見積原価詳細登録クエリ生成
	for ( $i = 0; $i <= 11; $i++ )
	{
		if( !isset($aryDetail[$i]) ) continue;
		
		for ( $j = 0; $j < count ( $aryDetail[$i] ); $j++ )
		{
			// 通貨レートコード 2：社内固定
			$aryDetail[$i][$j]["lngMonetaryRateCode"] = 2;

			// 表示用ソートキーNULL固定
			$aryDetail[$i][$j]["lngSortKey"] = "NULL";

			// パーセント入力フラグがOffの場合、計画率をNULLに設定
			if ( $aryDetail[$i][$j]["bytPercentInputFlag"] != "true" )
			{
				$aryDetail[$i][$j]["curProductRate"] = "NULL";
			}

			$aryRowsValues = $aryDetail[$i][$j];

// 2004.10.05 suzukaze update start
			// 仕入先情報が指定されていなければNULLに設定
			if ( !is_numeric( $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]] ) )
			{
				$aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]] = "NULL";
			}
			// 計画率の場合単価項目はNULLに設定する
			if ( !is_numeric( $aryRowsValues["curProductPrice"] ) or ($aryRowsValues["curProductPrice"] == "" ) )
			{
				$aryRowsValues["curProductPrice"] = "NULL";
			}
// 2004.10.05 suzukaze update end

			// 通貨単位コード
			if ( !is_numeric( $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] ) or $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] == "" )
			{
				$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] = 1;
			}
			if ( !is_numeric( $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]] ) or $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]] == "" )
			{
				$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] = 1.000000;
			}

			$aryEstimateQuery[] = "INSERT INTO t_EstimateDetail VALUES ( " . $aryData["lngEstimateNo"];
			$aryEstimateQuery[] = $lngEstimateDetailNo;
			$aryEstimateQuery[] = $lngRevisionNo;
			$aryEstimateQuery[] = isset($aryRowsValues["lngStockSubjectCode"]) ? $aryRowsValues["lngStockSubjectCode"] : '0';
			$aryEstimateQuery[] = isset($aryRowsValues["lngStockItemCode"]) ? $aryRowsValues["lngStockItemCode"] : '0';
			$aryEstimateQuery[] = $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]];
			$aryEstimateQuery[] = isset($aryRowsValues["lngSalesDivisionCode"]) ? 'NULL' : $aryRowsValues["bytPayOffTargetFlag"];	// lngSalesDivisionCode の存在を元に、仕入（償却区分）を判定する
			$aryEstimateQuery[] = $aryRowsValues["bytPercentInputFlag"];
			$aryEstimateQuery[] = $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]];
			$aryEstimateQuery[] = $aryRowsValues["lngMonetaryRateCode"];
			$aryEstimateQuery[] = $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]];
			$aryEstimateQuery[] = $aryRowsValues["lngProductQuantity"];
			$aryEstimateQuery[] = $aryRowsValues["curProductPrice"];
			$aryEstimateQuery[] = $aryRowsValues["curProductRate"];
			$aryEstimateQuery[] = $aryRowsValues["curSubTotalPrice"];
			$aryEstimateQuery[] = "'" . $aryRowsValues["strNote"] . "'";
			$aryEstimateQuery[] = $aryRowsValues["lngSortKey"];
			$aryEstimateQuery[] = isset($aryRowsValues["lngSalesDivisionCode"]) ? $aryRowsValues["lngSalesDivisionCode"] : 'NULL';
			$aryEstimateQuery[] = isset($aryRowsValues["lngSalesClassCode"]) ? $aryRowsValues["lngSalesClassCode"] : 'NULL';

			$aryQuery[] = join ( ", ", $aryEstimateQuery ) . ")";
			unset ( $aryEstimateQuery );
			unset ( $aryRowsValues );

			// 見積原価明細番号インクリメント
			$lngEstimateDetailNo++;
		}
	}

fncDebug( 'action.txt', $aryQuery, __FILE__, __LINE__);


	/////////////////////////////////////////////////////////////
	// ワークフロー関連クエリ生成開始
	/////////////////////////////////////////////////////////////
	// 仮保存で無い場合、ワークフローマスタに追加
	if ( $aryData["lngWorkflowOrderCode"] != 0 && $aryData["strActionName"] != "temporary" )
	{
		// 承認者のデータを取得
		$aryWorkflowQuery[] = "SELECT";
		$aryWorkflowQuery[] = " wo.lngLimitDays,";
		$aryWorkflowQuery[] = " u.bytMailTransmitFlag,";
		$aryWorkflowQuery[] = " u.strUserDisplayName,";
		$aryWorkflowQuery[] = " u.strMailAddress";
		$aryWorkflowQuery[] = "FROM m_WorkflowOrder wo";
		$aryWorkflowQuery[] = "INNER JOIN m_User u ON ( wo.lngInChargeCode = u.lngUserCode AND u.bytInvalidFlag = FALSE )";
		$aryWorkflowQuery[] = "WHERE wo.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"];
		$aryWorkflowQuery[] = " AND wo.lngWorkflowOrderNo = 1";
		$aryWorkflowQuery[] = " AND wo.bytWorkflowOrderDisplayFlag = TRUE";

		list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryWorkflowQuery ), $objDB );
		unset ( $aryWorkflowQuery );

		if ( $lngResultNum < 1 )
		{
			$objDB->execute( "ROLLBACK" );
			fncOutputError ( 9051, DEF_WARNING, "", TRUE, "", $objDB );
		}

		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$objDB->freeResult( $lngResultID );

//		$aryInChargeUser["strUserDisplayName"]	= $objResult->struserdisplayname;	
// 2004.10.09 suzukaze update start
		$aryInChargeUser["strWorkflowName"]		= "見積原価 [" . $aryData["strProductCode"] . "]";
// 2004.10.09 suzukaze update end
		$bytMailTransmitFlag	= $objResult->bytmailtransmitflag;
		$lngLimitDays			= $objResult->lnglimitdays;
		$strMailAddress			= $objResult->strmailaddress;
//======================================================================================================
// 05.03.15 by kou 
//		$aryInChargeUser["strUserDisplayName"]	= $objResult->UserDisplayName;
		$aryInChargeUser["strURL"] = LOGIN_URL;
		// 入力者のメールアドレスと名前を取る
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress, strUserDisplayName  FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;
		
		list ( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );
		if ( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag 	= $objResult->bytmailtransmitflag;
			$strFromMail	= $objResult->strmailaddress;
			$aryInChargeUser["strUserDisplayName"]	= $objResult->struserdisplayname;
		}
		else
		{
			$objDB->execute( "ROLLBACK" );
			fncOutputError ( 9051, DEF_WARNING, "", TRUE, "", $objDB );

		}
		$objDB->freeResult( $lngUserMailResultID );
//======================================================================================================
		unset ( $objResult );

		// ワークフローコードのインクリメント
		$lngWorkflowCode = fncGetSequence( "m_Workflow.lngworkflowcode", $objDB );
		// ワークフローマスタ登録クエリ生成
		$aryWorkflowQuery[] = "INSERT INTO m_Workflow VALUES ( " . $lngWorkflowCode;
		$aryWorkflowQuery[] = $aryData["lngWorkflowOrderCode"];
		$aryWorkflowQuery[] = "'" . $aryInChargeUser["strWorkflowName"] . "'";
		$aryWorkflowQuery[] = DEF_FUNCTION_E1;
		$aryWorkflowQuery[] = "'" . $aryData["lngEstimateNo"] . "'";
		$aryWorkflowQuery[] = "NOW()";
		$aryWorkflowQuery[] = "NULL";
		$aryWorkflowQuery[] = $objAuth->UserCode;
		$aryWorkflowQuery[] = $objAuth->UserCode;
		$aryWorkflowQuery[] = "FALSE";
		$aryWorkflowQuery[] = "NULL )";

		$aryQuery[] = join ( ", ", $aryWorkflowQuery );
		unset ( $aryWorkflowQuery );

		// ワークフロー登録クエリ生成
		$aryWorkflowQuery[] = "INSERT INTO t_Workflow VALUES ( " . $lngWorkflowCode;
		$aryWorkflowQuery[] = 1;
		$aryWorkflowQuery[] = 1;
		$aryWorkflowQuery[] = DEF_STATUS_ORDER;
		$aryWorkflowQuery[] = "NULL";
		$aryWorkflowQuery[] = "NOW()";
		$aryWorkflowQuery[] = "NOW() + interval '" . $lngLimitDays . " days' )";
		unset ( $lngLimitDays );

		$aryQuery[] = join ( ", ", $aryWorkflowQuery );
		unset ( $aryWorkflowQuery );
	}
}

// 削除の場合、見積原価マスタの製品コード更新クエリ生成
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
{
	$aryEstimateQuery[] = "UPDATE m_Estimate SET";
	$aryEstimateQuery[] = "strProductCode = '" . $aryData["strProductCode"] . "_del'";
	$aryEstimateQuery[] = "WHERE lngEstimateNo = " . $aryData["lngEstimateNo"];
	$aryQuery[] = join ( " ", $aryEstimateQuery );
	unset ( $aryEstimateQuery );
}


unset ( $aryEstimateData );
unset ( $aryCompanyCode );
unset ( $aryMonetaryUnitCode );
unset ( $aryRate );
unset ( $bytDecisionFlag );
unset ( $aryDetail );
unset ( $lngEstimateDetailNo );






//////////////////////////////////////////////////////////////////////////
// クエリ実行(見積原価追加)
//////////////////////////////////////////////////////////////////////////
for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
//	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}


//////////////////////////////////////////////////////////////////////////
// メール送信(見積原価追加)
//////////////////////////////////////////////////////////////////////////
if ( $bytMailTransmitFlag == "t" && $strMailAddress )
{

	list ( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_E1, $aryInChargeUser, $objDB );
//	$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );


	$blnSendMailFlag = fncSendMail( $strMailAddress, $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" );

	if ( !$strMailAddress || !$blnSendMailFlag )
	{
		$objDB->execute( "ROLLBACK" );
		fncOutputError ( 9053, DEF_WARNING, "メール送信失敗。", TRUE, "", $objDB );
	}
}

unset ( $aryInChargeUser );
unset ( $bytMailTransmitFlag );

$objDB->transactionCommit();


//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////


if ( $lngEstimateStatusCode == DEF_ESTIMATE_TEMPORARY )
{
	$aryData["lngSaveType"] = 1;
}
else
{
	$aryData["lngSaveType"] = 0;
}

// 帳票出力表示切替
// 削除以外にて帳票出力ボタンを表示する
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && $lngEstimateStatusCode == DEF_ESTIMATE_APPLICATE )
{
	$aryData["PreviewVisible"] = "hidden";
}
else
{
	$aryData["PreviewVisible"] = "hidden";
//	$aryData["PreviewVisible"] = "visible";
	$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $lngEstimateDetailNo . "&bytCopyFlag=TRUE";
}






	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード





// 見積原価情報の場合
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
{
	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/estimate/regist/edit.php?lngFunctionCode=" . DEF_FUNCTION_E1 ."&strSessionID=";

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/finish/parts.tmpl" );

//	$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
//	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;
}
// 見積原価修正・削除の場合
elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
{
	// 成功時戻り先のアドレス指定 （意味無し。削除予定）
	$aryData["strAction"] = "/estimate/search/index.php?lngFunctionCode=" . $aryData["lngFunctionCode"] ."&strSessionID=";

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/finish/parts.tmpl" );

//	$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
//	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;
}


unset ( $lngEstimateStatusCode );
unset ( $g_aryTemp );

$objDB->close();


return TRUE;
?>
