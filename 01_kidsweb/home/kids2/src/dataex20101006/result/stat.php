<?php

	
	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "dataex/cmn/lib_dataex.php");
	require (SRC_ROOT . "dataex/result/spreadsheet.php");
	
	
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
	$aryCheck["lngExportData"]            = "null:number(6,7)";
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
		case DEF_EXPORT_STAT01;		// 6
			///////////////////////////////////////////////////////////////////////////
			// 売上見込
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[DEF_EXPORT_STAT01]["filename"] = "stat01";
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
			
		case DEF_EXPORT_STAT02;		// 7
			///////////////////////////////////////////////////////////////////////////
			// 概算売上
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[7]["filename"] = "stat02";
			$aryData[7]["prefix"] = "stat02";
			$aryData[7]["strTitleName"] = $aryTitleName[$aryData["lngExportData"]];
			// 部門
			if(isset($aryData["lngGroupCode"]))
			{
				$aryExportDataInfo[7]["lngInchargeGroupCode"] = $aryData["lngGroupCode"];
			}
			else
			{
				$aryExportDataInfo[7]["lngInchargeGroupCode"] = '0';
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
			$aryExportDataInfo[7]["lngSalesClassCode"] = $strBuff;
			break;
	}


	// 期間指定設定
	$aryExportDataInfo[$aryData["lngExportData"]]["dtmAppropriationDateFrom"]	= $aryData["dtmAppropriationDateFrom"];
	$aryExportDataInfo[$aryData["lngExportData"]]["dtmAppropriationDateTo"]		= $aryData["dtmAppropriationDateTo"];


	// クエリファイルオープン
	if ( !$strQuery = file_get_contents( DEF_QUERY_ROOT . $aryExportDataInfo[$aryData["lngExportData"]]["filename"] . ".sql" ) )
	{
		fncOutputError ( 9059, DEF_FATAL, "ファイルオープンに失敗しました。", TRUE, "", $objDB );
	}


	// コメント(//タイプ)の削除
	$strQuery = preg_replace ( "/\/\/.+?\n/", "", $strQuery );
	// 2つのスペース、改行、タブをスペース1つに変換
	$strQuery = preg_replace ( "/(\s{2}|\n|\t)/", " ", $strQuery );
	// コメント(/**/タイプ)の削除
	$strQuery = preg_replace ( "/\/\*.+?\*\//m", "", $strQuery );


	// 置き換え文字列の置換
	$aryKeys = array_keys ( $aryExportDataInfo[$aryData["lngExportData"]] );
	foreach ( $aryKeys as $strKey )
	{
		$strQuery = preg_replace ( "/_%" . $strKey . "%_/", $aryExportDataInfo[$aryData["lngExportData"]][$strKey], $strQuery );

	}
	
	unset ( $aryKey );

	// 置き換えられなかった変数の WHERE 句、修正
	$strQuery = preg_replace ( "/AND [\w\._\(\)', ]+? [<>]?= '%??%??'/", "", $strQuery );

	// \ の処理(DB 問い合わせのため \ を \\ にする)
	$strQuery = preg_replace ( "/\\\\/", "\\\\\\\\", $strQuery );

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


	$aryResult = array();
	///////////////////////////////////////////////////////////////////// 
	// 社内統計データ 1 / 2
	$aryResult = fncSpreadSheetDataFormat( $result, $lngResultRows, $lngFieldNum, $aryData, $objDB );


exit;

?>
