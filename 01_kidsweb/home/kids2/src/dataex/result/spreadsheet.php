<?php
// *********************************
// ***** Please save as UTF-8. *****
// *********************************
// ----------------------------------------------------------------------------
/**
*       スプレッドシート形式、データ生成関数群
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     kuwagata
*       @access     public
*       @version    2.00
*
*
*       処理概要
*		・JSON形式でのデータ生成を行う
*		・Spreadsheet/Excel/Writer形式での出力を行う
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

	// 共通定義
	include_once('conf.inc');
	require_once('./js/JSON.php');
	require_once("Spreadsheet/Excel/Writer.php");

	// データ種の定義
	require_once("./spreadsheet/sales.php");
	require_once("./spreadsheet/stock.php");
	require_once("./spreadsheet/purchase.php");
	require_once("./spreadsheet/stat01.php");
	require_once("./spreadsheet/stat02.php");
	require_once("./spreadsheet/pplan.php");
	
	
	
	/*
	//****************************************************************************
	// スプレッドシート形式にデータをフォーマット
	// 
	//****************************************************************************
	*/
	function fncSpreadSheetDataFormat( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB )
	{

		/////////////////////////////////////////////////////////////////////////////
		// データプレビュー
		if ( $aryData["lngActionCode"] == 1 )
		{

/* HTMLプレビュー（SigmaGrid）を使用しないので コメントアウト */
/*
			// JSON形式のデータに変換
			$strDataJson = fncSpreadSheetDataJson2( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB );

			if($strDataJson=="")
			{
				return false;
			}
			// SigmaGrid用ヘッダー情報の取得
			if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
			{
				return false;
			}
			
			$aryExportDataInfo = array();
			$aryExportDataInfo[$aryData["lngExportData"]]["__DATASET__"]	= $aryHeader["DATASET"];
			$aryExportDataInfo[$aryData["lngExportData"]]["__COLUMNS__"]	= $aryHeader["COLUMNS"];
			$aryExportDataInfo[$aryData["lngExportData"]]["__DATA__"]	= $strDataJson;
*/
			$aryExportDataInfo[$aryData["lngExportData"]]["__TITLE__"]	= $aryData[$aryData["lngExportData"]]["strTitleName"];	// 社内統計データ


			// 売上区分の配列データを文字列へ変換する
			$strlngSalesClassCode = "";
			if(is_array($aryData["lngSalesClassCode"]) )
			{
				foreach($aryData["lngSalesClassCode"] as $data)
				{
					$strlngSalesClassCode .= "&amp;lngSalesClassCode[]=".$data;
				}
			}
			
			// リンク用のURLを生成する
			switch($aryData["lngExportData"])
			{
				case 1:	// 売上レシピ
				case 4:	// 仕入一覧表
				case 2:	// Purchase recipe
					$aryExportDataInfo[$aryData["lngExportData"]]["__EXCEL_URL__"]	= '/dataex/result/stat.php?strSessionID='.$aryData["strSessionID"].'&'.'lngExportData='.$aryData["lngExportData"].'&lngActionCode=2&dtmAppropriationDateFrom='.$aryData["dtmAppropriationDateFrom"].'&dtmAppropriationDateTo='.$aryData["dtmAppropriationDateTo"].'&lngExportConditions='.$aryData["lngExportConditions"].'&preview=true';
					break;
				case 6:	// 社内統計データ（売上見込）
				case 7:	// 社内統計データ（概算売上）
					$aryExportDataInfo[$aryData["lngExportData"]]["__EXCEL_URL__"]	= '/dataex/result/stat.php?strSessionID='.$aryData["strSessionID"].'&'.'lngExportData='.$aryData["lngExportData"].'&lngActionCode=2&dtmAppropriationDateFrom='.$aryData["dtmAppropriationDateFrom"].'&dtmAppropriationDateTo='.$aryData["dtmAppropriationDateTo"].'&lngGroupCode='.$aryData["lngGroupCode"].$strlngSalesClassCode.'&preview=true';
					break;
			}


			// SpreadSheet用テンプレートの取得
			//if( !$strTemplate = file_get_contents( "./spreadsheet_".$aryData[$aryData["lngExportData"]]["prefix"].".html" ) )
			if( !$strTemplate = file_get_contents( "./spreadsheet_preview.html" ) )
			{
				return false;
			}

			// 置き換え文字列の置換
			$aryKeys = array_keys( $aryExportDataInfo[$aryData["lngExportData"]] );
			foreach ( $aryKeys as $strKey )
			{
				$strTemplate = preg_replace( "/_%" . $strKey . "%_/", $aryExportDataInfo[$aryData["lngExportData"]][$strKey], $strTemplate );
			}

			echo $strTemplate;
			return true;
		}
		/////////////////////////////////////////////////////////////////////////////
		// データダウンロード（エクセル形式）
		elseif ( $aryData["lngActionCode"] == 2 )
		{
			// ワークブックの作成
			$workbook = fncSpreadSheetCreateWorkbook();

			if( (int)$aryData["lngExportData"] == 8)
			{
				// ExcelWriter形式のデータに変換
				if( fncSpreadSheetDataExcel_Block( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB, $workbook ) == false)
				{
					return false;
				}
			}
			else
			{
				// ExcelWriter形式のデータに変換 // $workbook を参照渡し
				if( fncSpreadSheetDataExcel( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB, $workbook ) == false)
				{
					return false;
				}
			}
			//	header("Content-Type: application/vnd.ms-excel");
			//	header("Content-Disposition: attachment; filename=hoge.xls");
//exit;
			/**
			 * Excelファイルを出力
			 */
			// ファイル名の生成
			$filename = mb_convert_encoding($aryData[$aryData["lngExportData"]]["strTitleName"].'_('.str_replace('/','',$aryData["dtmAppropriationDateFrom"]).'-'.str_replace('/','',$aryData["dtmAppropriationDateTo"]).')_'.date('Ymd_Hi', mktime()) .'.xls', 'shift_jis', 'auto');
			if( $aryData["preview"] ) $filename = date('Ymd_hm').'.xls';

			// Excelファイルを出力
			$workbook->send($filename);

			// Excelオブジェクトの後処理
			$workbook->close();

			return true;
		}
		/////////////////////////////////////////////////////////////////////////////
		// 配列データを生成する
		else
		{
			$aryResult = array();
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{
				// 行結果取得
				$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		
		return $aryResult;
	}

	/*
	//****************************************************************************
	//	データベースから取得したデータを
	//	JSON形式に変換する
	//
	*	@param  Integer $lngResultID		SQL結果ID
	*	@param  Integer $lngResultRows		結果行数
	*	@param  Integer $lngFieldNum		結果カラム数
	*	@param  Integer $aryData			入力パラメーターデータ
	*	@param  Object  $objDB				DBオブジェクト
	*	@return String  $strJsonData		JSON形式のデータ
	*	@access public
	//****************************************************************************
	*/
	function fncSpreadSheetDataJson2($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB)
	{

		//
		// JSON形式（SigmaGrid用）ヘッダー情報の取得
		if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
		{
			return false;
		}

		$objJson = new Services_JSON; 
		$aryDataset = $objJson->decode($aryHeader["DATASET"]); // dataset
		$aryColumns = $objJson->decode($aryHeader["COLUMNS"]); // dataset

		$arySetData = array();
		// 結果を成形
		for ( $i = 0; $i < $lngResultRows; $i++ )
		{
			// 行結果取得
			$aryResult = $objDB->fetchArray ( $lngResultID, $i );
			$aryCellData = array();
			for ( $j = 0; $j < count($aryDataset); $j++ )
			{
//				$strKey = strtolower($aryDataset[$j]->name);
//				$aryCellData[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$strKey] );// 空白削除

				$varCellData = "";
				
				// データ取得と生成
				$varCellData = fncSpreadSheetCellData01($aryDataset, $i, $j, $aryResult, 0);
				$aryDataset[$j]->data = $varCellData;

				$aryCellData[$j] = $varCellData;
				
			}
			
			// 2次元配列のセット
			$arySetData[] = $aryCellData;
		}
		
		return $objJson->encode($arySetData);
		
		//var_dump($objJson->encode($arySetData));
		//exit;
	}

	/*
	//****************************************************************************
	//	（fncSpreadSheetDataJson2() へ移管）
	//	データベースから取得したデータをJSON形式に変換する
	//****************************************************************************
	*/
	function fncSpreadSheetDataJson($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB)
	{

		$strSpreadSheetData = "";

		// 結果を成形
		for ( $i = 0; $i < $lngResultRows; $i++ )
		{
			// 行結果取得
			$aryResult = $objDB->fetchArray ( $lngResultID, $i );

			$strSpreadSheetData .= "[";
			
			// カラム数表示
			for ( $j = 0; $j < $lngFieldNum; $j++ )
			{
				$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
				$strResult = "";
				if( $j == 2 )
				{
					$strResult = $aryResult[$j];
				}
				else
				{
					$strResult = '"'.$aryResult[$j].'"';
				}
				
				$strSpreadSheetData .= '"'.$aryResult[$j].'"';
				if( ($j+1) < $lngFieldNum ) $strSpreadSheetData .= ",";
				
			}
			$strSpreadSheetData .= "]";
			if( ($i+1) < $lngResultRows )
			{
				$strSpreadSheetData .= ",\n";
			}
			else
			{
				$strSpreadSheetData .= "\n";
			}
			
		}
		
		return "[\n".$strSpreadSheetData."\n]";
	}


	/*
	//****************************************************************************
	//	
	//	データベースから取得したデータをSpreadsheet/Excel/Writer形式に変換する
	//****************************************************************************
	*/
	function fncSpreadSheetDataExcel_Block($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB, &$workbook)
	{

	    /**
	     * ワークシートの追加
	     */
		$worksheet =& $workbook->addWorksheet( mb_convert_encoding($aryData["strTitleName"], 'shift_jis') );
//		$worksheet =& $workbook->addWorksheet('stat01');
		$worksheet->setInputEncoding("sjis-win");
		$worksheet->setPaper(8);
//		$worksheet->setLandscape;
//		$worksheet->repeatRows(1,4);
		


		//
		// JSON形式ヘッダー情報の取得
		if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
		{
			return false;
		}
		$objJson = new Services_JSON; 
		$aryDataset = $objJson->decode($aryHeader["DATASET"]); // dataset
		$aryColumns = $objJson->decode($aryHeader["COLUMNS"]); // dataset
//var_dump($aryColumns);
//exit;
		unset($objJson);


		// ----------------------------------------------------------------
		// ヘッダーの設定

		// フォーマットオブジェクトの生成
		$objFormat0 =& $workbook->addFormat();
		// ヘッダースタイル
		fncSpreadSheetExcelFormatSetting($objFormat0, "header0");
		fncSpreadSheetExcelFormatSetting($objFormat0, "setFontFamilyMsGothic");

		// データ種による取得処理
		switch( (int)$aryData["lngExportData"] )
		{
			// ----------------------------------------------------
			// pplan
			case 8:
				// 最初の見出しの設定
				$lngHeaderCount = fncfncSpreadSheetCellHeader_pplan($worksheet, $objFormat0, $aryData);

				$worksheet->setZoom(57);
				break;
			// ----------------------------------------------------
		}


		// ----------------------------------------------------------------
		// ヘッダーを生成
		//
		//
		// ----------------------------------------------------------------
		$objFormat1 =& $workbook->addFormat();
		// ヘッダースタイル
		fncSpreadSheetExcelFormatSetting($objFormat1, "header");
		fncSpreadSheetExcelFormatSetting($objFormat1, "setFontFamilyMsGothic");

		// ヘッダーカラムの設定
		for( $j = 0; $j < count($aryColumns); $j++ )
		{
			//$worksheet->writeString( 0, $i, $aryColumns[$i]->header, $objFormat1 );
			//$worksheet->writeString( $lngHeaderCount, $j, mb_convert_encoding($aryColumns[$j]->header,'sjis-win'), $objFormat1 );

			// 列の幅設定
			$worksheet->setColumn($j, $j, ((int)($aryColumns[$j]->width)/10)+2 );
		}
		

		// ----------------------------------------------------------------
		// 印刷領域に関する設定
		//
		//
		// ----------------------------------------------------------------
		// 印刷ページ毎のタイトルを設定
		$worksheet->repeatRows(0, $lngHeaderCount);	// $lngHeaderCount=3を指定すると、見える行番号としては4行目が指定されます。(0, $lngHeaderCount) と言った指定も可能です。
		// 拡大縮小印刷の設定
		$worksheet->fitToPages(1, 0);	// 横, 縦 ／ 1,0 場合：横に1ページに収めて、縦はフレキシブル
		// 用紙サイズを設定します
		$worksheet->setPaper(8);	// A3=8,  A4=9
		// ページの方向を横に設定する
		$worksheet->setLandscape();	// 横
		//$worksheet->setPortrait();	// 縦
		// ヘッダ
		$worksheet->setHeader(mb_convert_encoding('&R&P/&N','sjis-win'), 0);
		// 余白
		$worksheet->setMargins(0.5); //各余白0.5インチ
		$worksheet->setMarginLeft(1); //左の余白は1インチ

		/* setHeader() 制御文字一覧
		
		    制御文字            種類                 説明
		    =======             ====                ===========
		    &L                  位置                左
		    &C                                      中央
		    &R                                      右
		    
		    &P                  情報                ページ番号
		    &N                                      合計ページ数
		    &D                                      日付
		    &T                                      時刻
		    &F                                      ファイル名
		    &A                                      ワークシート名
		    
		    &fontsize           フォント             フォントサイズ
		    &"font,style"                           フォント名とスタイル
		*/
		// ----------------------------------------------------------------


		// ヘッダーの行数（上記で設定済みの行数）
		$lngHeadLineCnt = $lngHeaderCount;// + 1;

		// ヘッダー部、最初のx行を固定する
		$worksheet->freezePanes(array($lngHeadLineCnt, 0));

		// フォーマットオブジェクトを保持する配列
		$aryFormat = array();

		//$lngSubtotal = 0; // 小計増加行のカウンタ
		
		$arySubTotalBuff = array();	// 小計行の計算バッファ

		

		/* **********************************************************
			工場のユニークな一覧を取得
		*/
		// [count] = lngFactoryCode 一覧 
		$aryFC = array();
		$aryFCInfo = array();


		for($monthCnt=0; $monthCnt< 6; $monthCnt++)
		{
			$aryYmd = ymd_count($aryData["dtmAppropriationDateFrom"], $monthCnt);
			$strQuery = $aryData["SQL0"];

			// SQL2=factory
			$strQuery = preg_replace("/_%PPLAN_SELECT_CONDITION%_/", $aryData["SQL2"], $strQuery);
			$strQuery = preg_replace("/_%YEAR%_/",  $aryYmd["Y"], $strQuery);
			$strQuery = preg_replace("/_%MONTH%_/", $aryYmd["m"], $strQuery);
			$strQuery = preg_replace("/_%WHERE%_/", "ORDER BY mp.lngFactoryCode", $strQuery);

			// マスタ取得クエリ実行
			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
			    continue;
			}
			// 行数の取得
			$lngResultRows = pg_Num_Rows( $lngResultID );
			if( $lngResultRows == 0 )
			{
				continue;
			}


//echo $strQuery;
//echo "\n";
//echo "MON[".$monthCnt."]".$lngResultRows;
//echo "\n";
			for( $i = 0; $i < $lngResultRows; $i++ )
			{
				// 行結果取得
				$aryResult = $objDB->fetchObject( $lngResultID, $i );

				if( is_null($aryResult->lngfactorycode))
				{
					$aryFC[null] = null;
				}
				else
				{
					$aryFC[] = (int)$aryResult->lngfactorycode;	//$aryResult->strcompanydisplayname;
					$aryFCInfo[(int)$aryResult->lngfactorycode] = $aryResult;
				}
			}

			$aryFC = array_unique($aryFC, SORT_NUMERIC);
			//$aryMonthFC[$monthCnt] = $aryFC;

		}
		array_multisort($aryFC, SORT_ASC, SORT_NUMERIC);
//var_dump($aryFC);
//echo "\n";
//exit;
		// [月][工場]= カウント
		$aryMonthFC = array();

		/* **********************************************************
			それぞれの工場が、月別にいくつのデータが有るかを取得
		*/
		for($monthCnt=0; $monthCnt< 6; $monthCnt++)
		{
			for( $i=0; $i<count($aryFC); $i++ )
			{

			$aryYmd = ymd_count($aryData["dtmAppropriationDateFrom"], $monthCnt);
			$strQuery = $aryData["SQL0"];

			// SQL3=count
			$strQuery = preg_replace("/_%PPLAN_SELECT_CONDITION%_/", $aryData["SQL3"], $strQuery);
			$strQuery = preg_replace("/_%YEAR%_/",  $aryYmd["Y"], $strQuery);
			$strQuery = preg_replace("/_%MONTH%_/", $aryYmd["m"], $strQuery);
			$strQuery = preg_replace("/_%WHERE%_/", " AND mp.lngFactoryCode ". (is_null($aryFC[$i]) ? "is null" : "=".$aryFC[$i]), $strQuery);

			// マスタ取得クエリ実行
			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
			    continue;
			}
			// 行数の取得
			$lngResultRows = pg_Num_Rows( $lngResultID );
			if( $lngResultRows == 0 )
			{
				continue;
			}
//echo $strQuery;
//echo "\n";

			// 行結果取得
			$aryResult = $objDB->fetchObject( $lngResultID, 0 );

				$aryMonthFC[$monthCnt][$aryFC[$i]] =  (int)$aryResult->count;
			}

		}
//var_dump($aryFC);
//var_dump($aryMonthFC);
//echo "-----\n";
		

		$aryFactoryLine =  convert_factory_count($aryMonthFC);
//var_dump($aryFactoryLine);
//echo "-----\n";
		$aryLineFB = line_factoryblock($aryFactoryLine);


		fncfncSpreadSheetColumnHeader_pplan( $workbook, $worksheet, $aryFactoryLine, $aryFCInfo);

//var_dump($aryLineFB);
//echo $aryLineFB[null];

//exit;

		/* ----------------------------------------------------------

		*/
		for($monthCnt=0; $monthCnt< 6; $monthCnt++)
		{
			$addCol = 4*$monthCnt;

			$aryYmd = ymd_count($aryData["dtmAppropriationDateFrom"], $monthCnt);
			$strQuery = $aryData["SQL0"];

			// SQL1=select
			$strQuery = preg_replace("/_%PPLAN_SELECT_CONDITION%_/", $aryData["SQL1"], $strQuery);
			$strQuery = preg_replace("/_%YEAR%_/",  $aryYmd["Y"], $strQuery);
			$strQuery = preg_replace("/_%MONTH%_/", $aryYmd["m"], $strQuery);
			$strQuery = preg_replace("/_%WHERE%_/", "ORDER BY mp.lngFactoryCode, mg.strGroupDisplayCode", $strQuery);

//echo $strQuery;
//echo "\n";

			$lngResultID = false;
			$lngResultRows = 0;
			$lngFieldNum = 0;

			// マスタ取得クエリ実行
			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
			    continue;
			}

			// 行数の取得
			$lngResultRows = pg_Num_Rows( $lngResultID );
			if( $lngResultRows == 0 )
			{
				continue;
			}
			
			$lngFieldNum = $objDB->getFieldsCount( $lngResultID );
			
			$lngBuffFactoryCode = "";

			// ----------------------------------------------------------------
			// 結果を成形
			//
			//
			// ----------------------------------------------------------------
			for( $i=0, $iLine=0; $i < $lngResultRows; $i++, $iLine++ )
			{

				
				// 行結果取得
				$aryResult = $objDB->fetchArray( $lngResultID, $i );

				$lngColBlock = 0;
				$lngColumn = 1;	// 0, 1 は左列に固定で設定されるために 1迄は予約済みとする

				if( $lngBuffFactoryCode != $aryResult["lngfactorycode"])
				{
					$iLine = 0;
					/*
					$lngLineFB = empty($aryLineFB[$aryResult["lngfactorycode"]]) ? 0 : $aryLineFB[$aryResult["lngfactorycode"]];
//var_dump("\n----- $i");
//var_dump($aryLineFB[$aryResult["lngfactorycode"]]);
					$lngWriteRow = $lngHeadLineCnt+$lngColBlock+$lngLineFB;
					$aryWriteRow = array("","","","","","","","","","","","","","","","","","","","","","","","aa");
					$objFormatBorderT =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockRowBorderT');
					$worksheet->writeRow( $lngWriteRow, 2, $aryWriteRow, $objFormatBorderT);
					*/

				}
				$lngBuffFactoryCode = $aryResult["lngfactorycode"];

				//echo $monthCnt."]factorycode:".(int)$aryResult["lngfactorycode"]."\n";
				//echo maxline_factoryblock($aryFC, $aryMonthFC, (int)$aryResult["lngfactorycode"]);
				//echo "\n";
/*
echo "bufFactoryCode>".$bufFactoryCode;
echo "\n";
echo "lngfactorycode: ".$aryResult["lngfactorycode"];
echo "\n";
echo "LineAdd: ".$aryLineFB[$aryResult["lngfactorycode"]];
echo "\n";
*/
				//---------------------------------------------------------------
				// カラムの設定（同行・左->右へ）
				//---------------------------------------------------------------
				for( $j=0; $j < count($aryDataset); $j++ )
				{

					$varCellData = "";
					unset($objFormat);

					// 2行目以降を折り返す
					if( $j > 0 && ($j) % 4 == 0 )
					{
						$lngColBlock++;
						$lngColumn = $lngColumn - 4;
					}


					// ----------------------------------------------------
					// データ種による取得処理

					// データ生成と取得
					$varCellData = fncSpreadSheetCellData_pplan($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
					$aryDataset[$j]->data = $varCellData;
					// フォーマットオブジェクトの取得
					$objFormat =& fncSpreadSheetCellFormat_pplan($workbook, $aryFormat, $aryDataset, $j, $aryResult, $iLine);

					// ----------------------------------------------------


			//var_dump($objFormat);
					$lngLine = ($iLine*4)+$lngHeadLineCnt+$lngColBlock+$aryLineFB[$aryResult["lngfactorycode"]];
					//fncSpreadSheetCellWrite($aryDataset[$j]->type, $lngLine, $j, $varCellData, $objFormat);


					$lngColumn++;
/*
	echo "-----\n";
	echo "lngLine[".$lngLine."] i[".$i."] j[".$j."] lngColumn[".$lngColumn."] aryDataset[".$aryDataset[$j]->type."";
	echo "\n";
	echo $varCellData."\n";
	echo "LineAdd: ".$aryLineFB[$aryResult["lngfactorycode"]];
	//var_dump($aryResult);
	echo "\n";
*/	
					if( $iLine == 0)
					{
						//$objFormatBorderT =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockRowBorderT');
						//$worksheet->writeRow( $lngLine, 2, array("","","","","","","","","",""), $objFormatBorderT);
					}

					// ワークシートにセルの書き込み
					if( $aryDataset[$j]->type == 'date' )
					{
						$worksheet->write( $lngLine, $lngColumn+$addCol, $varCellData, $objFormat);
					}
					elseif( $aryDataset[$j]->type == 'text' )
					{
						// BIFF5形式（shift_jisに変換）
						$worksheet->writeString( $lngLine, $lngColumn+$addCol, mb_convert_encoding($varCellData,'sjis-win'), $objFormat );
					}
					elseif( $aryDataset[$j]->type == 'int' || $aryDataset[$j]->type == 'float' )
					{
						$worksheet->writeNumber( $lngLine, $lngColumn+$addCol, $varCellData, $objFormat );
					}
					elseif( $aryDataset[$j]->type == 'null' )
					{
						// 何もしない
					}
					else
					{
						$worksheet->writeFormula( $lngLine, $lngColumn+$addCol, $varCellData, $objFormat );
					}

					//
					// セル操作
					// セルに文字を設定した後で、mergeCells() をしないと、文字が結合セル全てに表示されないので注意
					//
					switch( (int)$j )
					{
						case 1:
						$worksheet->mergeCells( $lngLine, $lngColumn+$addCol, $lngLine, $lngColumn+$addCol+1);
						break;
					}
		
				}

	/*
				$worksheet->writeString( ($i*4)+$lngHeadLineCnt+0, 1, mb_convert_encoding("商品化企画書",'sjis-win'), $objFormatCol );
				$worksheet->writeString( ($i*4)+$lngHeadLineCnt+1, 1, mb_convert_encoding("見積原価書",'sjis-win'), $objFormatCol );
				$worksheet->writeString( ($i*4)+$lngHeadLineCnt+2, 1, mb_convert_encoding("スケジュール表",'sjis-win'), $objFormatCol );
				$worksheet->writeString( ($i*4)+$lngHeadLineCnt+3, 1, mb_convert_encoding("部品表",'sjis-win'), $objFormatCol );
	*/

			}
			$bufRow[$monthCnt] = $lngLine;

		}

		/*
			カラムヘッダの設定
		*/
		// カラム・ヘッダースタイル
		$objFormatCol1 =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockColHeaderLTR');
		$objFormatCol2 =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockColHeaderLR');
		$objFormatCol3 =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockColHeaderLR');
		$objFormatCol4 =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockColHeaderLBR');

//var_dump($bufRow);
//echo max($bufRow);
//exit;
		if( isset($bufRow) )
		{
			for($i=0; $i< (max($bufRow)/4)-2; $i++)
			{
				$worksheet->writeString( $i*4+$lngHeadLineCnt+0, 1, mb_convert_encoding("商品化企画書",'sjis-win'), $objFormatCol1 );
				$worksheet->writeString( $i*4+$lngHeadLineCnt+1, 1, mb_convert_encoding("見積原価書",'sjis-win'), $objFormatCol2 );
				$worksheet->writeString( $i*4+$lngHeadLineCnt+2, 1, mb_convert_encoding("スケジュール表",'sjis-win'), $objFormatCol3 );
				$worksheet->writeString( $i*4+$lngHeadLineCnt+3, 1, mb_convert_encoding("部品表",'sjis-win'), $objFormatCol4 );

			}
		}
		return true;

	}

	function line_factoryblock($aryFCC)
	{
		$aryLine = array();
		$bufFactoryCode = null;
		$bufLine = 0;
		reset($aryFCC);
		$i=0;
		while(list($factoryCode, $val) = each($aryFCC))
		{
			if($i==0)
			{
				$aryLine[$factoryCode] = 0;
			}
			else
			{
				$aryLine[$factoryCode] = $aryLine[$bufFactoryCode] + $bufLine * 4;
			}
			$bufFactoryCode = $factoryCode;
			$bufLine = $val;

			$i++;
		}
		return $aryLine;
	}

	function convert_factory_count($aryMFC)
	{
		$aryLine = array();
		$nullValue = 0;
		for( $monthCnt=0; $monthCnt<6; $monthCnt++)
		{
			if(!isset($aryMFC[$monthCnt])) continue;

			reset($aryMFC[$monthCnt]);
			$i = 0;
			while(list($factoryCode, $val) = each($aryMFC[$monthCnt]))
			{
//echo "factoryCode[". $factoryCode ."]";
//echo "\n";
//echo "is_null[".(int)empty($factoryCode);
//echo "\n";

				if( empty($factoryCode))
				{
					if($nullValue < $val ) $nullValue = $val;
				}
				elseif( $aryLine[$factoryCode] < $aryMFC[$monthCnt][$factoryCode])
				{
					$aryLine[$factoryCode] = $aryMFC[$monthCnt][$factoryCode];
				}
				$i++;
			}
		}
		ksort($aryLine, SORT_NUMERIC);
		$aryLine[""] = $nullValue;
		return $aryLine;
	}

	function ymd_count($baseDate, $monthCnt)
	{
		$ymd = explode('/', $baseDate);
		$month	= $ymd[1]+$monthCnt;
		$day	= $ymd[2];
		$year	= $ymd[0];
		//$varMktime = mktime( 0,0,0, $month, $day, $year );
		$varMktime = strtotime($ymd[0]."-".$ymd[1]."-".$ymd[2]." +".$monthCnt." month");

		return array('Y' => date( "Y", $varMktime ), 'm' => date( "m", $varMktime ), 'd' => date( "d", $varMktime ));
	}

	/*
	//****************************************************************************
	//	
	//	データベースから取得したデータをSpreadsheet/Excel/Writer形式に変換する
	//****************************************************************************
	*/
	function fncSpreadSheetDataExcel($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB, &$workbook)
	{

	    /**
	     * ワークシートの追加
	     */
		$worksheet =& $workbook->addWorksheet( mb_convert_encoding($aryData["strTitleName"], 'shift_jis') );
//		$worksheet =& $workbook->addWorksheet('stat01');
		$worksheet->setInputEncoding("sjis-win");
		$worksheet->setPaper(8);
//		$worksheet->setLandscape;
//		$worksheet->repeatRows(1,4);
		


		//
		// JSON形式ヘッダー情報の取得
		if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
		{
			return false;
		}
		$objJson = new Services_JSON; 
		$aryDataset = $objJson->decode($aryHeader["DATASET"]); // dataset
		$aryColumns = $objJson->decode($aryHeader["COLUMNS"]); // dataset
//var_dump($aryColumns);
//exit;
		unset($objJson);


		// ----------------------------------------------------------------
		// ヘッダーの設定

		// フォーマットオブジェクトの生成
		$objFormat0 =& $workbook->addFormat();
		// ヘッダースタイル
		fncSpreadSheetExcelFormatSetting($objFormat0, "header0");
		fncSpreadSheetExcelFormatSetting($objFormat0, "setFontFamilyMsGothic");

		// データ種による取得処理
		switch( (int)$aryData["lngExportData"] )
		{
			// ----------------------------------------------------
			// 売上レシピ
			case 1:
				$lngHeaderCount = fncfncSpreadSheetCellHeader_Sales($worksheet, $objFormat0, $aryData);
				break;

			// 仕入一覧表
			case 4:
				$lngHeaderCount = fncfncSpreadSheetCellHeader_Stock($worksheet, $objFormat0, $aryData);
				break;

			// Purchase recipe
			case 2:
				$lngHeaderCount = fncfncSpreadSheetCellHeader_Purchase($worksheet, $objFormat0, $aryData);
				break;

			// 01 売上見込
			case 6:
				$lngHeaderCount = fncfncSpreadSheetCellHeader01($worksheet, $objFormat0, $aryData);
				break;
			
			case 7:
				// 最初の見出しの設定
				$lngHeaderCount = fncfncSpreadSheetCellHeader02($worksheet, $objFormat0, $aryData);
				break;
			// ----------------------------------------------------
		}


		// ----------------------------------------------------------------
		// ヘッダーを生成
		//
		//
		// ----------------------------------------------------------------
		$objFormat1 =& $workbook->addFormat();
		// ヘッダースタイル
		fncSpreadSheetExcelFormatSetting($objFormat1, "header");
		fncSpreadSheetExcelFormatSetting($objFormat1, "setFontFamilyMsGothic");

		// ヘッダーカラムの設定
		for( $j = 0; $j < count($aryColumns); $j++ )
		{
			//$worksheet->writeString( 0, $i, $aryColumns[$i]->header, $objFormat1 );
			$worksheet->writeString( $lngHeaderCount, $j, mb_convert_encoding($aryColumns[$j]->header,'sjis-win'), $objFormat1 );

			// 列の幅設定
			$worksheet->setColumn($j, $j, ((int)($aryColumns[$j]->width)/10)+2 );
		}

		// ----------------------------------------------------------------
		// 印刷領域に関する設定
		//
		//
		// ----------------------------------------------------------------
		// 印刷ページ毎のタイトルを設定
		$worksheet->repeatRows(0, $lngHeaderCount);	// $lngHeaderCount=3を指定すると、見える行番号としては4行目が指定されます。(0, $lngHeaderCount) と言った指定も可能です。
		// 拡大縮小印刷の設定
		$worksheet->fitToPages(1, 0);	// 横, 縦 ／ 1,0 場合：横に1ページに収めて、縦はフレキシブル
		// 用紙サイズを設定します
		$worksheet->setPaper(8);	// A3=8,  A4=9
		// ページの方向を横に設定する
		$worksheet->setLandscape();	// 横
		//$worksheet->setPortrait();	// 縦
		// ヘッダ
		$worksheet->setHeader(mb_convert_encoding('&R&P/&N','sjis-win'), 0);
		// 余白
		$worksheet->setMargins(0.5); //各余白0.5インチ
		$worksheet->setMarginLeft(1); //左の余白は1インチ

		/* setHeader() 制御文字一覧
		
		    制御文字            種類                 説明
		    =======             ====                ===========
		    &L                  位置                左
		    &C                                      中央
		    &R                                      右
		    
		    &P                  情報                ページ番号
		    &N                                      合計ページ数
		    &D                                      日付
		    &T                                      時刻
		    &F                                      ファイル名
		    &A                                      ワークシート名
		    
		    &fontsize           フォント             フォントサイズ
		    &"font,style"                           フォント名とスタイル
		*/
		// ----------------------------------------------------------------


		// ヘッダーの行数（上記で設定済みの行数）
		$lngHeadLineCnt = $lngHeaderCount + 1;

		// ヘッダー部、最初のx行を固定する
		$worksheet->freezePanes(array($lngHeadLineCnt, 0));

		// フォーマットオブジェクトを保持する配列
		$aryFormat = array();

		$lngSubtotal = 0; // 小計増加行のカウンタ
		
		$arySubTotalBuff = array();	// 小計行の計算バッファ
		
		// ----------------------------------------------------------------
		// 結果を成形
		//
		//
		// ----------------------------------------------------------------
		for( $i = 0; $i < $lngResultRows; $i++ )
		{
			// 行追加されていた場合（小計行の処理にて）
			if( $lngAddLine >= 1 )
			{
				$lngSubtotal = $lngSubtotal + $lngAddLine;		// 小計行が増えた分の合計カウンタ
				$lngAddLine = 0;	// 記録をリセットする
			}
			
			// 行結果取得
			$aryResult = $objDB->fetchArray( $lngResultID, $i );

			//---------------------------------------------------------------
			// カラムの設定（同行・左->右へ）
			//---------------------------------------------------------------
			for( $j = 0; $j < count($aryDataset); $j++ )
			{

				$varCellData = "";
				unset($objFormat);
				
				// ----------------------------------------------------
				// データ種による取得処理
				switch( (int)$aryData["lngExportData"] )
				{
					// 売上レシピ
					case 1:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData_Sales($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat_Sales($workbook, $aryFormat, $aryDataset, $j, $aryResult);

						// 小計行用の指定カラムの計算バッファ
						if($j==16 || $j == 17 || $j == 19 || $j == 21  )
						{
							$arySubTotalBuff[1][$j] += $varCellData;	// 製品コードまとめ
							$arySubTotalBuff[2][$j] += $varCellData;	// 顧客コードまとめ
							$arySubTotalBuff[3][$j] += $varCellData;	// 部門コードまとめ
							$arySubTotalBuff[4][$j] += $varCellData;	// 総計
						}

						break;

					// 仕入一覧表
					case 4:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData_Stock($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat_Stock($workbook, $aryFormat, $aryDataset, $j, $aryResult);

						// 小計行用の指定カラムの計算バッファ
						if( $j == 21 || $j == 22 || $j == 23 || $j == 24 )
						{
							$arySubTotalBuff[1][$j] += $varCellData;	// 
							$arySubTotalBuff[2][$j] += $varCellData;	// 
							$arySubTotalBuff[3][$j] += $varCellData;	// 
							$arySubTotalBuff[4][$j] += $varCellData;	// 
						}

						break;

					// Purchase recipe
					case 2:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData_Purchase($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat_Purchase($workbook, $aryFormat, $aryDataset, $j, $aryResult);

						// 小計行用の指定カラムの計算バッファ
						if( $j == 24 || $j == 25 )
						{
							$arySubTotalBuff[1][$j] += $varCellData;	// 
							$arySubTotalBuff[2][$j] += $varCellData;	// 
							$arySubTotalBuff[3][$j] += $varCellData;	// 
						}

						break;

					// 01 売上見込
					case 6:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData01($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat01($workbook, $aryFormat, $aryDataset, $j, $aryResult);
						break;
					
					// 02 概算売上
					case 7:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData02($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat02($workbook, $aryFormat, $aryDataset, $j, $aryResult);
						break;

				}
				// ----------------------------------------------------
		//var_dump($objFormat);
				$lngLine = $i+$lngHeadLineCnt+$lngSubtotal;
				//fncSpreadSheetCellWrite($aryDataset[$j]->type, $lngLine, $j, $varCellData, $objFormat);
				
				// ワークシートにセルの書き込み
				if( $aryDataset[$j]->type == 'date' )
				{
					$worksheet->write( $lngLine, $j, $varCellData, $objFormat);
				}
				elseif( $aryDataset[$j]->type == 'text' )
				{
					// BIFF5形式（shift_jisに変換）
					$worksheet->writeString( $lngLine, $j, mb_convert_encoding($varCellData,'sjis-win'), $objFormat );
				}
				elseif( $aryDataset[$j]->type == 'int' || $aryDataset[$j]->type == 'float' )
				{
					$worksheet->writeNumber( $lngLine, $j, $varCellData, $objFormat );
				}
				else
				{
					$worksheet->writeFormula( $lngLine, $j, $varCellData, $objFormat );
				}
				
			}

			// ----------------------------------------------------
			// データ種による取得処理
			switch( (int)$aryData["lngExportData"] )
			{
			/*
					// 8 商品計画書
					case 8:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData_pplan($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat_pplan($workbook, $aryFormat, $aryDataset, $j, $aryResult);
						break;
			*/
			}

			//-----------------------------------------------------------------------------------------------------
			// 小計行の追加ロジック
			//-----------------------------------------------------------------------------------------------------
			switch( (int)$aryData["lngExportData"] )
			{
				// 売上レシピ
				case 1:
					
					// キーカラムの取得
//					$arySubTitle = fncSpreadSheetCellData_StockSubTitle();
					$aryKeyCol = fncSpreadSheetCellData_SalesKeyCol($aryData);
					// 小計行数を判断する
					list($lngRet, $aryKeySet) = fncSpreadSheetCellData_SubTotalCount($aryKeyCol, $aryDataset, $i, $aryResult, $lngResultRows, $lngResultID, $objDB);
					
					// 小計行を追加する（次行との差がある場合）
					if( $lngRet > 0 )
					{
						// 小計行カラムタイトルの取得
						$arySubTitle = fncSpreadSheetCellData_SalesSubTitle();
						// 小計行の追加
						$lngAddLine = fncSpreadSheetCellData_SubTotalAdd($arySubTitle, $lngRet, $aryKeySet, $i, $lngHeadLineCnt, $lngSubtotal, $workbook, $worksheet, $aryFormat, $aryData, $aryDataset, $arySubTotalBuff, 15, 21 );
					}
					break;

				// 仕入一覧表
				case 4:

					// キーカラムの取得
					$aryKeyCol = fncSpreadSheetCellData_StockKeyCol($aryData);
					// 小計行数を判断する
					list($lngRet, $aryKeySet) = fncSpreadSheetCellData_SubTotalCount($aryKeyCol, $aryDataset, $i, $aryResult, $lngResultRows, $lngResultID, $objDB);

					// 小計行を追加する（次行との差がある場合）
					if( $lngRet > 0 )
					{
						// 小計行カラムタイトルの取得
						$arySubTitle = fncSpreadSheetCellData_StockSubTitle();
						// 小計行の追加
						$lngAddLine = fncSpreadSheetCellData_SubTotalAdd($arySubTitle, $lngRet, $aryKeySet, $i, $lngHeadLineCnt, $lngSubtotal, $workbook, $worksheet, $aryFormat, $aryData, $aryDataset, $arySubTotalBuff, 20, 24 );
					}
					break;

				// Purchase recipe
				case 2:

					// キーカラムの取得
					$aryKeyCol = fncSpreadSheetCellData_PurchaseKeyCol($aryData);
					// 増やすべき行数を取得
					list($lngRet, $aryKeySet) = fncSpreadSheetCellData_SubTotalCount($aryKeyCol, $aryDataset, $i, $aryResult, $lngResultRows, $lngResultID, $objDB);

					// 小計行を追加する（次行との差がある場合）
					if( $lngRet > 0 )
					{
						// 小計行カラムタイトルの取得
						$arySubTitle = fncSpreadSheetCellData_PurchasSubTitle();
						// 小計行の追加
						$lngAddLine = fncSpreadSheetCellData_SubTotalAdd($arySubTitle, $lngRet, $aryKeySet, $i, $lngHeadLineCnt, $lngSubtotal, $workbook, $worksheet, $aryFormat, $aryData, $aryDataset, $arySubTotalBuff, 23, 25 );
					}
					break;
			}
			//-----------------------------------------------------------------------------------------------------


		}


		return true;

	}

	/*
	//****************************************************************************
	//	ブロックを生成する
	*
	*	@param	array	$aryKeyCol		キーカラム定義
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$i				行カウンター
	*	@param	array	$aryKey			キーコード配列
	*	@param	array	$aryResult		データベース取得値（比較元）
	*	@param	integer	$lngResultRows	結果行数
	*	@param	integer	$lngResultID	結果ID
	*	@param	object	$objDB			DBオブジェクト
	*
	*	@return	Integer	小計行として増やすべき結果行数
	*
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCell_Blocks($aryData)
	{
		switch( (int)$aryData["lngExportData"] )
		{
			// 8 商品計画書
			case 8:
				// データ生成と取得
				$varCellData = fncSpreadSheetCellData_pplan($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
				$aryDataset[$j]->data = $varCellData;
				// フォーマットオブジェクトの取得
				$objFormat =& fncSpreadSheetCellFormat_pplan($workbook, $aryFormat, $aryDataset, $j, $aryResult);
				break;
		}

	}


	/*
	//****************************************************************************
	//	小計行を算出する
	*
	*	@param	array	$aryKeyCol		キーカラム定義
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$i				行カウンター
	*	@param	array	$aryKey			キーコード配列
	*	@param	array	$aryResult		データベース取得値（比較元）
	*	@param	integer	$lngResultRows	結果行数
	*	@param	integer	$lngResultID	結果ID
	*	@param	object	$objDB			DBオブジェクト
	*
	*	@return	Integer	小計行として増やすべき結果行数
	*
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCellData_SubTotalCount($aryKeyCol, $aryDataset, $i, $aryResult, $lngResultRows, $lngResultID, $objDB)
	{
		$lngRet = 0;
		
		// ------------------------------------
		// 小計行を何行増やすべきかを計算
		// ------------------------------------
		// 現行データコピー（次行のデータが存在しない場合を考慮）
		$aryAfterResult = $aryResult;
		// 最終行以外の場合に、1つ先のデータ行をフェッチ
		if( (int)$i+1 < $lngResultRows )
		{
			$aryAfterResult = $objDB->fetchArray( $lngResultID, $i+1 );
		}
		
		// 1つ先の情報が同一であるかを確認
		// 又は最後の行の場合、小計行を表示させる
		list($lngRet, $aryKeySet) = fncSpreadSheetCellData_Diff($aryDataset, $i, $aryKeyCol, $aryResult, $aryAfterResult);

		// 最終行の場合には、強制的に$aryKeyCol行に+1を追加
		if($i == $lngResultRows-1)
		{
			$lngRet = count($aryKeyCol)+1;
			//$aryKeySet = $aryKeyCol;
			//$aryKeySet[count($aryKeyCol)] = 0;
		}
		
		return array($lngRet, $aryKeySet);
	}

	/*
	//****************************************************************************
	//	小計行を追加（描画）する
	*
	*	@param	array	$arySubTitle	小計行のカラムタイトル
	*	@param	integer	$lngRet			小計行の数
	*	@param	integer	$lngHeadLineCnt	ヘッダ行数
	*	@param	integer	$lngSubtotal	今まで増えた小計行の合計数
	*	@param	object	$workbook		
	*	@param	object	$worksheet		
	*	@param	array	$aryFormat		
	*	@param	array	$aryData		
	*	@param	array	$aryDataset		
	*	@param	array	$arySubTotalBuff	加算値のバッファ
	*	@param	integer	$lngStartCol	開始カラム位置
	*	@param	integer	$lngEndCol		終了カラム位置
	*
	*	@return	Integer	追加した小計行の数
	*
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCellData_SubTotalAdd($arySubTitle, $lngRet, $aryKeySet, $i, $lngHeadLineCnt, $lngSubtotal, &$workbook, &$worksheet, $aryFormat, $aryData, $aryDataset, &$arySubTotalBuff, $lngStartCol, $lngEndCol)
	{
		
		// 小計行追加
		for( $lngSubLine =1; $lngSubLine <= $lngRet; $lngSubLine++ )
		{
			$lngLine = $i+$lngHeadLineCnt+$lngSubtotal+$lngSubLine;

			// 小計行のタイトル名を設定
//			$varCellDataSB = $arySubTitle[$aryData['lngExportConditions']][$aryKeySet[$lngSubLine-1]];
			$varCellDataSB = $arySubTitle[$aryData['lngExportConditions']][$lngSubLine];
			
//echo $aryKeySet[$lngSubLine-1].'<br />';
//var_dump($arySubTitle[$aryData['lngExportConditions']]);

			for( $j = $lngStartCol; $j <= $lngEndCol; $j++ )
			{

				// 小計行のタイトル名を追加
				if( $j == $lngStartCol )
				{
					$objFormatSB =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'subtotal');	// TEXTとしてフォーマット指定
					$worksheet->writeString( $lngLine, $j, mb_convert_encoding($varCellDataSB,'sjis-win'), $objFormatSB );
				}
				else
				// 小計行の合計値（税抜金額,税額,合計金額）
				{
					$varCellDataSB = $arySubTotalBuff[$lngSubLine][$j];
					$aryDataset[$j]->data = $varCellDataSB;
					$objFormatSB =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'int1');	// 計算式としてフォーマット指定
//					$objFormatSB =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDec');	// 計算式としてフォーマット指定
					$worksheet->writeNumber( $lngLine, $j, $varCellDataSB, $objFormatSB ); 
				}
				
				// バッファクリア（対象行・全てのカラム）
				$arySubTotalBuff[$lngSubLine][$j]=0;
			}
		}
		
		$lngAddLine = $lngSubLine-1;	// 小計行を追加したという記録
		$lngStartLine = 1+ $i + $lngAddLine + $lngSubtotal;	// 計算式用のカウンタバッファ

		return $lngAddLine;
	}

	/*
	//****************************************************************************
	//	データの比較
	*
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$i				行カウンター
	*	@param	array	$aryKey			キーコード配列
	*	@param	array	$aryResult		データベース取得値（比較元）
	*	@param	array	$aryAfterResult	データベース取得値（比較先）
	*
	*	@return	Integer	-1：同一である、1以上：異なるカラムが存在する、その数
	*
	// ・行単位での比較である。$j（列）の指定で値が比較される。
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCellData_Diff($aryDataset, $i, $aryKey, $aryResult, $aryAfterResult)
	{
		$strDiffValue1 = '';
		$strDiffValue2 = '';
		$aryReturn = array();
		$lngReturn = 0;
		$lngKeyPoint = 0;
		$blnLineChange = false;

		while (list($lngKey, $strValue) = each($aryKey))
		{
///echo '['.$i.']'.$lngKey.'<br />';
///echo $aryResult[strtolower($aryDataset[$strValue]->name)].'<br />';
///echo $aryAfterResult[strtolower($aryDataset[$strValue]->name)].'<br /><br />';

			$strDiffValue1 .= $aryResult[strtolower($aryDataset[$strValue]->name)].'|';			// 現在行データ
			$strDiffValue2 .= $aryAfterResult[strtolower($aryDataset[$strValue]->name)].'|';	// 次行データ


			// 事なるカラムをチェック
			if( $aryResult[strtolower($aryDataset[$strValue]->name)] != $aryAfterResult[strtolower($aryDataset[$strValue]->name)] || $blnLineChange)
			{
				$aryReturn[] = $strValue;	// 実際のカラム位置を記録
				$lngReturn++;	// 返却用（カラム数）
				
				// 配列の0番目（初回）に相違が有ったカラムは、小計行を全て表示させる
				if( $lngKeyPoint == 0 )
				{
					$blnLineChange = true;
				}
			}

			$lngKeyPoint++;
			
//echo $strDiffValue1.'||'.$strDiffValue2.'<br />';
//echo $strDiffValue2.'<br />';
		}

		// 前のデータと同一の場合は -1
		if( $strDiffValue1 == $strDiffValue2 )
		{
			return array(-1, $aryReturn);
		}
//var_dump($aryReturn);
//echo $strDiffValue1.' || '. $strDiffValue2.'<br />';
//echo '★'.$lngReturn.'--------------------------------------------------<br /><br />';


		return array($lngReturn, $aryReturn);
		
	}

	/*
	//****************************************************************************
		セルへの書き込み
	//****************************************************************************
	*/
	function fncSpreadSheetCellWrite($strDataset, $lngLine, $j, $varCellData, &$objFormat)
	{
		var_dump($objFormat);
		// ワークシートにセルの書き込み
		if( $strDataset == 'date' )
		{
			$worksheet->write( $lngLine, $j, $varCellData, $objFormat);
		}
		elseif( $strDataset == 'text' )
		{
			// BIFF5形式（shift_jisに変換）
			$worksheet->writeString( $lngLine, $j, mb_convert_encoding($varCellData,'sjis-win'), $objFormat );
		}
		elseif( $strDataset == 'int' || $strDataset == 'float' )
		{
			$worksheet->writeNumber( $lngLine, $j, $varCellData, $objFormat );
		}
		else
		{
			$worksheet->writeFormula( $lngLine, $j, $varCellData, $objFormat );
		}
				
		return true;
/*
				// ワークシートにセルの書き込み
				if( $aryDataset[$j]->type == 'date' )
				{
					$worksheet->write( $i+$lngHeadLineCnt+$lngSubtotal, $j, $varCellData, $objFormat);
				}
				elseif( $aryDataset[$j]->type == 'text' )
				{
					// BIFF5形式（shift_jisに変換）
					$worksheet->writeString( $i+$lngHeadLineCnt+$lngSubtotal, $j, mb_convert_encoding($varCellData,'sjis-win'), $objFormat );
				}
				elseif( $aryDataset[$j]->type == 'int' || $aryDataset[$j]->type == 'float' )
				{
					$worksheet->writeNumber( $i+$lngHeadLineCnt+$lngSubtotal, $j, $varCellData, $objFormat );
				}
				else
				{
					$worksheet->writeFormula( $i+$lngHeadLineCnt+$lngSubtotal, $j, $varCellData, $objFormat );
				}
*/

	}

	/*
	//****************************************************************************
		セルの書式別・フォーマットオブジェクトの生成
	//****************************************************************************
	*/
	function fncSpreadSheetCellFormat(&$workbook, &$objFormat, $varType)
	{
		$strRetFormat = "";

		switch( $varType )
		{
			case 'blockColumnHeader':
				$strRetFormat = "blockColumnHeader";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setVAlignTop");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setTextRotation90");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'blockColHeaderLTR':
				$strRetFormat = "blockColHeaderLTR";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setSize8");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "borderLTR");
				}
				return $objFormat[$strRetFormat];
				break;
			case 'blockColHeaderLR':
				$strRetFormat = "blockColHeaderLR";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setSize8");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "borderLR");
				}
				return $objFormat[$strRetFormat];
				break;
			case 'blockColHeaderLBR':
				$strRetFormat = "blockColHeaderLBR";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setSize8");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "borderLBR");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'blockRowMarkingT':
				$strRetFormat = "blockRowMarkingT";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& fncSpreadSheetCellFormat($workbook, $objFormat, 'text8pt');//$workbook->addFormat();
					//fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setFgColor33");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "borderT");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'blockRowBorderT':
				$strRetFormat = "blockRowBorderT";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					//fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setTopColor5");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "borderT");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'text6pt':
				$strRetFormat = "text6pt";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setSize6");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setHAlignLeft");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'text8pt':
				$strRetFormat = "text8pt";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setSize8");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setHAlignLeft");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'blockNumber':
				$strRetFormat = "blockNumber";
				if(!isset($objFormat[$strRetFormat]))
				{
					$objFormat[$strRetFormat] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat[$strRetFormat], "setNumFormatNumber");
				}
				return $objFormat[$strRetFormat];
				break;

			case 'date':
				if(!isset($objFormat["date"]))
				{
					$objFormat["date"] =& $workbook->addFormat();
					// Date用
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setSize9");
//					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setHAlignLeft");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setNumFormatDate");
				}
				return $objFormat["date"];
				break;
			
			case 'int':
			case 'float':
				if(!isset($objFormat["number"]))
				{
					$objFormat["number"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setNumFormatNumber");
				}
				return $objFormat["number"];
				break;

			case 'numberCalc':
				if(!isset($objFormat["numberCalc"]))
				{
					$objFormat["numberCalc"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setFgColor40");
				}
				return $objFormat["numberCalc"];
				break;
			case 'numberCalcDec':
				if(!isset($objFormat["numberCalcDec"]))
				{
					$objFormat["numberCalcDec"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setNumFormatNumberDec");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setFgColor40");
				}
				return $objFormat["numberCalcDec"];
				break;
				

			case 'numberDec':
				if(!isset($objFormat["numberDec"]))
				{
					$objFormat["numberDec"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setNumFormatNumberDec");
				}
				return $objFormat["numberDec"];
				break;
			case 'numberDec4':
				if(!isset($objFormat["numberDec4"]))
				{
					$objFormat["numberDec4"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec4"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec4"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec4"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec4"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec4"], "setNumFormatNumberDec4");
				}
				return $objFormat["numberDec4"];
				break;

			case 'numberPercent':
				if(!isset($objFormat["numberPercent"]))
				{
					$objFormat["numberPercent"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setNumFormatnumberPercent");
				}
				return $objFormat["numberPercent"];
				break;
				
//by kou 20090611 目立つ列に背景つける				
			case 'numberCalcPoint':
				if(!isset($objFormat["numberCalcPoint"]))
				{
					$objFormat["numberCalcPoint"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setFgColor43");
				}
				return $objFormat["numberCalcPoint"];
				break;

			case 'numberDecPoint':
				if(!isset($objFormat["numberDecPoint"]))
				{
					$objFormat["numberDecPoint"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setNumFormatNumberDec");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setFgColor43");
				}
				return $objFormat["numberDecPoint"];
				break;

			case 'textPoint':
				if(!isset($objFormat["textPoint"]))
				{
					$objFormat["textPoint"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setHAlignLeft");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setFgColor43");
				}
				return $objFormat["textPoint"];
				break;
//20090611 end
/*
			case 'error':
				if(!isset($objFormat["error"]))
				{
					$objFormat["error"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setHAlignLeft");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setFgColor45");
				}
				return $objFormat["error"];
				break;
*/

			case 'errorNumberDec':
				if(!isset($objFormat["errorNumberDec"]))
				{
					$objFormat["errorNumberDec"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setNumFormatNumberDec");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setFgColor45");
				}
				return $objFormat["errorNumberDec"];
				break;
				
				case 'errorNumber':
				if(!isset($objFormat["errorNumber"]))
				{
					$objFormat["errorNumber"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setFgColor45");
				}
				return $objFormat["errorNumber"];
				break;

			case 'int1':
				if(!isset($objFormat["int1"]))
				{
					$objFormat["int1"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["int1"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["int1"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["int1"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["int1"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["int1"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["int1"], "setBold");
				}
				return $objFormat["int1"];
				break;
			case 'subtotal':
				if(!isset($objFormat["subtotal"]))
				{
					$objFormat["subtotal"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["subtotal"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["subtotal"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["subtotal"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["subtotal"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["subtotal"], "setFgColor47");
					fncSpreadSheetExcelFormatSetting($objFormat["subtotal"], "setBold");
				}
				return $objFormat["subtotal"];
				break;

			case 'text':
				if(!isset($objFormat["text"]))
				{
					$objFormat["text"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "setHAlignLeft");
				}
				return $objFormat["text"];
				break;

			default:
				if(!isset($objFormat[$varType]))
				{
					$objFormat[$varType] =& $workbook->addFormat();
				}
				return $objFormat[$varType];

		}

		return false;
		
	}

	function fncSpreadSheetExcelFormatSettingCustomColor(&$workbook, $lngOriginColor, $aryRGB)
	{
		$workbook->setCustomColor($lngOriginColor+7, $aryRGB["r"], $aryRGB["g"], $aryRGB["b"]);
//var_dump($lngOriginColor);
//var_dump($aryRGB);
	}

	function fncSpreadSheetExcelFormatSettingColor(&$objFormat, $strPosition, $lngColorCode)
	{
		switch($strPosition)
		{
			case 'fg':
				$objFormat->setFgColor($lngColorCode+7);
				break;			
		}
		return true;
	}

	/*
	//****************************************************************************
	//	Excel のセルフォーマットを生成
	//	 
	//****************************************************************************
	*/
	function fncSpreadSheetExcelFormatSetting(&$objFormat, $strFormat)
	{
		// fromat setting
		switch($strFormat)
		{
			case "borderT":
				$objFormat->setTop(1);
				break;
			case "borderLTR":
				$objFormat->setLeft(1);
				$objFormat->setTop(1);
				$objFormat->setRight(1);
				break;
			case "borderLR":
				$objFormat->setLeft(1);
				$objFormat->setRight(1);
				break;
			case "borderLBR":
				$objFormat->setLeft(1);
				$objFormat->setRight(1);
				$objFormat->setBottom(1);
				break;
			case "header0":
				// $objFormat->setFgColor('yellow');
				$objFormat->setHAlign('left');
				$objFormat->setBold(1);
				break;

			case "header":
				$objFormat->setFgColor('yellow');
				$objFormat->setHAlign('center');
				$objFormat->setBold(1);
				$objFormat->setBorder(1);
				break;
				
			case "detail":
				$objFormat->setFgColor('white');
				$objFormat->setHAlign('left');
				//$objFormat->setBold(1);
				$objFormat->setBorder(1);
				break;

			case "setTextRotation90":
				$objFormat->setTextRotation(90);
				break;

			//
			// Color Reference to
			// http://dmcritchie.mvps.org/excel/colors.htm
			//

			case "setTopColor5":
				$objFormat->setTopColor(5+7);
				break;
			case "setFgColor33":
				$objFormat->setFgColor(33+7);
				break;
			case "setFgColor36":
				$objFormat->setFgColor(36+7);
				break;
			case "setFgColor40":
				$objFormat->setFgColor('40');
				break;
				
			case "setFgColor43":
				$objFormat->setFgColor('43');
				break;

			case "setFgColor45":
				$objFormat->setFgColor('45');
				break;

			case "setFgColor47":
				$objFormat->setFgColor('47');
				break;

			case "setFontFamilyTimes":
				$objFormat->setFontFamily("Times New Roman");
				
				break;
				
			case "setFontFamilyMsGothic":
				$objFormat->setFontFamily(mb_convert_encoding("ＭＳ ゴシック", "shift_jis"));
//				$objFormat->setFontFamily("ＭＳ ゴシック");
				break;
				
			case "setSize6":
				$objFormat->setSize(6);
				break;
			case "setSize7":
				$objFormat->setSize(7);
				break;
			case "setSize8":
				$objFormat->setSize(8);
				break;
			case "setSize9":
				$objFormat->setSize(9);
				break;

			case "setVAlignTop":
				$objFormat->setVAlign('top');
				break;
				
			case "setHAlignRight":
				$objFormat->setHAlign('right');
				break;
				
			case "setHAlignLeft":
				$objFormat->setHAlign('left');
				break;
				
			case "setNumFormatDate":
				$objFormat->setNumFormat("yyyy/mm/dd");
				break;
			case "setBold":
				$objFormat->setBold(1);
				break;	
			case "setNumFormatNumber":
				$objFormat->setNumFormat("##,###,###,###,###");
				break;
				
			case "setNumFormatNumberDec":
				$objFormat->setNumFormat("##,###,###,###,##0.00");
				break;
			case "setNumFormatNumberDec4":
				$objFormat->setNumFormat("##,###,###,###,##0.0000");
				break;
			
			case "setNumFormatnumberPercent":
				$objFormat->setNumFormat("##,###,###,###,##0.00%");
				break;
			default:
		}
		
		return true;
		
	}



	function fncSpreadSheetSearchDatasetName($aryDataset, $strColumnName)
	{

		for( $i = 0; $i < count($aryDataset); $i++ )
		{
			if( $aryDataset[$i]["name"] == $strColumnName ) break;
		}
		if( $i < count($aryDataset) ) return $i;
		return null;
	}


	/*
	//****************************************************************************
	//	ワークブックの生成
	//	
	//****************************************************************************
	*/
	function fncSpreadSheetCreateWorkbook()
	{

	    /**
	     * ワークブックの作成
	     */
	    $workbook = new Spreadsheet_Excel_Writer();
	    
		// 将来的に DIFF8形式の出力が必要となった場合、setVersion(8) の指定が必要となる
		//$workbook->setVersion(8);

		return $workbook;
	}
	

	/*
	//****************************************************************************
	//	SigmaGrid用のヘッダー情報を生成する
	//	
	//****************************************************************************
	*/
	function fncSpreadSheetHeaderJson($lngExportData)
	{
		$strDirName = SRC_ROOT."dataex/result/spreadsheet/";
		$aryFileName = array();
		
		switch ($lngExportData)
		{
			case 1:
				$aryFileName[0] = $strDirName . "sales_dataset.txt";
				$aryFileName[1] = $strDirName . "sales_colmuns.txt";
				break;
			case 4:
				$aryFileName[0] = $strDirName . "stock_dataset.txt";
				$aryFileName[1] = $strDirName . "stock_colmuns.txt";
				break;
			case 2:
				$aryFileName[0] = $strDirName . "purchase_dataset.txt";
				$aryFileName[1] = $strDirName . "purchase_colmuns.txt";
				break;
			case 6:
				$aryFileName[0] = $strDirName . "stat01_dataset.txt";
				$aryFileName[1] = $strDirName . "stat01_colmuns.txt";
				break;
			case 7:
				$aryFileName[0] = $strDirName . "stat02_dataset.txt";
				$aryFileName[1] = $strDirName . "stat02_colmuns.txt";
				break;
			case 8:
				$aryFileName[0] = $strDirName . "pplan_dataset.txt";
				$aryFileName[1] = $strDirName . "pplan_colmuns.txt";
				break;
		}

		$strDataSet = "";
		$strColumns = "";
		
		if ( !$strDataSet = file_get_contents($aryFileName[0]) )
		{
			return false;
		}
		if ( !$strColumns = file_get_contents($aryFileName[1]) )
		{
			return false;
		}

		

		return array('DATASET'=>$strDataSet, 'COLUMNS'=>$strColumns);
	}


	/*
	//****************************************************************************
	//	
	//	
	//****************************************************************************
	*/
	function fncTestJson()
	{
		$arr = array( 
		    array( 
		        "name" => "クワガタ", 
		        "url"  => "http://www.kuwagata.co.jp/" 
		    ), 
		    array( 
		        "name" => "くわがた", 
		        "url"  => "http://www.kuwagata.co.jp/" 
		    ) 
		); 

		$json = new Services_JSON; 
		$encode = $json->encode($arr); 
		header("Content-Type: text/javascript; charset=utf-8"); 
		echo $encode; 
	}
?>