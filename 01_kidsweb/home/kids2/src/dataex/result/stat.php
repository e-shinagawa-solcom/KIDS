<?php
// *********************************
// ***** Please save as UTF-8. *****
// *********************************

// ----------------------------------------------------------------------------
/** 
*	データエクスポート 実行
*
*
*	@package    K.I.D.S.
*	@license    http://www.kuwagata.co.jp/
*	@copyright  KUWAGATA CO., LTD.
*	@author     K.I.D.S. Groups <info@kids-groups.com>
*	@access     public
*	@version 	2.00
*
*
*	更新履歴
*	2014.11.08	商品計画書　機能追加 k.saito
*/
// ----------------------------------------------------------------------------
ini_set("display_errors", 1);
error_reporting(E_ALL);

	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require_once(LIB_FILE);
	require_once(SRC_ROOT . "dataex/cmn/lib_dataex.php");
	require_once(SRC_ROOT . "dataex/result/spreadsheet.php");
	
	
	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );
	$objDB->setInputEncoding("utf-8");

	// データ取得
	$aryData = $_REQUEST;

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認のための出力対象の機能コードを取得
	$lngFunctionCode = getFunctionCode( $aryData["lngExportData"] );

	// 権限確認
	if ( !fncCheckAuthority( $lngFunctionCode, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	$aryCheck["strSessionID"]             = "null:numenglish(32,32)";
	$aryCheck["lngExportData"]            = "null:number(1,8)";		// 1 - 8 種類
	$aryCheck["lngActionCode"]            = "null:number(1,3)";
 
	// 見積原価書のみ
	if ( $aryData["lngExportData"] == DEF_EXPORT_ESTIMATE )
	{
//		$aryCheck["strProductCode"] = "numenglish(0,4)";
	}
	// 他
	else
	{
//		$aryCheck["lngExportConditions"]      = "null:number(1,3)";
//		$aryCheck["dtmAppropriationDateFrom"] = "date(/)";
//		$aryCheck["dtmAppropriationDateTo"]   = "date(/)";
	}

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );


	switch($aryData["lngExportData"])
	{

		case DEF_EXPORT_SALES:		// 1
			///////////////////////////////////////////////////////////////////////////
			// 売上レシピ
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_SALES]["filename"] = "xls_sales";
			$aryData[DEF_EXPORT_SALES]["prefix"] = "sales";
			$aryData[DEF_EXPORT_SALES]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]][$aryData["lngExportConditions"]];
			
			// 部門・顧客別 or 部門・製品別
			switch($aryData["lngExportConditions"])
			{
				case 1:
					$aryExportDataInfo[DEF_EXPORT_SALES]["strExportConditions"] = 'g.strGroupDisplayCode, c.strCompanyDisplayCode, sa.strSalesCode';
					break;
				case 2:
					$aryExportDataInfo[DEF_EXPORT_SALES]["strExportConditions"] = 'g.strGroupDisplayCode, p.lngProductNo, sa.strSalesCode';
					break;
			}

			break;

		case DEF_EXPORT_STOCK:		// 4
			///////////////////////////////////////////////////////////////////////////
			// 仕入一覧表
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_STOCK]["filename"] = "xls_stock";
			$aryData[DEF_EXPORT_STOCK]["prefix"] = "stock";
			$aryData[DEF_EXPORT_STOCK]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]][$aryData["lngExportConditions"]];
			
			// 仕入科目・仕入先別 or 仕入科目・部門・製品別
			switch($aryData["lngExportConditions"])
			{
				case 1:
					$aryExportDataInfo[DEF_EXPORT_STOCK]["strExportConditions"] = 'c.strCompanyDisplayCode, sd.lngStockSubjectCode';
					break;
				case 2:
					$aryExportDataInfo[DEF_EXPORT_STOCK]["strExportConditions"] = 'sd.lngStockSubjectCode, g.strGroupDisplayCode, p.strProductCode';
					break;
			}

			break;

		case DEF_EXPORT_PURCHASE:		// 2
			///////////////////////////////////////////////////////////////////////////
			// Purchase Recipe
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_PURCHASE]["filename"] = "xls_purchase";
			$aryData[DEF_EXPORT_PURCHASE]["prefix"] = "purchase";
			$aryData[DEF_EXPORT_PURCHASE]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]][$aryData["lngExportConditions"]];
			
			// L/C or T/T or On Board
			switch($aryData["lngExportConditions"])
			{
				case 1:
					$aryExportDataInfo[DEF_EXPORT_PURCHASE]["strExportConditions"] = "AND s.lngPayConditionCode = " . DEF_PAYCONDITION_LC . " ORDER BY s.lngMonetaryUnitCode, c.strCompanyDisplayCode, g.strGroupDisplayCode, sd.strProductCode";
					break;
				case 2:
					$aryExportDataInfo[DEF_EXPORT_PURCHASE]["strExportConditions"] = "AND s.lngPayConditionCode = " . DEF_PAYCONDITION_TT . " ORDER BY s.lngMonetaryUnitCode, c.strCompanyDisplayCode, g.strGroupDisplayCode, sd.strProductCode";
					break;
				case 3:
					$aryExportDataInfo[DEF_EXPORT_PURCHASE]["strExportConditions"] = "AND date_trunc( 'month', s.dtmAppropriationDate ) < date_trunc( 'month', s.dtmExpirationDate )" . " ORDER BY s.lngMonetaryUnitCode, g.strGroupDisplayCode, sd.strProductCode";
					break;
			}

			break;

		case DEF_EXPORT_STAT01:		// 6
			///////////////////////////////////////////////////////////////////////////
			// 売上見込
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_STAT01]["filename"] = "xls_stat01";
			$aryData[DEF_EXPORT_STAT01]["prefix"] = "stat01";
			$aryData[DEF_EXPORT_STAT01]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]];
			
			// 部門
			if(isset($aryData["lngGroupCode"]))
			{
				$aryExportDataInfo[DEF_EXPORT_STAT01]["lngInchargeGroupCode"] = $aryData["lngGroupCode"];
			}
			else
			{
				$aryExportDataInfo[DEF_EXPORT_STAT01]["lngInchargeGroupCode"] = '0';
			}

			// 売上区分	
			if ( is_array($aryData["lngSalesClassCode"]) )
			{
				// 売上区分 は複数設定されている可能性があるので、設定個数分ループ
				$strBuff = "";

				for ( $i = 0; $i < count($aryData["lngSalesClassCode"]); $i++ )
				{
					if ( $i <> 0 )	// 初回処理以外
					{
						$strBuff .= " ,";
					}
					$strBuff .= "" . $aryData["lngSalesClassCode"][$i] . "";
				}

			}
			else
			{  $strBuff = "0";
			}
			$aryExportDataInfo[DEF_EXPORT_STAT01]["lngSalesClassCode"] = $strBuff;
			break;
			
		case DEF_EXPORT_STAT02:		// 7
			///////////////////////////////////////////////////////////////////////////
			// 概算売上
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_STAT02]["filename"] = "xls_stat02";
			$aryData[DEF_EXPORT_STAT02]["prefix"] = "stat02";
			$aryData[DEF_EXPORT_STAT02]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]];
			// 部門
			if(isset($aryData["lngGroupCode"]))
			{
				$aryExportDataInfo[DEF_EXPORT_STAT02]["lngInchargeGroupCode"] = $aryData["lngGroupCode"];
			}
			else
			{
				$aryExportDataInfo[DEF_EXPORT_STAT02]["lngInchargeGroupCode"] = '0';
			}
			
			// 売上区分	
			if ( is_array($aryData["lngSalesClassCode"]) )
			{
				// 売上区分 は複数設定されている可能性があるので、設定個数分ループ
				$strBuff = "";

				for ( $i = 0; $i < count($aryData["lngSalesClassCode"]); $i++ )
				{
					if ( $i <> 0 )	// 初回処理以外
					{
						$strBuff .= " ,";
					}
					$strBuff .= "" . $aryData["lngSalesClassCode"][$i] . "";
				}

			}
			else
			{  $strBuff = "0";
			}
			$aryExportDataInfo[DEF_EXPORT_STAT02]["lngSalesClassCode"] = $strBuff;
			break;

		case DEF_EXPORT_PPLAN:		// 8
			///////////////////////////////////////////////////////////////////////////
			// Purchase Recipe
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_PPLAN]["filename0"] = "xls_pplan";
			$aryExportDataInfo[DEF_EXPORT_PPLAN]["filename1"] = "xls_pplan_select";
			$aryExportDataInfo[DEF_EXPORT_PPLAN]["filename2"] = "xls_pplan_factory";
			$aryExportDataInfo[DEF_EXPORT_PPLAN]["filename3"] = "xls_pplan_count";
			$aryData[DEF_EXPORT_PPLAN]["prefix"] = "pplan";
			$aryData[DEF_EXPORT_PPLAN]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]];
/*
			if(isset($aryData["lngGroupCode"]))
			{
				$aryExportDataInfo[DEF_EXPORT_PPLAN]["lngInchargeGroupCode"] = $aryData["lngGroupCode"];
			}
			else
			{
				$aryExportDataInfo[DEF_EXPORT_PPLAN]["lngInchargeGroupCode"] = '0';
			}
*/
			// 部門
			if ( is_array($aryData["lngGroupCode"]) )
			{
				// 部門 は複数設定されている可能性があるので、設定個数分ループ
				$strBuff = "";

				for ( $i = 0; $i < count($aryData["lngGroupCode"]); $i++ )
				{
					if ( $i <> 0 )	// 初回処理以外
					{
						$strBuff .= " ,";
					}
					$strBuff .= "" . $aryData["lngGroupCode"][$i] . "";
				}

			}
			else
			{  $strBuff = "0";
			}
			$aryExportDataInfo[DEF_EXPORT_PPLAN]["lngInchargeGroupCode"] = $strBuff;
			//$aryExportDataInfo[DEF_EXPORT_PPLAN]["_%MONTH%_"] = '';
	}


	// 期間指定設定
	$aryExportDataInfo[$aryData["lngExportData"]]["dtmAppropriationDateFrom"]	= $aryData["dtmAppropriationDateFrom"];
	$aryExportDataInfo[$aryData["lngExportData"]]["dtmAppropriationDateTo"]		= $aryData["dtmAppropriationDateTo"];

	if( $aryData["lngExportData"] != 8)
	{
		// クエリファイルオープン
		if ( !$strQuery = file_get_contents( DEF_QUERY_ROOT . $aryExportDataInfo[$aryData["lngExportData"]]["filename"] . ".sql" ) )
		{
			fncOutputError ( 9059, DEF_FATAL, "ファイルオープンに失敗しました。", TRUE, "", $objDB );
			echo $aryExportDataInfo[$aryData["lngExportData"]]["filename"];
		}
		$strQuery = sqlquery_replace($strQuery, $aryExportDataInfo[$aryData["lngExportData"]]);

	}
	elseif($aryData["lngExportData"] == 8)
	{
		for($i=0; $i<4; $i++)
		{
		// クエリファイルオープン
			if ( !$strQuery = file_get_contents( DEF_QUERY_ROOT . $aryExportDataInfo[$aryData["lngExportData"]]["filename".$i] . ".sql" ) )
			{
				fncOutputError ( 9059, DEF_FATAL, "ファイルオープンに失敗しました。", TRUE, "", $objDB );
				echo $aryExportDataInfo[$aryData["lngExportData"]]["filename".$i];
			}
			$strQuery = sqlquery_replace($strQuery, $aryExportDataInfo[$aryData["lngExportData"]]);
			// SQLQueryを保持
			$aryData["SQL".$i] = $strQuery;
		}
	}



/*
	// タイトルの挿入
	$strMasterData = $aryTitleName[$aryData["lngExportData"]];
	// 検索指定内容の挿入
	$strMasterData .= "　";
	if ( $aryData["dtmAppropriationDateFrom"] != "" or $aryData["dtmAppropriationDateTo"] != "" )
	{
		$strMasterData .= "期間" . $aryData["dtmAppropriationDateFrom"] . " ～ " . $aryData["dtmAppropriationDateTo"];
	}
	if ( $aryData["strOrderCodeFrom"] != "" or $aryData["strOrderCodeTo"] != "" )
	{
		$strMasterData .= "発注No" . $aryData["strOrderCodeFrom"] . " ～ " . $aryData["strOrderCodeTo"];
	}
	$strMasterData .= "\n";
*/
	if( $aryData["lngExportData"] != 8)
	{

		/* ダウンロード実行の場合 */
		$result = false;
		$lngResultRows = 0;
		$lngFieldNum = 0;
		if( $aryData["lngActionCode"] == 2 )
		{
			// マスタ取得クエリ実行
			if ( !$result = $objDB->execute( $strQuery ) )
			{
				echo "id\tname1\nマスターデータの結果取得に失敗しました。\n";
		    	exit;
			}

			// 行数の取得
			$lngResultRows = pg_Num_Rows( $result );
			if( $lngResultRows == 0 )
			{
				echo '結果データが0件です。条件を再設定して下さい。<br />';
				if( $aryData["preview"] )
				{
					echo '<a href="javascript:window.close();">閉じる</a>';
				}
				else
				{
					echo '<a href="javascript:history.back();">戻る</a>';
				}
				exit;
			}
			
			$lngFieldNum = $objDB->getFieldsCount( $result );
		
		}
	}

//var_dump($aryData);
//exit;

	$aryResult = array();
	///////////////////////////////////////////////////////////////////// 
	// データ取得
	$aryResult = fncSpreadSheetDataFormat( $result, $lngResultRows, $lngFieldNum, $aryData, $objDB );


	/* **************************************************************
		SQLクエリの整形
	************************************************************** */
	function sqlquery_replace($strQuery, $replacekey)
	{

		// コメント(//タイプ)の削除
		$strQuery = preg_replace ( "/\/\/.+?\n/", "", $strQuery );
		// 2つのスペース、改行、タブをスペース1つに変換
		$strQuery = preg_replace ( "/(\s{2}|\n|\t)/", " ", $strQuery );
		// コメント(/**/タイプ)の削除
		$strQuery = preg_replace ( "/\/\*.+?\*\//m", "", $strQuery );


		// 置き換え文字列の置換
		$aryKeys = array_keys($replacekey);
		foreach( $aryKeys as $strKey )
		{
			$strQuery = preg_replace ( "/_%" . $strKey . "%_/", $replacekey[$strKey], $strQuery );

		}

		// 置き換えられなかった変数の WHERE 句、修正
		$strQuery = preg_replace ( "/AND [\w\._\(\)', ]+? [<>]?= '%??%??'/", "", $strQuery );

		// \ の処理(DB 問い合わせのため \ を \\ にする)
		$strQuery = preg_replace ( "/\\\\/", "\\\\\\\\", $strQuery );

		return $strQuery;
	}

?>
